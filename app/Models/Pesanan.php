<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pesanan extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'pesanans';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'kode_pesanan',
        'user_id',
        'tanggal_masuk',
        'total_biaya',
        'status',
        'estimasi_selesai',
        'actual_selesai',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'tanggal_masuk'    => 'date',
            'total_biaya'      => 'decimal:2',
            'estimasi_selesai' => 'datetime',
            'actual_selesai'   => 'datetime',
        ];
    }

    /**
     * Relationship: Pesanan belongsTo User (admin who created it).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Pesanan hasMany DetailTransaksi.
     */
    public function detailTransaksi(): HasMany
    {
        return $this->hasMany(DetailTransaksi::class);
    }

    /**
     * Relationship: Pesanan hasMany PrediksiLog.
     */
    public function prediksiLogs(): HasMany
    {
        return $this->hasMany(PrediksiLog::class);
    }
}
