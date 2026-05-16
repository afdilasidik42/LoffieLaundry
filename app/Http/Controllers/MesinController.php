<?php

namespace App\Http\Controllers;

use App\Models\Mesin;
use Illuminate\Http\Request;

class MesinController extends Controller
{
    /**
     * Display a listing of mesins with search & pagination.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        $mesins = Mesin::query()
            ->when($search, function ($query, $search) {
                $query->where('nama_mesin', 'LIKE', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('mesin.index', compact('mesins', 'search'));
    }

    /**
     * Show the form for creating a new mesin.
     */
    public function create()
    {
        return view('mesin.create');
    }

    /**
     * Store a newly created mesin in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_mesin'    => ['required', 'string', 'max:50'],
            'kapasitas_max' => ['required', 'integer', 'min:1'],
            'is_active'     => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->has('is_active');

        Mesin::create($validated);

        return redirect()->route('admin.mesin.index')
                         ->with('success', 'Data mesin berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified mesin.
     */
    public function edit(Mesin $mesin)
    {
        return view('mesin.edit', compact('mesin'));
    }

    /**
     * Update the specified mesin in storage.
     */
    public function update(Request $request, Mesin $mesin)
    {
        $validated = $request->validate([
            'nama_mesin'    => ['required', 'string', 'max:50'],
            'kapasitas_max' => ['required', 'integer', 'min:1'],
            'is_active'     => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->has('is_active');

        $mesin->update($validated);

        return redirect()->route('admin.mesin.index')
                         ->with('success', 'Data mesin berhasil diperbarui.');
    }

    /**
     * Remove the specified mesin from storage.
     */
    public function destroy(Mesin $mesin)
    {
        $mesin->delete();

        return redirect()->route('admin.mesin.index')
                         ->with('success', 'Data mesin berhasil dihapus.');
    }
}
