<?php

namespace Database\Seeders;

use App\Models\Layanan;
use Illuminate\Database\Seeder;

class LayananSeeder extends Seeder
{
    /**
     * Seed tabel layanans dengan 4 layanan standar.
     */
    public function run(): void
    {
        $layanans = [
            [
                'kode_layanan'    => 'LYN-001',
                'jenis_layanan'   => 'Cuci Reguler',
                'harga'           => 6000,
                'complexity_score' => 1,
            ],
            [
                'kode_layanan'    => 'LYN-002',
                'jenis_layanan'   => 'Setrika',
                'harga'           => 5000,
                'complexity_score' => 2,
            ],
            [
                'kode_layanan'    => 'LYN-003',
                'jenis_layanan'   => 'Express',
                'harga'           => 10000,
                'complexity_score' => 3,
            ],
            [
                'kode_layanan'    => 'LYN-004',
                'jenis_layanan'   => 'Dry Cleaning',
                'harga'           => 15000,
                'complexity_score' => 5,
            ],
        ];

        foreach ($layanans as $layanan) {
            Layanan::firstOrCreate(
                ['kode_layanan' => $layanan['kode_layanan']],
                $layanan
            );
        }
    }
}
