<?php

namespace App\Http\Controllers\Admin;

use App\Models\TimeBlock;
use Illuminate\Http\Request;
use App\Cruds\BaseCrudFields;
use App\Http\Requests\TimeBlockRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class TimeBlockCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class TimeBlockCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\InlineCreateOperation;
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
        CRUD::setModel(\App\Models\TimeBlock::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/timeblock');
        CRUD::setEntityNameStrings('bloque horario', 'bloques horarios');

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
        $this->customFilters();

        CRUD::addColumn([
            'name' => 'name',
            'label' => 'Nombre',
        ]);

        CRUD::addColumn([
            'name' => 'start_time',
            'label' => 'Hora de inicio',
        ]);

        CRUD::addColumn([
            'name' => 'end_time',
            'label' => 'Hora de fin',
        ]);

        CRUD::addColumn([
            'name' => 'status_description',
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
        CRUD::setValidation(TimeBlockRequest::class);

        $this->crud = (new BaseCrudFields())->setBaseFields($this->crud);

        CRUD::addField([
            'name' => 'name',
            'label' => 'Nombre',
            'type' => 'text',
        ]);

        CRUD::addField([
            'name' => 'code',
            'label' => 'Codigo',
            'type' => 'text',
            'default' => generateUniqueModelCodeAttribute(TimeBlock::class, auth()->user()->companies()->first()->id)
        ]);
        
        CRUD::addField([
            'name' => 'start_time',
            'label' => 'Hora de inicio',
        ]);

        CRUD::addField([
            'name' => 'end_time',
            'label' => 'Hora de fin',
        ]);

        CRUD::addField([
            'name' => 'status',
            'label' => 'Activo',
            'type' => 'checkbox',
            'default' => '1',
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

    public function getTimeblocksByService(Request $request) {
        $search_term = $request->input('q');
        $form = collect($request->input('form'))->pluck('value', 'name');
        $options = TimeBlock::query();

        if (! $form['service_id']) {
            return [];
        }

        if ($form['service_id']) {
            $options = $options->wherehas('services', function ($query) use ($form) {
                return $query->where('id',  $form['service_id']);
            });
        }

        if ($search_term) {
            $results = $options->whereRaw('name like ?', '%'.strtolower($search_term).'%')->paginate(10);
        } else {
            $results = $options->paginate(10);
        }

        return $options->paginate(10);
    }

    private function customFilters()
    {
        CRUD::addFilter([
            'type'  => 'select2',
            'name'  => 'name',
            'label' => 'Nombre',
        ], function() {
            return TimeBlock::all()->pluck('name', 'id')->toArray();
        }, function($value) {
            $this->crud->addClause('where', 'id', $value);
        });

        CRUD::addFilter([
            'name'  => 'status',
            'type'  => 'dropdown',
            'label' => 'Estado'
        ], [
            0 => 'Inactivo',
            1 => 'Activo',
        ], function($value) {
            $this->crud->addClause('where', 'status', $value);
        });
    }
}