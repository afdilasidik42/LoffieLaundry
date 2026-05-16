<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bahan extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'bahans';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'kode_bahan',
        'nama_bahan',
        'biaya_per_kg',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'biaya_per_kg' => 'decimal:2',
        ];
    }

    /**
     * Relationship: Bahan hasMany DetailTransaksi.
     * Will be used in Sprint 2 when detail_transaksis table is created.
     */
    // public function detailTransaksi()
    // {
    //     return $this->hasMany(\App\Models\DetailTransaksi::class);
    // }
}
