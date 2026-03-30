@extends('layouts.app')
@section('title', 'Hisobotlar')

@section('head')
<style>
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .stat-card { animation: fadeUp 0.4s cubic-bezier(0.16,1,0.3,1) both; }
    .stat-card:nth-child(1){animation-delay:.04s}
    .stat-card:nth-child(2){animation-delay:.09s}
    .stat-card:nth-child(3){animation-delay:.14s}
    .stat-card:nth-child(4){animation-delay:.19s}
    .tab-panel { animation: fadeUp 0.22s ease both; }
    .debt-row-overdue { background: linear-gradient(90deg,rgba(254,226,226,.6) 0%,rgba(255,241,242,.3) 100%); }
    .debt-row-partial  { background: linear-gradient(90deg,rgba(254,243,199,.6) 0%,rgba(255,251,235,.3) 100%); }
    @keyframes pulseRed {
        0%,100%{ box-shadow:0 0 0 0 rgba(239,68,68,.35); }
        50%    { box-shadow:0 0 0 6px rgba(239,68,68,0); }
    }
    .overdue-pulse { animation: pulseRed 2s ease-in-out infinite; }
    [x-cloak]{ display:none !important; }
</style>
@endsection

@section('content')
<div x-data="reportsApp()">

{{-- ══ STICKY HEADER + TABS ══════════════════════════════════════════ --}}
<div class="sticky top-0 z-30 bg-white border-b border-slate-200 shadow-sm">
    {{-- Top bar --}}
    <div class="max-w-7xl mx-auto px-4 md:px-5 py-3">

        {{-- ── MOBILE layout (< md): 2 rows ─────────────────────────── --}}
        <div class="md:hidden">
            {{-- Row 1: Title + Delete --}}
            <div class="flex items-center justify-between mb-2">
                <div>
                    <h1 class="text-base font-black text-slate-800 leading-tight">Hisobotlar</h1>
                    <p class="text-[11px] text-slate-400">
                        {{ \Carbon\Carbon::parse($startDate)->format('d.m.Y') }}
                        @if($startDate !== $endDate) — {{ \Carbon\Carbon::parse($endDate)->format('d.m.Y') }} @endif
                    </p>
                </div>
                <button @click="openClearDay()"
                    class="flex items-center gap-1 bg-white border border-red-200 hover:bg-red-50 active:bg-red-100 text-red-600 p-2 rounded-xl transition-all shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>
            {{-- Row 2: Full-width filter form --}}
            <form action="{{ url('/reports') }}" method="GET" class="flex items-center gap-2">
                <div class="flex-1 flex items-center rounded-xl overflow-hidden divide-x divide-slate-200 bg-slate-50 border border-slate-200 shadow-sm min-w-0">
                    <input type="date" name="start_date" value="{{ $startDate }}"
                        class="flex-1 min-w-0 px-2 py-2 text-xs font-medium text-slate-600 focus:outline-none [color-scheme:light] bg-transparent cursor-pointer">
                    <input type="date" name="end_date" value="{{ $endDate }}"
                        class="flex-1 min-w-0 px-2 py-2 text-xs font-medium text-slate-600 focus:outline-none [color-scheme:light] bg-transparent cursor-pointer">
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white px-4 py-2 rounded-xl flex items-center gap-1.5 shadow-sm shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    <span class="text-xs font-bold">Filter</span>
                </button>
            </form>
        </div>

        {{-- ── DESKTOP layout (≥ md): single row ────────────────────── --}}
        <div class="hidden md:flex items-center gap-3">
            <div class="flex-1 min-w-0">
                <h1 class="text-base font-black text-slate-800 leading-tight">Hisobotlar</h1>
                <p class="text-[11px] text-slate-400">
                    {{ \Carbon\Carbon::parse($startDate)->format('d.m.Y') }}
                    @if($startDate !== $endDate) — {{ \Carbon\Carbon::parse($endDate)->format('d.m.Y') }} @endif
                </p>
            </div>
            <form action="{{ url('/reports') }}" method="GET" class="flex items-center gap-1.5 shrink-0">
                <div class="flex items-center rounded-xl overflow-hidden divide-x divide-slate-200 bg-slate-50 border border-slate-200 shadow-sm">
                    <input type="date" name="start_date" value="{{ $startDate }}"
                        class="px-2 py-2 text-xs font-medium text-slate-600 focus:outline-none [color-scheme:light] bg-transparent cursor-pointer w-[118px]">
                    <input type="date" name="end_date" value="{{ $endDate }}"
                        class="px-2 py-2 text-xs font-medium text-slate-600 focus:outline-none [color-scheme:light] bg-transparent cursor-pointer w-[118px]">
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white px-3 py-2 rounded-xl transition-all flex items-center gap-1.5 shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    <span class="text-xs font-bold">Filter</span>
                </button>
            </form>
            <button @click="openClearDay()"
                class="flex items-center gap-1.5 bg-white border border-red-200 hover:bg-red-50 active:bg-red-100 text-red-600 font-semibold text-xs px-3 py-2 rounded-xl transition-all shadow-sm">
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Kunni o'chirish
            </button>
        </div>

    </div>
    {{-- Tab bar --}}
    <div class="max-w-7xl mx-auto px-4 md:px-5 flex items-end gap-0.5 overflow-x-auto scrollbar-hide">
        <button @click="activeTab='overview'"
            :class="activeTab==='overview' ? 'border-blue-600 text-blue-700 bg-blue-50/50' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50'"
            class="flex items-center gap-1.5 px-3 md:px-4 py-2.5 text-xs md:text-sm font-bold border-b-2 transition-all whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            Umumiy
        </button>
        <button @click="activeTab='nasiya'"
            :class="activeTab==='nasiya' ? 'border-amber-500 text-amber-700 bg-amber-50/50' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50'"
            class="flex items-center gap-1.5 px-3 md:px-4 py-2.5 text-xs md:text-sm font-bold border-b-2 transition-all whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            Nasiya
            @if($allDebtSales->count() > 0)
            <span class="px-1.5 py-0.5 text-[10px] font-black rounded-full {{ $overdueNasiya > 0 ? 'bg-red-500 text-white' : 'bg-amber-100 text-amber-700' }}">
                {{ $allDebtSales->count() }}
            </span>
            @endif
        </button>
        <button @click="activeTab='products'"
            :class="activeTab==='products' ? 'border-violet-600 text-violet-700 bg-violet-50/50' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50'"
            class="flex items-center gap-1.5 px-3 md:px-4 py-2.5 text-xs md:text-sm font-bold border-b-2 transition-all whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            <span class="hidden sm:inline">Mahsulotlar</span><span class="sm:hidden">Mahsulot</span>
        </button>
        <button @click="activeTab='sales'"
            :class="activeTab==='sales' ? 'border-teal-600 text-teal-700 bg-teal-50/50' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50'"
            class="flex items-center gap-1.5 px-3 md:px-4 py-2.5 text-xs md:text-sm font-bold border-b-2 transition-all whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
            Savdolar
            <span class="px-1.5 py-0.5 text-[10px] font-black bg-slate-100 text-slate-500 rounded-full">{{ count($sales) }}</span>
        </button>
    </div>
