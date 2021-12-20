<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Cart;
use App\Models\Seller;
use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Http\Request;
use App\Models\ProductReservation;
use Prologue\Alerts\Facades\Alert;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\ProductReservationChangeStatus;
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

    private $admin;
    private $userSeller;

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

        $this->admin = false;
        $this->userSeller = null;

        if (backpack_user()) {
            if (backpack_user()->can('product_reservation.admin')) {
                $this->admin = true;
            } else {
                $this->userSeller = Seller::where('user_id', backpack_user()->id)->firstOrFail();
            }
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
        $this->crud->addButtonFromView('line', 'change_status', 'product_reservation.change_status', 'beginning');
        
        $this->crud->addButtonFromView('line', 'view_order', 'product_reservation.view_order', 'beginning');
        
        $this->crud->addButtonFromView('bottom', 'modal_status', 'product_reservation.modal_status', 'begining');
       
        $this->crud->setActionsColumnPriority(0);

        if (!$this->admin) {
            $this->crud->addClause('whereHas', 'product', function ($q) {
                return $q->where('seller_id', $this->userSeller->id);
            });
        }

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
            'name' => 'type_text',
            'label' => 'Tipo',
            'priority' => 1,
        ]);

        CRUD::addColumn([
            'name' => 'name',
            'label' => 'Nombre',
            'priority' => 1,
        ]);

        CRUD::addColumn([
            'name' => 'email',
            'label' => 'Email',
            'priority' => 10,
        ]);

        CRUD::addColumn([
            'name' => 'check_in_date',
            'label' => 'Check In',
            'type' => 'product_reservations.datetime',
            'format' => 'l',
            'priority' => 0,
        ]);

        CRUD::addColumn([
            'name' => 'check_out_date',
            'label' => 'Check Out',
            'type' => 'product_reservations.datetime',
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

        $this->setupFilters();
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
            'name' => 'cellphone',
            'label' => 'Teléfono celular',
            'type' => 'text',
            'wrapper' => ['class' => 'form-group col-md-4'],
            'attributes' => ['readonly' => true],
        ]);

        if ($reservation->type === 'tour') {
            CRUD::addField([
                'name' => 'check_in_date',
                'label' => 'Fecha de la experiencia',
                'type' => 'datetime',
                'wrapper' => ['class' => 'form-group col-md-6'],
                'attributes' => ['readonly' => true],
            ]);
        } else {
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
        }

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

    protected function setupFilters()
    {
        $this->crud->addFilter(
            [
        'type'  => 'date_range',
        'name'  => 'created_at',
        'label' => 'Fecha'
        ],
            false,
            function ($value) { // if the filter is active, apply these constraints
                $dates = json_decode($value);
                $this->crud->addClause('where', 'created_at', '>=', $dates->from);
                $this->crud->addClause('where', 'created_at', '<=', $dates->to . ' 23:59:59');
            }
        );

        CRUD::addFilter([
            'name'  => 'product',
            'type'  => 'select2',
            'label' => 'Servicio'
        ], function () {
            return Product::where('is_housing', true)->orWhere('is_tour', true)->when(! $this->admin, function ($q) {
                return $q->where('seller_id', $this->userSeller->id);
            })->get()->pluck('name', 'id')->toArray();
        }, function ($value) {
            $this->crud->addClause('where', 'product_id', $value);
        });

        CRUD::addFilter([
            'name'  => 'type',
            'type'  => 'dropdown',
            'label' => 'Tipo'
        ], function () {
            return [
                'housing' => 'Alojamiento',
                'tour' => 'Tour',
            ];
        }, function ($value) {
            $this->crud->addClause('where', 'type', $value);
        });

        $this->crud->addFilter(
            [
            'type'  => 'date_range',
            'name'  => 'check_in',
            'label' => 'Check In'
          ],
            false,
            function ($value) { // if the filter is active, apply these constraints
                $dates = json_decode($value);
                $this->crud->addClause('where', 'check_in_date', '>=', $dates->from);
                $this->crud->addClause('where', 'check_in_date', '<=', $dates->to . ' 23:59:59');
            }
        );

        $this->crud->addFilter(
            [
        'type'  => 'date_range',
        'name'  => 'check_out',
        'label' => 'Check Out'
        ],
            false,
            function ($value) { // if the filter is active, apply these constraints
                $dates = json_decode($value);
                $this->crud->addClause('where', 'check_out_date', '>=', $dates->from);
                $this->crud->addClause('where', 'check_out_date', '<=', $dates->to . ' 23:59:59');
            }
        );

        CRUD::addFilter([
            'name'  => 'status',
            'type'  => 'dropdown',
            'label' => 'Estado'
        ], function () {
            return ProductReservation::STATUS_DICTIRONARY;
        }, function ($value) {
            $this->crud->addClause('where', 'reservation_status', $value);
        });
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

        if (
            $reservation->reservation_status === ProductReservation::ACCEPTED_STATUS
            || $reservation->reservation_status === ProductReservation::REJECTED_STATUS
        ) {
            try {
                Mail::to($reservation->email)
                    ->send(new ProductReservationChangeStatus($reservation, 'customer', $reservation->reservation_status));
            } catch (\Throwable $th) {
                Log::error('No se puedo enviar el correo', [
                    'error' => $th->getMessage(),
                    'stacktrace' => $th->getTraceAsString(),
                ]);

                Alert::add('warning', 'Ocurrio un problema enviando el correo de confirmación al cliente')->flash();
            }
        }

        return true;
    }

    public function addReservationToCart($hash)
    {
        $productReservation = ProductReservation::where('hash', $hash)->firstOrFail();

        $product = $productReservation->product;

        if ($productReservation->reservation_status !== ProductReservation::ACCEPTED_STATUS) {
            abort(404);
        }

        // Get cart
        $session = session()->getId();
        $user = auth()->check() ? auth()->user() : null;
        $cart = Cart::getInstance($user, $session);
        $cart->save();

        $cart->cart_items()->where('product_id', $product->id)->delete();

        $data = [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'sku' => $product->sku,
            'name' => $product->name,
            'price' => $productReservation->price,
            'qty' => 1,
            'sub_total' => $productReservation->price,
            'total' => $productReservation->price,
            'currency_id' => $product->currency_id,
            'product_reservation_id' => $productReservation->id,
        ];

        if ($product->parent_id) {
            $attributes = [];
            foreach ($product->getAttributesWithNames() as $key) {
                $attributes[] = [
                    $key['name'] => $key['value']
                ];
            }
            $data = array_merge($data, ['product_attributes' => json_encode($attributes)]);
        }

        if ($product->is_housing) {
            $data['product_attributes'] = json_encode([
                ['Fecha de Check In' => $productReservation->check_in_date->format('d/m/Y')],
                ['Fecha de Check Out' => $productReservation->check_out_date->format('d/m/Y')],
                ['Numero de adultos' => $productReservation->adults_number],
                ['Numero de niños' => $productReservation->childrens_number],
            ]);
        } elseif ($product->is_tour) {
            $data['product_attributes'] = json_encode([
                ['Fecha y hora' => Carbon::parse($productReservation->check_in_date)->format('d/m/Y h:i a ')],
                ['Numero de adultos' => $productReservation->adults_number],
                ['Numero de niños' => $productReservation->childrens_number],
            ]);
        }

        $item = CartItem::create($data);

        $cart->items_count++;

        $cart->recalculateSubtotal();

        $cart->recalculateQtys();

        $cart->update();

        return redirect()->route('shopping-cart');
    }
}
