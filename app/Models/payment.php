<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class payment extends Model
{
    protected $fillable = [
        'order_id',
        'status',
        'metode',
        'bukti_pembayaran',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
