<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrediksiLog extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'prediksi_logs';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'pesanan_id',
        'berat_input',
        'complexity_input',
        'kapasitas_input',
        'prediksi_jam',
        'actual_jam',
        'mape',
        'mae',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'berat_input'      => 'decimal:2',
            'complexity_input' => 'integer',
            'kapasitas_input'  => 'decimal:2',
            'prediksi_jam'     => 'decimal:4',
            'actual_jam'       => 'decimal:4',
            'mape'             => 'decimal:4',
            'mae'              => 'decimal:4',
        ];
    }

    /**
     * Relationship: PrediksiLog belongsTo Pesanan.
     */
    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class);
    }
}
