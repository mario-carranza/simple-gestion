<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\OrderErrorLog;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function show(Request $request)
    {
        $order = Order::find($request['id']);

        if (!$order) return response()->json([ 
            'status' => 'error', 
            'message' => 'La orden solicitada no existe',
        ],  404);

        return response()->json([
            'status' => 'success',
            'data' => $order,
        ], 200);
    }

    public function updateStatus(Request $request, $id) 
    {
        $validator = Validator::make($request->all(), [ 
            'status' => [
                'required', 
                Rule::in([
                    Order::ORDER_STATUS_AVAILABLE_FOR_PICKUP,
                    Order::ORDER_STATUS_CONFIRMED,
                    Order::ORDER_STATUS_DELIVERED,
                    Order::ORDER_STATUS_DISPATCHED,
                    Order::ORDER_STATUS_IN_PREPARATION,
                    Order::ORDER_STATUS_INVOICED_DOCUMENT,
                    Order::ORDER_STATUS_WAITING_PAYMENT,
                ]),
            ],
        ]);
      
        if ($validator->fails()) {
          return response()->json([ 'status' => 'error', 'message' => $validator->errors() ], 400);
        }
        
        $order = Order::find($id);

        if (!$order) return response()->json([ 
            'status' => 'error', 
            'message' => 'La orden solicitada no existe',
        ],  404);

        $order->order_status = $request->status;

        $order->update();

        /* DB::table('orders_status_history')->insert([
            'order_id' => $order->id,
            'order_status' => $request->status,
            'created_at' =>  now(),
            'updated_at' => now(),
        ]); */

        return response()->json([
            'status' => 'success',
            'data' => null,
            'message' => 'Estado de orden actualizado',
        ], 200);
    }

    public function fetchErrorLogs(Request $request)
    {
        $orderLogs = OrderErrorLog::orderBy('created_at', 'DESC')->get();

        return [
            'status' => 'success',
            'data' => $orderLogs,
        ];

        return $orderLogs;
    }

    public function fetchErrorLog($id)
    {
        $orderLog = OrderErrorLog::where('order_id', $id);

        if (!$orderLog->count()) return response()->json([ 
            'status' => 'error', 
            'message' => 'La orden solicitada no existe',
        ],  404);

        return [
            'status' => 'success',
            'data' => $orderLog,
        ];

        return $orderLogs;
    }

    public function getLast(Request $request)
    {
        $status = $request->input('status');

        $order = Order::orderBy('created_at', 'desc')
                    ->when($status, function ($query) use ($status) {
                        return $query->where('status', $status);
                    })
                    ->first();
        
        if (!$order) return response()->json([ 
            'status' => 'error', 
            'message' => 'No existe una orden con los parametros especificados',
        ],  404);

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $order->id,
                'status' => $order->status_description,
                'order_basic_data' => [
                    'customer_id' => $order->customer_id,
                    'uid' => $order->uid,
                    'first_name' => $order->first_name,
                    'last_name' => $order->last_name,
                    'email' => $order->email,
                    'total' => $order->total,
                    'order_status' => $order->order_status,
                    'date' => Carbon::parse($order->created_at)->format('d-m-Y'),
                ],
            ],
        ], 200);
    }
}
