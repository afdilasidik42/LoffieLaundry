<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Owner') — Loffie Laundry</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { font-family: 'Inter', sans-serif; }

        /* Lock viewport */
        html, body { height: 100vh; overflow: hidden; margin: 0; padding: 0; }

        /* Preload to prevent FOUC */
        .preload * { transition: none !important; }

        /* Sidebar transition */
        .sidebar-wrap {
            width: 256px;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .sidebar-wrap.collapsed {
            width: 72px;
        }

        /* Hide text inside sidebar when collapsed */
        .sidebar-wrap.collapsed .sidebar-label {
            opacity: 0;
            width: 0;
            overflow: hidden;
            white-space: nowrap;
            transition: opacity 0.15s ease, width 0.2s ease;
        }
        .sidebar-wrap:not(.collapsed) .sidebar-label {
            opacity: 1;
            width: auto;
            transition: opacity 0.2s ease 0.1s, width 0.2s ease;
        }

        /* Section header hide */
        .sidebar-wrap.collapsed .sidebar-section-title {
            opacity: 0;
            height: 0;
            margin: 0;
            padding: 0;
            overflow: hidden;
            transition: opacity 0.15s ease, height 0.15s ease;
        }
        .sidebar-wrap:not(.collapsed) .sidebar-section-title {
            opacity: 1;
            transition: opacity 0.2s ease 0.15s;
        }

        /* Tooltip for collapsed items */
        .nav-item { position: relative; }
        .nav-item .nav-tooltip {
            display: none;
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%);
            margin-left: 12px;
            background: #1f2937;
            color: #fff;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            white-space: nowrap;
            z-index: 100;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            pointer-events: none;
        }
        .nav-item .nav-tooltip::before {
            content: '';
            position: absolute;
            right: 100%;
            top: 50%;
            transform: translateY(-50%);
            border: 5px solid transparent;
            border-right-color: #1f2937;
        }
        .sidebar-wrap.collapsed .nav-item:hover .nav-tooltip {
            display: block;
        }

        /* Toggle button */
        .sidebar-toggle {
            transition: transform 0.3s ease;
        }
        .sidebar-wrap.collapsed .sidebar-toggle {
            transform: rotate(180deg);
        }

        @keyframes fade-in {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fade-in 0.3s ease-out; }
    </style>
