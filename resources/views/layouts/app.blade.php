<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>RIMzone — @yield('title', 'Boshqaruv paneli')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --primary-glow: rgba(37, 99, 235, 0.15);
            --sidebar-width: 260px;
        }
        *, body { font-family: 'Inter', sans-serif; scroll-behavior: smooth; }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        [x-cloak] { display: none !important; }

        /* Page Transitions */
        .page-enter { animation: slideUpFade 0.5s cubic-bezier(0.16, 1, 0.3, 1) both; }
        @keyframes slideUpFade {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Sidebar nav links */
        .nav-link { 
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); 
            border-left: 4px solid transparent; 
            margin: 2px 8px;
            border-radius: 12px;
        }
        .nav-link:hover { background-color: #f1f5f9; transform: translateX(4px); }
        .nav-link svg { transition: transform 0.25s ease; }
        .nav-link:hover svg { transform: scale(1.1); }

        /* Each section has its own accent color */
        .nav-link.active-blue  { background: #eff6ff; color: #2563eb; font-weight:700; border-left-color: #2563eb; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.08); }
        .nav-link.active-violet { background: #f5f3ff; color: #7c3aed; font-weight:700; border-left-color: #7c3aed; box-shadow: 0 4px 12px rgba(124, 58, 237, 0.08); }
        .nav-link.active-green { background: #f0fdf4; color: #16a34a; font-weight:700; border-left-color: #16a34a; box-shadow: 0 4px 12px rgba(22, 163, 74, 0.08); }
        .nav-link.active-amber { background: #fffbeb; color: #d97706; font-weight:700; border-left-color: #d97706; box-shadow: 0 4px 12px rgba(217, 119, 6, 0.08); }

        /* Hide scrollbar but keep scroll */
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

        /* Glassmorphism utility */
        .glass {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
        }
    </style>
    @yield('head')
</head>
<body class="bg-slate-50 min-h-screen flex text-slate-700" x-data="{ sidebarOpen: false }">

    <!-- Mobile backdrop -->
    <div x-show="sidebarOpen" x-cloak
         class="md:hidden fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-sm"
         @click="sidebarOpen = false"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"></div>

    <!-- ===== SIDEBAR ===== -->
    <aside class="fixed md:sticky md:top-0 inset-y-0 left-0 z-50 md:z-auto h-screen w-64 bg-white border-r border-slate-200 flex flex-col shrink-0 transition-transform duration-300 ease-in-out"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">

        <!-- Logo -->
        <div class="h-16 flex items-center px-5 border-b border-slate-200">
            <!-- Mobile close button -->
            <button @click="sidebarOpen = false"
                    class="md:hidden mr-3 p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <a href="{{ url('/products') }}" class="flex items-center gap-2.5 w-full">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-sm shrink-0">
                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"/>
                        <path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <span class="text-base font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">RIMzone</span>
                    <p class="text-[10px] text-slate-400 leading-none">Boshqaruv tizimi</p>
                </div>
            </a>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest px-3 pb-2">Asosiy bo'limlar</p>

            <!-- Ombor — blue -->
            <a href="{{ url('/products') }}"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-500 text-sm {{ request()->is('products') ? 'active-blue' : '' }}">
                <div class="w-7 h-7 rounded-lg {{ request()->is('products') ? 'bg-blue-100' : 'bg-slate-100' }} flex items-center justify-center shrink-0 transition-colors">
                    <svg class="w-3.5 h-3.5 {{ request()->is('products') ? 'text-blue-600' : 'text-slate-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                    </svg>
                </div>
                <span>Ombor</span>
            </a>

            <!-- Mijozlar — violet -->
            <a href="{{ url('/customers') }}"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-500 text-sm {{ request()->is('customers') ? 'active-violet' : '' }}">
                <div class="w-7 h-7 rounded-lg {{ request()->is('customers') ? 'bg-violet-100' : 'bg-slate-100' }} flex items-center justify-center shrink-0 transition-colors">
                    <svg class="w-3.5 h-3.5 {{ request()->is('customers') ? 'text-violet-600' : 'text-slate-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <span>Mijozlar</span>
            </a>

            <!-- Savdo — green -->
            <a href="{{ url('/sales') }}"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-500 text-sm {{ request()->is('sales') ? 'active-green' : '' }}">
                <div class="w-7 h-7 rounded-lg {{ request()->is('sales') ? 'bg-green-100' : 'bg-slate-100' }} flex items-center justify-center shrink-0 transition-colors">
                    <svg class="w-3.5 h-3.5 {{ request()->is('sales') ? 'text-green-600' : 'text-slate-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <span>Savdo</span>
            </a>

            <!-- Hisobotlar — amber -->
            <a href="{{ url('/reports') }}"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-500 text-sm {{ request()->is('reports') ? 'active-amber' : '' }}">
                <div class="w-7 h-7 rounded-lg {{ request()->is('reports') ? 'bg-amber-100' : 'bg-slate-100' }} flex items-center justify-center shrink-0 transition-colors">
                    <svg class="w-3.5 h-3.5 {{ request()->is('reports') ? 'text-amber-600' : 'text-slate-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <span>Hisobotlar</span>
            </a>
        </nav>

        <!-- Bottom: user info + logout -->
        <div class="p-4 border-t border-slate-200">
            <div class="flex items-center gap-2.5 px-2 mb-3">
                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shrink-0">
                    <svg class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-slate-700 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-[10px] text-slate-400 truncate">{{ auth()->user()->email }}</p>
                </div>
            </div>
            <form method="POST" action="{{ url('/logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-500 hover:bg-red-50 hover:text-red-600 transition-all duration-200 text-sm font-medium group">
                    <svg class="w-4 h-4 shrink-0 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Chiqish
                </button>
            </form>
        </div>
    </aside>

    <!-- ===== MAIN CONTENT ===== -->
    <div class="flex-1 flex flex-col min-h-screen overflow-auto page-enter min-w-0">

        <!-- Mobile top bar -->
        <div class="layout-mobile-topbar md:hidden sticky top-0 z-30 h-14 bg-white/95 backdrop-blur-sm border-b border-slate-200 flex items-center px-4 gap-3 shrink-0 shadow-sm">
            <button @click="sidebarOpen = true"
                    class="p-2 rounded-xl text-slate-500 hover:bg-slate-100 active:bg-slate-200 transition-colors shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <a href="{{ url('/products') }}" class="flex items-center gap-2 shrink-0">
                <div class="w-7 h-7 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-sm">
                    <svg class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"/>
                        <path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <span class="text-sm font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">RIMzone</span>
            </a>
            <span class="ml-auto text-xs font-semibold text-slate-500 truncate">@yield('title', '')</span>
        </div>

        @yield('content')
    </div>

    @yield('scripts')


</body>
</html>
