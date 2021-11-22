<div class="px-4 pt-lg-3 pb-3 mb-5">
    <div class="tab-content px-lg-3">
        <div class="tab-pane fade show active" id="general" role="tabpanel">
            <div class="row">
                <!-- Product gallery-->
                <div class="col-lg-7 pr-lg-0">
                    <div class="cz-product-gallery">
                        <div class="cz-preview order-sm-2">
                            @foreach($currentProduct->getImages() as $key => $value)
                                @if($key == 0)
                                    <div class="cz-preview-item active" id="img-{{$key}}"><img class="cz-image-zoom" src="{{ url($value->path) }}" data-zoom="{{ url($value->path) }}" alt="Product image">
                                        <div class="cz-image-zoom-pane"></div>
                                    </div>
                                @else
                                    <div class="cz-preview-item" id="img-{{$key}}"><img class="cz-image-zoom" src="{{ url($value->path) }}" data-zoom="{{ url($value->path) }}" alt="Product image">
                                        <div class="cz-image-zoom-pane"></div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        <div class="cz-thumblist order-sm-1">
                            @foreach($currentProduct->getImages() as $key => $value)
                                <a class="cz-thumblist-item" href="#img-{{$key}}"><img src="{{ url($value->path) }}" alt="Product thumb"></a>
                            @endforeach
                        </div>
                    </div>
                </div>
                <!-- Product details-->
                <div class="col-lg-5 pt-4 pt-lg-0">
                    <div class="product-details ml-auto">
                        <a href="{{ url('seller-shop/'.$parentProduct->seller->id) }}" class="d-inline-block font-size-sm text-body align-middle mt-1 ml-1">{{ $parentProduct->seller->visible_name }}</a>
                    </div>
                    <div class="product-details ml-auto pb-3">
                        @if ($selectedChildrenId)
                            @if ($currentProduct->special_price === $currentProduct->real_price)
                            <div class="mb-3"><span class="h3 font-weight-normal text-accent mr-1">{{ currencyFormat($currentProduct->special_price, defaultCurrency(), true) }}</span>
                                <del class="text-muted font-size-lg mr-3">{{ currencyFormat($currentProduct->price, defaultCurrency(), true) }}</del>
                                <br>
                                <span class="badge badge-warning badge-shadow align-middle mt-n2">Descuento</span>
                            </div>
                            @else
                                <div class="h3 font-weight-normal text-accent mb-3 mr-1">{{ currencyFormat($currentProduct->price, defaultCurrency(), true) }}</div>
                            @endif
                        @else
                            <div class="h3 font-weight-normal text-accent mb-1 mr-1">Desde {{ currencyFormat($priceFrom, 'CLP', true) }}</div>
                            @if ($parentProduct->has_special_price)
                                <span class="badge badge-warning badge-shadow align-middle mt-n2">Descuento</span>
                            @endif  
                        @endif
                    <!--
                            <div class="font-size-sm mb-4"><span class="text-heading font-weight-medium mr-1">Color:</span><span class="text-muted" id="colorOption">Dark blue/Orange</span></div>
                        -->
                        <div class="position-relative mr-n4 mb-3">
                            <!--
                                <div class="custom-control custom-option custom-control-inline mb-2">
                                    <input class="custom-control-input" type="radio" name="color" id="color1" data-label="colorOption" value="Dark blue/Orange" checked>
                                    <label class="custom-option-label rounded-circle" for="color1"><span class="custom-option-color rounded-circle" style="background-color: #f25540;"></span></label>
                                </div>
                                <div class="custom-control custom-option custom-control-inline mb-2">
                                    <input class="custom-control-input" type="radio" name="color" id="color2" data-label="colorOption" value="Dark blue/Green">
                                    <label class="custom-option-label rounded-circle" for="color2"><span class="custom-option-color rounded-circle" style="background-color: #65805b;"></span></label>
                                </div>
                                <div class="custom-control custom-option custom-control-inline mb-2">
                                    <input class="custom-control-input" type="radio" name="color" id="color3" data-label="colorOption" value="Dark blue/White">
                                    <label class="custom-option-label rounded-circle" for="color3"><span class="custom-option-color rounded-circle" style="background-color: #f5f5f5;"></span></label>
                                </div>
                                <div class="custom-control custom-option custom-control-inline mb-2">
                                    <input class="custom-control-input" type="radio" name="color" id="color4" data-label="colorOption" value="Dark blue/Black">
                                    <label class="custom-option-label rounded-circle" for="color4"><span class="custom-option-color rounded-circle" style="background-color: #333;"></span></label>
                                </div>
                            -->
                            @if ($currentProduct->haveSufficientQuantity(1))
                                <div class="product-badge product-available mt-n5"><i class="czi-security-check"></i>Producto disponible</div>
                            @else
                                <div class="product-badge product-not-available mt-n5"><i class="czi-security-close"></i>Producto no disponible</div>
                            @endif
                        </div>
                        {{-- @if ($selectedChildrenId)
                        <div class="d-flex align-items-center pt-2 pb-4">
                            <span><h4> {{ $currentProduct->name }}</h4></span>
                        </div>
                        @endif --}}
                        <div class="product-details mt-2 mb-3 ml-auto">
                            <span class="h6">Selecciona tus opciones para añadir al carro.</span>
                        </div>
                        @foreach ($options as $key => $option)
                            <div class="form-group">
                                <div class="d-flex justify-content-between align-items-center pb-1">
                                    <label class="font-weight-medium" for="product-size">{{ $option['name'] }}</label>
                                </div>
                                <select class="custom-select"
                                    {{-- wire:change="updatedOptions" --}}
                                    wire:model="options.{{ $key }}.selectedValue"
                                    name="attribute-{{ $option['id'] }}"
                                    @if(!$option['enableOptions']) disabled @endif
                                >
                                    @foreach ($option['items'] as $item)
                                        <option value="{{ $item }}">{{ $item }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach
                        @if ($selectedChildrenId && $currentProduct->haveSufficientQuantity(1) )
                        <div class="d-flex align-items-center pt-2 pb-4">
                            @livewire('qty-item', [
                                'qty' => 1,
                                'emitTo' => [
                                    'addtocart.cant',
                                ]
                            ])
                            <div style="margin-top: 14px">
                            @livewire('products.add-to-cart',['product' => $currentProduct, 'view' => 'single'])
                            </div>
                        </div>
                        @endif
                        <p> Información sobre el vendedor </p>
                        @if($parentProduct->seller->addresses)
                            @php
                                $sellerAddress = $parentProduct->seller->addresses[0];
                            @endphp
                            <small><strong>Dirección</strong></small>
                            @if($sellerAddress->commune)
                                <p class="font-size-ms text-muted mb-0">Comuna: {{$sellerAddress->commune->name}}</p>
                            @endif
                            @if($sellerAddress->street)
                                <p class="font-size-ms text-muted mb-0">Calle: {{$sellerAddress->street}}</p>
                            @endif
                            @if($sellerAddress->number)
                                <p class="font-size-ms text-muted mb-0">Número: {{$sellerAddress->number}}</p>
                            @endif
                            @if($sellerAddress->subnumber)
                                <p class="font-size-ms text-muted mb-0">Casa/Dpto/Oficina: {{$sellerAddress->subnumber}}</p>
                            @endif
                            @if($sellerAddress->email)
                                <p class="font-size-ms text-muted mb-0">Email: {{$sellerAddress->email}}</p>
                            @endif
                            @if($sellerAddress->cellphone)
                                <p class="font-size-ms text-muted mb-0">Tél.: {{$sellerAddress->cellphone}} </p>
                            @endif
                            @if($sellerAddress->phone)
                                <p class="font-size-ms text-muted mb-0">Tél.: {{$sellerAddress->phone}} </p>
                            @endif
                            <br>
                        @endif
                        <small>
                            <p>Métodos de envíos disponibles (puede variar de acuerdo a la comuna):</p>

                            <ul>
                                @foreach($parentProduct->seller->getResumeAvailableShippingMethods() as $method)
                                    <li>{{$method->title}}</li>
                                @endforeach
                            </ul>
                        </small>
                        <!--
                            <div class="d-flex mb-4">
                                <div class="w-100 mr-3">
                                    <button class="btn btn-secondary btn-block" type="button"><i class="czi-heart font-size-lg mr-2"></i><span class='d-none d-sm-inline'>Add to </span>Wishlist</button>
                                </div>
                                <div class="w-100">
                                    <button class="btn btn-secondary btn-block" type="button"><i class="czi-compare font-size-lg mr-2"></i>Compare</button>
                                </div>
                            </div>
                        -->
                        <!-- Product panels-->
                        {{-- <div class="accordion mb-4" id="productPanels">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="accordion-heading"><a href="#shippingOptions" role="button" data-toggle="collapse" aria-expanded="true" aria-controls="shippingOptions"><i class="czi-delivery text-muted lead align-middle mt-n1 mr-2"></i>Opciones de envío<span class="accordion-indicator"></span></a></h3>
                                </div>
                                <div class="collapse show" id="shippingOptions" data-parent="#productPanels">
                                    <div class="card-body font-size-sm">
                                        <div class="d-flex justify-content-between border-bottom pb-2">
                                            <div>
                                                <div class="font-weight-semibold text-dark">Local courier shipping</div>
                                                <div class="font-size-sm text-muted">2 - 4 days</div>
                                            </div>
                                            <div>$16.50</div>
                                        </div>
                                        <div class="d-flex justify-content-between border-bottom py-2">
                                            <div>
                                                <div class="font-weight-semibold text-dark">UPS ground shipping</div>
                                                <div class="font-size-sm text-muted">4 - 6 days</div>
                                            </div>
                                            <div>$19.00</div>
                                        </div>
                                        <div class="d-flex justify-content-between pt-2">
                                            <div>
                                                <div class="font-weight-semibold text-dark">Local pickup from store</div>
                                                <div class="font-size-sm text-muted">&mdash;</div>
                                            </div>
                                            <div>$0.00</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="accordion-heading"><a class="collapsed" href="#localStore" role="button" data-toggle="collapse" aria-expanded="true" aria-controls="localStore"><i class="czi-location text-muted font-size-lg align-middle mt-n1 mr-2"></i>Enviar a casa<span class="accordion-indicator"></span></a></h3>
                                </div>
                                <div class="collapse" id="localStore" data-parent="#productPanels">
                                    <div class="card-body">
                                        <select class="custom-select">
                                            <option value>Selecciona tu comuna</option>
                                            <option value="Argentina">Argentina</option>
                                            <option value="Belgium">Belgium</option>
                                            <option value="France">France</option>
                                            <option value="Germany">Germany</option>
                                            <option value="Spain">Spain</option>
                                            <option value="UK">United Kingdom</option>
                                            <option value="USA">USA</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                        <!-- Sharing-->
                        {{-- <h6 class="d-inline-block align-middle font-size-base my-2 mr-2">Share:</h6><a class="share-btn sb-twitter mr-2 my-2" href="#"><i class="czi-twitter"></i>Twitter</a><a class="share-btn sb-instagram mr-2 my-2" href="#"><i class="czi-instagram"></i>Instagram</a><a class="share-btn sb-facebook my-2" href="#"><i class="czi-facebook"></i>Facebook</a> --}}
                    </div>
                </div>
            </div>
        </div>
        <!-- Tech specs tab-->
        <div class="tab-pane fade" id="specs" role="tabpanel">
            <div class="d-md-flex justify-content-between align-items-start pb-4 mb-4 border-bottom">
                <div class="media align-items-center mr-md-3"><img src="{{ url($currentProduct->getFirstImagePath()) }}" width="90" alt="Product thumb">
                    <div class="mdeia-body pl-3">
                        <h6 class="font-size-base mb-2">{{$currentProduct->name}}</h6>
                        {{-- @if ($selectedChildrenId)
                        <div class="h4 font-weight-normal text-accent">{{ currencyFormat($currentProduct->price, 'CLP', true) }}</div>
                        @endif --}}
                        @if ($selectedChildrenId)
                            @if ($currentProduct->special_price === $currentProduct->real_price)
                            <div class="mb-3"><span class="h4 font-weight-normal text-accent">{{ currencyFormat($currentProduct->special_price, defaultCurrency(), true) }}</span>
                                <del class="text-muted font-size-lg mr-3">{{ currencyFormat($currentProduct->price, defaultCurrency(), true) }}</del>
                                <br>
                                <span class="badge badge-warning badge-shadow align-middle mt-n2">Descuento</span>
                            </div>
                            @else
                                <div class="h4 font-weight-normal text-accent">{{ currencyFormat($currentProduct->price, defaultCurrency(), true) }}</div>
                            @endif
                        @else
                            <div class="h4 font-weight-normal text-accent">Desde {{ currencyFormat($priceFrom, 'CLP', true) }}</div>
                        @endif
                    </div>
                </div>
                @if ( $selectedChildrenId && $currentProduct->haveSufficientQuantity(1) )
                <div class="d-flex align-items-center pt-3">
                    @livewire('qty-item', [
                        'qty' => 1,
                        'emitTo' => [
                            'addtocart.cant',
                        ]
                    ])
                    <div style="margin-left: 8px; margin-top: 14px">
                        @livewire('products.add-to-cart',['product' => $currentProduct, 'view' => 'single'])
                    </div>
                    {{-- <div class="mr-2">
                        <button class="btn btn-secondary btn-icon" type="button" data-toggle="tooltip" title="Add to Wishlist"><i class="czi-heart font-size-lg"></i></button>
                    </div>
                    <div>
                        <button class="btn btn-secondary btn-icon" type="button" data-toggle="tooltip" title="Compare"><i class="czi-compare font-size-lg"></i></button>
                    </div> --}}
                </div>
                @endif
            </div>
            <!-- Specs table-->
            <div class="row pt-2">
                <div class="col-lg-5 col-sm-6">
                    @if ($selectedChildrenId)
                    <h3 class="h6">Especificaciones del producto seleccionado</h3>
                    <ul class="list-unstyled font-size-sm pb-2">
                        @foreach ($currentProduct->getAttributesWithNames() as $attribute)
                        <li class="d-flex justify-content-between pb-2 border-bottom"><span class="text-muted">{{ $attribute['name'] }}:</span><span>{{ $attribute['value'] }}</span></li>
                        @endforeach
                    </ul>
                    @endif
                    @if ( count($parentProduct->getAttributesWithNames()) )
                    <h3 class="h6">Especificaciones generales</h3>
                    <ul class="list-unstyled font-size-sm pb-2">
                        @foreach ($parentProduct->getAttributesWithNames() as $attribute)
                        <li class="d-flex justify-content-between pb-2 border-bottom"><span class="text-muted">{{ $attribute['name'] }}:</span><span>{{ $attribute['value'] }}</span></li>
                        @endforeach
                    </ul>
                    @endif

                    {{-- <h3 class="h6">General specs</h3>
                    <ul class="list-unstyled font-size-sm pb-2">
                        <li class="d-flex justify-content-between pb-2 border-bottom"><span class="text-muted">Model:</span><span>Amazfit Smartwatch</span></li>
                        <li class="d-flex justify-content-between pb-2 border-bottom"><span class="text-muted">Gender:</span><span>Unisex</span></li>
                        <li class="d-flex justify-content-between pb-2 border-bottom"><span class="text-muted">Smartphone app:</span><span>Amazfit Watch</span></li>
                        <li class="d-flex justify-content-between pb-2 border-bottom"><span class="text-muted">OS campitibility:</span><span>Android / iOS</span></li>
                    </ul>
                    <h3 class="h6">Physical specs</h3>
                    <ul class="list-unstyled font-size-sm pb-2">
                        <li class="d-flex justify-content-between pb-2 border-bottom"><span class="text-muted">Shape:</span><span>Rectangular</span></li>
                        <li class="d-flex justify-content-between pb-2 border-bottom"><span class="text-muted">Body material:</span><span>Plastics / Ceramics</span></li>
                        <li class="d-flex justify-content-between pb-2 border-bottom"><span class="text-muted">Band material:</span><span>Silicone</span></li>
                    </ul>
                    <h3 class="h6">Display</h3>
                    <ul class="list-unstyled font-size-sm pb-2">
                        <li class="d-flex justify-content-between pb-2 border-bottom"><span class="text-muted">Display type:</span><span>Color</span></li>
                        <li class="d-flex justify-content-between pb-2 border-bottom"><span class="text-muted">Display size:</span><span>1.28"</span></li>
                        <li class="d-flex justify-content-between pb-2 border-bottom"><span class="text-muted">Screen resolution:</span><span>176 x 176</span></li>
                        <li class="d-flex justify-content-between pb-2 border-bottom"><span class="text-muted">Touch screen:</span><span>No</span></li>
                    </ul> --}}
                </div>
                <div class="col-lg-5 col-sm-6 offset-lg-1">
                    @if ($selectedChildrenId && !$currentProduct->is_service)
                    <h3 class="h6">Dimensiones de envio</h3>
                    <ul class="list-unstyled font-size-sm pb-2">
                        <li class="d-flex justify-content-between pb-2 border-bottom"><span class="text-muted">Peso:</span><span>{{ number_format($currentProduct->weight, 2, ',', '.') }} kg</span></li>
                        <li class="d-flex justify-content-between pb-2 border-bottom"><span class="text-muted">Alto:</span><span>{{ number_format($currentProduct->height, 2, ',', '.') }} cm</span></li>
                        <li class="d-flex justify-content-between pb-2 border-bottom"><span class="text-muted">Largo:</span><span>{{ number_format($currentProduct->depth, 2, ',', '.') }} cm</span></li>
                        <li class="d-flex justify-content-between pb-2 border-bottom"><span class="text-muted">Ancho:</span><span>{{ number_format($currentProduct->width, 2, ',', '.') }} cm</span></li>
                    </ul>
                    @endif
                   {{-- <h3 class="h6">Functions</h3>
                     <ul class="list-unstyled font-size-sm pb-2">
                        <li class="d-flex justify-content-between pb-2 border-bottom"><span class="text-muted">Phone calls:</span><span>Incoming call notification</span></li>
                        <li class="d-flex justify-content-between pb-2 border-bottom"><span class="text-muted">Monitoring:</span><span>Heart rate / Physical activity</span></li>
                        <li class="d-flex justify-content-between pb-2 border-bottom"><span class="text-muted">GPS support:</span><span>Yes</span></li>
                        <li class="d-flex justify-content-between pb-2 border-bottom"><span class="text-muted">Sensors:</span><span>Heart rate, Gyroscope, Geomagnetic, Light sensor</span></li>
                    </ul>
                    <h3 class="h6">Battery</h3>
                    <ul class="list-unstyled font-size-sm pb-2">
                        <li class="d-flex justify-content-between pb-2 border-bottom"><span class="text-muted">Battery:</span><span>Li-Pol</span></li>
                        <li class="d-flex justify-content-between pb-2 border-bottom"><span class="text-muted">Battery capacity:</span><span>190 mAh</span></li>
                    </ul>
                    <h3 class="h6">Dimensions</h3>
                    <ul class="list-unstyled font-size-sm pb-2">
                        <li class="d-flex justify-content-between pb-2 border-bottom"><span class="text-muted">Dimensions:</span><span>195 x 20 mm</span></li>
                        <li class="d-flex justify-content-between pb-2 border-bottom"><span class="text-muted">Weight:</span><span>32 g</span></li>
                    </ul> --}}
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    
<script>

    window.addEventListener('initialize-gallery', event => {
        intializeGallery()
    })

    function intializeGallery() {      
        
        // @todo ver como destruir la instancia previa de lightgallery
        gallery()
        productGallery()
        imageZoom()   
    }

    function gallery() {
        var e = document.querySelectorAll(".cz-gallery");
            if (e.length)
                for (var t = 0; t < e.length; t++)
                    lightGallery(e[t], { selector: ".gallery-item", download: !1, videojs: !0, youtubePlayerParams: { modestbranding: 1, showinfo: 0, rel: 0, controls: 0 }, vimeoPlayerParams: { byline: 0, portrait: 0, color: "fe696a" } });
    }

    function productGallery() {
        var s = document.querySelectorAll(".cz-product-gallery");
            if (s.length)
                for (
                    var e = function (r) {
                            for (var n = s[r].querySelectorAll(".cz-thumblist-item:not(.video-item)"), o = s[r].querySelectorAll(".cz-preview-item"), e = s[r].querySelectorAll(".cz-thumblist-item.video-item"), t = 0; t < n.length; t++)
                                n[t].addEventListener("click", a);
                            function a(e) {
                                e.preventDefault();
                                for (var t = 0; t < n.length; t++) o[t].classList.remove("active"), n[t].classList.remove("active");
                                this.classList.add("active"), s[r].querySelector(this.getAttribute("href")).classList.add("active");
                            }
                            for (var i = 0; i < e.length; i++)
                                lightGallery(e[i], {
                                    selector: "this",
                                    download: !1,
                                    videojs: !0,
                                    youtubePlayerParams: { modestbranding: 1, showinfo: 0, rel: 0, controls: 0 },
                                    vimeoPlayerParams: { byline: 0, portrait: 0, color: "fe696a" },
                                });
                        },
                        t = 0;
                    t < s.length;
                    t++
                )
                    e(t);
    }

    function imageZoom() {
        for (var e = document.querySelectorAll(".cz-image-zoom"), t = 0; t < e.length; t++) new Drift(e[t], { paneContainer: e[t].parentElement.querySelector(".cz-image-zoom-pane") });
    }

</script>

@endpush
