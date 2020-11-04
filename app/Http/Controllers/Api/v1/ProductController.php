<?php

namespace App\Http\Controllers\Api\v1;

use DateTime;
use App\Models\Seller;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Api\ProductRequest;

class ProductController extends Controller
{
    private $productService;
    
    public function show(Request $request)
    {
        $product = Product::with('categories')->find($request['id']);

        if (!$product) return response()->json([ 
            'status' => 'error', 
            'message' => 'El producto indicado no existe'
        ],  404);
        
        return response()->json([
            'status' => 'success',
            'data' => $product,
        ], 200);
    }


    public function store(ProductRequest $request)
    {   
        $this->productService = new ProductService();
        
        // Obtain seller id
        if ( ! $sellerId = $this->getSellerId($request) ) {
            return response()->json([ 
                'status' => 'error', 
                'message' => 'Debes indicar un campo seller_id'
            ],  400); 
        }
        
        // Validate seller id
        $seller = Seller::find($sellerId);
        if (! $seller) {
            return response()->json([ 
                'status' => 'error', 
                'message' => 'El seller_id especificado no existe'
            ],  404); 
        }

        // Validate SKU
        if ( ! $this->productService->validateUniqueSku($request['sku'], $sellerId, auth()->user()->companies->first()->id) ) {
            return response()->json([ 
                'status' => 'error', 
                'message' => 'Ya tienes un producto con el SKU indicado'
            ],  400); 
        }

        // Validate Url key
        $url_key = $request['url_key'] ?? Str::slug($request['name']);

        if ( ! $this->productService->validateUniqueSlug($url_key, auth()->user()->companies->first()->id) ) {
            return response()->json([ 
                'status' => 'error', 
                'message' => 'Ya existe un producto con el url_key indicado'
            ],  400); 
        }
        
        // Set default currency
        $currencyId = 63;

        // Validate and save custom attributes
        
        // Validate and save inventories
        
        // Save the product
        try {

            $product  = Product::create([
                'name' => $request['name'],
                'sku' => $request['sku'],
                'url_key' => $url_key,
                'is_service' => $request['is_service'],
                'use_inventory_control' => $request['use_inventory_control'],
                'short_description' => $request['short_description'],
                'description' =>  $request['description'],
                'product_type_id' => $request['product_type_id'],
                'product_class_id' => $request['product_class_id'],
                'prduct_brand_id' => $request['product_brand_id'],
                'price' => $request['price'],
                'cost' => $request['cost'],
                
                'special_price' => $request['special_price'],
                'special_price_from' => isset($request['special_price_from']) ? new DateTime($request['special_price_from']) : null,
                'special_price_to' => isset($request['special_price_to']) ? new DateTime($request['special_price_to']) : null,

                'currency_id' => $currencyId,

                'weight' => $request['is_service'] ? null : $request['weight'],
                'height' => $request['is_service'] ? null : $request['height'],
                'width' => $request['is_service'] ? null : $request['width'],
                'depth' => $request['is_service'] ? null : $request['depth'],

                'meta_title' => $request['meta_title'],
                'meta_keywords' => $request['meta_keywords'],
                'meta_description' => $request['meta_description'],
                
                'status' => $request['status'] ?? 1,
                'seller_id' => $request['seller_id'],
                'company_id' => auth()->user()->companies->first()->id,
            ]);

        } catch(\Illuminate\Database\QueryException $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception,
            ], 400);
        }

        // Attach categories
        $product->categories()->attach($request['categories']);

        // Convert images to base64 and save it in the images_json field of the product
        // so the product observer can take care of the upload
        if ( $request->file('images') ) {
            $imagesArray = [];
            foreach ($request->file('images') as $image) {
                array_push($imagesArray, ['image' => 'data:image/jpeg;base64,' . base64_encode(file_get_contents($image))]);
            }
            $product->images_json = $imagesArray;
        }
        
        $product->update();

        return response()->json([
            'status' => 'success',
            'data' => $product,
            'message' => 'Producto creado exitosamente',
        ], 200);

    }

    private function getSellerId($request)
    {
        if ( auth()->user()->hasAnyRole('Super admin|Administrador negocio|Supervisor Marketplace') ) {
            if ( ! isset($request['seller_id']) ) return false;
            return $request['seller_id'];
        } else {
            return $sellerId = Seller::where('user_id', auth()->user()->id)->first();
        }
    }

}