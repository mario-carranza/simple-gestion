<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Mail\PosBill;
use App\Models\Product;
use App\Models\Quotation;
use Illuminate\Http\Request;
use App\Services\DTE\DTEService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\InvoiceRequest;
use Illuminate\Support\Facades\Cache;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Models\{Invoice, InvoiceType, CustomerAddress, Payments};

/**
 * Class InvoiceCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ManageInvoiceCrudController extends CrudController
{
    const RETRY_SEND_DOCUMENT = 300;

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
            //\Alert::add('success', 'Se ha enviado el documento con éxito')->flash();
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
        $fileName = rutWithoutDV($invoice->company->uid) . '_T' . $invoice->invoice_type->code;

        if (isset($invoice->folio) && !empty($invoice->folio)) {
            $fileName .= 'F' . $invoice->folio;
        }

        return response()->make($blob, 200, [
            'Content-Type' =>  'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $fileName . '.pdf"'
        ]);
    }

    public function getPDF(Request $request, Invoice $invoice)
    {
        if (!isset($invoice->dte_code)) {
            return redirect('index');
        }

        $tipoPapel = $request->input('tipoPapel') ?? 0;

        $service = new DTEService();

        if (isset($invoice->folio)) {
            $response = $service->getRealPDF($invoice, $tipoPapel);
        } else {
            $response = $service->getTemporalPDF($invoice, $tipoPapel);
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

    /**
     * This method is called from the Invoices CRUD
     * 
     */
    public function createRealDocument(Request $request, Invoice $invoice)
    {
        if (!isset($invoice->dte_code)) {
            return redirect()->action([self::class, 'index'], ['invoice' => $invoice]);
        }

        if (Cache::has('dte_document_' . $invoice->id)) {
            \Log::info('El usuario intentó emitir un documento otra vez. IdDoc: ' . $invoice->id . ' usuario ' . backpack_user()->id);
            \Alert::add('warning', 'Parece que ya intentó enviar el documento. Debes esperar 5 minutos antes de solicitar nuevamente la emision de este documento')->flash();
            return redirect()->action([self::class, 'index'], ['invoice' => $invoice]);
        }

        Cache::put('dte_document_' . $invoice->id, 'sending', self::RETRY_SEND_DOCUMENT);

        // Check inventory of items
        if ($invoice->invoice_type->code != 61 && $invoice->impact_inventory) {
            foreach($invoice->invoice_items as $item) {
                if ($item['product_id']) {
                    $product = Product::find($item['product_id']);
                    if (!$product->haveSufficientQuantity($item['qty'], 'Invoice', $invoice->id)) {
                        \Alert::add('danger', 'No tienes suficiente stock del producto "' . $product->name .'"')->flash();
                        return redirect()->action([self::class, 'index'], ['invoice' => $invoice]);
                    }
                }
            }
        }

        $service = new DTEService();

        // Check if emisor have folios. "disponibles >0 "
        if (!$service->foliosAvailables($invoice)) {
            \Alert::add('warning', 'No tienes folios disponibles para este tipo de documento')->flash();
            return redirect()->action([self::class, 'index'], ['invoice' => $invoice]);
        }

        $folioService = $service->getFolioMaintainerStatus($invoice->invoice_type->code, $invoice->company->uid);
        $folio = 0;

        if ($folioService->getStatusCode() === 200) {
            $contentResponse = json_decode($folioService->getBody()->getContents(), true);
            if (isset($contentResponse) && array_key_exists('siguiente', $contentResponse)) {
                $folio = $contentResponse['siguiente'];
            }
        }

        if ($folio === 0) {
            \Alert::add('warning', 'Hubo un problema al obtener el folio. Intente nuevamente.')->flash();
            \Log::error('Se trató de obtener el folio siguiente pero el servicio falló.');
            return redirect()->route('dte_documents.index');
        }

        $response = $service->generateDTE($invoice);

        if ($response->getStatusCode() === 200) {
            $contentResponse = $response->getBody()->getContents();
            $contentResponse = json_decode($contentResponse, true);

            if (
                isset($contentResponse) && 
                is_array($contentResponse) && 
                array_key_exists('folio', $contentResponse)
            ) {
                $invoice->folio = $contentResponse['folio'];
            } else {
                $invoice->folio = $folio;
                \Log::warning('La respuesta de LibreDTE no trajo el folio. Se asignará el folio ' . $folio . ' obtenido de una consulta previa');
            }

            $invoice->invoice_status = Invoice::STATUS_SEND;
            $invoice->updateWithoutEvents();

            // Reduce inventory
            if ($invoice->impact_inventory) {
                try {
                    if ($invoice->invoice_type->code == 61) {
                        $invoice->incrementInventoryOfItems();
                    } else {
                        $invoice->reduceInventoryOfItems();
                    }
                } catch (Exception $exception) {
                    \Log::error('Ocurrio un error actualizando el inventario: ' . $exception->getMessage());
                    \Alert::add('warning', 'Ocurrio un problema al actualizar el stock de productos')->flash();
                }
            }

            if ($invoice->invoice_status === Invoice::STATUS_SEND && $invoice->way_to_payment === 2) {
                $payment = Payments::insertDataInvoices($invoice);
            }

            if (!empty($invoice->json_value['quotation_id'])) {
                $quotation = Quotation::find($invoice->json_value['quotation_id']);
                if ($quotation) {
                    $quotation->quotation_status = Quotation::STATUS_INVOICED;
                    $quotation->updateWithoutEvents();
                }
            }


            if ($request->input('send_mail_pos') == 1) {
                Mail::to($invoice->email)->send(new PosBill($invoice));
            }
            
            \Alert::add('success', 'Documento enviado al SII')->flash();
            return redirect()->action([self::class, 'index'], ['invoice' => $invoice->id]);
        }

        \Alert::add('warning', 'Hubo algun problema al generar el documento.')->flash();
        return redirect()->action([self::class, 'index'], ['invoice' => $invoice]);

    }

    /**
     * This method if called from the POS
     * 
     */
    public function generateTemporalAndRealDocument(Request $request, Invoice $invoice)
    {
        $tipoPapel = $request->input('tipoPapel') ?? 0;

        if ($invoice->folio) {
            return redirect()->route('invoice.get-pdf', ['invoice' => $invoice, 'tipoPapel' => $tipoPapel]);
        }

        if (! isset($invoice->invoice_type)) {
            \Alert::add('danger', 'No ha determinado el tipo de documento')->flash();
            return redirect()->action([self::class, 'index'], ['invoice' => $invoice]);
        }

        if (Cache::has('dte_document_' . $invoice->id)) {
            \Log::info('El usuario intentó emitir un documento otra vez. IdDoc: ' . $invoice->id . ' usuario ' . backpack_user()->id);
            \Alert::add('warning', 'Parece que ya intentó enviar el documento. Debes esperar 5 minutos antes de solicitar nuevamente la emision de este documento')->flash();
            return redirect()->action([self::class, 'index'], ['invoice' => $invoice]);
        }

        Cache::put('dte_document_' . $invoice->id, 'sending', self::RETRY_SEND_DOCUMENT);

        // Check inventory of items
        if ($invoice->invoice_type->code != 61 && $invoice->impact_inventory) {
            foreach($invoice->invoice_items as $item) {
                if ($item['product_id']) {
                    $product = Product::find($item['product_id']);
                    if (!$product->haveSufficientQuantity($item['qty'], 'Invoice', $invoice->id)) {
                        \Alert::add('danger', 'No tienes suficiente stock del producto "' . $product->name .'"')->flash();
                        return redirect()->action([self::class, 'index'], ['invoice' => $invoice]);
                    }
                }
            }
        }
        
        $service = new DTEService();

        // Check if emisor have folios. "disponibles >0 "
        if (!$service->foliosAvailables($invoice)) {
            \Alert::add('warning', 'No hay folios disponibles')->flash();
            return redirect()->action([self::class, 'index'], ['invoice' => $invoice]);
        }

        if (!$invoice->dte_code) {
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

                \Alert::add('success', 'Se ha enviado el documento temporal con éxito')->flash();
            } else {
                \Alert::add('warning', 'Hubo un problema al enviar el documento')->flash();
                return redirect()->action([self::class, 'index'], ['invoice' => $invoice]);
            }
        }

        if (!isset($invoice->dte_code)) {
            return redirect()->action([self::class, 'index'], ['invoice' => $invoice]);
        }

        $folioService = $service->getFolioMaintainerStatus($invoice->invoice_type->code, $invoice->company->uid);
        $folio = 0;

        if ($folioService->getStatusCode() === 200) {
            $contentResponse = json_decode($folioService->getBody()->getContents(), true);
            if (isset($contentResponse) && array_key_exists('siguiente', $contentResponse)) {
                $folio = $contentResponse['siguiente'];
            }
        }

        if ($folio === 0) {
            \Alert::add('warning', 'Hubo un problema al obtener el folio. Intente nuevamente.')->flash();
            \Log::error('Se trató de obtener el folio siguiente pero el servicio falló.');
            return redirect()->route('dte_documents.index');
        }

        $response = $service->generateDTE($invoice);

        if ($response->getStatusCode() === 200) {
            $contentResponse = $response->getBody()->getContents();
            $contentResponse = json_decode($contentResponse, true);

            if (
                isset($contentResponse) && 
                is_array($contentResponse) && 
                array_key_exists('folio', $contentResponse)
            ) {
                $invoice->folio = $contentResponse['folio'];
            } else {
                $invoice->folio = $folio;
                \Log::warning('La respuesta de LibreDTE no trajo el folio. Se asignará el folio ' . $folio . ' obtenido de una consulta previa');
            }

            $invoice->invoice_status = Invoice::STATUS_SEND;
            $invoice->updateWithoutEvents();

            // Reduce inventory
            if ($invoice->impact_inventory) {
                try {
                    if ($invoice->invoice_type->code == 61) {
                        $invoice->incrementInventoryOfItems();
                    } else {
                        $invoice->reduceInventoryOfItems();
                    }
                } catch (Exception $exception) {
                    \Log::error('Ocurrio un error actualizando el inventario: ' . $exception->getMessage());
                    \Alert::add('warning', 'Ocurrio un problema al actualizar el stock de productos')->flash();
                }
            }

            if ($invoice->invoice_status === Invoice::STATUS_SEND && $invoice->way_to_payment === 2) {
                $payment = Payments::insertDataInvoices($invoice);
            }

            if (!empty($invoice->json_value['quotation_id'])) {
                $quotation = Quotation::find($invoice->json_value['quotation_id']);
                if ($quotation) {
                    $quotation->quotation_status = Quotation::STATUS_INVOICED;
                    $quotation->updateWithoutEvents();
                }
            }

           return redirect()->route('invoice.get-pdf', ['invoice' => $invoice, 'tipoPapel' => $tipoPapel]);
        }

        \Alert::add('warning', 'Hubo algun problema al generar el documento.')->flash();
        return redirect()->action([self::class, 'index'], ['invoice' => $invoice]);
    }

    public function updateDteStatus(Request $request, Invoice $invoice)
    {
        $service = new DTEService();
        $response = $service->getDteUpdatedStatus($invoice);

        if ($response->getStatusCode() != 200) {
            \Alert::add('warning', 'Hubo algun problema al consultar el estado del documento. Intentalo mas tarde')->flash();
            return redirect()->action([self::class, 'index'], ['invoice' => $invoice]);
        }

        $dteStatusResponse =  json_decode($response->getBody()->getContents(), true);

        $dteStatus = [
            'track_id' => $dteStatusResponse["track_id"] ?? '',
            'revision_estado' =>  $dteStatusResponse["revision_estado"] ?? '',
            'revision_detalle' => $dteStatusResponse["revision_detalle"] ?? '',
        ];

        $invoice->dte_status = $dteStatus;

        $invoice->updateWithoutEvents();

        \Alert::add('success', 'Estado del documento actualizado correctamente.')->flash();
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
        $creditNote->dte_status = null;
        $creditNoteType = InvoiceType::whereCode('61')->first();
        $creditNote->invoice_type_id = $creditNoteType->id;
        $creditNote->invoice_date = null;
        $creditNote->references_json = json_encode([
            [
                'reference_type_document' => $invoice->invoice_type_id,
                'reference_folio' => $invoice->folio,
                'reference_date' => $invoice->invoice_date,
            ]
        ]);

        $creditNote->save();

        \Alert::success('Se creó una nota de crédito a partir del documento seleccionado')->flash();

        return redirect()->to('admin/invoice/' . $creditNote->id . '/edit');

    }

    public function issueDebitNote(Request $request, Invoice $invoice)
    {
        if (!isset($invoice->dte_code)) {
            return redirect()->action([self::class, 'index'], ['invoice' => $invoice]);
        }

        $debitNote = new Invoice($invoice->toArray());
        $debitNote->folio = null;
        $debitNote->dte_code = null;
        $debitNote->dte_status = null;
        $debitNoteType = InvoiceType::whereCode('56')->first();
        $debitNote->invoice_type_id = $debitNoteType->id;
        $debitNote->invoice_date = null;
        $debitNote->references_json = json_encode([
            [
                'reference_type_document' => $invoice->invoice_type_id,
                'reference_folio' => $invoice->folio,
                'reference_date' => $invoice->invoice_date,
                'reference_code' => 1
            ]
        ]);

        $debitNote->save();

        \Alert::success('Se creó una nota de débito a partir del documento seleccionado')->flash();

        return redirect()->to('admin/invoice/' . $debitNote->id . '/edit');

    }

    
}
