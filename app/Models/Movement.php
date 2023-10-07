<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movement extends Model
{
    protected $fillable = ['date_time', 'description', 'type', 'total', 'user_id'];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function products()
    {
        return $this->belongsToMany('App\Models\Product', 'movement_products', 'movement_id', 'product_id')
        ->withPivot(['quantity', 'value']);
    }
}
