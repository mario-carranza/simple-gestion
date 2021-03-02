<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\PriceList;
use Illuminate\Http\Request;
use App\Cruds\BaseCrudFields;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\PriceListRequest;
use App\Exports\PriceList\PriceListExport;
use App\Http\Requests\PriceListUpdateRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class PriceListCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PriceListCrudController extends CrudController
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
        CRUD::setModel(\App\Models\PriceList::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/pricelist');
        CRUD::setEntityNameStrings('lista de precio', 'listas de precios');

        $this->crud->allowAccess('modify');
        $this->crud->allowAccess('export');
        $this->crud->allowAccess('apply');
        $this->crud->denyAccess('update');
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
        $this->crud->addButtonFromView('line', 'modify', 'pricelist.modify', 'end');
        $this->crud->addButtonFromView('line', 'apply', 'pricelist.apply', 'end');
        $this->crud->addButtonFromView('line', 'export', 'pricelist.export', 'end');

        CRUD::addColumn([
            'label' => 'Fecha',
            'name' => 'created_at',
            'type' => 'date',
            'format' => 'L'
        ]);

        CRUD::addColumn([
            'label' => 'Nombre',
            'name' => 'name',
        ]);

        CRUD::addColumn([
            'label' => 'Codigo',
            'name' => 'code',
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
        CRUD::setValidation(PriceListRequest::class);

        $this->crud = (new BaseCrudFields())->setBaseFields($this->crud);

        CRUD::addField([
            'name' => 'name',
            'label' => 'Nombre',
            'wrapper' => [
                'class' => 'col-md-6 form-group',
            ],
        ]);

        CRUD::addField([
            'name' => 'code',
            'label' => 'Codigo',
            'wrapper' => [
                'class' => 'col-md-6 form-group',
            ],
        ]);

        CRUD::addField([   // radio
            'name'        => 'initial_price', // the name of the db column
            'label'       => 'Precio inicial de cada producto', // the input label
            'fake' => true,
            'store_in' => 'initial_options',
            'type'        => 'radio',
            'options'     => [
                // the key will be stored in the db, the value will be shown as label; 
                'actual_price' => "Precio actual del producto",
                'price_with_surcharge' => "Precio actual mas recargo",
                'price_with_discount' => "Precio actual menos descuento",
            ],
            'hint' => 'Al crear una lista de precios, se agregara a ella cada uno de tus productos con un precio. Puedes elegir utilizar el precio actual del producto, o utilizar el precio agregando un recargo o descuento.',
            // optional
            //'inline'      => false, // show the radios all on the same line?
            'wrapper' => [
                'class' => 'col-md-6 form-group',
            ],
        ]);

        CRUD::addField([
            'name' => 'price_surcharge_percentage',
            'label' => 'Porcentaje de recargo a aplicar',
            'fake' => true,
            'store_in' => 'initial_options',
            'type' => 'number',
            'suffix' => '%',
            'wrapper' => [
                'class' => 'col-md-6 form-group',
                'style' => 'display:none',
                'id' => 'surcharge_percentage',
            ],
        ]);

        CRUD::addField([
            'name' => 'price_discount_percentage',
            'fake' => true,
            'store_in' => 'initial_options',
            'label' => 'Porcentaje de descuento a aplicar',
            'type' => 'number',
            'suffix' => '%',
            'wrapper' => [
                'class' => 'col-md-6 form-group',
                'style' => 'display:none',
                'id' => 'discount_percentage',
            ],
        ]);

        CRUD::addField([
            'name' => 'price_list_custom_js',
            'type' => 'price_list.custom_js',
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

    protected function modify($id)
    {
        $priceList = PriceList::findOrFail($id);

        return view('vendor.backpack.crud.price_list.modify', compact('priceList'));
    }

    protected function setupModifyRoutes($segment, $routeName, $controller)
    {
        \Route::get($segment.'/{id}/modify', [
            'as'        => $routeName.'.modify',
            'uses'      => $controller.'@modify',
            'operation' => 'modify',
        ]);
    }

    protected function setupApplyRoutes($segment, $routeName, $controller)
    {
        \Route::get($segment.'/{id}/apply', [
            'as'        => $routeName.'.apply',
            'uses'      => $controller.'@apply',
            'operation' => 'apply',
        ]);
    }

    protected function setupExportRoutes($segment, $routeName, $controller)
    {
        \Route::get($segment.'/{id}/export', [
            'as'        => $routeName.'.export',
            'uses'      => $controller.'@export',
            'operation' => 'export',
        ]);
    }

    public function apply($id)
    {
        $priceList = PriceList::with('priceListItems.product')->findOrFail($id);

        foreach ($priceList->priceListItems as $item) {
            $product = $item->product;

            // If the product is a child product, update the field
            // variations_json on the parent to keep the json sync
            if ($product->parent_id) {
                $variations = collect($product->parent->variations_json);
                $variations = $variations->map( function ($child) use ($product, $item) {
                    if ($child['product_id'] == $product->id) $child['price'] = $item->price ? number_format($item->price, 2, ',', '') : null;
                    return $child;
                });
                $product->parent->variations_json = $variations;
                $product->parent->update();
            }

            $product->price = $item->price ? (float) $item->price : null;
            $product->cost = $item->cost ? (float) $item->cost : null;
            $product->update();
        }

        return 1;
    }

    public function export($id)
    {
        $priceList = PriceList::findOrFail($id);
        $date = now();
        $fileName = strtolower(str_replace(' ', '_', $priceList->name)) . '_' . $date->format('Y-m-d') . '.xls';
        $priceListExport = new PriceListExport(backpack_user()->current()->company->id, $id);
        return Excel::download($priceListExport, $fileName);
    }

    public function getProducts($priceListId)
    {
        $priceList = PriceList::with('priceListItems.product')->findOrFail($priceListId);

        $products = $priceList->priceListItems->map(function ($item) {
            return [
                'id' => $item->product->id,
                'sku' => $item->product->sku,
                'name' => $item->product->name,
                'price' => $item->price,
                'cost' => $item->cost,
            ];
        });

        return $products;
    }

    public function updatePriceList(PriceListUpdateRequest $request, $id)
    {
        // @todo validacioonse

        $priceList = PriceList::with('priceListItems')->findOrFail($id);
        $priceList->name = $request->name;
        $priceList->code = $request->code;

        foreach ($request->products as $product) {
            $item = $priceList->priceListItems->where('product_id', $product['id'])->first();
            $item->price = $product['price'];
            $item->cost = $product['cost'];
            $item->update();
        } 

        $priceList->update();

        return response()->json([
            'status' => true,
            'message' => 'Lista de precios actualizada con exito',
        ], 200);
    }
}