<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'tipe_layanan',
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
     */
    public function detailTransaksi(): HasMany
    {
        return $this->hasMany(DetailTransaksi::class);
    }
}
