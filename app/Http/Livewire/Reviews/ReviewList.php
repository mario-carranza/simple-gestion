<?php

namespace App\Http\Livewire\Reviews;

use Livewire\Component;
use Livewire\WithPagination;

class ReviewList extends Component
{
    use WithPagination;

    protected $queryString = ['sort_review'];
    protected $listeners = ['refreshList' => 'resetList'];
    public $product;
    public $sort_review;
    public $perPage = 5;

    public function render()
    {
        return view('livewire.reviews.review-list', [
            'reviews' => $this->product->reviews()->customSort($this->sort_review)->simplePaginate($this->perPage)
        ]);
    }

    public function mount($product)
    {
        $this->product = $product;
    }

    public function loadMore()
    {
        $this->perPage = $this->perPage + 5;
    }

    public function resetList()
    {
        $this->reset(['sort_review', 'page']);
    }
}