<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lacak Pesanan — Loffie Laundry</title>
    <meta name="description" content="Lacak status pesanan laundry Anda secara real-time di Loffie Laundry.">
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
        @keyframes pulse-ring {
            0%   { transform: scale(0.9); opacity: 1; }
            80%, 100% { transform: scale(1.8); opacity: 0; }
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
            <a href="{{ route('welcome') }}" class="text-sm text-gray-500 hover:text-gray-700 transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
        </div>
    </nav>

    {{-- Main Content --}}
    <main class="max-w-xl mx-auto px-6 py-16 animate-fade-in-up">
        {{-- Header --}}
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-sky-100 rounded-2xl mb-5 relative">
                <svg class="w-8 h-8 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <span class="absolute inset-0 rounded-2xl bg-sky-400 opacity-20" style="animation: pulse-ring 2s cubic-bezier(0.4,0,0.6,1) infinite;"></span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Lacak Pesanan Anda</h1>
            <p class="text-sm text-gray-500 mt-2 max-w-sm mx-auto">
                Masukkan ID Pesanan untuk melihat status laundry Anda secara real-time.
            </p>
        </div>

        {{-- Search Form Card --}}
        <div class="bg-white rounded-2xl shadow-lg shadow-gray-200/50 border border-gray-200/80 p-8">
            <form method="POST" action="{{ route('tracking.search') }}" id="tracking-form">
                @csrf

                <label for="id_pesanan" class="block text-sm font-semibold text-gray-700 mb-2">ID Pesanan</label>
                <div class="relative">
                    <input
                        type="text"
                        name="id_pesanan"
                        id="id_pesanan"
                        value="{{ old('id_pesanan') }}"
                        placeholder="Contoh: ORD-20260101-0001"
                        autocomplete="off"
                        class="w-full px-4 py-3.5 pr-12 border border-gray-300 rounded-xl text-sm text-gray-900 placeholder-gray-400
                               focus:outline-none focus:ring-2 focus:ring-sky-500/40 focus:border-sky-500
                               transition-all duration-200
                               @error('id_pesanan') border-red-400 ring-2 ring-red-500/20 @enderror"
                    >
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                        </svg>
                    </div>
                </div>

                @error('id_pesanan')
                    <div class="mt-3 flex items-center gap-2 px-4 py-3 bg-red-50 border border-red-200 rounded-xl">
                        <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm text-red-700 font-medium">{{ $message }}</span>
                    </div>
                @enderror

                <button
                    type="submit"
                    id="btn-search"
                    class="mt-5 w-full py-3.5 bg-gradient-to-r from-sky-500 to-cyan-600 hover:from-sky-600 hover:to-cyan-700
                           text-white text-sm font-semibold rounded-xl shadow-lg shadow-sky-500/25
                           transition-all duration-300 hover:shadow-sky-500/40 hover:-translate-y-0.5
                           flex items-center justify-center gap-2"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Lacak Sekarang
                </button>
            </form>
        </div>

        {{-- Hint --}}
        <div class="mt-6 bg-blue-50/80 border border-blue-200/60 rounded-xl px-5 py-4 flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="text-sm font-medium text-blue-800">Demo Mode</p>
                <p class="text-xs text-blue-700 mt-1">Gunakan ID <code class="bg-blue-200/60 px-1.5 py-0.5 rounded font-mono text-xs font-bold">ORD-MOCK</code> untuk melihat contoh hasil pelacakan.</p>
            </div>
        </div>
    </main>

</body>
</html>
