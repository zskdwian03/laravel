<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverActivityLog extends Model
{
    protected $table = 'driver_activity_logs'; // opsional, tapi eksplisit lebih aman

    protected $fillable = [
        'driver_id',
        'date',
    ];

    // Relasi ke model Driver
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
