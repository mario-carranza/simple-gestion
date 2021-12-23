<?php

namespace App\Http\Livewire\Products;

use Livewire\Component;
use App\Models\Product;
use App\Models\ProductCategory;

class ProductsGeneral extends Component
{
    public $idCategory;

    public $view;

    public $limit;

    public function render()
    {
        return view($this->view)->with('productsShort', $this->getProducts($this->idCategory));
    }

    public function mount($idCategory, $view = 'livewire.products.short-list', $limit = 5)
    {
        $this->idCategory = $idCategory;

        $this->view = $view;

        $this->limit = $limit;
    }

    public function getProducts($idCategory)
    {
        return Product::where('status','=','1')
        ->where('is_approved','=','1')
        ->where('parent_id', '=', null)
        ->whereHas('categories', function ($query) use ($idCategory) {
            $query->where('id', $idCategory);
        })
        ->limit($this->limit)->inRandomOrder()->get();
    }

}
