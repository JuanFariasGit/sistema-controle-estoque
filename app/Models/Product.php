<?php

namespace App\Models;

use App\Listeners\UpdateCurrentStock;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['code', 'name', 'capacity', 'photo', 'user_id'];

    public $timestamps = false;

    protected $dispatchesEvents = [
        'saved' => UpdateCurrentStock::class,
    ];

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
            $quantity += $movement->pivot->quantity;
        }

        return $quantity;
    }
}
