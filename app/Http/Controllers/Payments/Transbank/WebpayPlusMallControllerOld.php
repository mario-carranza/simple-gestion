<?php

namespace App\Http\Controllers\Payments\Transbank;

use App\Http\Controllers\Controller;
use App\Mail\OrderUpdated;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
// use Barryvdh\DomPDF\PDF;
use App\Models\OrderLog;
use App\Models\OrderPayment;
use App\Models\PaymentMethod;
use App\Models\PaymentMethodSeller;
use App\Models\Product;
use App\Models\Seller;
use App\Services\OrderLoggerService;
use Backpack\Settings\app\Models\Setting;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Transbank\Webpay\Configuration;
use Transbank\Webpay\Webpay;

class WebpayPlusMallController extends Controller
{
    const PAYMENT_CODE = 'tbkplusmall';
    private $paymentMethod;
    private $transaction;
    private $returnUrl;
    private $finalUrl;
    private $orderId;
    private $orderLoggerService;

    public function __construct()
    {

        $paymentMethodId = null;
        //Get Config Payment Method
        $this->paymentMethod = PaymentMethod::where('code', $this::PAYMENT_CODE)->first();
        if ($this->paymentMethod) {
            $paymentMethodId = $this->paymentMethod->id;
            $wpmConfig = json_decode($this->paymentMethod->json_value);
        }

        $configuration = new Configuration();

        $configuration->setEnvironment(Setting::get('payment_environment'));
        $configuration->setCommerceCode($wpmConfig[0]->variable_value);
        $configuration->setPublicCert($wpmConfig[1]->variable_value);
        $configuration->setPrivateKey($wpmConfig[2]->variable_value);
        $this->returnUrl = $wpmConfig[3]->variable_value;
        $this->finalUrl = $wpmConfig[4]->variable_value;

        if (Setting::get('payment_environment') == 'INTEGRACION') {
             $this->transaction = (new Webpay(Configuration::forTestingWebpayPlusMall()))->getMallNormalTransaction();
        } else {
            $this->transaction = (new Webpay($configuration))->getMallNormalTransaction();
        }

        $this->orderLoggerService = new OrderLoggerService();
    }

    public function redirect($orderId)
    {

        if (!intval($orderId)) {
            return redirect()->back()->with('error', 'Orden no generada , reintente');
        }
        $this->orderId = $orderId;
        //Get current Order
        $order = Order::where('id', $orderId)->first();

        // Identificador único de orden de compra generada por el comercio mall:
        $buyOrder = $order->id;
        // Identificador que será retornado en el callback de resultado:
        $sessionId = session()->getId();

        // Lista con detalles de cada una de las transacciones:
        $transactions = array();

        $products_id = OrderItem::whereOrderId($order->id)->select('product_id')->with('order')->get();
        foreach ($products_id as $id) {
            $ids[] = $id['product_id'];
        }

        $sellers_id = Product::whereIn('id', $ids)->select('seller_id')->groupBy('seller_id')->get();

        $sellers = Seller::whereIn('id', $sellers_id)->select('id', 'name')->get();

        // Group by items by businness

        $totalsByBusiness = [];
        foreach ($sellers as $key => $seller) {
            //TODO find payment method by id
            $pmSeller = PaymentMethodSeller::where('payment_method_id', $this->paymentMethod->id)->where('seller_id', $seller->id)->first();

            $totalsBySeller[$key] = array();
            $totalsBySeller[$key]['id'] = $seller->id;
            // $totalsBySeller[$key]['storeCode'] = $pmSeller->key;
            $totalsBySeller[$key]['amount'] = 0;
            //$totalsBySeller[$key]['status'] = $pmSeller->status;

            foreach ($order->order_items as $item) {

                $product = Product::find($item->product_id);
                if ($seller->id === $product->seller->id) {
                    $totalsBySeller[$key]['amount'] += ($item->price * $item->qty) + ($item->shipping_total);
                }
            }
        }

        // Order amount total
        $amountTotal = 0;

        //Add transactions
        foreach ($totalsBySeller as $key => $seller) {

            // Add transaction
            // $transactions[] = array(
            //     "storeCode" => $seller['storeCode'],
            //     "amount" => $seller['amount'],
            //     "buyOrder" => $buyOrder . 't' . ($key + 1),
            // );
            $amountTotal += $seller['amount'];
        }

        $transactions[] = array(
            "storeCode" => Setting::get('storecode_payment'),
            "amount" => $amountTotal,
            "buyOrder" => $buyOrder . 't1',
        );
        // $amountTotal += $seller['amount'];

        $response = $this->transaction->initTransaction($buyOrder, $sessionId, $this->returnUrl, $this->finalUrl, $transactions);

        //Register  order payment

        $orderpayment = new OrderPayment();
        $data = [
            'event' => 'init transaction',
            'data' => $response,
            'buyOrder' => $buyOrder,
            'sessionId' => $sessionId,
            'transactions' => $transactions,
        ];

        $orderpayment->order_id = $order->id;
        $orderpayment->method = $this->paymentMethod->code;
        $orderpayment->method_title = $this->paymentMethod->title;
        $orderpayment->json_out = json_encode($data);
        $orderpayment->date_out = Carbon::now();
        $orderpayment->save();

        //Register  order log

        $orderlog = new OrderLog();
        $orderlog->order_id = $order->id;
        $orderlog->event = 'Inicio de pago';
        $orderlog->save();

        if (!isset($response->url)) {
            $result = null;
            // return redirect()->back()->with('error', 'Ocurrió un error al generar la url de pago');
            return view('payments.transbank.webpay.mall.failed', compact('result', 'order'));
        } else {

            return view('payments.transbank.webpay.mall.redirect', compact('response'));
        }
    }

