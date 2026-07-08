<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\PrediksiLog;
use App\Services\GmPredictionService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PrediksiController extends Controller
{
    /**
     * Tampilkan riwayat semua prediksi GM(1,4) beserta akurasi MAPE/MAE.
     */
    public function index()
    {
        $logs = PrediksiLog::with(['pesanan.detailTransaksi.layanan'])
            ->latest()
            ->paginate(20);

        $totalPrediksi   = PrediksiLog::count();
        $prediksiSelesai = PrediksiLog::whereNotNull('actual_jam')->count();
        $prediksiProses  = $totalPrediksi - $prediksiSelesai;
        $avgMape         = $prediksiSelesai > 0 ? PrediksiLog::whereNotNull('actual_jam')->avg('mape') : null;

        return view('admin.prediksi.index', compact('logs', 'totalPrediksi', 'prediksiSelesai', 'prediksiProses', 'avgMape'));
    }

    /**
     * Re-run prediksi GM(1,4) untuk pesanan tertentu.
     * Hanya diperbolehkan untuk pesanan yang masih berstatus 'proses'.
     */
    public function rerun($pesananId)
    {
        $pesanan = Pesanan::with(['detailTransaksi.layanan'])->findOrFail($pesananId);

        if ($pesanan->status !== 'proses') {
            return redirect()->route('admin.prediksi.index')
                ->with('error', 'Re-run hanya dapat dilakukan untuk pesanan berstatus "proses".');
        }

        $detail = $pesanan->detailTransaksi->first();

        if (!$detail || !$detail->layanan) {
            return redirect()->route('admin.prediksi.index')
                ->with('error', 'Data detail transaksi atau layanan tidak ditemukan.');
        }

        $bebanInput      = $detail->layanan->tipe_layanan === 'satuan' ? (float) $detail->jumlah : (float) $detail->berat;
        $complexityInput = (int) $detail->layanan->complexity_score;
        $kapasitasInput  = (float) $detail->kapasitas_mesin;

        // Re-run GM(1,4) prediction
        $prediksiJam = GmPredictionService::predict([
            'beban'            => $bebanInput,
            'complexity_score' => $complexityInput,
            'kapasitas_mesin'  => $kapasitasInput,
        ]);

        // Update estimasi_selesai
        $waktuDasar      = Carbon::parse($pesanan->tanggal_masuk)->setTimeFrom(Carbon::now());
        $estimasiSelesai = $waktuDasar->copy()->addSeconds((int) round($prediksiJam * 3600));

        $pesanan->update([
            'estimasi_selesai' => $estimasiSelesai,
        ]);

        // Update or create prediction log
        PrediksiLog::updateOrCreate(
            ['pesanan_id' => $pesanan->id],
            [
                'berat_input'      => $bebanInput,
                'complexity_input' => $complexityInput,
                'kapasitas_input'  => $kapasitasInput,
                'prediksi_jam'     => $prediksiJam,
            ]
        );

        return redirect()->route('admin.prediksi.index')
            ->with('success', "Prediksi untuk {$pesanan->kode_pesanan} berhasil di-run ulang. Estimasi: {$estimasiSelesai->format('d M Y H:i')}");
    }
}
