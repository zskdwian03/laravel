<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatusLogs extends Model
{
    public function order() {
        return $this->belongsTo(Order::class);
    }
    
}
