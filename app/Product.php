<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['code', 'name', 'capacity', 'photo', 'current_stock', 'user_id'];

    public $timestamps = false;
}
