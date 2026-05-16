<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'pelanggans';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'kode_pelanggan',
        'nama',
        'no_telepon',
        'alamat',
    ];

    /**
     * Relationship: Pelanggan hasMany DetailTransaksi.
     * Will be used in Sprint 2 when detail_transaksis table is created.
     */
    // public function detailTransaksi()
    // {
    //     return $this->hasMany(\App\Models\DetailTransaksi::class);
    // }
}
