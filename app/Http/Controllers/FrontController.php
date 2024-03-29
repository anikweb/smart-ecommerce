<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;
use App\Models\{
    About,
    blog,
    Category,
    Faq,
    PrivacyPolicy,
    Product,
    Product_Attribute,
    ProductRequest,
    productSizeAttribute,
    ReturnPolicy,
    ShipDelivery,
    Slider,
    TermCondition,
    UniqueVisitors,
    Wishlist,
};
use Share;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FrontController extends Controller
{
    public function index(){
        if(!UniqueVisitors::where('ip_address',$_SERVER['REMOTE_ADDR'])->first()){
            // return 'nai';
            $uniqueVisitors = new UniqueVisitors;
            $uniqueVisitors->ip_address = $_SERVER['REMOTE_ADDR'];
            $uniqueVisitors->save();
        }
        if(Cookie::get('beshkichu_com') == ''){
            $cookie_name = 'beshkichu_com';
            $cookie_value = time().'-'.Str::random(10);
            $cookie_duration = 43200;
            Cookie::queue($cookie_name, $cookie_value, $cookie_duration);
        }
        return view('frontend.home',[
            'productAll' => Product::limit(20)->get(),
            'productLatest' => Product::latest()->limit(20)->get(),
            'wishlistProduct' =>Wishlist::all(),
            'sliders' =>Slider::where('status',1)->latest()->get(),
            'categories' =>Category::all(),
        ]);
    }
    public function productView(){
        return view('frontend.pages.product.product_list',[
            'productAll' => Product::latest()->get(),
            'productLatest' => Product::latest()->paginate(10),
        ]);
    }
    public function productViewByCatgory($slug){
        $Category = Category::where('slug',$slug)->first();
        // $Category->product->get();
        // return $Category->id;
        return view('frontend.pages.product.category_product',[
            'category' =>  Category::where('slug',$slug)->first(),
            'productLatest' =>  Product::where('category_id',$Category->id)->latest()->paginate(10),
        ]);
    }
    public function productSingle($slug){

        $product = Product::where('slug',$slug)->first();
        $SocialShare = Share::page(route('frontend.product.single',$product->slug),$product->name)
            ->facebook()
            ->twitter()
            ->linkedin()
            ->whatsapp()->getRawLinks();

        return view('frontend.pages.product.product_single',compact(['product','SocialShare']));
    }
    public function wishlistIndex(){

        $siteCookie = Cookie::get('beshkichu_com');

        return view('frontend.pages.wishlist',[

            'wishlists' => Wishlist::where('cookie_id',$siteCookie)->latest()->paginate(10),
        ]);
    }
    public function wishliststore($product_id){
            $siteCookie = Cookie::get('beshkichu_com');
            $wishlistCheck =  Wishlist::where('cookie_id',$siteCookie)->where('product_id',$product_id)->first();
            if($wishlistCheck){
                $wishlistCheck->delete();
                return response('deleted');
            }else{
                $wishlist = new Wishlist;
                $wishlist->cookie_id =$siteCookie;
                $wishlist->product_id =$product_id;
                $wishlist->save();
                return response()->json($wishlist);

            }
    }
    public function wishlistRemove($id){
        // return $id;
        $siteCookie = Cookie::get('beshkichu_com');
        $wishlistCheck =  Wishlist::where('cookie_id',$siteCookie)->where('product_id',$id)->first();
        $wishlistCheck->delete();
        return back()->with('success','Deleted!');
    }
    public function getColorSizeId($imid,$pid){
        $sizes = Product_Attribute::where('product_id',$pid)->where('image_gallery_id',$imid)->get();
        $outpot ='';
        foreach ($sizes as $key => $value) {
            # code...
            $s = productSizeAttribute::where('attribute_id',$value->id)->get();
            foreach ($s as $key => $size) {
                $outpot =  $outpot.'<li  class="sizeCheck sizeCheck'.$size->size_id.'"  data-rPrice="'.$value->regular_price.'"  data-size="'.$size->size_id.'" data-price="'.$value->offer_price.'">'.$size->size_id;
            }
        }
        return response()->json($outpot);
    }
    public function faqIndex(){
        return view('frontend.pages.faq.faq',[
            'faqs' => Faq::latest()->get(),
        ]);
    }
    public function aboutIndex(){
        return view('frontend.pages.about.about',[
            'about' => About::first(),
        ]);
    }
    public function blogIndex(){
        return view('frontend.pages.blog.index',[
            'blogs' => blog::latest()->paginate(10),
        ]);
    }
    public function blogShow($slug){

        $blog = blog::where('slug',$slug)->first();

        $SocialShare = Share::page(route('frontend.blog.show',$blog->slug),$blog->title)
            ->facebook()
            ->twitter()
            ->linkedin()
            ->whatsapp()->getRawLinks();

        return view('frontend.pages.blog.show',compact(['blog','SocialShare']));
    }
    public function indexProductRequest(){
        return view('frontend.pages.product_request.requested_product',[
            'requests' => ProductRequest::latest()->where('user_id',Auth::user()->id)->paginate(5),
        ]);
    }
    public function indexPrivacyPolicy(){
        return view('frontend.pages.privacy_policy.index',[
            'policy' => PrivacyPolicy::find(1),
        ]);
    }
    public function indexReturnPolicy(){
        return view('frontend.pages.return_policy.index',[
            'policy' => ReturnPolicy::find(1),
        ]);
    }
    public function indexTermsConditions(){
        return view('frontend.pages.term_condition.index',[
            'termsCondition' => TermCondition::find(1)
        ]);
    }
    public function indexShipDelivery(){
        return view('frontend.pages.ship_delivery.index',[
            'ship' => ShipDelivery::find(1),
        ]);
    }
    public function indexContactUs(){
        return view('frontend.pages.contact_us.index');
    }
    public function indexSizeGuide(){
        return view('frontend.pages.size_guide.index');
    }

}
