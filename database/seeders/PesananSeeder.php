<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Models\Pelanggan;
use App\Models\Layanan;
use App\Models\Bahan;
use App\Models\Pesanan;
use App\Models\DetailTransaksi;
use App\Models\PrediksiLog;

class PesananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $pelanggan = Pelanggan::first();
        $layanan = Layanan::first();
        $bahan = Bahan::first();

        // Fallback jika data master belum ada
        if (!$user || !$pelanggan || !$layanan || !$bahan) {
            $this->command->warn('Data master (User, Pelanggan, Layanan, Bahan) belum lengkap. Harap jalankan seeder master terlebih dahulu.');
            return;
        }

        DB::transaction(function () use ($user, $pelanggan, $layanan, $bahan) {
            for ($i = 1; $i <= 5; $i++) {
                $daysAgo = 7 - $i; // Rentang waktu mundur (6 sampai 2 hari lalu)
                
                $createdAt = Carbon::now()->subDays($daysAgo)->setHour(rand(8, 15))->setMinute(rand(0, 59));
                
                // Variasi durasi aktual: 24 hingga 72 jam dengan tambahan menit acak
                $hoursToAdd = rand(24, 72);
                $minutesToAdd = rand(0, 59);
                $actualSelesai = (clone $createdAt)->addHours($hoursToAdd)->addMinutes($minutesToAdd);

                // Perhitungan aktual jam dalam bentuk float (desimal)
                $actualJam = $actualSelesai->diffInMinutes($createdAt) / 60;

                // Simulasi nilai prediksi yang sedikit meleset dari aktual
                $prediksiJam = $actualJam + (rand(-15, 15) * 0.1);
                if ($prediksiJam < 1) $prediksiJam = 1; // Minimal 1 jam prediksi

                $error = abs($actualJam - $prediksiJam);
                $mape = $actualJam > 0 ? ($error / $actualJam) * 100 : 0;
                $mae = $error;

                $berat = rand(30, 100) / 10; // Rentang berat 3.00 - 10.00 kg
                $hargaPerBerat = 5000; // Asumsi harga per kg Rp 5.000
                $subTotal = $berat * $hargaPerBerat;
                
                $kapasitasOptions = [25.00, 40.00, 65.00, 80.00, 100.00];
                $kapasitasMesin = $kapasitasOptions[array_rand($kapasitasOptions)];
                
                $statusOptions = ['selesai', 'diambil'];
                $status = $statusOptions[array_rand($statusOptions)];

                // 1. Insert ke tabel pesanans
                $pesanan = Pesanan::create([
                    'kode_pesanan' => 'ORD-' . $createdAt->format('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                    'user_id' => $user->id,
                    'tanggal_masuk' => $createdAt->toDateString(),
                    'total_biaya' => $subTotal,
                    'status' => $status,
                    'estimasi_selesai' => (clone $createdAt)->addHours(48), // Estimasi standar 48 jam
                    'actual_selesai' => $actualSelesai,
                ]);

                // Update timestamps secara manual agar sesuai skenario masa lalu
                DB::table('pesanans')->where('id', $pesanan->id)->update([
                    'created_at' => $createdAt,
                    'updated_at' => $actualSelesai,
                ]);

                // 2. Insert ke tabel detail_transaksis
                $detail = DetailTransaksi::create([
                    'pesanan_id' => $pesanan->id,
                    'pelanggan_id' => $pelanggan->id,
                    'layanan_id' => $layanan->id,
                    'bahan_id' => $bahan->id,
                    'berat' => $berat,
                    'harga_per_berat' => $hargaPerBerat,
                    'sub_total' => $subTotal,
                    'kapasitas_mesin' => $kapasitasMesin,
                ]);

                DB::table('detail_transaksis')->where('id', $detail->id)->update([
                    'created_at' => $createdAt,
                    'updated_at' => $actualSelesai,
                ]);

                // 3. Insert ke tabel prediksi_logs
                $complexity = rand(1, 5); // Kompleksitas acak 1-5
                
                $log = PrediksiLog::create([
                    'pesanan_id' => $pesanan->id,
                    'berat_input' => $berat,
                    'complexity_input' => $complexity,
                    'kapasitas_input' => $kapasitasMesin,
                    'prediksi_jam' => $prediksiJam,
                    'actual_jam' => $actualJam,
                    'mape' => $mape,
                    'mae' => $mae,
                ]);

                DB::table('prediksi_logs')->where('id', $log->id)->update([
                    'created_at' => $createdAt,
                    'updated_at' => $actualSelesai,
                ]);
            }
        });
    }
}
