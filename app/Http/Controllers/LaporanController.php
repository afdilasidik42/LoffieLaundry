<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Models\Pesanan;
use App\Models\PrediksiLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class LaporanController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Admin Side — Create & View Reports
    |--------------------------------------------------------------------------
    */

    /**
     * Admin: Show report generation form + list of saved reports.
     */
    public function adminIndex()
    {
        $laporans = Laporan::with('creator')
            ->latest()
            ->paginate(10);

        return view('admin.laporan.index', compact('laporans'));
    }

    /**
     * Admin: Generate & save a new report from date range.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'tipe'       => 'required|in:harian,mingguan,bulanan',
        ]);

        $startDate = $request->start_date;
        $endDate   = $request->end_date;
        $tipe      = $request->tipe;

        // Fetch pesanans in the date range
        $pesanans = Pesanan::whereBetween('tanggal_masuk', [$startDate, $endDate])->get();

        if ($pesanans->isEmpty()) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Tidak ada pesanan dalam rentang tanggal yang dipilih.');
        }

        // Aggregate calculations
        $totalPesanan    = $pesanans->count();
        $totalPendapatan = $pesanans->sum('total_biaya');

        // Fetch prediksi_logs related to these pesanans that have actual values
        $pesananIds  = $pesanans->pluck('id');
        $prediksiLogs = PrediksiLog::whereIn('pesanan_id', $pesananIds)
            ->whereNotNull('actual_jam')
            ->get();

        $avgMape = $prediksiLogs->isNotEmpty() ? $prediksiLogs->avg('mape') : null;
        $avgMae  = $prediksiLogs->isNotEmpty() ? $prediksiLogs->avg('mae') : null;

        // Build judul
        $tipeLabel = ucfirst($tipe);
        $judul = "Laporan {$tipeLabel} — " . \Carbon\Carbon::parse($startDate)->format('d M Y') . ' s/d ' . \Carbon\Carbon::parse($endDate)->format('d M Y');

        // Save the report
        $laporan = Laporan::create([
            'judul'            => $judul,
            'tipe'             => $tipe,
            'start_date'       => $startDate,
            'end_date'         => $endDate,
            'total_pesanan'    => $totalPesanan,
            'total_pendapatan' => $totalPendapatan,
            'avg_mape'         => $avgMape,
            'avg_mae'          => $avgMae,
            'created_by'       => Auth::id(),
        ]);

        return redirect()->route('admin.laporan.show', $laporan->id)
            ->with('success', 'Laporan berhasil dibuat.');
    }

    /**
     * Admin: Show report detail with individual transactions.
     */
    public function show($id)
    {
        $laporan = Laporan::with('creator')->findOrFail($id);

        // Fetch transactions within the report date range
        $pesanans = Pesanan::with(['detailTransaksi.pelanggan', 'detailTransaksi.layanan', 'prediksiLogs'])
            ->whereBetween('tanggal_masuk', [$laporan->start_date, $laporan->end_date])
            ->latest('tanggal_masuk')
            ->get();

        return view('admin.laporan.show', compact('laporan', 'pesanans'));
    }

    /*
    |--------------------------------------------------------------------------
    | Owner Side — View & Download Reports
    |--------------------------------------------------------------------------
    */

    /**
     * Owner: Read-only list of all saved reports.
     */
    public function ownerIndex()
    {
        $laporans = Laporan::with('creator')
            ->latest()
            ->paginate(10);

        return view('owner.laporan.index', compact('laporans'));
    }

    /**
     * Owner: Download report as PDF.
     */
    public function downloadPdf($id)
    {
        $laporan = Laporan::with('creator')->findOrFail($id);

        $pesanans = Pesanan::with(['detailTransaksi.pelanggan', 'detailTransaksi.layanan', 'prediksiLogs'])
            ->whereBetween('tanggal_masuk', [$laporan->start_date, $laporan->end_date])
            ->latest('tanggal_masuk')
            ->get();

        $pdf = Pdf::loadView('laporan.pdf_template', compact('laporan', 'pesanans'))
            ->setPaper('a4', 'landscape');

        $filename = 'Laporan_' . str_replace(' ', '_', $laporan->tipe) . '_' . $laporan->start_date->format('Ymd') . '_' . $laporan->end_date->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Owner: Download report as CSV.
     */
    public function downloadCsv($id)
    {
        $laporan = Laporan::findOrFail($id);

        $pesanans = Pesanan::with(['detailTransaksi.pelanggan', 'detailTransaksi.layanan', 'prediksiLogs'])
            ->whereBetween('tanggal_masuk', [$laporan->start_date, $laporan->end_date])
            ->latest('tanggal_masuk')
            ->get();

        $filename = 'Laporan_' . $laporan->tipe . '_' . $laporan->start_date->format('Ymd') . '_' . $laporan->end_date->format('Ymd') . '.csv';

        return Response::streamDownload(function () use ($pesanans) {
            $handle = fopen('php://output', 'w');

            // CSV Header
            fputcsv($handle, [
                'Kode Pesanan',
                'Pelanggan',
                'Layanan',
                'Berat (kg)',
                'Total Biaya (Rp)',
                'Prediksi (jam)',
                'Aktual (jam)',
                'MAPE (%)',
                'Status',
                'Tanggal Masuk',
            ]);

            // CSV Rows
            foreach ($pesanans as $pesanan) {
                $detail      = $pesanan->detailTransaksi->first();
                $prediksiLog = $pesanan->prediksiLogs->first();

                fputcsv($handle, [
                    $pesanan->kode_pesanan,
                    $detail?->pelanggan?->nama ?? '-',
                    $detail?->layanan?->jenis_layanan ?? '-',
                    $detail?->berat ?? '-',
                    number_format($pesanan->total_biaya, 2, '.', ''),
                    $prediksiLog?->prediksi_jam ?? '-',
                    $prediksiLog?->actual_jam ?? '-',
                    $prediksiLog?->mape !== null ? number_format($prediksiLog->mape, 2) : '-',
                    ucfirst($pesanan->status),
                    $pesanan->tanggal_masuk->format('Y-m-d'),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
