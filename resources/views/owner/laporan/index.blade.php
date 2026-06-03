@extends('layouts.owner')

@section('title', 'Kelola Laporan')
@section('page-title', 'Kelola Laporan')
@section('page-description', 'Lihat dan unduh laporan yang telah dibuat oleh Admin.')

@section('content')
<div class="space-y-6">

    {{-- Report Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Daftar Laporan</h3>
                    <p class="text-xs text-gray-500">Semua laporan yang tersedia untuk diunduh</p>
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
                        <th class="px-6 py-4 font-semibold text-right">MAPE</th>
                        <th class="px-6 py-4 font-semibold text-center">Dibuat Oleh</th>
                        <th class="px-6 py-4 font-semibold text-center">Unduh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse ($laporans as $index => $lap)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 text-gray-500">{{ $laporans->firstItem() + $index }}</td>
                            <td class="px-6 py-4">
                                <span class="font-semibold text-gray-800">{{ $lap->judul }}</span>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $lap->created_at->format('d M Y H:i') }}</p>
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
                            <td class="px-6 py-4 text-right">
                                @if($lap->avg_mape !== null)
                                    @php
                                        $m = (float)$lap->avg_mape;
                                        $mB = $m < 10 ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($m < 20 ? 'bg-blue-50 text-blue-700 border-blue-200' : ($m < 50 ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-red-50 text-red-700 border-red-200'));
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold border {{ $mB }}">{{ number_format($m, 2) }}%</span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center text-gray-600">
                                {{ $lap->creator?->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    {{-- Download PDF --}}
                                    <a href="{{ route('owner.laporan.download-pdf', $lap->id) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 text-rose-700 text-xs font-semibold rounded-lg hover:bg-rose-100 border border-rose-200 transition-colors"
                                       title="Unduh PDF">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        PDF
                                    </a>
                                    {{-- Download CSV --}}
                                    <a href="{{ route('owner.laporan.download-csv', $lap->id) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-700 text-xs font-semibold rounded-lg hover:bg-emerald-100 border border-emerald-200 transition-colors"
                                       title="Unduh CSV">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                        CSV
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span class="text-sm font-medium">Belum ada laporan tersedia</span>
                                <p class="text-xs text-gray-400 mt-1">Laporan akan muncul setelah Admin membuat laporan baru</p>
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
