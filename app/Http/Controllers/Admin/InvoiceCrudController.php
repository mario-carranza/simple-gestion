<?php

namespace App\Http\Controllers\Admin;

use App\Models\Customer;
use App\Cruds\BaseCrudFields;
use Illuminate\Http\Request;
use App\Services\DTE\DTEService;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DteSalesReportExport;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use App\Http\Requests\{ InvoiceRequest, DteSalesReport };
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Models\{Tax, Invoice, InvoiceType, CustomerAddress, Seller, Company};
use Carbon\Carbon;

/**
 * Class InvoiceCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class InvoiceCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    protected $emitter;
    protected $seller;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Invoice::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/invoice');
        CRUD::setEntityNameStrings('documento electrónico', 'documentos electrónicos');
        $this->crud->enableExportButtons();
        
        $this->seller = Seller::where('user_id', backpack_user()->id);
        if ($this->seller->exists()) {
            $this->seller = $this->seller->first();
            if (! backpack_user()->can('showAllInvoices')) {
                $this->crud->addClause('where', 'seller_id', $this->seller->id);
            }
            if ($this->seller->is_approved != Seller::STATUS_ACTIVE) {
                $this->crud->denyAccess(['create', 'update', 'delete']);
            }
        } else {
            $this->seller = null;
        }
        
        $company = backpack_user()->current()->company->id; 
        $company = Company::find($company);
        $this->emitter = $company;
        // use scope $this->crud->addClause('where', 'company_id', $company->id);

        $this->crud->denyAccess('show');

        // if dte is real, deny delete
        if ($this->crud->getCurrentOperation() === 'delete' && $this->crud->getCurrentEntry()->invoice_status === Invoice::STATUS_SEND) {
            $this->crud->denyAccess(['delete']);
        }
    }

    protected function setupDteSalesReportRoutes($segment, $routeName, $controller)
    {
        \Route::get($segment.'/dte_sales_report', [
            'as'        => $routeName.'.getDteSalesReport',
            'uses'      => $controller.'@getDteSalesReportForm',
            'operation' => 'salesreport',
        ]);

        \Route::post($segment.'/dte_sales_report', [
            'as'        => $routeName.'.postDteSalesReport',
            'uses'      => $controller.'@postDteSalesReportForm',
            'operation' => 'salesreport',
        ]);
    }

    public function getDteSalesReportForm()
    {
        $this->crud->setOperation('Salesreport');
        $this->data['crud'] = $this->crud;
        $this->data['title'] = 'Generar reporte de ventas';
        return view('vendor.backpack.reports.dte_sales_report', $this->data);
    }

    public function postDteSalesReportForm(DteSalesReport $request )
    {
        $company = backpack_user()->current()->company->id;
        $company = Company::find($company);
        $uid = $company->uid;

        $dte = new DTEService();
        $response = $dte->getSalesReport($uid, $request->period_year . $request->period_month);
        
        if ($response->getStatusCode() === 200) {
            $salesReport = json_decode($response->getBody()->getContents(), true);

            $documentsWCN = \Arr::where($salesReport, function ($value, $key) {
                return $value['dte'] != 61;
            });

            $creditNotes = \Arr::where($salesReport, function ($value, $key) {
                return $value['dte'] === 61;
            });

            $array = [];

            foreach ($creditNotes as $document) {

                $moreData = $dte->getDataEmittedDocumentUnstructure($document['dte'], $document['folio'], $uid);
                if ($moreData->getStatusCode() !== 200) {
                    \Alert::warning('Lo sentimos, no se pudo generar el reporte')->flas();
                    return \Redirect::to($this->crud->route . '/dte_sales_report');
                }
                $response = $moreData->getBody()->getContents();
                $moreData = json_decode($response, true);
                $moreData = $moreData['datos_dte']['Referencia'];

                $documentsWCN = \Arr::where($documentsWCN, function ($value, $key) use ($moreData) {
                    return $value['folio'] != $moreData['FolioRef'] && $value['dte'] == $moreData['TpoDocRef'];
                });
            }

            foreach ($documentsWCN as &$document) {
                $moreData = $dte->getDataEmittedDocumentUnstructure($document['dte'], $document['folio'], $uid);
                if ($moreData->getStatusCode() !== 200) {
                    \Alert::warning('Lo sentimos, no se pudo generar el reporte')->flas();
                    return \Redirect::to($this->crud->route . '/dte_sales_report');
                }
                $response = $moreData->getBody()->getContents();
                $moreData = json_decode($response, true);
                $moreData = $moreData['datos_dte'];

                $totals = $moreData['Encabezado']['Totales'];
                $document['net'] = empty($totals['MntNeto']) ? $totals['MntExe'] : $totals['MntNeto']; 
                $document['tax'] = array_key_exists('IVA', $totals) ? $totals['IVA'] : ''; 

            }

            $report = new DteSalesReportExport($documentsWCN);
            return Excel::download($report, 'sales_report.xls');

        }

        \Alert::add('warning', 'Hubo un problema al generar el reporte ERR != 200')->flash();
        return redirect()->to($this->crud->route . '/dte_sales_report');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        //CRUD::setFromDb(); // columns
        
        // create temporary document
         
        $this->crud->addButtonFromView(
            'line', 'create_temporary_document', 
            'invoice.to_manage', 'beginning'
        );
        CRUD::addColumn([
            'name' => 'uid',
            'label' => 'RUT',
        ]);

        CRUD::addColumn([
            'name' => 'first_name',
            'label' => 'Nombre / Razón Soc.',
            'priority' => 2,
        ]);

        CRUD::addColumn([
            'name' => 'invoice_type',
            'type' => 'relationship',
            'label' => 'Tipo',
            'priority' => 3,
        ]);

        CRUD::addColumn([
            'name' => 'folio',
            'priority' => 4,
        ]);

        CRUD::addColumn([
            'label' => 'Fecha de emision',
            'name' => 'invoice_date',
            'type' => 'date',
            'format' => 'L',
            'priority' => 3,
        ]);

        CRUD::addColumn([
            'label' => 'Fecha de vencimiento',
            'name' => 'expiry_date',
            'type' => 'date',
            'format' => 'L',
            'priority' => 3,
        ]);

        CRUD::addColumn([
            'name' => 'total',
            'label' => 'Total',
            'priority' => 1,
            'type' => 'number',
            'prefix'        => '$',
            'decimals'      => 0,
            'thousands_sep' => ' ',
        ]);

        CRUD::addColumn([
            'name' => 'status_description',
            'label' => 'Estado',
            'priority' => 1,
            'wrapper' => [
                'element' => 'span',
                'class' => function ($crud, $column, $entry, $related_key) {
                    switch ($column['text']) {
                        case 'Borrador':
                            return 'badge badge-secondary';
                            break;
                        case 'Doc. Temporal':
                            return 'badge badge-info';
                            break;
                        case 'Doc. Enviado':
                            return 'badge badge-success';
                            break;
                        default:
                            return '';
                            break;
                    }
                },
            ],
        ]);

        $this->customFilters();


        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']); 
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(InvoiceRequest::class);

        //CRUD::setFromDb(); // fields

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number'])); 
         */
          
        $this->crud = (new BaseCrudFields())->setBaseFields($this->crud);

        $this->crud->setOperationSetting('saveAllInputsExcept', ['_token', '_method', 'http_referrer', 'current_tab', 'save_action']);

        CRUD::addField([
            'type' => 'custom_js_data',
            'name' => 'custom_data_for_invoice_type',
            'data' => InvoiceType::all()->toArray(),
            'variable_name' => 'invoiceTypeArray',
            'tab' => 'General',
        ]);
        
        CRUD::addField([
            'label' => 'Cliente',
            'name' => 'customer_id',
            'type' => 'relationship',
            'entity' => 'customer',
            'attribute' => 'full_name_with_raw_uid',
            'placeholder' => 'Selecciona un cliente',
            'wrapper' => [
                'class' => 'form-group col-md-6',
            ],
            'tab' => 'General',
        ]);

        CRUD::addField([
            'label' => 'Título',
            'name' => 'title',
            'wrapper' => [
                'class' => 'form-group col-md-6',
            ],
            'tab' => 'General',
        ]);

        /*
         * TO DO change method addresses to CustomerAddress
         */
        CRUD::addField([
            'label' => 'Dirección',
            'type' => 'select2_from_ajax',
            'name' => 'address_id',
            'entity' => 'address',
            'attribute' => 'addressDescription',
            'data_source' => url('admin/quotation/addresses'),
            'placeholder' => 'Selecciona una dirección',
            'minimum_input_length' => 0,
            'model' => CustomerAddress::class,
            'dependencies' => ['customer_id'],
            'method' => 'POST',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6',
            ],
            'include_all_form_fields' => true,
            'tab' => 'General',
        ]);
        
        CRUD::addField([
            'label' => 'Fecha de emisión',
            'name' => 'invoice_date',
            'type' => 'date',
            'default' => date("Y-m-d"),
            'wrapper' => [
                'class' => 'form-group col-md-3',
            ],
            'tab' => 'General',
        ]);

        CRUD::addField([
            'label' => 'Fecha vencimiento',
            'name' => 'expiry_date',
            'type' => 'date',
            'default' => Carbon::now()->addMonth(1)->format('Y-m-d'),
            'wrapper' => [
                'class' => 'form-group col-md-3',
            ],
            'tab' => 'General',
        ]);

        if (!empty($this->seller)) {
            //$sellerId = $this->seller->id;
            // set seller in observer
            CRUD::addField([
                'label' => 'Venedor',
                'name' => 'seller_name',
                'type' => 'text',
                'value' => $this->seller->name,
                'attributes' => [
                    'readonly' => true,
                ],
                'tab' => 'General',
                'wrapper' => [
                    'class' => 'form-group col-md-3',
                ],
            ]);
            /*CRUD::addField([
                'label' => 'Vendedor',
                'name' => 'seller_id',
                'type' => 'select2',
                'placeholder' => 'Selecciona un vendedor',
                'model' => 'App\Models\Seller',
                'attribute' => 'name',
                'value' => $sellerId, 
                'wrapper' => [
                    'class' => 'form-group col-md-3',
                ],
                'tab' => 'General',
                'options' => (function ($query) use($sellerId) {
                    return $query->where('id', $sellerId)->get();
                })
            ]);*/

        } else {
            CRUD::addField([
                'label' => 'Vendedor',
                'name' => 'seller_id',
                'type' => 'select2',
                'allows_null' => true,
                'placeholder' => 'Selecciona un vendedor',
                'model' => 'App\Models\Seller',
                'attribute' => 'name',
                'wrapper' => [
                    'class' => 'form-group col-md-3',
                ],
                'tab' => 'General',
            ]);
        }

        CRUD::addField([
            'type' => 'select2_from_array',
            'options' => InvoiceType::active()->pluck('name','id')->sort(),
            'attribute' => 'name',
            'name' => 'invoice_type_id',
            'allows_null' => true,
            'label' => 'Tipo de documento',
            'tab' => 'General',
            'wrapper' => [
                'class' => 'form-group col-md-3',
            ]
        ]);

        CRUD::addField([
            'name' => 'business_activity_id',
            'label' => 'Giro',
            'type' => 'relationship',
            'placeholder' => 'Seleccionar giro',
            'tab' => 'General',
            'wrapper' => [
                'class' => 'form-group col-md-3',
            ]
        ]);

        // get business activity
        CRUD::addField([
            'name' => 'customer_change_script',
            'type' => 'invoice.customer_change_script',
            'tab' => 'General',
        ]);

        CRUD::addField([
            'label' => 'Identificador de documento',
            'name' => 'dte_code',
            'type' => 'text',
            'prefix' => '#',
            'attributes' => [
                'disabled' => 'disabled',
                'readonly' => 'readonly',
            ],
            'wrapper' => [
                'class' => 'form-group col-md-3',
            ],
            'tab' => 'General',
        ]);

        CRUD::addField([
            'type' => 'text',
            'store_in' => 'json_value',
            'name' => 'quotation_id',
            'fake' => true,
            'wrapper' => [
                'style' => 'display:none',
            ],
            'tab' => 'General',
        ]);

        CRUD::addField([
            'label' => 'Productos o servicios',
            'name' => 'items_data',
            'type' => 'quotation.repeatable',
            'new_item_label' => 'Agregar producto / servicio',
            'fields' => [
                [
                    'label' => 'Exento',
                    'name' => 'ind_exe',
                    'type' => 'select2_from_array',
                    'default' => 0,
                    'allows_null' => false,
                    'options' => [
                        0 => 'No',
                        1 => 'Si',
                    ],
                    'attributes' => [
                        'class' => 'form-control is_exent',
                    ],
                    'wrapper' => [
                        'class' => 'form-group col-md-1',
                    ],
                ],
                [
                    'label' => 'Producto / Servicio',
                    'name' => 'product_id',
                    'type' => 'quotation.select2_custom',
                    'model' => 'App\Models\Product',
                    'placeholder' => 'Selecciona un producto',
                    'attribute' => 'name',
                    'data_source' => url('admin/api/products/get-by-current-company'),
                    'minimum_input_length' => 0,
                    'include_all_form_fields'  => true,
                    'dependencies'  => ['seller_id'],
                    'wrapper' => [
                        'class' => 'form-group col-md-3 product-select',
                    ],
                    'attributes' => [
                        'class' => 'form-control product-id-field'
                    ]
                ],
                [
                    'label' => 'Producto / Servicio',
                    'name' => 'name',
                    'type' => 'text',
                    'wrapper' => [
                        'class' => 'form-group col-md-3 custom-product-name',
                        'style' => 'display:none',
                    ],
                    'attributes' => [
                        'placeholder' => 'Nombre del producto o servicio',
                        'class' => 'form-control product-name-field'
                    ],
                ],
                [
                    'label' => 'Precio',
                    'name' => 'price',
                    'type' => 'text',
                    'attributes' => [
                        'class' => 'form-control price',
                    ],
                    'wrapper' => [
                        'class' => 'form-group col-md-2',
                    ],
                ],
                [
                    'label' => 'Cantidad',
                    'name' => 'qty',
                    'type' => 'number',
                    'attributes' => [
                        'class'=> 'form-control qty',
                    ],
                    'wrapper' => [
                        'class' => 'form-group col-md-1',
                    ],
                ],
                [
                    'label' => 'Descuento',
                    'name' => 'discount',
                    'type' => 'quotation.discount',
                    'default' => 0,
                    'suffix' => '<select class="form-control discount_type" name="discount_type"><option value="amount">$</option><option value="percentage">%</option></select>',
                    'wrapper' => [
                        'class' => 'form-group col-md-2',
                    ],
                ],
                [
                    'label' => 'Impuesto',
                    'name' => 'additional_tax_id',
                    'type' => 'select2_from_array',
                    'atributte' => 'name',
                    'options' => array_merge([0 => 'No aplica'] , Tax::all()->map( function($item) {
                        $item->name = $item->amount . '% - ' . $item->name;
                        return $item;
                    })->pluck('name', 'id')->toArray()),
                    'attributes' => [
                        'class' => 'form-control tax_id_field',
                    ],
                    'wrapper' => [
                        'class' => 'form-group col-md-2',
                    ],
                ],
                [
                    'label' => 'Total',
                    'name' => 'total',
                    'type' => 'text',
                    'default' => 0,
                    'attributes' => [
                        'class' => 'form-control subtotal',
                        'readonly' => true,
                    ],
                    'wrapper' => [
                        'class' => 'col-md-2',
                    ],
                ],
                [
                    'label' => 'Descripción',
                    'name' => 'description',
                    'type' => 'textarea',
                    'wrapper' => [
                        'class' => 'col-md-12 custom-description',
                        'style' => 'display:none',
                    ],
                ],
                [
                    'label' => 'Es un producto/servicio personalizado',
                    'name' => 'is_custom',
                    'type' => 'checkbox',
                    'attributes' => [
                        'class' => 'checkbox-is-custom',
                    ],
                    'wrapper' => [
                        'class' => 'form-group col-md-3',
                    ],
                ],
                [
                    'label' => 'Editar descripción',
                    'name' => 'edit_description',
                    'type' => 'checkbox',
                    'attributes' => [
                        'class' => 'checkbox-edit-description',
                    ],
                    'wrapper' => [
                        'class' => 'form-group col-md-2',
                    ],
                ],

                // Hidden inputs
                [
                    'label' => 'tax_amount',
                    'name' => 'additional_tax_amount',
                    'type' => 'hidden',
                    'attributes' => [
                        'class' => 'tax_amount_item'
                    ],
                ],
                [
                    'label' => 'tax_percent',
                    'name' => 'tax_percent',
                    'type' => 'hidden',
                    'attributes' => [
                        'class' => 'tax_percent_item'
                    ],
                ],
                [
                    'label' => 'tax_total',
                    'name' => 'additional_tax_total',
                    'type' => 'hidden',
                    'attributes' => [
                        'class' => 'tax_total_item'
                    ],
                ],
            ],
            'tab' => 'General',
        ]);

        CRUD::addField([
            'name' => 'check_group_start',
            'type' => 'group_start',
            'label' => '',
            'wrapperAttributes' => [
                'class' => 'col-md-6'
            ],
            'tab' => 'General',
        ]);

        /*CRUD::addField([
            'name' => 'tax_type',
            'label' => 'Impuesto',
            'type' => 'select2_from_array',
            'options' => ['A' => 'Afecta', 'E' => 'Exenta', 'H' => 'Honorarios'],
            'allows_null' => false,
            'default' => 'A',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-12',
            ],
            'tab' => 'General',
        ]);*/
        
        CRUD::addField([
            'name' => 'way_to_payment',
            'label' => 'Forma de pago',
            'type' => 'select2_from_array',
            'options' => ['1' => 'Contado', '2' => 'Crédito' ],
            'allows_null' => false,
            'default' => '1',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-4',
            ],
            'tab' => 'General',
        ]);

        CRUD::addField([
            'name' => 'payment_method',
            'label' => 'Medio de pago',
            'type' => 'select2_from_array',
            'options' => [
                'EF' => 'Efectivo', 
                'PE' => 'Pago a Cta. Cte.',
                'TC' => 'Tarjeta de crédito',
                'CF' => 'Cheque a fecha',
                'OT' => 'Otro'
            ],
            'allows_null' => true,
            'wrapperAttributes' => [
                'class' => 'form-group col-md-4',
            ],
            'tab' => 'General',
        ]);

        CRUD::addField([
            'label' => 'Tipo de cuenta',
            'name' => 'bank_account_type_id',
            'type' => 'select2',
            'placeholder' => 'Selecciona un banco',
            'model' => 'App\Models\BankAccountType',
            'attribute' => 'name',
            'wrapper' => [
                'class' => 'form-group col-md-6',
            ],
            'tab' => 'General',
        ]);
        
        CRUD::addField([
            'label' => 'Banco',
            'name' => 'bank_id',
            'type' => 'select2',
            'placeholder' => 'Selecciona un banco',
            'model' => 'App\Models\Bank',
            'attribute' => 'name',
            'wrapper' => [
                'class' => 'form-group col-md-6',
            ],
            'tab' => 'General',
        ]);

        CRUD::addField([
            'name' => 'bank_number_account',
            'label' => 'Número de cuenta',
            'type' => 'text',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6',
            ],
            'tab' => 'General',
        ]);

        /*CRUD::addField([
            'name' => 'include_payment_data',
            'label' => 'Incluir datos de pago',
            'type' => 'checkbox',
            'default' => true,
            'wrapperAttributes' => [
                'class' => 'form-group col-md-12'
            ],
            'tab' => 'General',
        ]);*/

        CRUD::addField([
            'name' => 'notes',
            'label' => 'Observaciones / Términos del pago',
            'type' => 'textarea',
            'wrapper' => [
                'class' => 'form-group col-md-12',
            ],
            'tab' => 'General',
        ]);


        CRUD::addField([
            'name' => 'check_group_end',
            'type' => 'group_end',
            'tab' => 'General',
        ]);

        CRUD::addField([
            'type' => 'invoice.totals_card',
            'name' => 'totals_card',
            'wrapper' => [
                'class' => 'form-group col-md-6',
            ],
            'tab' => 'General',
        ]);

        CRUD::addField([
            'name' => 'currency_id',
            'label' => 'currency',
            'type' => 'text',
            'default' => 63,
            'wrapper' => [
                'style' => 'display:none',
            ],
            'tab' => 'General',
        ]);

        $model = $this->crud->getCurrentEntry();
        $this->referencesFields();
        if (isset($model->invoice_type) && $model->invoice_type->code == 61) {
            CRUD::removeSaveActions(['save_and_back','save_and_new']);
        }

        $this->crud->addSaveAction([
            'name' => 'save_and_manage',
            'redirect' => function($crud, $request, $itemId) {
                return route('invoice.generate-temp-document', [ 'invoice' => $itemId]);
            }, // what's the redirect URL, where the user will be taken after saving?

            // OPTIONAL:
            'button_text' => 'Guardar y previsualizar', // override text appearing on the button
            // You can also provide translatable texts, for example:
            // 'button_text' => trans('backpack::crud.save_action_one'),
            'visible' => function($crud) {
                return true;
            }, // customize when this save action is visible for the current operation
            'referrer_url' => function($crud, $request, $itemId) {
                return $crud->route;
            }, // override http_referrer_url
            'order' => 1, // change the order save actions are in
        ]);

        $this->crud->removeSaveActions(['save_and_back','save_and_edit', 'save_and_new']);

        /*
        CRUD::addField([
            'name' => 'preface',
            'type' => 'wysiwyg',
            'label' => 'Descripción',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-12'
            ],
            'tab' => 'Adicional',
        ]);*/
    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();

        if ($this->crud->getCurrentEntry()->invoice_status == Invoice::STATUS_TEMPORAL) {
            \Alert::add('warning', 'El documento temporal se eliminará si guarda cambios');
        }

    }

    protected function referencesFields() : void
    {
        CRUD::addField([
            'name' => 'references_json',
            'type' => 'quotation.repeatable',
            'tab' => 'Referencias',
            'label' => '',
            'new_item_label' => 'Añadir referencia',
            'default' => '{}',
            'fields' => [
                [
                    'name' => 'reference_date',
                    'label' => 'Fecha del documento original',
                    'type' => 'date',
                    'wrapper' => [
                        'class' => 'form-group col-md-3',
                    ],
                    'attributes' => [
                        //'readonly' => true,
                    ],
                ],
                [
                    'name' => 'reference_folio',
                    'label' => 'Número de folio',
                    'wrapper' => [
                        'class' => 'form-group col-md-4',
                    ],
                    'attributes' => [
                        //'readonly' => true,
                    ],
                ],
                [
                    'type' => 'select2_from_array',
                    'options' => InvoiceType::all()->pluck('name','id'),
                    'attribute' => 'name',
                    'name' => 'reference_type_document',
                    'label' => 'Tipo de documento',
                    'wrapper' => [
                        'class' => 'form-group col-md-3',
                    ],
                ],
                [
                    'type' => 'select2_from_array',
                    'options' => [
                        1 => 'Anula documento de referencia',
                        2 => 'Corrige texto documento de referencia',
                        3 => 'Corrige montos',
                    ],
                    'allows_null' => true,
                    'wrapper' => [
                        'class' => 'form-group col-md-3',
                    ],
                    'name' => 'reference_code',
                    'label' => 'Código de referencia',
                ],
                [
                    'name' => 'reference_reason',
                    'type' => 'textarea',
                    'label' => 'Razón',
                    'wrapperAttributes' => [
                        'class' => 'form-group col-md-12',
                    ],
                    'attributes' => [
                        'maxlength' => 30,
                    ],
                ],
            ],
        ]);

    }


    protected function customFilters()
    {
        CRUD::addFilter([
            'name'  => 'customer_name',
            'type'  => 'select2',
            'label' => 'Cliente'
        ], function() {
            return Customer::all()->pluck('full_name', 'id')->toArray();
        }, function($value) {
            $this->crud->addClause('where', 'customer_id', $value);
        });
        
        CRUD::addFilter([
            'type'  => 'date_range',
            'name'  => 'from_to',
            'label' => 'Fecha'
          ],
          false,
          function ($value) { // if the filter is active, apply these constraints
            $dates = json_decode($value);
            $this->crud->addClause('where', 'invoice_date', '>=', $dates->from);
            $this->crud->addClause('where', 'invoice_date', '<=', $dates->to . ' 23:59:59');
        });

        CRUD::addFilter([
            'name'  => 'invoice_type',
            'type'  => 'select2',
            'label' => 'Tipo de documento'
        ], function() {
            return InvoiceType::active()->get()->sortBy('name')->pluck('name', 'id')->toArray();
        }, function($value) {
            $this->crud->addClause('where', 'invoice_type_id', $value);
        });

        CRUD::addFilter([
            'name'  => 'status',
            'type'  => 'dropdown',
            'label' => 'Estado'
          ], [
            'draft' => 'Borrador',
            'temporal' => 'Doc. Temporal',
            'send' => 'Doc. Enviado',
          ], function($value) {
            $this->crud->addClause('where', 'invoice_status', $value);
          });
    }

    public function salesByPeriod(Request $request)
    {
        $fromDate = $request->input('from');
        $toDate = $request->input('to');

        $invoices = Invoice::where('invoice_status', Invoice::STATUS_SEND);

        //@TODO validate date
        if (isset($fromDate)) {
            $invoices = $invoices->where('invoice_date', '>=', $fromDate);
        }

        if (isset($toDate)) {
            $invoices = $invoices->where('invoice_date', '<=', $toDate);
        }

        $invoices = $invoices->whereNotNull('folio')->whereHas('invoice_type', function ($q) {
                $q->whereIn('code', ['33', '34']);
            })
            ->get();
         
        /*return response()->json([
            'count' => $invoices->count(),
        ]);*/
        return '<div class="row">' .
            '<div class="col-6 text-center">' . $invoices->count() . '</div>' .
            '<div class="col-6 text-center">' . currencyFormat($invoices->sum('total'), 'CLP', true) . '</div>' .
            '</div>';

    }

}
