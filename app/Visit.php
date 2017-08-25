<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    protected $fillable = [
        'salesman_id',
        'customer_id',
        'record',
        'status',
    ];
}
