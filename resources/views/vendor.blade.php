@extends('layouts.base')

@section('content')
<!-- Page title-->
<!-- Page Content-->
<!-- Header-->
<div class="page-title-overlap bg-accent pt-4 bg-cp-gradient">
    <div class="container d-flex flex-wrap flex-sm-nowrap justify-content-center justify-content-sm-between align-items-center pt-2">
        <div class="media media-ie-fix align-items-center pb-3">
            @if($seller->logo)
                <div class="img-thumbnail rounded-circle position-relative" style="width: 6.375rem;"><img class="rounded-circle" src="{{ url($seller->logo) }}" alt="Logo {{ $seller->visible_name }}"></div>
            @endif
            <div class="media-body pl-3">
                <h3 class="text-light font-size-lg mb-0">{{ $seller->visible_name }}</h3>
                {{-- <span class="d-block text-light font-size-ms opacity-60 py-1">Member since November 2017</span>
                <span class="badge badge-success"><i class="czi-check mr-1"></i>Available for freelance</span> --}}
            </div>
        </div>
        {{-- <div class="d-flex">
            <div class="text-sm-right mr-5">
                <div class="text-light font-size-base">Total sales</div>
                <h3 class="text-light">426</h3>
            </div>
            <div class="text-sm-right">
                <div class="text-light font-size-base">Seller rating</div>
                <div class="star-rating"><i class="sr-star czi-star-filled active"></i><i class="sr-star czi-star-filled active"></i><i class="sr-star czi-star-filled active"></i><i class="sr-star czi-star-filled active"></i><i class="sr-star czi-star"></i>
                </div>
                <div class="text-light opacity-60 font-size-xs">Based on 98 reviews</div>
            </div>
        </div> --}}
    </div>
