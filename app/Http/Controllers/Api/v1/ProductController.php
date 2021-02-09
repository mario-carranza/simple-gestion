<?php

namespace App\Http\Controllers\Api\v1;

use DateTime;
use Exception;
use App\Models\Seller;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\OrderItem;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ProductInventory;
use App\Services\ProductService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductClassAttribute;
use App\Models\ProductInventorySource;
use Illuminate\Database\QueryException;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Api\ProductRequest;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    private $productService;
    

    public function show(Request $request)
    {
        $product = Product::with('categories')  
                        ->with('brand')
                        ->with('product_class')
                        ->with('inventories') 
                        ->find($request['id']);

        if (!$product) return response()->json([ 
            'status' => 'error', 
            'message' => 'El producto indicado no existe'
        ],  404);
        
        return response()->json([
            'status' => 'success',
            'data' => $product,
        ], 200);
    }

    public function showBySku(Request $request)
    {
        $product = Product::with('categories')
                        ->with('brand')
                        ->with('product_class')
                        ->with('inventories')                
                        ->where('sku', $request['sku'])->first();

        if (!$product) return response()->json([
            'status' => 'error', 
            'message' => 'El SKU del producto indicado no existe'
        ],  404);
        
        return response()->json([
            'status' => 'success',
            'data' => $product,
        ], 200);
    }

    public function delete($warehouseCode, $sku)
    {
        $messages = [
            '*.exists' => 'El valor de :attribute no se encuentra en la base de datos',
        ];

        $validator = Validator::make(['sku' => $sku, 'warehouse' => $warehouseCode], [ 
            'sku' => 'required|exists:products,sku',
            'warehouse' => 'required|exists:product_inventory_sources,code',
        ], $messages);
      
        if ($validator->fails()) {
          return response()->json([ 'status' => 'error', 'message' => $validator->errors() ], 400);
        }

        /**
         * NOTE: This may fail when there is two seller with the same product SKU in the same warehouse
         * 
         */
        $warehouse = ProductInventorySource::where('code', $warehouseCode)->first();

        $productInventory = ProductInventory::where('product_inventory_source_id', $warehouse->id)
                                ->whereHas('product', function ($query) use ($sku)  {
                                   return $query->where('sku', $sku);
                                })->first();

        if (!$productInventory) {
            return response()->json([ 
                'status' => 'error', 
                'message' => 'La bodega no contiene el producto con el SKU indicado',
            ],  404);
        };

        $product = Product::find($productInventory->product_id);

        try {
            $product->delete();
        } catch(QueryException $e) {
            return response()->json([ 'status' => 'error', 'message' => $exception ], 400);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Producto eliminado',
            'data' => $product,
        ], 200);
    }


    public function showBySku(Request $request)
    {
        $product = Product::with('categories')
                        ->with('brand')
                        ->with('product_class')
                        ->with('inventories')                
                        ->where('sku', $request['sku'])->first();

        if (!$product) return response()->json([
            'status' => 'error', 
            'message' => 'El SKU del producto indicado no existe'
        ],  404);
        
        return response()->json([
            'status' => 'success',
            'data' => $product,
        ], 200);
    }

    
    public function showBySkuAndWarehouse($warehouseCode, $sku)
    {
        $messages = [
            '*.exists' => 'El valor de :attribute no se encuentra en la base de datos',
        ];

        $validator = Validator::make(['sku' => $sku, 'warehouse' => $warehouseCode], [ 
            'sku' => 'required|exists:products,sku',
            'warehouse' => 'required|exists:product_inventory_sources,code',
        ], $messages);
      
        if ($validator->fails()) {
          return response()->json([ 'status' => 'error', 'message' => $validator->errors() ], 400);
        }

        /**
         * NOTE: This may fail when there is two seller with the same product SKU in the same warehouse
         * 
         */
        $warehouse = ProductInventorySource::where('code', $warehouseCode)->first();

        $productInventory = ProductInventory::where('product_inventory_source_id', $warehouse->id)
                                ->whereHas('product', function ($query) use ($sku)  {
                                   return $query->where('sku', $sku);
                                })->first();

        if (!$productInventory) {
            return response()->json([ 
                'status' => 'error', 
                'message' => 'La bodega no contiene el producto con el SKU indicado',
            ],  404);
        };

        $product = Product::with('categories')
                        ->with('brand')
                        ->with('product_class')
                        ->with('inventories')                
                        ->find($productInventory->product_id);
        
        return response()->json([
            'status' => 'success',
            'data' => $product,
        ], 200);
    }


    public function delete($warehouseCode, $sku)
    {
        $messages = [
            '*.exists' => 'El valor de :attribute no se encuentra en la base de datos',
        ];

        $validator = Validator::make(['sku' => $sku, 'warehouse' => $warehouseCode], [ 
            'sku' => 'required|exists:products,sku',
            'warehouse' => 'required|exists:product_inventory_sources,code',
        ], $messages);
      
        if ($validator->fails()) {
          return response()->json([ 'status' => 'error', 'message' => $validator->errors() ], 400);
        }

        /**
         * NOTE: This may fail when there is two seller with the same product SKU in the same warehouse
         * 
         */
        $warehouse = ProductInventorySource::where('code', $warehouseCode)->first();

        $productInventory = ProductInventory::where('product_inventory_source_id', $warehouse->id)
                                ->whereHas('product', function ($query) use ($sku)  {
                                   return $query->where('sku', $sku);
                                })->first();

        if (!$productInventory) {
            return response()->json([ 
                'status' => 'error', 
                'message' => 'La bodega no contiene el producto con el SKU indicado',
            ],  404);
        };

        $product = Product::find($productInventory->product_id);

        DB::beginTransaction();

        try {
            $productInOrders = OrderItem::where('product_id', $product->id)->first();
            $productInCarts = CartItem::where('product_id', $product->id)->first();

            if ($productInOrders || $productInCarts) throw new Exception('No puedes eliminar un producto existente en ordenes o carro');

            $product->delete();

        } catch(Exception $exception) {
            DB::rollBack();
            return response()->json([ 'status' => 'error', 'message' => $exception->getMessage() ], 400);
        }

        DB::commit();

        return response()->json([
            'status' => 'success',
            'message' => 'Producto eliminado',
            'data' => $product,
        ], 200);
    }


    public function store(ProductRequest $request)
    {
        $productService = new ProductService();

        DB::beginTransaction();

        $result = $productService->createSimpleProductWithWarehouse($request);

        if (!$result['status']) {

            DB::rollBack();

            return response()->json([
                'status' => $result['status_response'],
                'message' => $result['message'],
            ], 400);
        }

        DB::commit();

        return response()->json([
            'status' => $result['status_response'],
            'message' => $result['message'],
            'data' => $result['data'],
        ], 200);
    }


    public function updateStock(Request $request, $warehouseCode, $sku)
    {
        $messages = [
            '*.exists' => 'El valor de :attribute no se encuentra en la base de datos',
        ];

        $data = [
            'sku' => $sku, 
            'warehouse' => $warehouseCode, 
            'qty' => $request['qty']
        ];

        $rules = [ 
            'sku' => 'required|exists:products,sku',
            'warehouse' => 'required|exists:product_inventory_sources,code',
            'qty' => 'required|numeric|min:0',
        ];

        $validator = Validator::make($data, $rules, $messages);
      
        if ($validator->fails()) {
          return response()->json([ 'status' => 'error', 'message' => $validator->errors() ], 400);
        }


        /**
         * NOTE: This may fail when there is two seller with the same product SKU in the same warehouse
         * 
         */
        $warehouse = ProductInventorySource::where('code', $warehouseCode)->first();

        $productInventory = ProductInventory::where('product_inventory_source_id', $warehouse->id)
                                ->whereHas('product', function ($query) use ($sku)  {
                                   return $query->where('sku', $sku);
                                })->first();

        if (!$productInventory) {
            return response()->json([ 
                'status' => 'error', 
                'message' => 'La bodega no contiene el producto con el SKU indicado',
            ],  404);
        };

        $product = Product::find($productInventory->product_id);

        try {
            $product->updateInventory($request['qty'], $warehouse->id);
        } catch(Exception $exception) {
            return response()->json([ 'status' => 'error', 'message' => $exception->getMessage() ], 400);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Inventario del producto (' . $product->sku . ') en la bodega (' . $warehouse->name . ') actualizado',
        ], 200);
    }


    public function updatePrice(Request $request, $warehouseCode, $sku)
    {
        $messages = [
            '*.exists' => 'El valor de :attribute no se encuentra en la base de datos',
        ];

        $data = [
            'sku' => $sku,
            'warehouse' => $warehouseCode,
            'price' => $request['price'],
            'special_price' => $request['special_price'],
            'special_price_from' => $request['special_price_from'],
            'special_price_to' => $request['special_price_to'],
        ];

        $rules = [
            'sku' => 'required|exists:products,sku',
            'warehouse' => 'required|exists:product_inventory_sources,code',
            'price' => 'required|numeric|min:0',
            'special_price' => 'nullable|numeric|min:0',
            'special_price_from' => 'nullable|date_format:d-m-Y|before:special_price_to',
            'special_price_to' => 'nullable|date_format:d-m-Y|after:special_price_from',
        ];


        $validator = Validator::make($data, $rules, $messages);
      
        if ($validator->fails()) {
          return response()->json([ 'status' => 'error', 'message' => $validator->errors() ], 400);
        }

        /**
         * NOTE: This may fail when there is two seller with the same product SKU in the same warehouse
         * 
         */
        $warehouse = ProductInventorySource::where('code', $warehouseCode)->first();

        $productInventory = ProductInventory::where('product_inventory_source_id', $warehouse->id)
                                ->whereHas('product', function ($query) use ($sku)  {
                                   return $query->where('sku', $sku);
                                })->first();

        if (!$productInventory) {
            return response()->json([ 
                'status' => 'error', 
                'message' => 'La bodega no contiene el producto con el SKU indicado',
            ],  404);
        };

        $product = Product::find($productInventory->product_id);

        if ($request['price']) $product->price = $request['price'];
        if ($request['special_price']) $product->special_price = $request['special_price'];
        if ($request['special_price_from']) $product->special_price_from = new DateTime($request['special_price_from']);
        if ($request['special_price_to']) $product->special_price_to = new DateTime($request['special_price_to']);

        try {
            $product->update();
        } catch(Exception $exception) {
            return response()->json([ 'status' => 'error', 'message' => $exception->getMessage() ], 400);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Informacion de precio del producto (' . $product->sku . ') en la bodega (' . $warehouse->name . ') actualizado',
            'data' => $product,
            
        ], 200);
    }


}
