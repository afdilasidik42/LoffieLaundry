@extends('layouts.admin')

@section('title', 'Tambah Pelanggan')
@section('page-title', 'Tambah Pelanggan')
@section('page-description', 'Tambah data pelanggan baru ke dalam sistem')

@section('content')
    <div class="max-w-2xl">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            {{-- Form Header --}}
            <div class="px-6 py-4 bg-gradient-to-r from-sky-50 to-cyan-50 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-700">Formulir Data Pelanggan</h3>
                <p class="text-xs text-gray-500 mt-0.5">Isi semua field yang bertanda <span class="text-red-500">*</span></p>
            </div>

            <form method="POST" action="{{ route('admin.pelanggan.store') }}" class="p-6 space-y-5" id="form-create-pelanggan">
                @csrf

                {{-- Kode Pelanggan --}}
                <div>
                    <label for="kode_pelanggan" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Kode Pelanggan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="kode_pelanggan" id="kode_pelanggan"
                           value="{{ old('kode_pelanggan', $kodePelanggan) }}"
                           class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 font-mono transition-colors"
                           readonly>
                    @error('kode_pelanggan')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nama --}}
                <div>
                    <label for="nama" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Nama Pelanggan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama" id="nama"
                           value="{{ old('nama') }}"
                           placeholder="Masukkan nama lengkap pelanggan"
                           class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors @error('nama') border-red-400 @enderror"
                           required>
                    @error('nama')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- No Telepon --}}
                <div>
                    <label for="no_telepon" class="block text-sm font-medium text-gray-700 mb-1.5">
                        No. Telepon
                    </label>
                    <input type="text" name="no_telepon" id="no_telepon"
                           value="{{ old('no_telepon') }}"
                           placeholder="Contoh: 08123456789"
                           class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors @error('no_telepon') border-red-400 @enderror">
                    @error('no_telepon')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Alamat --}}
                <div>
                    <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Alamat
                    </label>
                    <textarea name="alamat" id="alamat" rows="3"
                              placeholder="Masukkan alamat lengkap pelanggan"
                              class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors resize-none @error('alamat') border-red-400 @enderror">{{ old('alamat') }}</textarea>
                    @error('alamat')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                    <button type="submit" id="btn-simpan-pelanggan"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-sky-500 to-cyan-600 text-white text-sm font-semibold rounded-xl shadow-lg shadow-sky-500/25 hover:shadow-sky-500/40 hover:scale-[1.02] active:scale-[0.98] transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan
                    </button>
                    <a href="{{ route('admin.pelanggan.index') }}"
                       class="inline-flex items-center gap-2 px-6 py-2.5 bg-gray-100 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-200 transition-colors duration-200">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