</div>
<div class="container mb-5 pb-3">
    <div class="bg-light box-shadow-lg rounded-lg overflow-hidden">
        <div class="row">
            <!-- Sidebar-->
            <aside class="col-lg-4">
                <div class="cz-sidebar-static h-100 border-right">
                    <h6>Categoría</h6>
                    <p class="font-size-ms text-muted">{{ $seller->seller_category->name }}</p>
                    @if($seller->description)
                        <h6>Información</h6>
                        <p>{{$seller->description}}</p>
                    @endif
                    @if($seller->legal_representative_name)
                        <h6>Representante</h6>
                        <p class="font-size-ms text-muted mb-0">{{$seller->legal_representative_name}}</p>
                        <br>
                    @endif
                    @if($seller->addresses)
                        @php
                            $sellerAddress = $seller->addresses[0];
                        @endphp
                        <h6>Dirección</h6>
                        @if($sellerAddress->street)
                            <p class="font-size-ms text-muted mb-0">Calle: {{$sellerAddress->street}}</p>
                        @endif
                        @if($sellerAddress->number)
                            <p class="font-size-ms text-muted mb-0">Número: {{$sellerAddress->number}}</p>
                        @endif
                        @if($sellerAddress->subnumber)
                            <p class="font-size-ms text-muted mb-0">Casa/Dpto/Oficina: {{$sellerAddress->subnumber}}</p>
                        @endif
                        @if($sellerAddress->commune_id)
                            <p class="font-size-ms text-muted mb-0">Comuna: {{\App\Models\Commune::find($sellerAddress->commune_id)->name}}</p>
                        @endif
                        @if ($seller->email)
                            <p class="font-size-ms text-muted mb-0">Email: {{$seller->email}}</p>
                        @endif
                        @if ($seller->phone)
                            <p class="font-size-ms text-muted mb-0">Teléfono: {{$seller->phone}}</p>
                        @endif
                        @if ($seller->cellphone)
                            <p class="font-size-ms text-muted mb-0">Celular: {{$seller->cellphone}}</p>
                        @endif
                        <br>
                    @endif
                      @if($seller->web)
                        <h6>Web</h6>
                        <a 
                            class="font-size-ms text-muted" 
                            target="_blank"
                            @if (Illuminate\Support\Str::startsWith($seller->web, 'https://') || Illuminate\Support\Str::startsWith($seller->web, 'http://',))
                                href="{{ $seller->web }}" 
                            @else
                                href="{{ 'https://' . $seller->web }}" 
                            @endif
                        >
                            {{$seller->web}}
                        </a>
                        <br>
                        <br>
                    @endif
                    
                    @if($seller->contacts_data)
                        <h6>Redes Sociales</h6>
                            @foreach($seller->contacts_data as $socialMedia)
                                <a 
                                    class="font-size-ms text-muted" 
                                    target="_blank"
                                    @if (Illuminate\Support\Str::startsWith($socialMedia['url'], 'https://') || Illuminate\Support\Str::startsWith($socialMedia['url'], 'http://')) 
                                        href="{{ $socialMedia['url'] }}" 
                                    @else
                                        href="{{ 'https://' . $socialMedia['url'] }}" 
                                    @endif
                                >
                                    {{$socialMedia['url']}}
                                </a>                    
                            @endforeach
                        <br>
                        <br>
                    @endif
                    @if ($seller->privacy_policy || $seller->shipping_policy || $seller->return_policy)
                        <h6>Políticas</h6>
                    @endif

                    @if($seller->privacy_policy)
                        <a href="#" data-toggle="modal" data-policy="privacy_policy" data-target="#policy" class="font-size-ms text-muted go-policy">Políticas de privacidad</a>
                        <br>
                    @endif
                    @if($seller->shipping_policy)
                        <a href="#" data-toggle="modal" data-policy="shipping_policy" data-target="#policy" class="font-size-ms text-muted go-policy">Política de compra</a>
                        <br>
                    @endif
                    @if($seller->return_policy)
                        <a href="#" data-toggle="modal" data-policy="return_policy" data-target="#policy" class="font-size-ms text-muted go-policy">Política de devolución</a>
                    @endif
                    <hr class="my-4">
                    <!--
                        <h6>Contacts</h6>
                        <ul class="list-unstyled font-size-sm">
                            <li><a class="nav-link-style d-flex align-items-center" href="mailto:contact@example.com"><i class="czi-mail opacity-60 mr-2"></i>contact@example.com</a></li>
                            <li><a class="nav-link-style d-flex align-items-center" href="#"><i class="czi-globe opacity-60 mr-2"></i>www.createx.studio</a></li>
                        </ul><a class="social-btn sb-facebook sb-outline sb-sm mr-2 mb-2" href="#"><i class="czi-facebook"></i></a><a class="social-btn sb-twitter sb-outline sb-sm mr-2 mb-2" href="#"><i class="czi-twitter"></i></a><a class="social-btn sb-dribbble sb-outline sb-sm mr-2 mb-2" href="#"><i class="czi-dribbble"></i></a><a class="social-btn sb-behance sb-outline sb-sm mr-2 mb-2" href="#"><i class="czi-behance"></i></a>
                        <hr class="my-4">
                        <h6 class="pb-1">Send message</h6>
                        <form class="needs-validation pb-2" method="post" novalidate>
                            <div class="form-group">
                                <textarea class="form-control" rows="6" placeholder="Your message" required></textarea>
                                <div class="invalid-feedback">Please wirte your message!</div>
                            </div>
                            <button class="btn btn-primary btn-sm btn-block" type="submit">Send</button>
                        </form>
                    -->
                </div>
            </aside>
            <!-- Content-->
            <section class="col-lg-8 pt-lg-4 pb-md-4">
                <!-- Banner-->
                @if($seller->banner)
                <div class="py-sm-2">
                    <div class="d-sm-flex justify-content-between align-items-center overflow-hidden mb-4 rounded-lg">
                        <img class="mw-75" src="{{ url($seller->banner) }}" alt="Banner {{ $seller->visible_name }}">
                    </div>
                </div>
                @endif
                <!--
                    <div class="py-sm-2">
                        <div class="d-sm-flex justify-content-between align-items-center bg-secondary overflow-hidden mb-4 rounded-lg">
                            <div class="py-4 my-2 my-md-0 py-md-5 px-4 ml-md-3 text-center text-sm-left">
                                <h4 class="font-size-lg font-weight-light mb-2">Converse All Star</h4>
                                <h3 class="mb-4">Make Your Day Comfortable</h3><a class="btn btn-primary btn-shadow btn-sm" href="#">Shop Now</a>
                            </div><img class="d-block ml-auto" src="img/shop/catalog/banner.jpg" alt="Shop Converse">
                        </div>
                    </div>
                -->
                <div class="pt-2 px-4 pl-lg-0 pr-xl-5">
                    <h2 class="h3 pt-2 pb-4 mb-4 text-center text-sm-left border-bottom">Productos<span class="badge badge-secondary font-size-sm text-body align-middle ml-2">{{ $countProduct }}</span></h2>
                    <!-- Toolbar-->
                    <!--
                        <div class="d-flex justify-content-center justify-content-sm-between align-items-center pt-2 pb-4 pb-sm-5">
                            <div class="d-flex flex-wrap">
                                <div class="form-inline flex-nowrap mr-3 mr-sm-4 pb-3">
                                    <label class="text-dark opacity-75 text-nowrap mr-2 d-none d-sm-block" for="sorting">Sort by:</label>
                                    <select class="form-control custom-select" id="sorting">
                                        <option>Popularity</option>
                                        <option>Low - Hight Price</option>
                                        <option>High - Low Price</option>
                                        <option>Average Rating</option>
                                        <option>A - Z Order</option>
                                        <option>Z - A Order</option>
                                    </select><span class="font-size-sm text-dark opacity-75 text-nowrap ml-2 d-none d-md-block">of 287 products</span>
                                </div>
                            </div>
                            <div class="d-flex pb-3"><a class="nav-link-style nav-link-dark mr-3" href="#"><i class="czi-arrow-left"></i></a><span class="font-size-md text-dark">1 / 5</span><a class="nav-link-style nav-link-dark ml-3" href="#"><i class="czi-arrow-right"></i></a></div>
                            <div class="d-none d-sm-flex pb-3"><a class="btn btn-icon nav-link-style bg-dark text-light disabled opacity-100 mr-2" href="#"><i class="czi-view-grid"></i></a><a class="btn btn-icon nav-link-style nav-link-dark" href="shop-list-ls.html"><i class="czi-view-list"></i></a></div>
                        </div>
                    -->
                    <!-- Products grid-->
                    <div class="row mx-n2">
                        <!-- Product-->
                        @livewire('products.card-general', ['columnLg' => 3, 'showPaginate' => true, 'paginateBy' => 8, 'showFrom' => $render['view'], 'valuesQuery' => $data])
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<div class="modal" tabindex="-1" role="dialog" id="policy">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title title-policy"></h5>
      </div>
      <div class="modal-body">
        <div class="d-none privacy_policy policy-text">
            <p class="font-size-sm">{!!$seller->privacy_policy!!}</p>
        </div>
        <div class="d-none shipping_policy policy-text">
            <p class="font-size-sm">{!!$seller->shipping_policy!!}</p>
        </div>
        <div class="d-none return_policy policy-text">
            <p class="font-size-sm">{!!$seller->return_policy!!}</p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm btn-close-modal" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

@endsection
