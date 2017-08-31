<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'salesman_id',
        'name',
        'phone',
        'wx',
        'company',
    ];
}
