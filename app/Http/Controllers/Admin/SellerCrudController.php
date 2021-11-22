<?php

namespace App\Http\Controllers\Admin;

use App\Models\Bank;

use App\Models\Seller;

use App\Models\Commune;
use App\Models\ContactType;
use App\Cruds\BaseCrudFields;
use App\Models\PaymentMethod;
use App\Models\SellerCategory;
use App\Models\ShippingMethod;
use Illuminate\Support\Carbon;
use App\Models\BankAccountType;
use App\Models\BusinessActivity;
use App\Traits\HasCustomAttributes;
use App\Http\Requests\SellerStoreRequest;
use App\Http\Requests\SellerUpdateRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class SellerCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class SellerCrudController extends CrudController
{
    use HasCustomAttributes;

    private $admin;
    private $userSeller;
    private $sellerData;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Seller::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/seller');
        CRUD::setEntityNameStrings('vendedor', 'vendedores');

        $this->crud->denyAccess('show');

        $this->admin = false;
        $this->userSeller = null;

        if (backpack_user()->can('seller.admin')) {
            $this->admin = true;

            $this->crud->enableExportButtons();
        } else {
            $this->crud->denyAccess('create');
            $this->crud->denyAccess('delete');

            $this->userSeller = Seller::where('user_id', backpack_user()->id)->firstOrFail();
        }

        $this->getExtras();
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
        if (!$this->admin) {
            $value = $this->userSeller->id;

            $this->crud->addClause('where', 'id', '=', $value);
        }

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */

        CRUD::addColumn([
            'name' => 'uid',
            'type' => 'text',
            'label' => 'RUT',
        ]);

        CRUD::addColumn([
            'name' => 'visible_name',
            'type' => 'text',
            'label' => 'Nombre visible',
        ]);

        CRUD::addColumn([
            'name' => 'email',
            'type' => 'text',
            'label' => 'Email',
        ]);

        CRUD::addColumn([
            'name' => 'seller_category',
            'type' => 'relationship',
            'label' => 'Categoría',
            'attribute' => 'name',
        ]);

        CRUD::addColumn([
            'name' => 'created_at_format',
            'type' => 'text',
            'label' => 'Fecha de inscripción',

        ]);

        CRUD::addColumn([
            'name' => 'approved_description',
            'type' => 'text',
            'label' => 'Aprobado',
            'wrapper' => [
                'element' => 'span',
                'class' => function ($crud, $column, $entry, $related_key) {
                    if ($column['text'] == 'Aprobado') {
                        return 'badge badge-success';
                    } elseif ($column['text'] == 'Rechazado') {
                        return 'badge badge-danger';
                    }

                    return 'badge badge-default';
                },
            ],
        ]);

        CRUD::addColumn([
            'name' => 'transbank_code',
            'type' => 'text',
            'label' => 'Webpay',
        ]);

        CRUD::addColumn([
            'name' => 'status_description',
            'type' => 'text',
            'label' => 'Estado',
            'wrapper' => [
                'element' => 'span',
                'class' => function ($crud, $column, $entry, $related_key) {
                    if ($column['text'] == 'Activa') {
                        return 'badge badge-success';
                    }
                    return 'badge badge-default';
                },
            ],
        ]);

        if ($this->admin) {
            $this->customFilters();
        }
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(SellerStoreRequest::class);

        //CRUD::setFromDb(); // fields

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
        $this->crud = (new BaseCrudFields())->setBaseFields($this->crud);

        CRUD::addField([
            'name' => 'uid',
            'type' => 'text',
            'label' => 'RUT',
            'tab' => 'General',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-4',
            ],
        ]);

        CRUD::addField([
            'name' => 'name',
            'type' => 'text',
            'label' => 'Razón social',
            'tab' => 'General',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-4',
            ],
        ]);

        CRUD::addField([
            'name' => 'visible_name',
            'type' => 'text',
            'label' => 'Nombre visible',
            'tab' => 'General',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-4',
            ],
        ]);

        CRUD::addField([
            'name' => 'email',
            'type' => 'email',
            'label' => 'Email',
            'tab' => 'General',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6',
            ],
        ]);

        CRUD::addField([
            'name' => 'phone',
            'type' => 'text',
            'label' => 'Teléfono',
            'tab' => 'General',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6',
            ],
        ]);

        CRUD::addField([
            'name' => 'cellphone',
            'type' => 'text',
            'label' => 'Teléfono móvil',
            'tab' => 'General',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6',
            ],
        ]);

        CRUD::addField([
            'name' => 'web',
            'type' => 'text',
            'label' => 'Sitio web',
            'tab' => 'General',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6',
            ],
        ]);

        CRUD::addField([
            'name' => 'seller_category_id',
            'type' => 'select2_from_array',
            'label' => 'Categoría',
            'options' => SellerCategory::pluck('name', 'id')->toArray(),
            'tab' => 'General',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6',
            ],
        ]);

        CRUD::addField([
            'name' => 'legal_representative_name',
            'type' => 'text',
            'label' => 'Representante legal',
            'tab' => 'General',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6',
            ],
        ]);

        CRUD::addField([
            'name' => 'password',
            'type' => 'password',
            'label' => 'Contraseña',
            'tab' => 'General',
            'wrapper' => [
                'class' => 'form-group col-6',
            ],
        ]);

        CRUD::addField([
            'name' => 'password_confirmation',
            'type' => 'password',
            'label' => 'Repetir contraseña',
            'tab' => 'General',
            'wrapper' => [
                'class' => 'form-group col-6',
            ],
        ]);

        CRUD::addField([
            'name' => 'notes',
            'type' => 'textarea',
            'label' => 'Notas',
            'tab' => 'General',
            'wrapper' => [
                'class' => 'form-group col-12',
            ],
        ]);

        CRUD::addField([
            'name' => 'status',
            'type' => 'checkbox',
            'label' => 'Activo',
            'tab' => 'General',
            'default' => 1,
            'wrapperAttributes' => [
                'class' => 'form-group col-md-12',
            ],
        ]);

        CRUD::addField([
            'name' => 'addresses_data',
            'type' => 'repeatable',
            'label' => 'Direcciones',
            'default' => '{}',
            'fields' => [
                [
                    'name' => 'street',
                    'type' => 'text',
                    'label' => 'Calle',
                    'wrapper' => ['class' => 'form-group col-md-4'],
                ],
                [
                    'name' => 'number',
                    'type' => 'text',
                    'label' => 'Número',
                    'wrapper' => ['class' => 'form-group col-md-2'],
                ],
                [
                    'name' => 'subnumber',
                    'type' => 'text',
                    'label' => 'Casa/Dpto/Oficina',
                    'wrapper' => ['class' => 'form-group col-md-2'],
                ],
                [
                    'name' => 'commune_id',
                    'type' => 'select2_from_array',
                    'label' => 'Comuna',
                    'options' => Commune::orderBy('name', 'asc')->pluck('name', 'id')->toArray(),
                    'wrapper' => ['class' => 'form-group col-md-4'],
                ],
                [
                    'name' => 'uid',
                    'type' => 'text',
                    'label' => 'RUT',
                    'wrapper' => ['class' => 'form-group col-md-4'],
                ],
                [
                    'name' => 'first_name',
                    'type' => 'text',
                    'label' => 'Nombre',
                    'wrapper' => ['class' => 'form-group col-md-4'],
                ],
                [
                    'name' => 'last_name',
                    'type' => 'text',
                    'label' => 'Apellido',
                    'wrapper' => ['class' => 'form-group col-md-4'],
                ],
                [
                    'name' => 'email',
                    'type' => 'email',
                    'label' => 'Email',
                    'wrapper' => ['class' => 'form-group col-md-4'],
                ],
                [
                    'name' => 'phone',
                    'type' => 'text',
                    'label' => 'Teléfono',
                    'wrapper' => ['class' => 'form-group col-md-4'],
                ],
                [
                    'name' => 'cellphone',
                    'type' => 'text',
                    'label' => 'Teléfono móvil',
                    'wrapper' => ['class' => 'form-group col-md-4'],
                ],
                [
                    'name' => 'extra',
                    'type' => 'textarea',
                    'label' => 'Detalles',
                    'wrapper' => ['class' => 'form-group col-md-12'],
                ],
            ],
            'new_item_label' => 'Agregar dirección',
            'tab' => 'Direcciones',
        ]);

        CRUD::addField([
            'name' => 'activities_data',
            'type' => 'repeatable',
            'label' => 'Giros',
            'default' => '{}',
            'fields' => [
                [
                    'name' => 'business_activity_id',
                    'type' => 'select2_from_array',
                    'label' => 'Giro',
                    'options' => BusinessActivity::orderBy('name', 'asc')->pluck('name', 'id')->toArray(),
                    'wrapperAttributes' => [
                        'class' => 'form-group col-md-12',
                    ],
                ],
            ],
            'new_item_label' => 'Agregar giro',
            'tab' => 'Actividades comerciales',
        ]);

        Crud::addField([
            'name' => 'banks_data',
            'label' => 'Cuentas',
            'type' => 'repeatable',
            'default' => '{}',
            'fields' => [
                [
                    'name' => 'account_type_id',
                    'type' => 'select2_from_array',
                    'label' => 'Tipo de cuenta',
                    'options' => BankAccountType::orderBy('name', 'asc')->pluck('name', 'id')->toArray(),
                    'wrapperAttributes' => [
                        'class' => 'form-group col-md-4',
                    ],
                ],
                [
                    'name' => 'bank_id',
                    'type' => 'select2_from_array',
                    'label' => 'Banco',
                    'options' => Bank::orderBy('name', 'asc')->pluck('name', 'id')->toArray(),
                    'wrapperAttributes' => [
                        'class' => 'form-group col-md-4',
                    ],
                ],
                [
                    'name' => 'account_number',
                    'type' => 'text',
                    'label' => 'Número de Cuenta',
                    'wrapperAttributes' => [
                        'class' => 'form-group col-md-4',
                    ],
                ],
                [
                    'name' => 'rut',
                    'type' => 'text',
                    'label' => 'RUT',
                    'wrapper' => [
                        'class' => 'form-group col-md-4',
                    ],
                ],
                [
                    'name' => 'first_name',
                    'type' => 'text',
                    'label' => 'Nombre',
                    'wrapper' => [
                        'class' => 'form-group col-md-4',
                    ],
                ],
                [
                    'name' => 'last_name',
                    'type' => 'text',
                    'label' => 'Apellido',
                    'wrapper' => [
                        'class' => 'form-group col-md-4',
                    ],
                ],
                [
                    'name' => 'email',
                    'type' => 'email',
                    'label' => 'Email',
                    'wrapper' => [
                        'class' => 'form-group col-md-6',
                    ],
                ],
                [
                    'name' => 'phone',
                    'type' => 'text',
                    'label' => 'Teléfono',
                    'wrapper' => [
                        'class' => 'form-group col-md-6',
                    ],
                ],
            ],
            'new_item_label' => 'Agregar cuenta',
            'tab' => 'Datos bancarios',
        ]);

        Crud::addField([
            'name' => 'contacts_data',
            'label' => 'Contactos',
            'type' => 'repeatable',
            'default' => '{}',
            'fields' => [
                [
                    'name' => 'contact_type_id',
                    'type' => 'select2_from_array',
                    'label' => 'Plataforma',
                    'options' => ContactType::orderBy('name', 'asc')->pluck('name', 'id')->toArray(),
                    'wrapperAttributes' => [
                        'class' => 'form-group col-md-6',
                    ],
                ],
                [
                    'name' => 'url',
                    'type' => 'text',
                    'label' => 'URL',
                    'wrapper' => [
                        'class' => 'form-group col-md-6',
                    ],
                ],
            ],
            'new_item_label' => 'Agregar contacto',
            'tab' => 'Contactos',
        ]);

        CRUD::addField([
            'name' => 'download_recomended_policy',
            'type' => 'custom_html',
            'value' => '<a href="' . asset('pdf/POLITICAS_SUGERIDAS.pdf') . '">DESCARGAR POLITICAS SUGERIDAS</a>',
            'tab' => 'Políticas',
            'wrapper' => [
                'style' => 'text-align: right; padding-right: 20px;',
            ],
        ]);

        CRUD::addField([
            'name' => 'privacy_policy',
            'type' => 'tinymce',
            'label' => 'Política de privacidad',
            'tab' => 'Políticas',
            'wrapper' => [
                'style' => 'margin-top: -15px;',
            ],
        ]);

        CRUD::addField([
            'name' => 'shipping_policy',
            'type' => 'tinymce',
            'label' => 'Política de compra',
            'tab' => 'Políticas',
        ]);

        CRUD::addField([
            'name' => 'return_policy',
            'tab' => 'Políticas',
            'label' => 'Política de devolución',
            'type' => 'tinymce',
        ]);

        CRUD::addField([
            'name' => 'maximun_days_for_shipped',
            'label' => 'Cantidad de días máximo para ser enviado un paquete',
            'type' => 'number',
            'tab' => 'Políticas',
            'wrapper' => [
                'class' => 'form-group col-md-2'
            ],
        ]);

        CRUD::addField([
            'name' => 'slug',
            'type' => 'text',
            'label' => 'Slug',
            'tab' => 'SEO',
            'prefix' => env('APP_URL') . '/shop/',
            'hint' => 'Esta sera el enlace que los visitantes pueden utilizar para acceder a los productos de tu tienda',
            'wrapper' => [
                'class' => 'form-group col-md-12'
            ],
        ]);

        CRUD::addField([
            'name' => 'slug_formatter_without_origin',
            'type' => 'slug_formatter_without_origin',
            'slug' => 'slug',
            'tab' => 'SEO',
        ]);

        CRUD::addField([
            'name' => 'meta_title',
            'type' => 'text',
            'label' => 'Título para los buscadores ',
            'tab' => 'SEO',
            'wrapper' => [
                'class' => 'form-group col-md-6',
            ],
        ]);

        CRUD::addField([
            'name' => 'meta_keywords',
            'type' => 'text',
            'attributes' => [
                'placeholder' => 'Ejemplo: actividad, nombre de empresa',
            ],
            'label' => 'Palabras clave (separadas por coma)',
            'tab' => 'SEO',
            'wrapper' => [
                'class' => 'form-group col-md-6',
            ],
        ]);

        CRUD::addField([
            'name' => 'meta_description',
            'type' => 'textarea',
            'label' => 'Descripción para los buscadores',
            'tab' => 'SEO',
        ]);

        CRUD::addField([
            'name' => 'logo',
            'type' => 'image',
            'label' => 'Logo',
            'crop' => true,
            'wrapper' => [
                'class' => 'form-group col-md-12',
            ],
            'tab' => 'SEO',
        ]);

        CRUD::addField([
            'name' => 'banner',
            'type' => 'image',
            'label' => 'Banner',
            'crop' => true,
            'wrapper' => [
                'class' => 'form-group col-md-12',
            ],
            'tab' => 'SEO',
        ]);

        CRUD::addField([
            'name' => 'description',
            'type' => 'textarea',
            'label' => 'Información de la tienda',
            'tab' => 'SEO',
        ]);

        CRUD::addField([
            'name' => 'payments_data',
            'type' => 'repeatable',
            'label' => 'Métodos de pago',
            'new_item_label' => 'Agregar método de pago',
            'default' => '{}',
            'fields' => [
                [
                    'name' => 'payment_method_id',
                    'type' => 'select2_from_array',
                    'options' => PaymentMethod::orderBy('title', 'asc')->pluck('title', 'id')->toArray(),
                    'label' => 'Metodo de pago',
                    'wrapper' => [
                        'class' => 'form-group col-12',
                    ],
                ],
                [
                    'name' => 'key',
                    'type' => 'text',
                    'label' => 'Llave',
                    'wrapper' => [
                        'class' => 'form-group col-12',
                    ],
                ],
                [
                    'name' => 'status',
                    'label' => 'Activo',
                    'type' => 'checkbox',
                    'wrapperAttributes' => [
                        'class' => 'form-group col-md-12',
                    ],
                    'default' => true,
                ],
            ],
            'tab' => 'Venta',
        ]);

        CRUD::addField([
            'name' => 'shippings_data',
            'type' => 'repeatable',
            'label' => 'Métodos de envío',
            'new_item_label' => 'Agregar método de envío',
            'default' => '{}',
            'fields' => [
                [
                    'name' => 'shipping_method_id',
                    'type' => 'select2_from_array',
                    'options' => ShippingMethod::orderBy('title', 'asc')->pluck('title', 'id')->toArray(),
                    'label' => 'Metodo de envío',
                    'wrapper' => [
                        'class' => 'form-group col-12',
                    ],
                ],
                [
                    'name' => 'key',
                    'type' => 'text',
                    'label' => 'Llave',
                    'wrapper' => [
                        'class' => 'form-group col-12',
                    ],
                ],
                [
                    'name' => 'status',
                    'label' => 'Activo',
                    'type' => 'checkbox',
                    'wrapperAttributes' => [
                        'class' => 'form-group col-md-12',
                    ],
                    'default' => true,

                ],
            ],
            'tab' => 'Venta',
        ]);

        if ($this->admin) {
            CRUD::addField([
                'name' => 'is_approved',
                'label' => 'Aprobado',
                'type' => 'radio',
                'options' => [
                    0 => 'En revisión',
                    1 => 'Aprobado',
                    2 => 'Rechazado',
                ],
                'default' => 0,
                'inline' => true,
                'tab' => 'Administrador',
                'wrapper' => [
                    'class' => 'form-group col-md-6',
                ],
            ]);
            // $createAt = $this->crud->getCurrentEntry()->created_at;
            // $inscriptionDate = $createAt->format('d/m/Y');
            CRUD::addField([
                'name' => 'created_at_format',
                'label' => 'Fecha de inscripción',
                'type' => 'text',
                'tab' => 'Administrador',
                'wrapper' => [
                    'class' => 'form-group col-md-6',
                ],
                'attributes' => [
                    'readonly' => 'readonly',
                ],
            ]);

            CRUD::addField([
                'name' => 'is_approved',
                'label' => 'Aprobado',
                'type' => 'radio',
                'options' => [
                    0 => 'En revisión',
                    1 => 'Aprobado',
                    2 => 'Rechazado',
                ],
                'default' => 0,
                'inline' => true,
                'tab' => 'Administrador',
                'wrapper' => [
                    'class' => 'form-group col-md-12',
                ],
            ]);

            CRUD::addField([
                'name' => 'rejected_reason',
                'type' => 'textarea',
                'label' => 'Motivo de rechazo',
                'tab' => 'Administrador',
                'wrapper' => [
                    'class' => 'form-group col-12',
                ],
            ]);

            CRUD::addField([
                'name' => 'commission_enable',
                'type' => 'checkbox',
                'label' => 'Comisión',
                'tab' => 'Administrador',
                'wrapper' => [
                    'class' => 'form-group col-md-12',
                ],
            ]);

            CRUD::addField([
                'name' => 'commission_percentage',
                'type' => 'number',
                'attributes' => [
                    'step' => '.01',
                    'min' => 0,
                    'max' => 100,
                ],
                'default' => '0,00',
                'label' => 'Porcentaje comisión',
                'tab' => 'Administrador',
                'wrapper' => [
                    'class' => 'form-group col-12',
                ],
            ]);

            CRUD::addField([
                'name' => 'disabled_commission',
                'type' => 'checkbox_disable_fields',
                'checkbox' => 'commission_enable',
                'fields' => [
                    'commission_percentage',
                ],
                'tab' => 'Administrador',
            ]);

            CRUD::addField([
                'name' => 'custom_1',
                'label' => 'Contrato transbank',
                'type' => 'radio',
                'options' => [
                    1 => 'Sí',
                    0 => 'No',
                ],
                'default' => 0,
                'inline' => true,
                'tab' => 'Administrador',
                'wrapper' => [
                    'class' => 'form-group col-md-6',
                ],
            ]);

            CRUD::addField([
                'name' => 'custom_2',
                'label' => 'Despacho',
                'tab' => 'Administrador',
                'wrapper' => [
                    'class' => 'form-group col-6',
                ],
            ]);


            if ( $this->crud->getCurrentEntry() && !$this->crud->getCurrentEntry()->subscription_data) {
                CRUD::addField([
                    'name' => 'plan_id',
                    'label' => 'Plan',
                    'placeholder' => 'Seleccionar un plan',
                    'minimum_input_length' => 0,
                    'attribute' => "name",
                    //'type' => 'select2_from_array',
                    //'options' => Plans::orderBy('name', 'asc')->pluck('name', 'id', 'invoice_interval','currency','price')->toArray(),
                    'type' => 'select2_from_ajax',
                    'data_source' => url('admin/api/getPlans'),
                    'model' => 'App\Models\Plans',
                    'method' => 'POST',
                    'tab' => 'Subscripción',
                    'include_all_form_fields' => true,
                    'wrapper' => [
                        'class' => 'form-group col-6',
                    ],
                    'attributes' => ['class' => 'select-plan'],
                    'fake' => true,
                    'store_in' => 'subscription_data',
                ]);

                CRUD::addField([
                    'name' => 'price',
                    'label' => 'Precio',
                    'type' => 'expense.select_plans_to_price',
                    'dependencies' => ['id_plan'],
                    'tab' => 'Subscripción',
                    'wrapper' => ['class' => 'form-group col-6'],
                    'attributes' => ['readonly' => 'readonly'],
                    'fake' => true,
                    'store_in' => 'subscription_data',
                ]);

                CRUD::addField([
                    'name' => 'starts_at',
                    'label' => 'Fecha de Inicio',
                    'type' => 'text',
                    'tab' => 'Subscripción',
                    'wrapper' => ['class' => 'form-group col-6'],
                    'attributes' => ['readonly' => 'readonly', 'class' => ' subscription_starts_at form-control col-11'],
                    'fake' => true,
                    'store_in' => 'subscription_data',
                ]);

                CRUD::addField([
                    'name' => 'ends_at',
                    'label' => 'Fecha de Fin',
                    'type' => 'text',
                    'tab' => 'Subscripción',
                    'wrapper' => [
                        'class' => 'form-group col-6',
                    ],
                    'attributes' => ['readonly' => 'readonly', 'class' => 'subscription_ends_at form-control col-11'],
                    'fake' => true,
                    'store_in' => 'subscription_data',
                ]);
                CRUD::addField([
                    'name' => 'user_id',
                    'type' => 'hidden',
                    'tab' => 'Subscripción',
                    'fake' => true,
                    'store_in' => 'subscription_data',
                ]);
            } else {

                CRUD::addField([
                    'name' => 'plan_id',
                    'label' => 'Plan',
                    'placeholder' => 'Seleccionar un plan',
                    'minimum_input_length' => 0,
                    'attribute' => "name",
                    'type' => 'select2_from_ajax',
                    'data_source' => url('admin/api/getPlans'),
                    'model' => 'App\Models\Plans',
                    'method' => 'POST',
                    'tab' => 'Datos de la Subscripción',
                    'include_all_form_fields' => true,
                    'wrapper' => [
                        'class' => 'form-group col-6',
                    ],
                    'attributes' => ['class' => 'select-plan', 'disabled' => 'disabled'],
                    'fake' => true,
                    'store_in' => 'subscription_data',
                ]);

                CRUD::addField([
                    'name' => 'price',
                    'label' => 'Precio',
                    'type' => 'text',
                    'dependencies' => ['id_plan'],
                    'tab' => 'Datos de la Subscripción',
                    'wrapper' => ['class' => 'form-group col-6'],
                    'attributes' => ['readonly' => 'readonly'],
                    'fake' => true,
                    'store_in' => 'subscription_data',
                ]);

                CRUD::addField([
                    'name' => 'starts_at',
                    'label' => 'Fecha de Inicio',
                    'type' => 'text',
                    'tab' => 'Datos de la Subscripción',
                    'wrapper' => ['class' => 'form-group col-6'],
                    'attributes' => ['readonly' => 'readonly', 'class' => ' subscription_starts_at form-control col-12'],
                    'fake' => true,
                    'store_in' => 'subscription_data',
                ]);

                CRUD::addField([
                    'name' => 'ends_at',
                    'label' => 'Fecha de Fin',
                    'type' => 'text',
                    'tab' => 'Datos de la Subscripción',
                    'wrapper' => [
                        'class' => 'form-group col-6',
                    ],
                    'attributes' => ['readonly' => 'readonly', 'class' => 'subscription_ends_at form-control col-12'],
                    'fake' => true,
                    'store_in' => 'subscription_data',
                ]);
                CRUD::addField([
                    'name' => 'user_id',
                    'type' => 'hidden',
                    'tab' => 'Datos de la Subscripción',
                    'fake' => true,
                    'store_in' => 'subscription_data',
                ]);
            }

        }

        CRUD::addField([
            'name' => 'rut_formatter',
            'type' => 'rut_formatter',
            'rut_fields' => ['uid', 'rut'],
            'tab' => 'General',
        ]);

        CRUD::addField([
            'name' => 'radio_script',
            'type' => 'radio_readonly_fields',
            'readonly_fields' => [
                'textarea' => ['rejected_reason'],
                //'input' => ['commission_percentage'] test
            ],
            'radiobutton_name' => 'is_approved',
            'is_value' => ['En revisión', 'Aprobado'],
            'tab' => 'General',
        ]);
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        if ($this->userSeller) {
            if ($this->userSeller->id != $this->crud->getCurrentEntryId()) {
                $this->crud->denyAccess('update');
            }
        }

        $this->setupCreateOperation();
        CRUD::setValidation(SellerUpdateRequest::class);

        if ($this->admin) {
            CRUD::addField([
                'name' => 'source',
                'type' => 'text',
                'label' => 'Fuente',
                'tab' => 'Administrador',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-12',
                ],
                'attributes' => [
                    'readonly' => 'readonly',
                ],
            ]);
        }
    }

    /**
     * Add filters in list view
     *
     * @return void
     */
    private function customFilters()
    {
        CRUD::addFilter([
            'type' => 'text',
            'name' => 'rut',
            'label' => 'RUT',
        ], false, function ($value) {
            $this->crud->addClause('where', 'uid', 'LIKE', '%' . $value . '%');
        });

        CRUD::addFilter([
            'type' => 'text',
            'name' => 'visible_name',
            'label' => 'Nombre visible',
        ], false, function ($value) {
            $this->crud->addClause('where', 'visible_name', 'LIKE', '%' . $value . '%');
        });

        CRUD::addFilter([
            'type' => 'text',
            'name' => 'email',
            'label' => 'Email',
        ], false, function ($value) {
            $this->crud->addClause('where', 'email', 'LIKE', '%' . $value . '%');
        });

        CRUD::addFilter([
            'name' => 'seller_category_id',
            'type' => 'select2',
            'label' => 'Categoria',
        ], function () {
            return SellerCategory::all()->pluck('name', 'id')->toArray();
        }, function ($values) {
            $this->crud->addClause('where', 'seller_category_id', 'LIKE', '%' . $values . '%');
        });
    }
}
