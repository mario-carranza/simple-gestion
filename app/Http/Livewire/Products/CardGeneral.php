<?php

namespace App\Http\Livewire\Products;

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
    public $render = null;
    public $filters = null;

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
       /*  $render = [
            'products' => (!empty($this->showFrom)) ? (($this->showFrom == 'searchCategory') ? $this->getProductsByCategory($this->valuesQuery) : $this->searchProduct($this->valuesQuery)) : $this->getProducts()
        ]; 
        */
        return view('livewire.products.card-general', $render);
    }

    public function mount($paginateBy, $showPaginate, $columnLg = null, $showFrom, $valuesQuery = null)
    {
        $this->paginateBy = $paginateBy;
        $this->columnLg = $columnLg;
        $this->showPaginate = $showPaginate;
        $this->showFrom = $showFrom;
        $this->valuesQuery = $valuesQuery;
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

    private function baseQuery($random = false, $category_id = null, $product_search = null, $seller_id = null)
    {
        $this->sortingField = $this->sortingField ?? 'random';
        $this->sortingDirection = $this->sortingDirection ?? 'random';

        $baseQuery =  ModelsProduct::where('status', '=', '1')
            ->where('parent_id', '=', null)
            ->where('is_approved', '=', '1')
            ->with('categories')
            ->whereHas('seller', function ($query) {
                return $query->where('is_approved', '=', '1');
            })
            ->when($category_id, function ($query) use ($category_id) {
                if ($category_id != 0) {
                    return $query->whereHas('categories', function ($q) use ($category_id) {
                        $q->where('id', '=', $category_id);
                    });
                }
                return $query;
            })
            ->when($product_search, function ($query) use ($product_search) {
                return $query->where('name', 'LIKE', '%' . $product_search . '%')
                ->orWhere('sku', 'LIKE', '%' . $product_search . '%')
                ->orWhere('attributes_json->attribute-1', 'LIKE', '%' . $product_search . '%')
                ->orWhereHas('product_brands', function ($q) use ($product_search) {
                    $q->where('name', 'LIKE', '%' . $product_search . '%');
                });
            })
            ->when($seller_id, function ($query) use ($seller_id) {
                if ($seller_id != 0) {
                    return $query->whereHas('seller', function ($q) use ($seller_id) {
                        $q->where('id', '=', $seller_id);
                    });
                }
                return $query;
            });

        //return $baseQuery->inRandomOrder()->paginate($this->paginateBy);

        // Current price
        $baseQuery->selectRaw('*');
        $baseQuery->selectRaw('(CASE
        WHEN special_price IS null THEN price
        ELSE special_price END)  
        AS current_price');
            
        // Filter
        $filterService = new ProductFilterService();
        $filterQuery = $filterService->filterByParams($baseQuery, $this->filters);

        // Sorting
        $filterQuery->when(!is_null($random), function ($query) use ($random) {
            if ($random || $this->sortingField === 'random') {
                return $query->inRandomOrder();
            } else {
                $query->orderBy($this->sortingField, $this->sortingDirection);
            }
        });

        return $filterQuery->paginate($this->paginateBy);
    }
}
