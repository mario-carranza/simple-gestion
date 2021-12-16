<?php

namespace App\Http\Controllers\Admin;

use DateTime;
use App\Models\Seller;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Cruds\BaseCrudFields;
use App\Models\ProductCategory;
use App\Http\Requests\ProductRequest;
use Backpack\Settings\app\Models\Setting;
use App\Http\Requests\ProductCreateRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Requests\ProductVariantUpdateRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ProductCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ProductCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    private $admin;
    private $userSeller;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Product::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/product');
        CRUD::setEntityNameStrings('producto', 'productos');

        $this->crud->denyAccess('show');
        $this->crud->allowAccess('bulkApprove');
        $this->crud->allowAccess('bulkReject');

        $this->admin = false;
        $this->userSeller = null;

        if (backpack_user()->can('product.admin')) {
            $this->admin = true;
            $this->crud->enableExportButtons();
            $this->crud->allowAccess('bulkDelete');
        } else {
            $this->userSeller = Seller::where('user_id', backpack_user()->id)->firstOrFail();
            $this->crud->denyAccess('delete');
        }

    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->enableBulkActions();

        $this->crud->addButtonFromView('top', 'create_housing', 'product.create_housing', 'end');

        $this->crud->addButtonFromView('top', 'create_tour', 'product.create_tour', 'end');

        // If not admin, show only user products
        if(!$this->admin) {
            $this->crud->addClause('where', 'seller_id', '=', $this->userSeller->id);
        } else {
            $this->crud->addButtonFromView('bottom', 'bulk_delete', 'product.bulk_delete', 'beginning');
            $this->crud->addButtonFromView('bottom', 'bulk_reject', 'product.bulk_reject', 'beginning');
            $this->crud->addButtonFromView('bottom', 'bulk_approve', 'product.bulk_approve', 'beginning');
        }

        // Hide children products
        $this->crud->addClause('where', 'parent_id', '=', null);

        CRUD::addColumn([
            'name' => 'sku',
            'label' => 'SKU',
            'type' => 'text',
            ]);


        if ($this->admin) {
            CRUD::addColumn([
                'name' => 'seller',
                'label' => 'Vendedor',
                'type' => 'relationship',
                'attribute' => 'visible_name',
            ]);
        }

        CRUD::addColumn([
            'name' => 'name',
            'label' => 'Nombre',
            'type' => 'text',
        ]);

        CRUD::addColumn([
            'name' => 'product_class',
            'label' => 'Clase de producto',
            'type' => 'relationship',
        ]);
        CRUD::addColumn([
            'name' => 'product_type',
            'label' => 'Tipo de producto',
            'type' => 'relationship',
        ]);

        CRUD::addColumn([
            'name' => 'is_approved_text',
            'label' => 'Estado de aprobacion',
            'type' => 'text',
            'wrapper' => [
                'element' => 'span',
                'class' => function ($crud, $column, $entry, $related_key) {
                    switch($column['text']) {
                        case 'Aprobado':
                            return 'badge badge-success';
                            break;
                        case 'Pendiente':
                            return 'badge badge-default';
                            break;
                        case 'Rechazado':
                            return 'badge badge-danger';
                            break;
                    }
                },
            ],
        ]);

        $this->customFilters();
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ProductCreateRequest::class);

        $this->crud = (new BaseCrudFields())->setBaseFields($this->crud);

        $this->crud->orderSaveAction('save_and_edit', 1);

        $createCrudType = request()->input('type');

        $entityNames = [
            'housing' => 'alojamiento',
            'tour' => 'tour',
        ];

        $entityName = $entityNames[request()->input('type')] ?? 'producto';

        CRUD::addField([
            'name' => 'name',
            'label' => 'Nombre del ' . $entityName,
            'type' => 'text',
        ]);

        CRUD::addField([
            'name' => 'sku',
            'label' => 'SKU',
            'type' => 'text',
        ]);

        CRUD::addField([
            'name' => 'url_key',
            'label' => 'URL Key (Slug)',
            'type' => 'text',
        ]);

        CRUD::addField([
            'label'     => "Categoría",
            'type'      => 'product.select2_multiple',
            'name'      => 'categories',
            'entity'    => 'categories',
            'maximumSelectionLength' => '1',
            'model'     => "App\Models\ProductCategory",
            'attribute' => 'name',
            'pivot'     => true,
            'options'   => (function ($query) {
                return $query->orderBy('name', 'ASC')->get();
            }),
            'attributes' => [
                'id' => 'categories',
            ]
        ]);

        CRUD::addField([
            'label'       => "Clase de " . $entityName,
            'type'        => "select2_from_ajax",
            'name'        => 'product_class_id',
            'placeholder' => 'Selecciona la clase de ' . $entityName,
            'entity'      => 'product_class',
            'attribute'   => "name",
            'data_source' => url("admin/api/productclass/get"),
            'minimum_input_length' => 0,
            'include_all_form_fields'  => true,
            'dependencies'  => ['categories'],
            'attributes' => [
                'id' => 'product_class_id'
            ],
        ]);

        if ($createCrudType === 'housing') {
            $this->prepareTravelCrud($createCrudType);
        } else if ($createCrudType === 'tour') {
            $this->prepareTravelCrud($createCrudType);
        } else {
            $this->prepareGeneralCreateCrud();
        }
    }

    protected function prepareGeneralCreateCrud()
    {
        CRUD::addField([
            'name' => 'product_type_class',
            'type' => 'product.product_class_hint',
        ]);

        CRUD::addField([
            'label' => 'Tipo de producto',
            'name' => 'product_type_id',
            'type' => 'relationship',
            'entity' => 'product_type',
            'placeholder' => 'Selecciona un tipo de producto',
        ]);

        CRUD::addField([
            'name' => 'product_type_hint',
            'type' => 'product.product_type_hint',
        ]);

        CRUD::addField([
            'label'       => "Atributos para variaciones",
            'type'        => "select2_from_ajax_multiple",
            'name'        => 'super_attributes',
            'placeholder' => 'Selecciona los atributos que usaras en tus variaciones',
            'entity'      => 'super_attributes',
            'attribute'   => "descripcion_name",
            'data_source' => url("admin/api/productclassattributes/get"),
            'pivot'       => true,
            'minimum_input_length' => 0,
            'include_all_form_fields'  => true,
            'dependencies'  => ['product_class_id'],
            'attributes' => [
                'id' => 'super_attributes',
            ],
            'wrapper' => [
               'style' => 'display:none',
               'id' => 'super_attributes_wrapper',
            ]
        ]);

        CRUD::addField([
            'name' => 'seller_id',
            'label' => 'Vendedor',
            'entity' => 'seller',
            'default' => $this->userSeller ?? '',
            'type' => 'relationship',
            'attribute' => 'visible_name',
            'placeholder' => 'selecciona un vendedor',
            'wrapper' => [
               'style' => $this->admin ? '' : 'display:none',
            ],
        ]);

        CRUD::addField([
            'name' => 'currency_id',
            'label' => 'Moneda',
            'type' => 'relationship',
            'entity' => 'currency',
            'default' => 63,
            'wrapper' => [
                'style' => 'display:none',
            ],
        ]);

        CRUD::addField([
            'name' => 'is_service',
            'label' => 'Es un servicio',
            'type' => 'checkbox',
            'wrapper' => [
                'class' => 'col-md-12 form-group is_service_checkbox',
            ],
            'attributes' => [
                'class' => 'service_check',
            ],
        ]);

        CRUD::addField([
            'name' => 'use_inventory_control',
            'label' => 'Usar control de inventario',
            'type' => 'checkbox',
            'wrapper' => [
                'class' => 'col-md-12 form-group is_inventory_checkbox',
            ],
            'attributes' => [
                'class' => 'inventory_check',
            ],
        ]);

        CRUD::addField([
            'name' => 'is_housing',
            'type' => 'hidden',
            'value' => null,
            'default' => null,
        ]);

        CRUD::addField([
            'name' => 'is_tour',
            'type' => 'hidden',
            'value' => null,
            'default' => null,
        ]);

        CRUD::addField([
            'name' => 'is_travel',
            'type' => 'hidden',
        ]);

        CRUD::addField([
            'name' => 'customShowHideSuperAttributes',
            'type' => 'product.show_hide_variants',
        ]);

        CRUD::addField([
            'name' => 'customJs',
            'type' => 'product.custom_js',
        ]);

        CRUD::addField([
            'name' => 'slug_formatter',
            'type' => 'slug_formatter',
            'origen' => 'name',
            'slug' => 'url_key',
        ]);
    }

    protected function prepareTravelCrud($type)
    {
        CRUD::addField([
            'name' => 'product_type_class',
            'type' => 'product.product_class_hint',
        ]);

        CRUD::addField([
            'name' => 'product_type_id',
            'type' => 'hidden',
            'default' => 1,
        ]);

        CRUD::addField([
            'name' => 'seller_id',
            'label' => 'Vendedor',
            'entity' => 'seller',
            'default' => $this->userSeller ?? '',
            'type' => 'relationship',
            'attribute' => 'visible_name',
            'placeholder' => 'selecciona un vendedor',
            'wrapper' => [
               'style' => $this->admin ? '' : 'display:none',
            ],
        ]);

        CRUD::addField([
            'name' => 'currency_id',
            'label' => 'Moneda',
            'type' => 'relationship',
            'entity' => 'currency',
            'default' => 63,
            'wrapper' => [
                'style' => 'display:none',
            ],
        ]);

        CRUD::addField([
            'name' => 'is_service',
            'type' => 'hidden',
            'value' => 1,
            'default' => 1,
        ]);

        CRUD::addField([
            'name' => 'use_inventory_control',
            'type' => 'hidden',
            'value' => 0,
            'default' => 0,
        ]);

        CRUD::addField([
            'name' => 'is_housing',
            'type' => 'hidden',
            'value' => $type === 'housing',
            'default' => $type === 'housing',
        ]);

        CRUD::addField([
            'name' => 'is_tour',
            'type' => 'hidden',
            'value' => $type === 'tour',
            'default' => $type === 'tour',
        ]);

        CRUD::addField([
            'name' => 'is_travel',
            'type' => 'hidden',
            'default' => 1,
            'value' => 1,
        ]);

        CRUD::addField([
            'name' => 'customJs',
            'type' => 'product.custom_js',
        ]);

        CRUD::addField([
            'name' => 'slug_formatter',
            'type' => 'slug_formatter',
            'origen' => 'name',
            'slug' => 'url_key',
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
        $product = $this->crud->getModel()->find($this->crud->getCurrentEntryId());
        $attributes = $product->product_class->product_class_attributes;

        // Set validations
        if($product->product_type->id == Product::PRODUCT_TYPE_SIMPLE) {
            CRUD::setValidation(ProductUpdateRequest::class);
        } else if ($product->product_type->id == Product::PRODUCT_TYPE_CONFIGURABLE) {
            CRUD::setValidation(ProductVariantUpdateRequest::class);
        }

        $this->setGeneralFields();

        $this->setImagesFields();

        if ($product->product_type->id == Product::PRODUCT_TYPE_CONFIGURABLE) {
            $this->setVariationsField($product);
        }

        if (
            $product->product_type->id == Product::PRODUCT_TYPE_SIMPLE
            && !$product->is_housing
            && !$product->is_tour
        ) {
            $this->setPriceDimensionsFields($product);
        }

        if ($product->product_type->id == Product::PRODUCT_TYPE_SIMPLE && $product->use_inventory_control) {
            $this->setInventoryFields($product);
        }

        if ($product->is_housing) {
            $this->setHousePricingFields(); 
        }

        if ($product->is_tour) {
            $this->setTourFields(); 
        }

        if (count($attributes) !== 0) {
            $this->setAttributesFields($attributes, $product);
        }

        $this->setTermnAndConditionFields();

        $this->setSeoFields();

        if ($this->admin) {
            $this->setStatusVisibilityFields();
        }
    }

    public function setGeneralFields() {
        CRUD::addField([
            'name' => 'name',
            'label' => 'Nombre del producto',
            'type' => 'text',
            'tab' => 'Información general'
        ]);

        CRUD::addField([
            'name' => 'sku',
            'label' => 'SKU',
            'type' => 'text',
            'tab' => 'Información general'
        ]);

        CRUD::addField([
            'name' => 'url_key',
            'label' => 'Url Key (Slug)',
            'type' => 'text',
            'tab' => 'Información general'
        ]);

       CRUD::addField([
            'name' => 'seller_id',
            'label' => 'Vendedor',
            'entity' => 'seller',
            'type' => 'relationship',
            'tab' => 'Información general',
            'attribute' => 'visible_name',
            'wrapper' => [
                'style' => $this->admin ? '' : 'display:none',
            ],
            'placeholder' => 'selecciona un vendedor',
        ]);

        CRUD::addField([
            'name' => 'short_description',
            'label' => 'Resumen',
            'type' => 'text',
            'tab' => 'Información general'
        ]);

        CRUD::addField([
            'name' => 'description',
            'label' => 'Descripción completa',
            'type' => 'wysiwyg',
            'tab' => 'Información general'
        ]);

        CRUD::addField([
            'label' => 'Marca',
            'name' => 'product_brand_id',
            'type' => 'relationship',
            'entity' => 'brand',
            'placeholder' => 'Selecciona una marca',
            'tab' => 'Información general'
        ]);

        CRUD::addField([
            'label'     => "Categorías",
            'type'      => 'product.select2_multiple',
            'name'      => 'categories',
            'entity'    => 'categories',
            'model'     => "App\Models\ProductCategory",
            'attribute' => 'name',
            'options'   => (function ($query) {
                return $query->orderBy('name', 'ASC')->get();
            }),
            'pivot'     => true,
            'tab' => 'Información general',
        ]);

        CRUD::addField([
            'name' => 'is_service',
            'label' => 'Tipo de publicación',
            'type' => 'select2_from_array',
            'tab' => 'Información general',
            'options' => [
                0 => 'Producto',
                1 => 'Servicio',
            ],
            'attributes' => [
                'disabled' => true,
            ]
        ]);

        CRUD::addField([
            'name' => 'product_class',
            'label' => 'Clase del producto',
            'type' => 'relationship',
            'tab' => 'Información general',
            'attributes' => [
                'disabled' => true,
            ]
        ]);

        CRUD::addField([
            'name' => 'is_template',
            'label' => 'Es una plantilla',
            'type' => 'checkbox',
            'tab' => 'Información general',
            'wrapper' => [
                'style' => 'display:none',
            ],
        ]);

        CRUD::addField([
            'name' => 'is_travel',
            'label' => 'Turismo',
            'type' => 'checkbox',
            'tab' => 'Información general'
        ]);

        /* CRUD::addField([
            'name' => 'has_reservation_form',
            'label' => 'Requiere formulario de reserva',
            'type' => 'checkbox',
            'tab' => 'Información general'
        ]); */

        CRUD::addField([
            'name' => 'status',
            'label' => 'Activo',
            'type' => 'checkbox',
            'default' => '1',
            'tab' => 'Información general'
        ]);

        CRUD::addField([
            'name' => 'CustomShowHidedFields',
            'type' => 'product.show_hide_fields',
            'tab' => 'Información general',
        ]);
    }

    public function setImagesFields() {
        CRUD::addField([
            'name'  => 'images_json',
            'label' => 'Imágenes.',
            'hint' => 'Los formatos permitidos son PNG y JPG. Las dimensiones deben ser menores o iguales a 1.024 x 1.024 px',
            'type'  => 'repeatable',
            'fields' => [
                [
                    'label' => "Imagen",
                    'name' => "image",
                    'type' => 'image',
                    'crop' => true,
                    'aspect_ratio' => 1,
                ]
                ],
            'new_item_label'  => 'Agregar otra imagen',
            'tab' => 'Imágenes',
        ]);
    }

    public function setPriceDimensionsFields($product) {
        CRUD::addField([
            'name' => 'currency_id',
            'label' => 'Moneda',
            'type' => 'relationship',
            'entity' => 'currency',
            'tab' => 'Precio y envío',
            'placeholder' => 'Selecciona una moneda',
            'default' => 63,
            'wrapper' => [
                'style' => 'display:none',
                'class' => 'form-group col-lg-12 required',
            ],
        ]);

        CRUD::addField([
            'name' => 'price',
            'label' => 'Precio',
            'type' => 'product.number_format',
            'tab' => 'Precio y envío',
            'attributes' => [
                'step' => 'any',
            ],
            'wrapper' => [
                'class' => 'form-group col-lg-12 required',
            ],
        ]);

        CRUD::addField([
            'name' => 'cost',
            'label' => 'Costo',
            'type' => 'product.number_format',
            'tab' => 'Precio y envío',
            'attributes' => [
                'step' => 'any',
            ],
        ]);

        CRUD::addField([
            'name' => 'special_price',
            'label' => 'Precio de oferta',
            'type' => 'product.number_format',
            'tab' => 'Precio y envío',
            'attributes' => [
                'step' => 'any',
            ],
        ]);

        CRUD::addField([
            'name' => 'special_price_from',
            'label' => 'Precio de oferta desde',
            'type' => 'date',
            'tab' => 'Precio y envío',
            'wrapper' => [
                'class' => 'col-lg-6 col-md-6 col-sm-12 mb-3 form-group',
                'id' => 'special_price_from',
                'style' => 'display:none',
            ]
        ]);

        CRUD::addField([
            'name' => 'special_price_to',
            'label' => 'Precio de oferta hasta',
            'type' => 'date',
            'tab' => 'Precio y envío',
            'wrapper' => [
                'class' => 'col-lg-6 col-md-6 col-sm-12 mb-3 form-group',
                'id' => 'special_price_to',
                'style' => 'display:none',
            ]
        ]);

        if (!$product->is_service){
            CRUD::addField([
                'name' => 'weight',
                'label' => 'Peso',
                'type' => 'product.number_format',
                'suffix' => 'kg',
                'tab' => 'Precio y envío',
                'attributes' => [
                    'step' => 'any',
                ],
                'wrapper' => [
                    'class' => 'form-group mb-3 col required',
                ],
            ]);

            CRUD::addField([
                'name' => 'height',
                'label' => 'Alto',
                'type' => 'product.number_format',
                'suffix' => 'cm',
                'tab' => 'Precio y envío',
                'attributes' => [
                    'step' => 'any',
                ],
                'wrapper' => [
                    'class' => 'form-group mb-3 col required',
                ],
            ]);

            CRUD::addField([
                'name' => 'width',
                'label' => 'Ancho',
                'type' => 'product.number_format',
                'suffix' => 'cm',
                'tab' => 'Precio y envío',
                'attributes' => [
                    'step' => 'any',
                ],
                'wrapper' => [
                    'class' => 'form-group mb-3 col required',
                ],
            ]);

            CRUD::addField([
                'name' => 'depth',
                'label' => 'Largo',
                'type' => 'product.number_format',
                'suffix' => 'cm',
                'tab' => 'Precio y envío',
                'attributes' => [
                    'step' => 'any',
                ],
                'wrapper' => [
                    'class' => 'form-group mb-3 col required',
                ],
            ]);
        }

        if ($product->use_inventory_control) {
            CRUD::addField([
                'name' => 'critical_stock',
                'label' => 'Alertar cuando el stock este por debajo de',
                'type' => 'number',
                'tab' => 'Precio y envío',
            ]);
        }
    }

    public function setHousePricingFields()
    {
        CRUD::addField([
            'name' => 'housing_pricing',
            'label' => 'Precios por dia',
            'type' => 'product.table_with_types',
            'columns' => [
                'day' => 'Dia',
                'adults_price' => 'Precio por adulto',
                'childrens_price' => 'Precio por niño',
            ],
            'column_types' => [
                'day' => [
                    'type' => 'select',
                    'disabled' => true,
                    'options' => [
                        0 => 'Lunes',
                        1 => 'Martes',
                        2 => 'Miercoles',
                        3 => 'Jueves',
                        4 => 'Viernes',
                        5 => 'Sabado',
                        6 => 'Domingo',
                    ],
                ],
            ],
            'default' => [
                [ 'day' => 0, ],
                [ 'day' => 1, ],
                [ 'day' => 2, ],
                [ 'day' => 3, ],
                [ 'day' => 4, ],
                [ 'day' => 5, ],
                [ 'day' => 6, ],
            ],
            'min' => 7,
            'max' => 7,
            'tab' => 'Precio', 
        ]);
    }

    public function setTourFields()
    {
        CRUD::addField([
            'name' => 'tour_date',
            'label' => 'Fecha de salida',
            'type' => 'datetime_picker',
            'wrapper' => ['class' => 'form-group col-md-6'],
            'tab' => 'Características del Tour',
            'fake' => true,
            'store_in' => 'tour_information',
        ]);

        CRUD::addField([
            'name' => 'adults_price',
            'label' => 'Precio por adulto',
            'type' => 'number',
            'wrapper' => ['class' => 'form-group col-md-3'],
            'tab' => 'Características del Tour',
            'fake' => true,
            'store_in' => 'tour_information',
        ]);

        CRUD::addField([
            'name' => 'childrens_price',
            'label' => 'Precio por niño',
            'type' => 'number',
            'wrapper' => ['class' => 'form-group col-md-3'],
            'tab' => 'Características del Tour',
            'fake' => true,
            'store_in' => 'tour_information',
        ]);
    }

    public function setStatusVisibilityFields() {
        CRUD::addField([
            'name' => 'is_approved',
            'label' => 'Estado de aprobación',
            'type' => 'select2_from_array',
            'options' => [
                1 => 'Aprobado',
                2 => 'Pendiente',
                0 => 'Rechazado',
            ],
            'default' => null,
            'tab' => 'Administrador'
        ]);

        CRUD::addField([
            'name' => 'new',
            'label' => 'Nuevo',
            'type' => 'checkbox',
            'tab' => 'Administrador'
        ]);

        CRUD::addField([
            'name' => 'featured',
            'label' => 'Destacado',
            'type' => 'checkbox',
            'tab' => 'Administrador'
        ]);

        CRUD::addField([
            'name' => 'visible',
            'label' => 'Visible',
            'type' => 'checkbox',
            'tab' => 'Administrador'
        ]);

        CRUD::addField([
            'name' => 'visible_from',
            'label' => 'Visible desde',
            'type' => 'date',
            'tab' => 'Administrador',
            'wrapper' => [
                'class' => 'col-lg-6 col-md-6 col-sm-12 mb-3',
            ]
        ]);

        CRUD::addField([
            'name' => 'visible_to',
            'label' => 'Visible hasta',
            'type' => 'date',
            'tab' => 'Administrador',
            'wrapper' => [
                'class' => 'col-lg-6 col-md-6 col-sm-12 mb-3',
            ]
        ]);

        CRUD::addField([
            'name' => 'rejected_reason',
            'label' => 'Razón del rechazo',
            'type' => 'textarea',
            'tab' => 'Administrador',
            'wrapper' => [
                'style' => 'display:none',
                'id' => 'rejected_reason'
            ]
        ]);

        CRUD::addField([
            'name' => 'date_of_rejected',
            'label' => 'Fecha de rechazo',
            'type' => 'date',
            'tab' => 'Administrador',
            'wrapper' => [
                'style' => 'display:none',
                'id' => 'date_of_rejected',
            ]
        ]);

    }

    public function setSeoFields() {
        CRUD::addField([
            'name' => 'meta_title',
            'label' => 'Título para los buscadores',
            'type' => 'text',
            'tab' => 'Configuración SEO',
        ]);

        CRUD::addField([
            'name' => 'meta_keywords',
            'label' => 'Palabras clave (separadas por coma)',
            'type' => 'text',
            'tab' => 'Configuración SEO',
            'attributes' => [
                'placeholder' => 'Ejemplo: computadora, laptop, procesador',
            ],
        ]);

        CRUD::addField([
            'name' => 'meta_description',
            'label' => 'Descripción para los buscadores',
            'type' => 'textarea',
            'tab' => 'Configuración SEO',
        ]);

        CRUD::addField([
            'name' => 'update_custom_js',
            'type' => 'product.update_custom_js',
            'tab' => 'Configuración SEO',
        ]);
    }

    public function setTermnAndConditionFields()
    {
        CRUD::addField([
            'name' => 'terms_and_conditions',
            'label' => 'Términos y condiciones',
            'type' => 'wysiwyg',
            'tab' => 'Términos y condiciones',
        ]);
    }

    public function setAttributesFields($attributes, $product)
    {
        foreach($attributes as $attribute) {

            // Hide super attributes
            $hide = null;
            if($product->product_type->id == Product::PRODUCT_TYPE_CONFIGURABLE) {
                foreach($product->super_attributes as $super_attribute) {
                    if($super_attribute->id == $attribute->id) {
                       $hide = true;
                    }
                }
            }

            if($hide) continue;

            $json_attributes = $attribute->json_attributes;

            $currentValue = $product->custom_attributes->filter(function($data) use ($attribute) {
                return $data->product_class_attribute_id == $attribute->id;
            })->first();

            // Text field
            if($json_attributes['type_attribute'] == 'text') {
                $this->crud->addField([
                    'type' => 'text',
                    'label' => $json_attributes['name'],
                    'name' => 'attribute-' . $attribute->id,
                    'default' => $currentValue->json_value ?? '',
                    'tab' => 'Atributos adicionales',
                    'fake' => true,
                    'store_in' => 'attributes_json',
                ]);

            // Checkbox field
            } else if ($json_attributes['type_attribute'] == 'checkbox') {
                $this->crud->addField([
                    'type' => 'checkbox',
                    'label' => $json_attributes['name'],
                    'name' => 'attribute-' . $attribute->id,
                    'default' => $currentValue->json_value ?? '',
                    'tab' => 'Atributos adicionales',
                    'fake' => true,
                    'store_in' => 'attributes_json',
                ]);

            // Select field
            } else if ($json_attributes['type_attribute'] == 'select') {
                $options = $attribute->json_options;
                $formattedOptions = $this->formatOptions($options);

                // Add default empty option
                $formattedOptions = array_replace(['* No aplica' => '* No aplica'], $formattedOptions);
                $this->crud->addField([
                    'type' => 'select2_from_array',
                    'label' => $json_attributes['name'],
                    'name' => 'attribute-' . $attribute->id,
                    'options'     => $formattedOptions,
                    'default' => $currentValue->json_value ?? '',
                    'tab' => 'Atributos adicionales',
                    'fake' => true,
                    'store_in' => 'attributes_json',
                ]);
            }
        }
    }

    public function setVariationsField($product) {

        $superAttributesFields = [];

        foreach($product->super_attributes as $super_attribute) {
            array_push($superAttributesFields, [
                'label' => $super_attribute->json_attributes['name'],
                'name' => 'super-attribute-' . $super_attribute->id,
                'type' => 'select2_from_array',
                'options' => $this->formatOptions($super_attribute->json_options),
            ]);
        }

        // @todo posible vulnerabilidad. hay que verificar que el id no sea modificado manualmente
        $baseFields = [
            [
                'label' => "product_id",
                'name' => "product_id",
                'type' => 'number',
                'wrapper' => [
                    'style' => 'display:none',
                ]
            ],
            [
                'name' => 'image',
                'type' => 'image',
                'label' => 'Imagen',
                'hint' => 'Los formatos permitidos son PNG y JPG. Las dimensiones deben ser menores o iguales a 1.024 x 1.024 px',
                'aspect_ratio' => 1,
                'crop' => true,
            ],
            [
                'label' => "Precio",
                'name' => "price",
                'type' => 'product.number_format',
                'wrapper' => [
                    'class' => 'col-lg-3 col-md-12 form-group required',
                ],
                'attributes' => [
                    'step' => 'any',
                ],
            ],
            [
                'name' => 'special_price',
                'label' => 'Precio de oferta',
                'type' => 'product.number_format',
                'tab' => 'Precio y envío',
                'wrapper' => [
                    'class' => 'col-md-3 form-group'
                ],
                'attributes' => [
                    'step' => 'any',
                    'placeholder' => 'Opcional'
                ],
            ],
            [
                'name' => 'special_price_from',
                'label' => 'Precio de oferta desde',
                'type' => 'date',
                'tab' => 'Precio y envío',
                'wrapper' => [
                    'class' => 'col-md-3 form-group',
                    'id' => 'special_price_from',
                ]
            ],
            [
                'name' => 'special_price_to',
                'label' => 'Precio de oferta hasta',
                'type' => 'date',
                'tab' => 'Precio y envío',
                'wrapper' => [
                    'class' => 'col-md-3 form-group',
                    'id' => 'special_price_to',
                ]
            ]
        ];

        $isProductFields = $product->is_service ? [] : [
            [
                'label' => "Peso",
                'name' => "weight",
                'type' => 'product.number_format',
                'suffix' => 'kg',
                'wrapper' => [
                    'class' => 'col-md-3 form-group required',
                ],
                'attributes' => [
                    'step' => 'any',
                ],
            ],
            [
                'label' => "Alto",
                'name' => "height",
                'type' => 'product.number_format',
                'suffix' => 'cm',
                'wrapper' => [
                    'class' => 'col-lg col-md-12 col-sm-12 form-group required',
                ],
                'attributes' => [
                    'step' => 'any',
                ],
            ],
            [
                'label' => "Ancho",
                'name' => "width",
                'type' => 'product.number_format',
                'suffix' => 'cm',
                'wrapper' => [
                    'class' => 'col-lg col-md-12 col-sm-12 form-group required',
                ],
                'attributes' => [
                    'step' => 'any',
                ],
            ],
            [
                'label' => "Largo",
                'name' => "depth",
                'type' => 'product.number_format',
                'suffix' => 'cm',
                'wrapper' => [
                    'class' => 'col-lg col-md-12 col-sm-12 form-group required',
                ],
                'attributes' => [
                    'step' => 'any',
                ],
            ],
        ];

        $inventoryFields = $product->use_inventory_control ? $this->inventoryFieldsToArray($product) : [];

        CRUD::addField([
            'name'  => 'variations_json',
            'label' => 'Variaciones',
            'type'  => 'product.repeatable',
            'fields' => array_merge($baseFields, $isProductFields, $superAttributesFields, $inventoryFields),
            'new_item_label'  => 'Agregar una nueva variacion',
            'tab' => 'Variaciones',
        ]);
    }

    public function setInventoryFields($product) {
        $company = $product->company;

        // Only for validation purposes
        CRUD::addField([
            'label' => 'inventories',
            'name' => 'inventories_val',
            'type' => 'text',
            'tab' => 'Inventario',
            'wrapper' => [
                'style' => 'display:none',
            ],
        ]);

        foreach($company->inventory_sources as $inventory_source) {
            CRUD::addField([
                'label' => 'Inventario en ' . $inventory_source->name,
                'name' => 'inventory-source-' . $inventory_source->id,
                'type' => 'number',
                'default' => 0,
                'fake' => true,
                'tab' => 'Inventario',
                'store_in' => 'inventories_json',
                'wrapper' => [
                    'class' => 'form-group col-lg-12 required',
                ],
            ]);
        }
    }

    public function inventoryFieldsToArray($product) {
        $company = $product->company;
        $array = [];

        foreach($company->inventory_sources as $inventory_source) {
            array_push($array, [
                'label' => 'Inventario en ' . $inventory_source->name,
                'name' => 'inventory-source-' . $inventory_source->id,
                'type' => 'number',
                'default' => 0,
                'fake' => true,
                'store_in' => 'inventories_json',
                'wrapper' => [
                    'class' => 'col-md-12 form-group required',
                ],
            ]);
        }

        return $array;
    }


    /**
     * Given a array [0 => ['option_name' => 'the option'] ]
     * returns a array with format ['the option' => 'the option']
     *
     * TODO: This could be move to a general helper class
     */
    public function formatOptions($options) {
        $formattedOptions = [];
        foreach($options as $option) {
            $formattedOptions[$option['option_name']] = $option['option_name'];
        }
        return $formattedOptions;
    }

    /**
     * Get and filter a list of configurable attributes depending of the product class
     *
     */
    public function getProductBySeller(Request $request) {
        $search_term = $request->input('q');
        $form = collect($request->input('form'))->pluck('value', 'name');
        $options = Product::query();

        if ($request->has('keys')) {
            return Product::findMany($request->input('keys'));
        }

        if (! $form['seller_id']) {
            return [];
        }

        // Find attributes that are configurable and belong to the product class
        if ($form['seller_id']) {
            $options = $options->where('seller_id', $form['seller_id'])->where('price', '!=', 'null')->orderBy('name');
        }

        // Filter by search term
        if ($search_term) {
            $results = $options->whereRaw('LOWER(name) like ?', '%'.strtolower($search_term).'%')->paginate(10);
        } else {
            $results = $options->paginate(10);
        }
        return $options->paginate(10);
    }

    private function customFilters()
    {
        CRUD::addFilter([
            'type'  => 'text',
            'name'  => 'sku',
            'label' => 'SKU',
        ], false, function ($value) {
            $this->crud->addClause('where', 'sku', 'LIKE', '%' . $value . '%');
        });

        if ($this->admin) {
            CRUD::addFilter([
                'name'  => 'seller_id',
                'type'  => 'select2',
                'label' => 'Vendedor'
            ], function() {
                return Seller::all()->pluck('visible_name', 'id')->toArray();
            }, function($value) {
                $this->crud->addClause('where', 'seller_id', $value);
            });
        }

        CRUD::addFilter([
            'type'  => 'text',
            'name'  => 'name',
            'label' => 'Nombre',
        ], false, function ($value) {
            $this->crud->addClause('where', 'name', 'LIKE', '%' . $value . '%');
        });


        CRUD::addFilter([
            'name'  => 'product_type',
            'type'  => 'dropdown',
            'label' => 'Tipo de producto'
          ], [
            1 => 'Simple',
            2 => 'Configurable',
          ], function($value) {
            $this->crud->addClause('where', 'product_type_id', $value);
          });


        CRUD::addFilter([
            'name'  => 'category_id',
            'type'  => 'select2',
            'label' => 'Categoría'
        ], function() {
            return ProductCategory::all()->sortBy('name')->pluck('name', 'id')->toArray();
        }, function($value) {
            $this->crud->addClause('whereHas', 'categories', function($query) use ($value) {
                $query->where('id', $value);
            });
        });

        CRUD::addFilter([
            'name'  => 'is_approved',
            'type'  => 'dropdown',
            'label' => 'Estado de aprobación'
        ], [
            2 => 'Pendiente',
            0 => 'Rechazado',
            1 => 'Aprobado',
        ], function($value) {
            if ($value == 2) $value = null;
        $this->crud->addClause('where', 'is_approved', $value);
        });
    }

    public function bulkApprove()
    {
        $this->crud->hasAccessOrFail('update');

        $entries = $this->crud->getRequest()->input('entries');

        foreach ($entries as $key => $id) {
            if ($entry = $this->crud->model->find($id)) {
               $entry->is_approved = 1;
               $entry->update();
            }
        }

        return $entries;
    }

    public function bulkReject()
    {
        $this->crud->hasAccessOrFail('update');

        $entries = $this->crud->getRequest()->input('entries');

        foreach ($entries as $key => $id) {
            if ($entry = $this->crud->model->find($id)) {
               $entry->is_approved = 0;
               $entry->date_of_rejected = new DateTime();
               $entry->update();
            }
        }

        return $entries;
    }

    public function bulkDelete()
    {
        $this->crud->hasAccessOrFail('delete');

        $entries = $this->crud->getRequest()->input('entries');

        foreach ($entries as $key => $id) {
            if ($entry = $this->crud->model->find($id)) {
               $entry->delete();
            }
        }

        return $entries;
    }
}
