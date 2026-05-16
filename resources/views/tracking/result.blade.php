<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Lacak — {{ $pesanan->kode_pesanan }} — Loffie Laundry</title>
    <meta name="description" content="Status pesanan {{ $pesanan->kode_pesanan }} di Loffie Laundry.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { font-family: 'Inter', sans-serif; }
        @keyframes fade-in-up {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up { animation: fade-in-up 0.5s ease-out; }
        @keyframes progress-pulse {
            0%, 100% { opacity: 1; }
            50%      { opacity: 0.5; }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-sky-50 via-white to-cyan-50 min-h-screen">

    {{-- Navigation Bar --}}
    <nav class="bg-white/80 backdrop-blur-md border-b border-gray-200/60 sticky top-0 z-50">
        <div class="max-w-5xl mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route('welcome') }}" class="flex items-center gap-3 group">
                <div class="w-9 h-9 bg-gradient-to-br from-sky-500 to-cyan-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                    </svg>
                </div>
                <span class="text-sm font-bold text-gray-900 group-hover:text-sky-600 transition-colors">Loffie Laundry</span>
            </a>
            <a href="{{ route('tracking.index') }}" class="text-sm text-gray-500 hover:text-gray-700 transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Lacak Lagi
            </a>
        </div>
    </nav>

    {{-- Main Content --}}
    <main class="max-w-2xl mx-auto px-6 py-16 animate-fade-in-up">
        {{-- Header --}}
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-emerald-100 rounded-2xl mb-5">
                <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Pesanan Ditemukan</h1>
            <p class="text-sm text-gray-500 mt-2">Berikut adalah detail pesanan Anda.</p>
        </div>

        {{-- Result Card --}}
        <div class="bg-white rounded-2xl shadow-lg shadow-gray-200/50 border border-gray-200/80 overflow-hidden">
            {{-- Card Header --}}
            <div class="bg-gradient-to-r from-sky-500 to-cyan-600 px-8 py-5 flex items-center justify-between">
                <div>
                    <p class="text-sky-100 text-xs font-medium uppercase tracking-wider">Kode Pesanan</p>
                    <p class="text-white text-lg font-bold mt-0.5">{{ $pesanan->kode_pesanan }}</p>
                </div>
                <div class="flex items-center gap-2">
                    @php
                        $statusMap = [
                            'Proses'  => 'bg-yellow-400 text-yellow-900',
                            'Selesai' => 'bg-emerald-400 text-emerald-900',
                            'Diambil' => 'bg-sky-400 text-sky-900',
                        ];
                        $statusClass = $statusMap[$pesanan->status] ?? 'bg-gray-400 text-gray-900';
                    @endphp
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-full {{ $statusClass }}">
                        @if($pesanan->status === 'Proses')
                            <span class="w-2 h-2 bg-yellow-700 rounded-full" style="animation: progress-pulse 1.5s ease-in-out infinite;"></span>
                        @endif
                        {{ $pesanan->status }}
                    </span>
                </div>
            </div>

            {{-- Card Body --}}
            <div class="px-8 py-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    {{-- Nama Pelanggan --}}
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-sky-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-medium">Nama Pelanggan</p>
                            <p class="text-sm font-semibold text-gray-900 mt-0.5">{{ $pesanan->nama_pelanggan }}</p>
                        </div>
                    </div>

                    {{-- Jenis Layanan --}}
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-violet-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-medium">Jenis Layanan</p>
                            <p class="text-sm font-semibold text-gray-900 mt-0.5">{{ $pesanan->jenis_layanan }}</p>
                        </div>
                    </div>

                    {{-- Berat --}}
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-medium">Berat</p>
                            <p class="text-sm font-semibold text-gray-900 mt-0.5">{{ $pesanan->berat }}</p>
                        </div>
                    </div>

                    {{-- Estimasi Selesai --}}
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-medium">Estimasi Selesai</p>
                            <p class="text-sm font-semibold text-gray-900 mt-0.5">{{ $pesanan->estimasi_selesai }}</p>
                        </div>
                    </div>
                </div>

                {{-- Tanggal Masuk --}}
                <div class="mt-6 pt-5 border-t border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Tanggal Masuk: <span class="font-medium text-gray-700">{{ $pesanan->tanggal_masuk }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Back Button --}}
        <div class="mt-8 text-center">
            <a href="{{ route('tracking.index') }}"
               class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-gray-300 text-gray-700 text-sm font-semibold rounded-xl
                      hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Lacak Pesanan Lain
            </a>
        </div>
    </main>

</body>
</html>
