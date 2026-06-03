<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Models\Pelanggan;
use App\Models\Layanan;
use App\Models\Pesanan;
use App\Models\DetailTransaksi;
use App\Models\PrediksiLog;

class DatasetPesananSeeder extends Seeder
{
    /**
     * Mapping jenis jasa → complexity score (X3) dan kode layanan.
     * Skor: Setrika=2, Cuci Kering=2, Cuci Kering Setrika=3, Bedcover/Karpet/Selimut/Bantal/Jaket=5
     */
    private function getComplexityScore(string $jenisJasa): int
    {
        $jasa = strtolower(trim($jenisJasa));

        // Combo / multi-service → skor tertinggi
        if (str_contains($jasa, '+')) {
            return 5;
        }

        // Item khusus (bedcover, karpet, selimut, bantal, jaket, sprei)
        $itemKhusus = ['bedcover', 'bed cover', 'karpet', 'kapret', 'selimut', 'bantal', 'jaket', 'sprei'];
        foreach ($itemKhusus as $item) {
            if (str_contains($jasa, $item)) {
                return 5;
            }
        }

        // Cuci Kering Setrika (3 kata) = skor 3
        if (str_contains($jasa, 'cuci kering setrika') || str_contains($jasa, 'cuci kering  setrika')) {
            return 3;
        }

        // Cuci Kering saja = skor 2
        if (str_contains($jasa, 'cuci kering')) {
            return 2;
        }

        // Setrika saja (termasuk typo "setika") = skor 2
        if (str_contains($jasa, 'setrika') || str_contains($jasa, 'setika')) {
            return 2;
        }

        // Default fallback
        return 3;
    }

    /**
     * Mengekstrak berat (kg) dari kolom Timbangan.
     * Contoh: "6.4 kg" → 6.4, "2 bedcover" → 2.0, "Karpet" → 1.0
     */
    private function extractBerat(string $timbangan): float
    {
        $timbangan = trim($timbangan);

        // Jika hanya teks tanpa angka (misal: "Karpet", "Selimut"), return default 1.0
        if (!preg_match('/[\d]/', $timbangan)) {
            return 1.0;
        }

        // Ambil angka desimal pertama dari string
        // Menangani kasus seperti "6.4 kg", "2 Bedcover", "12. 5 kg" (spasi di tengah angka)
        $cleaned = preg_replace('/(\d+)\.\s+(\d+)/', '$1.$2', $timbangan); // Fix "12. 5" → "12.5"

        if (preg_match('/(\d+\.?\d*)/', $cleaned, $matches)) {
            return (float) $matches[1];
        }

        return 1.0;
    }

    /**
     * Mengekstrak durasi hari dari kolom Lama Proses.
     * Contoh: "3 Hari" → 3
     */
    private function extractDurasiHari(string $lamaProses): int
    {
        if (preg_match('/(\d+)/', trim($lamaProses), $matches)) {
            return (int) $matches[1];
        }
        return 3; // default 3 hari
    }

    /**
     * Mengekstrak jumlah customer harian dari kolom Jumlah Transaksi Harian.
     * Contoh: "5 Customer" → 5, kosong → 5 (default aman)
     */
    private function extractJumlahCustomer(string $value): int
    {
        $value = trim($value);
        if (empty($value)) {
            return 5; // rata-rata aman
        }
        if (preg_match('/(\d+)/', $value, $matches)) {
            return (int) $matches[1];
        }
        return 5;
    }

    /**
     * Normalisasi nama jenis layanan untuk konsistensi di tabel layanans.
     */
    private function normalizeJenisLayanan(string $jenisJasa): string
    {
        $jasa = trim($jenisJasa);

        // Mapping normalisasi
        $map = [
            'setika'     => 'Setrika',              // typo
            'bedcoverr'  => 'Bedcover',              // typo
            'bed cover'  => 'Bedcover',              // variasi penulisan
            'kapret'     => 'Karpet',                // typo
        ];

        $lower = strtolower($jasa);
        foreach ($map as $typo => $corrected) {
            if ($lower === $typo) {
                return $corrected;
            }
        }

        // Capitalize per kata untuk konsistensi
        return ucwords(strtolower($jasa));
    }

