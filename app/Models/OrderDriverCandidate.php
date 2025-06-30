<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class OrderDriverCandidate extends Model
{
    protected $fillable = ['order_id', 'driver_id', 'status'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function driver() {
        return $this->belongsTo(Driver::class, 'driver_id', 'user_id');
    }



    
}
