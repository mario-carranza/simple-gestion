<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SectionRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class SectionCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class SectionCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Section::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/section');
        CRUD::setEntityNameStrings('section', 'sections');
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
        ]);

        CRUD::addColumn([
            'name' => 'slug',
            'label' => 'Slug',
        ]);

        CRUD::addColumn([
            'name' => 'status',
            'label' => 'Estado',
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
        CRUD::setValidation(SectionRequest::class);

        CRUD::addField([
            'name' => 'name',
            'label' => 'Nombre',
            'type' => 'text',
        ]);

        CRUD::addField([
            'name' => 'slug',
            'label' => 'Slug',
            'type' => 'text',
        ]);

        CRUD::addField([
            'name' => 'view_path',
            'label' => 'Plantilla',
            'type' => 'select2_from_array',
            'options' => [
                'default' => 'General',
                'travel'  => 'Turismo',
            ]
        ]);

        CRUD::addField([
            'name' => 'product_categories',
            'label' => 'Categorias',
            'type' => 'relationship',
        ]);

        CRUD::addField([
            'name' => 'status',
            'label' => 'Activa',
            'type' => 'checkbox',
            'default' => true,
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
