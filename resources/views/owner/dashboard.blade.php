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
                <p class="text-emerald-100 text-sm mt-1">Pantau performa bisnis, laporan pendapatan, dan akurasi prediksi GM(1,4) dari sini.</p>
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

    {{-- Chart.js Placeholder Section --}}
    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Visualisasi Data</h3>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- Placeholder Chart 1: Akurasi Prediksi --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h4 class="text-sm font-semibold text-gray-900">Akurasi Prediksi GM(1,4)</h4>
                    <p class="text-xs text-gray-500 mt-0.5">Prediksi vs Aktual waktu selesai</p>
                </div>
                <span class="text-[10px] bg-violet-100 text-violet-600 px-2.5 py-1 rounded-full font-semibold">Sprint 3</span>
            </div>
            <div class="bg-gray-50 rounded-xl border border-dashed border-gray-300 flex items-center justify-center" style="height: 240px;">
                <canvas id="chartPrediksiAkurasi" class="hidden"></canvas>
                <div class="text-center">
                    <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    <p class="text-xs text-gray-400 font-medium">Chart.js akan dirender di Sprint 3</p>
                </div>
            </div>
        </div>

        {{-- Placeholder Chart 2: Volume Transaksi --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h4 class="text-sm font-semibold text-gray-900">Volume Transaksi Bulanan</h4>
                    <p class="text-xs text-gray-500 mt-0.5">Tren jumlah pesanan per bulan</p>
                </div>
                <span class="text-[10px] bg-sky-100 text-sky-600 px-2.5 py-1 rounded-full font-semibold">Sprint 3</span>
            </div>
            <div class="bg-gray-50 rounded-xl border border-dashed border-gray-300 flex items-center justify-center" style="height: 240px;">
                <canvas id="chartVolumeTransaksi" class="hidden"></canvas>
                <div class="text-center">
                    <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <p class="text-xs text-gray-400 font-medium">Chart.js akan dirender di Sprint 3</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Info Note --}}
    <div class="bg-emerald-50 border border-emerald-200 rounded-xl px-5 py-4 flex items-start gap-3">
        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <p class="text-sm font-medium text-emerald-800">Catatan Pengembangan</p>
            <p class="text-xs text-emerald-700 mt-1">Grafik interaktif Chart.js, laporan pendapatan riil, dan fitur kelola user akan tersedia setelah Sprint 2 & 3 selesai.</p>
        </div>
    </div>
@endsection
