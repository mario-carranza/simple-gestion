<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\ProductReservation;
use App\Http\Livewire\Products\Product;
use App\Http\Requests\ProductReservationRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ProductReservationCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ProductReservationCrudController extends CrudController
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
        CRUD::setModel(\App\Models\ProductReservation::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/productreservation');
        CRUD::setEntityNameStrings('solicitud de reserva', 'solicitudes de reserva');

        $this->crud->denyAccess(['show', 'create']);
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->addButtonFromView('line', 'change_status', 'product_reservation.change_status', 'beginning');
        
        $this->crud->addButtonFromView('bottom', 'modal_status', 'product_reservation.modal_status', 'begining');
       
        $this->crud->setActionsColumnPriority(0);

        CRUD::addColumn([
            'name' => 'created_at',
            'label' => 'Fecha',
            'type' => 'datetime',
            'format' => 'l',
            'priority' => 0,
        ]);

        CRUD::addColumn([
            'name' => 'product.name',
            'label' => 'Servicio',
            'priority' => 0,
        ]);

        CRUD::addColumn([
            'name' => 'name',
            'label' => 'nombre',
            'priority' => 1,
        ]);

        CRUD::addColumn([
            'name' => 'email',
            'label' => 'Email',
            'priority' => 10,
        ]);

        CRUD::addColumn([
            'name' => 'check_in_date',
            'label' => 'Fecha de check-in',
            'type' => 'datetime',
            'format' => 'l',
            'priority' => 0,
        ]);

        CRUD::addColumn([
            'name' => 'check_out_date',
            'label' => 'Fecha de check-out',
            'type' => 'datetime',
            'format' => 'l',
            'priority' => 0,
        ]);

        CRUD::addColumn([
            'name' => 'adults_number',
            'label' => 'Numero de adultos',
            'priority' => 10,
        ]);

        CRUD::addColumn([
            'name' => 'childrens_number',
            'label' => 'Numero de niños',
            'priority' => 10,
        ]);

        CRUD::addColumn([
            'name' => 'price',
            'label' => 'Precio',
            'priority' => 0,
            'type' => 'number',
            'decimals' => 0,
            'thousands_sep' => '.',
            'prefix' => '$ ',
        ]);

        CRUD::addColumn([
            'name' => 'reservation_status_text',
            'label' => 'Estado',
            'priority' => 0,
            'wrapper' => [
                'element' => 'span',
                'class' => function ($crud, $column, $entry, $related_key) {
                    if ($column['text'] == 'Pagada') {
                        return 'badge badge-success';
                    }
                    if ($column['text'] == 'Aceptada') {
                        return 'badge badge-info';
                    }

                    if ($column['text'] == 'Rechazada' || $column['text'] == 'Cancelada') {
                        return 'badge badge-danger';
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
        CRUD::setValidation(ProductReservationRequest::class);

        $reservation = ProductReservation::find($this->crud->getCurrentEntryId());

        CRUD::addField([
            'name' => 'created_at',
            'label' => 'Fecha de solicitud',
            'type' => 'date',
            'wrapper' => ['class' => 'form-group col-md-6'],
            'attributes' => ['readonly' => true],
        ]);

        CRUD::addField([
            'name' => 'product',
            'label' => 'Servicio',
            'type' => 'relationship',
            'wrapper' => ['class' => 'form-group col-md-6'],
            'attributes' => ['readonly' => true],
        ]);

        CRUD::addField([
            'name' => 'name',
            'label' => 'Nombre',
            'type' => 'text',
            'wrapper' => ['class' => 'form-group col-md-4'],
            'attributes' => ['readonly' => true],
        ]);

        CRUD::addField([
            'name' => 'email',
            'label' => 'Email',
            'type' => 'text',
            'wrapper' => ['class' => 'form-group col-md-4'],
            'attributes' => ['readonly' => true],
        ]);

        CRUD::addField([
            'name' => 'celphone',
            'label' => 'Teléfono celular',
            'type' => 'text',
            'wrapper' => ['class' => 'form-group col-md-4'],
            'attributes' => ['readonly' => true],
        ]);

        CRUD::addField([
            'name' => 'check_in_date_only_date',
            'label' => 'Fecha de Check In',
            'type' => 'date',
            'wrapper' => ['class' => 'form-group col-md-3'],
            'attributes' => ['readonly' => true],
        ]);

        CRUD::addField([
            'name' => 'check_out_date_only_date',
            'label' => 'Fecha de Check Out',
            'type' => 'date',
            'wrapper' => ['class' => 'form-group col-md-3'],
            'attributes' => ['readonly' => true],
        ]);

        CRUD::addField([
            'name' => 'adults_number',
            'label' => 'Número de adultos',
            'type' => 'text',
            'wrapper' => ['class' => 'form-group col-md-3'],
            'attributes' => ['readonly' => true],
        ]);

        CRUD::addField([
            'name' => 'childrens_number',
            'label' => 'Número de niños',
            'type' => 'text',
            'wrapper' => ['class' => 'form-group col-md-3'],
            'attributes' => ['readonly' => true],
        ]);

        CRUD::addField([
            'name' => 'price',
            'label' => 'Precio',
            'type' => 'text',
            'wrapper' => ['class' => 'form-group col-md-6'],
            'value' => currencyFormat($reservation->price, 'CLP', true),
            'attributes' => ['disabled' => true],
        ]);

        CRUD::addField([
            'name' => 'reservation_status',
            'label' => 'Estado de las reserva',
            'type' => 'select2_from_array',
            'wrapper' => ['class' => 'form-group col-md-6'],
            'options' => ProductReservation::STATUS_DICTIRONARY,
            'attributes' => ['readonly' => true],
        ]);

        CRUD::addField([
            'name' => 'customer_comment',
            'label' => 'Comentario del cliente',
            'type' => 'textarea',
            'attributes' => ['readonly' => true],
        ]);

        CRUD::addField([
            'name' => 'seller_comment',
            'label' => 'Comentario del vendedor',
            'type' => 'textarea',
            'attributes' => ['readonly' => true],
        ]);

        CRUD::addField([
            'name' => 'custom_styles',
            'type' => 'product_reservation.custom_styles'
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

    public function changeStatusView($id)
    {
        $data['productReservation'] = ProductReservation::find($id);

        return view('product-reservation.change-status', $data);
    }

    public function changeStatus(Request $request, $id)
    {
        $reservation = ProductReservation::find($id);

        $reservation->reservation_status = $request->reservation_status;

        $reservation->seller_comment = $request->seller_comment;

        $reservation->update();

        return true;
    }
}
