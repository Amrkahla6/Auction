<?php

namespace App\Http\Controllers\Onlin;

use Auth;
use App\Models\Member;
use App\Models\Auction;
use App\Models\Category;
use App\Models\Favorite;
use LaravelLocalization;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;


class onlinehome extends Controller
{
    public function index(Request $request)
    {
        $data['categories'] = Category::where('parent_id',0)->select('id','category_name_' . LaravelLocalization::getCurrentLocale() . ' as name','img')->with(['auctions','auctions.images'])->get();
        // $data['auctions']   = Auction::with('images')->orderBy('id', 'desc')->take(8)->get();

          $data['auctions'] = Auction::whereHas('member', function ($query) use ($request) {
            $query->where('username', 'like', "%{$request->search}%")
            ->orWhere('email' , 'like' , '%'. $request->search. '%')
            ->orWhere('auction_title' , 'like' , '%'. $request->search. '%')
            ->orWhere('address' , 'like' , '%'. $request->search. '%')
            ->orWhere('price_opining' , 'like' , '%'. $request->search. '%')
            ->orWhere('price_closing' , 'like' , '%'. $request->search. '%')
            ->orWhere('status' , 'like' , '%'. $request->search. '%')
            ->orWhere('is_finished' , 'like' , '%'. $request->search. '%')
            ->orWhere('is_slider' , 'like' , '%'. $request->search. '%');
        })
        ->orWhereHas('category', function ($query) use ($request) {
            $query->where('category_name_ar', 'like', "%{$request->search}%")
            ->orWhere('category_name_en' , 'like' , '%'. $request->search. '%');
        })
        ->with('images')->orderBy('id', 'desc')->take(8)->get();

        return view('online.index')->with($data);
    }

    public function registerd()
    {
        if(empty($member = Member::find(auth()->guard('members')->id()))){
            return view('online.auth.login');
        }else{
        $data['categories']       = Category::where('parent_id',0)->get();
        $data['auctions_active']  = Auction::where('member_id',$member->id)->where('is_finished',0)->orderBy('id', 'DESC')->with('images')->get();
        $data['auctions_dis']     = Auction::where('member_id',$member->id)->where('is_finished',1)->orderBy('id', 'DESC')->with('images')->get();
        //dd($data['auctions_active']);
        return view('online.registerd')->with($data);
    }
}




    public function categories(){
        return view('online.categories');
    }

    public function sub_categories(Request $request, $parent_id){
        //الأقسام الفرعية والمزادات الخاصة بيها
        $data['categories'] = Category::where('parent_id',$parent_id)->select('id','category_name_ar','category_name_en')->with(['auctions','auctions.images'])->get();
       if(empty( $data['categories'])){
            $data['categories'] = Category::where('id',$parent_id)->select('id','category_name_ar','category_name_en')->with(['auctions','auctions.images'])->get();
        }
        return view('online.sub_categories')->with($data);
    }
}
