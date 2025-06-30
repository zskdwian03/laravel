<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable = [
        'order_id',
        'sender',
        'receiver',
        'message',
        'created_at',
        'updated_at'
    ];

    public $timestamps = false;
}
