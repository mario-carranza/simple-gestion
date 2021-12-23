<?php

namespace App\Http\Controllers\Payments\Transbank;

use Exception;
use Carbon\Carbon;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Seller;
// use Barryvdh\DomPDF\PDF;
use App\Models\Company;
use App\Models\Product;
use App\Models\OrderLog;
use App\Models\OrderItem;
use App\Mail\OrderUpdated;
use App\Mail\OrderPayedMail;
use App\Models\OrderPayment;
use Illuminate\Http\Request;
use Transbank\Webpay\Webpay;
use App\Models\OrderErrorLog;
use App\Models\PaymentMethod;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\DB;
use App\Models\PaymentMethodSeller;
use Illuminate\Support\Facades\Log;
use Transbank\Webpay\Configuration;
use App\Http\Controllers\Controller;
use App\Mail\OrderNotSendCovepaMail;
use App\Services\OrderLoggerService;
use Illuminate\Support\Facades\Mail;
use App\Services\Covepa\CovepaService;
use Backpack\Settings\app\Models\Setting;
use Illuminate\Contracts\Session\Session;
use App\Services\Transbank\WebpayPlusService;



class WebpayPlusController extends Controller
{
    const PAYMENT_CODE = 'tbkplus';
    private $paymentMethod;
    private $transaction;
    private $returnUrl;
    private $finalUrl;
    private $orderId;
    private $covepaService;
    private WebpayPlusService $webpayService;

    public function __construct()
    {
        $this->covepaService = new CovepaService();

        $this->webpayService = new WebpayPlusService();


        $this->paymentMethod = PaymentMethod::where('code', $this::PAYMENT_CODE)->first();

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
            $amountTotal += $seller['amount'];
        }

        try {
            $response = $this->webpayService->createTransaction($amountTotal, $buyOrder, $sessionId);
        } catch (Exception $e) {
            $data = [
                'event' => 'init transaction',
                'data' => $e->getMessage(),
                'buyOrder' => $buyOrder,
                'sessionId' => $sessionId,
                'transactions' => $transactions,
            ];

            $orderpayment = new OrderPayment();

            $orderpayment->order_id = $order->id;
            $orderpayment->method = $this->paymentMethod->code;
            $orderpayment->method_title = $this->paymentMethod->title;
            $orderpayment->json_out = json_encode($data);
            $orderpayment->date_out = Carbon::now();
            $orderpayment->save();

            return view('payments.transbank.webpay.plus.failed', compact('order'));
        }

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

        if (!isset($response['url'])) {
            $result = null;
            return view('payments.transbank.webpay.plus.failed', compact('result', 'order'));
        } else {
            return view('payments.transbank.webpay.plus.redirect', compact('response'));
        }
    }

    public function response()
    {
        $sessionId = null;
        $result = $this->transaction->getTransactionResult(request()->input("token_ws"));

        if (!isset($result->buyOrder)) {
            return redirect('/');
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

        $order = Order::where('id', $this->orderId)->first();

        $finalresult = false;

        $output = $result->detailOutput;
        if ($output->responseCode == 0) {
            $finalresult = true;
        } else {
            $finalresult = false;
        }

        if ($finalresult) {
            $order->status = 2; // Pagada
            $order->order_status = Order::ORDER_STATUS_CONFIRMED;
            $order->update();

            /* DB::table('orders_status_history')->insert([
                'order_id' => $order->id,
                'order_status' => Order::ORDER_STATUS_CONFIRMED,
                'created_at' =>  now(),
                'updated_at' => now(),
            ]); */

            try {
                $orderResponse = $this->covepaService->createOrder($order);
                $orderResponse = json_decode($orderResponse, true);

                if ($orderResponse['resultado'] == false) {
                    Log::error('Error enviando venta a covepa', [
                        'api_response' => $orderResponse,
                        'json_data' => json_encode($this->covepaService->prepareOrderData($order)),
                        'order_id' => $order->id,
                        'user_id' => backpack_user()->id ?? null,
                    ]);

                    $mail = new OrderNotSendCovepaMail($order, json_encode($orderResponse), null, json_encode($this->covepaService->prepareOrderData($order)));

                    OrderErrorLog::create([
                        'order_id' => $order->id,
                        'order_json' => $this->covepaService->prepareOrderData($order),
                        'api_response' => json_encode($orderResponse),
                        'error_message' => 'Error enviando orden a servidores de Covepa'
                    ]);
                    
                    Mail::to(Company::MAIL_TO_ERRORS)->send($mail);
                    
                } else {
                    //@todo eliminar luego
                    Log::info('Orden enviada con exito', ['api_response' => $orderResponse]);
                }

            } catch (Exception $e) {
                Log::error('Error enviando venta a covepa', [
                    'order_id' => $order->id,
                    'user_id' => backpack_user()->id ?? null,
                    'exception' => $e,
                ]);

                OrderErrorLog::create([
                    'order_id' => $order->id,
                    'order_json' => $this->covepaService->prepareOrderData($order),
                    'error_message' => $e->getMessage(),
                ]);

                $mail = new OrderNotSendCovepaMail($order, null, $e);

                Mail::to(Company::MAIL_TO_ERRORS)->send($mail);
            }
            

            // Reducir invententario de product
            // Por cada item
            $orderItems = $order->order_items;

            foreach($orderItems as $orderItem) {
                if($orderItem->product->use_inventory_control) {
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

            $sellers = $order->getSellers();

            //Order to customer
            Mail::to($order->email)->send(new OrderPayedMail($order, 1));

            //Order to seller
            /* foreach ($sellers as $seller) {
                Mail::to($seller->email)->cc('jorge.castro@twgroup.cl')->send(new OrderUpdated($order, 2, $seller));
            } */

            //Order to admins
            $administrators = Setting::get('administrator_email');
            $recipients = explode(';', $administrators);
            foreach ($recipients as $key => $recipient) {
                Mail::to($recipient)->send(new OrderPayedMail($order, 2));
            }

            return view('payments.transbank.webpay.plus.complete', compact('result', 'order'));
        } else {
            $order->status = 4; // rechazada
            $order->update();
            return view('payments.transbank.webpay.plus.failed', compact('result', 'order'));
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
        return view('payments.transbank.webpay.plus.complete', compact('result', 'order'));
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
