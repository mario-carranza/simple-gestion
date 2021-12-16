<div class="card product-card">
    {{-- <div class="product-card-actions d-flex align-items-center">
        <a class="btn-action nav-link-style mr-2" href="#"><i class="czi-compare mr-1"></i>Compare</a>
        <button class="btn-wishlist btn-sm" type="button" data-toggle="tooltip" data-placement="left"
            title="Add to wishlist">
            <i class="czi-heart"></i>
        </button>
    </div> --}}
    <div class="row">
        @if($product->is_travel)
        <div class="col-lg-4 col-md-4 col-sm-4">
            <span class="badge badge-success badge-shadow">Turismo</span>
        </div>
        @endif
        @if($product->has_special_price)
            <div class="col-lg-4 col-md-4 col-sm-4">
                <span class="badge badge-warning badge-shadow">Dcto</span>
            </div>
        @endif
        @if($product->new)
            <div class="col-lg-4 col-md-4 col-sm-4">
                <span class="badge badge-info badge-shadow">Nuevo</span>
            </div>
        @endif
        @if($product->product_type_id == 2)
            <div class="col-lg-4 col-md-4 col-sm-4">
                <span class="badge badge-danger badge-shadow">Variable</span>
            </div>
        @endif
    </div>
    <a class="card-img-top d-block overflow-hidden" href="{{ route('product',['slug' => $product->url_key]) }}">
        <img class="w-100 max-height-14 min-height-14 img-contain" src="{{ url($product->getFirstImagePath()) }}" alt="Product">
    </a>
    <div class="card-body py-2">
        <h3 class="product-title font-size-sm"><a href="{{ url('seller-shop').'/'.$product->seller->id }}">{{ $product->seller->visible_name }}</a></h3>
        @if ($product->categories->count())
        <a class="product-meta d-block font-size-xs pb-1" href="{{ route('category.products', $product->categories[0]->slug) }}">{{ $product->showCategory() }}</a>
        @endif
        <h3 class="product-title font-size-sm"><a href="{{ route('product',['slug' => $product->url_key]) }}">{{ $product->name }}</a></h3>
        {{-- <h3 class="product-title font-size-sm"><a href="{{route('product',['slug' => $product->url_key])}}" @if(strlen($product->name) > 80) data-toggle="tooltip" data-placement="top" title="{{ $product->name }}" @endif>{{ substr($product->name, 0, 80) }} @if(strlen($product->name) > 80) ... @endif</a></h3> --}}
        <div class="d-flex justify-content-between">
            <!--<div class="product-price"><span class="text-accent">$198.<small>00</small></span></div>-->
            @if ($product->children()->count())
                @if ($product->has_special_price)
                    <div class="product-price">
                        @if ($product->getRealPriceRange()[0] == $product->getRealPriceRange()[1])
                            <span class="text-accent">
                                {{ currencyFormat($product->getRealPriceRange()[0], defaultCurrency(), true) }}
                            </span>
                            <del class="font-size-sm text-muted"><small>
                                {{ currencyFormat($product->getPriceRange()[0], defaultCurrency(), true) }}
                            </small></del>
                        @else
                            <span class="text-accent">  
                                {{ currencyFormat($product->getRealPriceRange()[0], defaultCurrency(), true) }} - {{ currencyFormat($product->getRealPriceRange()[1], defaultCurrency(), true) }}
                            </span>
                            <del class="font-size-sm text-muted"><small>
                                {{ currencyFormat($product->getPriceRange()[0], defaultCurrency(), true) }} - {{ currencyFormat($product->getPriceRange()[1], defaultCurrency(), true) }}
                            </small></del>
                         @endif
                    </div>
                @else
                    <div class="product-price">
                        <span class="text-accent">
                            @if ($product->getPriceRange()[0] == $product->getPriceRange()[1])
                            {{ currencyFormat($product->getPriceRange()[0], defaultCurrency(), true) }}
                            @else
                            {{ currencyFormat($product->getPriceRange()[0], defaultCurrency(), true) }} - {{ currencyFormat($product->getPriceRange()[1], defaultCurrency(), true) }}
                            @endif
                        </span>
                    </div> 
                @endif  
            @else
                <div class="product-price">
                    @if($product->has_special_price)
                        <span class="text-accent">{{ currencyFormat($product->special_price, defaultCurrency(), true) }}</span>
                        <del class="font-size-sm text-muted"><small>{{ currencyFormat($product->price, defaultCurrency(), true) }}</small></del>
                    @else
                        <span class="text-accent">{{ currencyFormat($product->real_price, defaultCurrency(), true) }}</span>
                    @endif
                </div>
            @endif
            {{-- <div class="star-rating">
                <i class="sr-star czi-star-filled active"></i>
                <i class="sr-star czi-star-filled active"></i>
                <i class="sr-star czi-star-filled active"></i>
                <i class="sr-star czi-star-filled active"></i>
                <i class="sr-star czi-star-filled active"></i>
            </div> --}}
        </div>
    </div>
    <div class="card-body card-body-hidden">
        @if ($product->product_type_id == 1 && (!$product->is_housing && !$product->is_tour))
        @livewire('products.add-to-cart',['product' => $product])
        @endif
        <div class="text-center">
            <a class="nav-link-style font-size-ms" href="{{ route('product',['slug' => $product->url_key]) }}">
                @if ($product->is_service)
                    <i class="czi-eye align-middle mr-1"></i>Ver servicio
                @else
                    <i class="czi-eye align-middle mr-1"></i>Ver producto
                @endif
            </a>
        </div>
    </div>
</div>
