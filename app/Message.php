<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'type',
        'option',
        'salesman_id',
        'customer_id',
        'content',
        'origin',
        'status',
    ];
}
