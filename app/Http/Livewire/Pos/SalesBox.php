<?php

namespace App\Http\Livewire\Pos;

use Livewire\Component;
use Prophecy\Promise\ThrowPromise;

class SalesBox extends Component
{
    public $saleBox;
    public $isSaleBoxOpen = false;
    public $openSaleBoxModal = false;
    public $seller;
    public $branch_id;
    public $opening_amount;
    public $closing_amount;
    public $remarks_open;
    public $remarks_close;

    protected $rules = [
        'branch_id' => 'required',
        'opening_amount' => 'required|numeric|max:99999999.99',
        'remarks_open' => 'nullable',
        'remarks_close' => 'nullable',
    ];

    protected $messages = [
        'required' => 'Este monto es obligatorio',
        'max' => 'Estás excediendo el límite de 8 dígitos',
        'int' => 'El monto debe ser número',
    ];

    protected $listeners = [
        'showBoxModal' => 'showSaleBoxModal'
    ];

    public function render()
    {
        return view('livewire.pos.sales-box');
    }

    public function validateSaleBox()
    {
        $this->saleBox = $this->seller->sales_boxes()->latest()->first();

        $this->isSaleBoxOpen = optional($this->saleBox)->is_opened ?? false;

        if (! $this->isSaleBoxOpen) {
            $this->showSaleBoxModal();
        } else {
            $this->emit('salesBoxUpdated', $this->saleBox->id);
        }
    }

    public function openSaleBox()
    {


        $this->validate();
        $this->saleBox = $this->seller->sales_boxes()->create([
            'branch_id' => $this->branch_id,
            'opening_amount' => $this->opening_amount,
            'remarks_open' => $this->remarks_open,
            'opened_at' => now(),
        ]);

        $this->isSaleBoxOpen = true;
        $this->opening_amount = null;
        $this->closing_amount = null;
        $this->remarks_open = null;
        $this->emit('salesBoxUpdated', $this->saleBox->id);
        $this->dispatchBrowserEvent('closeSaleBoxModal');
    }

    public function closeSaleBox()
    {
        $this->saleBox->closing_amount = $this->closing_amount;
        $this->saleBox->closed_at = now();
        $this->saleBox->closing_amount = $this->saleBox->calculateClosingAmount();
        $this->saleBox->save();
        $this->isSaleBoxOpen = false;
        $this->emit('salesBoxUpdated');
        $this->dispatchBrowserEvent('closeSaleBoxModal');
    }

    public function showSaleBoxModal()
    {
        $this->dispatchBrowserEvent('openSaleBoxModal');
    }

    public function getClosingAmount()
    {
        $this->saleBox = $this->saleBox->refresh();
        dd($this->saleBox->getClosingAmount());
    }
}
