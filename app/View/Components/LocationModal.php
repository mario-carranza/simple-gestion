<?php

namespace App\View\Components;

use App\Models\Commune;
use Illuminate\View\Component;

class LocationModal extends Component
{
    public $communes;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->communes = Commune::whereHas('product_inventory_sources', function ($query) {
            return $query->whereHas('products');
        })->get()->sortBy('name');
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {

        return view('components.location-modal');
    }
}
