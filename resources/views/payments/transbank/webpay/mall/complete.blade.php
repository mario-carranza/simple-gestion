@php
use App\Models\Commune;
use App\Models\Product;
$transactionData = null;
try {
$transactionData = json_decode($order->order_payments->first()->json_in)->data;

} catch (\Throwable $th) {
//throw $th;
}

$addressData = $order->json_value;



$addressShipping = null;
if(isset($addressData['addressShipping'])){
$addressShipping = $addressData['addressShipping'];

}


$addressInvoice = null;
if(isset($addressData['addressInvoice'])){
$addressInvoice = $addressData['addressInvoice'];
}

$communeShipping = null;
$communeInvoice = null;

if($addressShipping){
$communeShipping = Commune::where('id', $addressShipping->address_commune_id)->first();
}

if($addressInvoice){
$communeInvoice = Commune::where('id', $addressInvoice->address_commune_id)->first();
}



@endphp
@extends('layouts.base')
@section('content')
    <!-- Page title-->
    <!-- Page Content-->
    <div class="container pb-5 mb-sm-4">

        <div class="pt-5">
            <div class="woocommerce">
                <div class="container py-5 my-3">
                    <div class="woocommerce-order">




                        <h1 class="woocommerce-thankyou-order-received h4 pb-3 text-center">Gracias. Su pedido ha sido
                            recibido.</h1>
                        <div class="woocommerce-order-overview woocommerce-thankyou-order-details order_details row mx-n2">

                            <div class="woocommerce-order-overview__order order col-md-4 col-sm-6 mb-3 px-2">
                                <div class="bg-secondary rounded-lg p-3 text-center font-size-md">
                                    Orden #: <span class="font-weight-medium">{{ $order->id }}</span>
                                </div>
                            </div>

                            <div class="woocommerce-order-overview__date date col-md-4 col-sm-6 mb-3 px-2">
                                <div class="bg-secondary rounded-lg p-3 text-center font-size-md">
                                    Fecha : <span
                                        class="font-weight-medium">{{ $order->created_at->format('d/m/Y H:i:s') }}</span>
                                </div>
                            </div>

                            <div class="woocommerce-order-overview__total total col-md-4 col-sm-6 mb-3 px-2">
                                <div class="bg-secondary rounded-lg p-3 text-center font-size-md">
                                    Total: <span class="font-weight-medium"><span
                                            class="woocommerce-Price-amount amount"><span
                                                class="woocommerce-Price-currencySymbol"></span>{{ currencyFormat($order->total ? $order->total : 0, 'CLP', true) }}</span></span>
                                </div>
                            </div>
                            <div class="woocommerce-order-overview__email email col-sm-6 mb-3 px-2">
                                <div class="bg-secondary rounded-lg p-3 text-center font-size-md">
                                    Nombre y Apellido: <span
                                        class="font-weight-medium">{{ $order->first_name . ' ' . $order->last_name }}</span>
                                </div>
                            </div>
                            <div class="woocommerce-order-overview__email email col-sm-6 mb-3 px-2">
                                <div class="bg-secondary rounded-lg p-3 text-center font-size-md">
                                    Email: <span class="font-weight-medium">{{ $order->email }}</span>
                                </div>
                            </div>

                            <div class="woocommerce-order-overview__payment-method method col-sm-6 mb-3 px-2">
                                <div class="bg-secondary rounded-lg p-3 text-center font-size-md">
                                    Método de pago : <span
                                        class="font-weight-medium">{{ $order->order_payments->first()->method_title }}</span>
                                </div>
                            </div>
                            @if ($transactionData)
                                <div class="woocommerce-order-overview__payment-method method col-sm-6 mb-3 px-2">
                                    <div class="bg-secondary rounded-lg p-3 text-center font-size-md">
                                        Fecha de pago : <span
                                            class="font-weight-medium">{{ $transactionData->transactionDate }}</span>
                                    </div>
                                </div>
                            @endif
                            @if ($transactionData)
                                <div class="woocommerce-order-overview__payment-method method col-sm-6 mb-3 px-2">
                                    <div class="bg-secondary rounded-lg p-3 text-center font-size-md">
                                        Tarjeta bancaria : <span
                                            class="font-weight-medium">xxxx-xxxx-xxxx-{{ $transactionData->cardDetail->cardNumber }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>


                        <div class="woocommerce-order-details card">
                            <div class="card-header">
                                <h2 class="woocommerce-order-details__title h6 mb-0">Detalle</h2>
                            </div>
                            <div class="card-body">
                                <div class="row mx-n2">
                                    <div class="col-md-7">

                                        <div class="widget widget_products">
                                            <ul class="product_list_widget">
                                                @foreach ($order->order_items as $item)
                                                    @php
                                                    $product = Product::where('id',$item->product_id)->first();
                                                    // $subtotal += ($product->price * $item->qty);
                                                    // $subtotalshipping += ($item->shipping_total * $item->qty) ;
                                                    // $total += ($product->price * $item->qty) + ($item->shipping_total *  $item->qty);
                                                    @endphp
                                                    <li class="woocommerce-table__line-item order_item">
                                                        <div class="media align-items-center">

                                                            <img width="15%"
                                                                src="{{ asset($product->getFirstImagePath()) }}"
                                                                class="pr-3">
                                                            <div class="media-body">
                                                                <h5> {{ $product->seller->visible_name }} </h5>
                                                                <h6 class="widget-product-title">
                                                                    {{ $product->name }}
                                                                </h6>
                                                                <div class="widget-product-meta">
                                                                    <span class="text-accent mr-1"><span
                                                                            class="woocommerce-Price-amount amount"><span
                                                                                class="woocommerce-Price-currencySymbol">{{ currencyFormat($item->price ? $item->price : 0, 'CLP', true) }}</span></span>
                                                                        <span class="text-muted">× {{ $item->qty }}</span>
                                                                        <br>
                                                                        @if($product->is_service == 0)
                                                                        <span class=" mr-1"><span
                                                                                class="text-muted">Envío
                                                                            </span>
                                                                            <span class="text-center"> {{ $item->shipping->title }}
                                                                            </span>
                                                                            {{-- @if ($item->shipping_total == 0)
                                                                            <span class="text-center"> {{ $item->shipping->title }}
                                                                            </span>
                                                                            @else
                                                                                <span
                                                                                    class="woocommerce-Price-currencySymbol">{{ currencyFormat($item->shipping_total ? $item->shipping_total : 0, 'CLP', true) }}</span>
                                                                            @endif --}}
                                                                        </span>
                                                                        @endif

                                                                </div>
                                                            </div>
                                                    </li>
                                                @endforeach

                                                {{-- <li
                                                    class="woocommerce-table__line-item order_item">
                                                    <div class="media align-items-center">
                                                        <a href="https://demo2.madrasthemes.com/cartzilla/marketplace/product/project-devices-showcase-psd/"
                                                            class="widget-product-thumb">
                                                            <img width="350" height="235"
                                                                src="https://demo2.madrasthemes.com/cartzilla/marketplace/wp-content/uploads/sites/3/2020/03/4-350x235.jpg"
                                                                class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail"
                                                                alt=""
                                                                srcset="https://demo2.madrasthemes.com/cartzilla/marketplace/wp-content/uploads/sites/3/2020/03/4-350x235.jpg 350w, https://demo2.madrasthemes.com/cartzilla/marketplace/wp-content/uploads/sites/3/2020/03/4-300x202.jpg 300w, https://demo2.madrasthemes.com/cartzilla/marketplace/wp-content/uploads/sites/3/2020/03/4.jpg 550w"
                                                                sizes="(max-width: 350px) 100vw, 350px"> </a>
                                                        <div class="media-body">
                                                            <h6 class="widget-product-title">
                                                                <a
                                                                    href="https://demo2.madrasthemes.com/cartzilla/marketplace/product/project-devices-showcase-psd/">Project
                                                                    Devices Showcase (PSD)</a>
                                                            </h6>
                                                            <div class="widget-product-meta">
                                                                <span class="text-accent mr-1"><span
                                                                        class="woocommerce-Price-amount amount"><span
                                                                            class="woocommerce-Price-currencySymbol">$</span>16.<small>98</small></span></span>
                                                                <span class="text-muted">× 1</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li> --}}
                                            </ul>
                                        </div>


                                        {{-- <header>
                                            <h2>Sub Orders</h2>
                                        </header>

                                        <div class="dokan-info">
                                            <strong>Note:</strong>
                                            This order has products from multiple vendors. So we divided this order into
                                            multiple vendor orders.
                                            Each order will be handled by their respective vendor independently.
                                        </div>

                                        <table class="shop_table my_account_orders table table-striped">

                                            <thead>
                                                <tr>
                                                    <th class="order-number"><span class="nobr">Order</span></th>
                                                    <th class="order-date"><span class="nobr">Date</span></th>
                                                    <th class="order-status"><span class="nobr">Status</span></th>
                                                    <th class="order-total"><span class="nobr">Total</span></th>
                                                    <th class="order-actions">&nbsp;</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="order">
                                                    <td class="order-number">
                                                        <a
                                                            href="https://demo2.madrasthemes.com/cartzilla/marketplace/my-account/view-order/818/">
                                                            818 </a>
                                                    </td>
                                                    <td class="order-date">
                                                        <time datetime="2020-10-29" title="1603991640">October 29,
                                                            2020</time>
                                                    </td>
                                                    <td class="order-status" style="text-align:left; white-space:nowrap;">
                                                        On hold </td>
                                                    <td class="order-total">
                                                        <span class="woocommerce-Price-amount amount"><span
                                                                class="woocommerce-Price-currencySymbol">$</span>23.<small>89</small></span>
                                                        for 1 item
                                                    </td>
                                                    <td class="order-actions">
                                                        <a href="https://demo2.madrasthemes.com/cartzilla/marketplace/my-account/view-order/818/"
                                                            class="button view">View</a>
                                                    </td>
                                                </tr>
                                                <tr class="order">
                                                    <td class="order-number">
                                                        <a
                                                            href="https://demo2.madrasthemes.com/cartzilla/marketplace/my-account/view-order/819/">
                                                            819 </a>
                                                    </td>
                                                    <td class="order-date">
                                                        <time datetime="2020-10-29" title="1603991640">October 29,
                                                            2020</time>
                                                    </td>
                                                    <td class="order-status" style="text-align:left; white-space:nowrap;">
                                                        On hold </td>
                                                    <td class="order-total">
                                                        <span class="woocommerce-Price-amount amount"><span
                                                                class="woocommerce-Price-currencySymbol">$</span>16.<small>98</small></span>
                                                        for 1 item
                                                    </td>
                                                    <td class="order-actions">
                                                        <a href="https://demo2.madrasthemes.com/cartzilla/marketplace/my-account/view-order/819/"
                                                            class="button view">View</a>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table> --}}
                                    </div>
                                    <div class="col-md-5 pt-4 pt-md-0">
                                        <div class="bg-secondary rounded-lg p-4 h-100">
                                            <div
                                                class="d-flex justify-content-between align-items-center font-size-md mb-2 pb-1">
                                                <span class="mr-2">Subtotal:</span>
                                                <span class="text-right"><span class="woocommerce-Price-amount amount"><span
                                                            class="woocommerce-Price-currencySymbol">{{ currencyFormat($order->sub_total ? $order->sub_total : 0, 'CLP', true) }}</span></span>
                                            </div>
                                            @if ($order->shipping_total > 0)
                                                <div
                                                    class="d-flex justify-content-between align-items-center font-size-md mb-2 pb-1">
                                                    <span class="mr-2">Envío:</span>
                                                    <span class="text-right"><span
                                                            class="woocommerce-Price-amount amount"><span
                                                                class="woocommerce-Price-currencySymbol">{{ currencyFormat($order->shipping_total ? $order->shipping_total : 0, 'CLP', true) }}
                                                                {{-- <small
                                                                    class="shipped_via">via Flat rate</small>
                                                                --}}
                                                            </span>
                                                </div>
                                            @endif
                                            <div
                                                class="d-flex justify-content-between align-items-center font-size-md mb-2 pb-1">
                                                <span class="mr-2"> Método de pago :</span>
                                                <span
                                                    class="text-right">{{ $order->order_payments->first()->method_title }}</span>
                                            </div>
                                            <div
                                                class="d-flex justify-content-between align-items-center font-size-md mb-2 pb-1">
                                                <span class="mr-2">Total:</span>
                                                <span class="text-right"><span class="woocommerce-Price-amount amount"><span
                                                            class="woocommerce-Price-currencySymbol">{{ currencyFormat($order->total ? $order->total : 0, 'CLP', true) }}</span></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <section class="woocommerce-customer-details">
                            <div class="row pt-4">
                                <div class="col-sm-6 mb-4 mb-sm-0">
                                    <div class="border rounded-lg p-4 h-100">
                                        <h2 class="woocommerce-column__title h6">Dirección de facturación</h2>
                                        <ul class="font-size-sm list-unstyled">
                                            @if (isset($addressInvoice) && !empty($addressInvoice->address_street))

                                                <li class="woocommerce-customer-details--address d-flex">
                                                    <i class="czi-card opacity-60 mr-2 mt-1"></i>
                                                    <div>RUT: {{ $addressInvoice->uid }}
                                                </li>
                                                <li class="woocommerce-customer-details--address d-flex">
                                                    <i class="czi-user opacity-60 mr-2 mt-1"></i>
                                                    <div>
                                                        {{ $addressInvoice->first_name . '  ' . $addressInvoice->last_name }}
                                                </li>
                                                <li class="woocommerce-customer-details--address d-flex">
                                                    <i class="czi-location opacity-60 mr-2 mt-1"></i>
                                                    <div>
                                                        {{ $addressInvoice->address_street . ' ' . $addressInvoice->address_number . ' ' . $addressInvoice->address_office }}
                                                        @if ($communeInvoice)
                                                            {{ $communeInvoice->name }}
                                                        @endif
                                                    </div>
                                                </li>
                                                <li class="woocommerce-customer-details--phone d-flex">
                                                    <i class="czi-mobile opacity-60 mr-2 mt-1"></i>
                                                    <div>{{ $addressInvoice->cellphone }}</div>
                                                </li>
                                                <li class="woocommerce-customer-details--email d-flex">
                                                    <i class="czi-mail opacity-60 mr-2 mt-1"></i>
                                                    <div>{{ $addressInvoice->email }}</div>
                                                </li>
                                            @else
                                                <li class="woocommerce-customer-details--address d-flex">
                                                    <i class="czi-card opacity-60 mr-2 mt-1"></i>
                                                    <div>{{ $order->uid }}
                                                </li>
                                                <li class="woocommerce-customer-details--address d-flex">
                                                    <i class="czi-user opacity-60 mr-2 mt-1"></i>
                                                    <div>{{ $order->first_name . ' ' . $order->last_name }}
                                                </li>
                                                <li class="woocommerce-customer-details--address d-flex">
                                                    <i class="czi-location opacity-60 mr-2 mt-1"></i>
                                                    <div>
                                                        {{ $addressShipping->address_street . '  ' . $addressShipping->address_number . ' ' . $addressShipping->address_office }}
                                                        @if ($communeShipping)
                                                            {{ $communeShipping->name }}
                                                        @endif
                                                    </div>
                                                </li>
                                                <li class="woocommerce-customer-details--address d-flex">
                                                    <i class="czi-mobile opacity-60 mr-2 mt-1"></i>
                                                    <div>{{ $order->cellphone }}</div>
                                                </li>
                                                <li class="woocommerce-customer-details--address d-flex">
                                                    <i class="czi-mail opacity-60 mr-2 mt-1"></i>
                                                    <div>{{ $order->email }}</div>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="border rounded-lg p-4 h-100">
                                        <h2 class="woocommerce-column__title h6">Dirección de envío</h2>
                                        @if (isset($addressShipping))
                                            <ul class="font-size-sm list-unstyled">
                                                <li class="woocommerce-customer-details--address d-flex">
                                                    <i class="czi-card opacity-60 mr-2 mt-1"></i>
                                                    <div>{{ $order->uid }}
                                                </li>
                                                <li class="woocommerce-customer-details--address d-flex">
                                                    <i class="czi-user opacity-60 mr-2 mt-1"></i>
                                                    <div>{{ $order->first_name . ' ' . $order->last_name }}
                                                </li>
                                                <li class="woocommerce-customer-details--address d-flex">
                                                    <i class="czi-location opacity-60 mr-2 mt-1"></i>
                                                    <div>
                                                        {{ $addressShipping->address_street . ' ' . $addressShipping->address_number . ' ' . $addressShipping->address_office }}
                                                        @if ($communeShipping)
                                                            {{ $communeShipping->name }}
                                                        @endif
                                                    </div>
                                                </li>
                                                <li class="woocommerce-customer-details--address d-flex">
                                                    <i class="czi-mobile opacity-60 mr-2 mt-1"></i>
                                                    <div>{{ $order->cellphone }}</div>
                                                </li>
                                                <li class="woocommerce-customer-details--address d-flex">
                                                    <i class="czi-mail opacity-60 mr-2 mt-1"></i>
                                                    <div>{{ $order->email }}</div>
                                                </li>
                                            </ul>
                                        @endif
                                    </div>
                                </div>
                            </div>


                        </section>


                    </div>
                </div>
            </div>
        </div>

        @php
            $sellersArrange = $order->order_items()->with('seller')->whereHas('shipping', function($query) {
                $query->where('code', 'arrange_with_seller');
            })->get()->map(function($item) {
                return $item->seller;
            })->unique();
        @endphp
        @if (count($sellersArrange) > 0)
        <div class="pt-1">
            <div class="card py-1 mt-sm-1">
                <div class="card-body text-center">
                    <p class="font-size-sm mb-2">Hay productos que poseen "Envio a convenir con el vendedor". Puede dejarle un mensaje a cada uno de ellos.</p>
                    <hr class="mt-4 mb-4">
                        <div class="row">
                        @foreach($sellersArrange as $seller)
                            <div class="col-md-6 mb-5 mx-auto">
                            @livewire('send-message-to-seller',['seller' => $seller, 'order' => $order])
                            </div>
                        @endforeach
                        </div>
                </div>
            </div>
        </div>
        @endif
        <div class="pt-5">
            <div class="card py-3 mt-sm-3">
                <div class="card-body text-center">
                    {{-- <h2 class="h4 pb-3">Gracias por su pedido!</h2>
                    --}}
                    <p class="font-size-sm mb-2">Su pedido ha sido realizado y será procesado tan pronto como sea posible.
                    </p>
                    {{-- <p class="font-size-sm mb-2">Asegúrese de anotar su número de pedido,
                        que es <span class='font-weight-medium'>#{{ $order->id }}</span></p>
                    --}}
                    <p class="font-size-sm">Recibirá un correo electrónico en breve con la confirmación de su pedido.
                        <u>Ahora puedes:</u>
                    </p><a class="btn btn-secondary mt-3 mr-3" href="/">Volver a comprar</a>
                    <a class="btn btn-primary mt-3"
                        href="{{ route('transbank.webpayplus.mall.download', ['order' => $order->id]) }}">&nbsp;Descargar
                        orden</a>
                </div>
            </div>
        </div>
    </div>
@endsection
