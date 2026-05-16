<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use Illuminate\Http\Request;

class LayananController extends Controller
{
    /**
     * Display a listing of layanan with search & pagination.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        $layanans = Layanan::query()
            ->when($search, function ($query, $search) {
                $query->where('jenis_layanan', 'LIKE', "%{$search}%")
                      ->orWhere('kode_layanan', 'LIKE', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('layanan.index', compact('layanans', 'search'));
    }

    /**
     * Show the form for creating a new layanan.
     */
    public function create()
    {
        // Auto-generate kode_layanan
        $lastLayanan = Layanan::orderBy('id', 'desc')->first();
        $nextNumber = $lastLayanan ? ((int) substr($lastLayanan->kode_layanan, 4)) + 1 : 1;
        $kodeLayanan = 'LAY-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        return view('layanan.create', compact('kodeLayanan'));
    }

    /**
     * Store a newly created layanan in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_layanan'     => ['required', 'string', 'max:20', 'unique:layanans,kode_layanan'],
            'jenis_layanan'    => ['required', 'string', 'max:100'],
            'harga'            => ['required', 'numeric', 'min:0'],
            'complexity_score' => ['required', 'integer', 'min:1', 'max:5'],
        ]);

        Layanan::create($validated);

        return redirect()->route('admin.layanan.index')
                         ->with('success', 'Data layanan berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified layanan.
     */
    public function edit(Layanan $layanan)
    {
        return view('layanan.edit', compact('layanan'));
    }

    /**
     * Update the specified layanan in storage.
     */
    public function update(Request $request, Layanan $layanan)
    {
        $validated = $request->validate([
            'kode_layanan'     => ['required', 'string', 'max:20', 'unique:layanans,kode_layanan,' . $layanan->id],
            'jenis_layanan'    => ['required', 'string', 'max:100'],
            'harga'            => ['required', 'numeric', 'min:0'],
            'complexity_score' => ['required', 'integer', 'min:1', 'max:5'],
        ]);

        $layanan->update($validated);

        return redirect()->route('admin.layanan.index')
                         ->with('success', 'Data layanan berhasil diperbarui.');
    }

    /**
     * Remove the specified layanan from storage.
     */
    public function destroy(Layanan $layanan)
    {
        $layanan->delete();

        return redirect()->route('admin.layanan.index')
                         ->with('success', 'Data layanan berhasil dihapus.');
    }
}
