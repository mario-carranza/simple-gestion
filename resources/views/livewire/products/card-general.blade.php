<div>
    @if(count($products))
        @if($renderIn == 'shop-grid')
            <div class="row">
                @foreach($products as $key => $product)
                    <div class="col-lg-{{$columnLg}} col-md-4 col-sm-6 px-3 mb-4" wire:key="{{ $key }}">
                        @livewire('products.product', ['product' => $product], key($product->id . $key))
                    </div>
                @endforeach
            </div>
        @else
            <div class="">
                @foreach($products as $product)
                    <div class="px-2 mb-4">
                        <div class="card product-card product-list mt-1">
                            <div class="d-sm-flex align-items-center"><a class="product-list-thumb" href="{{ route('product',['slug' => $product->url_key]) }}" ><img src="{{ url($product->getFirstImagePath()) }}" alt="Product"></a>
                                <div class="card-body py-2">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <a class="product-meta d-block font-size-xs pb-1" href="#">{{ $product->showCategory() }}</a>
                                            <h3 class="product-title font-size-base"><a href="{{ route('product',['slug' => $product->url_key]) }}">{{$product->name}}</a></h3>
                                            <div class="d-flex justify-content-between">
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
                                            </div>
                                            <div class="card-body card-body-hidden">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        @if ($product->product_type_id == 1)
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
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            @if($product->is_travel)
                                                <span class="badge badge-info badge-shadow position-relative">Turismo</span>
                                            @endif
                                            @if($product->has_special_price)
                                                <span class="badge badge-warning badge-shadow position-relative">Descuento</span>
                                            @endif
                                            @if($product->new)
                                                <span class="badge badge-info badge-shadow position-relative">Nuevo</span>
                                            @endif
                                            @if($product->product_type_id == 2)
                                                <span class="badge badge-danger badge-shadow position-relative">Variable</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>    
                    </div>
                @endforeach
            </div>
        @endif
            @if($paginateBy && $showPaginate)
                {{ $products->links('paginator') }}
            @endif
    @else
        <div class="col-lg-12 col-md-12 col-sm-12">
            <p class="text-center">No existen productos en esta búsqueda.</p>
        </div>
    @endif
</div>
