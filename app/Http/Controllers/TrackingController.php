<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TrackingController extends Controller
{
    /**
     * Tampilkan form pelacakan pesanan.
     */
    public function index()
    {
        return view('tracking.index');
    }

    /**
     * Cari pesanan berdasarkan ID.
     *
     * Karena tabel `pesanans` baru akan dibuat di Sprint 2,
     * method ini menggunakan data simulasi (mocking).
     */
    public function search(Request $request)
    {
        $request->validate([
            'id_pesanan' => 'required|string|max:50',
        ]);

        $idPesanan = strtoupper(trim($request->id_pesanan));

        // --- Mock Logic (akan diganti query Eloquent di Sprint 2) ---
        if ($idPesanan === 'ORD-MOCK') {
            $pesanan = (object) [
                'kode_pesanan'    => 'ORD-MOCK',
                'nama_pelanggan'  => 'Budi',
                'jenis_layanan'   => 'Express',
                'berat'           => '5 kg',
                'status'          => 'Proses',
                'estimasi_selesai' => '6 Jam',
                'tanggal_masuk'   => now()->format('d M Y, H:i'),
            ];

            return view('tracking.result', compact('pesanan'));
        }

        return back()
            ->withInput()
            ->withErrors(['id_pesanan' => 'ID Pesanan Tidak Ditemukan']);
    }
}
