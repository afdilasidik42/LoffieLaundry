@extends('layouts.admin')

@section('title', 'Kelola Laporan')
@section('page-title', 'Kelola Laporan')
@section('page-description', 'Buat laporan baru berdasarkan rentang tanggal dan lihat riwayat laporan tersimpan.')

@section('content')
<div class="space-y-8">

    {{-- Generate Report Form --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-sky-500 to-cyan-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Buat Laporan Baru</h3>
                    <p class="text-xs text-gray-500">Pilih tipe dan rentang tanggal untuk menghasilkan laporan otomatis</p>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.laporan.generate') }}" method="POST" class="p-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
                {{-- Tipe Laporan --}}
                <div>
                    <label for="tipe" class="block text-sm font-semibold text-gray-700 mb-1.5">Tipe Laporan</label>
                    <select name="tipe" id="tipe"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm py-2.5 transition-colors">
                        <option value="harian" {{ old('tipe') === 'harian' ? 'selected' : '' }}>Harian</option>
                        <option value="mingguan" {{ old('tipe') === 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                        <option value="bulanan" {{ old('tipe') === 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                    </select>
                    @error('tipe')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tanggal Mulai --}}
                <div>
                    <label for="start_date" class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal Mulai</label>
                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm py-2.5 transition-colors">
                    @error('start_date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tanggal Selesai --}}
                <div>
                    <label for="end_date" class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal Selesai</label>
                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm py-2.5 transition-colors">
                    @error('end_date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit --}}
                <div class="flex items-end">
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 hover:from-sky-700 hover:to-cyan-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-all focus:ring-2 focus:ring-sky-500 focus:ring-offset-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Generate Laporan
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Report History Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-violet-500 to-purple-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Riwayat Laporan</h3>
                    <p class="text-xs text-gray-500">Daftar semua laporan yang telah dibuat</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 text-sm">
                        <th class="px-6 py-4 font-semibold w-16">No</th>
                        <th class="px-6 py-4 font-semibold">Judul Laporan</th>
                        <th class="px-6 py-4 font-semibold">Tipe</th>
                        <th class="px-6 py-4 font-semibold">Periode</th>
                        <th class="px-6 py-4 font-semibold text-right">Total Pesanan</th>
                        <th class="px-6 py-4 font-semibold text-right">Pendapatan</th>
                        <th class="px-6 py-4 font-semibold text-center">Dibuat Oleh</th>
                        <th class="px-6 py-4 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse ($laporans as $index => $lap)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 text-gray-500">{{ $laporans->firstItem() + $index }}</td>
                            <td class="px-6 py-4">
                                <span class="font-semibold text-gray-800">{{ $lap->judul }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $tipeBadge = match($lap->tipe) {
                                        'harian'   => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'mingguan'  => 'bg-violet-50 text-violet-700 border-violet-200',
                                        'bulanan'   => 'bg-amber-50 text-amber-700 border-amber-200',
                                        default     => 'bg-gray-50 text-gray-700 border-gray-200',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold border uppercase tracking-wider {{ $tipeBadge }}">
                                    {{ ucfirst($lap->tipe) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $lap->start_date->format('d M Y') }} — {{ $lap->end_date->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-right font-semibold text-gray-800">
                                {{ number_format($lap->total_pesanan) }}
                            </td>
                            <td class="px-6 py-4 text-right font-semibold text-gray-800">
                                Rp {{ number_format($lap->total_pendapatan, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center text-gray-600">
                                {{ $lap->creator?->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('admin.laporan.show', $lap->id) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-sky-50 text-sky-700 text-xs font-semibold rounded-lg hover:bg-sky-100 border border-sky-200 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span class="text-sm font-medium">Belum ada laporan dibuat</span>
                                <p class="text-xs text-gray-400 mt-1">Gunakan form di atas untuk membuat laporan baru</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($laporans->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $laporans->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
