<div>
    <div class="widget-cart-item pb-2 border-bottom">
        {{--@if ($confirm == $item->id)
            <button class="close text-danger" wire:click.prevent="delete" type="button" aria-label="Remove"><span aria-hidden="true"><i
                class="czi-trash font-size-sm"></i></span></button>
        @else
            <button class="close text-danger" wire:click.prevent="deleteConfirm({{$item->id}})" type="button" aria-label="Remove"><span aria-hidden="true"><i
                class="czi-close-circle font-size-sm"></i></span></button>
        @endif--}}
        <button class="close text-danger" wire:click.prevent="delete()" type="button" aria-label="Remove"><span aria-hidden="true"><i
            class="czi-close-circle font-size-sm"></i></span></button>
        <div class="media align-items-center">
            <a class="d-block mr-2" href="{{ $product->url_key ? route('product',['slug' => $product->url_key]) : '#' }}"><img width="64" src="{{ url($product->getFirstImagePath()) }}" alt="Product" /></a>
            <div class="media-body">
                <h6 class="widget-product-title"><a href="{{$product->url_key ? route('product',['slug' => $product->url_key])  : '#'}}">{{ $item->name }}</a></h6>
                <div class="widget-product-meta">
                    <!--<span class="text-accent mr-2">$259.<small>00</small></span>-->
                    <span class="text-accent mr-2">{{ $item->price ? currencyFormat($item->price, defaultCurrency(), true) : currencyFormat(0, defaultCurrency(), true) }}</span>
                    <span class="text-muted">x {{$qty}}</span>
                </div>
            </div>
        </div>
    </div>
</div>
