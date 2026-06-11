<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\PrediksiLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatusController extends Controller
{
    /**
     * Display the Kanban board of all pesanans grouped by status.
     */
    public function index()
    {
        $pesanans = Pesanan::with(['detailTransaksi.pelanggan', 'detailTransaksi.layanan'])
            ->latest()
            ->get();

        return view('status.index', compact('pesanans'));
    }

    /**
     * Update pesanan status with strict one-way state transition guard.
     * Allowed: proses -> selesai -> diambil (no backward).
     * On 'selesai': triggers MAPE/MAE accuracy calculation.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => ['required', 'in:selesai,diambil'],
        ]);

        $pesanan   = Pesanan::findOrFail($id);
        $newStatus = $request->input('status');

        // State Transition Guard (one-way only)
        $allowedTransitions = [
            'proses'  => 'selesai',
            'selesai' => 'diambil',
        ];

        $currentStatus = $pesanan->status;

        if (!isset($allowedTransitions[$currentStatus]) || $allowedTransitions[$currentStatus] !== $newStatus) {
            return redirect()->route('admin.status.index')
                             ->with('error', "Transisi status dari \"{$currentStatus}\" ke \"{$newStatus}\" tidak diperbolehkan.");
        }

        // Status -> selesai: Trigger accuracy calculation
        if ($newStatus === 'selesai') {
            DB::transaction(function () use ($pesanan) {
                $now = Carbon::now();

                // 1. Set actual_selesai
                $pesanan->update([
                    'status'         => 'selesai',
                    'actual_selesai' => $now,
                ]);

                // 2. Calculate actual_jam = (actual_selesai - tanggal_masuk) / 3600
                //    Must use tanggal_masuk (not created_at) to be consistent with
                //    GmPredictionService::fetchHistoricalData() which uses tanggal_masuk
                //    for computing historical X1 (durasi_jam) training data.
                $tanggalMasuk = Carbon::parse($pesanan->tanggal_masuk);
                $diffSeconds  = $now->diffInSeconds($tanggalMasuk);
                $actualJam    = $diffSeconds / 3600;

                // 3. Retrieve prediksi_log for this pesanan
                $prediksiLog = PrediksiLog::where('pesanan_id', $pesanan->id)->first();

                if ($prediksiLog) {
                    $prediksiJam = (float) $prediksiLog->prediksi_jam;

                    // 4. Calculate MAPE and MAE
                    $mae  = abs($actualJam - $prediksiJam);
                    $mape = $actualJam > 0
                        ? (abs($actualJam - $prediksiJam) / $actualJam) * 100
                        : 0;

                    // 5. Persist accuracy metrics
                    $prediksiLog->update([
                        'actual_jam' => round($actualJam, 4),
                        'mape'       => round($mape, 4),
                        'mae'        => round($mae, 4),
                    ]);
                }
            });

            return redirect()->route('admin.status.index')
                             ->with('success', "Pesanan {$pesanan->kode_pesanan} ditandai SELESAI. Akurasi prediksi telah dihitung.");
        }

        // Status -> diambil: Simple update
        $pesanan->update(['status' => 'diambil']);

        return redirect()->route('admin.status.index')
                         ->with('success', "Pesanan {$pesanan->kode_pesanan} ditandai DIAMBIL.");
    }
}
