<?php

namespace App\Services\DTE;

use App\Models\Invoice;
use App\Services\DTE\Types\{
    ElectronicInvoice, 
    ExemptElectronicInvoice,
    ElectronicTicket,
    CreditNote,
    DebitNote,
    ExemptElectronicTicket
};

class DTEFactory
{
    public static function init(int $type, Invoice $invoice)
    {
        switch ($type) {
            case 33:
                return new ElectronicInvoice($invoice);
                break;
            case 34:
                return new ExemptElectronicInvoice($invoice);
                break;
            case 39:
                return new ElectronicTicket($invoice);
                break;
            case 41:
                return new ExemptElectronicTicket($invoice);
                break;
            case 61;
                return new CreditNote($invoice);
                break;
            case 56;
                return new DebitNote($invoice);
                break;
            default:
                throw new \Exception("No se puede crear el tipo de documento porque no está disponible.");
        }
    }
}
