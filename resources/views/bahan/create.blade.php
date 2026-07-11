@extends('layouts.admin')

@section('title', 'Tambah Bahan')
@section('page-title', 'Tambah Bahan')
@section('page-description', 'Tambah data bahan material baru ke dalam sistem')

@section('content')
    <div class="max-w-2xl">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            {{-- Form Header --}}
            <div class="px-6 py-4 bg-gradient-to-r from-sky-50 to-cyan-50 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-700">Formulir Data Bahan</h3>
                <p class="text-xs text-gray-500 mt-0.5">Isi semua field yang bertanda <span class="text-red-500">*</span></p>
            </div>

            <form method="POST" action="{{ route('admin.bahan.store') }}" class="p-6 space-y-5" id="form-create-bahan">
                @csrf

                {{-- Kode Bahan --}}
                <div>
                    <label for="kode_bahan" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Kode Bahan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="kode_bahan" id="kode_bahan"
                           value="{{ old('kode_bahan', $kodeBahan) }}"
                           class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-sky-500/40 focus:border-sky-500 font-mono transition-colors"
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
                           value="{{ old('nama_bahan') }}"
                           placeholder="Contoh: Detergen, Pewangi, Pemutih"
                           class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500/40 focus:border-sky-500 transition-colors @error('nama_bahan') border-red-400 @enderror"
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
                               value="{{ old('biaya_per_kg') }}"
                               placeholder="0"
                               step="0.01" min="0"
                               class="w-full pl-12 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500/40 focus:border-sky-500 transition-colors @error('biaya_per_kg') border-red-400 @enderror"
                               required>
                    </div>
                    @error('biaya_per_kg')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                    <button type="submit" id="btn-simpan-bahan"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-sky-500 to-cyan-600 text-white text-sm font-semibold rounded-xl shadow-lg shadow-sky-500/25 hover:shadow-sky-500/40 hover:scale-[1.02] active:scale-[0.98] transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan
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
