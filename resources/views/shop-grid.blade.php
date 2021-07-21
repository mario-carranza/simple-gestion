@extends('layouts.base', ['showFilters' => $data['showFilters'] ?? false])

@section('content')
<!-- Page Title-->
<div class="page-title-overlap bg-dark pt-4 bg-cp-gradient">
    <div class="container d-lg-flex justify-content-between py-2 py-lg-3">
        <div class="order-lg-1 pr-lg-4 text-center text-lg-left">
            <h1 class="h3 text-light mb-0">Búsqueda de productos</h1>
        </div>
        @if (isset($data['category']) && $data['category'] != 0)
        <x-category-breadcrumbs :categoryId="$data['category']"></x-category-bradcrumbs>
        @endif
    </div>
</div>
<!-- Page Content-->
<div class="container pb-5 mb-2 mb-md-4">
    <div class="row">
        <!-- Sidebar-->
        <aside class="col-lg-4">
                @livewire('filters', ['data' => $data])
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
                @livewire('products.card-general', ['columnLg' => 3, 'showPaginate' => true, 'paginateBy' => 16, 'showFrom' => $render['view'], 'valuesQuery' => $data, 'renderIn' => (Request::get('render'))?:'shop-grid'])
            </div>
            <hr class="my-3">
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
    /* $(document).ready(function(){
        const urlParams = new URLSearchParams(window.location.search);
        let field = urlParams.get('field')
        let render = urlParams.get('render')
        let direction = urlParams.get('direction');
        let value = field + '-' + direction
        if(render){
            if(render == 'shop-grid'){            
                $('.btn-shop-grid').removeClass('nav-link-light');
                $('.btn-shop-grid').addClass('bg-light text-dark disabled opacity-100 mr-2');
                $('.btn-shop-list').removeClass('disabled bg-light');

            }else{
                $('.btn-shop-list').removeClass('nav-link-light');
                $('.btn-shop-list').addClass('bg-light text-dark disabled opacity-100 mr-2');
                $('.btn-shop-grid').removeClass('disabled bg-light');
            }
        }

        // Set option selected
        switch (value) {
            case 'name-ASC':
                $('#sorting').val('4')
                break;
            case 'name-DESC':
                $('#sorting').val('5')
                break;
            case 'price-ASC':
                $('#sorting').val('2')
                break;
            case 'price-DESC':
                $('#sorting').val('3')
                break;
        }

        var url = window.location.href.split('?')[0];

        $('#sorting').change(function(){
            window.location.href = url + '?field=' + $(this).find(':selected').data('field') + '&direction=' + $(this).find(':selected').data('direction')+'&render='+render;
        });
}); */
</script>
@endpush
