<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Seller;
use App\Models\Product;
use App\Models\FaqTopic;
use App\Models\FaqAnswer;
use Illuminate\Http\Request;
use App\Models\SellerAddress;
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

        $sellers =  Seller::where('status', '=', '1')
        ->where('is_approved', '=', '1')
        ->whereHas('products', function($query) {
            $query->where('status', 1)->where('is_approved', 1);
        })->has('products', '>', 0)
        ->get()
        ->shuffle();

        $sellers = $sellers->split(2);

        return view('marketplace', compact('categories', 'sellers'));
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

        return view('shop-grid', compact('render', 'data'));
    }

    public function getProductsByCategory(Request $request)
    {
        $render = ['view' => 'searchCategory'];
        $data = ['category' => $request->category];

        return view('shop-grid', compact('render', 'data'));
    }

    public function getSeller(Request $request)
    {
        //$seller = Seller::where('id', '=', $request->id)->with('seller_category')->with('company')->first();
        $seller = Seller::where('id', '=', $request->id)
            ->with('seller_category')
            ->with('company')
            ->withCount([
                'products' => function ($query) {
                    $query->where('parent_id', '=', null)
                          ->where('status', '=', '1')
                          ->where('is_approved', '=', '1');
                }
            ])
            ->with('addresses.commune')->first();
        //$countProduct = Product::where('seller_id', '=', $request->id)->where('parent_id', '=', null)->where('status', '=', '1')->where('is_approved', '=', '1')->get()->count();
        //$sellerAdress = SellerAddress::whereSellerId($seller->id)->with('commune')->first();
        $render = ['view' => 'seller'];
        $data = $seller->id;
        return view('vendor', compact('seller', 'render', 'data'));
        //return view('vendor', compact('seller', 'countProduct','render', 'data','sellerAdress'));
    }

    public function getSellerBySlug(Request $request)
    {
        $seller = Seller::where('slug', '=', $request->slug)->with('seller_category')->with('company')->firstOrFail();
        $countProduct = Product::where('seller_id', '=', $seller->id)->where('parent_id', '=', null)->where('status', '=', '1')->where('is_approved', '=', '1')->get()->count();
        $sellerAdress = SellerAddress::whereSellerId($seller->id)->with('commune')->first();
        $render = ['view' => 'seller'];
        $data = $seller->id;
        return view('vendor', compact('seller', 'countProduct','render', 'data','sellerAdress'));
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
}
