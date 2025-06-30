<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'driver_id',
        'tipe_kendaraan',
        'lokasi_jemput',
        'lokasi_tujuan',
        'jemput_latitude',
        'jemput_longitude',
        'tujuan_latitude',
        'tujuan_longitude',
        'jarak',
        'tarif',
        'status',
        'waktu_pesan',
        'waktu_selesai',
        'bukti_transaksi',
        'alasan_batal',
        'dibatalkan_oleh',
        'canceled_at',
    ];

    // ✅ Casting ke Carbon instance
    protected $casts = [
        'waktu_selesai' => 'datetime',
        'canceled_at' => 'datetime',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function driverCandidates()
    {
        return $this->hasMany(OrderDriverCandidate::class, 'order_id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function getPendapatanDriverAttribute()
    {
        return $this->tarif * 0.8;
    }

    public function getPendapatanAdminAttribute()
    {
        return $this->tarif * 0.2;
    }

}
