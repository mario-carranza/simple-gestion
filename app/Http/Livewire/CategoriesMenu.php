<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\ProductCategory;

class CategoriesMenu extends Component
{
    public $categories;

    public function render()
    {
        return view('livewire.categories-menu');
    }

    public function mount()
    {
        $this->loadCategories();
    }

    public function loadCategories()
    {
        $this->categories = ProductCategory::whereHas('products', function ($query) {
            return $query->where('id', '<>', '')->where('is_approved', '=', 1)->whereHas('seller', function ($query) {
                return $query->where('is_approved', '=', 1);
            });
        })->orderBy('name', 'ASC')->get();
    }
}
