<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\InvoiceRequest;
use Illuminate\Http\Request;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Models\{Invoice, InvoiceType, CustomerAddress, Payments};
use App\Services\DTE\DTEService;
use Illuminate\Support\Facades\Gate;

/**
 * Class InvoiceCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ManageInvoiceCrudController extends CrudController
{
    public function index(Invoice $invoice)
    {
        //@todo check permissions
        Gate::authorize('manageInvoice', $invoice); 
            
        return view('invoice.manage_invoice.index', compact('invoice'));
    }

    public function sendTemporaryDocument(Request $request, Invoice $invoice)
    {
        if (! isset($invoice->invoice_type)) {
            \Alert::add('danger', 'No ha determinado el tipo de documento')->flash();
            return redirect()->action([self::class, 'index'], ['invoice' => $invoice]);
        }

        $service = new DTEService();
        $response = $service->tempDocument($invoice);

        if ($response->getStatusCode() === 200) {
            $content = json_decode($response->getBody()->getContents(), true);
            $code = array_key_exists('codigo', $content) ? $content['codigo'] : null;
            if (empty($content) || empty($code)) {
                //@todo problem with values ??? decimals in number_format
                \Alert::add('warning', 'Hubo un problema al enviar el documento')->flash();
                return redirect()->action([self::class, 'index'], ['invoice' => $invoice]);
            }
            $invoice->invoice_status = Invoice::STATUS_TEMPORAL;
            $invoice->dte_code = $code;
            $invoice->updateWithoutEvents();
            \Alert::add('success', 'Se ha enviado el documento con éxito')->flash();
        } else {
            \Alert::add('warning', 'Hubo un problema al enviar el documento')->flash();
        }

        return redirect()->action([self::class, 'index'], ['invoice' => $invoice]);
    }

    public function deleteTemporaryDocument(Request $request, Invoice $invoice)
    {
        $service = new DTEService();
        $response = $service->deleteTemporalDocument($invoice);

        if ($response->getStatusCode() === 200) {
            //@todo update status
            $invoice->toDraft();
            $invoice->updateWithoutEvents();
            \Alert::add('success', 'Se ha eliminado el documento temporal con éxito')->flash();
        } else {
            \Alert::add('warning', 'Hubo un problema al eliminar el documento')->flash();
        }

        return redirect()->action([self::class, 'index'], ['invoice' => $invoice]);
    }

    /**
     * @deprecated
     */
    public function getRealPDF(Request $request, Invoice $invoice)
    {
        if (! isset($invoice->folio) ) {
            \Alert::add('warning', 'Este no es un document emitido');
            return redirect()->action([self::class, 'index'], ['invoice' => $invoice]);
        }
        
        $service = new DTEService();
        $pdfContent = $response->getBody()->getContents();

        return $this->getResponsePDF($pdfContent, $invoice);

    }

    private function getResponsePDF($blob, Invoice $invoice)
    {
        return response()->make($blob, 200, [
            'Content-Type' =>  'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$invoice->dte_code . '.pdf"'
        ]);
    }

    public function getPDF(Request $request, Invoice $invoice)
    {
        if (!isset($invoice->dte_code)) {
            return redirect('index');
        }
        $service = new DTEService();
        if (isset($invoice->folio)) {
            $response = $service->getRealPDF($invoice);
        } else {
            $response = $service->getTemporalPDF($invoice);
        }

        $pdfContent = $response->getBody()->getContents();
        //@todo check

        return $this->getResponsePDF($pdfContent, $invoice);
        //\Alert::add('success', 'Descargar pdf')->flash();
        //return redirect()->action(
        //    [self::class, 'index'],
        //    ['invoice' => $invoice->id]
        //);
    }

    public function createRealDocument(Request $request, Invoice $invoice)
    {
        if (!isset($invoice->dte_code)) {
            return redirect()->action([self::class, 'index'], ['invoice' => $invoice]);
        }
        //check if emisor have folios. "disponibles >0 "

        $service = new DTEService();

        if (!$service->foliosAvailables($invoice)) {
            \Alert::add('warning', 'No hay folios disponibles')->flash();
            return redirect()->action([self::class, 'index'], ['invoice' => $invoice]);
        }

        $response = $service->generateDTE($invoice);
        if ($response->getStatusCode() === 200) {
            $contentResponse = $response->getBody()->getContents();
            $contentResponse = json_decode($contentResponse, true);

            if (array_key_exists('folio', $contentResponse)) {
                $invoice->folio = $contentResponse['folio'];
                $invoice->invoice_status = Invoice::STATUS_SEND;
            }
            
            $invoice->updateWithoutEvents();

            if ($invoice->invoice_status === Invoice::STATUS_SEND && $invoice->way_to_payment === 2) {
                $payment = Payments::insertDataInvoices($invoice);
            }
            #ddd($contentResponse, $response);
            return redirect()->action([self::class, 'index'], ['invoice' => $invoice->id]);
        }

        dd($response);
        \Alert::add('warning', 'Hubo algun problema al generar el documento.')->flash();
        return redirect()->action([self::class, 'index'], ['invoice' => $invoice]);

    }

    public function issueCreditNote(Request $request, Invoice $invoice)
    {
        if (!isset($invoice->dte_code)) {
            return redirect()->action([self::class, 'index'], ['invoice' => $invoice]);
        }

        $creditNote = new Invoice($invoice->toArray());
        $creditNote->folio = null;
        $creditNote->dte_code = null;
        $creditNoteType = InvoiceType::whereCode('61')->first();
        $creditNote->invoice_type_id = $creditNoteType->id;
        $creditNote->json_value = [
            'reference_type_document' => $invoice->invoice_type_id,
            'reference_folio' => $invoice->folio,
            'reference_date' => $invoice->invoice_date,
        ];

        $creditNote->save();

        \Alert::success('Se creó una nota de crédito a partir del documento seleccionado')->flash();

        return redirect()->to('admin/invoice/' . $creditNote->id . '/edit');
        
    }
}