    /**
     * Generate kode layanan dari nama jenis layanan.
     * Contoh: "Cuci Kering Setrika" → "LYN-CKS"
     */
    private function generateKodeLayanan(string $jenisLayanan): string
    {
        $words = explode(' ', trim($jenisLayanan));
        $code = '';
        foreach ($words as $word) {
            $code .= strtoupper(substr($word, 0, 1));
        }

        // Pastikan unik dengan menambahkan counter jika perlu
        $base = 'LYN-' . $code;
        $existing = Layanan::where('kode_layanan', $base)->exists();
        if (!$existing) {
            return $base;
        }

        $counter = 2;
        while (Layanan::where('kode_layanan', $base . $counter)->exists()) {
            $counter++;
        }
        return $base . $counter;
    }

    /**
     * Run the database seeds.
     *
     * Membaca dataset_loffie.csv dan menyuntikkan data riil operasional
     * Loffie Laundry ke tabel pesanans, detail_transaksis, dan prediksi_logs.
     */
    public function run(): void
    {
        $csvPath = database_path('seeders/dataset_loffie.csv');

        if (!file_exists($csvPath)) {
            $this->command->error("File CSV tidak ditemukan di: {$csvPath}");
            return;
        }

        $handle = fopen($csvPath, 'r');
        if ($handle === false) {
            $this->command->error("Gagal membuka file CSV: {$csvPath}");
            return;
        }

        // Ambil user admin sebagai pembuat pesanan
        $adminUser = User::where('role', 'admin')->first();
        if (!$adminUser) {
            $this->command->error('User admin belum ada. Jalankan UserSeeder terlebih dahulu.');
            fclose($handle);
            return;
        }

        // Baca header
        $header = fgetcsv($handle, 1000, ',');
        if ($header === false) {
            $this->command->error('File CSV kosong atau format header tidak valid.');
            fclose($handle);
            return;
        }

        // Trim header untuk menghindari whitespace tersembunyi
        $header = array_map('trim', $header);

        $this->command->info('Memulai import dataset Loffie Laundry...');

        $rowNumber = 1;
        $imported  = 0;
        $skipped   = 0;

        DB::transaction(function () use ($handle, $header, $adminUser, &$rowNumber, &$imported, &$skipped) {
            // Cache layanan yang sudah dibuat untuk menghindari query berulang
            $layananCache = [];

            // Cache pelanggan yang sudah dibuat
            $pelangganCache = [];

            // Tracking jumlah customer per tanggal (untuk X4)
            $dailyCustomerCount = [];

            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                $rowNumber++;

                // Pastikan jumlah kolom sesuai header
                if (count($row) < count($header)) {
                    $row = array_pad($row, count($header), '');
                }

                // Buat array asosiatif dari header + row
                $data = array_combine($header, array_map('trim', $row));

                // ── SKIP: Baris dengan Nama Customer atau Timbangan kosong ──
                $namaCustomer = $data['Nama Customer'] ?? '';
                $timbangan    = $data['Timbangan'] ?? '';
                $jenisJasa    = $data['Jenis Jasa'] ?? '';
                $hargaStr     = $data['Harga'] ?? '';

                if (empty($namaCustomer) || empty($timbangan)) {
                    $skipped++;
                    continue;
                }

                // Skip baris yang tidak memiliki jenis jasa dan harga (subtotal/summary rows)
                if (empty($jenisJasa) && empty($hargaStr)) {
                    $skipped++;
                    continue;
                }

                // ────────────────────────────────────────────────────────
                // PARSING VARIABEL GM(1,4)
                // ────────────────────────────────────────────────────────

                // X1: Durasi Aktual (jam)
                $durasiHari = $this->extractDurasiHari($data['Lama Proses'] ?? '3 Hari');
                $durasiJam  = $durasiHari * 24; // Konversi ke jam

                // X2: Berat / Timbangan (kg)
                $berat = $this->extractBerat($timbangan);

                // X3: Kompleksitas Layanan
                $complexityScore = $this->getComplexityScore($jenisJasa);

                // X4: Kapasitas Mesin (utilitas %)
                $jumlahCustomerStr = $data['Jumlah Transaksi Harian'] ?? '';
                $jumlahCustomer    = $this->extractJumlahCustomer($jumlahCustomerStr);

                // Simpan jumlah customer harian berdasarkan tanggal untuk baris lain di hari yang sama
                $tanggalMasukStr = $data['Tanggal Masuk'] ?? '';
                if (!empty($jumlahCustomerStr) && !empty($tanggalMasukStr)) {
                    $dailyCustomerCount[$tanggalMasukStr] = $jumlahCustomer;
                } elseif (isset($dailyCustomerCount[$tanggalMasukStr])) {
                    $jumlahCustomer = $dailyCustomerCount[$tanggalMasukStr];
                }

                // Formula kapasitas mesin: (customer/20)*100, max 100%
                $kapasitasMesin = (float) min(($jumlahCustomer / 20) * 100, 100);

                // ────────────────────────────────────────────────────────
                // PARSING TANGGAL
                // ────────────────────────────────────────────────────────

                // Tanggal masuk: Buat Carbon dari tahun 2025
                $tanggalMasuk = $this->parseTanggal($tanggalMasukStr, 2025);
                if (!$tanggalMasuk) {
                    $skipped++;
                    continue;
                }

                // Set jam masuk: 08:00
                $tanggalMasuk->setHour(8)->setMinute(0)->setSecond(0);

                // Actual selesai: tanggal masuk + durasi jam
                $actualSelesai = (clone $tanggalMasuk)->addHours($durasiJam);

                // Estimasi selesai: sama dengan actual karena ini data latih bersih
                $estimasiSelesai = clone $actualSelesai;

                // ────────────────────────────────────────────────────────
                // 1. PELANGGAN: firstOrCreate berdasarkan nama
                // ────────────────────────────────────────────────────────

                $namaNormalized = trim($namaCustomer);
                if (!isset($pelangganCache[$namaNormalized])) {
                    $pelanggan = Pelanggan::firstOrCreate(
                        ['nama' => $namaNormalized],
                        [
                            'kode_pelanggan' => 'PLG-' . str_pad(
                                Pelanggan::count() + 1,
                                4,
                                '0',
                                STR_PAD_LEFT
                            ),
                            'no_telepon' => null,
                            'alamat'     => null,
                        ]
                    );
                    $pelangganCache[$namaNormalized] = $pelanggan;
                } else {
                    $pelanggan = $pelangganCache[$namaNormalized];
                }

                // ────────────────────────────────────────────────────────
                // 2. LAYANAN: firstOrCreate berdasarkan jenis layanan
                // ────────────────────────────────────────────────────────

                $jenisLayananNormalized = $this->normalizeJenisLayanan($jenisJasa);

                if (!isset($layananCache[$jenisLayananNormalized])) {
                    $layanan = Layanan::firstOrCreate(
                        ['jenis_layanan' => $jenisLayananNormalized],
                        [
                            'kode_layanan'    => $this->generateKodeLayanan($jenisLayananNormalized),
                            'harga'           => 0, // Harga bervariasi per order, set 0 sebagai default
                            'complexity_score' => $complexityScore,
                        ]
                    );
                    $layananCache[$jenisLayananNormalized] = $layanan;
                } else {
                    $layanan = $layananCache[$jenisLayananNormalized];
                }

                // ────────────────────────────────────────────────────────
                // 3. PESANAN (Header): Insert ke tabel pesanans
                // ────────────────────────────────────────────────────────

                // Harga dari CSV (dalam ribuan → kalikan 1000)
                $harga = $this->extractHarga($hargaStr);

                $kodePesanan = 'ORD-DATASET-' . str_pad($imported + 1, 4, '0', STR_PAD_LEFT);

                $pesanan = Pesanan::create([
                    'kode_pesanan'    => $kodePesanan,
                    'user_id'         => $adminUser->id,
                    'tanggal_masuk'   => $tanggalMasuk->toDateString(),
                    'total_biaya'     => $harga,
                    'status'          => 'selesai',
                    'estimasi_selesai' => $estimasiSelesai,
                    'actual_selesai'   => $actualSelesai,
                ]);

                // Update timestamps agar sesuai kronologi dataset
                DB::table('pesanans')->where('id', $pesanan->id)->update([
                    'created_at' => $tanggalMasuk,
                    'updated_at' => $actualSelesai,
                ]);

                // ────────────────────────────────────────────────────────
                // 4. DETAIL TRANSAKSI (Line Item)
                // ────────────────────────────────────────────────────────

                $hargaPerBerat = $berat > 0 ? round($harga / $berat, 2) : 0;

                $detail = DetailTransaksi::create([
                    'pesanan_id'     => $pesanan->id,
                    'pelanggan_id'   => $pelanggan->id,
                    'layanan_id'     => $layanan->id,
                    'bahan_id'       => null,
                    'berat'          => $berat,
                    'harga_per_berat' => $hargaPerBerat,
                    'sub_total'      => $harga,
                    'kapasitas_mesin' => $kapasitasMesin,
                ]);

                DB::table('detail_transaksis')->where('id', $detail->id)->update([
                    'created_at' => $tanggalMasuk,
                    'updated_at' => $actualSelesai,
                ]);

                // ────────────────────────────────────────────────────────
                // 5. PREDIKSI LOG (Audit GM(1,4))
                //    prediksi_jam = actual_jam agar data latih bersih (error 0)
                // ────────────────────────────────────────────────────────

                $log = PrediksiLog::create([
                    'pesanan_id'       => $pesanan->id,
                    'berat_input'      => $berat,
                    'complexity_input' => $complexityScore,
                    'kapasitas_input'  => $kapasitasMesin,
                    'prediksi_jam'     => $durasiJam,
                    'actual_jam'       => $durasiJam,
                    'mape'             => 0.0000,
                    'mae'              => 0.0000,
                ]);

                DB::table('prediksi_logs')->where('id', $log->id)->update([
                    'created_at' => $tanggalMasuk,
                    'updated_at' => $actualSelesai,
                ]);

                $imported++;
            }
        });

