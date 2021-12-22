<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\ProductCategory;

class CategoryBreadcrumbs extends Component
{
    public $categories = [];
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($categoryId)
    {
        $category = ProductCategory::findOrFail($categoryId);

        $categoryStack = [];

        do {
            $categoryStack[] = $category;
            $category = $category->parent;
        } while($category);

        $categoryStack = collect($categoryStack)->reverse();

        $this->categories = $categoryStack;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.category-breadcrumbs');
    }
}
