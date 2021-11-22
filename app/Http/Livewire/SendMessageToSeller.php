<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Models\Seller;
use App\Mail\SendMessageArrangeToSeller;
use App\Mail\SendMessageArrangeToCustomer;

class SendMessageToSeller extends Component
{
    public $enable;
    public $seller;
    public $order;

    protected $rules = [
        'order.arrange_messages.*.message' => 'required|string|min:6|max:500',
    ];

    protected $messages = [
        'order.arrange_messages.*.message.required' => 'El mensaje es requerido para enviar',
        'order.arrange_messages.*.message.min' => 'Debe contener al menos 6 caracteres',
        'order.arrange_messages.*.message.max' => 'Se ha superado el límite del mensaje (500 caracteres)',
    ];

    protected $validationAttributes = [
        'order.arrange_messages.*.message' => 'Mensaje'
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function mount()
    {
        $sellerMessages = is_array($this->order->arrange_messages) ? 
            $this->order->arrange_messages : 
            json_decode($this->order->arrange_messages, true);
        $sellerMessages = $sellerMessages[$this->seller->id]['send'] ?? null;
        $this->enable = $sellerMessages !== true ?? true;
    }

    public function send()
    {
        $validatedData = $this->validate();
        try {
            \Mail::send(new SendMessageArrangeToSeller($this->seller, $this->order));
            \Mail::send(new SendMessageArrangeToCustomer($this->order->email, $this->seller, $this->order));
            sleep(1);
            $sellerMessage = is_array($this->order->arrange_messages) ? $this->order->arrange_messages: json_decode($this->order->arrange_messages, true);
            $sellerMessage[$this->seller->id]['send'] = true;
            $this->order->arrange_messages = $sellerMessage;
            $this->order->updateWithoutEvents();
            $this->enable = false;
        } catch(\Exception $e) {
            $this->enable = 'error';
            \Log::error($e);
        }
    }

    public function render()
    {
        return view('livewire.send-message-to-seller');
    }
}
