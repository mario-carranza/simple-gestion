<?php

namespace App\Http\Livewire\Checkout;

use Exception;
use Carbon\Carbon;
use App\Models\Order;
use Livewire\Component;
use App\Models\CartItem;
use App\Models\OrderItem;
use App\Models\ShippingMethod;
use Illuminate\Support\Facades\DB;
use App\Services\Covepa\CovepaService;
use App\Services\Covepa\Helpers as CovepaHelper;

class Checkout extends Component
{
    public $steps;
    public $activeStep;
    public $subtotal;
    // public $shippingtotal;
    // public $chilexpresstotal;
    public $total;
    public $cart;
    public $items;
    public $loading;
    public $canContinue;
    public $chilexpress;
    public $shippings;
    public $shippingtotals;
    public $shippingTotal;
    public $blockButton;

    protected $listeners = [
        'prev-step' => 'prevStep',
        'next-step' => 'nextStep',
        'set-detail' => 'setDetails',
        'finishTask' => 'finishTask',
        'notFinishTask' => 'notFinishTask',
        'select-shipping' => 'addShipping',
        'pay' => 'pay',
        'change' => 'updateTotals',
        'updateTotals' => 'updateTotals',
        'cartpreview' => 'cartpreview',
        'update-shipping-totals' => 'updateShippingTotals',
        'checkout.blockButton' => 'blockButton',
       // 'updateLoading' => 'updateLoading'
    ];

    public function mount()
    {
        $this->steps = [
            [
                'name' => 'Carro',
                'status' => 'active',
                'number' => 1,
                'icon' => 'czi-cart',
                'prev-button' => 'Volver a comprar',
                'next-button' => 'Ingresar datos envío',
                'event-prev' => null,
                'event-next' => null,

            ],
            [
                'name' => 'Detalle',
                'status' => 'active',
                'number' => 2,
                'icon' => 'czi-user-circle',
                'prev-button' => 'Volver al carro',
                'next-button' => 'Seleccionar tipo de despacho',
                'event-prev' => 'cartpreview',
                'event-next' => 'details:save',

            ],
            [
                'name' => 'Despacho',
                'status' => '',
                'number' => 3,
                'icon' => 'czi-package',
                'prev-button' => 'Volver a información de envío',
                'next-button' => 'Detalles de despacho',
                'event-prev' => null,
                'event-next' => null,

            ],
            [
                'name' => 'Detalles',
                'status' => '',
                'number' => 4,
                'icon' => 'czi-lable',
                'prev-button' => 'Volver a selección de despacho',
                'next-button' => 'Ver detalles de la orden',
                'event-prev' => null,
                'event-next' => 'shipping-details:save',

            ],
            [
                'name' => 'Revisión',
                'status' => '',
                'number' => 5,
                'icon' => 'czi-check-circle',
                'prev-button' => 'Volver a detalles de despacho',
                'next-button' => 'Seleccionar método de pago',
                'event-prev' => null,
                'event-next' => null,

            ],
            [
                'name' => 'Pago',
                'status' => '',
                'number' => 6,
                'icon' => 'czi-card',
                'prev-button' => 'Volver a método de pago',
                'next-button' => '',
                'event-prev' => null,
                'event-next' => null,
            ],


        ];

        //Initialize active step
        $this->activeStep = $this->steps[1];

        //Get chilexpress
        $this->chilexpress = ShippingMethod::where('code', 'chilexpress')->first();
        //Get items
        $this->items = $this->getItems();

        $this->subtotal = $this->getSubTotal();
        $this->total = $this->getTotal();
        $this->shippings = [];

        //$this->loading = false;

    }

    public function render()
    {
        return view('livewire.checkout.checkout');
    }

