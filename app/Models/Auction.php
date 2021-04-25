<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auction extends Model
{
    public function member(){
        return $this->belongsTo(Member::class);
    }

    public function category(){
        return $this->belongsTo(Category::class,'cat_id');
    }

    public function images(){
        return $this->hasMany(AuctionImage::class);
    }

    public function more_detials(){
        return $this->hasMany(AuctionDetials::class);
    }

    public function city(){
        return $this->belongsTo(City::class,'city_id');
    }
}
