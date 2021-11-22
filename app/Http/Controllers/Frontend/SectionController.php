<?php

namespace App\Http\Controllers\Frontend;

use Carbon\Carbon;
use App\Models\Seller;
use App\Models\Slider;
use App\Models\Banners;
use App\Models\Product;
use App\Models\Section;
use App\Models\FaqTopic;
use App\Models\FaqAnswer;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Http\Controllers\Controller;
use App\Services\ProductFilterService;
use Illuminate\Database\Eloquent\Builder;

class SectionController extends Controller
{
    private $section;

    public function __construct()
    {
        $this->section = Section::where('slug', request()->route('section'))->first();

        $this->viewPath = $this->section->view_path === 'default' 
                                ? ''
                                : $this->section->view_path; 

        Product::addGlobalScope('section', function (Builder $builder) {
            $builder->whereHas('categories', function ($query) {
                return $query->whereIn('id', $this->section->product_categories->pluck('id')->toArray());
            });
        });

        view()->share('section', $this->section);
    }
    public function index()
    {
        $categories = ProductCategory::where('status', '=' ,'1')
        ->whereHas('products', function ($query) {
            $query->where('status', '=', '1')
            ->where('is_approved', '=', '1')
            ->shouldShow()
            ->where('parent_id', '=', null);
        })->limit(3)->inRandomOrder()->get();

        $featuredProducts = Product::where('status', '=' ,'1')
            ->where('featured', '=' ,'1')
            ->shouldShow()
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

        $today = Carbon::now();//->format('Y-m-d');

        $sliders = Slider::where('status','=','1')->orderBy('order')->get();

        return view($this->viewPath . '.marketplace', compact('categories','featuredProducts','banners','sliders'));
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
        $product = Product::where('url_key', $request->slug)->select('*')->shouldShow()->firstOrFail();

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

        return view("$this->viewPath.shop-grid", compact('render', 'data'));
    }

    public function getProductsByCategorySlug(Request $request)
    {
        $categoryId = ProductCategory::where('slug', $request->category)->first()->id ?? null;

        if (is_null($categoryId)) {
            $categoryId = ProductCategory::find($request->category)->id ?? null;
        }

        $render = ['view' => 'searchCategory'];
        $data = ['category' => $categoryId];

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
}
