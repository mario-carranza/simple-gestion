<?php

namespace App\Http\Controllers\Admin;

use App\Models\Plans;
use App\Cruds\BaseCrudFields;
use App\Http\Requests\ProductCategoryRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ProductCategoryCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ProductCategoryCrudController extends CrudController
{

    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\ProductCategory::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/productcategory');
        CRUD::setEntityNameStrings('categoría de producto', 'categorías de producto');
        $this->crud->denyAccess('show');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::addColumn([
            'name' => 'name',
            'label' => 'Nombre',
            'type' => 'text',
        ]);

        CRUD::addColumn([
            'name' => 'code',
            'label' => 'Codigo',
            'type' => 'text',
        ]);

        CRUD::addColumn([
            'name' => 'position',
            'label' => 'Posición',
            'type' => 'number',
        ]);

        CRUD::addColumn([
            'name' => 'parent',
            'type' => 'relationship',
            'label' => 'Categoría padre',
        ]);
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ProductCategoryRequest::class);

        $this->crud = (new BaseCrudFields())->setBaseFields($this->crud);

        CRUD::addField([
            'name' => 'name',
            'label' => 'Nombre',
            'type' => 'text',
            'tab' => 'General',
        ]);

        CRUD::addField([
            'name' => 'slug',
            'label' => 'Slug',
            'type' => 'text',
            'tab' => 'General',
        ]);

        CRUD::addField([
            'name' => 'code',
            'label' => 'Codigo',
            'type' => 'text',
            'tab' => 'General',
        ]);

        CRUD::addField([
            'label'   => "Icono",
            'name'    => 'icon',
            'type'    => 'icon_picker',
            'iconset' => 'fontawesome', // options: fontawesome, glyphicon, ionicon, weathericon, mapicon, octicon, typicon, elusiveicon, materialdesign
            'tab' => 'General',
            'wrapper' => [
                'class' => 'form-group col-md-3',
            ],
        ]);

        CRUD::addField([
            'name' => 'image',
            'type' => 'image',
            'label' => 'Imagen',
            'crop' => true,
            'tab' => 'General',
            'wrapper' => [
                'class' => 'form-group col-md-3',
            ],
        ]);

        CRUD::addField([
            'name' => 'featured_image',
            'type' => 'image',
            'label' => 'Imagen destacada',
            'tab' => 'General',
            'wrapper' => [
                'class' => 'form-group col-md-3',
            ],
        ]);

        CRUD::addField([
            'name' => 'position',
            'label' => 'Posición',
            'type' => 'number',
            'tab' => 'General',
        ]);

        CRUD::addField([  // TO DO: Maked nested
            'label'     => "Categoría padre",
            'type'      => 'select2',
            'name'      => 'parent_id',
            'entity'    => 'parent',
            'model'     => "App\Models\ProductCategory",
            'attribute' => 'name',
            'tab' => 'General',
         ]);

         CRUD::addField([
            'name'        => 'display_mode',
            'label'       => "Modo de visualizacion",
            'type'        => 'select2_from_array',
            'options'     => ['products_and_description' => 'Productos y descripcion'],
            'allows_null' => false,
            'default'     => 'products_and_description',
            'tab' => 'General',
         ]);

        CRUD::addField([
            'name' => 'status',
            'label' => 'Estado',
            'type' => 'checkbox',
            'default' => 1,
            'tab' => 'General',
        ]);

        CRUD::addField([
            'name' => 'slug_formatter',
            'type' => 'slug_formatter',
            'origen' => 'name',
            'slug' => 'slug',
            'tab' => 'General',
        ]);
        CRUD::addField([
            'name' => 'commission',
            'type' => 'repeatable',
            'label' => 'Comisiones',
            'new_item_label'  => 'Agregar comision',
            'default' => '{}',
            'fields' => [
                [
                    'name' => 'plan_id',
                    'type' => 'select2_from_array',
                    'options' => Plans::orderBy('name', 'asc')->pluck('name', 'id')->toArray(),
                    'label' => 'Plan',
                    'wrapper' => [
                        'class' => 'form-group col-12'
                    ],
                ],
                [
                    'name' => 'commission',
                    'type'  => 'text',
                    'label' => 'Comisión',
                    'wrapper' => [
                        'class' => 'form-group col-12'
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
                ]
            ],
            'tab' => 'Comisiones',
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
        $this->setupCreateOperation();
    }
}
