<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarif extends Model
{
    use HasFactory;

    protected $fillable = [
        'jenis_kendaraan',
        'tarif_per_km',
        'tarif_minimum',
        'biaya_tambahan'
    ];
}