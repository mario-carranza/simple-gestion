<?php

use App\Models\Seller;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return redirect(backpack_url('dashboard'));
// });

Route::get('/', 'Frontend\HomeController@index')->name('index');

Route::get('/customer/sign', 'Frontend\CustomerController@sign')->name('customer.sign')->middleware(['guest']);
Route::post('/customer/register', 'Frontend\CustomerController@store')->name('customer.frontend.store');
Route::post('/customer/login', 'Frontend\CustomerController@authenticate')->name('customer.frontend.login');
Route::post('/customer/logout', 'Frontend\CustomerController@logout')->name('logout');
Route::get('/customer/forget', 'Frontend\CustomerController@forget')->middleware(['guest'])->name('customer.forget');
Route::post('/customer/forget', 'Frontend\CustomerController@recovery')->name('customer.frontend.recovery');
Route::post('/customer/reset', 'Frontend\CustomerController@updatePassword')->name('password.update');
Route::get('/customer/reset/{token}', 'Frontend\CustomerController@reset')->name('password.reset');
Route::get('/customer/exit', 'Frontend\CustomerController@logout')->name('exit');
Route::get('/support', 'Frontend\CustomerController@support')->name('customer.support');
Route::post('/support', 'Frontend\CustomerController@createIssue')->name('customer.support.create');
Route::get('/turismo-rural', 'Frontend\HomeController@travel')->name('landing.travel');

Route::middleware(['auth'])->group(function () {
    Route::put('/customer/{customer}', 'Frontend\CustomerController@update')->name('customer.update');
    Route::get('/customer/profile', 'Frontend\CustomerController@profile')->name('customer.profile');
    Route::get('/customer/address', 'Frontend\CustomerController@address')->name('customer.address');
    Route::get('/customer/order', 'Frontend\CustomerController@order')->name('customer.order');
    Route::get('/customer/subscription', 'Frontend\CustomerController@subscription')->name('customer.subscription');
    Route::post('/customer/subscription/add', 'Frontend\CustomerController@addSubscription')->name('customer.subscription.add');
    Route::post('/customer/subscription/plans', 'Frontend\CustomerController@getPlans')->name('customer.subscription.plans');
    Route::put('/address/{customer}', 'Frontend\AddressController@store')->name('address.update');
    Route::get('/payment/subscription/{id}', 'Admin\Payments\WebPayPlusController@subscriptionCustomerPayment')->name('payment.customer.subscription');
        
});
Route::post('/payment/subscription/result', 'Admin\Payments\WebPayPlusController@subscriptionResultCustomerPayment')->name('payment.customer.result');
Route::post('/payment/subscription/detail', 'Admin\Payments\WebPayPlusController@subscriptionDetailCustomerPayment')->name('payment.customer.detail');

Route::get('/seller/register', 'Frontend\SellerController@index')->name('seller.sign');
Route::post('/seller/register', 'Frontend\SellerController@store')->name('seller.frontend.store');

Route::get('/home', 'Frontend\HomeController@index')->name('home');

Route::get('/product/{slug}', 'Frontend\HomeController@productDetail')->name('product');
Route::get('/search-products/{category}/{product}', 'Frontend\HomeController@searchProduct');
//Route::get('/search-products/{category}', 'Frontend\HomeController@getProductsByCategory');
Route::get('/search-products/{category}', 'Frontend\HomeController@getProductsByCategorySlug')->name('category.products');

Route::get('/shop-list/', function () {
    return view('shop-list');
});
Route::get('/shop-grid/', 'Frontend\HomeController@getProductsByCategory');
Route::get('/seller-shop/{id}', 'Frontend\HomeController@getSeller');
Route::get('/shop/{slug}', 'Frontend\HomeController@getSellerBySlug')->name('seller-slug');
Route::get('/faq', 'Frontend\HomeController@getFaq');
Route::get('/faq-single', function () {
    return view('faq-single');
});
Route::get('/faq-request', function () {
    return view('faq-request');
});
Route::get('/privacy', function () {
    return view('privacy');
});
Route::get('/terms-conditions', function () {
    return view('terms-conditions');
});

//Auth::routes();
Route::redirect('/login', '/customer/login')->name('login');

Route::get('/shopping-cart', 'Frontend\CartController@shoppingCart')->name('shopping-cart');
Route::get('/checkout', 'Frontend\CheckoutController@index')->name('checkout');
Route::get('/filter-products', 'Frontend\HomeController@filterProducts');

Route::group([
    'prefix' => '/transbank'
], function () {
    Route::get('main', function(){
        return view('payments.transbank.test');
    });

    // WebPayPlus Mall
    Route::get('webpay/mall/{order}', 'Payments\Transbank\WebpayPlusMallController@redirect')->name('transbank.webpayplus.mall.redirect');
    Route::post('webpay/mall/response', 'Payments\Transbank\WebpayPlusMallController@response')->name('transbank.webpayplus.mall.response');
    Route::post('final', 'Payments\Transbank\WebpayPlusMallController@final')->name('transbank.final');
    Route::get('webpay/mall/download/{order}', 'Payments\Transbank\WebpayPlusMallController@download')->name('transbank.webpayplus.mall.download');
    Route::get('test/{order}', 'Payments\Transbank\WebpayPlusMallController@test')->name('transbank.test.view');
});
// Route::get('complete', function(){
//     return view('payments.transbank.webpay.mall.complete');
// });
