<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Loffie Laundry — Sistem Informasi Laundry berbasis web dengan prediksi waktu selesai otomatis.">
    <title>Loffie Laundry — Selamat Datang</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-sky-50 via-white to-cyan-50 min-h-screen flex items-center justify-center">

    {{-- Decorative background blobs --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none -z-10" aria-hidden="true">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-sky-200/40 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute -bottom-32 -right-32 w-[28rem] h-[28rem] bg-cyan-200/30 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
        <div class="absolute top-1/2 left-1/3 w-64 h-64 bg-blue-100/20 rounded-full blur-2xl"></div>
    </div>

    <div class="w-full max-w-md mx-auto px-6 py-12">
        {{-- Logo & Branding --}}
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-sky-500 to-cyan-600 rounded-2xl shadow-lg shadow-sky-500/25 mb-6 transform hover:scale-105 transition-transform duration-300">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                </svg>
            </div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                Loffie Laundry
            </h1>
            <p class="mt-2 text-gray-500 text-sm leading-relaxed">
                Sistem Informasi Laundry<br>dengan Prediksi Waktu Cerdas
            </p>
        </div>

        {{-- Action Cards --}}
        <div class="space-y-4">
            {{-- Login Staff Button --}}
            <a href="{{ route('login') }}" id="btn-login-staff"
               class="group relative flex items-center w-full px-6 py-4 bg-white rounded-xl border border-gray-200/80 shadow-sm hover:shadow-md hover:border-sky-300 transition-all duration-300 overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-sky-500 to-cyan-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative flex items-center w-full">
                    <div class="flex-shrink-0 w-12 h-12 bg-sky-100 group-hover:bg-white/20 rounded-lg flex items-center justify-center transition-colors duration-300">
                        <svg class="w-6 h-6 text-sky-600 group-hover:text-white transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-base font-semibold text-gray-900 group-hover:text-white transition-colors duration-300">
                            Login Staff
                        </p>
                        <p class="text-xs text-gray-500 group-hover:text-sky-100 transition-colors duration-300 mt-0.5">
                            Masuk sebagai Admin atau Owner
                        </p>
                    </div>
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-white group-hover:translate-x-1 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </a>

            {{-- Cek Pesanan Button --}}
            <a href="/track" id="btn-cek-pesanan"
               class="group relative flex items-center w-full px-6 py-4 bg-white rounded-xl border border-gray-200/80 shadow-sm hover:shadow-md hover:border-emerald-300 transition-all duration-300 overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-emerald-500 to-teal-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative flex items-center w-full">
                    <div class="flex-shrink-0 w-12 h-12 bg-emerald-100 group-hover:bg-white/20 rounded-lg flex items-center justify-center transition-colors duration-300">
                        <svg class="w-6 h-6 text-emerald-600 group-hover:text-white transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-base font-semibold text-gray-900 group-hover:text-white transition-colors duration-300">
                            Cek Pesanan
                        </p>
                        <p class="text-xs text-gray-500 group-hover:text-emerald-100 transition-colors duration-300 mt-0.5">
                            Lacak status pesanan Anda
                        </p>
                    </div>
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-white group-hover:translate-x-1 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </a>
        </div>

        {{-- Footer --}}
        <p class="text-center text-xs text-gray-400 mt-10">
            &copy; {{ date('Y') }} Loffie Laundry. All rights reserved.
        </p>
    </div>

</body>
</html>
