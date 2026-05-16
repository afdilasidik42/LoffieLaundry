<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailTransaksi extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'detail_transaksis';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'pesanan_id',
        'pelanggan_id',
        'layanan_id',
        'bahan_id',
        'berat',
        'harga_per_berat',
        'sub_total',
        'kapasitas_mesin',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'berat'           => 'decimal:2',
            'harga_per_berat' => 'decimal:2',
            'sub_total'       => 'decimal:2',
            'kapasitas_mesin' => 'decimal:2',
        ];
    }

    /**
     * Relationship: DetailTransaksi belongsTo Pesanan.
     */
    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class);
    }

    /**
     * Relationship: DetailTransaksi belongsTo Pelanggan.
     */
    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class);
    }

    /**
     * Relationship: DetailTransaksi belongsTo Layanan.
     */
    public function layanan(): BelongsTo
    {
        return $this->belongsTo(Layanan::class);
    }

    /**
     * Relationship: DetailTransaksi belongsTo Bahan.
     */
    public function bahan(): BelongsTo
    {
        return $this->belongsTo(Bahan::class);
    }
}
