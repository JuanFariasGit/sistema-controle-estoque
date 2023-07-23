<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['code', 'name', 'capacity', 'photo', 'user_id'];

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

    public function getQtInStock()
    {
        $movements = $this->movements()->get();
        $quantity = 0;

        foreach ($movements as $movement) {
            if ($movement->type == 'entry') {
                $quantity += $movement->pivot->quantity;
            } else {
                $quantity -= $movement->pivot->quantity;
            }
        }

        return $quantity;
    }
}
