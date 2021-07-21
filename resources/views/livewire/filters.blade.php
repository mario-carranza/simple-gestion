<div class="cz-sidebar rounded-lg box-shadow-lg" id="shop-sidebar">
        <div class="cz-sidebar-header box-shadow-sm">
            <button class="close ml-auto" type="button" data-dismiss="sidebar" aria-label="Close"><span class="d-inline-block font-size-xs font-weight-normal align-middle">Cerrar</span><span class="d-inline-block align-middle ml-2" aria-hidden="true">&times;</span></button>
        </div>
        <div class="cz-sidebar-body">
            <!-- Categories-->
            {{--
                <div class="widget widget-categories mb-4 pb-4 border-bottom">
                    <h3 class="widget-title">Categorías</h3>
                    <div class="accordion mt-n1" id="shop-categories">
                        @foreach ($categories as $category)
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="accordion-heading"><a class="collapsed" href="#{{$category->code}}" role="button" data-toggle="collapse" aria-expanded="false" aria-controls="shoes">{{$category->name}}<span class="accordion-indicator"></span></a></h3>
                                </div>
                                <div class="collapse" id="{{$category->code}}" data-parent="#shop-categories">
                                    <div class="card-body">
                                        <div class="widget widget-links cz-filter">
                                            <div class="input-group-overlay input-group-sm mb-2">
                                                <input class="cz-filter-search form-control form-control-sm appended-form-control" type="text" placeholder="Buscar">
                                                <div class="input-group-append-overlay"><span class="input-group-text"><i class="czi-search"></i></span></div>
                                            </div>
                                            <ul class="widget-list cz-filter-list pt-1" style="height: 12rem;" data-simplebar data-simplebar-auto-hide="false">
                                                <li class="widget-list-item cz-filter-item"><a class="widget-list-link d-flex justify-content-between align-items-center" href="{{ url('search-products/'.$category->id) }}"><span class="cz-filter-item-text">Ver todos</span><span class="font-size-xs text-muted ml-3">{{$category->products->count()}}</span></a></li>
                                                @if($category->product_class)
                                                    @foreach($category->product_class as $product_class)
                                                        <li class="widget-list-item cz-filter-item"><a class="widget-list-link d-flex justify-content-between align-items-center" href="#"><span class="cz-filter-item-text">{{$product_class->name}}</span><span class="font-size-xs text-muted ml-3"></span></a></li>
                                                    @endforeach
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <!--
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="accordion-heading"><a class="collapsed" href="#shoes" role="button" data-toggle="collapse" aria-expanded="false" aria-controls="shoes">Shoes<span class="accordion-indicator"></span></a></h3>
                                </div>
                                <div class="collapse" id="shoes" data-parent="#shop-categories">
                                    <div class="card-body">
                                        <div class="widget widget-links cz-filter">
                                            <div class="input-group-overlay input-group-sm mb-2">
                                                <input class="cz-filter-search form-control form-control-sm appended-form-control" type="text" placeholder="Search">
                                                <div class="input-group-append-overlay"><span class="input-group-text"><i class="czi-search"></i></span></div>
                                            </div>
                                            <ul class="widget-list cz-filter-list pt-1" style="height: 12rem;" data-simplebar data-simplebar-auto-hide="false">
                                                <li class="widget-list-item cz-filter-item"><a class="widget-list-link d-flex justify-content-between align-items-center" href="#"><span class="cz-filter-item-text">View all</span><span class="font-size-xs text-muted ml-3">1,953</span></a></li>
                                                <li class="widget-list-item cz-filter-item"><a class="widget-list-link d-flex justify-content-between align-items-center" href="#"><span class="cz-filter-item-text">Pumps &amp; High Heels</span><span class="font-size-xs text-muted ml-3">247</span></a></li>
                                                <li class="widget-list-item cz-filter-item"><a class="widget-list-link d-flex justify-content-between align-items-center" href="#"><span class="cz-filter-item-text">Ballerinas &amp; Flats</span><span class="font-size-xs text-muted ml-3">156</span></a></li>
                                                <li class="widget-list-item cz-filter-item"><a class="widget-list-link d-flex justify-content-between align-items-center" href="#"><span class="cz-filter-item-text">Sandals</span><span class="font-size-xs text-muted ml-3">310</span></a></li>
                                                <li class="widget-list-item cz-filter-item"><a class="widget-list-link d-flex justify-content-between align-items-center" href="#"><span class="cz-filter-item-text">Sneakers</span><span class="font-size-xs text-muted ml-3">402</span></a></li>
                                                <li class="widget-list-item cz-filter-item"><a class="widget-list-link d-flex justify-content-between align-items-center" href="#"><span class="cz-filter-item-text">Boots</span><span class="font-size-xs text-muted ml-3">393</span></a></li>
                                                <li class="widget-list-item cz-filter-item"><a class="widget-list-link d-flex justify-content-between align-items-center" href="#"><span class="cz-filter-item-text">Ankle Boots</span><span class="font-size-xs text-muted ml-3">50</span></a></li>
                                                <li class="widget-list-item cz-filter-item"><a class="widget-list-link d-flex justify-content-between align-items-center" href="#"><span class="cz-filter-item-text">Loafers</span><span class="font-size-xs text-muted ml-3">93</span></a></li>
                                                <li class="widget-list-item cz-filter-item"><a class="widget-list-link d-flex justify-content-between align-items-center" href="#"><span class="cz-filter-item-text">Slip-on</span><span class="font-size-xs text-muted ml-3">122</span></a></li>
                                                <li class="widget-list-item cz-filter-item"><a class="widget-list-link d-flex justify-content-between align-items-center" href="#"><span class="cz-filter-item-text">Flip Flops</span><span class="font-size-xs text-muted ml-3">116</span></a></li>
                                                <li class="widget-list-item cz-filter-item"><a class="widget-list-link d-flex justify-content-between align-items-center" href="#"><span class="cz-filter-item-text">Clogs &amp; Mules</span><span class="font-size-xs text-muted ml-3">24</span></a></li>
                                                <li class="widget-list-item cz-filter-item"><a class="widget-list-link d-flex justify-content-between align-items-center" href="#"><span class="cz-filter-item-text">Athletic Shoes</span><span class="font-size-xs text-muted ml-3">31</span></a></li>
                                                <li class="widget-list-item cz-filter-item"><a class="widget-list-link d-flex justify-content-between align-items-center" href="#"><span class="cz-filter-item-text">Oxfords</span><span class="font-size-xs text-muted ml-3">9</span></a></li>
                                                <li class="widget-list-item cz-filter-item"><a class="widget-list-link d-flex justify-content-between align-items-center" href="#"><span class="cz-filter-item-text">Smart Shoes</span><span class="font-size-xs text-muted ml-3">18</span></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        -->
                    </div>
                </div>
            --}}

            <!-- Price range-->
            <div class="widget mb-4 pb-4 border-bottom">
                <h3 class="widget-title">Precio</h3>
                {{-- <div class="cz-range-slider" 
                    data-start-min="100000" 
                    data-start-max="800000" 
                    data-min="100" 
                    data-max="1000000" 
                    data-step="1000"
                > --}}
                    {{-- <div class="cz-range-slider-ui"></div> --}}
                    <div>
                    <div class="d-flex pb-1">
                        <div class="w-50 pr-2 mr-2">
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                                <input class="form-control cz-range-slider-value-min" wire:model="filterOptions.price.min" type="text" placeholder="Mínimo">
                            </div>
                        </div>
                        <div class="w-50 pl-2">
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                                <input class="form-control cz-range-slider-value-max" wire:model="filterOptions.price.max" type="text" placeholder="Máximo">
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-block mt-3 btn-primary btn-shadow" wire:click="filter">Buscar</button>
                </div>
            </div>

            <!-- Filter by Brand-->
            @if ($brands->count())
            <div class="widget cz-filter mb-4 pb-4 border-bottom">
                <h3 class="widget-title">Marca</h3>
                <ul class="widget-list cz-filter-list list-unstyled pt-1" style="max-height: 12rem;" data-simplebar data-simplebar-auto-hide="false">
                    @foreach($brands as $key => $brand)                    
                        <li class="cz-filter-item d-flex justify-content-between align-items-center">
                            <div class="custom-control custom-checkbox">
                                <input
                                    wire:change="filter"
                                    class="custom-control-input" 
                                    wire:model="filterOptions.brand.{{ $key }}" 
                                    type="checkbox" 
                                    value="{{$brand->id}}" 
                                    id="{{$brand->id}}">
                                <label class="custom-control-label cz-filter-item-text"  for="{{$brand->id}}">{{$brand->name}}</label>
                            </div>{{-- <span class="font-size-xs text-muted">{{$brand->products->count()}}</span> --}}
                        </li>
                    @endforeach
                </ul>
            </div>
            @endif
            
            <!-- Filter by attributes -->
            <form action="">
            @foreach($attributes as $attribute)
            @php
                $product_attributes = $attribute->product_attributes->unique("json_value");
                $product_attributes = $product_attributes->where('json_value','<>', '* No aplica')->sortBy('json_value');
            @endphp

            <div class="widget cz-filter mb-4 pb-4 border-bottom">
                <h3 class="widget-title">{{$attribute->getNameAttribute()}}</h3>
                <ul class="widget-list cz-filter-list list-unstyled pt-1" style="max-height: 12rem;" data-simplebar data-simplebar-auto-hide="false">
                    
                    @foreach($product_attributes as $key => $value)           
                    
                        <li class="cz-filter-item d-flex justify-content-between align-items-center">
                            <div class="custom-control custom-checkbox">
                                <input 
                                    {{-- onChange="this.form.submit()" --}} 
                                    wire:change="filter"
                                    class="custom-control-input" 
                                    name="ca-{{ $value->product_class_attribute_id }}" 
                                    type="checkbox" 
                                    id="${{ $value->id }}"
                                    value="{{$value->json_value}}"
                                    wire:model="filterOptions.attributes.{{ $value->product_class_attribute_id }}.{{ $key }}"
                                >
                                <label class="custom-control-label cz-filter-item-text" for="${{ $value->id }}">{{$value->json_value}}</label>
                            {{-- </div><span class="font-size-xs text-muted">0</span> --}}
                        </li>
                    @endforeach
                
                </ul>
            </div>
            @endforeach
            </form>
            <!-- Filter by Size-->
            <!--
                <div class="widget cz-filter mb-4 pb-4 border-bottom">
                    <h3 class="widget-title">Talla</h3>
                    <ul class="widget-list cz-filter-list list-unstyled pt-1" style="max-height: 12rem;" data-simplebar data-simplebar-auto-hide="false">
                        <li class="cz-filter-item d-flex justify-content-between align-items-center mb-1">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" id="size-xs">
                                <label class="custom-control-label cz-filter-item-text" for="size-xs">XS</label>
                            </div><span class="font-size-xs text-muted">34</span>
                        </li>
                        <li class="cz-filter-item d-flex justify-content-between align-items-center mb-1">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" id="size-s">
                                <label class="custom-control-label cz-filter-item-text" for="size-s">S</label>
                            </div><span class="font-size-xs text-muted">57</span>
                        </li>
                        <li class="cz-filter-item d-flex justify-content-between align-items-center mb-1">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" id="size-m">
                                <label class="custom-control-label cz-filter-item-text" for="size-m">M</label>
                            </div><span class="font-size-xs text-muted">198</span>
                        </li>
                        <li class="cz-filter-item d-flex justify-content-between align-items-center mb-1">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" id="size-l">
                                <label class="custom-control-label cz-filter-item-text" for="size-l">L</label>
                            </div><span class="font-size-xs text-muted">72</span>
                        </li>
                        <li class="cz-filter-item d-flex justify-content-between align-items-center mb-1">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" id="size-xl">
                                <label class="custom-control-label cz-filter-item-text" for="size-xl">XL</label>
                            </div><span class="font-size-xs text-muted">46</span>
                        </li>
                        <li class="cz-filter-item d-flex justify-content-between align-items-center mb-1">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" id="size-39">
                                <label class="custom-control-label cz-filter-item-text" for="size-39">39</label>
                            </div><span class="font-size-xs text-muted">112</span>
                        </li>
                        <li class="cz-filter-item d-flex justify-content-between align-items-center mb-1">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" id="size-40">
                                <label class="custom-control-label cz-filter-item-text" for="size-40">40</label>
                            </div><span class="font-size-xs text-muted">85</span>
                        </li>
                        <li class="cz-filter-item d-flex justify-content-between align-items-center mb-1">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" id="size-41">
                                <label class="custom-control-label cz-filter-item-text" for="size-40">41</label>
                            </div><span class="font-size-xs text-muted">210</span>
                        </li>
                        <li class="cz-filter-item d-flex justify-content-between align-items-center mb-1">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" id="size-42">
                                <label class="custom-control-label cz-filter-item-text" for="size-42">42</label>
                            </div><span class="font-size-xs text-muted">57</span>
                        </li>
                        <li class="cz-filter-item d-flex justify-content-between align-items-center mb-1">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" id="size-43">
                                <label class="custom-control-label cz-filter-item-text" for="size-43">43</label>
                            </div><span class="font-size-xs text-muted">30</span>
                        </li>
                        <li class="cz-filter-item d-flex justify-content-between align-items-center mb-1">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" id="size-44">
                                <label class="custom-control-label cz-filter-item-text" for="size-44">44</label>
                            </div><span class="font-size-xs text-muted">61</span>
                        </li>
                        <li class="cz-filter-item d-flex justify-content-between align-items-center mb-1">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" id="size-45">
                                <label class="custom-control-label cz-filter-item-text" for="size-45">45</label>
                            </div><span class="font-size-xs text-muted">23</span>
                        </li>
                        <li class="cz-filter-item d-flex justify-content-between align-items-center mb-1">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" id="size-46">
                                <label class="custom-control-label cz-filter-item-text" for="size-46">46</label>
                            </div><span class="font-size-xs text-muted">19</span>
                        </li>
                        <li class="cz-filter-item d-flex justify-content-between align-items-center mb-1">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" id="size-47">
                                <label class="custom-control-label cz-filter-item-text" for="size-47">47</label>
                            </div><span class="font-size-xs text-muted">15</span>
                        </li>
                        <li class="cz-filter-item d-flex justify-content-between align-items-center mb-1">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" id="size-48">
                                <label class="custom-control-label cz-filter-item-text" for="size-48">48</label>
                            </div><span class="font-size-xs text-muted">12</span>
                        </li>
                        <li class="cz-filter-item d-flex justify-content-between align-items-center mb-1">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" id="size-49">
                                <label class="custom-control-label cz-filter-item-text" for="size-49">49</label>
                            </div><span class="font-size-xs text-muted">8</span>
                        </li>
                        <li class="cz-filter-item d-flex justify-content-between align-items-center">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" id="size-50">
                                <label class="custom-control-label cz-filter-item-text" for="size-50">50</label>
                            </div><span class="font-size-xs text-muted">6</span>
                        </li>
                    </ul>
                </div>
            -->
            <!-- Filter by Color-->
            <!--
                <div class="widget">
                    <h3 class="widget-title">Color</h3>
                    <div class="d-flex flex-wrap">
                        <div class="custom-control custom-option text-center mb-2 mx-1" style="width: 4rem;">
                            <input class="custom-control-input" type="checkbox" id="color-blue-gray">
                            <label class="custom-option-label rounded-circle" for="color-blue-gray"><span class="custom-option-color rounded-circle" style="background-color: #b3c8db;"></span></label>
                            <label class="d-block font-size-xs text-muted mt-n1" for="color-blue-gray">Blue-gray</label>
                        </div>
                        <div class="custom-control custom-option text-center mb-2 mx-1" style="width: 4rem;">
                            <input class="custom-control-input" type="checkbox" id="color-burgundy">
                            <label class="custom-option-label rounded-circle" for="color-burgundy"><span class="custom-option-color rounded-circle" style="background-color: #ca7295;"></span></label>
                            <label class="d-block font-size-xs text-muted mt-n1" for="color-burgundy">Burgundy</label>
                        </div>
                        <div class="custom-control custom-option text-center mb-2 mx-1" style="width: 4rem;">
                            <input class="custom-control-input" type="checkbox" id="color-teal">
                            <label class="custom-option-label rounded-circle" for="color-teal"><span class="custom-option-color rounded-circle" style="background-color: #91c2c3;"></span></label>
                            <label class="d-block font-size-xs text-muted mt-n1" for="color-teal">Teal</label>
                        </div>
                        <div class="custom-control custom-option text-center mb-2 mx-1" style="width: 4rem;">
                            <input class="custom-control-input" type="checkbox" id="color-brown">
                            <label class="custom-option-label rounded-circle" for="color-brown"><span class="custom-option-color rounded-circle" style="background-color: #9a8480;"></span></label>
                            <label class="d-block font-size-xs text-muted mt-n1" for="color-brown">Brown</label>
                        </div>
                        <div class="custom-control custom-option text-center mb-2 mx-1" style="width: 4rem;">
                            <input class="custom-control-input" type="checkbox" id="color-coral-red">
                            <label class="custom-option-label rounded-circle" for="color-coral-red"><span class="custom-option-color rounded-circle" style="background-color: #ff7072;"></span></label>
                            <label class="d-block font-size-xs text-muted mt-n1" for="color-coral-red">Coral red</label>
                        </div>
                        <div class="custom-control custom-option text-center mb-2 mx-1" style="width: 4rem;">
                            <input class="custom-control-input" type="checkbox" id="color-navy">
                            <label class="custom-option-label rounded-circle" for="color-navy"><span class="custom-option-color rounded-circle" style="background-color: #696dc8;"></span></label>
                            <label class="d-block font-size-xs text-muted mt-n1" for="color-navy">Navy</label>
                        </div>
                        <div class="custom-control custom-option text-center mb-2 mx-1" style="width: 4rem;">
                            <input class="custom-control-input" type="checkbox" id="color-charcoal">
                            <label class="custom-option-label rounded-circle" for="color-charcoal"><span class="custom-option-color rounded-circle" style="background-color: #4e4d4d;"></span></label>
                            <label class="d-block font-size-xs text-muted mt-n1" for="color-charcoal">Charcoal</label>
                        </div>
                        <div class="custom-control custom-option text-center mb-2 mx-1" style="width: 4rem;">
                            <input class="custom-control-input" type="checkbox" id="color-sky-blue">
                            <label class="custom-option-label rounded-circle" for="color-sky-blue"><span class="custom-option-color rounded-circle" style="background-color: #8bcdf5;"></span></label>
                            <label class="d-block font-size-xs text-muted mt-n1" for="color-sky-blue">Sky blue</label>
                        </div>
                    </div>
                </div>
            -->
        </div>
    
    <button type="button" wire:loading wire:target="filter" class="btn btn-primary loader">
        <span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>
        Cargando...
    </button>
</div>
