@extends('travel.layouts.base')

@section('content')
<!-- Page Title-->
<div class="page-title-overlap bg-dark pt-4 bg-cp-gradient">
    <div class="container d-lg-flex justify-content-between py-2 py-lg-3">
        {{-- <div class="order-lg-2 mb-3 mb-lg-0 pt-lg-2">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-light flex-lg-nowrap justify-content-center justify-content-lg-start">
                    <li class="breadcrumb-item"><a class="text-nowrap" href="index.html"><i class="czi-home"></i>Home</a></li>
                    <li class="breadcrumb-item text-nowrap"><a href="#">Shop</a>
                    </li>
                    <li class="breadcrumb-item text-nowrap active" aria-current="page">Grid left sidebar</li>
                </ol>
            </nav>
        </div> --}}
        <div class="order-lg-1 pr-lg-4 text-center text-lg-left">
            <h1 class="h3 text-light mb-0">Búsqueda de productos</h1>
        </div>
    </div>
</div>
<!-- Page Content-->
<div class="container pb-5 mb-2 mb-md-4">
    <div class="row">
        <!-- Sidebar-->
        <aside class="col-lg-4">
            <div class="cz-sidebar rounded-lg box-shadow-lg" id="shop-sidebar">
                @livewire('filters', ['data' => $data])
            </div>
        </aside>
        <!-- Content  -->
        <section class="col-lg-8">
            <!-- Toolbar-->
            <div class="d-flex justify-content-center justify-content-sm-between align-items-center pt-2 pb-4 pb-sm-5">
                @livewire('sorting-products')
             {{--   <div class="d-flex pb-3"><a class="nav-link-style nav-link-light mr-3" href="#"><i class="czi-arrow-left"></i></a><span class="font-size-md text-light">1 / 5</span><a class="nav-link-style nav-link-light ml-3" href="#"><i class="czi-arrow-right"></i></a></div> --}}
             <div class="d-none d-sm-flex pb-3">
                    <a class="btn btn-icon nav-link-style bg-light text-dark disabled opacity-100 mr-2 btn-shop-grid" href="{{ url()->current().'?render=shop-grid' }}"><i class="czi-view-grid"></i></a>
                    <!--

                        <a class="btn btn-icon nav-link-style nav-link-light btn-shop-list" href="{{ url()->current().'?render=shop-list'}}"><i class="czi-view-list"></i></a>
                    -->
                </div>
            </div>
            <!-- Products grid-->
            <div class=" mx-n2 mt-5">
                @livewire('products.card-general', [
                    'columnLg' => 3, 
                    'showPaginate' => true, 
                    'paginateBy' => 16, 
                    'showFrom' => $render['view'], 
                    'valuesQuery' => $data, 
                    'renderIn' => (Request::get('render'))?:'shop-grid',
                    'sectionId' => $section->id,
                ])
            </div>
            <hr class="my-3">
        </section>
    </div>
</div>
@endsection