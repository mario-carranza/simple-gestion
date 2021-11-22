@extends('travel.layouts.base', ['collaboration' => true])

@section('content')
<!-- Page title-->
<!-- Page Content-->
<!-- Hero One item + Dots + Loop (defaults)-->
@php
use Carbon\Carbon;

$today =  Carbon::now();

@endphp

@if(count($sliders)>0)


<div class="d-none d-lg-block d-md-block d-sm-block cz-carousel cz-dots-enabled">
    <div class="cz-carousel-inner" data-carousel-options='{"autoplay": true, "autoHeight": true, "autoplayTimeout": 5000}'>
        @foreach ($sliders as $slider)

            @if($slider->visible_from || $slider->visible_to)
                @if( ($slider->visible_from ? $today->gte( $slider->visible_from):true) && ($slider->visible_to ? $today->lte($slider->visible_to): true) )
                    @if($slider->link)
                        <a href={{$slider->link}} target="_blank">
                            <img src="{{url($slider->path_web)}}" alt="{{$slider->name}}" class="img-fluid w-100">
                        </a>
                    @else
                        <img src="{{url($slider->path_web)}}" alt="{{$slider->name}}" >
                    @endif
                @endif
            @else
                @if($slider->link)
                    <a href={{$slider->link}} target="_blank">
                        <img src="{{url($slider->path_web)}}" alt="{{$slider->name}}" class="img-fluid w-100">
                    </a>
                @else
                    <img src="{{url($slider->path_web)}}" alt="{{$slider->name}}" >
                @endif
            @endif
        @endforeach
    </div>
</div>

<div class="d-block d-sm-none">
    <div class="cz-carousel cz-dots-enabled">
        <div class="cz-carousel-inner" data-carousel-options='{"autoplay": true, "autoHeight": true, "autoplayTimeout": 5000}'>
        @foreach ($sliders as $slider)
            @if($slider->visible_from || $slider->visible_to)
            @if( ($slider->visible_from ? $today->gte( $slider->visible_from):true) && ($slider->visible_to ? $today->lte($slider->visible_to): true) )
                    @if($slider->link)
                        <a href={{$slider->link}} target="_blank">
                            @if(!is_null($slider->path_mobile))
                                <img src="{{url($slider->path_mobile)}}" alt="{{$slider->name}}">
                            @else
                                <img src="{{url($slider->path_web)}}" alt="{{$slider->name}}">
                            @endif
                        </a>
                    @else
                        @if(!is_null($slider->path_mobile))
                            <img src="{{url($slider->path_mobile)}}" alt="{{$slider->name}}">
                        @else
                            <img src="{{url($slider->path_web)}}" alt="{{$slider->name}}">
                        @endif
                    @endif
                @endif
            @else
                @if($slider->link)
                    <a href={{$slider->link}} target="_blank">
                        @if(!is_null($slider->path_mobile))
                            <img src="{{url($slider->path_mobile)}}" alt="{{$slider->name}}">
                        @else
                            <img src="{{url($slider->path_web)}}" alt="{{$slider->name}}">
                        @endif
                    </a>
                @else
                    @if(!is_null($slider->path_mobile))
                        <img src="{{url($slider->path_mobile)}}" alt="{{$slider->name}}">
                    @else
                        <img src="{{url($slider->path_web)}}" alt="{{$slider->name}}">
                    @endif
                @endif
            @endif
        @endforeach
        </div>
    </div>
</div>
@else

<div class="d-none d-lg-block d-md-block d-sm-block cz-carousel cz-dots-enabled">
    <div class="cz-carousel-inner" data-carousel-options='{"autoplay": true, "autoHeight": true, "autoplayTimeout": 5000}'>
        <img src="{{ asset('img/home/hero-slider/banner-navidad-general.png') }}" alt="Contigo Pyme Navidad">
        <img src="{{ asset('img/home/hero-slider/banner_navidad.png') }}" alt="Contigo Pyme Banner Navidad">
        <a href="{{ route('seller.sign') }}"><img src="{{ asset('img/seller-register.png') }}" alt="Registra tu Pyme" class="img-fluid"></a>
        <img src="{{ asset('img/home/hero-slider/banner-02.png') }}" alt="Contigo Pyme Banner 2">
        <img src="{{ asset('img/home/hero-slider/banner-03.png') }}" alt="Contigo Pyme Banner 3">
    </div>
