@extends('layouts.admin')

@section('title', 'Edit Bahan')
@section('page-title', 'Edit Bahan')
@section('page-description', 'Perbarui data bahan ' . $bahan->nama_bahan)

@section('content')
    <div class="max-w-2xl">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            {{-- Form Header --}}
            <div class="px-6 py-4 bg-gradient-to-r from-amber-50 to-orange-50 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-700">Edit Data Bahan</h3>
                <p class="text-xs text-gray-500 mt-0.5">Perbarui informasi bahan yang sudah ada</p>
            </div>

            <form method="POST" action="{{ route('admin.bahan.update', $bahan) }}" class="p-6 space-y-5" id="form-edit-bahan">
                @csrf
                @method('PUT')

                {{-- Kode Bahan --}}
                <div>
                    <label for="kode_bahan" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Kode Bahan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="kode_bahan" id="kode_bahan"
                           value="{{ old('kode_bahan', $bahan->kode_bahan) }}"
                           class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 font-mono transition-colors"
                           readonly>
                    @error('kode_bahan')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nama Bahan --}}
                <div>
                    <label for="nama_bahan" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Nama Bahan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_bahan" id="nama_bahan"
                           value="{{ old('nama_bahan', $bahan->nama_bahan) }}"
                           placeholder="Contoh: Detergen, Pewangi, Pemutih"
                           class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors @error('nama_bahan') border-red-400 @enderror"
                           required>
                    @error('nama_bahan')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Biaya per Kg --}}
                <div>
                    <label for="biaya_per_kg" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Biaya per Kg <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-500 font-medium">Rp</span>
                        <input type="number" name="biaya_per_kg" id="biaya_per_kg"
                               value="{{ old('biaya_per_kg', $bahan->biaya_per_kg) }}"
                               placeholder="0"
                               step="0.01" min="0"
                               class="w-full pl-12 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors @error('biaya_per_kg') border-red-400 @enderror"
                               required>
                    </div>
                    @error('biaya_per_kg')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                    <button type="submit" id="btn-update-bahan"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 text-white text-sm font-semibold rounded-xl shadow-lg shadow-amber-500/25 hover:shadow-amber-500/40 hover:scale-[1.02] active:scale-[0.98] transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Perbarui
                    </button>
                    <a href="{{ route('admin.bahan.index') }}"
                       class="inline-flex items-center gap-2 px-6 py-2.5 bg-gray-100 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-200 transition-colors duration-200">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
