@extends('layouts.admin')

@section('title', 'Kelola Status Pesanan')
@section('page-title', 'Kelola Status Pesanan')
@section('page-description', 'Papan Pembaruan Status — Kelola status pesanan laundry secara real-time.')

@section('content')
@php
    $currentFilter = request('status', 'semua');
    $filteredPesanans = $currentFilter === 'semua' ? $pesanans : $pesanans->where('status', $currentFilter);
    
    // Hitung jumlah untuk badge filter
    $allCount = $pesanans->count();
    $prosesCount = $pesanans->where('status', 'proses')->count();
    $selesaiCount = $pesanans->where('status', 'selesai')->count();
    $diambilCount = $pesanans->where('status', 'diambil')->count();
@endphp

<div class="space-y-6">

    {{-- Filter Buttons --}}
    <div class="flex flex-wrap items-center gap-3">
        <a href="{{ route('admin.status.index', ['status' => 'semua']) }}" 
           class="px-4 py-2 rounded-lg text-sm font-medium transition-colors border {{ $currentFilter === 'semua' ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
            Semua <span class="ml-1.5 px-2 py-0.5 rounded-full text-xs {{ $currentFilter === 'semua' ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-600' }}">{{ $allCount }}</span>
        </a>
        <a href="{{ route('admin.status.index', ['status' => 'proses']) }}" 
           class="px-4 py-2 rounded-lg text-sm font-medium transition-colors border {{ $currentFilter === 'proses' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
            Dalam Proses <span class="ml-1.5 px-2 py-0.5 rounded-full text-xs {{ $currentFilter === 'proses' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-600' }}">{{ $prosesCount }}</span>
        </a>
        <a href="{{ route('admin.status.index', ['status' => 'selesai']) }}" 
           class="px-4 py-2 rounded-lg text-sm font-medium transition-colors border {{ $currentFilter === 'selesai' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
            Selesai <span class="ml-1.5 px-2 py-0.5 rounded-full text-xs {{ $currentFilter === 'selesai' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">{{ $selesaiCount }}</span>
        </a>
        <a href="{{ route('admin.status.index', ['status' => 'diambil']) }}" 
           class="px-4 py-2 rounded-lg text-sm font-medium transition-colors border {{ $currentFilter === 'diambil' ? 'bg-sky-50 text-sky-700 border-sky-200' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
            Sudah Diambil <span class="ml-1.5 px-2 py-0.5 rounded-full text-xs {{ $currentFilter === 'diambil' ? 'bg-sky-100 text-sky-700' : 'bg-gray-100 text-gray-600' }}">{{ $diambilCount }}</span>
        </a>
    </div>

    {{-- Data Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 text-sm">
                        <th class="px-6 py-4 font-semibold w-16">No</th>
                        <th class="px-6 py-4 font-semibold">Kode Pesanan</th>
                        <th class="px-6 py-4 font-semibold">Pelanggan</th>
                        <th class="px-6 py-4 font-semibold">Jenis Layanan</th>
                        <th class="px-6 py-4 font-semibold">Berat (kg)</th>
                        <th class="px-6 py-4 font-semibold">Estimasi Selesai</th>
                        <th class="px-6 py-4 font-semibold text-center">Status</th>
                        <th class="px-6 py-4 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse ($filteredPesanans as $index => $p)
                        @php 
                            $detail = $p->detailTransaksi->first(); 
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 text-gray-500">
                                {{ $loop->iteration }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-gray-800">{{ $p->kode_pesanan }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-medium text-gray-700">{{ $detail?->pelanggan?->nama ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $detail?->layanan?->jenis_layanan ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $detail?->berat ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $p->estimasi_selesai ? $p->estimasi_selesai->format('d M Y, H:i') : '-' }}
                            </td>
                            
                            {{-- Badge Status --}}
                            <td class="px-6 py-4 text-center">
                                @if($p->status === 'proses')
                                    <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200 uppercase tracking-wider">
                                        Proses
                                    </span>
                                @elseif($p->status === 'selesai')
                                    <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase tracking-wider">
                                        Selesai
                                    </span>
                                @elseif($p->status === 'diambil')
                                    <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-bold bg-sky-50 text-sky-700 border border-sky-200 uppercase tracking-wider">
                                        Diambil
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-bold bg-gray-50 text-gray-700 border border-gray-200 uppercase tracking-wider">
                                        {{ ucfirst($p->status) }}
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-6 py-4">
                                <div class="flex justify-center">
                                    @if($p->status === 'proses')
                                        <form action="{{ route('admin.status.update', $p->id) }}" method="POST" class="m-0">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="selesai">
                                            <button type="submit" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white text-[13px] font-semibold rounded-lg shadow-sm transition-all focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 whitespace-nowrap w-full min-w-[140px]">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                Tandai Selesai
                                            </button>
                                        </form>
                                    @elseif($p->status === 'selesai')
                                        <form action="{{ route('admin.status.update', $p->id) }}" method="POST" class="m-0">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="diambil">
                                            <button type="submit" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-[13px] font-semibold rounded-lg shadow-sm transition-all focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 whitespace-nowrap w-full min-w-[140px]">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                Tandai Diambil
                                            </button>
                                        </form>
                                    @elseif($p->status === 'diambil')
                                        <div class="inline-flex items-center justify-center gap-1.5 text-gray-400 text-[13px] font-semibold px-4 py-2 w-full min-w-[140px]">
                                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Done
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                <span class="text-sm font-medium">Tidak ada data pesanan</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

