@extends('layouts.admin')

@section('title', 'Edit Mesin')
@section('page-title', 'Edit Mesin')
@section('page-description', 'Perbarui data mesin laundry')

@section('content')
    <div class="max-w-2xl">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            {{-- Form Header --}}
            <div class="px-6 py-4 bg-gradient-to-r from-amber-50 to-orange-50 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-700">Edit Data Mesin</h3>
                <p class="text-xs text-gray-500 mt-0.5">Perbarui informasi mesin <span class="font-medium">{{ $mesin->nama_mesin }}</span></p>
            </div>

            <form method="POST" action="{{ route('admin.mesin.update', $mesin) }}" class="p-6 space-y-5" id="form-edit-mesin">
                @csrf
                @method('PUT')

                {{-- Nama Mesin --}}
                <div>
                    <label for="nama_mesin" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Nama Mesin <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_mesin" id="nama_mesin"
                           value="{{ old('nama_mesin', $mesin->nama_mesin) }}"
                           class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors @error('nama_mesin') border-red-400 @enderror"
                           required>
                    @error('nama_mesin')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Kapasitas Max --}}
                <div>
                    <label for="kapasitas_max" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Kapasitas Maksimal (kg per siklus) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="kapasitas_max" id="kapasitas_max"
                           value="{{ old('kapasitas_max', $mesin->kapasitas_max) }}"
                           min="1"
                           class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors @error('kapasitas_max') border-red-400 @enderror"
                           required>
                    @error('kapasitas_max')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status Aktif --}}
                <div>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1"
                               class="w-4 h-4 text-sky-600 border-gray-300 rounded focus:ring-sky-500"
                               {{ old('is_active', $mesin->is_active) ? 'checked' : '' }}>
                        <span class="text-sm font-medium text-gray-700">Mesin Aktif</span>
                    </label>
                    <p class="mt-1 text-xs text-gray-500 ml-7">Mesin aktif akan diperhitungkan dalam kalkulasi kapasitas.</p>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                    <button type="submit" id="btn-update-mesin"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-sky-500 to-cyan-600 text-white text-sm font-semibold rounded-xl shadow-lg shadow-sky-500/25 hover:shadow-sky-500/40 hover:scale-[1.02] active:scale-[0.98] transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Perbarui
                    </button>
                    <a href="{{ route('admin.mesin.index') }}"
                       class="inline-flex items-center gap-2 px-6 py-2.5 bg-gray-100 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-200 transition-colors duration-200">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
