<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Laporan extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'laporans';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'judul',
        'tipe',
        'start_date',
        'end_date',
        'total_pesanan',
        'total_pendapatan',
        'avg_mape',
        'avg_mae',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'start_date'       => 'date',
            'end_date'         => 'date',
            'total_pendapatan' => 'decimal:2',
            'avg_mape'         => 'decimal:4',
            'avg_mae'          => 'decimal:4',
        ];
    }

    /**
     * Relationship: Laporan belongsTo User (admin who created it).
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