    public function prevStep()
    {

        if ($this->activeStep['event-prev']) {
            $this->emit($this->activeStep['event-prev']);
        } else {
            $currentStep = array_search($this->activeStep, $this->steps);

            if ($currentStep > 0) {
                $this->steps[$currentStep]['status'] = '';
                $this->activeStep = $this->steps[$currentStep - 1];
            }
        }
        if($this->activeStep['number']!=3){
            $this->blockButton= false;
        }
    }
    public function nextStep($currentStep)
    {
        //$currentStep  = array_search($this->activeStep, $this->steps);

        if ($this->activeStep['event-next']) {
            $this->emit($this->activeStep['event-next']);
        } else {
            $this->finishTask($currentStep + 1);
        }
//        $this->activeStep = $this->steps[$currentStep + 1];
        //$this->

    }

    public function finishTask($nextStep)
    {
        /* $currentStep = array_search($this->activeStep, $this->steps); */
        $this->steps[$nextStep - 1]['status'] = 'active';
        $this->activeStep = $this->steps[$nextStep - 1];

       // $this->loading = false;
      // $this->updateLoading(false);
    }

    public function getItems()
    {
        return CartItem::whereCartId($this->cart->id)->with('product')->get();
        //    $items =  CartItem::whereCartId($this->cart->id)->with(['product' => function ($query) {
        //         $query->select('products.seller_id');
        //         $query->groupBy('seller_id');
        //     }])->get();

        //     dd($items);
    }

    public function addShipping($selected, $item)
    {

        // $shippingSelected = array(
        //     'id' => $selected['id'],
        //     'name' => $selected['name'],
        //     'total' => 0,
        //     'qty' => 0,

        // );

        // $existShipping = array_filter($this->shippings, function ($shipping) use ($shippingSelected) {
        //     return $shipping['id'] == $shippingSelected['id'];
        // });

        // if (!$existShipping) {

        //     array_push($this->shippings, $shippingSelected);
        // }

        $cartItem = CartItem::find($item);

        // $shippingId = $selected['id'];
        // $shippingTotal = $selected['price']; // * $cartItem->qty;

        $cartItem->shipping_id = $selected['id'];
        // $cartItem->shipping_total = $shippingTotal; // * $cartItem->qty; ;
        $cartItem->update();
        //$this->shippingtotal += $shippingTotal;

        $this->total = $this->getTotal();
        $this->cart->total = $this->total;
        //                 $this->cart->shipping_total = $this->shippingtotal; // //$shippingTotal;// *  $cartItem->qty);
        $this->cart->update();

    }

    private function getSubTotal(): float
    {
        $subtotal = 0;
        foreach ($this->getItems() as $item) {
            $subtotal += $item->price * $item->qty;
        }
        return $subtotal;
    }
    private function getTotal(): float
    {
        $this->shippingTotal = 0;
        if ($this->shippings) {
            foreach ($this->shippings as $key => $shipping) {
                $this->shippings[$key]['total'] = 0;
                $this->shippings[$key]['qty'] = 0;
            }
        }

        $total = 0;
        $totalshipping = 0;
        foreach ($this->getItems() as $item) {

            $total += $item->price * $item->qty;
            if ($this->shippings) {
                //  $shipping = array_search($item->shipping_id, array_column($this->shippings,'id'));
                //  dd( $this->shippings[0]);

                //  if ($shipping) {
                foreach ($this->shippings as $keyt => $shipping) {
                    if (intval($shipping['id']) == $item->shipping_id) {

                        if ($item->shipping_total) {
                            $this->shippings[$keyt]['total'] += $item->shipping_total;
                        } else {
                            $this->shippings[$keyt]['total'] = null;
                        }
                        $this->shippings[$keyt]['qty'] += $item->qty;
                        $totalshipping += $item->shipping_total;
                    }
                }

                //  }

            }

            }

        if ($this->shippingtotals) {
            foreach ($this->shippingtotals as $shippingtotal) {
                if (!is_null($shippingtotal['totalPrice'])) {
                    $this->shippingTotal += $shippingtotal['totalPrice'];
                }
            }
        }
        $total += $this->shippingTotal;

//        $total += $totalshipping;
        if ($total <= 0) {
            $total = 0;
            $subtotal = 0;
            $shippingtotal = 0;
            $this->canContinue = false;
        } else {
            $this->canContinue = true;
        }

        return $total;
    }

    