</div>

<div class="d-block d-sm-none">
    <div class="cz-carousel cz-dots-enabled">

        <div class="cz-carousel-inner" data-carousel-options='{"autoplay": true, "autoHeight": true, "autoplayTimeout": 5000}'>
            <img src="{{ asset('img/home/hero-slider/mobile-banner-navidad-general.png') }}" alt="Contigo Pyme Navidad">
            <img src="{{ asset('img/home/hero-slider/mobile-banner-navidad.png') }}" alt="Contigo Pyme Banner 1">
            <a href="{{ route('seller.sign') }}">
                <img src="{{ asset('img/home/hero-slider/mobile-banner-1.png') }}" class="img-fluid w-100" alt="Contigo Pyme Banner 1">
            </a>
            <img src="{{ asset('img/home/hero-slider/mobile-banner-2.png') }}" alt="Contigo Pyme Banner 2">
            <img src="{{ asset('img/home/hero-slider/mobile-banner-3.png') }}" alt="Contigo Pyme Banner 3">
        </div>
    </div>
</div>
@endif

@if(count($featuredProducts) > 3)
    <section class="container mt-5 mb-grid-gutter">
        <div class="d-flex flex-wrap justify-content-between align-items-center pt-1 border-bottom pb-4 mb-4">
            <h2 class="h3 mb-0 pt-3 mr-2">El regalo de la semana</h2>
        </div>
        <div class="cz-carousel cz-controls-static cz-controls-outside">
            <div class="cz-carousel-inner" data-carousel-options='{"items": 4, "nav": false, "responsive": {"0":{"items":1},"500":{"items":2, "gutter": 18},"768":{"items":4, "gutter": 20}, "1100":{"gutter": 24}}}'>
                @foreach($featuredProducts as $products)
                    <div class="card product-card">
                        <div class="row">
                            @if($products->has_special_price)
                                <div class="col-lg-4 col-md-4 col-sm-4">
                                    <span class="badge badge-warning badge-shadow">Dcto</span>
                                </div>
                            @endif
                            @if($products->new)
                                <div class="col-lg-4 col-md-4 col-sm-4">
                                    <span class="badge badge-info badge-shadow">Nuevo</span>
                                </div>
                            @endif
                            @if($products->product_type_id == 2)
                                <div class="col-lg-4 col-md-4 col-sm-4">
                                    <span class="badge badge-danger badge-shadow">Variable</span>
                                </div>
                            @endif
                        </div>
                        <a class="card-img-top d-block overflow-hidden" href="{{ route('product',['slug' => $products->url_key]) }}">
                            <img class="w-100 max-height-14 min-height-14" src="{{ url($products->getFirstImagePath()) }}" alt="Product">
                        </a>
                        <div class="card-body py-2">
                            <h3 class="product-title font-size-sm"><a href="{{ 'seller-shop/' . $products->seller->id }}">{{ $products->seller->visible_name }}</a></h3>
                            <a class="product-meta d-block font-size-xs pb-1" href="{{ route('category.products', $products->categories[0]->slug) }}">{{ $products->showCategory() }}</a>
                            <h3 class="product-title font-size-sm"><a href="{{ route('product',['slug' => $products->url_key]) }}">{{ $products->name }}</a></h3>
                            <div class="d-flex justify-content-between">
                                <div class="product-price">
                                    @if ($products->children()->count())
                                        @if ($products->has_special_price)
                                            <div class="product-price">
                                                @if ($products->getRealPriceRange()[0] == $products->getRealPriceRange()[1])
                                                    <span class="text-accent">
                                                        {{ currencyFormat($products->getRealPriceRange()[0], defaultCurrency(), true) }}
                                                    </span>
                                                    <del class="font-size-sm text-muted"><small>
                                                        {{ currencyFormat($products->getPriceRange()[0], defaultCurrency(), true) }}
                                                    </small></del>
                                                @else
                                                    <span class="text-accent">
                                                        {{ currencyFormat($products->getRealPriceRange()[0], defaultCurrency(), true) }} - {{ currencyFormat($products->getRealPriceRange()[1], defaultCurrency(), true) }}
                                                    </span>
                                                    <del class="font-size-sm text-muted"><small>
                                                        {{ currencyFormat($products->getPriceRange()[0], defaultCurrency(), true) }} - {{ currencyFormat($products->getPriceRange()[1], defaultCurrency(), true) }}
                                                    </small></del>
                                                @endif
                                            </div>
                                        @else
                                            <div class="product-price">
                                                <span class="text-accent">
                                                    @if ($products->getPriceRange()[0] == $products->getPriceRange()[1])
                                                    {{ currencyFormat($products->getPriceRange()[0], defaultCurrency(), true) }}
                                                    @else
                                                    {{ currencyFormat($products->getPriceRange()[0], defaultCurrency(), true) }} - {{ currencyFormat($products->getPriceRange()[1], defaultCurrency(), true) }}
                                                    @endif
                                                </span>
                                            </div>
                                        @endif
                                    @else
                                        <div class="product-price">
                                            @if($products->has_special_price)
                                                <span class="text-accent">{{ currencyFormat($products->special_price, defaultCurrency(), true) }}</span>
                                                <del class="font-size-sm text-muted"><small>{{ currencyFormat($products->price, defaultCurrency(), true) }}</small></del>
                                            @else
                                                <span class="text-accent">{{ currencyFormat($products->real_price, defaultCurrency(), true) }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-body ">
                            @if ($products->product_type_id == 1)
                                @livewire('products.add-to-cart',['product' => $products])
                            @endif
                            <div class="text-center">
                                <a class="nav-link-style font-size-ms" href="{{ route('product',['slug' => $products->url_key]) }}">
                                    @if ($products->is_service)
                                        <i class="czi-eye align-middle mr-1"></i>Ver servicio
                                    @else
                                        <i class="czi-eye align-middle mr-1"></i>Ver producto
                                    @endif
                                </a>
                            </div>
                            
                        </div>
                        
                    </div>
                    
                @endforeach
            </div>
        </div>
    </section>
@endif
<!-- Products grid (Trending products)-->
<section class="container pt-5">
    <!-- Heading-->
    <div class="d-flex flex-wrap justify-content-between align-items-center pt-1 border-bottom pb-4 mb-4">
        <h2 class="h3 mb-0 pt-3 mr-2">Productos</h2>
        <div class="pt-3"><a class="btn btn-outline-accent btn-sm" href="{{ route("section.$section->slug.shop-grid") }}">Más productos<i class="czi-arrow-right ml-1 mr-n1"></i></a></div>
    </div>
    <!-- Grid-->
    <div class="pt-2 mx-n2">
        <!-- Product-->
        @livewire('products.card-general', ['columnLg' => 3, 'showPaginate' => false, 'paginateBy' => 8, 'showFrom' => '', 'data' => ''])
    </div>
</section>

@if($banners[1]['path_web'] && $banners[1]['status'])
<section class="container mt-4 mb-grid-gutter">
    <div class="rounded-lg py-4">
        <div class="row align-items-center">
            <div class="col-md-12 d-none d-lg-block d-md-block d-sm-block">
                <img src="{{url($banners[1]['path_web'])}}" alt="{{$banners[1]['name']}}">

            </div>
            @if(!is_null($banners[1]['path_mobile']))
                <div class="col-md-12 d-block d-sm-none">
                    <img src="{{url($banners[1]['path_mobile'])}}" alt="{{$banners[1]['name']}}" class="w-100">
                </div>
            @else
                <div class="col-md-12 d-none d-block d-sm-none">
                    <img src="{{url($banners[1]['path_web'])}}" alt="{{$banners[1]['name']}}">
                </div>
            @endif
        </div>
    </div>
</section>
@endif

<!-- Promo banner-->
{{-- <section class="container mt-4 mb-grid-gutter">
    <div class="bg-faded-info rounded-lg py-4">
        <div class="row align-items-center">
            <div class="col-md-5">
                <img src="{{ asset('img/home-banner-01.png') }}" alt="Banner promoción 1" class="img-fluid">
                <div class="px-4 pr-sm-0 pl-sm-5"><span class="badge badge-danger">Limited Offer</span>
                    <h3 class="mt-4 mb-1 text-body font-weight-light">All new</h3>
                    <h2 class="mb-1">Last Gen iPad Pro</h2>
                    <p class="h5 text-body font-weight-light">at discounted price. Hurry up!</p>
                    <div class="cz-countdown py-2 h4" data-countdown="07/01/2021 07:00:00 PM">
                        <div class="cz-countdown-days"><span class="cz-countdown-value"></span><span class="cz-countdown-label text-muted">d</span></div>
                        <div class="cz-countdown-hours"><span class="cz-countdown-value"></span><span class="cz-countdown-label text-muted">h</span></div>
                        <div class="cz-countdown-minutes"><span class="cz-countdown-value"></span><span class="cz-countdown-label text-muted">m</span></div>
                        <div class="cz-countdown-seconds"><span class="cz-countdown-value"></span><span class="cz-countdown-label text-muted">s</span></div>
                    </div><a class="btn btn-accent" href="#">View offers<i class="czi-arrow-right font-size-ms ml-1"></i></a>
                </div>
            </div>
            <div class="col-md-7"><img src="{{ asset('img/home/banners/offer.jpg') }}" alt="iPad Pro Offer"></div>
        </div>
    </div>
</section> --}}
<!-- Brands carousel-->
{{-- <section class="container mb-5">
    <div class="cz-carousel border-right">
        <div class="cz-carousel-inner" data-carousel-options="{ &quot;nav&quot;: false, &quot;controls&quot;: false, &quot;autoplay&quot;: true, &quot;autoplayTimeout&quot;: 4000, &quot;loop&quot;: true, &quot;responsive&quot;: {&quot;0&quot;:{&quot;items&quot;:1},&quot;360&quot;:{&quot;items&quot;:2},&quot;600&quot;:{&quot;items&quot;:3},&quot;991&quot;:{&quot;items&quot;:4},&quot;1200&quot;:{&quot;items&quot;:4}} }">
            <div><a class="d-block bg-white border py-4 py-sm-5 px-2" href="#" style="margin-right: -.0625rem;"><img class="d-block mx-auto" src="{{ asset('img/shop/brands/13.png') }}" style="width: 165px;" alt="Brand"></a></div>
            <div><a class="d-block bg-white border py-4 py-sm-5 px-2" href="#" style="margin-right: -.0625rem;"><img class="d-block mx-auto" src="{{ asset('img/shop/brands/14.png') }}" style="width: 165px;" alt="Brand"></a></div>
            <div><a class="d-block bg-white border py-4 py-sm-5 px-2" href="#" style="margin-right: -.0625rem;"><img class="d-block mx-auto" src="{{ asset('img/shop/brands/17.png') }}" style="width: 165px;" alt="Brand"></a></div>
            <div><a class="d-block bg-white border py-4 py-sm-5 px-2" href="#" style="margin-right: -.0625rem;"><img class="d-block mx-auto" src="{{ asset('img/shop/brands/16.png') }}" style="width: 165px;" alt="Brand"></a></div>
            <div><a class="d-block bg-white border py-4 py-sm-5 px-2" href="#" style="margin-right: -.0625rem;"><img class="d-block mx-auto" src="{{ asset('img/shop/brands/15.png') }}" style="width: 165px;" alt="Brand"></a></div>
            <div><a class="d-block bg-white border py-4 py-sm-5 px-2" href="#" style="margin-right: -.0625rem;"><img class="d-block mx-auto" src="{{ asset('img/shop/brands/18.png') }}" style="width: 165px;" alt="Brand"></a></div>
            <div><a class="d-block bg-white border py-4 py-sm-5 px-2" href="#" style="margin-right: -.0625rem;"><img class="d-block mx-auto" src="{{ asset('img/shop/brands/19.png') }}" style="width: 165px;" alt="Brand"></a></div>
            <div><a class="d-block bg-white border py-4 py-sm-5 px-2" href="#" style="margin-right: -.0625rem;"><img class="d-block mx-auto" src="{{ asset('img/shop/brands/20.png') }}" style="width: 165px;" alt="Brand"></a></div>
        </div>
    </div>
</section> --}}
<!-- Product widgets-->
<section class="container pb-4 pb-md-5">
    <div class="row">
        @foreach($categories as $category)
        <div class="col-lg-4 col-md-6 mb-2 py-3">
            <div class="widget">
                <h3 class="widget-title">{{$category->name}}</h3>
                @livewire('products.products-general',['idCategory'=>$category->id])
                <p class="mb-0">...</p><a class="font-size-sm" href="{{ route('category.products', $category->slug) }}">Ver más<i class="czi-arrow-right font-size-xs ml-1"></i></a>
            </div>
        </div>
        @endforeach
    </div>
</section>

<!--
    <section class="container pb-4 pb-md-5">
        <div class="row">
            @livewire('search-products')
        </div>
    </section>
-->

@if(@$banners[2]['status'] && @$banners[3]['status'])
<section class="container mt-4 mb-grid-gutter">
    <div class="rounded-lg py-4">
        <div class="row text-center">
            <div class="col-md-6 pb-3">
                <img src="{{url($banners[2]['path_web'])}}" alt="{{$banners[2]['name']}}" class="img-fluid border-radious-3">
            </div>
            <div class="col-md-6 pb-3">
                <img src="{{url($banners[3]['path_web'])}}" alt="{{$banners[3]['name']}}" class="img-fluid border-radious-3">
            </div>
        </div>
    </div>
</section>
@elseif(@$banners[2]['status'])
<section class="container mt-4 mb-grid-gutter">
    <div class="rounded-lg py-4">
        <div class="row text-center">
            <div class="col-md-12 pb-3">
                <img src="{{url($banners[2]['path_web'])}}" alt="{{$banners[2]['name']}}" class="img-fluid border-radious-3">
            </div>
        </div>
    </div>
</section>
@elseif(@$banners[3]['status'])
<section class="container mt-4 mb-grid-gutter">
    <div class="rounded-lg py-4">
        <div class="row text-center">
            <div class="col-md-12 pb-3">
                <img src="{{url($banners[3]['path_web'])}}" alt="{{$banners[3]['name']}}" class="img-fluid border-radious-3">
            </div>
        </div>
    </div>
</section>
@endif

@if($banners[4]['path_web'] && $banners[4]['status'])
<section class="container mt-4 mb-grid-gutter">
    <div class="rounded-lg py-4">
        <div class="row align-items-center">
            <div class="col-md-12 d-none d-lg-block d-md-block d-sm-block">
                <img src="{{url($banners[4]['path_web'])}}" alt="{{$banners[4]['name']}}">
            </div>
            @if(!is_null($banners[4]['path_mobile']))
                <div class="col-md-12 d-block d-sm-none">
                    <img src="{{url($banners[4]['path_mobile'])}}" alt="{{$banners[4]['name']}}" class="w-100">
                </div>
            @else
                <div class="col-md-12 d-none d-block d-sm-none">
                    <img src="{{url($banners[4]['path_web'])}}" alt="{{$banners[4]['name']}}">
                </div>
            @endif
        </div>
    </div>
</section>
@endif

<!-- YouTube feed-->
{{-- <section class="container pb-5 mb-md-3">
    <div class="border rounded-lg p-3">
        <div class="row">
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="bg-secondary p-5 text-center"><img class="d-block mb-4 mx-auto" src="{{ asset('img/home/yt-logo.png') }}" width="120" alt="YouTube">
<div class="media justify-content-center align-items-center mb-4"><img class="mr-2" src="{{ asset('img/home/yt-subscribers.png') }}" width="126" alt="YouTube Subscribers"><span class="font-size-sm">250k+</span></div><a class="btn btn-primary border-0 btn-sm mb-3" href="#" style="background-color: #ff0000;"><i class="czi-add-user mr-2"></i>Subscribe*</a>
<p class="font-size-sm mb-0">*View latest gadgets reviews available for purchase in our store.
</p>
</div>
</div>
<div class="col-md-8">
    <div class="d-flex flex-wrap justify-content-between align-items-center pt-3 pb-2">
        <h2 class="h4 mb-3">Latest videos from Cartzilla channel</h2><a class="btn btn-outline-accent btn-sm mb-3" href="#">More videos<i class="czi-arrow-right font-size-xs ml-1 mr-n1"></i></a>
    </div>
    <div class="row no-gutters">
        <div class="col-lg-4 col-6 mb-3"><a class="video-cover video-popup-btn d-block text-decoration-0 px-2" href="https://www.youtube.com/embed/vS93u75NnPo">
                <div class="video-cover-thumb mb-2"><span class="badge badge-dark">6:16</span><img class="w-100" src="{{ asset('img/home/video/cover01.jpg') }}" alt="Video cover">
                </div>
                <h6 class="font-size-sm pt-1">5 New Cool Gadgets You Must See on Cartzilla - Cheap
                    Budget</h6>
            </a></div>
        <div class="col-lg-4 col-6 mb-3"><a class="video-cover video-popup-btn d-block text-decoration-0 px-2" href="https://www.youtube.com/embed/B6LaYgGogf0">
                <div class="video-cover-thumb mb-2"><span class="badge badge-dark">7:27</span><img class="w-100" src="{{ asset('img/home/video/cover02.jpg') }}" alt="Video cover">
                </div>
                <h6 class="font-size-sm pt-1">5 Super Useful Gadgets on Cartzilla You Must Have in 2020
                </h6>
            </a></div>
        <div class="col-lg-4 col-6 mb-3"><a class="video-cover video-popup-btn d-block text-decoration-0 px-2" href="https://www.youtube.com/embed/kB-ROfRS9V4">
                <div class="video-cover-thumb mb-2"><span class="badge badge-dark">6:20</span><img class="w-100" src="{{ asset('img/home/video/cover03.jpg') }}" alt="Video cover">
                </div>
                <h6 class="font-size-sm pt-1">Top 5 New Amazing Gadgets on Cartzilla You Must See</h6>
            </a></div>
        <div class="col-lg-4 col-6 mb-3 d-lg-none"><a class="video-cover video-popup-btn d-block text-decoration-0 px-2" href="https://www.youtube.com/embed/sJK67XXE_Rg">
                <div class="video-cover-thumb mb-2"><span class="badge badge-dark">6:11</span><img class="w-100" src="{{ asset('img/home/video/cover04.jpg') }}" alt="Video cover">
                </div>
                <h6 class="font-size-sm font-weight-bold pt-1">5 Amazing Construction Inventions and
                    Working Tools Available...</h6>
            </a></div>
    </div>
</div>
</div>
</div>
</section> --}}
<!-- Blog + Instagram info cards-->
{{-- <section class="container-fluid px-0">
    <div class="row no-gutters">
        <div class="col-md-6"><a class="card border-0 rounded-0 text-decoration-none py-md-4 bg-faded-primary" href="blog-list-sidebar.html">
                <div class="card-body text-center"><i class="czi-edit h3 mt-2 mb-4 text-primary"></i>
                    <h3 class="h5 mb-1">Read the blog</h3>
                    <p class="text-muted font-size-sm">Latest store, fashion news and trends</p>
                </div>
            </a></div>
        <div class="col-md-6"><a class="card border-0 rounded-0 text-decoration-none py-md-4 bg-faded-accent" href="#">
                <div class="card-body text-center"><i class="czi-instagram h3 mt-2 mb-4 text-accent"></i>
                    <h3 class="h5 mb-1">Follow on Instagram</h3>
                    <p class="text-muted font-size-sm">#ShopWithCartzilla</p>
                </div>
            </a></div>
    </div>
</section> --}}

<!-- Toast: Added to Cart-->
@endsection
