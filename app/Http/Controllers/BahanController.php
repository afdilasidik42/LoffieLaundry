<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class BahanController extends Controller
{
    /**
     * Display a listing of bahan with search & pagination.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        $bahans = Bahan::query()
            ->when($search, function ($query, $search) {
                $query->where('nama_bahan', 'LIKE', "%{$search}%")
                      ->orWhere('kode_bahan', 'LIKE', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('bahan.index', compact('bahans', 'search'));
    }

    /**
     * Show the form for creating a new bahan.
     */
    public function create()
    {
        // Auto-generate kode_bahan
        $lastBahan = Bahan::orderBy('id', 'desc')->first();
        $nextNumber = $lastBahan ? ((int) substr($lastBahan->kode_bahan, 4)) + 1 : 1;
        $kodeBahan = 'BHN-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        return view('bahan.create', compact('kodeBahan'));
    }

    /**
     * Store a newly created bahan in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_bahan'   => ['required', 'string', 'max:20', 'unique:bahans,kode_bahan'],
            'nama_bahan'   => ['required', 'string', 'max:100'],
            'biaya_per_kg' => ['required', 'numeric', 'min:0'],
        ]);

        Bahan::create($validated);

        return redirect()->route('admin.bahan.index')
                         ->with('success', 'Data bahan berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified bahan.
     */
    public function edit(Bahan $bahan)
    {
        return view('bahan.edit', compact('bahan'));
    }

    /**
     * Update the specified bahan in storage.
     */
    public function update(Request $request, Bahan $bahan)
    {
        $validated = $request->validate([
            'kode_bahan'   => ['required', 'string', 'max:20', 'unique:bahans,kode_bahan,' . $bahan->id],
            'nama_bahan'   => ['required', 'string', 'max:100'],
            'biaya_per_kg' => ['required', 'numeric', 'min:0'],
        ]);

        $bahan->update($validated);

        return redirect()->route('admin.bahan.index')
                         ->with('success', 'Data bahan berhasil diperbarui.');
    }

    /**
     * Remove the specified bahan from storage.
     */
    public function destroy(Bahan $bahan)
    {
        // Cek apakah bahan digunakan di transaksi
        if ($bahan->detailTransaksi()->exists()) {
            return redirect()->route('admin.bahan.index')
                             ->with('error', 'Bahan "' . $bahan->nama_bahan . '" tidak dapat dihapus karena masih digunakan di data transaksi.');
        }

        try {
            $bahan->delete();

            return redirect()->route('admin.bahan.index')
                             ->with('success', 'Data bahan berhasil dihapus.');
        } catch (QueryException $e) {
            return redirect()->route('admin.bahan.index')
                             ->with('error', 'Gagal menghapus bahan. Data masih digunakan oleh transaksi lain.');
        }
    }
}
