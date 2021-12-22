<?php

namespace App\Http\Controllers\Admin\Report;

use App\Models\Order;
use App\Models\Seller;
use Backpack\CRUD\app\Http\Controllers\BaseController;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Class OrderCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class SalesReportController extends BaseController
{

    public function __construct()
    {
        $this->middleware(backpack_middleware());
    }

    public function index()
    {
        $request = request();

        // $period = $this->getPeriod();

        $data = [];
        $subscriptionPlan = null;
        $sellers = Seller::orderBy('visible_name')->get();
        $data = $this->getData($request);
        if ($request->wantsJson()) {
            return json_encode($data);
        } else {
            return view('backpack::reports.index', compact('data', 'subscriptionPlan', 'sellers'));
        }
    }

    private function getPeriod()
    {
        $request = request();

        if ($request->wantsJson()) {
            $from = Carbon::parse($request->from) ?? today()->subDays(14);
            $to = Carbon::parse($request->to) ?? today();

        } else {
            $from = today()->subDays(14);
            $to = today();
        }

        return [$from, $to];
    }

    private function getData(Request $request)
    {
        $data = [];
        $period = $this->getPeriod();
        $requestSellerId = -1;
        if (backpack_user()->hasRole('Vendedor marketplace')) {
            $requestSellerId = Seller::where('user_id', backpack_user()->id)->firstOrFail()->id;
        } else {
            if (isset($request->seller)) {
                $requestSellerId = $request->seller;
            } else {
                $requestSellerId = -1;
            }
        }

        $sales = null;

        if ($requestSellerId == -1) {

            $sales = Order::whereHas('order_items')
                ->whereBetween(
                    'created_at',
                    [$period[0]->startOfDay(),
                        $period[1]->endOfDay()]
                )->with(['order_items.seller' => function ($query) {
                $query->groupBy('id');
            }])->whereIn('status', [Order::STATUS_PAID, Order::STATUS_COMPLETED])
                ->get();

        } else {
            $sales = Order::whereHas('order_items'
                , function ($query) use ($requestSellerId) {
                    $query->where('seller_id', $requestSellerId);
                }
            )->whereBetween(
                'created_at',
                [$period[0]->startOfDay(),
                    $period[1]->endOfDay()]
            )->with(['order_items.seller' => function ($query) use ($requestSellerId) {
                $query->where('id', $requestSellerId)->groupBy('id');
            }])->whereIn('status', [Order::STATUS_PAID, Order::STATUS_COMPLETED])
                ->get();
        }

        foreach ($sales as $order) {
            /*TODO update */
            $orderPayment = null;
            if (!is_null($order->order_payments)) {

                $tmpOrderPayment = $order->order_payments->first()->json_in;
                if (!is_null($tmpOrderPayment)) {
                    $tmpOrderPayment = json_decode($tmpOrderPayment);

                    if (!is_null($tmpOrderPayment->data)) {


                        // Como el formato de la respuesta de Transbank cambio, se debe
                        // transformar la nueva respuesta para que coincida con las que
                        // estan guardadas en ordenes anteriores

                        if (!isset($tmpOrderPayment->data->detailOutput)) {
                            $tmpOrderPayment->data = (object) [
                                'buyOrder' => $tmpOrderPayment->data->buy_order,
                                'cardDetail' => $tmpOrderPayment->data->card_detail,
                                'detailOutput' => (object) [
                                    'paymentTypeCode' => $tmpOrderPayment->data->details[0]->payment_type_code
                                ],
                                'sessionId' => $tmpOrderPayment->data->session_id,
                                'transactionDate' => $tmpOrderPayment->data->transaction_date,
                                'VCI' => $tmpOrderPayment->data->details[0]->payment_type_code,
                                'urlRedirection' => '',
                            ];
                        }

                        if (!is_null($tmpOrderPayment->data->detailOutput)) {

                            if (isset($tmpOrderPayment->data->detailOutput->paymentTypeCode)) {
                                $pymentType = $tmpOrderPayment->data->detailOutput->paymentTypeCode;
                                switch ($pymentType) {
                                    case 'VD':$orderPayment = 'Venta Débito.';
                                        break;
                                    case 'VN':$orderPayment = 'Venta Normal.';
                                        break;
                                    case 'VC':$orderPayment = 'Venta en cuotas.';
                                        break;
                                    case 'SI':$orderPayment = '3 cuotas sin interés.';
                                        break;
                                    case 'S2':$orderPayment = '2 cuotas sin interés.';
                                        break;
                                    case 'NC':$orderPayment = 'N Cuotas sin interés';
                                        break;
                                    case 'VP':$orderPayment = 'Venta Prepago.';
                                        break;
                                }
                            }
                        }
                    }
                }
            }

            $itemSale = [];
            if (isset($order->order_items)) {
                $sellers = $order->order_items->groupBy('seller_id');
                if ($requestSellerId == -1) {
                    $sellers = $order->order_items->groupBy('seller_id'); //collec de items por vendedor

                } else {
                    $sellers = $order->order_items->where('seller_id', $requestSellerId)->groupBy('seller_id'); //collec de items por vendedor
                }

                $sellers->map(function ($orderItems) {
                    $totalOrder = 0;
                    $totalCommission = 0;
                    $sellerName = null;

                    foreach ($orderItems as $orderItem) {
                        if (empty($sellerName)) {
                            $sellerName = $orderItem->seller->visible_name;
                        }
                        if ($orderItem->seller->commission_enable == 1) {
                            $percentageCommission = $orderItem->seller->commission_percentage;
                            if (!is_null($percentageCommission)) {
                                $percentageCommission = $percentageCommission / 100;
                            }
                            $totalCommission += ($orderItem->sub_total + $orderItem->shipping_total) * $percentageCommission;
                        } else {
                            $totalCommission += 0; //($orderItem->sub_total + $orderItem->shipping_total);
                        }
                        $totalOrder += ($orderItem->sub_total + $orderItem->shipping_total);
                    }
                    $orderItems->totalOrder = $totalOrder;
                    $orderItems->totalCommission = $totalCommission;
                    $orderItems->totalFinal = $totalOrder - $totalCommission;
                    $orderItems->sellerName = $sellerName;
                    return $orderItems;
                });

                foreach ($sellers as $sellerId => $seller) {
                    $itemSale['id'] = $order->id;
                    $itemSale['exhibitor'] = $seller->sellerName;
                    $itemSale['created_at'] = $order->created_at;
                    $itemSale['payment'] = $orderPayment;
                    $itemSale['total'] = $seller->totalOrder;
                    $itemSale['totalCommission'] = $seller->totalCommission;
                    $itemSale['totalFinal'] = $seller->totalFinal;
                    array_push($data, $itemSale);
                }
            }

        }

        return $data;
    }

}