    public function notFinishTask()
    {

        //$this->loading = false;
       // $this->updateLoading(false);
    }

    public function pay()
    {
        $covepaService = new CovepaService();
        $sufficienQuantity = true;

        // Check inventory of every item
        foreach ($this->getItems() as $item) {
            $product = $item->product;
            if (!$product->haveSufficientQuantity($item->qty)) {
                $sufficienQuantity = false;
            }
        }

        if (!$sufficienQuantity) {
            return $this->emit('showToast', '¡Stock insuficiente!', 'Verifique la cantidad de sus productos.', 3000, 'warning');
        }

        // Cliente de covepa debe ser creado en base a la informacion de facturacion
        $invoiceData = (object) $this->cart->getInvoiceData();

        $checkCovepaCusomterExists = $this->checkCovepaCustomerExists(rutWithoutDV($invoiceData->uid));

        if ($checkCovepaCusomterExists == 2) {
            return  $this->emit('showToast', '¡No pudimos generar la orden!', 'Ocurrio un problema generando esta orden, contacte con el administrador para mas detalles.', 3000, 'warning');
        }

        if ($checkCovepaCusomterExists == 0) {

            $customerData = [
                'id' => rutWithoutDV($invoiceData->uid),
                'uid' => sanitizeRUT($invoiceData->uid),
                'taxable' => (bool) $invoiceData->is_company,
                'default_billing' => 1,
                'default_shipping' => 1,
                'confirmation' => Carbon::now()->format('d/m/Y'),
                'email' => $invoiceData->email,
                'telephone' => !empty($invoiceData->phone) ? $invoiceData->phone : $this->cart->pickup_person_info['phone'],
                'cellphone' => $invoiceData->cellphone,
                'firstname' => !$invoiceData->is_company ? $invoiceData->first_name : $invoiceData->business_name,
                'lastname' => !$invoiceData->is_company ? $invoiceData->last_name : '',
                'addresses' => [
                    [
                        'id' => 1,
                        'city_id' => CovepaHelper::COMMUNE_MAPPING[$invoiceData->address_commune_id]['id_city'],
                        'street' => $invoiceData->address_street,

                        // @todo
                        // Dato no requerido en el formulario. Colocar lo mismo que address_street o 
                        // hacerlo requerido en el formulario?

                        'number' => $invoiceData->address_office ?? $invoiceData->address_street,
                        
                        'cellphone' => $invoiceData->cellphone,
                        'telephone' => !empty($invoiceData->phone) ? $invoiceData->phone : $this->cart->pickup_person_info['phone'],
                        'taxable' => (bool) $invoiceData->is_company,
                        'uid' => sanitizeRUT($invoiceData->uid),
                        'firstname' => $invoiceData->is_company ? $invoiceData->business_name : $invoiceData->first_name,
                        'lastname' => $invoiceData->is_company ? null : $invoiceData->last_name,
                        'sii_activity' => CovepaHelper::GIRO_MAPPING[$invoiceData->business_activity_id] ?? null,

                        'default_shipping' => true,
                        'default_billing' => true,
                    ],
                ],
            ];

            try {
                $response = $covepaService->createCustomer($customerData);

                if ($response->getStatusCode() !== 201) {
                    \Log::error('Error creating new customer from pay() method on Checkout.php', ['error' => $response->getBody()->getContents(), 'data' => $customerData]);
                    return  $this->emit('showToast', '¡No pudimos generar la orden!', 'Ocurrio un problema generando esta orden, contacte con el administrador para mas detalles.', 3000, 'warning');
                }

            } catch (\Exception $e) {
                \Log::error('Error creating new customer from pay() method on Checkout.php: ' . $e->getMessage());
                return  $this->emit('showToast', '¡No pudimos generar la orden!', 'Ocurrio un problema generando esta orden, contacte con el administrador para mas detalles.', 3000, 'warning');
            }
        }

        if (is_array($checkCovepaCusomterExists)) {
            $covepaService = new CovepaService();

            if (isset($checkCovepaCusomterExists['email']) && $checkCovepaCusomterExists['email'] != $invoiceData->email) {

                try {
                    $updateCustomer = $covepaService->updateCustomerEmail(rutWithoutDV($invoiceData->uid), $invoiceData->email);

                    if ($updateCustomer->getStatusCode() != 200) {
                        \Log::error('Error updating customer email from pay() method on Checkout.php', ['error' => $updateCustomer->getBody()->getContents(), 'data' => ['email' => $invoiceData->email, 'id' => rutWithoutDV($invoiceData->uid)]]);
                        return  $this->emit('showToast', '¡No pudimos generar la orden!', 'Ocurrio un problema generando esta orden, contacte con el administrador para mas detalles. COD: CC-001', 3000, 'warning');    
                    }

                } catch (Exception $e) {
                    \Log::error('Error updating email customer from pay() method on Checkout.php: ' . $e->getMessage());
                    return  $this->emit('showToast', '¡No pudimos generar la orden!', 'Ocurrio un problema generando esta orden, contacte con el administrador para mas detalles. COD: CC-002', 3000, 'warning');    
                }
            }
        }
        
        // Get cart addresses
        $addressShipping = [
            'address_street' => $this->cart->address_street,
            'address_number' => $this->cart->address_number,
            'address_office' => $this->cart->address_office,
            'address_commune_id' => $this->cart->address_commune_id,
            'address_details' => $this->cart->shipping_details,
            'business_activity_id' => $this->cart->json_value['business_activity_id'] ?? null,
        ];
        
        $addressShipping = json_encode($addressShipping);

        $addressInvoiceCart = null;

        if ($this->cart->invoice_value) {
            $addressInvoiceCart = $this->cart->invoice_value;
            $addressInvoiceCart = json_decode($addressInvoiceCart, true);
            
            if ($addressInvoiceCart['is_business'] == true) {
                $addressInvoiceCart['first_name'] = $addressInvoiceCart['business_name'];
            }

            $addressInvoiceCart = json_encode($addressInvoiceCart);
        }

        $addressData = [
            'addressShipping' => $addressShipping,
            'addressInvoice' => $addressInvoiceCart,
        ];

        $order = new Order();
        $order->company_id = $this->cart->company_id;
        $order->uid = $this->cart->uid;
        $order->is_company = $this->cart->is_company;
        $order->first_name = $this->cart->first_name;
        $order->last_name = $this->cart->last_name;
        $order->email = $this->cart->email;
        $order->phone = $this->cart->phone;
        $order->cellphone = $this->cart->cellphone;
        $order->currency_id = $this->cart->currency_id;
        $order->customer_id = $this->cart->customer_id;
        $order->required_invoice = $this->cart->required_invoice;
        $order->pickup_person_info = $this->cart->pickup_person_info;
        $order->json_value = json_encode($addressData);
        $order->status = 1; //initiated
        $order->order_status = Order::ORDER_STATUS_WAITING_PAYMENT;
        $order->save();

        $shippingtotal_order = 0;
        $subtotal_order = 0;
        $total_order = 0;

        //Add Order Item
        foreach ($this->getItems() as $item) {
            $orderitem = new OrderItem();
            $orderitem->order_id = $order->id;
            $orderitem->seller_id = $item->product->seller->id;
            $orderitem->currency_id = 63;
            $orderitem->product_id = $item->product->id;
            $orderitem->name = $item->product->name;
            $orderitem->sku = $item->product->sku;
            $orderitem->price = $item->price;
            $orderitem->qty = $item->qty;
            $orderitem->shipping_id = $item->shipping_id;
            $orderitem->shipping_total = $item->shipping_total;
            $orderitem->sub_total = $item->price * $item->qty;
            $orderitem->total = ($item->price * $item->qty) + $item->shipping_total;
            $orderitem->save();
        }

        $order->shipping_total = $this->shippingTotal ;//$shippingtotal_order;
        $order->sub_total = $this->subtotal;//$subtotal_order;
        $order->total = $this->total ; //$total_order;

        $order->save();

        DB::table('orders_status_history')->insert([
            'order_id' => $order->id,
            'order_status' => Order::ORDER_STATUS_WAITING_PAYMENT,
            'created_at' =>  now(),
            'updated_at' => now(),
        ]);

        return redirect()->to(route('transbank.webpayplus.redirect', ['order' => $order]));
    }

