<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


/********************************************
 * 
 *  API
 *  V1
 * 
 *******************************************/

Route::group([ 'prefix' => '/v1'], function() {

    // Reset DB
    Route::get('/tools/reset', 'Api\v1\CompanyController@indexReset');
    Route::post('/tools/reset', 'Api\v1\CompanyController@reset');

    // Login
    Route::post('/login', 'Api\v1\AuthController@authenticate');
    Route::get('/test', 'Api\v1\AuthController@test')->middleware('auth.jwt');;


    // Customer
    Route::post('/customers', 'Api\v1\CustomerController@store')
        ->middleware(['auth.jwt', 'permission:customer.create'])
        ->name('api.customers.store');
    Route::get('/customers/{id}', 'Api\v1\CustomerController@show')
        ->name('api.customers.show');


    // Product
    Route::post('/products', 'Api\v1\ProductController@store')
        ->middleware(['auth.jwt', 'permission:product.create'])
        ->name('api.products.store');
    Route::delete('/products/{warehouse}/{sku}', 'Api\v1\ProductController@delete')
        ->middleware(['auth.jwt', 'permission:product.delete'])
        ->name('api.products.delete');
    Route::get('/products/{warehouse}/{sku}', 'Api\v1\ProductController@showBySkuAndWarehouse')
        ->name('api.products.show-by-sku');
    Route::post('/products/{warehouse}/{sku}/stock', 'Api\v1\ProductController@updateStock')
        ->middleware(['auth.jwt', 'permission:product.update'])
        ->name('api.products.update-stock');
    Route::post('/products/{warehouse}/{sku}/price', 'Api\v1\ProductController@updatePrice')
        ->middleware(['auth.jwt', 'permission:product.update'])
        ->name('api.products.update-price');
    Route::post('/products/{warehouse}/{sku}/images', 'Api\v1\ProductController@updateImages')
        ->middleware(['auth.jwt', 'permission:product.update'])
        ->name('api.products.update-images');
    Route::post('/products/{warehouse}/{sku}/categories', 'Api\v1\ProductController@updateCategories')
        ->middleware(['auth.jwt', 'permission:product.update'])
        ->name('api.products.update-categories');
    Route::get('/products/{id}', 'Api\v1\ProductController@show')
        ->name('api.products.show');
    Route::post('/products/{warehouse}/{sku}/shipping', 'Api\v1\ProductController@updateShipping')
        ->middleware(['auth.jwt', 'permission:product.update'])
        ->name('api.products.update-shipping');
    Route::get('/products/{id}', 'Api\v1\ProductController@show')
        ->name('api.products.show');


    // Product Brand
    Route::post('/product-brands', 'Api\v1\ProductBrandController@store')
        ->middleware(['auth.jwt', 'permission:productbrand.create'])
        ->name('api.product-brands.store');
    Route::get('/product-brands/{id}', 'Api\v1\ProductBrandController@show')
        ->name('api.product-brands.show');
    Route::get('/product-brands', 'Api\v1\ProductBrandController@all')
        ->name('api.product-brands.all');

    // Brand
    Route::post('/brands', 'Api\v1\ProductBrandController@store')
        ->middleware(['auth.jwt', 'permission:productbrand.create'])
        ->name('api.brands.store');
    Route::delete('/brands/code/{code}', 'Api\v1\ProductBrandController@delete')
        ->middleware(['auth.jwt', 'permission:productbrand.delete'])
        ->name('api.brands.code-delete');
    Route::get('/brands/{id}', 'Api\v1\ProductBrandController@show')
        ->name('api.brands.show');
    Route::get('/brands/code/{code}', 'Api\v1\ProductBrandController@showByCode')
        ->name('api.brands.show-by-code');
    Route::get('/brands', 'Api\v1\ProductBrandController@all')
        ->name('api.brands.all');


    // Product Category
    Route::post('/product-categories', 'Api\v1\ProductCategoryController@store')
        ->middleware(['auth.jwt', 'permission:productcategory.create'])
        ->name('api.product-categories.store');
    Route::get('/product-categories/{id}', 'Api\v1\ProductCategoryController@show')
        ->name('api.product-categories.show');
    Route::get('/product-categories', 'Api\v1\ProductCategoryController@all')
        ->name('api.product-categories.all');


    // Category
    Route::post('/categories', 'Api\v1\ProductCategoryController@store')
        ->middleware(['auth.jwt', 'permission:productcategory.create'])
        ->name('api.categories.store');
    Route::delete('/categories/code/{code}', 'Api\v1\ProductCategoryController@delete')
        ->middleware(['auth.jwt', 'permission:productcategory.delete'])
        ->name('api.categories.code-delete');
    Route::get('/categories/{id}', 'Api\v1\ProductCategoryController@show')
        ->name('api.categories.show');
    Route::get('/categories/code/{code}', 'Api\v1\ProductCategoryController@showByCode')
        ->name('api.categories.show-by-code');
    Route::get('/categories', 'Api\v1\ProductCategoryController@all')
        ->name('api.categories.all');

    
    // Product Class
    Route::post('/product-classes', 'Api\v1\ProductClassController@store')
        ->middleware(['auth.jwt', 'permission:productclass.create'])
        ->name('api.product-classes.store');
    Route::get('/product-classes', 'Api\v1\ProductClassController@all')
        ->name('api.product-classes.all');
    Route::get('/product-classes/code/{code}', 'Api\v1\ProductClassController@showBycode')
        ->name('api.product-classes.show-by-code');
    Route::delete('/product-classes/code/{code}', 'Api\v1\ProductClassController@delete')
        ->middleware(['auth.jwt', 'permission:productclass.delete'])
        ->name('api.product-classes.code-delete');
    Route::get('/product-classes/{id}', 'Api\v1\ProductClassController@show')
        ->name('api.product-classes.show');
    Route::get('/product-classes/{code}/attributes', 'Api\v1\ProductClassController@getAttributes')
        ->name('api.product-classes.get-attributes');
    

    // Product Class Attribute
    Route::post('/attributes', 'Api\v1\ProductClassAttributeController@store')
        ->middleware(['auth.jwt', 'permission:productclassattribute.create'])
        ->name('api.product-class-attributes.show-by-code');
    Route::delete('/attributes/code/{code}', 'Api\v1\ProductClassAttributeController@delete')
        ->middleware(['auth.jwt', 'permission:productclassattribute.delete'])
        ->name('api.product-class-attributes.code-delete');
    Route::get('/attributes/code/{code}', 'Api\v1\ProductClassAttributeController@showByCode')
        ->name('api.product-class-attributes.show-by-code');

    // Orders
    Route::get('/orders/getLast', 'Api\v1\OrderController@getLast')
        ->name('api.orders.show.last');
    Route::get('/orders/{id}', 'Api\v1\OrderController@show')
        ->name('api.orders.show');
    Route::put('/orders/{id}/status', 'Api\v1\OrderController@updateStatus')
        ->middleware(['auth.jwt', 'permission:order.update'])
        ->name('api.orders.status.update');
    Route::get('/orders/error/logs', 'Api\v1\OrderController@fetchErrorLogs')
        ->name('api.orders.show.logs');
    Route::get('/orders/error/logs/{id}', 'Api\v1\OrderController@fetchErrorLog')
        ->name('api.orders.show.log');
    

    // Warehouse
    Route::post('/warehouses', 'Api\v1\ProductInventorySourceController@store')
        ->middleware(['auth.jwt', 'permission:productinventorysource.create'])
        ->name('api.warehouses.store');
    Route::delete('/warehouses/code/{code}', 'Api\v1\ProductInventorySourceController@delete')
        ->middleware(['auth.jwt', 'permission:productinventorysource.delete'])
        ->name('api.warehouses.code-delete');
    Route::get('/warehouses/{id}', 'Api\v1\ProductInventorySourceController@show')
        ->name('api.warehouses.show');
    Route::get('/warehouses/code/{code}', 'Api\v1\ProductInventorySourceController@showByCode')
        ->name('api.warehouses.show-by-code');

    // Commune 
    Route::get('/communes', 'Api\v1\CommuneController@index')->name('api.commune.index');
});

