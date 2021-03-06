<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['code', 'name', 'capacity', 'photo', 'current_stock'];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function movements()
    {
        return $this->belongsToMany('App\Models\Movement', 'movement_products')
        ->withPivot(['quantity', 'value']);
    }
}