    public function updateTotals()
    {
        $this->cart->recalculateSubtotal();
        $this->cart->update();
        $this->subtotal = $this->getSubTotal();
        $this->total = $this->getTotal();
        $this->emit('dropdown.update');
        $this->emit('cart.updateSubtotal');
    }

    public function cartpreview()
    {
        return redirect('shopping-cart');
    }

    public function updateShippingTotals($shippingsSellers)
    {
       //    dd($shippingsSellers);
        //     $shippings = array_column($shippingsSellers,'shipping');
        //     dd($shippings);


        $itemShippingTotal = [];

        foreach ($shippingsSellers as $sellerKey => $sellerValue) {

            foreach ($sellerValue as $shippingKey => $shippingValue) {

                //$existsKey = false;
                // if (isset($itemShippingTotal)) {
                //     $existsKey = array_key_exists(strval($shippingValue['shipping']['id']), $itemShippingTotal);
                // }
                if (isset($shippingValue) && isset($shippingValue['shipping'])) {
                    $indice = strval($shippingValue['shipping']['id']);

                    if (array_key_exists($indice, $itemShippingTotal)) {
                        if (!is_null($shippingValue['shipping']['totalPrice'])) {
                            $itemShippingTotal[$indice]['totalPrice'] += $shippingValue['shipping']['totalPrice'];
                            $itemShippingTotal[$indice]['totalShippingPackage'] += $shippingValue['shipping']['totalShippingPackage'];
                        }else{
                            $itemShippingTotal[$indice]['totalShippingPackage'] += $shippingValue['shipping']['totalShippingPackage'];
                        }

                    } else {
                        $itemShippingTotal[$indice] = [];
                        $itemShippingTotal[$indice]['title'] = $shippingValue['shipping']['title'];
                        $itemShippingTotal[$indice]['totalPrice'] = $shippingValue['shipping']['totalPrice'];
                        $itemShippingTotal[$indice]['totalShippingPackage'] = $shippingValue['shipping']['totalShippingPackage'];
                    }
                    // dd($itemShippingTotal, $shippingsSellers);
                }

            }
        }

        $this->shippingtotals = $itemShippingTotal;

        $this->emit('change');

        //$this->updateLoading(false);


    }

    public function updateLoading($value)
    {

        $this->loading = $value;
    }


    public function blockButton($value)
    {
        if ($this->activeStep['number'] ==3) {
            $this->blockButton = $value;
        }
    }

    /**
     * 0 - Customer doesnt exists on covepa
     * 2 - Internal or Server error
     * ARRAY RESPONSE - Customer exists on covepa
     */
    public function checkCovepaCustomerExists($id)
    {
        $covepaService = new CovepaService();

        try {
            $response = $covepaService->getCustomer($id);

            if ($response->getStatusCode() != 200) {
                return 0;
            }

            return json_decode($response->getBody()->getContents(), true);

        } catch (\Exception $e) {
            \Log::warning('error on checkCovepaCustomerExists function: ' . $e->getMessage());
            return 2;
        }
    }
}
