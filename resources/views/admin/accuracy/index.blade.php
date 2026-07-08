@extends('layouts.admin')

@section('title', 'Uji Akurasi')
@section('page-title', 'Uji Akurasi Model GM(1,4)')
@section('page-description', 'Evaluasi akurasi prediksi dengan metrik MAPE dan MAE berdasarkan Lewis (1982).')

@section('content')
<div class="space-y-6">

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        @php
            $cards = [
                ['label' => 'Jumlah Data', 'value' => number_format($totalData), 'color' => 'from-gray-500 to-gray-600', 'note' => 'pesanan selesai'],
                ['label' => 'Rata-rata MAPE', 'value' => $avgMape !== null ? number_format($avgMape, 2) . '%' : 'N/A', 'color' => 'from-sky-500 to-cyan-600', 'note' => 'lower is better'],
                ['label' => 'Rata-rata MAE', 'value' => $avgMae !== null ? number_format($avgMae, 2) . ' jam' : 'N/A', 'color' => 'from-violet-500 to-purple-600', 'note' => 'error absolut rata-rata'],
                ['label' => 'MAPE Min / Max', 'value' => ($minMape !== null ? number_format($minMape, 2) . '%' : '-') . ' / ' . ($maxMape !== null ? number_format($maxMape, 2) . '%' : '-'), 'color' => 'from-amber-500 to-orange-600', 'note' => 'rentang error'],
                ['label' => 'Kategori Lewis', 'value' => $kategori, 'color' => 'from-emerald-500 to-green-600', 'note' => 'klasifikasi akurasi'],
            ];
        @endphp

        @foreach ($cards as $card)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br {{ $card['color'] }} rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs text-gray-500 font-medium">{{ $card['label'] }}</p>
                        <p class="text-lg font-bold text-gray-900 mt-0.5 truncate">{{ $card['value'] }}</p>
                        <p class="text-[10px] text-gray-400 mt-0.5">{{ $card['note'] }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Lewis Reference + Export --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3 text-xs">
            <span class="font-semibold text-gray-600">Referensi Lewis (1982):</span>
            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold">&lt;10% Sangat Baik</span>
            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 border border-blue-200 font-bold">10–20% Baik</span>
            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 border border-amber-200 font-bold">20–50% Layak</span>
            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-red-50 text-red-700 border border-red-200 font-bold">&gt;50% Buruk</span>
        </div>
        <a href="{{ route('admin.accuracy.export-csv') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Export CSV
        </a>
    </div>

    {{-- Scatter Plot --}}
    @if ($totalData > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-sky-500 to-cyan-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Scatter Plot: Prediksi vs Aktual</h3>
                    <p class="text-xs text-gray-500">Titik mendekati garis diagonal (y=x) menunjukkan prediksi yang akurat</p>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div class="h-80">
                <canvas id="scatterChart"></canvas>
            </div>
        </div>
    </div>
    @endif

    {{-- Detail Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col max-h-[600px] overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-violet-500 to-purple-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Detail Akurasi Per Pesanan</h3>
                    <p class="text-xs text-gray-500">Data lengkap perbandingan prediksi dengan waktu aktual</p>
                </div>
            </div>
        </div>

        <div class="overflow-auto main-scroll flex-1">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead class="sticky top-0 z-10">
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 text-sm shadow-sm">
                        <th class="px-6 py-4 font-semibold w-16">No</th>
                        <th class="px-6 py-4 font-semibold">Kode Pesanan</th>
                        <th class="px-6 py-4 font-semibold text-right">Berat (kg)</th>
                        <th class="px-6 py-4 font-semibold text-center">Complexity</th>
                        <th class="px-6 py-4 font-semibold text-right">Kapasitas (%)</th>
                        <th class="px-6 py-4 font-semibold text-right">Prediksi (jam)</th>
                        <th class="px-6 py-4 font-semibold text-right">Aktual (jam)</th>
                        <th class="px-6 py-4 font-semibold text-right">MAPE (%)</th>
                        <th class="px-6 py-4 font-semibold text-right">MAE (jam)</th>
                        <th class="px-6 py-4 font-semibold text-center">Kategori</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse ($logs as $i => $log)
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
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 text-gray-500">{{ $i + 1 }}</td>
                            <td class="px-6 py-4 font-semibold text-gray-800">{{ optional($log->pesanan)->kode_pesanan ?? '-' }}</td>
                            <td class="px-6 py-4 text-right text-gray-800">{{ number_format($log->berat_input, 2) }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gray-100 text-gray-700 text-xs font-bold">{{ $log->complexity_input }}</span>
                            </td>
                            <td class="px-6 py-4 text-right text-gray-800">{{ number_format($log->kapasitas_input, 2) }}%</td>
                            <td class="px-6 py-4 text-right font-semibold text-sky-700">{{ number_format($log->prediksi_jam, 4) }}</td>
                            <td class="px-6 py-4 text-right font-semibold text-emerald-700">{{ number_format($log->actual_jam, 4) }}</td>
                            <td class="px-6 py-4 text-right font-semibold">{{ number_format($log->mape, 4) }}%</td>
                            <td class="px-6 py-4 text-right text-gray-800">{{ number_format($log->mae, 4) }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold border {{ $badge }}">{{ $label }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                <span class="text-sm font-medium">Belum ada data akurasi</span>
                                <p class="text-xs text-gray-400 mt-1">Data muncul setelah pesanan ditandai selesai</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
@if ($totalData > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const scatterData = @json($scatterData);

    // Find max value for reference line
    const allVals = scatterData.flatMap(d => [d.x, d.y]);
    const maxVal = Math.ceil(Math.max(...allVals) * 1.1);

    const ctx = document.getElementById('scatterChart').getContext('2d');
    new Chart(ctx, {
        type: 'scatter',
        data: {
            datasets: [
                {
                    label: 'Prediksi vs Aktual',
                    data: scatterData,
                    backgroundColor: 'rgba(14, 165, 233, 0.6)',
                    borderColor: 'rgba(14, 165, 233, 1)',
                    borderWidth: 1.5,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                },
                {
                    label: 'Garis Ideal (y = x)',
                    data: [{ x: 0, y: 0 }, { x: maxVal, y: maxVal }],
                    type: 'line',
                    borderColor: 'rgba(239, 68, 68, 0.5)',
                    borderDash: [6, 4],
                    borderWidth: 2,
                    pointRadius: 0,
                    fill: false,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            if (ctx.datasetIndex === 0) {
                                const d = ctx.raw;
                                return `${d.label}: Prediksi ${d.x}jam, Aktual ${d.y}jam`;
                            }
                            return null;
                        }
                    }
                },
                legend: { position: 'top' }
            },
            scales: {
                x: {
                    title: { display: true, text: 'Prediksi (jam)', font: { weight: 'bold' } },
                    min: 0,
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                y: {
                    title: { display: true, text: 'Aktual (jam)', font: { weight: 'bold' } },
                    min: 0,
                    grid: { color: 'rgba(0,0,0,0.05)' }
                }
            }
        }
    });
});
</script>
@endif
@endpush
