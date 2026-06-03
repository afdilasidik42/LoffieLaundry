<?php

namespace App\Http\Controllers;

use App\Models\DetailTransaksi;
use App\Models\Pesanan;
use App\Models\PrediksiLog;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GrafikController extends Controller
{
    /**
     * Halaman Kelola Grafik — menampilkan semua chart interaktif.
     */
    public function index()
    {
        return view('owner.grafik.index');
    }

    /**
     * API: Ambil data akurasi prediksi untuk Chart.js Line Chart.
     *
     * Mengambil maksimal 30 data prediksi_logs terbaru yang sudah memiliki
     * nilai actual_jam (order selesai). Mengembalikan JSON berisi labels,
     * predicted, actual, mape, dan avg_mape.
     */
    public function prediksiAkurasi(): JsonResponse
    {
        $logs = PrediksiLog::whereNotNull('actual_jam')
            ->latest()
            ->take(30)
            ->get()
            ->reverse()          // Urutkan kronologis (paling lama → paling baru)
            ->values();

        // Ambil kode_pesanan sebagai label via relasi
        $labels    = [];
        $predicted = [];
        $actual    = [];
        $mape      = [];

        foreach ($logs as $log) {
            $labels[]    = optional($log->pesanan)->kode_pesanan ?? "LOG-{$log->id}";
            $predicted[] = round((float) $log->prediksi_jam, 2);
            $actual[]    = round((float) $log->actual_jam, 2);
            $mape[]      = $log->mape !== null ? round((float) $log->mape, 2) : null;
        }

        // Hitung rata-rata MAPE (abaikan null)
        $validMape = array_filter($mape, fn($v) => $v !== null);
        $avgMape   = count($validMape) > 0
            ? round(array_sum($validMape) / count($validMape), 2)
            : null;

        return response()->json([
            'labels'    => $labels,
            'predicted' => $predicted,
            'actual'    => $actual,
            'mape'      => $mape,
            'avg_mape'  => $avgMape,
        ]);
    }

    /**
     * API: Ambil data volume transaksi & pendapatan bulanan untuk Chart.js.
     *
     * Mendukung filter via query parameter `range`:
     *   - '6bulan' → 6 bulan terakhir
     *   - 'tahun'  → tahun berjalan (default)
     */
    public function volumeTransaksi(Request $request): JsonResponse
    {
        $range = $request->query('range', 'tahun');

        $query = Pesanan::query();

        if ($range === '6bulan') {
            $query->where('tanggal_masuk', '>=', Carbon::now()->subMonths(6)->startOfMonth());
        } else {
            // Default: tahun berjalan
            $query->whereYear('tanggal_masuk', Carbon::now()->year);
        }

        $data = $query
            ->select(
                DB::raw('MONTH(tanggal_masuk) as bulan'),
                DB::raw('YEAR(tanggal_masuk) as tahun'),
                DB::raw('COUNT(*) as volume'),
                DB::raw('COALESCE(SUM(total_biaya), 0) as pendapatan')
            )
            ->groupBy('tahun', 'bulan')
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get();

        $namaBulan = [
            1  => 'Jan', 2  => 'Feb', 3  => 'Mar', 4  => 'Apr',
            5  => 'Mei', 6  => 'Jun', 7  => 'Jul', 8  => 'Agu',
            9  => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
        ];

        $labels    = [];
        $volume    = [];
        $revenue   = [];

        foreach ($data as $row) {
            $labels[]  = ($namaBulan[$row->bulan] ?? $row->bulan) . ' ' . $row->tahun;
            $volume[]  = (int) $row->volume;
            $revenue[] = round((float) $row->pendapatan, 0);
        }

        return response()->json([
            'labels'  => $labels,
            'volume'  => $volume,
            'revenue' => $revenue,
            'range'   => $range,
        ]);
    }

    /**
     * API: Ambil data tren pelanggan untuk Chart.js.
     *
     * Query 1 — Top 10 Pelanggan: Agregasi dari detail_transaksis,
     * dikelompokkan berdasarkan pelanggan_id, hitung frekuensi (COUNT),
     * urutkan paling banyak, batasi 10.
     *
     * Query 2 — Distribusi Layanan: Hitung frekuensi penggunaan setiap
     * jenis layanan dari detail_transaksis JOIN layanans,
     * dikelompokkan berdasarkan jenis_layanan.
     */
    public function trenPelanggan(): JsonResponse
    {
        // Query 1: Top 10 pelanggan berdasarkan frekuensi transaksi
        $topPelanggan = DetailTransaksi::with('pelanggan')
            ->select('pelanggan_id', DB::raw('COUNT(*) as frekuensi'))
            ->groupBy('pelanggan_id')
            ->orderByDesc('frekuensi')
            ->limit(10)
            ->get();

        $pelangganLabels = [];
        $pelangganData   = [];

        foreach ($topPelanggan as $item) {
            $pelangganLabels[] = optional($item->pelanggan)->nama ?? 'Pelanggan #' . $item->pelanggan_id;
            $pelangganData[]   = (int) $item->frekuensi;
        }

        // Query 2: Distribusi jenis layanan
        $distribusiLayanan = DetailTransaksi::join('layanans', 'detail_transaksis.layanan_id', '=', 'layanans.id')
            ->select('layanans.jenis_layanan', DB::raw('COUNT(*) as total'))
            ->groupBy('layanans.jenis_layanan')
            ->orderByDesc('total')
            ->get();

        $layananLabels = [];
        $layananData   = [];

        foreach ($distribusiLayanan as $item) {
            $layananLabels[] = $item->jenis_layanan;
            $layananData[]   = (int) $item->total;
        }

        return response()->json([
            'pelanggan' => [
                'labels' => $pelangganLabels,
                'data'   => $pelangganData,
            ],
            'layanan' => [
                'labels' => $layananLabels,
                'data'   => $layananData,
            ],
        ]);
    }
}