</div>

<main class="p-4 md:p-5 pb-24 md:pb-8 max-w-7xl mx-auto space-y-4 md:space-y-5">

    {{-- ══ 3 MAIN CARDS ════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        {{-- Card 1: Jami Savdo --}}
        <div class="stat-card relative bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl p-5 border border-blue-500 overflow-hidden hover:-translate-y-1 transition-transform shadow-lg shadow-blue-500/20">
            <div class="absolute right-0 top-0 w-32 h-32 bg-white/10 rounded-full blur-2xl -mr-8 -mt-8 pointer-events-none"></div>
            <div class="relative z-10">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <p class="text-xs font-bold text-blue-200 uppercase tracking-widest">Jami Savdo</p>
                </div>
                <h2 class="text-3xl font-black text-white leading-none">{{ number_format($totalRevenue,0,',',' ') }}</h2>
                <p class="text-sm text-blue-300 mt-1.5 font-medium">so'm &nbsp;·&nbsp; {{ count($sales) }} ta sotuv</p>
            </div>
        </div>

        {{-- Card 2: Sof Foyda (Naqd) — eng katta --}}
        <div class="stat-card relative rounded-2xl p-5 border overflow-hidden hover:-translate-y-1 transition-transform shadow-xl
            {{ $realCashProfit >= 0 ? 'bg-gradient-to-br from-emerald-500 to-teal-600 border-emerald-400 shadow-emerald-500/25' : 'bg-gradient-to-br from-red-500 to-rose-600 border-red-400 shadow-red-500/25' }}">
            <div class="absolute right-0 top-0 w-32 h-32 bg-white/10 rounded-full blur-2xl -mr-8 -mt-8 pointer-events-none"></div>
            <div class="relative z-10">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                    <p class="text-xs font-bold text-white/70 uppercase tracking-widest">Sof Foyda (Naqd)</p>
                </div>
                <h2 class="text-3xl font-black text-white leading-none">{{ $realCashProfit >= 0 ? '+' : '' }}{{ number_format($realCashProfit,0,',',' ') }}</h2>
                <p class="text-sm text-white/70 mt-1.5 font-medium">so'm &nbsp;·&nbsp; hozir cho'ntakda</p>
                @if($profitInDebt > 0.01)
                <p class="text-[11px] text-white/50 mt-1">+ {{ number_format($profitInDebt,0,',',' ') }} so'm nasiyada kutilmoqda</p>
                @endif
            </div>
        </div>

        {{-- Card 3: Nasiya Summasi --}}
        <div class="stat-card relative rounded-2xl p-5 border overflow-hidden hover:-translate-y-1 transition-transform shadow-lg
            {{ $overdueNasiya > 0 ? 'bg-gradient-to-br from-orange-500 to-red-600 border-orange-400 shadow-orange-500/20 overdue-pulse' : 'bg-gradient-to-br from-amber-400 to-orange-500 border-amber-300 shadow-amber-400/20' }}">
            <div class="absolute right-0 top-0 w-32 h-32 bg-white/10 rounded-full blur-2xl -mr-8 -mt-8 pointer-events-none"></div>
            <div class="relative z-10">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-white/25 flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="text-xs font-bold text-white/80 uppercase tracking-widest">Nasiya Summasi</p>
                </div>
                <h2 class="text-3xl font-black text-white leading-none">{{ number_format($totalNasiya,0,',',' ') }}</h2>
                <p class="text-sm text-white/70 mt-1.5 font-medium">so'm &nbsp;·&nbsp; {{ $allDebtSales->count() }} ta qarz</p>
                @if($overdueNasiya > 0)
                <p class="text-[11px] text-white/60 mt-1">{{ number_format($overdueNasiya,0,',',' ') }} so'm muddati o'tgan</p>
                @endif
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- TAB 1: UMUMIY                                                     --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div x-show="activeTab==='overview'" x-cloak class="space-y-5 tab-panel">
    @php
        $naqdPct      = $totalRevenue > 0 ? round($paymentSummary['naqd']  / $totalRevenue * 100, 1) : 0;
        $kartaPct     = $totalRevenue > 0 ? round($paymentSummary['karta'] / $totalRevenue * 100, 1) : 0;
        $nasiyaPct    = $totalRevenue > 0 ? round($paymentSummary['nasiya']/ $totalRevenue * 100, 1) : 0;
        $realMoney    = $paymentSummary['naqd'] + $paymentSummary['karta'];
        $realMoneyPct = $totalRevenue > 0 ? round($realMoney / $totalRevenue * 100, 1) : 0;
    @endphp

        {{-- ── 1. NAQD TUSHUM HOLATI (Progress Bar) ────────────────────── --}}
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
            <div class="px-6 pt-5 pb-4">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">Naqd tushum holati</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Davr savdolarining necha foizi naqd?</p>
                    </div>
                    <span class="text-2xl font-black {{ $cashRatio >= 50 ? 'text-emerald-600' : 'text-orange-500' }}">{{ $cashRatio }}%</span>
                </div>

                {{-- Thick progress bar --}}
                <div class="relative h-8 bg-orange-100 rounded-xl overflow-hidden border border-orange-200">
                    @if($cashRatio > 0)
                    <div class="h-full bg-gradient-to-r from-emerald-500 to-teal-500 rounded-xl flex items-center justify-end pr-3 transition-all"
                         style="width:{{ max(6,$cashRatio) }}%">
                        @if($cashRatio >= 20)
                        <span class="text-[11px] font-black text-white drop-shadow">{{ $cashRatio }}%</span>
                        @endif
                    </div>
                    @endif
                    @if($cashRatio < 100 && round(100-$cashRatio,1) >= 15)
                    <div class="absolute right-3 top-1/2 -translate-y-1/2">
                        <span class="text-[11px] font-black text-orange-600">{{ round(100-$cashRatio,1) }}%</span>
                    </div>
                    @endif
                </div>

                <div class="flex justify-between mt-2.5">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded bg-emerald-500 inline-block shrink-0"></span>
                        <div>
                            <p class="text-[10px] text-slate-400 font-medium">Naqd tushum</p>
                            <p class="text-sm font-black text-emerald-700">+ {{ number_format($cashReceived,0,',',' ') }} so'm</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 text-right">
                        <div>
                            <p class="text-[10px] text-slate-400 font-medium">Qarzda qolgan</p>
                            <p class="text-sm font-black text-orange-600">− {{ number_format($debtInPeriod,0,',',' ') }} so'm</p>
                        </div>
                        <span class="w-3 h-3 rounded bg-orange-300 inline-block shrink-0"></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── 2. MOLIYAVIY BALANS (+/−) ────────────────────────────────── --}}
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-slate-100">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">Moliyaviy Balans</p>
            </div>
            <div class="divide-y divide-slate-100">
                {{-- Kutilayotgan foyda --}}
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-center shrink-0">
                            <span class="text-base font-black text-blue-500 leading-none">≈</span>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-700">Kutilayotgan foyda</p>
                            <p class="text-[11px] text-slate-400">Barcha nasiyalar to'lansa</p>
                        </div>
                    </div>
                    <p class="text-lg font-black text-slate-600">{{ $totalProfit >= 0 ? '+' : '' }}{{ number_format($totalProfit,0,',',' ') }} <span class="text-xs font-semibold text-slate-400">so'm</span></p>
                </div>

                {{-- Nasiyadagi summa --}}
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-red-50 border border-red-100 flex items-center justify-center shrink-0">
                            <span class="text-lg font-black text-red-500 leading-none">−</span>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-700">Nasiyadagi summa</p>
                            <p class="text-[11px] text-slate-400">Mijozlarda kutilayotgan
                                @if($overdueNasiya > 0)
                                &nbsp;·&nbsp; <span class="text-red-500 font-bold">{{ number_format($overdueNasiya,0,',',' ') }} so'm muddati o'tgan</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <p class="text-lg font-black text-red-600">− {{ number_format($totalNasiya,0,',',' ') }} <span class="text-xs font-semibold text-red-300">so'm</span></p>
                </div>

                {{-- Sof Foyda (Naqd) — BOLDEST --}}
                <div class="flex items-center justify-between px-6 py-5 {{ $realCashProfit >= 0 ? 'bg-emerald-50/60' : 'bg-red-50/60' }}">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl {{ $realCashProfit >= 0 ? 'bg-emerald-500' : 'bg-red-500' }} flex items-center justify-center shrink-0 shadow-md">
                            <span class="text-lg font-black text-white leading-none">{{ $realCashProfit >= 0 ? '=' : '!' }}</span>
                        </div>
                        <div>
                            <p class="text-base font-black {{ $realCashProfit >= 0 ? 'text-emerald-800' : 'text-red-800' }}">Sof Foyda (Naqd)</p>
                            <p class="text-[11px] {{ $realCashProfit >= 0 ? 'text-emerald-600' : 'text-red-500' }}">Hozir cho'ntakda mavjud</p>
                        </div>
                    </div>
                    <p class="text-2xl font-black {{ $realCashProfit >= 0 ? 'text-emerald-700' : 'text-red-700' }}">
                        {{ $realCashProfit >= 0 ? '+' : '' }}{{ number_format($realCashProfit,0,',',' ') }}
                        <span class="text-sm font-semibold {{ $realCashProfit >= 0 ? 'text-emerald-500' : 'text-red-400' }}">so'm</span>
                    </p>
                </div>
            </div>
        </div>

        {{-- ── 3. TO'LOV USULLARI (guruhlab, foizlar bilan) ─────────────── --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

            {{-- Real Pul: Naqd + Karta --}}
            <div class="sm:col-span-2 bg-white border border-emerald-100 rounded-2xl overflow-hidden shadow-sm">
                <div class="px-5 py-3.5 border-b border-emerald-100 flex items-center justify-between bg-emerald-50/50">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                        <p class="text-xs font-black text-emerald-800 uppercase tracking-widest">Real Pul (Naqd + Karta)</p>
                    </div>
                    <span class="text-xs font-black text-emerald-700 bg-emerald-100 px-2.5 py-1 rounded-full">{{ $realMoneyPct }}%</span>
                </div>
                <div class="divide-y divide-slate-100">
                    <div class="flex items-center justify-between px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>
                            <span class="text-sm font-semibold text-slate-700">💵 Naqd pul</span>
                        </div>
                        <div class="text-right">
                            <p class="text-base font-black text-slate-800">{{ number_format($paymentSummary['naqd'],0,',',' ') }} <span class="text-xs text-slate-400">so'm</span></p>
                            <p class="text-[11px] font-bold text-emerald-500">{{ $naqdPct }}% ulush</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            </div>
                            <span class="text-sm font-semibold text-slate-700">💳 Karta</span>
                        </div>
                        <div class="text-right">
                            <p class="text-base font-black text-slate-800">{{ number_format($paymentSummary['karta'],0,',',' ') }} <span class="text-xs text-slate-400">so'm</span></p>
                            <p class="text-[11px] font-bold text-blue-500">{{ $kartaPct }}% ulush</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between px-5 py-3 bg-emerald-50/40">
                        <span class="text-xs font-bold text-emerald-700 uppercase tracking-wide">Jami real pul</span>
                        <p class="text-base font-black text-emerald-700">{{ number_format($realMoney,0,',',' ') }} <span class="text-xs">so'm</span></p>
                    </div>
                </div>
            </div>

            {{-- Nasiya --}}
            <div class="bg-white border border-orange-100 rounded-2xl overflow-hidden shadow-sm">
                <div class="px-5 py-3.5 border-b border-orange-100 flex items-center justify-between bg-orange-50/50">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-orange-400"></div>
                        <p class="text-xs font-black text-orange-800 uppercase tracking-widest">Nasiya</p>
                    </div>
                    <span class="text-xs font-black text-orange-700 bg-orange-100 px-2.5 py-1 rounded-full">{{ $nasiyaPct }}%</span>
                </div>
                <div class="px-5 py-5 flex flex-col gap-2">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-orange-400 to-amber-500 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <span class="text-sm font-semibold text-slate-700">📋 Nasiya savdolar</span>
                    </div>
                    <p class="text-xl font-black text-orange-700 mt-1">{{ number_format($paymentSummary['nasiya'],0,',',' ') }} <span class="text-xs font-semibold text-orange-400">so'm</span></p>
                    <p class="text-[11px] text-orange-500 font-semibold">{{ $allDebtSales->count() }} ta to'lanmagan qarz</p>
                    @if($overdueNasiya > 0)
                    <div class="mt-1 flex items-center gap-1.5 bg-red-50 border border-red-100 rounded-lg px-3 py-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-500 shrink-0 overdue-pulse"></span>
                        <span class="text-[10px] font-bold text-red-600">{{ number_format($overdueNasiya,0,',',' ') }} so'm muddati o'tgan</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- TAB 2: NASIYA                                                     --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div x-show="activeTab==='nasiya'" x-cloak class="tab-panel">

        {{-- Summary Stats Bar --}}
        @if(!$allDebtSales->isEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-5">
            <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4">
                <div class="flex items-center gap-2 mb-1.5">
                    <div class="w-6 h-6 rounded-lg bg-emerald-100 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <p class="text-[11px] font-bold text-emerald-600 uppercase tracking-wide">Bu oy yig'ildi</p>
                </div>
                <p class="text-xl font-black text-emerald-700">{{ number_format($totalCollectedDebt,0,',',' ') }} <span class="text-sm font-bold opacity-70">so'm</span></p>
            </div>
            <div class="bg-orange-50 border border-orange-200 rounded-2xl p-4">
                <div class="flex items-center gap-2 mb-1.5">
                    <div class="w-6 h-6 rounded-lg bg-orange-100 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="text-[11px] font-bold text-orange-600 uppercase tracking-wide">Hali kutilayotgan</p>
                </div>
                <p class="text-xl font-black text-orange-700">{{ number_format($totalNasiya,0,',',' ') }} <span class="text-sm font-bold opacity-70">so'm</span></p>
            </div>
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4">
                <div class="flex items-center gap-2 mb-1.5">
                    <div class="w-6 h-6 rounded-lg bg-slate-200 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wide">Eng katta qarzdor</p>
                </div>
                @if($biggestDebtor)
                <p class="text-base font-black text-slate-800 truncate leading-tight">{{ $biggestDebtor->customer->name ?? 'Noma\'lum' }}</p>
                <p class="text-xs font-bold text-slate-500 mt-0.5">{{ number_format($biggestDebtor->remaining_debt,0,',',' ') }} so'm</p>
                @else
                <p class="text-xl font-black text-slate-400">—</p>
                @endif
            </div>
        </div>
        @endif

        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2.5">
                <div class="w-1 h-5 bg-gradient-to-b from-amber-400 to-orange-500 rounded-full"></div>
                <h2 class="font-bold text-slate-800">Nasiya va Qarzlar</h2>
                <span class="px-2 py-0.5 bg-amber-50 text-amber-700 font-bold text-xs rounded-lg border border-amber-200">{{ $allDebtSales->count() }} ta faol</span>
            </div>
            <div class="flex gap-2">
                <button @click="debtFilter='all'"
                    :class="debtFilter==='all' ? 'bg-slate-800 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'"
                    class="text-xs font-bold px-3 py-1.5 rounded-lg transition-all">Barchasi</button>
                <button @click="debtFilter='overdue'"
                    :class="debtFilter==='overdue' ? 'bg-red-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-red-50 hover:text-red-700'"
                    class="text-xs font-bold px-3 py-1.5 rounded-lg transition-all flex items-center gap-1.5">
                    @if($overdueNasiya > 0)<span class="w-2 h-2 rounded-full bg-red-500" x-show="debtFilter!=='overdue'"></span>@endif
                    Muddati o'tgan
                </button>
            </div>
        </div>

        @if($allDebtSales->isEmpty())
        <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-10 flex flex-col items-center text-center">
            <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center mb-3">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="font-bold text-emerald-800">Barcha qarzlar to'langan!</h3>
            <p class="text-sm text-emerald-600 mt-1">Hech qanday faol nasiya qarz yo'q.</p>
        </div>
        @else
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-400 uppercase tracking-widest">
                            <th class="px-3 md:px-5 py-3">Mijoz</th>
                            <th class="px-3 md:px-5 py-3 text-right hidden sm:table-cell">Jami</th>
                            <th class="px-3 md:px-5 py-3 text-right hidden sm:table-cell">To'langan</th>
                            <th class="px-3 md:px-5 py-3 text-right">Qolgan</th>
                            <th class="px-3 md:px-5 py-3 text-center">Muddat</th>
                            <th class="px-3 md:px-5 py-3 text-center hidden sm:table-cell">Holat</th>
                            <th class="px-3 md:px-5 py-3 text-right">Amal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allDebtSales as $debtSale)
                        @php
                            $isOverdue = $debtSale->due_date && $debtSale->due_date->lt($today);
                            $isPartial = $debtSale->status === 'partial';
                            $remaining = $debtSale->remaining_debt;
                            $rowFilter = $isOverdue ? 'true' : 'false';
                            $paidPct   = $debtSale->total_price > 0 ? min(100, round(($debtSale->paid_amount / $debtSale->total_price) * 100)) : 0;
                        @endphp
                        {{-- Main debt row --}}
                        <tr x-show="debtFilter==='all' || (debtFilter==='overdue' && {{ $rowFilter }})"
                            class="border-b border-slate-100 transition-colors hover:bg-slate-50/60 {{ $isOverdue ? 'debt-row-overdue' : ($isPartial ? 'debt-row-partial' : '') }}">
                            <td class="px-3 md:px-5 py-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-2 h-2 rounded-full shrink-0 {{ $isOverdue ? 'bg-red-500 overdue-pulse' : ($isPartial ? 'bg-amber-400' : 'bg-orange-400') }}"></div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800">{{ $debtSale->customer->name ?? 'Noma\'lum' }}</p>
                                        <p class="text-[11px] text-slate-400">{{ \Carbon\Carbon::parse($debtSale->sale_date)->format('d.m.Y') }} • SA-{{ str_pad($debtSale->id,4,'0',STR_PAD_LEFT) }}</p>
                                        @if($paidPct > 0)
                                        <div class="mt-1.5 h-1.5 w-24 bg-slate-200 rounded-full overflow-hidden">
                                            <div class="h-full rounded-full {{ $paidPct >= 100 ? 'bg-emerald-500' : 'bg-amber-400' }}" style="width:{{ $paidPct }}%"></div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 md:px-5 py-3 text-right text-sm font-semibold text-slate-700 hidden sm:table-cell">{{ number_format($debtSale->total_price,0,',',' ') }} so'm</td>
                            <td class="px-3 md:px-5 py-3 text-right text-sm font-semibold {{ $debtSale->paid_amount > 0 ? 'text-emerald-600' : 'text-slate-400' }} hidden sm:table-cell">{{ number_format($debtSale->paid_amount,0,',',' ') }} so'm</td>
                            <td class="px-3 md:px-5 py-3 text-right text-sm font-black {{ $isOverdue ? 'text-red-600' : 'text-orange-600' }}">{{ number_format($remaining,0,',',' ') }} so'm</td>
                            <td class="px-3 md:px-5 py-3 text-center">
                                @if($debtSale->due_date)
                                <span class="inline-flex items-center gap-1 px-1.5 md:px-2 py-1 rounded-lg text-[11px] md:text-xs font-bold {{ $isOverdue ? 'bg-red-100 text-red-700 border border-red-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                    @if($isOverdue)<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>@endif
                                    {{ \Carbon\Carbon::parse($debtSale->due_date)->format('d.m.Y') }}
                                </span>
                                @else
                                <span class="text-xs text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-3 md:px-5 py-3 text-center hidden sm:table-cell">
                                @if($isOverdue)
                                <span class="px-2 py-1 rounded-lg text-xs font-bold bg-red-100 text-red-700 border border-red-200">Muddati o'tgan</span>
                                @elseif($isPartial)
                                <span class="px-2 py-1 rounded-lg text-xs font-bold bg-yellow-100 text-yellow-700 border border-yellow-200">Qisman to'langan</span>
                                @else
                                <span class="px-2 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-500 border border-slate-200">Kutilmoqda</span>
                                @endif
                            </td>
                            <td class="px-3 md:px-5 py-3 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if($debtSale->payments->isNotEmpty())
                                    <button @click="openHistoryId = (openHistoryId === {{ $debtSale->id }} ? null : {{ $debtSale->id }})"
                                        :class="openHistoryId === {{ $debtSale->id }} ? 'bg-indigo-100 text-indigo-700 border-indigo-200' : 'bg-white text-slate-500 border-slate-200 hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200'"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-bold border rounded-xl transition-all"
                                        title="To'lov tarixini ko'rish">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                        {{ $debtSale->payments->count() }}
                                    </button>
                                    @endif
                                    <button @click="openPayModal({{ $debtSale->id }}, '{{ addslashes($debtSale->customer->name ?? 'Noma\'lum') }}', {{ $remaining }}, {{ $debtSale->total_price }})"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-600 hover:text-white border border-emerald-200 hover:border-emerald-600 rounded-xl transition-all">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        To'lov
                                    </button>
                                </div>
                            </td>
                        </tr>
                        {{-- Expandable payment history row --}}
                        <tr x-show="openHistoryId === {{ $debtSale->id }}" x-cloak
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="border-b border-indigo-100 bg-indigo-50/40">
                            <td colspan="7" class="px-8 py-4">
                                <div class="flex items-center gap-2 mb-3">
                                    <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    <p class="text-[11px] font-black text-indigo-700 uppercase tracking-wide">To'lov tarixi — {{ $debtSale->customer->name ?? 'Noma\'lum' }}</p>
                                </div>
                                @if($debtSale->payments->isEmpty())
                                <p class="text-sm text-slate-400 italic">Hali to'lov amalga oshirilmagan.</p>
                                @else
                                @php $runningBal = (float)$debtSale->total_price; @endphp
                                <table class="w-full max-w-xl text-sm">
                                    <thead>
                                        <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-wide border-b border-indigo-200/60">
                                            <th class="text-left pb-2 pr-6">Sana</th>
                                            <th class="text-right pb-2 pr-6">To'landi</th>
                                            <th class="text-right pb-2 pr-6">Qolgan qarz</th>
                                            <th class="text-left pb-2">Izoh</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-indigo-100/60">
                                        @foreach($debtSale->payments->sortBy('payment_date') as $payment)
                                        @php $runningBal = max(0, $runningBal - (float)$payment->amount); @endphp
                                        <tr>
                                            <td class="py-1.5 pr-6 font-medium text-slate-600">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d.m.Y') }}</td>
                                            <td class="py-1.5 pr-6 text-right font-bold text-emerald-600">+{{ number_format($payment->amount,0,',',' ') }} so'm</td>
                                            <td class="py-1.5 pr-6 text-right font-bold {{ $runningBal > 0 ? 'text-orange-600' : 'text-emerald-600' }}">{{ number_format($runningBal,0,',',' ') }} so'm</td>
                                            <td class="py-1.5 text-slate-400 text-xs">{{ $payment->notes ?? '—' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50 border-t-2 border-slate-200">
                        <tr>
                            <td colspan="7" class="px-3 md:px-5 py-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wide">Jami qolgan qarz</span>
                                    <span class="font-black text-orange-600">{{ number_format($totalNasiya,0,',',' ') }} so'm</span>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- TAB 3: MAHSULOTLAR                                                --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div x-show="activeTab==='products'" x-cloak class="space-y-5 tab-panel">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            {{-- Top 5 foydali mahsulotlar --}}
            <div class="lg:col-span-2">
                <div class="flex items-center gap-2.5 mb-4">
                    <div class="w-1 h-5 bg-gradient-to-b from-violet-500 to-purple-600 rounded-full"></div>
                    <h2 class="font-bold text-slate-800">Top 5 Foydali Mahsulot</h2>
                    <span class="text-xs text-slate-400">davr bo'yicha</span>
                </div>
                @if(count($topProfitProducts) > 0)
                <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm divide-y divide-slate-100">
                    @foreach($topProfitProducts as $i => $prod)
                    @php $rank = $i + 1; @endphp
                    <div class="flex items-center gap-4 px-5 py-3.5 hover:bg-slate-50/50 transition-colors">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 text-sm font-black
                            {{ $rank===1 ? 'bg-amber-100 text-amber-700' : ($rank===2 ? 'bg-slate-200 text-slate-600' : ($rank===3 ? 'bg-orange-100 text-orange-700' : 'bg-slate-100 text-slate-500')) }}">
                            {{ $rank }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-slate-800 truncate">{{ $prod['product_name'] }}</p>
                            <p class="text-xs text-slate-400">{{ number_format($prod['total_sold'],0) }} dona • {{ number_format($prod['total_revenue'],0,',',' ') }} so'm tushum</p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-sm font-black {{ $prod['total_profit'] >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                                {{ $prod['total_profit'] >= 0 ? '+' : '' }}{{ number_format($prod['total_profit'],0,',',' ') }} so'm
                            </p>
                            <p class="text-[10px] text-slate-400">sof foyda</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-8 text-center text-sm text-slate-400">Tanlangan davr uchun ma'lumot yo'q</div>
                @endif
            </div>

            {{-- Ombor Qiymati --}}
            <div>
                <div class="flex items-center gap-2.5 mb-4">
                    <div class="w-1 h-5 bg-gradient-to-b from-cyan-500 to-blue-500 rounded-full"></div>
                    <h2 class="font-bold text-slate-800">Ombor Qiymati</h2>
                </div>
                <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl p-5 text-white shadow-xl">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">Tannarx bo'yicha</p>
                    <p class="text-3xl font-black text-white">{{ number_format($inventoryValue,0,',',' ') }}</p>
                    <p class="text-xs text-slate-400 mb-4">so'm • {{ $inventoryCount }} xil mahsulot</p>
                    @if($inventoryItems->count() > 0)
                    <div class="space-y-2 border-t border-white/10 pt-4">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Eng qimmat zaxiralar</p>
                        @foreach($inventoryItems as $invItem)
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-slate-300 truncate max-w-[60%]">{{ $invItem->name }}</span>
                            <span class="text-xs font-bold text-white">{{ number_format($invItem->cost_price * $invItem->quantity,0,',',' ') }} so'm</span>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Barcha mahsulotlar grid --}}
        <div>
            <div class="flex items-center gap-2.5 mb-4">
                <div class="w-1 h-5 bg-gradient-to-b from-indigo-500 to-purple-500 rounded-full"></div>
                <h2 class="font-bold text-slate-800">Barcha mahsulotlar kesimida</h2>
                <span class="text-xs text-slate-400">{{ count($groupedProducts) }} ta</span>
            </div>
            @if(count($groupedProducts) > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                @foreach($groupedProducts as $stat)
                <div class="bg-white border border-slate-200/60 rounded-2xl p-4 hover:shadow-md hover:border-blue-200 transition-all flex flex-col">
                    <p class="text-sm font-bold text-slate-800 truncate mb-0.5" title="{{ $stat['product_name'] }}">{{ $stat['product_name'] }}</p>
                    <p class="text-xs text-slate-400 mb-3">{{ number_format($stat['total_sold'],0) }} dona sotildi</p>
                    <div class="space-y-1 mt-auto pt-3 border-t border-slate-100">
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] font-bold text-slate-400 uppercase">Tushum</span>
                            <span class="text-xs font-black text-emerald-600">{{ number_format($stat['total_revenue'],0,',',' ') }}</span>
                        </div>
                        @if($stat['total_cost'] > 0)
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] font-bold text-slate-400 uppercase">Tannarx</span>
                            <span class="text-xs font-semibold text-orange-500">{{ number_format($stat['total_cost'],0,',',' ') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] font-bold text-slate-400 uppercase">Foyda</span>
                            <span class="text-xs font-black {{ $stat['total_profit'] >= 0 ? 'text-blue-600' : 'text-red-500' }}">
                                {{ $stat['total_profit'] >= 0 ? '+' : '' }}{{ number_format($stat['total_profit'],0,',',' ') }}
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-10 text-center text-slate-400">Bu davr uchun mahsulot ma'lumotlari yo'q</div>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- TAB 4: SAVDOLAR                                                   --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div x-show="activeTab==='sales'" x-cloak class="tab-panel">
        <div class="flex items-center gap-2.5 mb-4">
            <div class="w-1 h-5 bg-gradient-to-b from-teal-400 to-emerald-500 rounded-full"></div>
            <h2 class="font-bold text-slate-800">Batafsil Savdolar</h2>
            <span class="px-2 py-0.5 bg-slate-100 text-slate-600 font-bold text-xs rounded-lg">{{ count($sales) }} ta savdo</span>
        </div>

        @if(count($sales) > 0)
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-400 uppercase tracking-widest">
                            <th class="px-5 py-3.5">Xaridor & Sana</th>
                            <th class="px-5 py-3.5">Mahsulot</th>
                            <th class="px-5 py-3.5 text-center">Dona</th>
                            <th class="px-5 py-3.5 text-center">To'lov</th>
                            <th class="px-5 py-3.5 text-right">Narx</th>
                            <th class="px-5 py-3.5 text-right">Summa</th>
                            <th class="px-5 py-3.5 text-right"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100/60">
                        @foreach($sales as $sale)
                            @foreach($sale->items as $idx => $item)
                            <tr class="hover:bg-blue-50/30 transition-colors">
                                <td class="px-5 py-2.5 min-w-[175px] align-top">
                                    @if($idx === 0)
                                    <div class="flex items-start gap-2.5 mt-0.5">
                                        <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-indigo-100 to-blue-50 border border-blue-100 flex items-center justify-center shrink-0">
                                            <span class="text-xs font-black text-blue-700">{{ mb_strtoupper(mb_substr($sale->customer->name ?? 'U',0,1)) }}</span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-800 leading-tight">{{ $sale->customer->name ?? 'Umumiy xaridor' }}</p>
                                            <p class="text-[11px] text-slate-400">{{ \Carbon\Carbon::parse($sale->sale_date)->format('d.m.Y') }}</p>
                                        </div>
                                    </div>
                                    @else
                                    <div class="pl-11 h-full flex items-center"><div class="w-px h-6 bg-slate-200"></div></div>
                                    @endif
                                </td>
                                <td class="px-5 py-2.5">
                                    <span class="text-sm font-semibold {{ $item->product ? 'text-slate-700' : 'text-slate-400 italic' }}">{{ $item->product->name ?? 'O\'chirilgan' }}</span>
                                </td>
                                <td class="px-5 py-2.5 text-center">
                                    <span class="inline-flex items-center justify-center px-2 py-0.5 min-w-[2.5rem] bg-indigo-50 border border-indigo-100 text-indigo-700 font-bold text-xs rounded-lg">{{ $item->quantity }}</span>
                                </td>
                                <td class="px-5 py-2.5 text-center align-top">
                                    @if($idx === 0)
                                    @php
                                        $pm = $sale->payment_method ?? 'naqd';
                                        $pmLabel = $pm==='nasiya' ? '📋 Nasiya' : ($pm==='karta' ? '💳 Karta' : '💵 Naqd');
                                        $pmClass = $pm==='nasiya' ? 'bg-orange-50 text-orange-700 border-orange-200' : ($pm==='karta' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200');
                                    @endphp
                                    <span class="inline-flex px-2 py-0.5 rounded-lg border text-xs font-bold {{ $pmClass }}">{{ $pmLabel }}</span>
                                    @endif
                                </td>
                                <td class="px-5 py-2.5 text-right text-sm text-slate-500">{{ number_format($item->unit_price,0,',',' ') }}</td>
                                <td class="px-5 py-2.5 text-right text-sm font-black text-emerald-600">{{ number_format($item->unit_price * $item->quantity,0,',',' ') }}</td>
                                <td class="px-5 py-2.5 text-right align-top">
                                    @if($idx === 0)
                                    <div class="flex flex-col gap-1">
                                        <button onclick="printSaleReceipt({{ $sale->id }})"
                                            title="Chek chiqarish"
                                            class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-slate-400 bg-white border border-slate-200 hover:text-blue-600 hover:bg-blue-50 hover:border-blue-200 transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                        </button>
                                        <button @click="confirmDelete({{ $sale->id }})"
                                            title="O'chirish"
                                            class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-slate-400 bg-white border border-slate-200 hover:text-red-600 hover:bg-red-50 hover:border-red-200 transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                            <tr class="bg-slate-50/60 border-b-2 border-slate-200/80">
                                <td colspan="5" class="px-5 py-2">
                                    <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-widest pl-11">
                                        #{{ $sale->id }} • {{ $sale->items->count() }} xil • {{ $sale->items->sum('quantity') }} dona
                                        @if($sale->payment_method==='nasiya' && $sale->status !== 'paid')
                                        • <span class="text-orange-500">Qolgan: {{ number_format($sale->remaining_debt,0,',',' ') }} so'm</span>
                                        @endif
                                    </p>
                                </td>
                                <td class="px-5 py-2 text-right font-black text-slate-700 text-sm">{{ number_format($sale->total_price,0,',',' ') }}</td>
                                <td></td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white">
                        <tr>
                            <td colspan="5" class="px-5 py-4 text-sm font-bold opacity-80 uppercase tracking-wider">Umumiy yig'indi davr uchun</td>
                            <td class="px-5 py-4 text-right text-xl font-black">{{ number_format($totalRevenue,0,',',' ') }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        @else
        <div class="bg-slate-50 border border-slate-200 rounded-2xl p-12 text-center text-slate-400 font-medium">Bu davr oralig'ida savdolar yo'q.</div>
        @endif
    </div>

</main>

{{-- ══ QARZNI YOPISH MODALI ═══════════════════════════════════════════ --}}
<div x-show="payModal.open" x-cloak
     class="fixed inset-0 z-[200] flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm"
     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    <div class="absolute inset-0" @click="if(!payModal.loading) payModal.open = false"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-md overflow-hidden z-10" @click.stop
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-90 translate-y-6" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">
        <div class="bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-5 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-white/20 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black leading-none">Qarzni yopish</h3>
                        <p class="text-emerald-100 text-sm mt-0.5" x-text="payModal.customerName"></p>
                    </div>
                </div>
                <button @click="payModal.open = false" :disabled="payModal.loading" class="p-2 rounded-xl text-emerald-100 hover:bg-white/20 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
        <div class="p-6 space-y-5">
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-slate-50 rounded-xl p-3 border border-slate-200">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Jami qarz</p>
                    <p class="text-base font-black text-slate-800" x-text="formatMoney(payModal.totalPrice) + ' so\'m'"></p>
                </div>
                <div class="bg-red-50 rounded-xl p-3 border border-red-100">
                    <p class="text-[10px] font-bold text-red-400 uppercase tracking-wide mb-1">Qolgan</p>
                    <p class="text-base font-black text-red-700" x-text="formatMoney(payModal.remaining) + ' so\'m'"></p>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-2">To'lov miqdori <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="number" x-model="payModal.amount"
                        :max="payModal.remaining" min="0.01" step="1"
                        @input="if(parseFloat($event.target.value) > payModal.remaining) payModal.amount = payModal.remaining"
                        placeholder="Miqdorni kiriting..."
                        class="w-full px-4 py-3 text-base font-bold border-2 border-slate-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 rounded-xl outline-none transition-all text-slate-800">
                    <button @click="payModal.amount = payModal.remaining"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-emerald-600 bg-emerald-50 hover:bg-emerald-100 px-2 py-1 rounded-lg transition-colors">
                        To'liq
                    </button>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-2">To'lov sanasi</label>
                <input type="date" x-model="payModal.paymentDate"
                    class="w-full px-4 py-2.5 text-sm font-medium border-2 border-slate-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 rounded-xl outline-none transition-all text-slate-700 [color-scheme:light]">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-2">Izoh (ixtiyoriy)</label>
                <input type="text" x-model="payModal.notes" maxlength="255" placeholder="Masalan: Naqd to'lov, 1-qism..."
                    class="w-full px-4 py-2.5 text-sm border-2 border-slate-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 rounded-xl outline-none transition-all text-slate-700">
            </div>
        </div>
        <div class="flex gap-3 px-6 pb-6">
            <button @click="payModal.open = false" :disabled="payModal.loading"
                class="flex-1 px-4 py-3 text-sm font-bold text-slate-600 bg-white border-2 border-slate-200 rounded-xl hover:bg-slate-50 transition-all disabled:opacity-50">
                Bekor
            </button>
            <button @click="submitPayment()" :disabled="payModal.loading || !payModal.amount || parseFloat(payModal.amount) <= 0"
                class="flex-1 px-4 py-3 text-sm font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 rounded-xl transition-all disabled:from-slate-300 disabled:to-slate-300 disabled:text-slate-400 disabled:cursor-not-allowed shadow-lg flex items-center justify-center gap-2">
                <svg x-show="payModal.loading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                <svg x-show="!payModal.loading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <span x-text="payModal.loading ? 'Saqlanmoqda...' : 'To\'lovni tasdiqlash'"></span>
            </button>
        </div>
    </div>
</div>

{{-- ══ SAVDONI O'CHIRISH MODALI ════════════════════════════════════════ --}}
<div x-show="deleteId" x-cloak
     class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm"
     x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    <div @click.outside="if(!isDeleting) deleteId = null"
         class="bg-white rounded-3xl shadow-2xl w-full max-w-sm overflow-hidden"
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-90 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-90 translate-y-4">
        <div class="p-6 text-center relative overflow-hidden">
            <div class="absolute inset-0 bg-red-50/50"></div>
            <div class="w-16 h-16 rounded-full bg-red-100 border-4 border-white shadow-sm flex items-center justify-center mx-auto mb-4 relative z-10 text-red-500">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 class="text-lg font-black text-slate-800 mb-2 relative z-10">Savdoni o'chirish!</h3>
            <p class="text-sm font-medium text-slate-500 relative z-10 leading-relaxed px-2">Rostdan ham ushbu savdoni o'chirmoqchimisiz? Mahsulotlar omborga qaytariladi.</p>
        </div>
        <div class="p-5 flex gap-3 bg-slate-50 border-t border-slate-100">
            <button @click="deleteId = null" :disabled="isDeleting"
                    class="flex-1 px-4 py-3 text-sm font-bold text-slate-600 bg-white border border-slate-200 shadow-sm rounded-xl hover:bg-slate-50 transition-all">
                Bekor qilish
            </button>
            <button @click="executeDelete()" :disabled="isDeleting"
                    class="flex-1 px-4 py-3 text-sm font-bold text-white bg-red-600 shadow-md shadow-red-600/20 rounded-xl hover:bg-red-700 disabled:opacity-70 flex items-center justify-center gap-2 transition-all">
                <svg x-show="isDeleting" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                <span x-text="isDeleting ? 'O\'chirilmoqda' : 'Ha, o\'chirish'"></span>
            </button>
        </div>
    </div>
</div>

{{-- ══ KUNNI O'CHIRISH MODALI ══════════════════════════════════════════ --}}
<div x-show="clearDayOpen" x-cloak
     class="fixed inset-0 z-[200] flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-sm"
     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden border-2 border-red-200" @click.stop
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-90 translate-y-6" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">
        <div class="bg-gradient-to-r from-red-600 to-rose-600 px-6 py-5 text-white relative overflow-hidden">
            <div class="absolute inset-0 opacity-20" style="background-image: repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255,255,255,0.1) 10px, rgba(255,255,255,0.1) 20px);"></div>
            <div class="relative flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center shrink-0 border-2 border-white/30">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                </div>
                <div>
                    <h3 class="text-xl font-black leading-tight">XAVFLI AMAL!</h3>
                    <p class="text-red-100 text-sm font-medium mt-0.5">Kunlik savdolarni to'liq o'chirish</p>
                </div>
            </div>
        </div>
        <div class="p-6 space-y-5">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-2">O'chirmoqchi bo'lgan sanani tanlang:</label>
                <input type="date" x-model="clearDayDate"
                    :max="new Date().toISOString().split('T')[0]"
                    class="w-full px-4 py-3 text-base font-bold border-2 border-red-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-400/30 focus:border-red-400 text-slate-700 bg-red-50/30 [color-scheme:light]">
            </div>
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 space-y-2">
                <p class="text-xs font-black text-amber-800 uppercase tracking-wide flex items-center gap-1.5">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Bu amal nimani o'chiradi?
                </p>
                <ul class="space-y-1.5 text-xs font-medium text-amber-700">
                    <li class="flex items-start gap-2"><span class="w-1.5 h-1.5 rounded-full bg-amber-500 mt-1 shrink-0"></span>Tanlangan sanadagi <strong>barcha savdolar</strong> o'chiriladi</li>
                    <li class="flex items-start gap-2"><span class="w-1.5 h-1.5 rounded-full bg-amber-500 mt-1 shrink-0"></span>Mahsulotlar <strong>omborga qaytariladi</strong></li>
                    <li class="flex items-start gap-2"><span class="w-1.5 h-1.5 rounded-full bg-red-500 mt-1 shrink-0"></span>Bu amal <strong class="text-red-700">qaytarib bo'lmaydi!</strong></li>
                </ul>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-2">
                    Davom etish uchun yozing: <span class="text-red-600 font-black ml-1 tracking-widest">HA, O'CHIRAMAN</span>
                </label>
                <input type="text" x-model="clearDayConfirmText" placeholder="HA, O'CHIRAMAN"
                    class="w-full px-4 py-2.5 text-sm font-bold border-2 rounded-xl focus:outline-none focus:ring-2 transition-all"
                    :class="clearDayConfirmText === 'HA, O\'CHIRAMAN'
                        ? 'border-red-500 ring-red-200 bg-red-50 text-red-700'
                        : 'border-slate-200 ring-transparent bg-slate-50 text-slate-700'">
            </div>
        </div>
        <div class="px-6 pb-6 flex gap-3">
            <button @click="clearDayOpen = false; clearDayDate = ''; clearDayConfirmText = ''"
                :disabled="clearDayLoading"
                class="flex-1 px-4 py-3 text-sm font-bold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-all disabled:opacity-50">
                Bekor qilish
            </button>
            <button @click="executeClearDay()"
                :disabled="clearDayConfirmText !== 'HA, O\'CHIRAMAN' || !clearDayDate || clearDayLoading"
                class="flex-1 px-4 py-3 text-sm font-black text-white bg-gradient-to-r from-red-600 to-rose-600 rounded-xl transition-all
                       disabled:from-slate-300 disabled:to-slate-300 disabled:text-slate-400 disabled:cursor-not-allowed
                       enabled:hover:from-red-700 enabled:hover:to-rose-700 enabled:shadow-lg enabled:shadow-red-500/40
                       flex items-center justify-center gap-2">
                <svg x-show="clearDayLoading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                <svg x-show="!clearDayLoading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                <span x-text="clearDayLoading ? 'O\'chirilmoqda...' : 'O\'CHIRISH'"></span>
            </button>
        </div>
    </div>
</div>

</div>
@endsection

@section('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('reportsApp', () => ({
        activeTab: 'overview',
        debtFilter: 'all',
        openHistoryId: null,
        deleteId: null,
        isDeleting: false,
        clearDayOpen: false,
        clearDayDate: '',
        clearDayConfirmText: '',
        clearDayLoading: false,

        payModal: {
            open: false, saleId: null, customerName: '',
            remaining: 0, totalPrice: 0, amount: '',
            paymentDate: new Date().toISOString().split('T')[0],
            notes: '', loading: false,
        },

        openPayModal(saleId, customerName, remaining, totalPrice) {
            this.payModal = {
                open: true, saleId, customerName,
                remaining: parseFloat(remaining),
                totalPrice: parseFloat(totalPrice),
                amount: '', paymentDate: new Date().toISOString().split('T')[0],
                notes: '', loading: false,
            };
        },

        async submitPayment() {
            const amount = parseFloat(this.payModal.amount);
            if (!amount || amount <= 0) { alert("To'lov miqdorini to'g'ri kiriting!"); return; }
            this.payModal.loading = true;
            try {
                const resp = await fetch(`/sales/${this.payModal.saleId}/pay`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({ amount: this.payModal.amount, payment_date: this.payModal.paymentDate, notes: this.payModal.notes || null }),
                });
                const data = await resp.json();
                if (data.success) {
                    this.payModal.open = false;
                    this.showNotif('✓ ' + data.message, 'success');
                    setTimeout(() => window.location.reload(), 800);
                } else { alert(data.message || 'Xatolik yuz berdi!'); }
            } catch (e) { alert('Xatolik: ' + e.message); }
            finally { this.payModal.loading = false; }
        },

        openClearDay() {
            this.clearDayDate = new Date().toISOString().split('T')[0];
            this.clearDayConfirmText = '';
            this.clearDayOpen = true;
        },

        async executeClearDay() {
            if (this.clearDayConfirmText !== "HA, O'CHIRAMAN" || !this.clearDayDate) return;
            this.clearDayLoading = true;
            try {
                const response = await fetch('/reports/clear-day', {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                    body: JSON.stringify({ date: this.clearDayDate })
                });
                const data = await response.json();
                if (data.success) { this.clearDayOpen = false; this.clearDayDate = ''; this.clearDayConfirmText = ''; window.location.reload(); }
                else { alert(data.message || 'Xatolik yuz berdi!'); }
            } catch (error) { alert('Xatolik: ' + error.message); }
            finally { this.clearDayLoading = false; }
        },

        confirmDelete(id) { this.deleteId = id; },

        async executeDelete() {
            if (!this.deleteId) return;
            this.isDeleting = true;
            try {
                const response = await fetch(`/sales/${this.deleteId}`, {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
                });
                const data = await response.json();
                if (data.success) { this.deleteId = null; window.location.reload(); }
                else { alert(data.message || 'Xatolik!'); }
            } catch (error) { alert('Xatolik: ' + error.message); }
            finally { this.isDeleting = false; }
        },

        formatMoney(n) { return Number(n || 0).toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ' '); },

        showNotif(msg, type) {
            const isSuccess = type === 'success';
            const el = document.createElement('div');
            el.className = `fixed bottom-6 right-6 px-6 py-4 rounded-xl border shadow-2xl text-sm font-bold z-[9999] transition-all duration-500 transform translate-y-24 opacity-0 flex items-center gap-3 min-w-[300px] ${isSuccess ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-red-50 border-red-200 text-red-800'}`;
            el.innerHTML = isSuccess
                ? `<svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg><span>${msg}</span>`
                : `<svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg><span>${msg}</span>`;
            document.body.appendChild(el);
            setTimeout(() => el.classList.remove('translate-y-24', 'opacity-0'), 10);
            setTimeout(() => { el.classList.add('translate-y-24', 'opacity-0'); setTimeout(() => el.remove(), 500); }, 4000);
        },
    }));
});

function printSaleReceipt(saleId) {
    const old = document.getElementById('report-receipt-iframe');
    if (old) old.remove();
    const iframe = document.createElement('iframe');
    iframe.id = 'report-receipt-iframe';
    iframe.style.cssText = 'position:fixed;top:-9999px;left:-9999px;width:0;height:0;border:none;';
    iframe.src = '/receipts/' + saleId;
    document.body.appendChild(iframe);
    iframe.onload = () => {
        try { iframe.contentWindow.focus(); iframe.contentWindow.print(); }
        catch(e) { window.open('/receipts/' + saleId, '_blank'); }
        setTimeout(() => { iframe.remove(); }, 3000);
    };
}
</script>
@endsection
