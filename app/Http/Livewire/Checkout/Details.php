<?php

namespace App\Http\Livewire\Checkout;

use App\Rules\RutRule;
use Livewire\Component;
use App\Rules\PhoneRule;

class Details extends Component
{

    public $data;
    public $invoice;
    public $cart;
    public $is_business;
    public $anotherDataInvoice;
    public $communes;
    public $invoiceCommunes;
    protected $listeners = [
        'details:save' => 'save'
    ];
    protected $rules = [

        //'business_name' => '',
        'data.address_street' => 'required|min:3',
        'data.address_number' => 'required|numeric',
        'data.address_commune_id' => 'required',
        'data.address_region_id' => 'required',
        'data.email' => 'required|email',
        'data.cellphone' => 'required',
        'data.receiver_name' => 'max:255',
        'data.shipping_details' => 'max:500'
    ];


    protected $messages = [
        'required' => 'Es necesario completar este campo',
        'email' => 'Revise la dirección de email',
        'exists' => 'Cuidado, ha ingresado un valor no válido',
        'min' => 'El mínimo es de 3 caracteres.',
        'numeric' => 'El valor ingresado no es numérico.',
        'max' => 'El máximo es de :max caracteres.',

    ];



    public function mount()
    {
        $this->is_business = $this->cart->is_company != 0;
        $this->data = [
            'uid' => $this->cart->uid,
            'first_name' => $this->cart->first_name,
            'last_name' => $this->cart->last_name,
            'phone' => $this->cart->phone,
            'cellphone' => $this->cart->cellphone,
            'email' => $this->cart->email,
            'address_commune_id' => $this->cart->address_commune_id,
            'address_region_id' => $this->cart->address_region_id,
            'address_street' => $this->cart->address_street,
            'address_number' => $this->cart->address_number,
            'address_office' => $this->cart->address_office,
            'business_name' => $this->cart->business_name,
            'receiver_name' => $this->cart->receiver_name,
            'shipping_details' => $this->cart->shipping_details
        ];



        $invoice = [];
        //this variable can be simplified @todo
        //example is_business invoice
        $this->anotherDataInvoice = false;

        if ($this->cart->invoice_value) {
            $invoice = json_decode($this->cart->invoice_value, true);
            if (array_key_exists('status', $invoice)) {
                $this->anotherDataInvoice = $invoice['status'];
            }
        }

        $this->invoice = [
            'status' => $this->anotherDataInvoice,
            'uid' => array_key_exists('uid', $invoice) ? $invoice['uid'] : '',
            'first_name' =>  array_key_exists('first_name', $invoice) ? $invoice['first_name'] : '',
            'last_name' =>  array_key_exists('last_name', $invoice) ? $invoice['last_name'] : '',
            'phone' =>  array_key_exists('phone', $invoice) ? $invoice['phone'] : '',
            'cellphone' =>  array_key_exists('cellphone', $invoice) ? $invoice['cellphone'] : '',
            'email' =>  array_key_exists('email', $invoice) ? $invoice['email'] : '',
            'address_commune_id' =>  array_key_exists('address_commune_id', $invoice) ? $invoice['address_commune_id'] : '',
            'address_region_id' =>  array_key_exists('address_region_id', $invoice) ? $invoice['address_region_id'] : '',
            'address_street' =>  array_key_exists('address_street', $invoice) ? $invoice['address_street'] : '',
            'address_number' =>  array_key_exists('address_number', $invoice) ? $invoice['address_number'] : '',
            'address_office' =>  array_key_exists('address_office', $invoice) ? $invoice['address_office'] : '',
            'business_activity_id' => array_key_exists('business_activity_id', $invoice) ? $invoice['business_activity_id'] : '',
            'is_business' => array_key_exists('is_business', $invoice) ? $invoice['is_business'] : false,
            'business_name' => array_key_exists('business_name', $invoice) ? $invoice['business_name'] : '',
        ];
    }

    public function render()
    {
        if (!empty($this->data['address_region_id'])) {
            $region = $this->data['address_region_id'];
            $this->communes = \App\Models\Commune::whereHas('attribute_province', function($q) use($region) {
                $q->where('region_id', $region);
            })->orderBy('name')->get();
        }
        if (!empty($this->invoice['address_region_id'])) {
            $region = $this->invoice['address_region_id'];
            $this->invoiceCommunes = \App\Models\Commune::whereHas('attribute_province', function($q) use($region) {
                $q->where('region_id', $region);
            })->orderBy('name')->get();
        }
        return view('livewire.checkout.details');
    }

    public function updated($propertyName)
    {
        $dynamicRules = $this->getCustomRules();
        $this->rules = array_merge($this->rules, $dynamicRules);
        $this->rules = array_merge($this->rules, [
            'data.uid' => ['required', new RutRule()],
            'data.cellphone' => ['required', new PhoneRule('El número ingresado no es válido')],
            'data.phone' => new PhoneRule('El número ingresado no es válido'),
        ]);
        $this->validateOnly($propertyName);
    }

    private function getCustomRules()
    {
        $dynamicRules = [
            'data.first_name' => 'required|min:3',
            'data.last_name' => 'required|min:3',
        ];

        if ($this->is_business) {
            $dynamicRules = [
                'data.business_name' => 'required|min:3',
                //'data.business_activity' => ''
            ];
        }

        if ($this->anotherDataInvoice) {
            $dynamicRules = array_merge($dynamicRules, [
                'invoice.uid' => ['required', new RutRule()],
                'invoice.first_name' => 'required|min:3',
                'invoice.last_name' => 'required|min:3',
                'invoice.phone' => new PhoneRule('El número ingresado no es válido'),
                'invoice.cellphone' => ['required',new PhoneRule('El número ingresado no es válido')],
                'invoice.email' => 'required|email',
                'invoice.address_street' => 'required',
                'invoice.address_number' => 'required|numeric',
                'invoice.address_street' => 'required',
                'invoice.address_commune_id' => 'required|exists:communes,id',
                'invoice.address_region_id' => 'required|exists:regions,id',
                // 'invoice.business_activity_id' => 'required|exists:business_activities,id',
                // 'invoice.business_name' => 'required|min:3',
            ]);

            if($this->invoice['is_business']){
                $dynamicRules = array_merge($dynamicRules, [
                    'invoice.business_activity_id' => 'required|exists:business_activities,id',
                    'invoice.business_name' => 'required|min:3',
                ]);
            }
        }

        return $dynamicRules;
    }

    // public function prevStep()
    // {
    //     $this->emitUp('prev-step');
    // }
    // public function nextStep()
    // {
    //     $this->emitUp('next-step');
    // }

    public function save()
    {



        $dynamicRules = $this->getCustomRules();

        $this->rules = array_merge($this->rules,$dynamicRules);
        try {
            $validatedData = $this->validate();
            $this->invoice['status'] = $this->anotherDataInvoice;
            $this->data['invoice_value'] = json_encode($this->invoice);

            $this->data['is_company'] = $this->is_business;

            $this->cart->update($this->data);

            $this->emitUp('finishTask');

        } catch (\Throwable $th){ // (\Throwable $th) {
            $this->emitUp('notFinishTask');
            throw $th;



        }





    }
}
