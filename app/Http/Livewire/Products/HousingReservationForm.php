<?php

namespace App\Http\Livewire\Products;

use Carbon\Carbon;
use Livewire\Component;
use Carbon\CarbonPeriod;

class HousingReservationForm extends Component
{
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

    protected $rules = [
        'name' => 'required',
        'email' => 'required|email',
        'adultsNumber' => 'required|numeric',
        'childrensNumber' => 'required|numeric',
        'cellphone' => 'required',
        'checkOutDate' => 'required',
        'checkInDate' => 'required',
    ];

    public function render()
    {
        return view('livewire.products.housing-reservation-form');
    }

    public function mount($product)
    {
        $this->product;

        $this->priceLabel = 'Calcular precio';

        $this->canMakeReservation = false;
    }

    public function calculatePrice()
    {
        $this->validate();

        $checkInDate = Carbon::parse($this->checkInDate)->midDay();

        $checkOutDate = Carbon::parse($this->checkOutDate)->midDay();

        $datePeriod = new CarbonPeriod($checkInDate, '1 days', $checkOutDate);
        
        $pricingData = collect($this->product->parent->housing_pricing);

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
}
