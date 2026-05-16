<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    /**
     * Display a listing of pelanggan with search & pagination.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        $pelanggans = Pelanggan::query()
            ->when($search, function ($query, $search) {
                $query->where('nama', 'LIKE', "%{$search}%")
                      ->orWhere('kode_pelanggan', 'LIKE', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('pelanggan.index', compact('pelanggans', 'search'));
    }

    /**
     * Show the form for creating a new pelanggan.
     */
    public function create()
    {
        // Auto-generate kode_pelanggan
        $lastPelanggan = Pelanggan::orderBy('id', 'desc')->first();
        $nextNumber = $lastPelanggan ? ((int) substr($lastPelanggan->kode_pelanggan, 4)) + 1 : 1;
        $kodePelanggan = 'PLG-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        return view('pelanggan.create', compact('kodePelanggan'));
    }

    /**
     * Store a newly created pelanggan in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_pelanggan' => ['required', 'string', 'max:20', 'unique:pelanggans,kode_pelanggan'],
            'nama'           => ['required', 'string', 'max:100'],
            'no_telepon'     => ['nullable', 'string', 'max:20'],
            'alamat'         => ['nullable', 'string'],
        ]);

        Pelanggan::create($validated);

        return redirect()->route('admin.pelanggan.index')
                         ->with('success', 'Data pelanggan berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified pelanggan.
     */
    public function edit(Pelanggan $pelanggan)
    {
        return view('pelanggan.edit', compact('pelanggan'));
    }

    /**
     * Update the specified pelanggan in storage.
     */
    public function update(Request $request, Pelanggan $pelanggan)
    {
        $validated = $request->validate([
            'kode_pelanggan' => ['required', 'string', 'max:20', 'unique:pelanggans,kode_pelanggan,' . $pelanggan->id],
            'nama'           => ['required', 'string', 'max:100'],
            'no_telepon'     => ['nullable', 'string', 'max:20'],
            'alamat'         => ['nullable', 'string'],
        ]);

        $pelanggan->update($validated);

        return redirect()->route('admin.pelanggan.index')
                         ->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    /**
     * Remove the specified pelanggan from storage.
     */
    public function destroy(Pelanggan $pelanggan)
    {
        $pelanggan->delete();

        return redirect()->route('admin.pelanggan.index')
                         ->with('success', 'Data pelanggan berhasil dihapus.');
    }
}
