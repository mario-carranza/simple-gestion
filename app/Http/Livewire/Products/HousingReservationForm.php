<?php

namespace App\Http\Livewire\Products;

use Carbon\Carbon;
use Livewire\Component;
use Carbon\CarbonPeriod;
use Illuminate\Support\Str;
use App\Models\ProductReservation;
use App\Models\ProductReservations;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\ProductReservationCreated;
use Backpack\Settings\app\Models\Setting;

class HousingReservationForm extends Component
{
    protected $listeners = [
        'housing:make-reservation' => 'makeReservation',
        'housing:calculateHours' => 'calculteTourAvailableHours',
        'housing:resetCalculation' => 'resetCalculation',
    ];

    public $step;
    public $product;
    public $checkInDate;
    public $checkOutDate;
    public $name;
    public $email;
    public $cellphone;
    public $childrensNumber;
    public $adultsNumber;
    public $priceLabel;
    public $price;
    public $canMakeReservation;
    public $comments;
    public $tourDate;
    public $tourHour;
    public $disabledDays;
    public $availableHours = [];
    public $tourPricingData = [];
 
    const daysMapping = [
        0 => 1,
        1 => 2,
        2 => 3,
        3 => 4,
        4 => 5,
        5 => 6,
        6 => 0,
    ];

