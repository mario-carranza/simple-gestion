<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\FaqAnswerRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class FaqAnswerCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class FaqAnswerCrudController extends CrudController
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
        CRUD::setModel(\App\Models\FaqAnswer::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/faqanswer');
        CRUD::setEntityNameStrings('pregunta frecuente', 'preguntas frecuentes');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        /* CRUD::setFromDb(); // columns */

        CRUD::addColumn([
            'label' => 'Pregunta',
            'name' => 'question',
        ]);

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
        CRUD::setValidation(FaqAnswerRequest::class);

        CRUD::addField([
            'name' => 'question',
            'label' => 'Pregunta',
        ]);

        CRUD::addField([
            'name' => 'answer',
            'label' => 'Respuesta',
        ]);

        CRUD::addField([
            'name' => 'faq_topic',
            'label' => 'Topico',
            'placeholder' => 'Seleccionar un topico',
        ]);

        CRUD::addField([
            'name' => 'slug',
            'label' => 'Slug',
        ]);

        CRUD::addField([
            'name' => 'status',
            'label' => 'Activa',
            'type' => 'checkbox',
            'default' => 1,
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
