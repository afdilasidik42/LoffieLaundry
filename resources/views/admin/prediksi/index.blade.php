@extends('layouts.admin')

@section('title', 'Riwayat Prediksi')
@section('page-title', 'Riwayat Prediksi GM(1,4)')
@section('page-description', 'Daftar semua prediksi waktu selesai beserta akurasi MAPE dan MAE per pesanan.')

@section('content')
<div class="space-y-6">

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-violet-500 to-purple-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Log Prediksi</h3>
                    <p class="text-xs text-gray-500">Riwayat input, output GM(1,4), dan metrik akurasi setiap pesanan</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 text-sm">
                        <th class="px-6 py-4 font-semibold w-16">No</th>
                        <th class="px-6 py-4 font-semibold">Kode Pesanan</th>
                        <th class="px-6 py-4 font-semibold">Layanan</th>
                        <th class="px-6 py-4 font-semibold text-right">Berat (kg)</th>
                        <th class="px-6 py-4 font-semibold text-center">Complexity</th>
                        <th class="px-6 py-4 font-semibold text-right">Kapasitas (%)</th>
                        <th class="px-6 py-4 font-semibold text-right">Prediksi (jam)</th>
                        <th class="px-6 py-4 font-semibold text-right">Aktual (jam)</th>
                        <th class="px-6 py-4 font-semibold text-right">MAPE (%)</th>
                        <th class="px-6 py-4 font-semibold text-center">Kategori</th>
                        <th class="px-6 py-4 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse ($logs as $index => $log)
                        @php
                            $detail  = optional($log->pesanan)->detailTransaksi?->first();
                            $layanan = $detail?->layanan;
                            $status  = optional($log->pesanan)->status;
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 text-gray-500">{{ $logs->firstItem() + $index }}</td>
                            <td class="px-6 py-4 font-semibold text-gray-800">
                                {{ optional($log->pesanan)->kode_pesanan ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ $layanan?->jenis_layanan ?? '-' }}</td>
                            <td class="px-6 py-4 text-right text-gray-800">{{ number_format($log->berat_input, 2) }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gray-100 text-gray-700 text-xs font-bold">
                                    {{ $log->complexity_input }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-gray-800">{{ number_format($log->kapasitas_input, 2) }}%</td>
                            <td class="px-6 py-4 text-right font-semibold text-sky-700">{{ number_format($log->prediksi_jam, 2) }}</td>
                            <td class="px-6 py-4 text-right font-semibold {{ $log->actual_jam !== null ? 'text-emerald-700' : 'text-gray-400' }}">
                                {{ $log->actual_jam !== null ? number_format($log->actual_jam, 2) : '—' }}
                            </td>
                            <td class="px-6 py-4 text-right font-semibold">
                                @if ($log->mape !== null)
                                    {{ number_format($log->mape, 2) }}%
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if ($log->mape !== null)
                                    @php
                                        $mapeVal = (float) $log->mape;
                                        $badge = match(true) {
                                            $mapeVal < 10 => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            $mapeVal < 20 => 'bg-blue-50 text-blue-700 border-blue-200',
                                            $mapeVal < 50 => 'bg-amber-50 text-amber-700 border-amber-200',
                                            default       => 'bg-red-50 text-red-700 border-red-200',
                                        };
                                        $label = match(true) {
                                            $mapeVal < 10 => 'Sangat Baik',
                                            $mapeVal < 20 => 'Baik',
                                            $mapeVal < 50 => 'Layak',
                                            default       => 'Buruk',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold border {{ $badge }}">
                                        {{ $label }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">Belum selesai</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if ($status === 'proses')
                                    <form action="{{ route('admin.prediksi.rerun', $log->pesanan_id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-violet-50 text-violet-700 text-xs font-semibold rounded-lg hover:bg-violet-100 border border-violet-200 transition-colors"
                                                title="Re-run prediksi GM(1,4)">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            </svg>
                                            Re-run
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="px-6 py-12 text-center text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-sm font-medium">Belum ada data prediksi</span>
                                <p class="text-xs text-gray-400 mt-1">Prediksi akan muncul setelah pesanan dibuat</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($logs->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