    private function getRulesValidations() 
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|email',
            'adultsNumber' => 'required|numeric',
            'childrensNumber' => 'required|numeric',
            'cellphone' => 'required',
        ];

        if ($this->product->is_housing) {
            $rules['checkOutDate'] = 'required|date|after:checkInDate';
            $rules['checkInDate'] = 'required|date';
        }

        if ($this->product->is_tour) {
            $rules['tourDate'] = 'required|date_format:d/m/Y';
            $rules['tourHour'] = 'required|min:1';
        }

        return $rules;
    }

    public function render()
    {
        return view('livewire.products.housing-reservation-form');
    }

    public function mount($product)
    {
        $this->step = 1;

        $this->product = $product;

        $this->priceLabel = 'Calcular precio';

        $this->canMakeReservation = false;

        if ($this->product->is_tour) {
            $this->disabledDays = collect($product->tour_information)->map(function ($item) {
                return (int) self::daysMapping[$item['day']];
            })->values();
            
            $this->disabledDays = collect([0,1,2,3,4,5,6])->diff($this->disabledDays->unique())->values()->toArray();
        }
    }

    public function initModal()
    {
        $this->step = 1;

        $this->childrensNumber = 0;
        $this->adultsNumber = 0;
        $this->price = null;
        $this->priceLabel = 'Calcular';
        $this->name = null;
        $this->email = null;
        $this->cellphone = null;
        $this->checkInDate = null;
        $this->checkOutDate = null;
        $this->comments = null;
        $this->canMakeReservation = false;
    }

    public function calculteTourAvailableHours()
    {
        if (! $this->tourDate) return false;

        $dayNumber = Carbon::createFromFormat('d/m/Y', $this->tourDate)->dayOfWeekIso - 1;

        $this->availableHours = collect($this->product->tour_information)
                            ->where('day', $dayNumber)
                            ->mapWithKeys(function ($item) {
                                return [
                                    $item['hour'] => Carbon::parse($item['hour'])->format('h:i a ')
                                ];
                            });
    }

    public function resetCalculation()
    {
        $this->canMakeReservation = false;
        $this->priceLabel = 'Calcular precio';
        $this->price = null;

        if ($this->tourDate && $this->tourHour) {
            $this->setTourPricingData();
        }
    }

    public function calculatePrice()
    {
        $this->price = 0;

        $this->priceLabel = 'Calcular precio';

        $this->canMakeReservation = false;

        $this->validate($this->getRulesValidations());

        if ($this->product->is_housing) {
            $estimatePrice = $this->calculateHousingPrice();
        }  else if ($this->product->is_tour) {
            $estimatePrice = $this->calculateTourPrice();
        }

        $this->priceLabel = "$ " . currencyFormat($estimatePrice, 'CLP') .  " (recalcular)";

        $this->price = $estimatePrice;

        if ($this->price !== 0) $this->canMakeReservation = true;
    }

    public function calculateHousingPrice() : float
    {
        $checkInDate = Carbon::parse($this->checkInDate)->midDay();

        $checkOutDate = Carbon::parse($this->checkOutDate)->midDay();

        $datePeriod = new CarbonPeriod($checkInDate, '1 days', $checkOutDate);
        
        $pricingData = collect($this->product->housing_pricing);

        $estimatePrice = 0;

        foreach ($datePeriod as $i => $day) {
            if ($i === $datePeriod->count() - 1) continue;

            $dayNumber = $day->dayOfWeekIso - 1;

            $pricingDay = $pricingData->where('day', $dayNumber)->first();

            $estimatePrice += $pricingDay['price_per_night'];
        }

        return $estimatePrice;
    }

    /**
     * @deprecated not in used
     */
    public function calculateHousingPricePerPerson() : float
    {
        $checkInDate = Carbon::parse($this->checkInDate)->midDay();

        $checkOutDate = Carbon::parse($this->checkOutDate)->midDay();

        $datePeriod = new CarbonPeriod($checkInDate, '1 days', $checkOutDate);
        
        $pricingData = collect($this->product->housing_pricing);

        $estimatePrice = 0;

        foreach ($datePeriod as $i => $day) {
            if ($i === $datePeriod->count() - 1) continue;

            $dayNumber = $day->dayOfWeekIso - 1;

            $pricingDay = $pricingData->where('day', $dayNumber)->first();

            $adultsPrice = $pricingDay['adults_price'] * $this->adultsNumber;

            $childrensPrice = $pricingDay['childrens_price'] * $this->childrensNumber;

            $estimatePrice += $adultsPrice + $childrensPrice;
        }

        return $estimatePrice;
    }

    public function calculateTourPrice() : float
    {
        $estimatePrice = 0;

        $this->setTourPricingData();

        $estimatePrice += ($this->adultsNumber * $this->tourPricingData['adults_price']);

        $estimatePrice += ($this->childrensNumber * $this->tourPricingData['childrens_price']);

        return $estimatePrice;
    }

    public function setTourPricingData()
    {
        if (!$this->tourHour || !$this->tourDate) {
            $this->tourPricingData = null;
            return false;
        }

        $dayNumber = Carbon::createFromFormat('d/m/Y', $this->tourDate)->dayOfWeekIso - 1;
        
        $this->tourPricingData = collect($this->product->tour_information)
                            ->where('hour', $this->tourHour)
                            ->where('day', $dayNumber)
                            ->first();
    }

    public function makeReservationEvent()
    {
        $this->canMakeReservation = false;

        $this->emit('housing:make-reservation');
    }

    public function makeReservation()
    {
        $this->canMakeReservation = false;
        
        $this->validate($this->getRulesValidations());

        if ($this->product->is_housing) {
            $checkInDate = $this->checkInDate;
            $checkOutDate = $this->checkOutDate;
            $type = 'housing';
        } else if ($this->product->is_tour) {
            $tourDate = Carbon::createFromFormat('d/m/Y', $this->tourDate);
            $checkInDate = $tourDate->format('Y-m-d')  . ' ' . $this->tourHour;
            $checkOutDate = $tourDate->format('Y-m-d')  . ' ' . $this->tourHour;
            $type = 'tour';
        }

        $productReservation = ProductReservation::create([
            'hash' => (string) Str::uuid(),
            'product_id' => $this->product->id,
            'adults_number' => $this->adultsNumber,
            'childrens_number' => $this->childrensNumber,
            'check_in_date' => $checkInDate,
            'check_out_date' => $checkOutDate,
            'name' => $this->name,
            'email' => $this->email,
            'cellphone' => $this->cellphone,
            'price' => $this->price,
            'type' => $type,
            'reservation_status' => ProductReservation::PENDING_STATUS,
            'customer_comment' => $this->comments,
            'customer_id' => auth()->user()->customer->id ?? null,
        ]);

        try {
            Mail::to($this->product->seller->email)->send(new ProductReservationCreated($productReservation, 'seller'));
            
            Mail::to($this->email)->send(new ProductReservationCreated($productReservation, 'customer'));
            
            $administrators = Setting::get('administrator_email');
            
            $recipients = explode(';', $administrators);
            
            foreach ($recipients as $key => $recipient) {
                Mail::to($recipient)->send(new ProductReservationCreated($productReservation, 'admin'));
            }
        } catch (\Throwable $th) {
            Log::error('No se puedo enviar el correo', [
                'error' => $th->getMessage(),
                'stacktrace' => $th->getTraceAsString(),
            ]);
        }

        $this->step = 2;
    }
}
