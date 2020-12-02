
<div>
 @php
use App\Models\Product;
$product = Product::whereId($item)->first();
@endphp
@if ($product)
    <div class="d-sm-flex justify-content-between align-items-center my-4 pb-3 border-bottom">
        <div class="media media-ie-fix d-block d-sm-flex align-items-center text-center text-sm-left">
            {{-- <a
                class="d-inline-block mx-auto mr-sm-4" style="width: 10rem;"><img
                    src="{{ url($product->getFirstImagePath()) }}" alt="Product"></a> --}}
            <div class="media-body pt-2">
                <h3 class="product-title font-size-base mb-2"><a
                        href="{{ route('product', ['slug' => $product->url_key]) }}"
                        target="_blank">{{ $product->name }}</a><a wire:click="$emitUp('remove-from-cart:post', {{ $item }})" href="#"><i class="la la-times"></i></a></h3>
                @if (filled($product->getAttributesWithNames()))
                    @foreach ($product->getAttributesWithNames() as $attribute)
                        @if ($attribute['value'] != '* No aplica')
                            <div class="font-size-sm"><span
                                    class="text-muted mr-2">{{ $attribute['name'] }}:</span>{{ $attribute['value'] }}
                            </div>
                        @endif
                    @endforeach
                @endif
                <div class="d-inline-block font-size-lg text-accent pt-2 form-inline">
                    <input wire:model="qty" type="number" class="form-control w-50 h-25 input-sm"> item(s)
                </div>
                <strong>{{ currencyFormat($product->real_price ?? 0, 'CLP', true) }}</strong> por unidad
                {{-- <a class="d-inline-block text-accent font-size-ms border-left ml-2 pl-2"
                    href="{{ 'seller-shop/' . $product->seller->id }}">por {{ $product->seller->visible_name }}</a> --}}
                {{-- @if ($shippingMethods)
                    <div class="select-shipping mb-0 pt-2">
                        <select class="custom-select custom-select-sm my-1 mr-2" wire:model="selected"
                            wire:change="$emit('select-shipping-item')" wire:init="setSelected(0)">

                            @foreach ($shippingMethods as $key => $shipping)
                                @if ($shipping['is_available'] == true)
                                    <option value="{{ $key }}">{{ $shipping['name'] }}
                                        @if ($shipping['price'] && $shipping['price'] > 0)
                                            ({{ currencyFormat($shipping['price'] ? $shipping['price'] : 0, 'CLP', true) }})
                                        @endif
                                        @if (!empty($shipping['message']))
                                            ({{ $shipping['message'] }}) @endif
                                    </option>
                                    @else
                                    <option value="{{ $key }}">
                                        {{ $shipping['message'] }}
                                    </option>
                                @endif

                            @endforeach
                        </select>
                    </div>
                @endif --}}


            </div>

        </div>
        {{-- <div class="pt-2 pt-sm-0 pl-sm-3 mx-auto mx-sm-0 text-center text-sm-left" style="max-width: 9rem;">
            <div class="form-group mb-0">
                @livewire('qty-item', [
                'qty' => $item->qty,
                //'parentListener' => 'setQty' implicit
                ])
            </div>
            @if ($confirm == $item->id)
                <button wire:click.prevent="delete" class="btn btn-link px-0 text-danger" type="button"><i
                        class="czi-trash mr-2"></i><span class="font-size-sm">Eliminar</span></button>
            @else
                <button wire:click.prevent="deleteConfirm({{ $item->id }})" class="btn btn-link px-0 text-danger"
                    type="button"><i class="czi-close-circle mr-2"></i><span
                        class="font-size-sm">Eliminar</span></button>
            @endif

        </div> --}}

    </div>
@endif

</div>