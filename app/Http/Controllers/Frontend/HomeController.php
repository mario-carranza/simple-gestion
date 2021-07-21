<?php

namespace App\Http\Controllers\Frontend;

use Carbon\Carbon;
use App\Models\Cart;
use App\Models\Seller;
use App\Models\Slider;
use App\Models\Banners;
use App\Models\Commune;
use App\Models\Product;
use App\Models\FaqTopic;
use App\Models\FaqAnswer;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Http\Controllers\Controller;
use App\Services\ProductFilterService;

class HomeController extends Controller
{
    public function index()
    {
        $categories = ProductCategory::where('status', '=' ,'1')
        ->whereHas('products', function ($query) {
            $query->where('status', '=', '1')
            ->where('is_approved', '=', '1')
            ->where('parent_id', '=', null);
        })->limit(3)->inRandomOrder()->get();

        $featuredProducts = Product::byLocation()->where('status', '=' ,'1')
        ->where('featured', '=' ,'1')
        ->inRandomOrder()->get();

        $banner1 = Banners::where('section',1)->first();
        $banner2 = Banners::where('section',2)->first();
        $banner3 = Banners::where('section',3)->first();
        $banner4 = Banners::where('section',4)->first();

        $banners = [
            1 => $banner1,
            2 => $banner2,
            3 => $banner3,
            4 => $banner4
        ];

        $today =  Carbon::now();//->format('Y-m-d');

        $sliders = Slider::where('status','=','1')
            // ->orWhere(function($query) use ($today){
            //     $query->whereDate('visible_from','>=',$today)
            //     ->whereDate('visible_to','<=',$today)
            //     ->where('status','=','1');
            // })
        ->orderBy('order')->get();

        return view('marketplace', compact('categories','featuredProducts','banners','sliders'));
    }

    public function getAllProducts()
    {
        $products = Product::where('status', '=', '1')->where('is_approved', '=', '1')->where('parent_id', '=', null)->with('seller')->with('categories')->orderBy('id', 'DESC')->get();
        $render = ['view' => 'shop-general'];
        $data = ['category' => $products];

        return view('shop-grid', compact('products', 'render', 'data'));
    }

    public function productDetail(Request $request)
    {
        $product = Product::where('url_key', $request->slug)->firstOrFail();

        return view('product', compact('product'));
    }

    public function searchProduct(Request $request)
    {
        $render = ['view' => 'searchProduct'];
        $data = ['category' => $request->category, 'product' => $request->product];
        $data['showFilters'] = true;

        return view('shop-grid', compact('render', 'data'));
    }

    public function getProductsByCategory(Request $request)
    {
        $render = ['view' => 'searchCategory'];
        $data = ['category' => $request->category];
        $data['showFilters'] = true;

        return view('shop-grid', compact('render', 'data'));
    }

    public function getProductsByCategorySlug(Request $request)
    {
        $categoryId = ProductCategory::where('slug', $request->category)->first()->id ?? null;

        if (is_null($categoryId)) {
            $categoryId = ProductCategory::find($request->category)->id ?? null;
        }

        $render = ['view' => 'searchCategory'];
        $data = ['category' => $categoryId];
        $data['showFilters'] = true;

        return view('shop-grid', compact('render', 'data'));
    }

    public function getSeller(Request $request)
    {
        $seller = Seller::where('id', '=', $request->id)->with('seller_category')->with('company')->first();
        $countProduct = Product::where('seller_id', '=', $request->id)->where('parent_id', '=', null)->where('status', '=', '1')->where('is_approved', '=', '1')->get()->count();
        $render = ['view' => 'seller'];
        $data = $seller->id;
        return view('vendor', compact('seller', 'countProduct','render', 'data'));
    }

    public function getSellerBySlug(Request $request)
    {
        $seller = Seller::where('slug', '=', $request->slug)->with('seller_category')->with('company')->firstOrFail();
        $countProduct = Product::where('seller_id', '=', $seller->id)->where('parent_id', '=', null)->where('status', '=', '1')->where('is_approved', '=', '1')->get()->count();
        $render = ['view' => 'seller'];
        $data = $seller->id;
        return view('vendor', compact('seller', 'countProduct','render', 'data'));
    }

    public function getFaq()
    {
        $faqs = FaqAnswer::where('status', '=', '1')->with('faq_topic')->get();
        $faqTopic = FaqTopic::get();

        return view('faq', compact('faqs', 'faqTopic'));
    }

    public function filterProducts(Request $request)
    {
        $filterService = new ProductFilterService();
        $filterService->filterByParams($request);
    }

    public function setLocation(Request $request)
    {   
        $cart = Cart::where('session_id', session()->getId())->first();

        if ($cart) {
            $cart->cart_items()->delete();
            $cart->delete();
        }

        $commune = Commune::find($request->commune_id);
        $request->session()->put('commune_id', $commune->id);
        $request->session()->put('commune_name', $commune->name);
        return redirect()->back();
    }
}
