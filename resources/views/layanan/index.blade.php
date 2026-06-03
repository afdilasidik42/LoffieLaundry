@extends('layouts.admin')

@section('title', 'Kelola Layanan')
@section('page-title', 'Kelola Layanan')
@section('page-description', 'Katalog layanan laundry beserta tingkat kompleksitas dan harga')

@section('content')
    {{-- Header Actions --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        {{-- Search --}}
        <form method="GET" action="{{ route('admin.layanan.index') }}" class="flex-1 max-w-md">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari jenis atau kode layanan..."
                       class="w-full pl-10 pr-4 py-2.5 text-sm bg-white border border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors" id="search-layanan">
            </div>
        </form>

        {{-- Add Button --}}
        <a href="{{ route('admin.layanan.create') }}" id="btn-tambah-layanan"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-sky-500 to-cyan-600 text-white text-sm font-semibold rounded-xl shadow-lg shadow-sky-500/25 hover:shadow-sky-500/40 hover:scale-[1.02] active:scale-[0.98] transition-all duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Layanan
        </a>
    </div>

    {{-- Data Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="table-layanan">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kode</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jenis Layanan</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Harga</th>
                        <th class="px-6 py-3.5 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Complexity Score</th>
                        <th class="px-6 py-3.5 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($layanans as $index => $layanan)
                        <tr class="hover:bg-sky-50/50 transition-colors duration-150" id="row-layanan-{{ $layanan->id }}">
                            <td class="px-6 py-4 text-gray-500">{{ $layanans->firstItem() + $index }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2.5 py-1 bg-violet-100 text-violet-700 text-xs font-mono font-semibold rounded-lg">
                                    {{ $layanan->kode_layanan }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $layanan->jenis_layanan }}</td>
                            <td class="px-6 py-4 text-right text-gray-700 font-medium">Rp {{ number_format($layanan->harga, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $scoreColors = [
                                        1 => 'bg-green-100 text-green-700',
                                        2 => 'bg-lime-100 text-lime-700',
                                        3 => 'bg-yellow-100 text-yellow-700',
                                        4 => 'bg-orange-100 text-orange-700',
                                        5 => 'bg-red-100 text-red-700',
                                    ];
                                    $scoreLabels = [1 => 'Rendah', 2 => 'Sedang-Rendah', 3 => 'Sedang', 4 => 'Sedang-Tinggi', 5 => 'Tinggi'];
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-lg {{ $scoreColors[$layanan->complexity_score] ?? 'bg-gray-100 text-gray-600' }}">
                                    <span class="flex gap-0.5">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <span class="w-1.5 h-1.5 rounded-full {{ $i <= $layanan->complexity_score ? 'bg-current' : 'bg-current opacity-20' }}"></span>
                                        @endfor
                                    </span>
                                    {{ $layanan->complexity_score }}/5
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.layanan.edit', $layanan) }}" title="Edit"
                                       class="inline-flex items-center justify-center w-8 h-8 text-amber-600 bg-amber-50 hover:bg-amber-100 rounded-lg transition-colors duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.layanan.destroy', $layanan) }}" class="inline"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus layanan {{ $layanan->jenis_layanan }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Hapus"
                                                class="inline-flex items-center justify-center w-8 h-8 text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors duration-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                    </svg>
                                    <p class="text-gray-500 text-sm">Belum ada data layanan.</p>
                                    <a href="{{ route('admin.layanan.create') }}" class="mt-2 text-sky-600 hover:text-sky-700 text-sm font-medium">+ Tambah Layanan Baru</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($layanans->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $layanans->links() }}
            </div>
        @endif
    </div>
@endsection