        fclose($handle);

        $this->command->info("✅ Import selesai! {$imported} baris berhasil diimport, {$skipped} baris di-skip.");
    }

    /**
     * Parse tanggal dari format CSV (misal: "2-Sep", "15-Oct") ke Carbon.
     */
    private function parseTanggal(string $tanggalStr, int $tahun): ?Carbon
    {
        $tanggalStr = trim($tanggalStr);
        if (empty($tanggalStr)) {
            return null;
        }

        // Mapping bulan singkatan ke angka
        $bulanMap = [
            'jan' => 1,  'feb' => 2,  'mar' => 3,  'apr' => 4,
            'may' => 5,  'jun' => 6,  'jul' => 7,  'aug' => 8,
            'sep' => 9,  'oct' => 10, 'nov' => 11, 'dec' => 12,
        ];

        // Parse format "2-Sep", "15-Oct", dll
        if (preg_match('/^(\d{1,2})-(\w{3})$/i', $tanggalStr, $matches)) {
            $hari  = (int) $matches[1];
            $bulanStr = strtolower($matches[2]);

            if (isset($bulanMap[$bulanStr])) {
                try {
                    return Carbon::create($tahun, $bulanMap[$bulanStr], $hari);
                } catch (\Exception $e) {
                    return null;
                }
            }
        }

        return null;
    }

    /**
     * Mengekstrak harga dari string CSV.
     * CSV menampilkan harga dalam ribuan (misal "38" = Rp 38.000).
     * Mengalikan dengan 1000 untuk mendapatkan harga penuh.
     */
    private function extractHarga(string $hargaStr): float
    {
        $hargaStr = trim($hargaStr);
        if (empty($hargaStr)) {
            return 0;
        }

        // Hapus karakter non-numerik kecuali titik
        $cleaned = preg_replace('/[^\d.]/', '', $hargaStr);
        $harga   = (float) $cleaned;

        // Harga di CSV dalam ribuan, kalikan 1000
        return $harga * 1000;
    }
}