</head>
<body class="bg-gray-100 preload">

    <div class="flex h-screen overflow-hidden">
        {{-- Sidebar --}}
        <aside id="ownerSidebar" class="sidebar-wrap bg-gray-900 text-white flex flex-col flex-shrink-0 h-screen">
            <script>
                if (localStorage.getItem('ownerSidebarCollapsed') === 'true') {
                    document.getElementById('ownerSidebar').classList.add('collapsed');
                }
            </script>
            {{-- Brand --}}
            <div class="px-4 py-5 border-b border-gray-700/50 flex items-center gap-3">
                <a href="{{ route('owner.dashboard') }}" class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                        </svg>
                    </div>
                    <div class="sidebar-label min-w-0">
                        <h1 class="text-sm font-bold leading-tight truncate">Loffie Laundry</h1>
                        <p class="text-[10px] text-gray-400">Owner Panel</p>
                    </div>
                </a>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 px-3 py-6 space-y-1 overflow-y-auto overflow-x-hidden">
                <p class="sidebar-section-title px-3 text-[10px] font-semibold uppercase tracking-wider text-gray-500 mb-3">Menu Utama</p>

                {{-- Dashboard --}}
                <a href="{{ route('owner.dashboard') }}"
                   class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg transition-colors duration-200
                          {{ request()->routeIs('owner.dashboard') ? 'bg-emerald-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span class="sidebar-label">Dashboard</span>
                    <span class="nav-tooltip">Dashboard</span>
                </a>

                <p class="sidebar-section-title px-3 pt-5 text-[10px] font-semibold uppercase tracking-wider text-gray-500 mb-3">Manajemen</p>

                {{-- Kelola Laporan --}}
                <a href="{{ route('owner.laporan.index') }}"
                   class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg transition-colors duration-200
                          {{ request()->routeIs('owner.laporan.*') ? 'bg-emerald-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span class="sidebar-label">Kelola Laporan</span>
                    <span class="nav-tooltip">Kelola Laporan</span>
                </a>

                {{-- Kelola Grafik --}}
                <a href="{{ route('owner.grafik.index') }}"
                   class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg transition-colors duration-200
                          {{ request()->routeIs('owner.grafik.*') ? 'bg-emerald-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <span class="sidebar-label">Kelola Grafik</span>
                    <span class="nav-tooltip">Kelola Grafik</span>
                </a>

                {{-- Kelola User --}}
                <a href="{{ route('owner.users.index') }}"
                   class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg transition-colors duration-200
                          {{ request()->routeIs('owner.users.*') ? 'bg-emerald-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span class="sidebar-label">Kelola User</span>
                    <span class="nav-tooltip">Kelola User</span>
                </a>
            </nav>

            {{-- Collapse Toggle --}}
            <div class="px-3 py-2 border-t border-gray-700/50">
                <button onclick="toggleOwnerSidebar()" class="w-full flex items-center gap-3 px-3 py-2.5 text-sm text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition-colors duration-200" title="Toggle Sidebar">
                    <svg class="w-5 h-5 flex-shrink-0 sidebar-toggle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                    </svg>
                    <span class="sidebar-label">Tutup Sidebar</span>
                </button>
            </div>

            {{-- User Info --}}
            <div class="px-4 py-4 border-t border-gray-700/50">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-gray-700 rounded-full flex items-center justify-center text-sm font-semibold text-emerald-400 flex-shrink-0">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="sidebar-label flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] text-gray-400">{{ ucfirst(Auth::user()->role) }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="sidebar-label">
                        @csrf
                        <button type="submit" title="Logout" class="p-1.5 text-gray-400 hover:text-red-400 rounded-lg hover:bg-gray-800 transition-colors duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col h-screen overflow-hidden">
            {{-- Top Header --}}
            <header class="bg-white border-b border-gray-200 shadow-sm flex-shrink-0">
                <div class="px-8 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        {{-- Mobile toggle --}}
                        <button onclick="toggleOwnerSidebar()" class="lg:hidden p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">@yield('page-title', 'Dashboard')</h2>
                            <p class="text-xs text-gray-500 mt-0.5">@yield('page-description', '')</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-500">{{ now()->translatedFormat('l, d M Y') }}</span>
                    </div>
                </div>
            </header>

            {{-- Flash Messages --}}
            <div class="px-8 pt-6 flex-shrink-0">
                @if (session('success'))
                    <div id="alert-success" class="flex items-center gap-3 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl mb-4 animate-fade-in">
                        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                        <button onclick="document.getElementById('alert-success').remove()" class="ml-auto text-emerald-400 hover:text-emerald-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div id="alert-error" class="flex items-center gap-3 px-4 py-3 bg-red-50 border border-red-200 text-red-800 rounded-xl mb-4 animate-fade-in">
                        <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm font-medium">{{ session('error') }}</span>
                        <button onclick="document.getElementById('alert-error').remove()" class="ml-auto text-red-400 hover:text-red-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif
            </div>

            {{-- Page Content --}}
            <main class="flex-1 overflow-y-auto px-8 pb-8 @if(!session('success') && !session('error')) pt-6 @endif">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        function toggleOwnerSidebar() {
            const sidebar = document.getElementById('ownerSidebar');
            sidebar.classList.toggle('collapsed');
            if (sidebar.classList.contains('collapsed')) {
                localStorage.setItem('ownerSidebarCollapsed', 'true');
            } else {
                localStorage.setItem('ownerSidebarCollapsed', 'false');
            }
        }

        // Restore sidebar state on load
        document.addEventListener('DOMContentLoaded', function() {
            // Remove preload to enable transitions
            setTimeout(() => {
                document.body.classList.remove('preload');
            }, 50);
        });
    </script>

    @stack('scripts')
</body>
</html>
