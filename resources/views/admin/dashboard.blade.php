@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-description', 'Ringkasan informasi sistem Loffie Laundry')

@section('content')
    {{-- Welcome Card --}}
    <div class="bg-gradient-to-r from-sky-500 to-cyan-600 rounded-2xl shadow-lg shadow-sky-500/20 p-8 mb-8 text-white">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-bold">Selamat Datang, {{ Auth::user()->name }}!</h2>
                <p class="text-sky-100 text-sm mt-1">Panel admin Loffie Laundry siap digunakan. Kelola data pelanggan, layanan, dan bahan dari sidebar navigasi.</p>
            </div>
        </div>
    </div>

    {{-- Stat Cards --}}
    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Statistik Hari Ini</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @foreach ($stats as $stat)
            @php
                $colorMap = [
                    'sky'     => ['bg' => 'bg-sky-100',     'text' => 'text-sky-600',     'ring' => 'ring-sky-500/20'],
                    'amber'   => ['bg' => 'bg-amber-100',   'text' => 'text-amber-600',   'ring' => 'ring-amber-500/20'],
                    'violet'  => ['bg' => 'bg-violet-100',  'text' => 'text-violet-600',  'ring' => 'ring-violet-500/20'],
                    'emerald' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'ring' => 'ring-emerald-500/20'],
                ];
                $c = $colorMap[$stat['color']] ?? $colorMap['sky'];
            @endphp
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-300">
                <div class="flex items-start justify-between">
                    <div class="w-12 h-12 {{ $c['bg'] }} rounded-xl flex items-center justify-center flex-shrink-0">
                        @if ($stat['icon'] === 'users')
                            <svg class="w-6 h-6 {{ $c['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        @elseif ($stat['icon'] === 'clipboard')
                            <svg class="w-6 h-6 {{ $c['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                        @elseif ($stat['icon'] === 'refresh')
                            <svg class="w-6 h-6 {{ $c['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        @elseif ($stat['icon'] === 'check-circle')
                            <svg class="w-6 h-6 {{ $c['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        @endif
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-2xl font-bold text-gray-900">{{ $stat['value'] }}</p>
                    <p class="text-xs text-gray-500 font-medium mt-1">{{ $stat['label'] }}</p>
                    @if ($stat['note'])
                        <p class="text-[10px] text-amber-600 font-medium mt-1.5 italic">{{ $stat['note'] }}</p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- Quick Access Cards --}}
    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Akses Cepat — Master Data</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Pelanggan Card --}}
        <a href="{{ route('admin.pelanggan.index') }}" class="group bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-md hover:border-sky-300 transition-all duration-300">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-sky-100 group-hover:bg-sky-200 rounded-xl flex items-center justify-center transition-colors duration-300">
                    <svg class="w-6 h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <h4 class="text-base font-semibold text-gray-900 group-hover:text-sky-700 transition-colors">Kelola Pelanggan</h4>
                    <p class="text-xs text-gray-500 mt-0.5">Tambah, edit, hapus data pelanggan</p>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sky-600 text-xs font-medium">
                Buka Modul
                <svg class="w-3.5 h-3.5 ml-1 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
        </a>

        {{-- Layanan Card --}}
        <a href="{{ route('admin.layanan.index') }}" class="group bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-md hover:border-violet-300 transition-all duration-300">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-violet-100 group-hover:bg-violet-200 rounded-xl flex items-center justify-center transition-colors duration-300">
                    <svg class="w-6 h-6 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                    </svg>
                </div>
                <div>
                    <h4 class="text-base font-semibold text-gray-900 group-hover:text-violet-700 transition-colors">Kelola Layanan</h4>
                    <p class="text-xs text-gray-500 mt-0.5">Katalog layanan & complexity score</p>
                </div>
            </div>
            <div class="mt-4 flex items-center text-violet-600 text-xs font-medium">
                Buka Modul
                <svg class="w-3.5 h-3.5 ml-1 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
        </a>

        {{-- Bahan Card --}}
        <a href="{{ route('admin.bahan.index') }}" class="group bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-md hover:border-teal-300 transition-all duration-300">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-teal-100 group-hover:bg-teal-200 rounded-xl flex items-center justify-center transition-colors duration-300">
                    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div>
                    <h4 class="text-base font-semibold text-gray-900 group-hover:text-teal-700 transition-colors">Kelola Bahan</h4>
                    <p class="text-xs text-gray-500 mt-0.5">Material & biaya per kilogram</p>
                </div>
            </div>
            <div class="mt-4 flex items-center text-teal-600 text-xs font-medium">
                Buka Modul
                <svg class="w-3.5 h-3.5 ml-1 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
        </a>
    </div>

    {{-- Info Note --}}
    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-xl px-5 py-4 flex items-start gap-3">
        <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <p class="text-sm font-medium text-blue-800">Catatan Pengembangan</p>
            <p class="text-xs text-blue-700 mt-1">Fitur Kelola Pesanan, Prediksi Waktu GM(1,4), dan Dashboard Statistik lengkap akan tersedia pada Sprint berikutnya.</p>
        </div>
    </div>
@endsection
