<!-- Navbar Electronics Store-->
<header class="box-shadow-sm">
    <!-- Topbar-->
    {{-- <div class="topbar topbar-dark bg-dark">
        <div class="container">
            <div>
                <div class="topbar-text dropdown disable-autohide"><a class="topbar-link dropdown-toggle" href="#" data-toggle="dropdown"><img class="mr-2" width="20" src="{{ asset('img/flags/en.png') }}" alt="English" />Eng / $</a>
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
    <div class="topbar-text text-nowrap d-none d-md-inline-block border-left border-light pl-3 ml-3">
        <span class="text-muted mr-1">Available 24/7 at</span><a class="topbar-link" href="tel:00331697720">(00) 33 169 7720</a></div>
    </div>
    <div class="topbar-text dropdown d-md-none ml-auto"><a class="topbar-link dropdown-toggle" href="#" data-toggle="dropdown">Wishlist / Compare / Track</a>
        <ul class="dropdown-menu dropdown-menu-right">
            <li><a class="dropdown-item" href="account-wishlist.html"><i class="czi-heart text-muted mr-2"></i>Wishlist (3)</a></li>
            <li><a class="dropdown-item" href="comparison.html"><i class="czi-compare text-muted mr-2"></i>Compare (3)</a></li>
            <li><a class="dropdown-item" href="order-tracking.html"><i class="czi-location text-muted mr-2"></i>Order tracking</a></li>
        </ul>
    </div>
    <div class="d-none d-md-block ml-3 text-nowrap"><a class="topbar-link d-none d-md-inline-block" href="account-wishlist.html"><i class="czi-heart mt-n1"></i>Wishlist (3)</a><a class="topbar-link ml-3 pl-3 border-left border-light d-none d-md-inline-block" href="comparison.html"><i class="czi-compare mt-n1"></i>Compare (3)</a><a class="topbar-link ml-3 border-left border-light pl-3 d-none d-md-inline-block" href="order-tracking.html"><i class="czi-location mt-n1"></i>Order tracking</a></div>
    </div>
    </div> --}}
    <!-- Remove "navbar-sticky" class to make navigation bar scrollable with the page.-->
    <div class="navbar-sticky bg-dark">
        <div class="navbar navbar-expand-lg navbar-dark">
            <div class="container">
                <a class="navbar-brand d-none d-sm-block m-0 flex-shrink-0 logo-principal" href="{{ url('/home') }}" >
                    <img src="{{ asset('img/prolibro/logo-prolibro.png') }}" alt="Prolibro" />
                </a>
                <a class="navbar-brand d-sm-none mr-2" href="{{ url('/home') }}" style="min-width: 4.625rem;">
                    <img width="74" src="{{ asset('img/prolibro/logo-prolibro.png') }}" alt="Prolibro" />
                </a>
                <!-- Search-->
                @if($header)
                @php
                    $category = isset($data['category']) ? $data['category'] : 0;
                    $product = isset($data['product']) ? $data['product'] : '';
                @endphp
                @livewire('search-navbar', [ 'query' => $product ?? '' , 'selected' => $category ?? 0])
                <!-- Toolbar-->
                <div class="navbar-toolbar d-flex flex-shrink-0 align-items-center">
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse"><span class="navbar-toggler-icon"></span></button>
                    <a class="navbar-tool navbar-stuck-toggler" href="#"><span class="navbar-tool-tooltip">Expand menu</span>
                        <div class="navbar-tool-icon-box"><i class="navbar-tool-icon czi-menu"></i></div>
                    </a>
                    @guest
                    {{-- <a class="navbar-tool ml-1 ml-lg-0 mr-n1 mr-lg-2" href="#signin-modal" data-toggle="modal"> --}}
                    <a class="navbar-tool ml-1 ml-lg-0 mr-n1 mr-lg-2" href="{{ route('customer.sign') }}">
                        <div class="navbar-tool-icon-box"><i class="navbar-tool-icon czi-user"></i></div>
                        <div class="navbar-tool-text ml-n3"><small>Hola, Invitado</small>Mi cuenta</div>
                    </a>
                    @else
                    <!-- Toolbar-->
                    <div class="navbar-toolbar d-flex align-items-center">
                        <div class="navbar-tool dropdown ml-2">
                            <a class="" href="{{ route('customer.profile') }}" >
                                <div class="navbar-tool-icon-box"><i class="navbar-tool-icon czi-user"></i></div>
                            </a>
                            <a class="" href="#">
                                <div class="navbar-tool-text ml-n3"><small>Hola, {{ explode(' ', trim(Auth::user()->name))[0] }}</small>Mi cuenta</div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" style="min-width: 14rem;">
                                <h6 class="dropdown-header">Cuenta</h6>
                                <a class="dropdown-item d-flex align-items-center" href="{{ route('customer.profile') }}"><i class="czi-user opacity-60 mr-2"></i>Perfil</a>
                                <a class="dropdown-item d-flex align-items-center" href="{{ route('customer.address') }}"><i class="czi-location opacity-60 mr-2"></i>Direcciones</a>
                                <a class="dropdown-item d-flex align-items-center" href="{{ route('customer.order') }}"><i class="czi-bag opacity-60 mr-2"></i>Órdenes</a>
                                {{-- <div class="dropdown-divider"></div>
                                <h6 class="dropdown-header">Seller Dashboard</h6>
                                <a class="dropdown-item d-flex align-items-center" href="dashboard-sales.html"><i class="czi-dollar opacity-60 mr-2"></i>Sales<span class="font-size-xs text-muted ml-auto">$1,375.00</span></a>
                                <a class="dropdown-item d-flex align-items-center" href="dashboard-products.html"><i class="czi-package opacity-60 mr-2"></i>Products<span class="font-size-xs text-muted ml-auto">5</span></a>
                                <a class="dropdown-item d-flex align-items-center" href="dashboard-add-new-product.html"><i class="czi-cloud-upload opacity-60 mr-2"></i>Add New Product</a>
                                <a class="dropdown-item d-flex align-items-center" href="dashboard-payouts.html"><i class="czi-currency-exchange opacity-60 mr-2"></i>Payouts</a> --}}
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item d-flex align-items-center" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="czi-sign-out opacity-60 mr-2"></i> Cerrar sesión
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    </div>
                    @endguest
                    @livewire('cart.cart')<!--//, ['user' => $user], key($user->id))-->
                </div>
            </div>
            @endif
        </div>
        @if($header)
        <div class="navbar navbar-expand-lg navbar-dark navbar-stuck-menu mt-n2 pt-0 pb-2">
            <div class="container">
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <!-- Search-->
                    @livewire('search-navbar-movile')
                    <!--

                        <div class="input-group-overlay d-lg-none my-3">
                            <div class="input-group-prepend-overlay"><span class="input-group-text"><i class="czi-search"></i></span></div>
                            <input class="form-control prepended-form-control" type="text" placeholder="Search for products">
                        </div>
                    -->
                    <!-- Categories Menu-->
                    @livewire('categories-menu')
                    <!-- Primary menu-->
                    <ul class="navbar-nav">
                        <li class="nav-item active"><a class="nav-link" href="{{ url('/home') }}">Inicio</a></li>
                        <li class="nav-item active"><a class="nav-link" href="{{ url('/about-us') }}">Quienes Somos</a></li>
                        @if( ! empty(Setting::get('featured_page_title')))
                            <li class="nav-item active"><a class="nav-link" href="{{ url('/featured-products') }}">{{ Setting::get('featured_page_title') }}</a></li>
                        @endif
                        {{-- <li class="nav-item active"><a class="nav-link" href="https://actividades.prolibro.cl/wp-content/uploads/2021/12/Programa-Cultural.pdf" target="_blank" >Programa Cultural</a></li> --}}
                        {{-- <li class="nav-item dropdown active"><a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">Inicio</a>
                            <ul class="dropdown-menu">
                                <li class="dropdown position-static mb-0"><a class="dropdown-item py-2 border-bottom" href="home-fashion-store-v1.html"><span class="d-block text-heading">Fashion
                                            Store v.1</span><small class="d-block text-muted">Classic shop
                                            layout</small></a>
                                    <div class="dropdown-menu h-100 animation-0 mt-0 p-3"><a class="d-block" href="home-fashion-store-v1.html" style="width: 250px;"><img src="{{ asset('img/home/preview/th01.jpg') }}" alt="Fashion Store v.1" /></a>
                </div>
                </li>
                <li class="dropdown position-static mb-0"><a class="dropdown-item py-2 border-bottom" href="home-electronics-store.html"><span class="d-block text-heading">Electronics Store</span><small class="d-block text-muted">Slider + Promo banners</small></a>
                    <div class="dropdown-menu h-100 animation-0 mt-0 p-3"><a class="d-block" href="home-electronics-store.html" style="width: 250px;"><img src="{{ asset('img/home/preview/th03.jpg') }}" alt="Electronics Store" /></a></div>
                </li>
                <li class="dropdown position-static mb-0"><a class="dropdown-item py-2 border-bottom" href="home-marketplace.html"><span class="d-block text-heading">Marketplace</span><small class="d-block text-muted">Multi-vendor, digital goods</small></a>
                    <div class="dropdown-menu h-100 animation-0 mt-0 p-3"><a class="d-block" href="home-marketplace.html" style="width: 250px;"><img src="{{ asset('img/home/preview/th04.jpg') }}" alt="Marketplace" /></a></div>
                </li>
                <li class="dropdown position-static mb-0"><a class="dropdown-item py-2 border-bottom" href="home-grocery-store.html"><span class="d-block text-heading">Grocery
                            Store</span><small class="d-block text-muted">Full width + Side
                            menu</small></a>
                    <div class="dropdown-menu h-100 animation-0 mt-0 p-3"><a class="d-block" href="home-grocery-store.html" style="width: 250px;"><img src="{{ asset('img/home/preview/th06.jpg') }}" alt="Grocery Store" /></a></div>
                </li>
                <li class="dropdown position-static mb-0"><a class="dropdown-item py-2 border-bottom" href="home-food-delivery.html"><span class="d-block text-heading">Food
                            Delivery Service</span><small class="d-block text-muted">Food &amp;
                            Beverages delivery</small></a>
                    <div class="dropdown-menu h-100 animation-0 mt-0 p-3"><a class="d-block" href="home-food-delivery.html" style="width: 250px;"><img src="{{ asset('img/home/preview/th07.jpg') }}" alt="Food Delivery Service" /></a></div>
                </li>
                <li class="dropdown position-static mb-0"><a class="dropdown-item py-2 border-bottom" href="home-fashion-store-v2.html"><span class="d-block text-heading">Fashion
                            Store v.2</span><small class="d-block text-muted">Slider + Featured
                            categories</small></a>
                    <div class="dropdown-menu h-100 animation-0 mt-0 p-3"><a class="d-block" href="home-fashion-store-v2.html" style="width: 250px;"><img src="{{ asset('img/home/preview/th02.jpg') }}" alt="Fashion Store v.2" /></a></div>
                </li>
                <li class="dropdown position-static mb-0"><a class="dropdown-item py-2" href="home-single-store.html"><span class="d-block text-heading">Single
                            Product Store</span><small class="d-block text-muted">Single product /
                            mono brand</small></a>
                    <div class="dropdown-menu h-100 animation-0 mt-0 p-3"><a class="d-block" href="home-single-store.html" style="width: 250px;"><img src="{{ asset('img/home/preview/th05.jpg') }}" alt="Single Product / Brand Store" /></a></div>
                </li>
                </ul>
                </li>
                <li class="nav-item dropdown"><a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">Shop</a>
                    <div class="dropdown-menu p-0">
                        <div class="d-flex flex-wrap flex-md-nowrap px-2">
                            <div class="mega-dropdown-column py-4 px-3">
                                <div class="widget widget-links mb-3">
                                    <h6 class="font-size-base mb-3">Shop layouts</h6>
                                    <ul class="widget-list">
                                        <li class="widget-list-item pb-1"><a class="widget-list-link" href="shop-grid-ls.html">Shop Grid - Left Sidebar</a></li>
                                        <li class="widget-list-item pb-1"><a class="widget-list-link" href="shop-grid-rs.html">Shop Grid - Right Sidebar</a></li>
                                        <li class="widget-list-item pb-1"><a class="widget-list-link" href="shop-grid-ft.html">Shop Grid - Filters on Top</a></li>
                                        <li class="widget-list-item pb-1"><a class="widget-list-link" href="shop-list-ls.html">Shop List - Left Sidebar</a></li>
                                        <li class="widget-list-item pb-1"><a class="widget-list-link" href="shop-list-rs.html">Shop List - Right Sidebar</a></li>
                                        <li class="widget-list-item pb-1"><a class="widget-list-link" href="shop-list-ft.html">Shop List - Filters on Top</a></li>
                                    </ul>
                                </div>
                                <div class="widget widget-links">
                                    <h6 class="font-size-base mb-3">Marketplace</h6>
                                    <ul class="widget-list">
                                        <li class="widget-list-item pb-1"><a class="widget-list-link" href="marketplace-category.html">Category Page</a></li>
                                        <li class="widget-list-item pb-1"><a class="widget-list-link" href="marketplace-single.html">Single Item Page</a></li>
                                        <li class="widget-list-item pb-1"><a class="widget-list-link" href="marketplace-vendor.html">Vendor Page</a></li>
                                        <li class="widget-list-item pb-1"><a class="widget-list-link" href="marketplace-cart.html">Cart</a></li>
                                        <li class="widget-list-item pb-1"><a class="widget-list-link" href="marketplace-checkout.html">Checkout</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="mega-dropdown-column py-4 px-3">
                                <div class="widget widget-links">
                                    <h6 class="font-size-base mb-3">Shop pages</h6>
                                    <ul class="widget-list">
                                        <li class="widget-list-item pb-1"><a class="widget-list-link" href="shop-categories.html">Shop Categories</a></li>
                                        <li class="widget-list-item pb-1"><a class="widget-list-link" href="shop-single-v1.html">Product Page v.1</a></li>
                                        <li class="widget-list-item pb-1"><a class="widget-list-link" href="shop-single-v2.html">Product Page v.2</a></li>
                                        <li class="widget-list-item pb-1"><a class="widget-list-link" href="shop-cart.html">Cart</a></li>
                                        <li class="widget-list-item pb-1"><a class="widget-list-link" href="checkout-details.html">Checkout - Details</a></li>
                                        <li class="widget-list-item pb-1"><a class="widget-list-link" href="checkout-shipping.html">Checkout - Shipping</a></li>
                                        <li class="widget-list-item pb-1"><a class="widget-list-link" href="checkout-payment.html">Checkout - Payment</a></li>
                                        <li class="widget-list-item pb-1"><a class="widget-list-link" href="checkout-review.html">Checkout - Review</a></li>
                                        <li class="widget-list-item pb-1"><a class="widget-list-link" href="checkout-complete.html">Checkout - Complete</a></li>
                                        <li class="widget-list-item pb-1"><a class="widget-list-link" href="order-tracking.html">Order Tracking</a></li>
                                        <li class="widget-list-item pb-1"><a class="widget-list-link" href="comparison.html">Product Comparison</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="mega-dropdown-column py-4 pr-3">
                                <div class="widget widget-links mb-3">
                                    <h6 class="font-size-base mb-3">Grocery store</h6>
                                    <ul class="widget-list">
                                        <li class="widget-list-item pb-1"><a class="widget-list-link" href="grocery-catalog.html">Product Catalog</a></li>
                                        <li class="widget-list-item pb-1"><a class="widget-list-link" href="grocery-single.html">Single Product Page</a></li>
                                        <li class="widget-list-item pb-1"><a class="widget-list-link" href="grocery-checkout.html">Checkout</a></li>
                                    </ul>
                                </div>
                                <div class="widget widget-links">
                                    <h6 class="font-size-base mb-3">Food Delivery</h6>
                                    <ul class="widget-list">
                                        <li class="widget-list-item pb-1"><a class="widget-list-link" href="food-delivery-category.html">Category Page</a></li>
                                        <li class="widget-list-item pb-1"><a class="widget-list-link" href="food-delivery-single.html">Single Item
                                                (Restaurant)</a></li>
                                        <li class="widget-list-item pb-1"><a class="widget-list-link" href="food-delivery-cart.html">Cart (Your Order)</a></li>
                                        <li class="widget-list-item pb-1"><a class="widget-list-link" href="food-delivery-checkout.html">Checkout (Address &amp;
                                                Payment)</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="nav-item dropdown"><a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">Account</a>
                    <ul class="dropdown-menu">
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" href="#" data-toggle="dropdown">Shop User Account</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="account-orders.html">Orders History</a>
                                </li>
                                <li><a class="dropdown-item" href="account-profile.html">Profile
                                        Settings</a></li>
                                <li><a class="dropdown-item" href="account-address.html">Account
                                        Addresses</a></li>
                                <li><a class="dropdown-item" href="account-payment.html">Payment Methods</a>
                                </li>
                                <li><a class="dropdown-item" href="account-wishlist.html">Wishlist</a></li>
                                <li><a class="dropdown-item" href="account-tickets.html">My Tickets</a></li>
                                <li><a class="dropdown-item" href="account-single-ticket.html">Single
                                        Ticket</a></li>
                            </ul>
                        </li>
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" href="#" data-toggle="dropdown">Vendor Dashboard</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="dashboard-settings.html">Settings</a>
                                </li>
                                <li><a class="dropdown-item" href="dashboard-purchases.html">Purchases</a>
                                </li>
                                <li><a class="dropdown-item" href="dashboard-favorites.html">Favorites</a>
                                </li>
                                <li><a class="dropdown-item" href="dashboard-sales.html">Sales</a></li>
                                <li><a class="dropdown-item" href="dashboard-products.html">Products</a>
                                </li>
                                <li><a class="dropdown-item" href="dashboard-add-new-product.html">Add New
                                        Product</a></li>
                                <li><a class="dropdown-item" href="dashboard-payouts.html">Payouts</a></li>
                            </ul>
                        </li>
                        <li><a class="dropdown-item" href="account-signin.html">Sign In / Sign Up</a></li>
                        <li><a class="dropdown-item" href="account-password-recovery.html">Password
                                Recovery</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown"><a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">Pages</a>
                    <ul class="dropdown-menu">
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" href="#" data-toggle="dropdown">Navbar Variants</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="navbar-1-level-light.html">1 Level
                                        Light</a></li>
                                <li><a class="dropdown-item" href="navbar-1-level-dark.html">1 Level
                                        Dark</a></li>
                                <li><a class="dropdown-item" href="navbar-2-level-light.html">2 Level
                                        Light</a></li>
                                <li><a class="dropdown-item" href="navbar-2-level-dark.html">2 Level
                                        Dark</a></li>
                                <li><a class="dropdown-item" href="navbar-3-level-light.html">3 Level
                                        Light</a></li>
                                <li><a class="dropdown-item" href="navbar-3-level-dark.html">3 Level
                                        Dark</a></li>
                                <li><a class="dropdown-item" href="home-electronics-store.html">Electronics
                                        Store</a></li>
                                <li><a class="dropdown-item" href="home-marketplace.html">Marketplace</a>
                                </li>
                                <li><a class="dropdown-item" href="home-grocery-store.html">Side Menu
                                        (Grocery)</a></li>
                                <li><a class="dropdown-item" href="home-single-store.html">Transparent</a>
                                </li>
                            </ul>
                        </li>
                        <li class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="about.html">About Us</a></li>
                        <li><a class="dropdown-item" href="contacts.html">Contacts</a></li>
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" href="#" data-toggle="dropdown">Help Center</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="help-topics.html">Help Topics</a></li>
                                <li><a class="dropdown-item" href="help-single-topic.html">Single Topic</a>
                                </li>
                                <li><a class="dropdown-item" href="help-submit-request.html">Submit a
                                        Request</a></li>
                            </ul>
                        </li>
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" href="#" data-toggle="dropdown">404 Not Found</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="404-simple.html">404 - Simple Text</a>
                                </li>
                                <li><a class="dropdown-item" href="404-illustration.html">404 -
                                        Illustration</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown"><a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">Blog</a>
                    <ul class="dropdown-menu">
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" href="#" data-toggle="dropdown">Blog List Layouts</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="blog-list-sidebar.html">List with
                                        Sidebar</a></li>
                                <li><a class="dropdown-item" href="blog-list.html">List no Sidebar</a></li>
                            </ul>
                        </li>
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" href="#" data-toggle="dropdown">Blog Grid Layouts</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="blog-grid-sidebar.html">Grid with
                                        Sidebar</a></li>
                                <li><a class="dropdown-item" href="blog-grid.html">Grid no Sidebar</a></li>
                            </ul>
                        </li>
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" href="#" data-toggle="dropdown">Single Post Layouts</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="blog-single-sidebar.html">Article with
                                        Sidebar</a></li>
                                <li><a class="dropdown-item" href="blog-single.html">Article no Sidebar</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown"><a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">Docs / Components</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="docs/dev-setup.html">
                                <div class="d-flex">
                                    <div class="lead text-muted pt-1"><i class="czi-book"></i></div>
                                    <div class="ml-2"><span class="d-block text-heading">Documentation</span><small class="d-block text-muted">Kick-start customization</small>
                                    </div>
                                </div>
                            </a></li>
                        <li class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="components/typography.html">
                                <div class="d-flex">
                                    <div class="lead text-muted pt-1"><i class="czi-server"></i></div>
                                    <div class="ml-2"><span class="d-block text-heading">Components<span class="badge badge-info ml-2">40+</span></span><small class="d-block text-muted">Faster page building</small></div>
                                </div>
                            </a></li>
                        <li class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="docs/changelog.html">
                                <div class="d-flex">
                                    <div class="lead text-muted pt-1"><i class="czi-edit"></i></div>
                                    <div class="ml-2"><span class="d-block text-heading">Changelog<span class="badge badge-success ml-2">v1.4</span></span><small class="d-block text-muted">Regular updates</small></div>
                                </div>
                            </a></li>
                        <li class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="mailto:contact@createx.studio">
                                <div class="d-flex">
                                    <div class="lead text-muted pt-1"><i class="czi-help"></i></div>
                                    <div class="ml-2"><span class="d-block text-heading">Support</span><small class="d-block text-muted">contact@createx.studio</small></div>
                                </div>
                            </a></li>
                    </ul>
                </li> --}}
                </ul>
            </div>
        </div>
    </div>
    @endif
    </div>
</header>
