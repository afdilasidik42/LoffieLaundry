<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use App\Models\DetailTransaksi;
use App\Models\Layanan;
use App\Models\Mesin;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\PrediksiLog;
use App\Services\GmPredictionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PesananController extends Controller
{
    /**
     * Display a listing of pesanans with search, filter & pagination.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $status = $request->query('status');

        $pesanans = Pesanan::with(['detailTransaksi.pelanggan', 'detailTransaksi.layanan', 'user'])
            ->when($search, function ($query, $search) {
                $query->where('kode_pesanan', 'LIKE', "%{$search}%")
                      ->orWhereHas('detailTransaksi.pelanggan', function ($q) use ($search) {
                          $q->where('nama', 'LIKE', "%{$search}%");
                      });
            })
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('pesanan.index', compact('pesanans', 'search', 'status'));
    }

    /**
     * Show the form for creating a new pesanan.
     */
    public function create()
    {
        $pelanggans = Pelanggan::orderBy('nama')->get();
        $layanans   = Layanan::orderBy('jenis_layanan')->get();
        $bahans     = Bahan::orderBy('nama_bahan')->get();

        return view('pesanan.create', compact('pelanggans', 'layanans', 'bahans'));
    }

    /**
     * Store a newly created pesanan with detail transaksis (DB transaction).
     *
     * After the transaction commits successfully, the GM(1,4) prediction engine
     * is invoked to estimate the completion time. The result is persisted to
     * both pesanans.estimasi_selesai and the prediksi_logs audit table.
     */
    public function store(Request $request)
    {
        if ($request->has('berat')) {
            $request->merge(['berat' => str_replace(',', '.', $request->input('berat'))]);
        }

        $validated = $request->validate([
            'pelanggan_id'  => ['required', 'exists:pelanggans,id'],
            'layanan_id'    => ['required', 'exists:layanans,id'],
            'bahan_id'      => ['nullable', 'exists:bahans,id'],
            'berat'         => ['required', 'numeric', 'min:0.1', 'max:9999.99'],
            'tanggal_masuk' => ['required', 'date'],
        ]);

        $layanan   = Layanan::findOrFail($validated['layanan_id']);
        $bahan     = $validated['bahan_id'] ? Bahan::findOrFail($validated['bahan_id']) : null;

        // Calculate subtotal: berat × harga_layanan + (biaya_bahan × berat)
        $hargaPerBerat = (float) $layanan->harga;
        $biayaBahan    = $bahan ? (float) $bahan->biaya_per_kg * (float) $validated['berat'] : 0;
        $subTotal      = ((float) $validated['berat'] * $hargaPerBerat) + $biayaBahan;

        // Calculate X4: machine capacity utilisation %
        $kapasitasMesin = $this->calculateMachineCapacity();

        // ── DB Transaction ──────────────────────────────────────────────
        $pesanan = DB::transaction(function () use ($validated, $layanan, $subTotal, $hargaPerBerat, $kapasitasMesin) {
            // Generate kode_pesanan: ORD-YYYYMMDD-XXXX
            $kodePesanan = $this->generateKodePesanan();

            // Create pesanan header (estimasi_selesai will be updated after prediction)
            $pesanan = Pesanan::create([
                'kode_pesanan'    => $kodePesanan,
                'user_id'         => Auth::id(),
                'tanggal_masuk'   => $validated['tanggal_masuk'],
                'total_biaya'     => $subTotal,
                'status'          => 'proses',
                'estimasi_selesai' => null, // Will be set after GM(1,4) prediction
            ]);

            // Create detail transaksi line item
            DetailTransaksi::create([
                'pesanan_id'      => $pesanan->id,
                'pelanggan_id'    => $validated['pelanggan_id'],
                'layanan_id'      => $validated['layanan_id'],
                'bahan_id'        => $validated['bahan_id'] ?: null,
                'berat'           => $validated['berat'],
                'harga_per_berat' => $hargaPerBerat,
                'sub_total'       => $subTotal,
                'kapasitas_mesin' => $kapasitasMesin,
            ]);

            return $pesanan;
        });

        // ── GM(1,4) Prediction (after commit) ───────────────────────────
        $beratInput      = (float) $validated['berat'];
        $complexityInput = (int) $layanan->complexity_score;
        $kapasitasInput  = $kapasitasMesin;

        $prediksiJam = GmPredictionService::predict([
            'berat'            => $beratInput,
            'complexity_score' => $complexityInput,
            'kapasitas_mesin'  => $kapasitasInput,
            'jenis_layanan'    => $layanan->jenis_layanan,
        ]);

        // Calculate estimasi_selesai = tanggal_masuk + prediksi_jam hours
        $waktuDasar      = Carbon::parse($validated['tanggal_masuk'])->setTimeFrom(Carbon::now());
        $estimasiSelesai = $waktuDasar->copy()->addSeconds((int) round($prediksiJam * 3600));

        // Update pesanan with the predicted completion datetime
        $pesanan->update([
            'estimasi_selesai' => $estimasiSelesai,
        ]);

        // Store prediction audit log
        PrediksiLog::create([
            'pesanan_id'       => $pesanan->id,
            'berat_input'      => $beratInput,
            'complexity_input' => $complexityInput,
            'kapasitas_input'  => $kapasitasInput,
            'prediksi_jam'     => $prediksiJam,
        ]);

        return redirect()->route('admin.pesanan.index')
                         ->with('success', 'Pesanan berhasil dibuat. Estimasi selesai: ' . $estimasiSelesai->format('d M Y H:i'));
    }

    /**
     * Display the specified pesanan detail (nota transaksi).
     */
    public function show(Pesanan $pesanan)
    {
        $pesanan->load([
            'detailTransaksi.pelanggan',
            'detailTransaksi.layanan',
            'detailTransaksi.bahan',
            'user',
            'prediksiLogs',
        ]);

        return view('pesanan.show', compact('pesanan'));
    }

    /**
     * Show the form for editing the specified pesanan.
     * Only allowed if status is 'proses'.
     */
    public function edit(Pesanan $pesanan)
    {
        if ($pesanan->status !== 'proses') {
            return redirect()->route('admin.pesanan.index')
                             ->with('error', 'Pesanan dengan status "' . $pesanan->status . '" tidak dapat diedit.');
        }

        $pesanan->load('detailTransaksi');
        $pelanggans = Pelanggan::orderBy('nama')->get();
        $layanans   = Layanan::orderBy('jenis_layanan')->get();
        $bahans     = Bahan::orderBy('nama_bahan')->get();

        $detail = $pesanan->detailTransaksi->first();

        return view('pesanan.edit', compact('pesanan', 'detail', 'pelanggans', 'layanans', 'bahans'));
    }

    /**
     * Update the specified pesanan in storage.
     * Only allowed if status is 'proses'. Re-triggers GM(1,4) prediction.
     */
    public function update(Request $request, Pesanan $pesanan)
    {
        if ($pesanan->status !== 'proses') {
            return redirect()->route('admin.pesanan.index')
                             ->with('error', 'Pesanan dengan status "' . $pesanan->status . '" tidak dapat diedit.');
        }

        if ($request->has('berat')) {
            $request->merge(['berat' => str_replace(',', '.', $request->input('berat'))]);
        }

        $validated = $request->validate([
            'pelanggan_id'  => ['required', 'exists:pelanggans,id'],
            'layanan_id'    => ['required', 'exists:layanans,id'],
            'bahan_id'      => ['nullable', 'exists:bahans,id'],
            'berat'         => ['required', 'numeric', 'min:0.1', 'max:9999.99'],
            'tanggal_masuk' => ['required', 'date'],
        ]);

        $layanan = Layanan::findOrFail($validated['layanan_id']);
        $bahan   = $validated['bahan_id'] ? Bahan::findOrFail($validated['bahan_id']) : null;

        $hargaPerBerat = (float) $layanan->harga;
        $biayaBahan    = $bahan ? (float) $bahan->biaya_per_kg * (float) $validated['berat'] : 0;
        $subTotal      = ((float) $validated['berat'] * $hargaPerBerat) + $biayaBahan;

        $kapasitasMesin = $this->calculateMachineCapacity();

        // ── DB Transaction ──────────────────────────────────────────────
        DB::transaction(function () use ($pesanan, $validated, $subTotal, $hargaPerBerat, $kapasitasMesin) {
            $pesanan->update([
                'tanggal_masuk'    => $validated['tanggal_masuk'],
                'total_biaya'      => $subTotal,
            ]);

            // Update the first detail line item
            $detail = $pesanan->detailTransaksi()->first();
            if ($detail) {
                $detail->update([
                    'pelanggan_id'    => $validated['pelanggan_id'],
                    'layanan_id'      => $validated['layanan_id'],
                    'bahan_id'        => $validated['bahan_id'] ?: null,
                    'berat'           => $validated['berat'],
                    'harga_per_berat' => $hargaPerBerat,
                    'sub_total'       => $subTotal,
                    'kapasitas_mesin' => $kapasitasMesin,
                ]);
            }
        });

        // ── Re-trigger GM(1,4) Prediction (after commit) ────────────────
        $beratInput      = (float) $validated['berat'];
        $complexityInput = (int) $layanan->complexity_score;
        $kapasitasInput  = $kapasitasMesin;

        $prediksiJam = GmPredictionService::predict([
            'berat'            => $beratInput,
            'complexity_score' => $complexityInput,
            'kapasitas_mesin'  => $kapasitasInput,
            'jenis_layanan'    => $layanan->jenis_layanan,
        ]);

        $waktuDasar      = Carbon::parse($validated['tanggal_masuk'])->setTimeFrom(Carbon::now());
        $estimasiSelesai = $waktuDasar->copy()->addSeconds((int) round($prediksiJam * 3600));

        // Update pesanan with new prediction
        $pesanan->update([
            'estimasi_selesai' => $estimasiSelesai,
        ]);

        // Delete old prediction logs for this pesanan and create new one
        PrediksiLog::where('pesanan_id', $pesanan->id)->delete();

        PrediksiLog::create([
            'pesanan_id'       => $pesanan->id,
            'berat_input'      => $beratInput,
            'complexity_input' => $complexityInput,
            'kapasitas_input'  => $kapasitasInput,
            'prediksi_jam'     => $prediksiJam,
        ]);

        return redirect()->route('admin.pesanan.index')
                         ->with('success', 'Pesanan berhasil diperbarui. Estimasi selesai: ' . $estimasiSelesai->format('d M Y H:i'));
    }

    /**
     * Remove the specified pesanan from storage.
     * Only allowed if status is 'proses'.
     */
    public function destroy(Pesanan $pesanan)
    {
        if ($pesanan->status !== 'proses') {
            return redirect()->route('admin.pesanan.index')
                             ->with('error', 'Pesanan dengan status "' . $pesanan->status . '" tidak dapat dihapus.');
        }

        $pesanan->delete();

        return redirect()->route('admin.pesanan.index')
                         ->with('success', 'Pesanan berhasil dihapus.');
    }

    /**
     * Generate unique kode_pesanan: ORD-YYYYMMDD-XXXX
     */
    private function generateKodePesanan(): string
    {
        $date   = now()->format('Ymd');
        $prefix = "ORD-{$date}-";

        // Get the latest order with today's date prefix
        $lastOrder = Pesanan::where('kode_pesanan', 'LIKE', "{$prefix}%")
                            ->orderBy('kode_pesanan', 'desc')
                            ->first();

        if ($lastOrder) {
            $lastNumber = (int) substr($lastOrder->kode_pesanan, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Calculate machine capacity utilisation percentage (X4).
     *
     * Formula: (Jumlah pesanan status 'proses' / Total kapasitas_max mesin aktif) * 100
     */
    private function calculateMachineCapacity(): float
    {
        $totalActiveOrders   = Pesanan::where('status', 'proses')->count();
        $totalMachineCapacity = Mesin::where('is_active', true)->sum('kapasitas_max');

        if ($totalMachineCapacity <= 0) {
            return 0.00;
        }

        $percentage = ($totalActiveOrders / $totalMachineCapacity) * 100;

        return round(min($percentage, 100.00), 2);
    }
}
