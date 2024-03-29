@php
    $dayNames = [
        0 => 'Lunes',
        1 => 'Martes',
        2 => 'Miercoles',
        3 => 'Jueves',
        4 => 'Viernes',
        5 => 'Sabados',
        6 => 'Domingos',
    ]
@endphp

<div class="px-4 pt-lg-3 pb-3 mb-5">
    <div class="tab-content px-lg-3">
        <!-- General info tab-->
        <div class="tab-pane fade show active" id="general" role="tabpanel">

            <div class="row">
                <!-- Product gallery-->
                <div class="col-lg-7 pr-lg-0">
                    <div class="cz-product-gallery">
                        <div class="cz-preview order-sm-2">
                            @foreach($product->getImages() as $key => $value)
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
                            @foreach($product->getImages() as $key => $value)
                                <a class="cz-thumblist-item" href="#img-{{$key}}"><img src="{{ url($value->path) }}" alt="Product thumb"></a>
                            @endforeach
                        </div>
                    </div>
                </div>
                <!-- Product details-->
                <div class="col-lg-5 pt-4 pt-lg-0">
                    <div class="product-details ml-auto">
                        <span class="d-inline-block font-size-sm align-middle mt-1">Lo ofrece: </span><a href="{{ url('seller-shop/'.$product->seller->id) }}" class="d-inline-block font-size-sm align-middle mt-1 ml-1">{{ $product->seller->visible_name }}</a>
                    </div>
                    <div class="product-details ml-auto pb-3">
                        @if ($product->is_tour)
{{--                             <div class="h4 font-weight-normal text-accent mb-1 mr-1">Adultos: {{ currencyFormat($product->tour_information['adults_price'], 'CLP', true) }}</div>
                            <div class="h4 font-weight-normal text-accent mb-3 mr-1">Niños: {{ currencyFormat($product->tour_information['childrens_price'], 'CLP', true) }}</div> 
 --}}                        @elseif($product->is_housing)
                            <div class="h4 font-weight-normal text-accent mb-3 mr-1">
                                {{ currencyFormat($product->getHousingPriceRange()[0], 'CLP', true) }} - {{ currencyFormat($product->getHousingPriceRange()[1], 'CLP', true) }}
                            </div>
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
                            {{-- @if ($product->haveSufficientQuantity(1))
                                <div class="product-badge product-available mt-n5"><i class="czi-security-check"></i>Producto disponible</div>
                            @else
                                <div class="product-badge product-not-available mt-n5"><i class="czi-security-close"></i>Producto no disponible</div>
                            @endif --}}
                        </div>
                        <!--
                            <div class="form-group">
                                <div class="d-flex justify-content-between align-items-center pb-1">
                                    <label class="font-weight-medium" for="product-size">Size:</label><a class="nav-link-style font-size-sm" href="#size-chart" data-toggle="modal"><i class="czi-ruler lead align-middle mr-1 mt-n1"></i>Size guide</a>
                                </div>
                                <select class="custom-select" required id="product-size">
                                    <option value="">Select size</option>
                                    <option value="xs">XS</option>
                                    <option value="s">S</option>
                                    <option value="m">M</option>
                                    <option value="l">L</option>
                                    <option value="xl">XL</option>
                                </select>
                            </div>
                        -->
                        @if ($product->haveSufficientQuantity(1))
                            <div class="mb-3">
                                @livewire('products.housing-reservation-form', ['product' => $product], key($product->id))
                                @if ($product->terms_and_conditions) 
                                    <div class="text-center">
                                        <button class="btn btn-link" data-toggle="modal" data-target="#termsModal">
                                            Términos y condiciones
                                        </button>
                                    </div>
                                @endif 
                            </div>
                        @endif


                        @if ($product->is_tour)
                            <div class="mb-4">
                                <h4>Horarios disponibles</h4>
                                @foreach (collect($product->tour_information)->sortBy('day') as $item)
                                    <small>Todos los {{ $dayNames[$item['day']] }} las {{ Carbon\Carbon::parse($item['hour'])->format('h:i a') }}</small><br>
                                @endforeach
                            </div>
                        @endif
                        <p> Información sobre el vendedor </p>
                        @if($product->seller->addresses)
                            @php
                                $sellerAddress = $product->seller->addresses[0];
                            @endphp
                            <small><strong>Dirección</strong></small>
                            @if($sellerAddress->commune_id)
                                <p class="font-size-ms text-muted mb-0">Comuna: {{\App\Models\Commune::find($sellerAddress->commune_id)->name}}</p>
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
                            @if ($product->seller->email)
                                <p class="font-size-ms text-muted mb-0">Email: {{$product->seller->email}}</p>
                            @endif
                            @if ($product->seller->phone)
                                <p class="font-size-ms text-muted mb-0">Teléfono: {{$product->seller->phone}}</p>
                            @endif
                            @if ($product->seller->cellphone)
                                <p class="font-size-ms text-muted mb-0">Celular: {{$product->seller->cellphone}}</p>
                            @endif
                            <br>
                        @endif
                        {{-- <small>
                            <p>Métodos de envíos disponibles (puede variar de acuerdo a la comuna):</p>
                            <ul>
                                @foreach($product->seller->getResumeAvailableShippingMethods() as $method)
                                    <li>{{$method->title}}</li>
                                @endforeach
                            </ul>
                        </small>
                        @if(isset($product->seller->maximun_days_for_shipped) && $product->seller->maximun_days_for_shipped > 0)
                            <p><small>Cantidad de días máximo para envío del producto: {{$product->seller->maximun_days_for_shipped}}</small></p>
                        @endif --}}

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
                <div class="media align-items-center mr-md-3"><img src="{{ url($product->getFirstImagePath()) }}" width="90" alt="Product thumb">
                    <div class="mdeia-body pl-3">
                        <h6 class="font-size-base mb-2">{{$product->name}}</h6>
                    </div>
                </div>
                @if ($product->haveSufficientQuantity(1))
                <div class="d-flex align-items-center pt-3">
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
                    @if ( count($product->getAttributesWithNames()) )
                    <h3 class="h6">Especificaciones generales</h3>
                    <ul class="list-unstyled font-size-sm pb-2">
                        @foreach ($product->getAttributesWithNames() as $attribute)
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
                    @if (!$product->is_service)
                        <h3 class="h6">Dimensiones de envio</h3>
                        <ul class="list-unstyled font-size-sm pb-2">
                            <li class="d-flex justify-content-between pb-2 border-bottom"><span class="text-muted">Peso:</span><span>{{ number_format($product->weight, 2, ',', '.') }} kg</span></li>
                            <li class="d-flex justify-content-between pb-2 border-bottom"><span class="text-muted">Alto:</span><span>{{ number_format($product->height, 2, ',', '.') }} cm</span></li>
                            <li class="d-flex justify-content-between pb-2 border-bottom"><span class="text-muted">Largo:</span><span>{{ number_format($product->depth, 2, ',', '.') }} cm</span></li>
                            <li class="d-flex justify-content-between pb-2 border-bottom"><span class="text-muted">Ancho:</span><span>{{ number_format($product->width, 2, ',', '.') }} cm</span></li>
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
        <!-- Reviews tab-->
        {{-- <div class="tab-pane fade" id="reviews" role="tabpanel">
            <div class="d-md-flex justify-content-between align-items-start pb-4 mb-4 border-bottom">
                <div class="media align-items-center mr-md-3"><img src="img/shop/single/gallery/th05.jpg" width="90" alt="Product thumb">
                    <div class="mdeia-body pl-3">
                        <h6 class="font-size-base mb-2">{{$product->name}}</h6>
                        <div class="h4 font-weight-normal text-accent">{{ currencyFormat($product->price, 'CLP', true) }}</div>
                    </div>
                </div>
                <div class="d-flex align-items-center pt-3">
                    <select class="custom-select mr-2" style="width: 5rem;">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                    <button class="btn btn-primary btn-shadow mr-2" type="button"><i class="czi-cart font-size-lg mr-sm-2"></i><span class="d-none d-sm-inline">Add to Cart</span></button>
                    <div class="mr-2">
                        <button class="btn btn-secondary btn-icon" type="button" data-toggle="tooltip" title="Add to Wishlist"><i class="czi-heart font-size-lg"></i></button>
                    </div>
                    <div>
                        <button class="btn btn-secondary btn-icon" type="button" data-toggle="tooltip" title="Compare"><i class="czi-compare font-size-lg"></i></button>
                    </div>
                </div>
            </div>
            <!-- Reviews-->
            <div class="row pt-2 pb-3">
                <div class="col-lg-4 col-md-5">
                    <h2 class="h3 mb-4">74 Reviews</h2>
                    <div class="star-rating mr-2"><i class="czi-star-filled font-size-sm text-accent mr-1"></i><i class="czi-star-filled font-size-sm text-accent mr-1"></i><i class="czi-star-filled font-size-sm text-accent mr-1"></i><i class="czi-star-filled font-size-sm text-accent mr-1"></i><i class="czi-star font-size-sm text-muted mr-1"></i></div><span class="d-inline-block align-middle">4.1 Overall rating</span>
                    <p class="pt-3 font-size-sm text-muted">58 out of 74 (77%)<br>Customers recommended this product</p>
                </div>
                <div class="col-lg-8 col-md-7">
                    <div class="d-flex align-items-center mb-2">
                        <div class="text-nowrap mr-3"><span class="d-inline-block align-middle text-muted">5</span><i class="czi-star-filled font-size-xs ml-1"></i></div>
                        <div class="w-100">
                            <div class="progress" style="height: 4px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 60%;" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div><span class="text-muted ml-3">43</span>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <div class="text-nowrap mr-3"><span class="d-inline-block align-middle text-muted">4</span><i class="czi-star-filled font-size-xs ml-1"></i></div>
                        <div class="w-100">
                            <div class="progress" style="height: 4px;">
                                <div class="progress-bar" role="progressbar" style="width: 27%; background-color: #a7e453;" aria-valuenow="27" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div><span class="text-muted ml-3">16</span>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <div class="text-nowrap mr-3"><span class="d-inline-block align-middle text-muted">3</span><i class="czi-star-filled font-size-xs ml-1"></i></div>
                        <div class="w-100">
                            <div class="progress" style="height: 4px;">
                                <div class="progress-bar" role="progressbar" style="width: 17%; background-color: #ffda75;" aria-valuenow="17" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div><span class="text-muted ml-3">9</span>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <div class="text-nowrap mr-3"><span class="d-inline-block align-middle text-muted">2</span><i class="czi-star-filled font-size-xs ml-1"></i></div>
                        <div class="w-100">
                            <div class="progress" style="height: 4px;">
                                <div class="progress-bar" role="progressbar" style="width: 9%; background-color: #fea569;" aria-valuenow="9" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div><span class="text-muted ml-3">4</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="text-nowrap mr-3"><span class="d-inline-block align-middle text-muted">1</span><i class="czi-star-filled font-size-xs ml-1"></i></div>
                        <div class="w-100">
                            <div class="progress" style="height: 4px;">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: 4%;" aria-valuenow="4" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div><span class="text-muted ml-3">2</span>
                    </div>
                </div>
            </div>
            <hr class="mt-4 pb-4 mb-3">
            <div class="row pb-4">
                <!-- Reviews list-->
                <div class="col-md-7">
                    <div class="d-flex justify-content-end pb-4">
                        <div class="form-inline flex-nowrap">
                            <label class="text-muted text-nowrap mr-2 d-none d-sm-block" for="sort-reviews">Sort by:</label>
                            <select class="custom-select custom-select-sm" id="sort-reviews">
                                <option>Newest</option>
                                <option>Oldest</option>
                                <option>Popular</option>
                                <option>High rating</option>
                                <option>Low rating</option>
                            </select>
                        </div>
                    </div>
                    <!-- Review-->
                    <div class="product-review pb-4 mb-4 border-bottom">
                        <div class="d-flex mb-3">
                            <div class="media media-ie-fix align-items-center mr-4 pr-2"><img class="rounded-circle" width="50" src="img/shop/reviews/01.jpg" alt="Rafael Marquez" />
                                <div class="media-body pl-3">
                                    <h6 class="font-size-sm mb-0">Rafael Marquez</h6><span class="font-size-ms text-muted">June 28, 2019</span>
                                </div>
                            </div>
                            <div>
                                <div class="star-rating"><i class="sr-star czi-star-filled active"></i><i class="sr-star czi-star-filled active"></i><i class="sr-star czi-star-filled active"></i><i class="sr-star czi-star-filled active"></i><i class="sr-star czi-star"></i>
                                </div>
                                <div class="font-size-ms text-muted">83% of users found this review helpful</div>
                            </div>
                        </div>
                        <p class="font-size-md mb-2">Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est...</p>
                        <ul class="list-unstyled font-size-ms pt-1">
                            <li class="mb-1"><span class="font-weight-medium">Pros:&nbsp;</span>Consequuntur magni, voluptatem sequi, tempora</li>
                            <li class="mb-1"><span class="font-weight-medium">Cons:&nbsp;</span>Architecto beatae, quis autem</li>
                        </ul>
                        <div class="text-nowrap">
                            <button class="btn-like" type="button">15</button>
                            <button class="btn-dislike" type="button">3</button>
                        </div>
                    </div>
                    <!-- Review-->
                    <div class="product-review pb-4 mb-4 border-bottom">
                        <div class="d-flex mb-3">
                            <div class="media media-ie-fix align-items-center mr-4 pr-2"><img class="rounded-circle" width="50" src="img/shop/reviews/02.jpg" alt="Barbara Palson" />
                                <div class="media-body pl-3">
                                    <h6 class="font-size-sm mb-0">Barbara Palson</h6><span class="font-size-ms text-muted">May 17, 2019</span>
                                </div>
                            </div>
                            <div>
                                <div class="star-rating"><i class="sr-star czi-star-filled active"></i><i class="sr-star czi-star-filled active"></i><i class="sr-star czi-star-filled active"></i><i class="sr-star czi-star-filled active"></i><i class="sr-star czi-star-filled active"></i>
                                </div>
                                <div class="font-size-ms text-muted">99% of users found this review helpful</div>
                            </div>
                        </div>
                        <p class="font-size-md mb-2">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
                        <ul class="list-unstyled font-size-ms pt-1">
                            <li class="mb-1"><span class="font-weight-medium">Pros:&nbsp;</span>Consequuntur magni, voluptatem sequi, tempora</li>
                            <li class="mb-1"><span class="font-weight-medium">Cons:&nbsp;</span>Architecto beatae, quis autem</li>
                        </ul>
                        <div class="text-nowrap">
                            <button class="btn-like" type="button">34</button>
                            <button class="btn-dislike" type="button">1</button>
                        </div>
                    </div>
                    <!-- Review-->
                    <div class="product-review pb-4 mb-4 border-bottom">
                        <div class="d-flex mb-3">
                            <div class="media media-ie-fix align-items-center mr-4 pr-2"><img class="rounded-circle" width="50" src="img/shop/reviews/03.jpg" alt="Daniel Adams" />
                                <div class="media-body pl-3">
                                    <h6 class="font-size-sm mb-0">Daniel Adams</h6><span class="font-size-ms text-muted">May 8, 2019</span>
                                </div>
                            </div>
                            <div>
                                <div class="star-rating"><i class="sr-star czi-star-filled active"></i><i class="sr-star czi-star-filled active"></i><i class="sr-star czi-star-filled active"></i><i class="sr-star czi-star"></i><i class="sr-star czi-star"></i>
                                </div>
                                <div class="font-size-ms text-muted">75% of users found this review helpful</div>
                            </div>
                        </div>
                        <p class="font-size-md mb-2">Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem.</p>
                        <ul class="list-unstyled font-size-ms pt-1">
                            <li class="mb-1"><span class="font-weight-medium">Pros:&nbsp;</span>Consequuntur magni, voluptatem sequi</li>
                            <li class="mb-1"><span class="font-weight-medium">Cons:&nbsp;</span>Architecto beatae, quis autem, voluptatem sequ</li>
                        </ul>
                        <div class="text-nowrap">
                            <button class="btn-like" type="button">26</button>
                            <button class="btn-dislike" type="button">9</button>
                        </div>
                    </div>
                    <div class="text-center">
                        <button class="btn btn-outline-accent" type="button"><i class="czi-reload mr-2"></i>Load more reviews</button>
                    </div>
                </div>
                <!-- Leave review form-->
                <div class="col-md-5 mt-2 pt-4 mt-md-0 pt-md-0">
                    <div class="bg-secondary py-grid-gutter px-grid-gutter rounded-lg">
                        <h3 class="h4 pb-2">Write a review</h3>
                        <form class="needs-validation" method="post" novalidate>
                            <div class="form-group">
                                <label for="review-name">Your name<span class="text-danger">*</span></label>
                                <input class="form-control" type="text" required id="review-name">
                                <div class="invalid-feedback">Please enter your name!</div><small class="form-text text-muted">Will be displayed on the comment.</small>
                            </div>
                            <div class="form-group">
                                <label for="review-email">Your email<span class="text-danger">*</span></label>
                                <input class="form-control" type="email" required id="review-email">
                                <div class="invalid-feedback">Please provide valid email address!</div><small class="form-text text-muted">Authentication only - we won't spam you.</small>
                            </div>
                            <div class="form-group">
                                <label for="review-rating">Rating<span class="text-danger">*</span></label>
                                <select class="custom-select" required id="review-rating">
                                    <option value="">Choose rating</option>
                                    <option value="5">5 stars</option>
                                    <option value="4">4 stars</option>
                                    <option value="3">3 stars</option>
                                    <option value="2">2 stars</option>
                                    <option value="1">1 star</option>
                                </select>
                                <div class="invalid-feedback">Please choose rating!</div>
                            </div>
                            <div class="form-group">
                                <label for="review-text">Review<span class="text-danger">*</span></label>
                                <textarea class="form-control" rows="6" required id="review-text"></textarea>
                                <div class="invalid-feedback">Please write a review!</div><small class="form-text text-muted">Your review must be at least 50 characters.</small>
                            </div>
                            <div class="form-group">
                                <label for="review-pros">Pros</label>
                                <textarea class="form-control" rows="2" placeholder="Separated by commas" id="review-pros"></textarea>
                            </div>
                            <div class="form-group mb-4">
                                <label for="review-cons">Cons</label>
                                <textarea class="form-control" rows="2" placeholder="Separated by commas" id="review-cons"></textarea>
                            </div>
                            <button class="btn btn-primary btn-shadow btn-block" type="submit">Submit a Review</button>
                        </form>
                    </div>
                </div>
            </div>
        </div> --}}
    </div>
</div>