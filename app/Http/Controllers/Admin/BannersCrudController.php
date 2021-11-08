<?php

namespace App\Http\Controllers\Admin;

use App\Traits\HasCustomAttributes;
use App\Http\Requests\BannersRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
/**
 * Class BannersCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class BannersCrudController extends CrudController
{
    use HasCustomAttributes;
    private $admin;


    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Banners::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/banners');
        CRUD::setEntityNameStrings('banners', 'banners');
        $this->crud->denyAccess('create');
        $this->crud->denyAccess('delete');

       $this->admin = false;
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
       // CRUD::setFromDb(); // columns

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
        CRUD::addColumn([
            'name' => 'name',
            'type' => 'text',
            'label' => 'Nombre',
        ]);

        CRUD::addColumn([
            'name' => 'path_web',
            'type' => 'text',
            'label' => 'Ruta Banner Web',
        ]);

        CRUD::addColumn([
            'name' => 'path_mobile',
            'type' => 'text',
            'label' => 'Ruta Banner Móvil',
        ]);

        CRUD::addColumn([
            'name' => 'status_description',
            'type' => 'text',
            'label' => 'Estado',
            'wrapper' => [
                'element' => 'span',
                'class' => function ($crud, $column, $entry, $related_key) {
                    if ($column['text'] == 'Activo') {
                        return 'badge badge-success';
                    }
                    return 'badge badge-default';
                },
            ],
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
        CRUD::setValidation(BannersRequest::class);

        //CRUD::setFromDb(); // fields

        CRUD::addField([
            'name' => 'name',
            'type'  => 'text',
            'label' => 'Nombre',
        ]);

        CRUD::addField([
            'name' => 'path_web',
            'type' => 'image',
            'label' => 'Banner Web (Recomendable: 1350x180)',
            'crop' => true,
            'wrapper' => [
                'class' => 'form-group col-md-6'
            ],
        ]);

        CRUD::addField([
            'name' => 'path_mobile',
            'type' => 'image',
            'label' => 'Banner Mobile (Recomendable: 350x150)',
            'crop' => true,
            'wrapper' => [
                'class' => 'form-group col-md-6'
            ],
        ]);
        CRUD::addField([
            'name'            => 'section',
            'label'           => "Sección",
            'type'            => 'select_from_array',
            'options'         => ['1' => '1', '2' => '2', '3' => '3', '4'=>'4'],
            'allows_null'     => false,
            'allows_multiple' => false,
            'wrapperAttributes' => [
                'class' => 'form-group col-md-12',
            ],
        ]);

        CRUD::addField([
            'name' => 'status',
            'type' => 'checkbox',
            'label' => 'Activo',
            'default' => 1,
            'wrapperAttributes' => [
                'class' => 'form-group col-md-12',
            ],
        ]);

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
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
