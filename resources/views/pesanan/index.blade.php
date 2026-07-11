@extends('layouts.admin')

@section('title', 'Kelola Pesanan')
@section('page-title', 'Kelola Pesanan')
@section('page-description', 'Daftar seluruh pesanan laundry')

@section('content')
    {{-- Header Actions --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-3 flex-1">
            {{-- Search --}}
            <form method="GET" action="{{ route('admin.pesanan.index') }}" class="flex-1 max-w-md" id="form-search-pesanan">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari kode pesanan atau nama pelanggan..."
                           class="w-full pl-10 pr-4 py-2.5 text-sm bg-white border border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500/40 focus:border-sky-500 transition-colors" id="search-pesanan">
                    @if ($status)
                        <input type="hidden" name="status" value="{{ $status }}">
                    @endif
                </div>
            </form>

            {{-- Status Filter --}}
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.pesanan.index', ['search' => $search]) }}"
                   class="px-4 py-2.5 text-xs font-semibold rounded-xl border transition-colors duration-200
                          {{ !$status ? 'bg-sky-600 text-white border-sky-600' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }}">
                    Semua
                </a>
                <a href="{{ route('admin.pesanan.index', ['status' => 'proses', 'search' => $search]) }}"
                   class="px-4 py-2.5 text-xs font-semibold rounded-xl border transition-colors duration-200
                          {{ $status === 'proses' ? 'bg-yellow-500 text-white border-yellow-500' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }}">
                    Proses
                </a>
                <a href="{{ route('admin.pesanan.index', ['status' => 'selesai', 'search' => $search]) }}"
                   class="px-4 py-2.5 text-xs font-semibold rounded-xl border transition-colors duration-200
                          {{ $status === 'selesai' ? 'bg-emerald-500 text-white border-emerald-500' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }}">
                    Selesai
                </a>
                <a href="{{ route('admin.pesanan.index', ['status' => 'diambil', 'search' => $search]) }}"
                   class="px-4 py-2.5 text-xs font-semibold rounded-xl border transition-colors duration-200
                          {{ $status === 'diambil' ? 'bg-blue-500 text-white border-blue-500' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }}">
                    Diambil
                </a>
            </div>
        </div>

        {{-- Add Button --}}
        <a href="{{ route('admin.pesanan.create') }}" id="btn-tambah-pesanan"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-sky-500 to-cyan-600 text-white text-sm font-semibold rounded-xl shadow-lg shadow-sky-500/25 hover:shadow-sky-500/40 hover:scale-[1.02] active:scale-[0.98] transition-all duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Buat Pesanan
        </a>
    </div>

    {{-- Data Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="table-pesanan">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kode Pesanan</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Pelanggan</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Layanan</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Berat</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Total Biaya</th>
                        <th class="px-6 py-3.5 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Estimasi Selesai</th>
                        <th class="px-6 py-3.5 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($pesanans as $index => $pesanan)
                        @php
                            $detail    = $pesanan->detailTransaksi->first();
                            $pelanggan = $detail?->pelanggan;
                            $layanan   = $detail?->layanan;
                        @endphp
                        <tr class="group hover:bg-sky-50/60 transition-colors duration-200" id="row-pesanan-{{ $pesanan->id }}">
                            <td class="px-6 py-4 text-gray-500 group-hover:text-sky-600 transition-colors">{{ $pesanans->firstItem() + $index }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2.5 py-1 bg-violet-100 text-violet-700 text-xs font-mono font-semibold rounded-lg">
                                    {{ $pesanan->kode_pesanan }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $pelanggan?->nama ?? '—' }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $layanan?->jenis_layanan ?? '—' }}</td>
                            <td class="px-6 py-4 text-right text-gray-700">{{ $detail?->berat ?? '—' }} kg</td>
                            <td class="px-6 py-4 text-right text-gray-700 font-medium">Rp {{ number_format($pesanan->total_biaya, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-center">
                                @if ($pesanan->status === 'proses')
                                    <span class="inline-flex px-2.5 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-lg">Proses</span>
                                @elseif ($pesanan->status === 'selesai')
                                    <span class="inline-flex px-2.5 py-1 bg-emerald-100 text-emerald-800 text-xs font-semibold rounded-lg">Selesai</span>
                                @else
                                    <span class="inline-flex px-2.5 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-lg">Diambil</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-600 text-sm">
                                {{ $pesanan->estimasi_selesai ? $pesanan->estimasi_selesai->format('d M Y H:i') : '—' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    {{-- Detail --}}
                                    <a href="{{ route('admin.pesanan.show', $pesanan) }}" title="Detail"
                                       class="inline-flex items-center justify-center w-8 h-8 text-sky-600 bg-sky-50 hover:bg-sky-100 rounded-lg transition-colors duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>

                                    @if ($pesanan->status === 'proses')
                                        {{-- Edit --}}
                                        <a href="{{ route('admin.pesanan.edit', $pesanan) }}" title="Edit"
                                           class="inline-flex items-center justify-center w-8 h-8 text-amber-600 bg-amber-50 hover:bg-amber-100 rounded-lg transition-colors duration-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>

                                        {{-- Delete --}}
                                        <form method="POST" action="{{ route('admin.pesanan.destroy', $pesanan) }}" class="inline"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesanan {{ $pesanan->kode_pesanan }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus"
                                                    class="inline-flex items-center justify-center w-8 h-8 text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors duration-200">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-sky-50 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                        </svg>
                                    </div>
                                    <p class="text-gray-900 font-semibold text-base">Belum ada pesanan</p>
                                    <p class="text-gray-500 text-sm mt-1 max-w-sm">Mulai kelola laundry Anda dengan membuat pesanan baru sekarang.</p>
                                    <a href="{{ route('admin.pesanan.create') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-sky-600 transition-colors shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        Buat Pesanan Baru
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($pesanans->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $pesanans->links() }}
            </div>
        @endif
    </div>
@endsection
