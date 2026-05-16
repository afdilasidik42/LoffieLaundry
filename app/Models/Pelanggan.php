<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
     */
    public function detailTransaksi(): HasMany
    {
        return $this->hasMany(DetailTransaksi::class);
    }
}
