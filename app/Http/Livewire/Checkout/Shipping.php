<?php

namespace App\Http\Livewire\Checkout;

use App\Http\Chilexpress;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Seller;
use App\Models\ShippingMethod;
use Livewire\Component;

class Shipping extends Component
{
    public $cart;
    public $items;
    public $sellers;
    public $sellersShippings;
    protected $listeners = [
        'deleteItem' => 'deleteItem',
        'shipping-update' => 'updateSellersShippings',
    ];

    public function mount()
    {
        $this->items = $this->getItems();
        $this->sellers = $this->getSellers();
        $this->sellersShippings = [];
        //$this->updateSellersShippings();

    }
    public function render()
    {
        return view('livewire.checkout.shipping');
    }

    private function getSellers()
    {
        $products_id = CartItem::whereCartId($this->cart->id)->select('product_id')->with('product')->get();
        foreach ($products_id as $id) {
            $ids[] = $id['product_id'];
        }

        if (count($products_id) > 0) {
            $sellers_id = Product::whereIn('id', $ids)->select('seller_id')->groupBy('seller_id')->get();
            return Seller::whereIn('id', $sellers_id)->select('id', 'name', 'visible_name')->get();
        } else {
            return null;
        }

    }

    public function deleteItem()
    {
        $this->change();

    }

    public function change()
    {
        $this->sellers = $this->getSellers();
        $this->emit('cart.updateSubtotal', null);
        $this->emitUp('updateTotals');

    }

    public function updateSellersShippings()
    {

        //  if ($this->sellers) {

        $items = $this->items->groupBy(['product.seller_id', 'shipping_id']);

        // dd($items);
        $this->sellersShippings = [];
        foreach ($items as $sellerKey => $sellerValue) {
            $seller = Seller::where('id', $sellerKey)->first();
            $itemShippingSeller = [];
            $itemShippingSeller['sellerId'] = $sellerKey;

            foreach ($sellerValue as $shippingKey => $shippingValue) {
                $shippingMethod = ShippingMethod::where('id', $shippingKey)->first();

                $itemShipping['shipping']['id'] = $shippingKey;
                $itemShipping['shipping']['title'] = $shippingMethod->title;
                if (!empty($shippingMethod->json_value)) {
                    $itemShipping['shipping']['pricePackpage'] = json_decode($shippingMethod->json_value)->price;
                } else {
                    $itemShipping['shipping']['pricePackpage'] = null;
                }
                $itemShipping['shipping']['totalWidth'] = 0;
                $itemShipping['shipping']['totalHeight'] = 0;
                $itemShipping['shipping']['totalDepth'] = 0;
                $itemShipping['shipping']['totalWeight'] = 0;
                $itemShipping['shipping']['totalPrice'] = 0;
                $itemShipping['shipping']['totalShippingPackage'] = 0;
                foreach ($shippingValue as $item) {
                    $itemShipping['shipping']['totalShippingPackage'] += 1;
                    $itemShipping['shipping']['totalWidth'] += $item->width * $item->qty;
                    $itemShipping['shipping']['totalHeight'] += $item->height * $item->qty;
                    $itemShipping['shipping']['totalDepth'] += $item->depth * $item->qty;
                    $itemShipping['shipping']['totalWeight'] += $item->weight * $item->qty;
                    $itemShipping['shipping']['totalPrice'] += $item->price * $item->qty;
                }
                if ($shippingMethod->code == 'chilexpress') {
                    $chilexpress = new Chilexpress();
                    $communeOrigin = $seller->addresses_data[0]['commune_id'];
                    $communeDestine = $this->cart->address_commune_id;
                    $chilexpressResult = $chilexpress->calculateItemBySeller($itemShipping, $sellerKey, $communeOrigin, $communeDestine);
                    $resultitem = json_decode(json_encode($chilexpressResult['item']), false);
                    $itemShipping['shipping']['totalPrice'] = $resultitem->service->serviceValue;

                } else {
                    if (is_null($itemShipping['shipping']['pricePackpage'])) {
                        $itemShipping['shipping']['totalPrice'] = null;
                    } else {
                        $itemShipping['shipping']['totalPrice'] = $itemShipping['shipping']['pricePackpage'] * $itemShipping['shipping']['totalShippingPackage'];
                    }
                }
                array_push($itemShippingSeller, $itemShipping);

            }

            array_push($this->sellersShippings, $itemShippingSeller);


        }
        $this->emitUp('update-shipping-totals', $this->sellersShippings);

    }

    public function getItems()
    {
        return CartItem::whereCartId($this->cart->id)->with('product')->get();
    }
}
