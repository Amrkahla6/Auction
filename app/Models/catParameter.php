<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class catParameter extends Model
{
    public function category()
    {
        return $this->belongsTo(Category::class,'cat_id ');
    }
}
