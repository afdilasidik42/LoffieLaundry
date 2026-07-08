<?php

namespace Database\Seeders;

use App\Models\Layanan;
use App\Models\Mesin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatasetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFile = database_path('seeders/Data Layanan.csv');
        if (!file_exists($csvFile)) {
            $this->command->error("File $csvFile not found.");
            return;
        }

        $lines = file($csvFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        $mode = ''; // 'kiloan', 'satuan', 'mesin'

        foreach ($lines as $line) {
            $data = str_getcsv($line);

            // Clean weird invisible characters (e.g., non-breaking spaces \xC2\xA0)
            $data = array_map(function($val) {
                return trim(str_replace("\xC2\xA0", ' ', $val));
            }, $data);
            
            // Check headers to switch mode
            if (str_contains(strtolower($data[0] ?? ''), 'jenis layanan') && str_contains(strtolower($data[2] ?? ''), 'harga / kg')) {
                $mode = 'kiloan';
                continue;
            } elseif (str_contains(strtolower($data[0] ?? ''), 'laundry satuan') && str_contains(strtolower($data[2] ?? ''), 'harga satuan')) {
                $mode = 'satuan';
                continue;
            } elseif (str_contains(strtolower($data[0] ?? ''), 'mesin') && str_contains(strtolower($data[1] ?? ''), 'brand')) {
                $mode = 'mesin';
                continue;
            }

            if (!isset($data[0]) || trim($data[0]) === '') {
                continue;
            }

            if ($mode === 'kiloan' && isset($data[1], $data[2])) {
                $jenisLayanan = trim($data[0]);
                $lama = strtolower(trim($data[1]));
                $harga = (float) trim($data[2]);
                
                $complexity = str_contains($lama, '1 hari') ? 3 : 1;

                Layanan::updateOrCreate(
                    ['jenis_layanan' => $jenisLayanan],
                    [
                        'kode_layanan' => 'KIL-' . strtoupper(Str::random(4)),
                        'tipe_layanan' => 'kiloan',
                        'harga' => $harga,
                        'complexity_score' => $complexity,
                    ]
                );
            } elseif ($mode === 'satuan' && isset($data[1], $data[2])) {
                $jenisLayanan = trim($data[0]);
                $lama = strtolower(trim($data[1]));
                $harga = (float) trim($data[2]);

                $complexity = 2; // Default for satuan
                if (str_contains($lama, '1 hari')) $complexity = 3;
                if (str_contains($lama, '4') || str_contains($lama, '5')) $complexity = 4;

                Layanan::updateOrCreate(
                    ['jenis_layanan' => $jenisLayanan],
                    [
                        'kode_layanan' => 'SAT-' . strtoupper(Str::random(4)),
                        'tipe_layanan' => 'satuan',
                        'harga' => $harga,
                        'complexity_score' => $complexity,
                    ]
                );
            } elseif ($mode === 'mesin' && isset($data[1], $data[2])) {
                $nama = trim($data[0]);
                $brand = trim($data[1]);
                // Parse capacity, e.g., "9 kg" -> 9
                preg_match('/(\d+)/', $data[2], $matches);
                $kapasitas = isset($matches[1]) ? (int) $matches[1] : 0;
                
                if ($kapasitas > 0) {
                    Mesin::updateOrCreate(
                        ['nama_mesin' => "$nama $brand ($kapasitas kg)"],
                        [
                            'kapasitas_max' => $kapasitas,
                            'is_active' => true
                        ]
                    );
                }
            }
        }
        
        $this->command->info('Dataset Layanan, Satuan, dan Mesin berhasil di-import dari CSV!');
    }
}
