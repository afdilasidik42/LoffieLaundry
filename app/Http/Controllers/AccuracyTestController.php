<?php

namespace App\Http\Controllers;

use App\Models\PrediksiLog;
use Illuminate\Support\Facades\Response;

class AccuracyTestController extends Controller
{
    /**
     * Tampilkan halaman uji akurasi GM(1,4) — ringkasan MAPE/MAE + tabel detail.
     */
    public function index()
    {
        $logs = PrediksiLog::with(['pesanan'])
            ->whereNotNull('actual_jam')
            ->whereNotNull('mape')
            ->latest()
            ->get();

        // Overall metrics
        $totalData = $logs->count();
        $avgMape   = $totalData > 0 ? round($logs->avg('mape'), 4) : null;
        $avgMae    = $totalData > 0 ? round($logs->avg('mae'), 4) : null;
        $minMape   = $totalData > 0 ? round($logs->min('mape'), 4) : null;
        $maxMape   = $totalData > 0 ? round($logs->max('mape'), 4) : null;

        // Lewis (1982) category
        $kategori = $this->kategoriMape($avgMape);

        // Data for Chart.js scatter plot
        $scatterData = $logs->map(function ($log) {
            return [
                'x'     => round((float) $log->prediksi_jam, 2),
                'y'     => round((float) $log->actual_jam, 2),
                'label' => optional($log->pesanan)->kode_pesanan ?? "LOG-{$log->id}",
            ];
        })->values();

        return view('admin.accuracy.index', compact(
            'logs', 'totalData', 'avgMape', 'avgMae',
            'minMape', 'maxMape', 'kategori', 'scatterData'
        ));
    }

    /**
     * Export hasil akurasi ke CSV.
     */
    public function exportCsv()
    {
        $logs = PrediksiLog::with(['pesanan'])
            ->whereNotNull('actual_jam')
            ->whereNotNull('mape')
            ->latest()
            ->get();

        $filename = 'Akurasi_GM14_' . now()->format('Ymd_His') . '.csv';

        return Response::streamDownload(function () use ($logs) {
            $handle = fopen('php://output', 'w');

            // BOM for Excel UTF-8
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'No',
                'Kode Pesanan',
                'Berat (kg)',
                'Complexity',
                'Kapasitas Mesin (%)',
                'Prediksi (jam)',
                'Aktual (jam)',
                'MAPE (%)',
                'MAE (jam)',
                'Kategori Lewis',
            ]);

            foreach ($logs as $i => $log) {
                fputcsv($handle, [
                    $i + 1,
                    optional($log->pesanan)->kode_pesanan ?? '-',
                    number_format($log->berat_input, 2),
                    $log->complexity_input,
                    number_format($log->kapasitas_input, 2),
                    number_format($log->prediksi_jam, 4),
                    number_format($log->actual_jam, 4),
                    number_format($log->mape, 4),
                    number_format($log->mae, 4),
                    $this->kategoriMape((float) $log->mape),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Klasifikasi akurasi MAPE berdasarkan Lewis (1982).
     */
    private function kategoriMape(?float $mape): string
    {
        if ($mape === null) return 'N/A';
        if ($mape < 10) return 'Sangat Baik';
        if ($mape < 20) return 'Baik';
        if ($mape < 50) return 'Layak';
        return 'Buruk';
    }
}
