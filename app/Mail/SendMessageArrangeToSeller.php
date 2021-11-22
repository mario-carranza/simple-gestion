<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;
use App\Models\Seller;

class SendMessageArrangeToSeller extends Mailable
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
    public $sellerMessage;

    public function __construct(Seller $seller, Order $order)
    {
        $this->seller = $seller;
        $this->order = $order;

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
        return $this->subject('Un cliente ha dejado un mensaje para acordar envío.')->view('maileclipse::templates.messageArrangeToSeller')->to($this->seller->email);
    }
}
