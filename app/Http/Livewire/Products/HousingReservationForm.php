<?php

namespace App\Http\Livewire\Products;

use Carbon\Carbon;
use Livewire\Component;
use Carbon\CarbonPeriod;
use Illuminate\Support\Str;
use App\Models\ProductReservation;
use App\Models\ProductReservations;

class HousingReservationForm extends Component
{
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

    protected $rules = [
        'name' => 'required',
        'email' => 'required|email',
        'adultsNumber' => 'required|numeric',
        'childrensNumber' => 'required|numeric',
        'cellphone' => 'required',
        'checkOutDate' => 'required|date|after:checkInDate',
        'checkInDate' => 'required|date',
    ];

    public function render()
    {
        return view('livewire.products.housing-reservation-form');
    }

    public function mount($product)
    {
        $this->step = 1;

        $this->product;

        $this->priceLabel = 'Calcular precio';

        $this->canMakeReservation = false;
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

    public function calculatePrice()
    {
        $this->price = 0;

        $this->priceLabel = 'Calcular precio';

        $this->canMakeReservation = false;

        $this->validate();

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

        $this->priceLabel = "$ " . currencyFormat($estimatePrice, 'CLP') .  " (recalcular)";

        $this->price = $estimatePrice;

        if ($this->price !== 0) $this->canMakeReservation = true;
    }

    public function makeReservation()
    {
        $this->validate();

        ProductReservation::create([
            'hash' => (string) Str::uuid(),
            'product_id' => $this->product->id,
            'adults_number' => $this->adultsNumber,
            'childrens_number' => $this->childrensNumber,
            'check_in_date' => $this->checkInDate,
            'check_out_date' => $this->checkOutDate,
            'name' => $this->name,
            'email' => $this->email,
            'cellphone' => $this->cellphone,
            'price' => $this->price,
            'type' => 'housing',
            'reservation_status' => ProductReservation::PENDING_STATUS,
            'customer_comment' => $this->comments,
            'customer_id' => auth()->user()->customer->id ?? null,
        ]);

        $this->step = 2;
    }
}
