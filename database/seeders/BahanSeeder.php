<?php

namespace Database\Seeders;

use App\Models\Bahan;
use Illuminate\Database\Seeder;

class BahanSeeder extends Seeder
{
    /**
     * Seed tabel bahans dengan 2 bahan tambahan.
     */
    public function run(): void
    {
        $bahans = [
            [
                'kode_bahan'   => 'BHN-001',
                'nama_bahan'   => 'Parfum Premium',
                'biaya_per_kg' => 2000,
            ],
            [
                'kode_bahan'   => 'BHN-002',
                'nama_bahan'   => 'Softener Ekstra',
                'biaya_per_kg' => 1500,
            ],
        ];

        foreach ($bahans as $bahan) {
            Bahan::firstOrCreate(
                ['kode_bahan' => $bahan['kode_bahan']],
                $bahan
            );
        }
    }
}
