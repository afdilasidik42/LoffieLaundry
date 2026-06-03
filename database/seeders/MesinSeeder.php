<?php

namespace Database\Seeders;

use App\Models\Mesin;
use Illuminate\Database\Seeder;

class MesinSeeder extends Seeder
{
    /**
     * Seed tabel mesins dengan 3 data mesin.
     */
    public function run(): void
    {
        $mesins = [
            [
                'nama_mesin'    => 'Mesin Cuci LG 15kg',
                'kapasitas_max' => 15,
                'is_active'     => true,
            ],
            [
                'nama_mesin'    => 'Mesin Cuci Samsung 10kg',
                'kapasitas_max' => 10,
                'is_active'     => true,
            ],
            [
                'nama_mesin'    => 'Mesin Pengering 20kg',
                'kapasitas_max' => 20,
                'is_active'     => true,
            ],
        ];

        foreach ($mesins as $mesin) {
            Mesin::firstOrCreate(
                ['nama_mesin' => $mesin['nama_mesin']],
                $mesin
            );
        }
    }
}
