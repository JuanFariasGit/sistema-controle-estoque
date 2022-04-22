<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Movement extends Model
{
    protected $fillable = ['date_time', 'description', 'type', 'total', 'user_id'];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function products()
    {
        return $this->belongsToMany('App\Product', 'movement_products')
        ->withPivot(['quantity', 'value']);
    }
}
