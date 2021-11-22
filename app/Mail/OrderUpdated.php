<?php

namespace App\Mail;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Seller;
use App\Models\Commune;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Models\ShippingMethod;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $orderData;
    public $orderItems;
    public $logo;
    public $addressShipping;
    public $addressInvoice;
    public $communeShipping;
    public $communeInvoice;
    public $paymentData;
    public $title;
    public $shippingMessage;
    /**
     * Create a new message instance.
     *
     * Int receiver
     * 1-Customer
     * 2-Filter Sellers
     * 3-Admin
     */
    public function __construct(Order $order, Int $receiver, Seller $seller = null)
    {

        //
        if ($order) {

            if($order->status == 3 ){
                $this->subject = 'Orden completa';
            }else{
                $this->subject = 'Nueva Orden';
            }


            $this->orderData['id'] = $order->id;
            $this->orderData['fecha'] =  $order->created_at;
            $this->orderData['uid'] =  $order->uid;
            $this->orderData['first_name'] =  $order->first_name;
            $this->orderData['last_name'] =  $order->last_name;
            $this->orderData['cellphone'] =  $order->cellphone;
            $this->orderData['email'] =  $order->email;



            $addressData = $order->json_value;



            $this->addressShipping = null;
            if (isset($addressData['addressInvoice'])) {

                $this->addressShipping = $addressData['addressShipping'];
            }


            $this->addressInvoice = null;
            if (isset($addressData['addressInvoice'])) {
                $this->addressInvoice = $addressData['addressInvoice'];
            }

            $this->communeShipping = null;
            $this->communeInvoice = null;

            if ($this->addressShipping) {
                $this->communeShipping = Commune::where('id', $this->addressShipping->address_commune_id)->first();
            }

            if ($this->addressInvoice) {
                $this->communeInvoice = Commune::where('id', $this->addressInvoice->address_commune_id)->first();
            }

            //If receiver is seller, filter items by seller
            if ($receiver == 2) {
                $this->title = 'Orden generada';
                //$this->orderItems = $order->order_items()->;
                foreach ($order->order_items as $item) {
                    if ($item->product->seller->id == $seller->id) {
                        $shipping_method = ShippingMethod::where('id', $item->shipping_id)->first();
                        $item->shipping_method = $shipping_method;
                        $this->orderItems[] = $item;
                    }
                }
            } else {

                if(count($order->order_payments)>0){
                    $this->paymentData['title'] = $order->order_payments->first()->method_title;
                    $this->paymentData['date'] = Carbon::createFromFormat('Y-m-d H:i:s', $order->order_payments->first()->created_at)->format('d/m/Y H:i:s');
                }else{
                    $this->paymentData['title'] = 'Sin información';
                    $this->paymentData['date'] = '';
                }
                $this->paymentData['total'] =  currencyFormat($order->total ? $order->total : 0, 'CLP', true);
                if ($receiver == 1) {
                    if($order->status == 3){
                        $this->title = '¡Tu orden está completa!';
                    }else{
                        $this->title = '¡Tu orden está pagada!';
                    }

                    // “Próximamente estaremos notificando la fecha de envío” cambiar por
                    //$this->shippingMessage = '“*Por evento Cyber las fechas de envío podrían variar, estaremos notificando la fecha de envío”';
                    $this->shippingMessage = '';
                }else{
                    $this->title = '¡Nueva orden generada!';


                }
                // $this->orderItems = $order->order_items;
                foreach ($order->order_items as $item) {

                    $shipping_method = ShippingMethod::where('id', $item->shipping_id)->first();
                    $item->shipping_method = $shipping_method;
                    $this->orderItems[] = $item;
                }
            }
        }
        $this->logo = 'img/logo-pyme.png';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject)->view('maileclipse::templates.orderEmailTemplate');
    }
}
