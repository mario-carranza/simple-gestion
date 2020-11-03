<?php

namespace App\Http\Livewire\Cart;

use App\Http\Livewire\Traits\CartTrait;
use App\Models\CartItem;
use Livewire\Component;

class Preview extends Component
{
    use CartTrait;

    public $items;

    protected $listeners = [
        'change' => 'change',
        'deleteItem' => 'deleteItem'
    ];


    protected $messages = [
        'digits_between' => 'Debe indicar una cantidad.',
    ];

    public function mount()
    {
        $this->items = $this->getItems();
        // $this->total = $this->getTotal();
    }

    public function render()
    {
        return view('livewire.cart.preview');
    }

    public function change()
    {
        $this->emit('cart.updateSubtotal', null);

        if ($this->updatedCartTrait()) {
            return;
        }

    }

    public function deleteItem()
    {
        $this->change();
        $this->items = $this->getItems();
        // $this->total = $this->getTotal();
    }

    public function checkout()
    {
        return redirect()->route('go-checkout');
    }

    // private function getTotal(): float
    // {
    //     $total = 0;
    //     foreach ($this->getItems() as $item) {
    //         $total += $item->price * $item->qty;
    //     }
    //     return $total;
    // }

    /**
     * replace for getItemsProperty???
     *
     * @return void
     */
    private function getItems()
    {
        return CartItem::whereCartId($this->cart->id)->get();
    }
}
