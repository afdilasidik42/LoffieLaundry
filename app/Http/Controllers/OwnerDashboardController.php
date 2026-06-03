<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\PrediksiLog;
use Carbon\Carbon;

class OwnerDashboardController extends Controller
{
    /**
     * Tampilkan dashboard owner dengan overview cards (data riil).
     */
    public function index()
    {
        // Revenue bulan ini
        $revenueBulanIni = Pesanan::whereMonth('tanggal_masuk', Carbon::now()->month)
            ->whereYear('tanggal_masuk', Carbon::now()->year)
            ->sum('total_biaya');

        // Total transaksi bulan ini
        $totalTransaksi = Pesanan::whereMonth('tanggal_masuk', Carbon::now()->month)
            ->whereYear('tanggal_masuk', Carbon::now()->year)
            ->count();

        // Rata-rata MAPE prediksi (dari semua log yang punya actual_jam)
        $avgMape = PrediksiLog::whereNotNull('actual_jam')
            ->whereNotNull('mape')
            ->avg('mape');

        $overview = [
            [
                'label' => 'Revenue Bulan Ini',
                'value' => 'Rp' . number_format($revenueBulanIni, 0, ',', '.'),
                'note'  => Carbon::now()->translatedFormat('F Y'),
                'icon'  => 'currency',
                'color' => 'emerald',
            ],
            [
                'label' => 'Total Transaksi',
                'value' => number_format($totalTransaksi),
                'note'  => 'Bulan berjalan',
                'icon'  => 'receipt',
                'color' => 'sky',
            ],
            [
                'label' => 'Akurasi Prediksi MAPE',
                'value' => $avgMape !== null ? number_format($avgMape, 2) . '%' : 'N/A',
                'note'  => $avgMape !== null ? $this->kategoriMape($avgMape) : 'Belum ada data',
                'icon'  => 'chart',
                'color' => 'violet',
            ],
        ];

        return view('owner.dashboard', compact('overview'));
    }

    /**
     * Klasifikasi akurasi berdasarkan Lewis (1982).
     */
    private function kategoriMape(float $mape): string
    {
        if ($mape < 10) return 'Sangat Baik (< 10%)';
        if ($mape < 20) return 'Baik (10–20%)';
        if ($mape < 50) return 'Layak (20–50%)';
        return 'Buruk (> 50%)';
    }
}
