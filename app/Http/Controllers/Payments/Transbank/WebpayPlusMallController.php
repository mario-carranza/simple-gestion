<?php

namespace App\Http\Controllers\Payments\Transbank;

use Exception;
use Carbon\Carbon;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Seller;
// use Barryvdh\DomPDF\PDF;
use App\Models\Product;
use App\Models\OrderLog;
use App\Models\OrderItem;
use App\Mail\OrderUpdated;
use App\Models\OrderPayment;
use Illuminate\Http\Request;
use Transbank\Webpay\Webpay;
use App\Models\PaymentMethod;
use App\Models\ProductReservation;
use Barryvdh\DomPDF\Facade as PDF;
use App\Models\PaymentMethodSeller;
use Illuminate\Support\Facades\Log;
use Transbank\Webpay\Configuration;
use App\Http\Controllers\Controller;
use App\Services\OrderLoggerService;
use Illuminate\Support\Facades\Mail;
use Backpack\Settings\app\Models\Setting;
use Illuminate\Contracts\Session\Session;
use App\Services\Transbank\WebpayPlusMallService;

class WebpayPlusMallController extends Controller
{
    const PAYMENT_CODE = 'tbkplusmall';
    private $paymentMethod;
    private $transaction;
    private $returnUrl;
    private $finalUrl;
    private $orderId;
    private WebpayPlusMallService $service;

    public function __construct()
    {
        $this->paymentMethod = PaymentMethod::where('code', self::PAYMENT_CODE)->first();

        $this->service = new WebpayPlusMallService();
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
            $totalsBySeller[$key]['storeCode'] = $pmSeller->key;
            $totalsBySeller[$key]['amount'] = 0;
            $totalsBySeller[$key]['status'] = $pmSeller->status;

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
            $transactions[] = array(
                "commerce_code" => $seller['storeCode'],
                "amount" => $seller['amount'],
                "buy_order" => $buyOrder . 't' . ($key + 1),
            );
            $amountTotal += $seller['amount'];
        }

        try {
            $response = $this->service->createTransaction($buyOrder, $sessionId, $transactions);
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

            return view('payments.transbank.webpay.mall.failed', compact('order'));
        }

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
        $orderlog->json_value = json_encode($response);
        $orderlog->save();


        if (!isset($response['url'])) {
            $result = null;
            return view('payments.transbank.webpay.mall.failed', compact('result', 'order'));
        } else {
            return view('payments.transbank.webpay.mall.redirect', compact('response'));
        }
    }

    public function response(Request $request)
    {
        $sessionId = null;

        if (! $request->token_ws) {
            $order = Order::where('id', $request->TBK_ORDEN_COMPRA)->first();
            $order->status = Order::STATUS_REJECT;
            $order->update();
            return view('payments.transbank.webpay.mall.failed', compact('order'));
        }

        try {
            $result = $this->service->getTokenResult(request()->input('token_ws'));
        } catch (Exception $e) {
            Log::error('Error obteniendo resultado del token', ['error' => $e->getMessage(), 'stacktrace' => $e->getTraceAsString()]);
            
            $order = Order::where('id', $request->TBK_ORDEN_COMPRA)->first();
            $order->status = Order::STATUS_REJECT;
            $order->update();
            
            return view('payments.transbank.webpay.mall.failed', compact('order'));
        }

        $sessionId = $result['session_id'];
        session()->setId($sessionId);
        session()->start();

        $this->orderId = $result['buy_order'];

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
        $orderlog->json_value =  json_encode($data);
        $orderlog->save();

        $finalresult = false;

        if (is_array($result['details'])) {
            foreach ($result['details'] as $output) {
                // Se debe chequear cada transacción de cada tienda del
                // mall por separado:
                if ($output['response_code'] == 0) {
                    // Transaccion exitosa, puedes procesar el resultado
                    // con el contenido de las variables result y output.
                    $finalresult = true;
                } else {
                    $finalresult = false;
                }
            }
        } else {
            if ($result['details']['response_code'] == 0) {
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

            $orderItems = $order->order_items;

            foreach ($orderItems as $orderItem) {
                if ($orderItem->product_reservation) {
                    $orderItem->product_reservation->reservation_status = ProductReservation::PAYED_STATUS;
                    $orderItem->product_reservation->order_id = $order->id;
                    $orderItem->product_reservation->update();
                }

                // Reducir invententario de product
                // Por cada item
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

            $sellers = $order->getSellers();

            //Order to customer
            Mail::to($order->email)->send(new OrderUpdated($order, 1, null));

            //Order to seller
            foreach ($sellers as $seller) {
                Mail::to($seller->email)->cc('jorge.castro@twgroup.cl')->send(new OrderUpdated($order, 2, $seller));
                sleep(1);
            }

            //Order to admins
            $administrators = Setting::get('administrator_email');
            $recipients = explode(';', $administrators);
            foreach ($recipients as $key => $recipient) {
                Mail::to($recipient)->send(new OrderUpdated($order, 3, null));
                sleep(1);
            }

            sleep(1);

            return view('payments.transbank.webpay.mall.complete', compact('result', 'order'));
        } else {
            //Update order status
            $order = Order::where('id', $this->orderId)->first();
            $order->status = 4; // Reject
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

    public function final()
    {
        $sessionId = request()->input("TBK_ID_SESION");
        session()->setId($sessionId);
        session()->start();
        $result = $this->transaction->getTransactionResult(request()->input("TBK_TOKEN"));
        $orderId = request()->input('TBK_ORDEN_COMPRA');
        $order = Order::where('id', $orderId)->first();
        return view('payments.transbank.webpay.mall.failed', compact('result', 'order'));
    }
}
