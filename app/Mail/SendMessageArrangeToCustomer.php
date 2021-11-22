<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;
use App\Models\Seller;

class SendMessageArrangeToCustomer extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    //public $data;
    public $order;
    public $seller;
    public $customerEmail;
    public $sellerMessage;

    public function __construct($customerEmail, Seller $seller, Order $order)
    {
        $this->customerEmail = $customerEmail;
        $this->order = $order;
        $this->seller = $seller;

        $sellerMessage = is_array($order->arrange_messages) ? $order->arrange_messages : json_decode($order->arrange_messages, true);
        $this->sellerMessage =  $sellerMessage[$seller->id]['message'] ?? '';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Comprobante de contacto.')->view('maileclipse::templates.messageArrangeToCustomer')->to($this->customerEmail);
    }
}
