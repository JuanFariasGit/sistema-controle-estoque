<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Movement extends Model
{
    protected $fillable = ['date_time', 'description', 'type', 'total', 'user_id'];

    public $timestamps = false;
}
