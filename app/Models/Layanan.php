<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Layanan extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'layanans';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'kode_layanan',
        'jenis_layanan',
        'harga',
        'complexity_score',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'harga' => 'decimal:2',
            'complexity_score' => 'integer',
        ];
    }

    /**
     * Relationship: Layanan hasMany DetailTransaksi.
     * Will be used in Sprint 2 when detail_transaksis table is created.
     */
    // public function detailTransaksi()
    // {
    //     return $this->hasMany(\App\Models\DetailTransaksi::class);
    // }
}
