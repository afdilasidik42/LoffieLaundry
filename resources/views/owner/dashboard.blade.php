@extends('layouts.owner')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Owner')
@section('page-description', 'Ringkasan bisnis dan performa prediksi Loffie Laundry')

@section('content')
    {{-- Welcome Card --}}
    <div class="bg-gradient-to-r from-emerald-500 to-teal-600 rounded-2xl shadow-lg shadow-emerald-500/20 p-8 mb-8 text-white">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-bold">Selamat Datang, {{ Auth::user()->name }}!</h2>
                <p class="text-emerald-100 text-sm mt-1">Pantau performa bisnis, laporan pendapatan, dan akurasi prediksi dari sini.</p>
            </div>
        </div>
    </div>

    {{-- Overview Cards --}}
    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Ringkasan Bisnis</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        @foreach ($overview as $card)
            @php
                $colorMap = [
                    'emerald' => [
                        'bg'   => 'bg-emerald-100',
                        'text' => 'text-emerald-600',
                        'glow' => 'shadow-emerald-500/10',
                    ],
                    'sky' => [
                        'bg'   => 'bg-sky-100',
                        'text' => 'text-sky-600',
                        'glow' => 'shadow-sky-500/10',
                    ],
                    'violet' => [
                        'bg'   => 'bg-violet-100',
                        'text' => 'text-violet-600',
                        'glow' => 'shadow-violet-500/10',
                    ],
                ];
                $c = $colorMap[$card['color']] ?? $colorMap['sky'];
            @endphp
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-300 {{ $c['glow'] }}">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 {{ $c['bg'] }} rounded-xl flex items-center justify-center flex-shrink-0">
                        @if ($card['icon'] === 'currency')
                            <svg class="w-6 h-6 {{ $c['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        @elseif ($card['icon'] === 'receipt')
                            <svg class="w-6 h-6 {{ $c['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                        @elseif ($card['icon'] === 'chart')
                            <svg class="w-6 h-6 {{ $c['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        @endif
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">{{ $card['label'] }}</p>
                        <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ $card['value'] }}</p>
                        @if ($card['note'])
                            <p class="text-[10px] text-gray-400 mt-1 italic">{{ $card['note'] }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ============================================================ --}}
    {{-- VISUALISASI DATA — Chart.js Interaktif                       --}}
    {{-- ============================================================ --}}
    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Visualisasi Data</h3>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

        {{-- ── Chart 1: Akurasi Prediksi — Line Chart ──────── --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h4 class="text-sm font-semibold text-gray-900">Akurasi Prediksi</h4>
                    <p class="text-xs text-gray-500 mt-0.5">Prediksi vs Aktual waktu selesai (jam)</p>
                </div>
                <span class="text-[10px] bg-violet-100 text-violet-600 px-2.5 py-1 rounded-full font-semibold">Line Chart</span>
            </div>

            {{-- Canvas container --}}
            <div id="prediksiChartWrapper" class="relative rounded-xl" style="height: 260px;">
                {{-- Loading state --}}
                <div id="prediksiLoading" class="absolute inset-0 bg-gray-50 rounded-xl border border-dashed border-gray-300 flex items-center justify-center z-10">
                    <div class="text-center">
                        <svg class="w-8 h-8 text-gray-300 mx-auto mb-2 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        <p class="text-xs text-gray-400 font-medium">Memuat data prediksi…</p>
                    </div>
                </div>
                {{-- Empty state (hidden by default) --}}
                <div id="prediksiEmpty" class="absolute inset-0 bg-gray-50 rounded-xl border border-dashed border-gray-300 flex items-center justify-center z-10 hidden">
                    <div class="text-center">
                        <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                        <p class="text-xs text-gray-400 font-medium">Belum ada data prediksi selesai</p>
                        <p class="text-[10px] text-gray-300 mt-1">Selesaikan pesanan untuk melihat grafik</p>
                    </div>
                </div>
                <canvas id="prediksiChart"></canvas>
            </div>

            {{-- MAPE Summary Stats --}}
            <div id="mapeSummary" class="mt-4 hidden">
                <div class="flex items-center gap-3 px-4 py-3 rounded-xl border" id="mapeSummaryBox">
                    <div class="flex-shrink-0">
                        <div id="mapeIcon" class="w-10 h-10 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">Rata-rata MAPE: <span id="mapeValue" class="font-bold"></span></p>
                        <p class="text-xs mt-0.5" id="mapeCategory"></p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Chart 2: Volume Transaksi Bulanan — Bar + Line Chart ── --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h4 class="text-sm font-semibold text-gray-900">Volume Transaksi & Pendapatan</h4>
                    <p class="text-xs text-gray-500 mt-0.5">Tren pesanan dan revenue bulanan</p>
                </div>
                {{-- Filter Dropdown --}}
                <select id="volumeRangeFilter"
                        class="text-xs bg-gray-100 border border-gray-200 text-gray-700 rounded-lg px-3 py-1.5 font-medium focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors cursor-pointer">
                    <option value="tahun" selected>Tahun Ini</option>
                    <option value="6bulan">6 Bulan Terakhir</option>
                </select>
            </div>

            {{-- Canvas container --}}
            <div id="volumeChartWrapper" class="relative rounded-xl" style="height: 260px;">
                {{-- Loading state --}}
                <div id="volumeLoading" class="absolute inset-0 bg-gray-50 rounded-xl border border-dashed border-gray-300 flex items-center justify-center z-10">
                    <div class="text-center">
                        <svg class="w-8 h-8 text-gray-300 mx-auto mb-2 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        <p class="text-xs text-gray-400 font-medium">Memuat data volume…</p>
                    </div>
                </div>
                {{-- Empty state (hidden by default) --}}
                <div id="volumeEmpty" class="absolute inset-0 bg-gray-50 rounded-xl border border-dashed border-gray-300 flex items-center justify-center z-10 hidden">
                    <div class="text-center">
                        <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <p class="text-xs text-gray-400 font-medium">Belum ada data transaksi</p>
                        <p class="text-[10px] text-gray-300 mt-1">Data akan muncul setelah pesanan tercatat</p>
                    </div>
                </div>
                <canvas id="volumeChart"></canvas>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- CHART 3 — Tren Pelanggan & Distribusi Layanan                --}}
    {{-- ============================================================ --}}
    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Analitik Pelanggan</h3>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

        {{-- ── Chart 3a: Top 10 Pelanggan Terloyal — Horizontal Bar ── --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h4 class="text-sm font-semibold text-gray-900">Top 10 Pelanggan Terloyal</h4>
                    <p class="text-xs text-gray-500 mt-0.5">Berdasarkan frekuensi transaksi</p>
                </div>
                <span class="text-[10px] bg-sky-100 text-sky-600 px-2.5 py-1 rounded-full font-semibold">Bar Chart</span>
            </div>
            <div id="pelangganChartWrapper" class="relative rounded-xl" style="height: 300px;">
                <div id="pelangganLoading" class="absolute inset-0 bg-gray-50 rounded-xl border border-dashed border-gray-300 flex items-center justify-center z-10">
                    <div class="text-center">
                        <svg class="w-8 h-8 text-gray-300 mx-auto mb-2 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        <p class="text-xs text-gray-400 font-medium">Memuat data pelanggan…</p>
                    </div>
                </div>
                <div id="pelangganEmpty" class="absolute inset-0 bg-gray-50 rounded-xl border border-dashed border-gray-300 flex items-center justify-center z-10 hidden">
                    <div class="text-center">
                        <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <p class="text-xs text-gray-400 font-medium">Belum ada data pelanggan</p>
                        <p class="text-[10px] text-gray-300 mt-1">Data muncul setelah transaksi tercatat</p>
                    </div>
                </div>
                <canvas id="pelangganChart"></canvas>
            </div>
        </div>

        {{-- ── Chart 3b: Distribusi Jenis Layanan — Doughnut Chart ── --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h4 class="text-sm font-semibold text-gray-900">Distribusi Jenis Layanan</h4>
                    <p class="text-xs text-gray-500 mt-0.5">Persentase sebaran layanan yang dipilih</p>
                </div>
                <span class="text-[10px] bg-rose-100 text-rose-600 px-2.5 py-1 rounded-full font-semibold">Doughnut</span>
            </div>
            <div id="layananChartWrapper" class="relative rounded-xl" style="height: 300px;">
                <div id="layananLoading" class="absolute inset-0 bg-gray-50 rounded-xl border border-dashed border-gray-300 flex items-center justify-center z-10">
                    <div class="text-center">
                        <svg class="w-8 h-8 text-gray-300 mx-auto mb-2 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        <p class="text-xs text-gray-400 font-medium">Memuat data layanan…</p>
                    </div>
                </div>
                <div id="layananEmpty" class="absolute inset-0 bg-gray-50 rounded-xl border border-dashed border-gray-300 flex items-center justify-center z-10 hidden">
                    <div class="text-center">
                        <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <p class="text-xs text-gray-400 font-medium">Belum ada data layanan</p>
                        <p class="text-[10px] text-gray-300 mt-1">Data muncul setelah transaksi tercatat</p>
                    </div>
                </div>
                <canvas id="layananChart"></canvas>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
{{-- Chart.js CDN (v4.x) --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ================================================================
     *  HELPER: Format rupiah
     * ================================================================ */
    function formatRupiah(num) {
        return 'Rp ' + Number(num).toLocaleString('id-ID');
    }

    /* ================================================================
     *  HELPER: Klasifikasi MAPE — Lewis (1982)
     * ================================================================ */
    function klasifikasiMape(mape) {
        if (mape < 10)  return { label: 'Sangat Baik (< 10%)',  colorClass: 'text-emerald-600', bgClass: 'bg-emerald-100', borderClass: 'border-emerald-200', iconBg: 'bg-emerald-500' };
        if (mape < 20)  return { label: 'Baik (10–20%)',        colorClass: 'text-sky-600',     bgClass: 'bg-sky-100',     borderClass: 'border-sky-200',     iconBg: 'bg-sky-500' };
        if (mape < 50)  return { label: 'Layak (20–50%)',       colorClass: 'text-amber-600',   bgClass: 'bg-amber-50',    borderClass: 'border-amber-200',   iconBg: 'bg-amber-500' };
        return             { label: 'Buruk (> 50%)',       colorClass: 'text-red-600',     bgClass: 'bg-red-50',      borderClass: 'border-red-200',     iconBg: 'bg-red-500' };
    }

    /* ================================================================
     *  CHART 1 — Akurasi Prediksi — Line Chart
     * ================================================================ */
    let prediksiChartInstance = null;

    async function loadPrediksiChart() {
        const loading = document.getElementById('prediksiLoading');
        const empty   = document.getElementById('prediksiEmpty');
        const summary = document.getElementById('mapeSummary');

        try {
            const res  = await fetch('{{ route("owner.grafik.prediksi-akurasi.data") }}');
            const data = await res.json();

            loading.classList.add('hidden');

            // Jika tidak ada data
            if (!data.labels || data.labels.length === 0) {
                empty.classList.remove('hidden');
                return;
            }

            // Render chart
            const ctx = document.getElementById('prediksiChart').getContext('2d');

            if (prediksiChartInstance) prediksiChartInstance.destroy();

            prediksiChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [
                        {
                            label: 'Prediksi (jam)',
                            data: data.predicted,
                            borderColor: '#3B82F6',
                            backgroundColor: 'rgba(59, 130, 246, 0.08)',
                            borderWidth: 2.5,
                            pointBackgroundColor: '#3B82F6',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            tension: 0.3,
                            fill: true,
                        },
                        {
                            label: 'Aktual (jam)',
                            data: data.actual,
                            borderColor: '#10B981',
                            backgroundColor: 'rgba(16, 185, 129, 0.08)',
                            borderWidth: 2.5,
                            pointBackgroundColor: '#10B981',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            tension: 0.3,
                            fill: true,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle',
                                padding: 16,
                                font: { size: 11, family: 'Inter', weight: '500' }
                            }
                        },
                        tooltip: {
                            backgroundColor: '#1F2937',
                            titleFont: { size: 11, family: 'Inter' },
                            bodyFont: { size: 11, family: 'Inter' },
                            padding: 10,
                            cornerRadius: 8,
                            callbacks: {
                                afterBody: function(context) {
                                    const idx = context[0].dataIndex;
                                    const mapeVal = data.mape[idx];
                                    return mapeVal !== null ? '\nMAPE: ' + mapeVal + '%' : '';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: { font: { size: 10, family: 'Inter' }, maxRotation: 45, minRotation: 0 },
                            grid: { display: false }
                        },
                        y: {
                            beginAtZero: true,
                            title: { display: true, text: 'Jam', font: { size: 11, family: 'Inter', weight: '600' } },
                            ticks: { font: { size: 10, family: 'Inter' } },
                            grid: { color: 'rgba(0,0,0,0.04)' }
                        }
                    }
                }
            });

            // Tampilkan ringkasan MAPE
            if (data.avg_mape !== null) {
                const kat = klasifikasiMape(data.avg_mape);

                document.getElementById('mapeValue').textContent = data.avg_mape + '%';
                document.getElementById('mapeCategory').textContent = 'Kategori: ' + kat.label;
                document.getElementById('mapeCategory').className = 'text-xs mt-0.5 font-medium ' + kat.colorClass;

                const box = document.getElementById('mapeSummaryBox');
                box.className = 'flex items-center gap-3 px-4 py-3 rounded-xl border ' + kat.bgClass + ' ' + kat.borderClass;

                document.getElementById('mapeIcon').className = 'w-10 h-10 rounded-lg flex items-center justify-center ' + kat.iconBg;

                summary.classList.remove('hidden');
            }

        } catch (err) {
            console.error('Gagal memuat grafik prediksi:', err);
            loading.classList.add('hidden');
            empty.classList.remove('hidden');
        }
    }

    /* ================================================================
     *  CHART 2 — Volume Transaksi & Pendapatan — Bar + Line Chart
     * ================================================================ */
    let volumeChartInstance = null;

    async function loadVolumeChart(range) {
        const loading = document.getElementById('volumeLoading');
        const empty   = document.getElementById('volumeEmpty');

        loading.classList.remove('hidden');
        empty.classList.add('hidden');

        try {
            const url  = '{{ route("owner.grafik.volume-transaksi.data") }}' + '?range=' + encodeURIComponent(range);
            const res  = await fetch(url);
            const data = await res.json();

            loading.classList.add('hidden');

            if (!data.labels || data.labels.length === 0) {
                empty.classList.remove('hidden');
                return;
            }

            const ctx = document.getElementById('volumeChart').getContext('2d');

            if (volumeChartInstance) volumeChartInstance.destroy();

            volumeChartInstance = new Chart(ctx, {
                data: {
                    labels: data.labels,
                    datasets: [
                        {
                            type: 'bar',
                            label: 'Jumlah Pesanan',
                            data: data.volume,
                            backgroundColor: 'rgba(16, 185, 129, 0.7)',
                            hoverBackgroundColor: 'rgba(16, 185, 129, 0.9)',
                            borderColor: 'rgba(16, 185, 129, 1)',
                            borderWidth: 1,
                            borderRadius: 6,
                            barPercentage: 0.6,
                            yAxisID: 'y',
                            order: 2,
                        },
                        {
                            type: 'line',
                            label: 'Pendapatan (Rp)',
                            data: data.revenue,
                            borderColor: '#6366F1',
                            backgroundColor: 'rgba(99, 102, 241, 0.1)',
                            borderWidth: 2.5,
                            pointBackgroundColor: '#6366F1',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            tension: 0.3,
                            fill: true,
                            yAxisID: 'y1',
                            order: 1,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle',
                                padding: 16,
                                font: { size: 11, family: 'Inter', weight: '500' }
                            }
                        },
                        tooltip: {
                            backgroundColor: '#1F2937',
                            titleFont: { size: 11, family: 'Inter' },
                            bodyFont: { size: 11, family: 'Inter' },
                            padding: 10,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    if (context.dataset.yAxisID === 'y1') {
                                        return context.dataset.label + ': ' + formatRupiah(context.raw);
                                    }
                                    return context.dataset.label + ': ' + context.raw + ' pesanan';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: { font: { size: 10, family: 'Inter' } },
                            grid: { display: false }
                        },
                        y: {
                            type: 'linear',
                            position: 'left',
                            beginAtZero: true,
                            title: { display: true, text: 'Pesanan', font: { size: 11, family: 'Inter', weight: '600' } },
                            ticks: {
                                font: { size: 10, family: 'Inter' },
                                stepSize: 1,
                                precision: 0,
                            },
                            grid: { color: 'rgba(0,0,0,0.04)' }
                        },
                        y1: {
                            type: 'linear',
                            position: 'right',
                            beginAtZero: true,
                            title: { display: true, text: 'Pendapatan', font: { size: 11, family: 'Inter', weight: '600' } },
                            ticks: {
                                font: { size: 10, family: 'Inter' },
                                callback: function(value) { return formatRupiah(value); }
                            },
                            grid: { drawOnChartArea: false }
                        }
                    }
                }
            });

        } catch (err) {
            console.error('Gagal memuat grafik volume:', err);
            loading.classList.add('hidden');
            empty.classList.remove('hidden');
        }
    }

    /* ================================================================
     *  CHART 3 — Tren Pelanggan & Distribusi Layanan
     * ================================================================ */
    let pelangganChartInstance = null;
    let layananChartInstance = null;

    async function loadTrenPelangganChart() {
        const pelLoading = document.getElementById('pelangganLoading');
        const pelEmpty   = document.getElementById('pelangganEmpty');
        const layLoading = document.getElementById('layananLoading');
        const layEmpty   = document.getElementById('layananEmpty');

        try {
            const res  = await fetch('{{ route("owner.grafik.tren-pelanggan.data") }}');
            const data = await res.json();

            // === Chart 3a: Top 10 Pelanggan (Horizontal Bar) ===
            pelLoading.classList.add('hidden');

            if (!data.pelanggan || !data.pelanggan.labels || data.pelanggan.labels.length === 0) {
                pelEmpty.classList.remove('hidden');
            } else {
                const ctxPel = document.getElementById('pelangganChart').getContext('2d');
                if (pelangganChartInstance) pelangganChartInstance.destroy();

                const pelColors = data.pelanggan.data.map(function(_, i) {
                    const hues = [160, 200, 260, 30, 340, 180, 220, 280, 50, 140];
                    return 'hsla(' + hues[i % hues.length] + ', 70%, 55%, 0.85)';
                });

                pelangganChartInstance = new Chart(ctxPel, {
                    type: 'bar',
                    data: {
                        labels: data.pelanggan.labels,
                        datasets: [{
                            label: 'Jumlah Transaksi',
                            data: data.pelanggan.data,
                            backgroundColor: pelColors,
                            borderColor: pelColors.map(function(c) { return c.replace('0.85', '1'); }),
                            borderWidth: 1,
                            borderRadius: 4,
                            barPercentage: 0.7,
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#1F2937',
                                titleFont: { size: 11, family: 'Inter' },
                                bodyFont: { size: 11, family: 'Inter' },
                                padding: 10,
                                cornerRadius: 8,
                                callbacks: {
                                    label: function(ctx) { return ctx.raw + ' transaksi'; }
                                }
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                title: { display: true, text: 'Frekuensi', font: { size: 11, family: 'Inter', weight: '600' } },
                                ticks: { font: { size: 10, family: 'Inter' }, stepSize: 1, precision: 0 },
                                grid: { color: 'rgba(0,0,0,0.04)' }
                            },
                            y: {
                                ticks: { font: { size: 10, family: 'Inter' } },
                                grid: { display: false }
                            }
                        }
                    }
                });
            }

            // === Chart 3b: Distribusi Layanan (Doughnut) ===
            layLoading.classList.add('hidden');

            if (!data.layanan || !data.layanan.labels || data.layanan.labels.length === 0) {
                layEmpty.classList.remove('hidden');
            } else {
                const ctxLay = document.getElementById('layananChart').getContext('2d');
                if (layananChartInstance) layananChartInstance.destroy();

                const doughnutColors = [
                    'rgba(16, 185, 129, 0.85)',
                    'rgba(59, 130, 246, 0.85)',
                    'rgba(249, 115, 22, 0.85)',
                    'rgba(139, 92, 246, 0.85)',
                    'rgba(236, 72, 153, 0.85)',
                    'rgba(234, 179, 8, 0.85)',
                    'rgba(20, 184, 166, 0.85)',
                    'rgba(99, 102, 241, 0.85)',
                ];

                layananChartInstance = new Chart(ctxLay, {
                    type: 'doughnut',
                    data: {
                        labels: data.layanan.labels,
                        datasets: [{
                            data: data.layanan.data,
                            backgroundColor: doughnutColors.slice(0, data.layanan.labels.length),
                            borderColor: '#fff',
                            borderWidth: 3,
                            hoverOffset: 8,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '55%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    padding: 16,
                                    font: { size: 11, family: 'Inter', weight: '500' }
                                }
                            },
                            tooltip: {
                                backgroundColor: '#1F2937',
                                titleFont: { size: 11, family: 'Inter' },
                                bodyFont: { size: 11, family: 'Inter' },
                                padding: 10,
                                cornerRadius: 8,
                                callbacks: {
                                    label: function(ctx) {
                                        var total = ctx.dataset.data.reduce(function(a, b) { return a + b; }, 0);
                                        var pct = total > 0 ? ((ctx.raw / total) * 100).toFixed(1) : 0;
                                        return ctx.label + ': ' + ctx.raw + ' (' + pct + '%)';
                                    }
                                }
                            }
                        }
                    }
                });
            }

        } catch (err) {
            console.error('Gagal memuat grafik tren pelanggan:', err);
            pelLoading.classList.add('hidden');
            pelEmpty.classList.remove('hidden');
            layLoading.classList.add('hidden');
            layEmpty.classList.remove('hidden');
        }
    }

    /* ================================================================
     *  INIT: Muat semua grafik saat halaman siap
     * ================================================================ */
    loadPrediksiChart();
    loadVolumeChart('tahun');
    loadTrenPelangganChart();

    /* ================================================================
     *  EVENT: Filter rentang waktu volume transaksi
     * ================================================================ */
    document.getElementById('volumeRangeFilter').addEventListener('change', function () {
        loadVolumeChart(this.value);
    });

});
</script>
@endpush
