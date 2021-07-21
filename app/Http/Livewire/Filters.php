<?php

namespace App\Http\Livewire;

use GuzzleHttp\Client;
use App\Models\Product;
use Livewire\Component;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use App\Models\ProductClassAttribute;

class Filters extends Component
{

    public $categories;
    public $brands;
    public $attributes;
    public $min_price;
    public $max_price;
    public $filterOptions = []; 
    public $data;
    
    public function render()
    {
        return view('livewire.filters');
    }

    public function mount($data = [])
    {
        $this->data = $data;
        $this->loadCategories();
        $this->loadBrands();
        $this->loadAttributes();
    }

    public function filter() {
        $this->emit('shop-grid.filter', $this->filterOptions);
    }

    public function loadBrands() 
    {
        $category_id = $this->data['category'] ?? null;
        $childrenIds = null;

        if ($category_id) {
            $childrenIds = ProductCategory::findOrFail($category_id)->getChildrensId();
            $childrenIds[] = $category_id;
        }

        $this->brands = ProductBrand::where('status','=','1')
            ->when($category_id, function ($query) use ($category_id, $childrenIds) {
                return $query->whereHas('products', function ($query) use ($category_id, $childrenIds)  {
                    return $query->byLocation()->whereHas('categories', function ($query) use ($category_id, $childrenIds)  {
                        return $query->whereIn('id', $childrenIds);
                    });
                });
            })
            ->when(!$category_id, function ($query) {
                return $query->whereHas('products', function ($query) {
                    return $query->byLocation();
                });
            })
            ->with('products')
            ->orderBy('name','ASC')->get();
    }

    public function loadAttributes() 
    {
        $category_id = $this->data['category'] ?? null;
        $childrenIds = null;
        if ($category_id) {
            $childrenIds = ProductCategory::find($category_id)->getChildrensId();
            $childrenIds[] = $category_id;
        }

        $this->attributes = ProductClassAttribute::withCount('product_attributes')
        ->where('json_options','<>','[]')
        ->where('json_attributes->type_attribute','select')
        ->whereHas('product_attributes', function ($query) use ($category_id, $childrenIds) {
            return $query->where('json_value', '<>', '')
                         ->where('json_value', 'NOT LIKE', "%*%")
                         ->when($category_id, function ($query) use ($childrenIds) {
                            return $query->whereHas('product', function ($query) use ($childrenIds)  {
                                return $query->whereHas('categories', function ($query) use ($childrenIds)  {
                                    return $query->whereIn('id', $childrenIds);
                                });
                            });
                        })
                ->groupBy('json_value');
        })
        ->limit(10)
        ->orderBy('product_attributes_count')
        ->get();
    }

    public function loadCategories() 
    {
        $this->categories = ProductCategory::with('product_class')->with('products')->orderBy('name','ASC')->get();
    }
}
