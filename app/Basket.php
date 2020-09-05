<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Basket extends Model
{
    //

    public function products()
    {
        return $this->belongsToMany('Product', 'baskets_products'); 
    }
}
