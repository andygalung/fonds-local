<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — SIIAR PTPN IV Regional 1</title>
    <meta name="description" content="Sistem Informasi Investasi & Aset Regional PTPN IV — monitoring data investasi real-time dari Google Sheets">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        primary: {
                            50:  '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                            950: '#172554',
                        },
                        gold: {
                            50:  '#fffbeb',
                            100: '#fef3c7',
                            200: '#fde68a',
                            300: '#fcd34d',
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309',
                            800: '#92400e',
                            900: '#78350f',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <style>
        :root { --sidebar-width: 260px; }
        * { font-family: 'Inter', sans-serif; }

        /* Sidebar transition */
        #sidebar { transition: transform 0.3s ease; }
        #main-content { transition: margin-left 0.3s ease; }

        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Active nav item */
        .nav-item.active { background: rgba(255,255,255,0.15); border-left: 3px solid #fbbf24; }
        .nav-item { transition: all 0.2s; }
        .nav-item:hover { background: rgba(255,255,255,0.1); }

        /* Card hover */
        .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 20px 40px rgba(0,0,0,0.1); }

        /* Gradient text */
        .gradient-text {
            background: linear-gradient(135deg, #1e40af, #d97706);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Loading spinner */
        .spinner {
            width: 20px; height: 20px;
            border: 2px solid #fff; border-top-color: transparent;
            border-radius: 50%; animation: spin 0.7s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* Table row hover */
        .table-row-hover { transition: background 0.15s; }
        .table-row-hover:hover { background: #eff6ff; }
    </style>

    @stack('styles')
</head>
<body class="h-full bg-slate-50 text-slate-800">

<div class="flex h-full">
    <!-- ─── Sidebar ─────────────────────────────────────────────────── -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 flex flex-col w-64 bg-gradient-to-b from-primary-900 to-primary-950 shadow-2xl">

        <!-- Logo / Brand -->
        <div class="flex items-center gap-3 px-6 py-5 border-b border-white/10">
            <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-gold-500 flex items-center justify-center shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div>
                <p class="text-white font-bold text-sm leading-tight">SIIAR</p>
                <p class="text-gold-400 text-xs font-medium">PTPN IV Regional 1</p>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            <p class="px-3 text-white/40 text-xs font-semibold uppercase tracking-wider mb-2">Menu Utama</p>

            <a href="{{ route('dashboard') }}"
               class="nav-item @if(request()->routeIs('dashboard')) active @endif flex items-center gap-3 px-3 py-2.5 rounded-lg text-white/80 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span class="text-sm font-medium">Dashboard</span>
            </a>

            <a href="{{ route('investasi.index') }}"
               class="nav-item @if(request()->routeIs('investasi.*')) active @endif flex items-center gap-3 px-3 py-2.5 rounded-lg text-white/80 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="text-sm font-medium">Data Investasi</span>
            </a>

            <a href="{{ route('prioritas.index') }}"
               class="nav-item @if(request()->routeIs('prioritas.*')) active @endif flex items-center gap-3 px-3 py-2.5 rounded-lg text-white/80 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                </svg>
                <span class="text-sm font-medium">Dashboard Prioritas</span>
            </a>

            <div class="my-3 border-t border-white/10"></div>
            <p class="px-3 text-white/40 text-xs font-semibold uppercase tracking-wider mb-2">Sistem</p>

            <a href="{{ route('sync.index') }}"
               class="nav-item @if(request()->routeIs('sync.*')) active @endif flex items-center gap-3 px-3 py-2.5 rounded-lg text-white/80 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <span class="text-sm font-medium">Sinkronisasi</span>
            </a>
        </nav>

        <!-- Footer sidebar -->
        <div class="px-5 py-4 border-t border-white/10">
            <p class="text-white/40 text-xs text-center">© {{ date('Y') }} SIIAR — PTPN IV Regional 1</p>
        </div>
    </aside>

    <!-- ─── Main Content ──────────────────────────────────────────────── -->
    <div id="main-content" class="flex-1 ml-64 flex flex-col min-h-full">

        <!-- Top Bar -->
        <header class="sticky top-0 z-30 bg-white border-b border-slate-200 shadow-sm">
            <div class="flex items-center justify-between px-6 py-3">
                <div class="flex items-center gap-3">
                    <!-- Mobile hamburger -->
                    <button id="sidebar-toggle" class="lg:hidden p-1.5 rounded-lg text-slate-500 hover:bg-slate-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <div>
                        <h1 class="text-base font-semibold text-slate-800">@yield('page-title', 'Dashboard')</h1>
                        <p class="text-xs text-slate-500">@yield('page-subtitle', 'Sistem Informasi Investasi & Aset Regional')</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Date badge -->
                    <span class="hidden sm:flex items-center gap-1.5 text-xs text-slate-500 bg-slate-100 px-3 py-1.5 rounded-full">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        {{ \Carbon\Carbon::now('Asia/Jakarta')->isoFormat('D MMM YYYY') }}
                    </span>

                    <!-- Sync shortcut -->
                    <a href="{{ route('sync.index') }}"
                       class="flex items-center gap-1.5 text-xs font-medium bg-primary-800 text-white px-3 py-1.5 rounded-full hover:bg-primary-700 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Sync
                    </a>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 p-6">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="text-center text-xs text-slate-400 py-4 border-t border-slate-200">
            Sistem Informasi Investasi & Aset Regional (SIIAR) &mdash; PTPN IV Regional 1 &mdash; {{ date('Y') }}
        </footer>
    </div>
</div>

<!-- Mobile sidebar overlay -->
<div id="sidebar-overlay" class="fixed inset-0 z-30 bg-black/50 hidden lg:hidden"></div>

<script>
    const toggle = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');

    toggle?.addEventListener('click', () => {
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    });

    overlay?.addEventListener('click', () => {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    });
</script>

@stack('scripts')
</body>
</html>
