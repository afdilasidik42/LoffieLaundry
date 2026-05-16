<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mesin extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'mesins';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'nama_mesin',
        'kapasitas_max',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'kapasitas_max' => 'integer',
            'is_active'     => 'boolean',
        ];
    }
}
