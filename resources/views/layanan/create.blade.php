@extends('layouts.admin')

@section('title', 'Tambah Layanan')
@section('page-title', 'Tambah Layanan')
@section('page-description', 'Tambah jenis layanan baru ke katalog')

@section('content')
    <div class="max-w-2xl">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            {{-- Form Header --}}
            <div class="px-6 py-4 bg-gradient-to-r from-sky-50 to-cyan-50 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-700">Formulir Data Layanan</h3>
                <p class="text-xs text-gray-500 mt-0.5">Isi semua field yang bertanda <span class="text-red-500">*</span></p>
            </div>

            <form method="POST" action="{{ route('admin.layanan.store') }}" class="p-6 space-y-5" id="form-create-layanan">
                @csrf

                {{-- Kode Layanan --}}
                <div>
                    <label for="kode_layanan" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Kode Layanan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="kode_layanan" id="kode_layanan"
                           value="{{ old('kode_layanan', $kodeLayanan) }}"
                           class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 font-mono transition-colors"
                           readonly>
                    @error('kode_layanan')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Jenis Layanan --}}
                <div>
                    <label for="jenis_layanan" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Jenis Layanan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="jenis_layanan" id="jenis_layanan"
                           value="{{ old('jenis_layanan') }}"
                           placeholder="Contoh: Cuci Reguler, Express, Dry Cleaning, Setrika"
                           class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors @error('jenis_layanan') border-red-400 @enderror"
                           required>
                    @error('jenis_layanan')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Harga --}}
                <div>
                    <label for="harga" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Harga per Kg <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-500 font-medium">Rp</span>
                        <input type="number" name="harga" id="harga"
                               value="{{ old('harga') }}"
                               placeholder="0"
                               step="0.01" min="0"
                               class="w-full pl-12 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors @error('harga') border-red-400 @enderror"
                               required>
                    </div>
                    @error('harga')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Complexity Score --}}
                <div>
                    <label for="complexity_score" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Complexity Score <span class="text-red-500">*</span>
                    </label>
                    <select name="complexity_score" id="complexity_score"
                            class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors @error('complexity_score') border-red-400 @enderror"
                            required>
                        <option value="">— Pilih Tingkat Kompleksitas —</option>
                        <option value="1" {{ old('complexity_score') == 1 ? 'selected' : '' }}>1 — Rendah (misal: Setrika)</option>
                        <option value="2" {{ old('complexity_score') == 2 ? 'selected' : '' }}>2 — Sedang-Rendah</option>
                        <option value="3" {{ old('complexity_score') == 3 ? 'selected' : '' }}>3 — Sedang (misal: Cuci Reguler)</option>
                        <option value="4" {{ old('complexity_score') == 4 ? 'selected' : '' }}>4 — Sedang-Tinggi (misal: Express)</option>
                        <option value="5" {{ old('complexity_score') == 5 ? 'selected' : '' }}>5 — Tinggi (misal: Dry Cleaning)</option>
                    </select>
                    <p class="mt-1.5 text-xs text-gray-500">
                        <svg class="inline w-3.5 h-3.5 text-blue-500 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Skor ini menentukan tingkat kompleksitas layanan untuk estimasi waktu pengerjaan.
                    </p>
                    @error('complexity_score')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                    <button type="submit" id="btn-simpan-layanan"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-sky-500 to-cyan-600 text-white text-sm font-semibold rounded-xl shadow-lg shadow-sky-500/25 hover:shadow-sky-500/40 hover:scale-[1.02] active:scale-[0.98] transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan
                    </button>
                    <a href="{{ route('admin.layanan.index') }}"
                       class="inline-flex items-center gap-2 px-6 py-2.5 bg-gray-100 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-200 transition-colors duration-200">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