    public function response()
    {

        $sessionId = null;
        $result = $this->transaction->getTransactionResult(request()->input("token_ws"));

        if (!isset($result->buyOrder)) {
            return redirect()->route('home');
        }
        $sessionId = $result->sessionId;
        session()->setId($sessionId);
        session()->start();

        $this->orderId = $result->buyOrder;

        //Register  order payment
        $orderpayment = OrderPayment::where('order_id', $this->orderId)->first();
        $data = [
            'event' => 'result transaction',
            'token' => request()->input("token_ws"),
            'data' => $result,

        ];
        $orderpayment->json_in = json_encode($data);
        $orderpayment->date_in = Carbon::now();
        $orderpayment->save();

        //Register  order log

        $orderlog = new OrderLog();

        $orderlog->order_id = $this->orderId;
        $orderlog->event = 'Resultado pago';
        $orderlog->save();


        $finalresult = false;
        if (is_array($result->detailOutput)) {
            foreach ($result->detailOutput as $output) {
                // Se debe chequear cada transacción de cada tienda del
                // mall por separado:
                if ($output->responseCode == 0) {
                    // Transaccion exitosa, puedes procesar el resultado
                    // con el contenido de las variables result y output.
                    $finalresult = true;
                } else {
                    $finalresult = false;
                }
            }
        } else {

            if ($result->detailOutput->responseCode == 0) {
                // Transaccion exitosa, puedes procesar el resultado
                // con el contenido de las variables result y output.
                $finalresult = true;
            } else {
                $finalresult = false;
            }
        }
        if ($finalresult) {
            //Update order status
            $order = Order::where('id', $this->orderId)->first();
            $order->status = 2; //paid
            $order->update();
            // Reducir invententario de product
            // Por cada item
            $orderItems = $order->order_items;

            foreach ($orderItems as $orderItem) {
                if ($orderItem->product->use_inventory_control) {
                    // 1. obtener cantidad en stock (cual bodega)
                    $qtyInStock = $orderItem->product->inventories->first()->pivot->qty;
                    $inventorySourceId = $orderItem->product->inventories->first()->id;
                    // 2. restar cantidad y verificar que no sea negativa
                    $finalQtyStock = $qtyInStock - $orderItem->qty;
                    // 3. guardar cantidad en inventario
                    $orderItem->product->updateInventory($finalQtyStock, $inventorySourceId);
                }
            }

            $cart = Cart::where('session_id', $sessionId)->first();

            //Destroy cart
            if ($cart) {
                $cart->cart_items()->delete();
                $cart->delete();
            }
            // $order = Order::where('id', $orderId)->first();
            $sellers = $order->getSellers();
            //Order to customer
            $datacustomer = [
                'email' => $order->email,
            ];
            try {
                Mail::to($order->email)->send(new OrderUpdated($order, 1, null));
                //Create log event
                $this->orderLoggerService->registerLog($order, 'Customer Email Sent', $datacustomer);

            } catch (Exception $ex) {
                $datacustomer['error'] = $ex->getMessage();

                //Create log event
                $this->orderLoggerService->registerLog($order, 'Customer Email Error', $datacustomer);
            }

            //Order to seller
            $datasellers = [
                'email' => null
            ];
            foreach ($sellers as $seller) {
                $datasellers['email'] = $seller->email ;
                try {
                    Mail::to($seller->email)->bcc('jorge.castro@twgroup.cl')->send(new OrderUpdated($order, 2, $seller));
                    //Create log event
                    $this->orderLoggerService->registerLog($order, 'Seller Email Sent', $datasellers);
                } catch (Exception $ex) {
                    $datasellers['error'] = $ex->getMessage();

                    //Create log event
                    $this->orderLoggerService->registerLog($order, 'Seller Email Error', $datasellers);
                }
            }
            //Order to admins
            $dataadmins = [
                'email' => null
            ];
            $administrators = Setting::get('administrator_email');
            $recipients = explode(';', $administrators);
            foreach ($recipients as $key => $recipient) {
                $dataadmins['email'] = $recipient ;
                try {
                    Mail::to($recipient)->send(new OrderUpdated($order, 3, null));
                    //Create log event
                    $this->orderLoggerService->registerLog($order, 'Admin Email Sent', $dataadmins);
                } catch (Exception $ex) {
                    $dataadmins['error'] = $ex->getMessage();

                    //Create log event
                    $this->orderLoggerService->registerLog($order, 'Admin Email Error', $dataadmins);
                }
            }

            //Verifico que no existan error en el envio de mails
            if (count(Mail::failures()) > 0) {

                $order->email_sent = false;
                $order->update();
            } else {
                $order->email_sent = true;
                $order->update();
            }

            return view('payments.transbank.webpay.mall.complete', compact('result', 'order'));
        } else {
            //Update order status
            $order = Order::where('id', $this->orderId)->first();
            $order->status = 4; //Reject
            $order->update();
            return view('payments.transbank.webpay.mall.failed', compact('result', 'order'));
        }
    }

    public function download($orderId)
    {
        $order = Order::where('id', $orderId)->first();
        // $sellers = $order->getSellers();
        // //Order to customer
        // Mail::to($order->email)->send(new OrderUpdated($order,1,null));
        // //Order to seller
        // foreach($sellers as $seller){
        //     Mail::to($seller->email)->send(new OrderUpdated($order,2,$seller));
        // }
        // //Order to admin
        $data = [
            'order' => $order,
        ];
        $pdf = PDF::loadView('order.pdf_order', $data);
        return $pdf->download('order_' . $orderId . '.pdf');
    }

    public function test($orderId)
    {
        # code...
        $order = Order::where('id', $orderId)->first();

        $result = null;
        return view('payments.transbank.webpay.mall.complete', compact('result', 'order'));
    }

    function final () {
        $sessionId = request()->input("TBK_ID_SESION");
        session()->setId($sessionId);
        session()->start();
        $result = $this->transaction->getTransactionResult(request()->input("TBK_TOKEN"));
        $orderId = request()->input('TBK_ORDEN_COMPRA');
        $order = Order::where('id', $orderId)->first();
        return view('payments.transbank.webpay.mall.failed', compact('result', 'order'));
    }
}
