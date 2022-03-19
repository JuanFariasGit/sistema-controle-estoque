<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductMovement extends Model
{
    protected $fillable = ['product_id', 'quantity', 'value', 'movement_id'];

    public $timestamps = false;
}
