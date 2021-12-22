<?php

namespace App\Http\Livewire\Products;

use App\Models\Section;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\DB;
use App\Services\ProductFilterService;
use App\Models\Product as ModelsProduct;
//use Barryvdh\Debugbar\Facade as Debugbar;

class CardGeneral extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $columnLg;
    public $columnMd;
    public $columnSm;
    public $paginateBy;
    public $showPaginate;
    public $valuesQuery;
    public $showFrom = '';
    public $sortingField = null;
    public $sortingDirection = null;
    public $renderIn = null;
    public $render = null;
    public $filters = null;
    public $sectionId = null;

    protected $listeners = [
        'shop-grid.filter' => 'filterProducts',
        'shop-grid.sort' => 'sortProducts',
    ];

    public function render()
    {
        switch($this->showFrom) {
            case 'shop-general': 
                $render = [ 'products' => $this->getProductsNoRandom()];
                break;
            case 'searchCategory': 
                $render = [ 'products' => $this->getProductsByCategory($this->valuesQuery)];
                break;
            case 'seller': 
                $render = [ 'products' => $this->searchSeller($this->valuesQuery)];
                break;
            default:
                if ( empty($this->showFrom) ) {
                    $render = [ 'products' => $this->getProducts()];
                } else {
                    $render = [ 'products' => $this->searchProduct($this->valuesQuery)];
                }
                break;
        } 
      
        return view('livewire.products.card-general', $render);
    }

    public function mount(
        $paginateBy, 
        $showPaginate, 
        $columnLg = null, 
        $showFrom, 
        $valuesQuery = null, 
        $renderIn = 'shop-grid',
        $sectionId = null
    ) {
        $this->paginateBy = $paginateBy;
        $this->columnLg = $columnLg;
        $this->showPaginate = $showPaginate;
        $this->showFrom = $showFrom;
        $this->valuesQuery = $valuesQuery;
        $this->renderIn = $renderIn;
        $this->sectionId = $sectionId;
    }

    public function filterProducts($data)
    {
        $this->filters = $data;
        $this->render();
    }

    public function sortProducts($data)
    {
        $this->sortingField = $data['field'];
        $this->sortingDirection = $data['direction'];
        $this->render();
    }

    public function getProducts()
    {
        return $this->baseQuery(true);
    }

    public function getProductsNoRandom()
    {
        return $this->baseQuery(false);
    }

    public function searchProduct($data)
    {
        return $this->baseQuery(false, $data['category'], $data['product']);
    }

    public function getProductsByCategory($data)
    {
        return $this->baseQuery(false, $data['category']);
    }

    public function searchSeller($data)
    {
        return $this->baseQuery(false, null, null, $data);
    }

    public function paginationView()
    {
        return 'paginator';
    }

    private function baseQuery($random = false, $category_id = null, $product_search = null, $seller_id = null, $section_id = null)
    {
        $this->sortingField = $this->sortingField ?? 'created_at';
        $this->sortingDirection = $this->sortingDirection ?? 'DESC';

        $baseQuery =  ModelsProduct::where('status', '=', '1')
            ->where('parent_id', '=', null)
            ->where('is_approved', '=', '1')
            ->with('categories')
            ->whereHas('seller', function ($query) {
                return $query->where('is_approved', '=', '1');
            })
            ->when($this->sectionId, function ($query) {
                $section = Section::find($this->sectionId);
                return $query->whereHas('categories', function ($query) use ($section) {
                    return $query->whereIn('id', $section->product_categories->pluck('id')->toArray());
                });
            })
            ->when($category_id, function ($query) use ($category_id) {
                if ($category_id != 0) {
                    $category = ProductCategory::find($category_id);
                    $categories = array_merge([$category_id], $category->getChildrensId());
                    return $query->whereHas('categories', function ($q) use ($categories) {
                        $q->whereIn('id', $categories);
                    });
                }
                return $query;
            })
            ->when($product_search, function ($query) use ($product_search) {
                return $query->where('name', 'LIKE', '%' . $product_search . '%');
            })
            ->when($seller_id, function ($query) use ($seller_id) {
                if ($seller_id != 0) {
                    return $query->whereHas('seller', function ($q) use ($seller_id) {
                        $q->where('id', '=', $seller_id);
                    });
                }
                return $query;
            });
        
        // Filter and sorting

        // Current price
        $baseQuery->selectRaw('*');
        $baseQuery->selectRaw('id as aux_id');
        $baseQuery->selectRaw('(CASE
        WHEN (visible_from IS NULL AND visible_to IS NULL) THEN 1
        WHEN (visible_from IS NOT NULL AND visible_to IS NULL) AND (visible_from <= CURDATE()) THEN 1
        WHEN (visible_to IS NOT NULL AND visible_from IS NULL) AND (visible_to >= CURDATE()) THEN 1
        WHEN (visible_from IS NOT NULL AND visible_to IS NOT NULL) AND (visible_from <= CURDATE() AND  CURDATE() <= visible_to) THEN 1
        ELSE 0
        END)  
        AS should_show');
        $baseQuery->selectRaw('(CASE
        WHEN product_type_id = 2 THEN (SELECT MAX(
            CASE
            WHEN special_price IS NULL THEN price
            WHEN special_price IS NOT NULL AND (special_price_from IS NULL OR special_price_to IS NULL) THEN special_price 
            WHEN special_price IS NOT NULL AND (special_price_from <= CURDATE() AND  CURDATE() <= special_price_to) THEN special_price
            ELSE price
            END
            ) FROM products WHERE parent_id = aux_id) 
        WHEN special_price IS NULL THEN price
        WHEN special_price IS NOT NULL AND (special_price_from IS NULL OR special_price_to IS NULL) THEN special_price 
        WHEN special_price IS NOT NULL AND (special_price_from <= CURDATE() AND  CURDATE() <= special_price_to) THEN special_price
        ELSE price 
        END)  
        AS current_price');

        $baseQuery->having('should_show', '>=', 1);
        
        // Filter
        $filterService = new ProductFilterService();
        $filterQuery = $filterService->filterByParams($baseQuery, $this->filters);

        // Sorting
        $filterQuery->when(!is_null($random), function ($query) use ($random) {
                if ($random) {
                    return $query->inRandomOrder();
                } else {
                    $query->orderBy($this->sortingField, $this->sortingDirection);
                }
            });

        return $filterQuery->paginate($this->paginateBy);
    }
}
