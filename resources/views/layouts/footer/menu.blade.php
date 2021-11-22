@php
    $collaboration = (isset($collaboration)) ? $collaboration : false;
@endphp

<!-- Toast: Added to Cart-->
<div class="toast-container toast-bottom-center">
    <div class="toast mb-3" id="cart-toast" data-delay="5000" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-success text-white"><i class="czi-check-circle mr-2"></i>
            <h6 class="font-size-sm text-white mb-0 mr-auto">Added to cart!</h6>
            <button class="close text-white ml-2 mb-1" type="button" data-dismiss="toast" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="toast-body">This item has been added to your cart.</div>
    </div>
</div>

<!-- Footer-->
<footer class="bg-light pt-5">
    @if($collaboration)
    <div class="container">
        <div class="row pb-2">
            {{-- <div class="col-md-4 col-sm-6">
                <div class="widget widget-links widget-dark pb-2 mb-4">
                    <h3 class="widget-title text-dark">Shop departments</h3>
                    <ul class="widget-list">
                        <li class="widget-list-item"><a class="widget-list-link" href="#">Sneakers &amp;
                                Athletic</a></li>
                        <li class="widget-list-item"><a class="widget-list-link" href="#">Athletic Apparel</a></li>
                        <li class="widget-list-item"><a class="widget-list-link" href="#">Sandals</a></li>
                        <li class="widget-list-item"><a class="widget-list-link" href="#">Jeans</a></li>
                        <li class="widget-list-item"><a class="widget-list-link" href="#">Shirts &amp; Tops</a></li>
                        <li class="widget-list-item"><a class="widget-list-link" href="#">Shorts</a></li>
                        <li class="widget-list-item"><a class="widget-list-link" href="#">T-Shirts</a></li>
                        <li class="widget-list-item"><a class="widget-list-link" href="#">Swimwear</a></li>
                        <li class="widget-list-item"><a class="widget-list-link" href="#">Clogs &amp; Mules</a></li>
                        <li class="widget-list-item"><a class="widget-list-link" href="#">Bags &amp; Wallets</a>
                        </li>
                        <li class="widget-list-item"><a class="widget-list-link" href="#">Accessories</a></li>
                        <li class="widget-list-item"><a class="widget-list-link" href="#">Sunglasses &amp;
                                Eyewear</a></li>
                        <li class="widget-list-item"><a class="widget-list-link" href="#">Watches</a></li>
                    </ul>
                </div>
            </div> --}}
            {{-- <div class="col-md-4 col-sm-6">
                <div class="widget widget-links widget-dark pb-2 mb-4">
                    <h3 class="widget-title text-dark">Menú</h3>
                    <ul class="widget-list">
                        <li class="widget-list-item"><a class="widget-list-link" href="#">Your account</a></li>
                        <li class="widget-list-item"><a class="widget-list-link" href="#">Shipping rates &amp;
                                policies</a></li>
                        <li class="widget-list-item"><a class="widget-list-link" href="#">Refunds &amp;
                                replacements</a></li>
                        <li class="widget-list-item"><a class="widget-list-link" href="#">Order tracking</a></li>
                        <li class="widget-list-item"><a class="widget-list-link" href="#">Delivery info</a></li>
                        <li class="widget-list-item"><a class="widget-list-link" href="#">Taxes &amp; fees</a></li>
                    </ul>
                </div>
                <div class="widget widget-links widget-dark pb-2 mb-4">
                    <h3 class="widget-title text-dark">About us</h3>
                    <ul class="widget-list">
                        <li class="widget-list-item"><a class="widget-list-link" href="#">About company</a></li>
                        <li class="widget-list-item"><a class="widget-list-link" href="#">Our team</a></li>
                        <li class="widget-list-item"><a class="widget-list-link" href="#">Careers</a></li>
                        <li class="widget-list-item"><a class="widget-list-link" href="#">News</a></li>
                    </ul>
                </div>
            </div> --}}
            <div class="col-md-12 mb-4 d-none d-md-flex justify-content-center">
                <img class="img-fluid w-75" src="{{ asset('img/logos/footer.png') }}" alt="Footer" />

                {{-- <div class="widget pb-2 mb-2">
                    <h3 class="widget-title text-dark pb-1">Organizan</h3>
                    <div class="d-flex flex-wrap">
                        <div class="col-6 mb-2 text-center">
                            <img class="img-fluid" src="{{ asset('img/logos/logo-crcp.png') }}" alt="CRCP" />
                        </div>
                        <div class="col-6 mb-2 text-center">
                            <img class="img-fluid" src="{{ asset('img/logos/logo-corfo.png') }}" alt="Corfo" />
                        </div>
                    </div>
                </div>
                <div class="widget pb-2 mb-2">
                    <h3 class="widget-title text-dark pb-1">Colaboran</h3>
                    <div class="d-flex flex-wrap">
                        <div class="col-6 col-md-2 mb-2 text-center">
                            <img class="img-fluid" src="{{ asset('img/logos/footer-twgroup.png') }}" alt="CRCP" />
                        </div>
                        <div class="col-6 col-md-2 mb-2 text-center">
                            <img class="img-fluid" src="{{ asset('img/logos/footer-worklab.png') }}" alt="Corfo" />
                        </div>
                        <div class="col-6 col-md-2 mb-2 text-center">
                            <img class="img-fluid" src="{{ asset('img/logos/footer-transbank.png') }}" alt="Corfo" />
                        </div>
                        <div class="col-6 col-md-2 mb-2 text-center">
                            <img class="img-fluid" src="{{ asset('img/logos/footer-pedidosya.png') }}" alt="Corfo" />
                        </div>
                        <div class="col-6 col-md-2 mb-2 text-center">
                            <img class="img-fluid" src="{{ asset('img/logos/footer-vr.png') }}" alt="Corfo" />
                        </div>
                        <div class="col-6 col-md-2 mb-2 text-center">
                            <img class="img-fluid" src="{{ asset('img/logos/footer-ani.png') }}" alt="Corfo" />
                        </div>
                    </div>
                </div> --}}
                {{-- <div class="widget pb-2 mb-4">
                    <h3 class="widget-title text-light pb-1">Stay informed</h3>
                    <form class="cz-subscribe-form validate" action="https://studio.us12.list-manage.com/subscribe/post?u=c7103e2c981361a6639545bd5&amp;amp;id=29ca296126" method="post" name="mc-embedded-subscribe-form" target="_blank" novalidate>
                        <div class="input-group input-group-overlay flex-nowrap">
                            <div class="input-group-prepend-overlay"><span class="input-group-text text-muted font-size-base"><i class="czi-mail"></i></span>
                            </div>
                            <input class="form-control prepended-form-control" type="email" name="EMAIL" placeholder="Your email" required>
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit" name="subscribe">Subscribe*</button>
                            </div>
                        </div>
                        <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
                        <div style="position: absolute; left: -5000px;" aria-hidden="true">
                            <input class="cz-subscribe-form-antispam" type="text" name="b_c7103e2c981361a6639545bd5_29ca296126" tabindex="-1">
                        </div><small class="form-text text-light opacity-50">*Subscribe to our newsletter to receive
                            early discount offers, updates and new products info.</small>
                        <div class="subscribe-status"></div>
                    </form>
                </div>
                <div class="widget pb-2 mb-4">
                    <h3 class="widget-title text-light pb-1">Download our app</h3>
                    <div class="d-flex flex-wrap">
                        <div class="mr-2 mb-2"><a class="btn-market btn-apple" href="#" role="button"><span class="btn-market-subtitle">Download on the</span><span class="btn-market-title">App
                                    Store</span></a></div>
                        <div class="mb-2"><a class="btn-market btn-google" href="#" role="button"><span class="btn-market-subtitle">Download on the</span><span class="btn-market-title">Google Play</span></a></div>
                    </div>
                </div> --}}
            </div>
            <div class="col-md-12 mb-4 d-block d-sm-block d-md-none">
                <img class="img-fluid" src="{{ asset('img/logos/footer_mobile.png') }}" alt="Footer" />
            </div>
        </div>
    </div>
    @endif
    <div class="pt-5 bg-darker">
        <div class="container">
            {{-- <div class="row pb-3">
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="media"><i class="czi-rocket text-primary" style="font-size: 2.25rem;"></i>
                        <div class="media-body pl-3">
                            <h6 class="font-size-base text-light mb-1">Fast and free delivery</h6>
                            <p class="mb-0 font-size-ms text-light opacity-50">Free delivery for all orders over
                                $200</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="media"><i class="czi-currency-exchange text-primary" style="font-size: 2.25rem;"></i>
                        <div class="media-body pl-3">
                            <h6 class="font-size-base text-light mb-1">Money back guarantee</h6>
                            <p class="mb-0 font-size-ms text-light opacity-50">We return money within 30 days</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="media"><i class="czi-support text-primary" style="font-size: 2.25rem;"></i>
                        <div class="media-body pl-3">
                            <h6 class="font-size-base text-light mb-1">24/7 customer support</h6>
                            <p class="mb-0 font-size-ms text-light opacity-50">Friendly 24/7 customer support</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="media"><i class="czi-card text-primary" style="font-size: 2.25rem;"></i>
                        <div class="media-body pl-3">
                            <h6 class="font-size-base text-light mb-1">Secure online payment</h6>
                            <p class="mb-0 font-size-ms text-light opacity-50">We possess SSL / Secure сertificate
                            </p>
                        </div>
                    </div>
                </div>
            </div> --}}
            {{-- <hr class="hr-light pb-4 mb-3"> --}}
            @if($footer)
            <div class="row pb-2">
                <div class="col-md-6 text-center text-md-left mb-4">
                    {{-- <div class="text-nowrap mb-4"><a class="d-inline-block align-middle mt-n1 mr-3" href="#"><img class="d-block" width="90" src="{{ asset('img/logo-pyme.png') }}" alt="Contigo Pyme" /></a>
                        <div class="btn-group dropdown disable-autohide">
                            <button class="btn btn-outline-light border-light btn-sm dropdown-toggle px-2" type="button" data-toggle="dropdown"><img class="mr-2" width="20" src="{{ asset('img/flags/en.png') }}" alt="English" />Eng / $
                            </button>
                            <ul class="dropdown-menu">
                                <li class="dropdown-item">
                                    <select class="custom-select custom-select-sm">
                                        <option value="usd">$ USD</option>
                                        <option value="eur">€ EUR</option>
                                        <option value="ukp">£ UKP</option>
                                        <option value="jpy">¥ JPY</option>
                                    </select>
                                </li>
                                <li><a class="dropdown-item pb-1" href="#"><img class="mr-2" width="20" src="{{ asset('img/flags/fr.png') }}" alt="Français" />Français</a></li>
                                <li><a class="dropdown-item pb-1" href="#"><img class="mr-2" width="20" src="{{ asset('img/flags/de.png') }}" alt="Deutsch" />Deutsch</a></li>
                                <li><a class="dropdown-item" href="#"><img class="mr-2" width="20" src="{{ asset('img/flags/it.png') }}" alt="Italiano" />Italiano</a></li>
                            </ul>
                        </div>
                    </div> --}}
                    <div class="widget widget-links widget-light">
                        <ul class="widget-list d-flex flex-wrap justify-content-center justify-content-md-start">
                            <li class="widget-list-item mr-4"><a class="widget-list-link" href="{{ url('faq') }}">Preguntas frecuentes</a></li>
                            <li class="widget-list-item mr-4"><a class="widget-list-link" href="{{ url('privacy') }}">Privacidad</a></li>
                            <li class="widget-list-item mr-4"><a class="widget-list-link" href="{{ url('terms-conditions') }}">Términos y condiciones</a></li>
                            <li class="widget-list-item mr-4"><a class="widget-list-link" href="{{ route('customer.support') }}">Servicio al cliente</a></li>
                        </ul>
                        <span class="font-size-md text-light"><a href="mailto:contigopyme@crcpvalpo.cl">contigopyme@crcpvalpo.cl</a></span>
                    </div>
                    <span class="font-size-md text-light"><a href="tel:+56322354000">(+56) 32 235 4000</a></span>
                </div>
                <div class="col-md-6 text-center text-md-right mb-4">
                    <div class="mb-3">
                        {{-- <a class="social-btn sb-light sb-twitter ml-2 mb-2" href="#"><i class="czi-twitter"></i></a> --}}
                        <a class="social-btn sb-light sb-facebook ml-2 mb-2" href="https://www.facebook.com/Contigopymecl-100648024754209" target="_blank"><i class="czi-facebook"></i></a>
                        <a class="social-btn sb-light sb-instagram ml-2 mb-2" href="https://www.instagram.com/contigopymecl/" target="_blank"><i class="czi-instagram" target="_blank"></i></a>
                        {{-- <a class="social-btn sb-light sb-pinterest ml-2 mb-2" href="#"><i class="czi-pinterest"></i></a>
                        <a class="social-btn sb-light sb-youtube ml-2 mb-2" href="#"><i class="czi-youtube"></i></a> --}}
                    </div>
                    <h6 class="text-white">Medios de Pago</h6>
                    <img class="d-inline-block img-fluid" width="210" src="{{ asset('img/logo-webpay.png') }}" alt="Métodos de pago" />
                </div>
            </div>
            @endif
            <div class="pb-4 font-size-xs text-light opacity-50 text-center text-md-left">Desarrollado por <a class="text-light" href="https://twgroup.cl/" target="_blank" rel="noopener">TWGroup</a></div>
        </div>
    </div>
</footer>
