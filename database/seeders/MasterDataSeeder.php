<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    /**
     * Seed semua data master (Layanan, Mesin, Bahan).
     * Jalankan: php artisan db:seed --class=MasterDataSeeder
     */
    public function run(): void
    {
        $this->call([
            LayananSeeder::class,
            MesinSeeder::class,
            BahanSeeder::class,
        ]);
    }
}
