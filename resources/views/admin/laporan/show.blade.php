@extends('layouts.admin')

@section('title', 'Detail Laporan')
@section('page-title', 'Detail Laporan')
@section('page-description', $laporan->judul)

@section('content')
<div class="space-y-6">

    <div>
        <a href="{{ route('admin.laporan.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-sky-600 transition-colors font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Daftar Laporan
        </a>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Tipe</p>
            @php
                $tipeBadge = match($laporan->tipe) {
                    'harian'  => 'bg-blue-50 text-blue-700 border-blue-200',
                    'mingguan' => 'bg-violet-50 text-violet-700 border-violet-200',
                    'bulanan'  => 'bg-amber-50 text-amber-700 border-amber-200',
                    default    => 'bg-gray-50 text-gray-700 border-gray-200',
                };
            @endphp
            <span class="mt-2 inline-flex items-center px-3 py-1 rounded-md text-xs font-bold border uppercase tracking-wider {{ $tipeBadge }}">{{ ucfirst($laporan->tipe) }}</span>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Total Pesanan</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($laporan->total_pesanan) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Total Pendapatan</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">Rp {{ number_format($laporan->total_pendapatan, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Rata-rata MAPE</p>
            @if($laporan->avg_mape !== null)
                @php
                    $mapeVal = (float)$laporan->avg_mape;
                    if ($mapeVal < 10) { $mapeColor = 'text-emerald-600'; $mapeLabel = 'Sangat Baik'; }
                    elseif ($mapeVal < 20) { $mapeColor = 'text-blue-600'; $mapeLabel = 'Baik'; }
                    elseif ($mapeVal < 50) { $mapeColor = 'text-amber-600'; $mapeLabel = 'Layak'; }
                    else { $mapeColor = 'text-red-600'; $mapeLabel = 'Buruk'; }
                @endphp
                <p class="text-2xl font-bold {{ $mapeColor }} mt-1">{{ number_format($mapeVal, 2) }}%</p>
                <p class="text-xs {{ $mapeColor }} font-medium">{{ $mapeLabel }}</p>
            @else
                <p class="text-lg text-gray-400 mt-1">—</p>
            @endif
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Rata-rata MAE</p>
            @if($laporan->avg_mae !== null)
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format((float)$laporan->avg_mae, 2) }} jam</p>
            @else
                <p class="text-lg text-gray-400 mt-1">—</p>
            @endif
        </div>
    </div>

    {{-- Report Info --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex flex-wrap items-center gap-6 text-sm text-gray-600">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span><strong>Periode:</strong> {{ $laporan->start_date->format('d M Y') }} — {{ $laporan->end_date->format('d M Y') }}</span>
            </div>
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span><strong>Dibuat oleh:</strong> {{ $laporan->creator?->name ?? '-' }}</span>
            </div>
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span><strong>Dibuat pada:</strong> {{ $laporan->created_at->format('d M Y H:i') }}</span>
            </div>
        </div>
    </div>

    {{-- Transactions Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-900">Rincian Transaksi</h3>
            <p class="text-xs text-gray-500 mt-0.5">Daftar pesanan yang termasuk dalam periode laporan ini</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 text-sm">
                        <th class="px-6 py-4 font-semibold w-16">No</th>
                        <th class="px-6 py-4 font-semibold">Kode Pesanan</th>
                        <th class="px-6 py-4 font-semibold">Pelanggan</th>
                        <th class="px-6 py-4 font-semibold">Layanan</th>
                        <th class="px-6 py-4 font-semibold text-right">Berat</th>
                        <th class="px-6 py-4 font-semibold text-right">Total Biaya</th>
                        <th class="px-6 py-4 font-semibold text-right">Prediksi</th>
                        <th class="px-6 py-4 font-semibold text-right">Aktual</th>
                        <th class="px-6 py-4 font-semibold text-right">MAPE</th>
                        <th class="px-6 py-4 font-semibold text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse ($pesanans as $i => $p)
                        @php $d = $p->detailTransaksi->first(); $pr = $p->prediksiLogs->first(); @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 text-gray-500">{{ $i + 1 }}</td>
                            <td class="px-6 py-4 font-bold text-gray-800">{{ $p->kode_pesanan }}</td>
                            <td class="px-6 py-4 text-gray-700">{{ $d?->pelanggan?->nama ?? '-' }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $d?->layanan?->jenis_layanan ?? '-' }}</td>
                            <td class="px-6 py-4 text-right text-gray-600">{{ $d?->berat ?? '-' }} kg</td>
                            <td class="px-6 py-4 text-right font-semibold text-gray-800">Rp {{ number_format($p->total_biaya, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right text-gray-600">{{ $pr?->prediksi_jam ? number_format((float)$pr->prediksi_jam, 2) . ' jam' : '—' }}</td>
                            <td class="px-6 py-4 text-right text-gray-600">{{ $pr?->actual_jam ? number_format((float)$pr->actual_jam, 2) . ' jam' : '—' }}</td>
                            <td class="px-6 py-4 text-right">
                                @if($pr?->mape !== null)
                                    @php
                                        $m = (float)$pr->mape;
                                        $mB = $m < 10 ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($m < 20 ? 'bg-blue-50 text-blue-700 border-blue-200' : ($m < 50 ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-red-50 text-red-700 border-red-200'));
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold border {{ $mB }}">{{ number_format($m, 2) }}%</span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($p->status === 'proses')
                                    <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200 uppercase tracking-wider">Proses</span>
                                @elseif($p->status === 'selesai')
                                    <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase tracking-wider">Selesai</span>
                                @elseif($p->status === 'diambil')
                                    <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-bold bg-sky-50 text-sky-700 border border-sky-200 uppercase tracking-wider">Diambil</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center text-gray-400">
                                <span class="text-sm font-medium">Tidak ada transaksi dalam periode ini</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
