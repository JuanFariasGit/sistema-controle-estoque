<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MovementProduct extends Model
{
    protected $fillable = ['product_id', 'movement_id', 'quantity', 'value'];

    public $timestamps = false;
}
