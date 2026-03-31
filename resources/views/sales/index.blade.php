@extends('layouts.app')
@section('title', 'Savdo — POS')

@section('head')
<style>
    /* Hide layout's default mobile top bar — POS has its own */
    @media (max-width: 767px) {
        .layout-mobile-topbar { display: none !important; }
    }

    .pos-left  { height: calc(100dvh - 3.5rem); overflow-y: auto; }
    .pos-right { height: calc(100dvh - 3.5rem); overflow-y: auto; }
    @media (min-width: 768px) {
        .pos-left  { height: calc(100dvh - 4rem); }
        .pos-right { height: calc(100dvh - 4rem); }
    }
    
    /* Smooth animations */
    @keyframes slideInCart { from { opacity: 0; transform: translateX(12px) scale(0.98); } to { opacity: 1; transform: translateX(0) scale(1); } }
    @keyframes fadeUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes pulseSoft { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.08); } }
    @keyframes bounceIn { 0% { transform: scale(0.9); opacity: 0; } 50% { transform: scale(1.1); } 100% { transform: scale(1); opacity: 1; } }
    @keyframes shimmer { 0% { background-position: -1000px 0; } 100% { background-position: 1000px 0; } }
    @keyframes glow { 0%, 100% { box-shadow: 0 0 10px rgba(59, 130, 246, 0.3); } 50% { box-shadow: 0 0 20px rgba(59, 130, 246, 0.6); } }
    
    .cart-item { animation: slideInCart 0.3s cubic-bezier(0.16, 1, 0.3, 1) both; }
    .animate-fade-up { animation: fadeUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) both; }
    .animate-pulse-soft.active { animation: pulseSoft 0.3s ease-out; }
    .qty-badge { animation: bounceIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
    
    /* Product card with gradient and smooth hover */
    .product-card { 
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1); 
        border: 1px solid rgba(226, 232, 240, 0.8);
        background: linear-gradient(135deg, rgba(255,255,255,1) 0%, rgba(248,250,252,0.5) 100%);
    }
    .product-card:hover { 
        transform: translateY(-6px); 
        box-shadow: 0 20px 40px -8px rgba(59, 130, 246, 0.2); 
        border-color: rgba(96, 165, 250, 0.5);
        background: linear-gradient(135deg, rgba(255,255,255,1) 0%, rgba(240, 249, 255, 0.8) 100%);
    }
    .product-card:active { transform: translateY(-2px) scale(0.99); }
    
    /* Stock indicator */
    .stock-badge {
        transition: all 0.3s ease;
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    }
    .stock-badge.low {
        background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
        animation: glow 2s infinite;
    }
    
    /* Qty buttons */
    .qty-btn { 
        transition: all 0.2s cubic-bezier(0.4, 0, 1, 1);
        border-radius: 0.5rem;
    }
    .qty-btn:active { transform: scale(0.92); }
    .qty-btn:disabled { opacity: 0.4; cursor: not-allowed; }
    
    /* Modal smooth transitions */
    .modal-overlay {
        backdrop-filter: blur(4px);
    }
    
    /* Cart showcase */
    .cart-glow {
        box-shadow: inset 0 0 20px rgba(59, 130, 246, 0.1);
    }
    
    /* Smooth color transitions */
    .color-transition {
        transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
    }

    [x-cloak] { display: none !important; }

    /* ── Mobile: cart becomes a slide-up bottom sheet ────────── */
    @media (max-width: 767px) {
        /* Product panel takes full width on mobile */
        .pos-left {
            width: 100% !important;
            max-width: 100% !important;
        }
        /* Extra bottom padding so content not hidden behind float bar + browser nav */
        .pos-product-grid {
            padding-bottom: calc(88px + env(safe-area-inset-bottom, 16px)) !important;
        }
        /* Cart becomes a fixed bottom sheet */
        .pos-right {
            position: fixed !important;
            left: 0; right: 0; bottom: 0;
            width: 100% !important;
            max-width: 100% !important;
            height: 92dvh !important;
            z-index: 250;
            border-radius: 1.25rem 1.25rem 0 0;
            box-shadow: 0 -8px 40px rgba(0,0,0,0.22);
            transform: translateY(100%);
            transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            overflow: hidden;
            display: flex !important;
            flex-direction: column !important;
        }
        .pos-right.mobile-open {
            transform: translateY(0);
        }
        /* drag handle */
        .pos-drag-handle { display: block; }
        /* prevent body scroll when cart is open */
        body.cart-open { overflow: hidden; }
        /* Sidebar stays on top of everything when open */
        aside { z-index: 600 !important; }
        /* All action modals must be above cart (z-250) */
        .modal-above-cart { z-index: 500 !important; }
    }
    @media (min-width: 768px) {
        .pos-right { transform: none !important; }
        .pos-drag-handle { display: none; }
        .pos-mobile-bar { display: none !important; }
    }
</style>
@endsection

@section('content')
<div x-data="posApp()" x-cloak>

    <!-- ── TOP BAR ─────────────────────────────────────────────── -->
    <div class="h-14 md:h-16 bg-gradient-to-r from-slate-50 to-blue-50 border-b border-slate-200 flex items-center justify-between px-4 md:px-6 shrink-0 z-10 sticky top-0">
        <div class="flex items-center gap-3">
            <h1 class="text-base md:text-lg font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">POS</h1>
            <span class="px-2.5 py-1 bg-gradient-to-r from-blue-50 to-indigo-50 text-blue-700 text-xs font-bold rounded-full border border-blue-200 transition-all duration-300 animate-pulse-soft"
                  :class="pulseCart ? 'active bg-blue-100 ring-2 ring-blue-300 shadow-lg shadow-blue-200' : ''"
                  x-text="cartCount + ' ta'"></span>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" @click="isHistoryOpen = true"
                class="p-2 md:px-4 md:py-2 bg-white hover:bg-slate-50 text-slate-700 text-sm font-bold rounded-xl transition-all flex items-center gap-2 border border-slate-200 shadow-sm">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="hidden md:inline">Hisobot</span>
            </button>
            {{-- Mobile: open sidebar for navigation --}}
            <button @click="$root.sidebarOpen = true"
               class="md:hidden p-2.5 bg-white hover:bg-slate-50 text-slate-500 active:scale-95 rounded-xl border border-slate-200 shadow-sm transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
    </div>

    <!-- ── POS VIEW ─────────────────────────────────────────────── -->
    <div class="flex flex-1 overflow-hidden h-[calc(100dvh-3.5rem)] md:h-[calc(100dvh-4rem)]">

        <!-- LEFT: Product panel -->
        <div class="flex-1 flex flex-col border-r border-slate-200 bg-gradient-to-b from-slate-50 to-slate-100 pos-left">

            <!-- Search -->
            <div class="p-4 bg-white border-b border-slate-200 sticky top-0 z-10 shadow-sm">
                <div class="relative">
                    <svg class="w-4 h-4 text-blue-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" x-model="search" placeholder="Mahsulot qidirish..."
                        class="w-full pl-9 pr-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 transition-all text-slate-700 placeholder-slate-400 shadow-sm">
                </div>
            </div>

            <!-- Product Grid -->
            <div class="pos-product-grid flex-1 overflow-y-auto p-3 md:p-4 pb-28 md:pb-4">
                <div class="grid grid-cols-2 xl:grid-cols-3 gap-2 md:gap-3">
                    <template x-for="(product, idx) in filteredProducts" :key="product.id">
                        <div class="product-card animate-fade-up rounded-xl p-3 md:p-4 flex flex-col gap-2 md:gap-3 cursor-pointer"
                             :style="`animation-delay: ${idx * 0.03}s`">
                            <!-- Name, narx, qoldiq -->
                            <div class="flex flex-col gap-1">
                                <p class="text-xs md:text-sm font-bold text-slate-800 leading-snug line-clamp-2" x-text="product.name"></p>
                                <p class="text-xs md:text-sm font-semibold text-blue-600"
                                   x-text="formatMoney(product.price) + ' so\'m / ' + (product.unit || 'dona')"></p>
                                <div class="flex items-center gap-1">
                                    <span class="text-[10px] font-medium text-slate-400">Ombor:</span>
                                    <span class="text-xs font-bold px-2 py-0.5 rounded-lg border stock-badge"
                                          :class="parseFloat(product.quantity) <= 0
                                            ? 'bg-red-50 text-red-600 border-red-300'
                                            : parseFloat(product.quantity) <= 5
                                              ? 'low bg-amber-50 text-amber-700 border-amber-300'
                                              : 'bg-emerald-50 text-emerald-700 border-emerald-200'"
                                          x-text="parseFloat(product.quantity) + ' ' + (product.unit || 'dona')"></span>
                                </div>
                            </div>

                            <!-- Counter -->
                            <div class="flex items-center gap-1.5">
                                <div class="flex items-center bg-slate-100 border border-slate-200 rounded-lg overflow-hidden flex-1 shadow-sm">
                                    <button @click="decrement(product.id)"
                                        class="qty-btn w-10 h-10 md:w-9 md:h-9 flex items-center justify-center text-slate-500 hover:bg-red-100 hover:text-red-600 font-bold color-transition shrink-0"
                                        :disabled="!cartQty(product.id)">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/></svg>
                                    </button>
                                    <input
                                        type="number"
                                        :inputmode="(product.unit === 'kg' || product.unit === 'litr') ? 'decimal' : 'numeric'"
                                        :step="(product.unit === 'kg' || product.unit === 'litr') ? '0.1' : '1'"
                                        min="0"
                                        :max="product.quantity"
                                        :value="cartQty(product.id) || ''"
                                        @input="debouncedSetQty(product, $event.target.value)"
                                        @focus="$event.target.select()"
                                        placeholder="—"
                                        class="flex-1 w-0 min-w-0 text-center text-sm font-bold text-slate-800 bg-transparent border-none outline-none py-1 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                    <button @click="increment(product)"
                                        class="qty-btn w-10 h-10 md:w-9 md:h-9 flex items-center justify-center text-slate-500 hover:bg-blue-100 hover:text-blue-600 font-bold color-transition shrink-0"
                                        :disabled="cartQty(product.id) >= product.quantity">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                    </button>
                                </div>
                                <button x-show="!cartQty(product.id)"
                                    @click="increment(product)"
                                    class="shrink-0 h-10 md:h-9 px-3 md:px-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 active:scale-95 text-white text-sm md:text-xs font-bold rounded-lg transition-all shadow-sm hover:shadow-blue-300">
                                    +
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Empty state -->
                <div x-show="filteredProducts.length === 0" class="flex flex-col items-center justify-center py-20 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-slate-200/50 border-2 border-dashed border-slate-300 flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-slate-500">Mahsulot topilmadi</p>
                </div>
            </div>
        </div>

        <!-- RIGHT: Cart sidebar / mobile bottom sheet -->
        <div class="w-80 xl:w-96 bg-gradient-to-b from-white to-slate-50 flex flex-col pos-right shrink-0 border-l border-slate-200"
             :class="showCartMobile ? 'mobile-open' : ''">

            <!-- Drag handle (mobile only) -->
            <div class="pos-drag-handle flex justify-center pt-3 pb-1 cursor-pointer bg-white" @click="showCartMobile = false">
                <div class="w-10 h-1.5 rounded-full bg-slate-300"></div>
            </div>

            <!-- Cart header -->
            <div class="px-5 py-3 md:py-4 border-b border-slate-200 flex items-center justify-between bg-white shadow-sm">
                <h2 class="font-bold text-slate-800 flex items-center gap-2 text-base md:text-lg">
                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zm10 0c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2zM7.17 14.5h11.66c.49 0 .95-.25 1.23-.64l3.57-5.95c.12-.22.2-.49.2-.8 0-1.1-.9-2-2-2H5.21l-.94-2H2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63z"/>
                    </svg>
                    Savatcha
                </h2>
                <div class="flex items-center gap-2">
                    <button x-show="cart.length > 0" @click="clearCart()"
                        class="text-xs text-slate-400 hover:text-red-600 transition-colors font-bold hover:bg-red-50 px-2 py-1 rounded-lg">
                        Tozalash
                    </button>
                    <!-- Mobile close button -->
                    <button class="md:hidden p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors"
                            @click="showCartMobile = false">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Cart items scroll area -->
            <div class="flex-1 overflow-y-auto p-4 cart-glow">
                <!-- Empty state -->
                <div x-show="cart.length === 0" class="flex flex-col items-center justify-center h-full pb-6 text-center gap-3">
                    <div class="w-24 h-24 rounded-3xl bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center shadow-inner">
                        <svg class="w-12 h-12 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-base font-bold text-slate-700">Savatcha bo'sh</p>
                        <p class="text-xs text-slate-400 mt-1">Chapdan mahsulot <span class="font-bold text-blue-500">+</span> tugmasini bosing</p>
                    </div>
                </div>

                <!-- Cart items -->
                <div class="space-y-2">
                    <template x-for="(item, idx) in cart" :key="item.id">
                        <div class="cart-item bg-white hover:bg-blue-50/50 border rounded-xl p-3 transition-all shadow-sm"
                             :class="flashCartId === item.id ? 'border-emerald-400 bg-emerald-50/60' : 'border-slate-200'"
                             :style="`animation-delay: ${idx * 0.05}s`">
                            <div class="flex items-start gap-2 justify-between">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-slate-800 leading-tight truncate" x-text="item.name"></p>
                                    <p class="text-xs text-slate-500 mt-0.5" x-text="formatMoney(item.price) + ' so\'m / ' + (item.unit || 'dona')"></p>
                                </div>
                                <div class="shrink-0 flex flex-col items-end gap-1.5">
                                    <p class="text-sm font-bold text-blue-700" x-text="formatMoney(item.price * item.qty) + ' so\'m'"></p>
                                    <!-- Mini stepper -->
                                    <div class="flex items-center bg-slate-100 border border-slate-200 rounded-xl overflow-hidden">
                                        <button @click="decrement(item.id)"
                                            class="w-9 h-9 flex items-center justify-center text-slate-400 hover:bg-red-100 hover:text-red-600 active:scale-90 transition-all text-xs font-bold">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/></svg>
                                        </button>
                                        <input
                                            type="number"
                                            :inputmode="(item.unit === 'kg' || item.unit === 'litr') ? 'decimal' : 'numeric'"
                                            :step="(item.unit === 'kg' || item.unit === 'litr') ? '0.1' : '1'"
                                            :min="(item.unit === 'kg' || item.unit === 'litr') ? '0.001' : '1'"
                                            :value="item.qty"
                                            @input="debouncedSetQtyById(item.id, $event.target.value)"
                                            @focus="$event.target.select()"
                                            class="w-10 text-center text-sm font-bold text-slate-800 bg-transparent border-none outline-none py-1 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                        <button @click="incrementById(item.id)"
                                            class="w-9 h-9 flex items-center justify-center text-slate-400 hover:bg-blue-100 hover:text-blue-600 active:scale-90 transition-all text-xs font-bold">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                        </button>
                                    </div>
                                    <button @click="removeFromCart(item.id)" class="text-xs text-slate-400 hover:text-red-500 transition-colors font-medium px-1 py-1">O'chirish</button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Cart footer -->
            <div class="border-t border-slate-200 p-5 space-y-4 bg-white">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-600 font-medium">Mahsulotlar jami:</span>
                        <span class="text-sm font-bold text-slate-800" x-text="cartCount + ' ta'"></span>
                    </div>
                    <div class="h-0.5 bg-gradient-to-r from-blue-300 to-indigo-300 rounded-full"></div>
                </div>
                <div class="flex items-center justify-between rounded-xl transition-all duration-300 px-2 -mx-2 py-1 -my-1"
                     :class="totalFlash ? 'bg-emerald-50 ring-1 ring-emerald-300' : ''">
                    <span class="text-sm font-bold text-slate-700">UMUMIY SUMMA:</span>
                    <span class="text-2xl font-black bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent" x-text="formatMoney(cartTotal) + ' so\'m'"></span>
                </div>

                <!-- To'lov usuli tugmalari -->
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">To'lov usuli:</p>
                    <div class="grid grid-cols-3 gap-2">
                        <button @click="sellForm.payment_method = 'naqd'"
                            :class="sellForm.payment_method === 'naqd'
                                ? 'bg-emerald-600 text-white border-emerald-600 shadow-md ring-2 ring-emerald-300'
                                : 'bg-white text-slate-600 border-slate-200 hover:border-emerald-400 hover:text-emerald-700'"
                            class="py-2.5 text-xs font-bold rounded-xl border transition-all flex flex-col items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Naqd
                        </button>
                        <button @click="sellForm.payment_method = 'karta'"
                            :class="sellForm.payment_method === 'karta'
                                ? 'bg-blue-600 text-white border-blue-600 shadow-md ring-2 ring-blue-300'
                                : 'bg-white text-slate-600 border-slate-200 hover:border-blue-400 hover:text-blue-700'"
                            class="py-2.5 text-xs font-bold rounded-xl border transition-all flex flex-col items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            Karta
                        </button>
                        <button @click="sellForm.payment_method = 'nasiya'"
                            :class="sellForm.payment_method === 'nasiya'
                                ? 'bg-orange-500 text-white border-orange-500 shadow-md ring-2 ring-orange-300'
                                : 'bg-white text-slate-600 border-slate-200 hover:border-orange-400 hover:text-orange-700'"
                            class="py-2.5 text-xs font-bold rounded-xl border transition-all flex flex-col items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            Nasiya
                        </button>
                    </div>
                </div>

                <button @click="openSellModal()"
                    :disabled="cart.length === 0 || !sellForm.payment_method"
                    class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 disabled:from-slate-300 disabled:to-slate-300 disabled:text-slate-500 disabled:cursor-not-allowed active:scale-[0.98] text-white font-bold py-4 md:py-3 rounded-2xl transition-all text-base md:text-sm flex items-center justify-center gap-2 shadow-lg hover:shadow-blue-400/40 disabled:shadow-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m7 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span x-text="sellForm.payment_method ? 'TASDIQLASH' : 'To\'lov usulini tanlang'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- ── MOBILE: Cart backdrop ───────────────────────────────── -->
    <div x-show="showCartMobile" style="display:none"
         class="md:hidden fixed inset-0 z-[240] bg-slate-900/50 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         @click="showCartMobile = false">
    </div>

    <!-- ── MOBILE: Floating cart button (only shown when cart has items) ── -->
    <div class="pos-mobile-bar fixed left-0 right-0 z-[200] md:hidden"
         style="bottom: env(safe-area-inset-bottom, 0px); padding: 0 12px 12px 12px;"
         x-show="cart.length > 0 && !showCartMobile"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-6 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95">
        <button @click="showCartMobile = true"
            class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 active:from-blue-700 active:to-indigo-700 text-white rounded-2xl shadow-2xl shadow-blue-500/40 active:shadow-none active:scale-[0.97] transition-all duration-150 overflow-hidden"
            style="min-height: 58px;">
            <div class="flex items-center justify-between px-5 py-3">
                <div class="flex items-center gap-3">
                    <div class="relative shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span class="absolute -top-2 -right-2 bg-white text-blue-600 text-[10px] font-black w-5 h-5 rounded-full flex items-center justify-center shadow"
                              x-text="cart.length"></span>
                    </div>
                    <div class="text-left">
                        <p class="text-[11px] text-blue-200 font-semibold leading-tight" x-text="cartCount + ' ta mahsulot'"></p>
                        <p class="text-lg font-black leading-tight" x-text="formatMoney(cartTotal) + ' so\'m'"></p>
                    </div>
                </div>
                <div class="flex items-center gap-1.5 bg-white/20 rounded-xl px-3 py-2">
                    <span class="text-sm font-bold">Savatcha</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/>
                    </svg>
                </div>
            </div>
        </button>
    </div>

    <!-- ── HISTORY/REPORT MODAL ────────────────────────────────── -->
    <div x-show="isHistoryOpen" style="display:none" x-cloak
         class="modal-above-cart fixed inset-0 z-[500] flex items-center justify-center p-4 bg-slate-900/60 modal-overlay"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
         
        <div class="absolute inset-0" @click="isHistoryOpen = false"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-4xl flex flex-col z-10"
             style="max-height: 88vh;" @click.stop
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
            
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 shrink-0 bg-gradient-to-r from-blue-50 to-indigo-50">
                <h2 class="text-xl font-bold text-slate-800 flex items-center gap-3">
                    <span class="w-10 h-10 bg-gradient-to-br from-blue-600 to-indigo-600 text-white rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </span>
                    Savdo Hisoboti
                </h2>
                <button type="button" @click="isHistoryOpen = false" class="p-2 rounded-xl text-slate-400 hover:bg-red-100 hover:text-red-600 transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            <!-- Content -->
            <div class="flex-1 overflow-y-auto p-6 bg-slate-50/50">
                <div x-show="sales.length === 0" class="text-center py-12">
                    <div class="w-20 h-20 bg-slate-200/50 border-2 border-dashed border-slate-300 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                    </div>
                    <p class="text-slate-600 font-bold text-lg">Hozircha savdolar mavjud emas.</p>
                    <p class="text-slate-400 text-sm mt-2">Satuvni boshlangu, barcha ma'lumotlar shu yerda ko'rinadi.</p>
                </div>
                
                <div x-show="sales.length > 0" class="space-y-3">
                    <template x-for="sale in sales" :key="sale.id">
                        <div class="bg-white border border-slate-200 rounded-2xl p-5 hover:shadow-lg hover:shadow-blue-100 hover:border-blue-300 transition-all">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <!-- Info -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-2 mb-2">
                                        <span class="text-xs font-black px-3 py-1 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg shadow-sm" x-text="'SA-' + String(sale.id).padStart(4, '0')"></span>
                                        <span class="text-xs font-bold text-slate-600 bg-slate-100 px-2.5 py-1 rounded-lg border border-slate-200 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                            <span x-text="formatDate(sale.sale_date)"></span>
                                        </span>
                                        <!-- To'lov usuli badge -->
                                        <span class="text-xs font-bold px-2.5 py-1 rounded-lg border"
                                              :class="sale.payment_method === 'nasiya' ? 'bg-orange-50 text-orange-700 border-orange-200' : sale.payment_method === 'karta' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200'"
                                              x-text="sale.payment_method === 'nasiya' ? '📋 Nasiya' : sale.payment_method === 'karta' ? '💳 Karta' : '💵 Naqd'">
                                        </span>
                                    </div>
                                    <h3 class="text-base font-bold text-slate-900 mb-1 truncate" x-text="sale.customer ? sale.customer.name : '👤 Umumiy Mijoz'"></h3>
                                    
                                    <!-- Items -->
                                    <div class="flex flex-wrap items-center gap-1.5 mt-2">
                                        <template x-for="item in sale.items">
                                            <div class="inline-flex items-center gap-1 text-xs bg-slate-100 border border-slate-300 rounded-lg px-2 py-1 text-slate-700 font-medium">
                                                <span x-text="item.product ? item.product.name : 'o\'chirilgan'"></span>
                                                <span class="font-bold text-slate-900" x-text="'(' + item.quantity + ')'"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                
                                <!-- Price & Action -->
                                <div class="flex flex-col md:items-end justify-center shrink-0 gap-3">
                                    <div class="text-right">
                                        <p class="text-xs font-bold text-slate-400 uppercase">Jami</p>
                                        <p class="text-2xl font-black text-emerald-600" x-text="formatMoney(sale.total_price) + ' so\'m'"></p>
                                    </div>
                                    <button @click.stop="deleteSaleHistory(sale.id)" :disabled="isDeletingId === sale.id"
                                        class="inline-flex items-center justify-center gap-1.5 px-4 py-2 text-sm font-bold text-red-600 bg-red-50 hover:bg-red-600 hover:text-white border border-red-200 hover:border-red-600 rounded-xl transition-all disabled:opacity-50 w-full md:w-auto">
                                        <svg x-show="isDeletingId === sale.id" class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                        <svg x-show="isDeletingId !== sale.id" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0115.138 21H8.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        <span x-text="isDeletingId === sale.id ? 'O\'chirilmoqda...' : 'O\'chirish'"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- ── CHECKOUT MODAL ──────────────────────────────────────── -->
    <div x-show="isSellOpen" style="display:none" x-cloak
         class="modal-above-cart fixed inset-0 z-[500] flex items-center justify-center p-4 bg-slate-900/60 modal-overlay"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
         
        <div class="absolute inset-0" @click="isSellOpen = false"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-lg z-10 overflow-hidden flex flex-col" style="max-height:92dvh" @click.stop
             x-transition:enter="transition ease-out duration-300" 
             x-transition:enter-start="opacity-0 scale-90 translate-y-8" 
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200">

            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-blue-600 to-indigo-600">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center text-white shadow-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="text-white">
                        <h2 class="font-bold text-lg leading-none">Sotish Tasdiqlash</h2>
                        <p class="text-xs text-blue-100 font-semibold mt-1">Checkout</p>
                    </div>
                </div>
                <button @click="isSellOpen = false" class="p-2 rounded-xl text-blue-100 hover:bg-white/20 transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Items Summary -->
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
                <p class="text-xs font-bold text-slate-500 uppercase mb-3">Savatcha ma'lumoti</p>
                <div class="space-y-2 max-h-32 overflow-y-auto">
                    <template x-for="item in cart" :key="item.id">
                        <div class="flex justify-between text-sm bg-white p-2 rounded-lg">
                            <span class="text-slate-700 font-medium" x-text="`${item.name} ×${item.qty}`"></span>
                            <span class="font-bold text-blue-700" x-text="formatMoney(item.price * item.qty) + ' so\'m'"></span>
                        </div>
                    </template>
                </div>
                <div class="border-t border-slate-200 mt-3 pt-3 flex justify-between text-base font-black">
                    <span class="text-slate-800">JAMI:</span>
                    <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent" x-text="formatMoney(cartTotal) + ' so\'m'"></span>
                </div>
            </div>

            <!-- Form -->
            <div class="p-6 space-y-4 overflow-y-auto flex-1">

                <!-- Tanlangan to'lov usuli (readonly ko'rsatish) -->
                <div class="flex items-center gap-3 p-3 rounded-xl border"
                     :class="sellForm.payment_method === 'naqd' ? 'bg-emerald-50 border-emerald-200' : sellForm.payment_method === 'karta' ? 'bg-blue-50 border-blue-200' : 'bg-orange-50 border-orange-200'">
                    <span class="text-xs font-bold text-slate-500 uppercase">To'lov usuli:</span>
                    <span class="text-sm font-black"
                          :class="sellForm.payment_method === 'naqd' ? 'text-emerald-700' : sellForm.payment_method === 'karta' ? 'text-blue-700' : 'text-orange-700'"
                          x-text="sellForm.payment_method === 'naqd' ? '💵 Naqd pul' : sellForm.payment_method === 'karta' ? '💳 Karta' : '📋 Nasiya'"></span>
                </div>

                <!-- Customer selector -->
                <div x-show="!showNewCustomerForm">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-xs font-bold uppercase"
                               :class="sellForm.payment_method === 'nasiya' ? 'text-orange-600' : 'text-slate-700'">
                            Mijoz
                            <span x-text="sellForm.payment_method === 'nasiya' ? '(NASIYA — MAJBURIY!)' : '(ixtiyoriy)'"></span>
                        </label>
                        <button type="button" @click="showNewCustomerForm = true" class="text-xs font-bold text-blue-600 hover:text-blue-700 transition">+ Yangi</button>
                    </div>
                    <select x-model="sellForm.customer_id"
                        :class="sellForm.payment_method === 'nasiya' && !sellForm.customer_id ? 'border-orange-400 ring-2 ring-orange-200' : 'border-slate-300'"
                        class="w-full px-3 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 text-slate-700 bg-white shadow-sm">
                        <option value="">👤 Tanlash shart emas...</option>
                        <template x-for="customer in customers" :key="customer.id">
                            <option :value="customer.id" x-text="customer.company_name ? `${customer.name} (${customer.company_name})` : customer.name"></option>
                        </template>
                    </select>
                    <!-- Nasiya ogohlantirish -->
                    <p x-show="sellForm.payment_method === 'nasiya' && !sellForm.customer_id"
                       class="mt-1.5 text-xs font-semibold text-orange-600 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                        Nasiya uchun mijoz tanlash shart!
                    </p>
                </div>

                <!-- New Customer Form -->
                <div x-show="showNewCustomerForm" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="bg-blue-50 border border-blue-200 rounded-lg p-4 space-y-3">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-bold text-blue-900">Yangi Mijoz</h3>
                        <button type="button" @click="showNewCustomerForm = false" class="text-xs font-bold text-blue-600 hover:text-blue-700">Bekor</button>
                    </div>
                    
                    <div x-show="Object.keys(sellErrors).length > 0" class="bg-red-50 border border-red-200 rounded-lg p-2">
                        <template x-for="(errors, field) in sellErrors">
                            <p class="text-xs text-red-700 font-bold" x-text="Array.isArray(errors) ? errors[0] : errors"></p>
                        </template>
                    </div>

                    <input type="text" x-model="newCustomer.name" placeholder="F.I.SH." maxlength="100" class="w-full px-3 py-2 text-sm border border-blue-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/30 text-slate-800 placeholder-slate-400">
                    
                    <div class="grid grid-cols-2 gap-2">
                        <input type="text" x-model="newCustomer.company_name" placeholder="Korxona" class="w-full px-3 py-2 text-sm border border-blue-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/30 text-slate-800 placeholder-slate-400">
                        <input type="tel" x-model="newCustomer.phone" placeholder="+998..." class="w-full px-3 py-2 text-sm border border-blue-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/30 text-slate-800 placeholder-slate-400">
                    </div>
                    
                    <input type="text" x-model="newCustomer.address" placeholder="Manzil" class="w-full px-3 py-2 text-sm border border-blue-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/30 text-slate-800 placeholder-slate-400">
                    
                    <button type="button" @click="saveNewCustomer" :disabled="customerLoading" class="w-full py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white text-sm font-bold rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all disabled:opacity-50">
                        <span x-show="!customerLoading">Saqlash va Tanlash</span>
                        <span x-show="customerLoading" class="flex items-center justify-center gap-2">
                            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            Saqlanmoqda...
                        </span>
                    </button>
                </div>

                <!-- Date picker -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Savdo Sanasi</label>
                    <input type="date" x-model="sellForm.sale_date"
                        class="w-full px-3 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 text-slate-700 bg-white shadow-sm">
                </div>

                <!-- Nasiya muddati (faqat nasiya uchun) -->
                <div x-show="sellForm.payment_method === 'nasiya'"
                     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                    <label class="block text-xs font-bold text-orange-600 uppercase mb-2">
                        <svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Nasiya muddati (ixtiyoriy)
                    </label>
                    <input type="date" x-model="sellForm.due_date"
                        class="w-full px-3 py-2.5 text-sm border border-orange-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400/30 focus:border-orange-400 text-slate-700 bg-orange-50/30 [color-scheme:light]">
                    <p class="text-[10px] text-slate-400 mt-1">Qarzni to'lash kerak bo'lgan sana</p>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-3 px-6 py-4 border-t border-slate-200 bg-slate-50">
                <button @click="isSellOpen = false" class="flex-1 px-4 py-2.5 text-sm font-bold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-all">
                    Bekor
                </button>
                <button @click="confirmSell()" :disabled="loading"
                    class="flex-1 px-4 py-2.5 text-sm font-bold text-white bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 rounded-lg transition-all disabled:opacity-50 flex items-center justify-center gap-2 shadow-lg hover:shadow-emerald-300/40 disabled:shadow-none">
                    <svg x-show="loading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    <svg x-show="!loading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span x-text="loading ? 'SAQLANMOQDA...' : 'TASDIQLASH'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- ── PRINT MODAL ─────────────────────────────────────────── -->
    <div x-show="showPrintModal" x-cloak style="display:none"
         class="fixed inset-0 z-[700] flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-sm overflow-hidden"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-90"
             x-transition:enter-end="opacity-100 scale-100">

            <!-- Icon + Title -->
            <div class="flex flex-col items-center px-6 pt-7 pb-5 text-center">
                <div class="w-16 h-16 rounded-2xl bg-emerald-50 border-2 border-emerald-200 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-black text-slate-800 mb-1">Chek chiqarasizmi?</h3>
                <p class="text-sm text-slate-500">Savdo muvaffaqiyatli amalga oshirildi.<br>Printerdan chek bosib chiqarish mumkin.</p>
            </div>

            <!-- Buttons -->
            <div class="flex gap-3 px-5 pb-6">
                <button @click="skipPrint()"
                        class="flex-1 px-4 py-3 text-sm font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-all">
                    Kerak emas
                </button>
                <button @click="printReceipt()" :disabled="printLoading"
                        class="flex-1 px-4 py-3 text-sm font-bold text-white bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 rounded-xl transition-all disabled:opacity-60 flex items-center justify-center gap-2 shadow-lg shadow-emerald-200">
                    <svg x-show="printLoading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <svg x-show="!printLoading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    <span x-text="printLoading ? 'Chiqarilmoqda...' : 'Ha, chiqarish'"></span>
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
function posApp() {
    return {
        sales: {!! json_encode($sales) !!},
        products: {!! json_encode($products) !!},
        customers: {!! json_encode($customers) !!},

        cart: [],
        search: '',

        isSellOpen: false,
        isHistoryOpen: false,
        isDeletingId: null,
        loading: false,
        pulseCart: false,
        showCartMobile: false,
        showPrintModal: false,

        init() {
            this.$watch('showCartMobile', val => {
                document.body.classList.toggle('cart-open', val);
            });
        },
        pendingSaleId: null,
        printLoading: false,

        _qtyTimers: {},   // debounce timer handles per product id
        totalFlash: false,
        flashCartId: null,

        sellForm: { customer_id: '', sale_date: '', payment_method: '', due_date: '' },
        sellErrors: {},

        showNewCustomerForm: false,
        customerLoading: false,
        newCustomer: { name: '', company_name: '', phone: '', address: '' },

        /* ─── Computed ──────────────────────────────────────────── */
        get filteredProducts() {
            const s = this.search.toLowerCase().trim();
            return s 
                ? this.products.filter(p => p.name.toLowerCase().includes(s)) 
                : this.products;
        },
        get cartCount() { return this.cart.reduce((sum, i) => sum + parseFloat(i.qty), 0); },
        get cartTotal() { return this.cart.reduce((sum, i) => sum + parseFloat(i.price) * parseFloat(i.qty), 0); },

        /* ─── Cart Methods ──────────────────────────────────────── */
        cartQty(pid) { return this.cart.find(i => i.id === pid)?.qty ?? 0; },
        
        triggerPulse() {
            this.pulseCart = true;
            setTimeout(() => this.pulseCart = false, 300);
        },
        
        increment(product) {
            const curr = this.cartQty(product.id);
            if (curr >= product.quantity) {
                this.showNotif(`${product.name}: omborda faqat ${product.quantity} dona mavjud!`, 'error');
                return;
            }
            const existing = this.cart.find(i => i.id === product.id);
            const step = (product.unit === 'kg' || product.unit === 'litr') ? 0.1 : 1;
            if (existing) {
                existing.qty = parseFloat((existing.qty + step).toFixed(3));
            } else {
                this.cart.push({
                    id: product.id,
                    name: product.name,
                    price: parseFloat(product.price),
                    unit: product.unit || 'dona',
                    qty: step
                });
            }
            this.triggerPulse();
            this.triggerFlash(product.id);
            this.triggerTotalFlash();
        },

        decrement(pid) {
            const idx = this.cart.findIndex(i => i.id === pid);
            if (idx === -1) return;
            const step = (this.cart[idx].unit === 'kg' || this.cart[idx].unit === 'litr') ? 0.1 : 1;
            const newQty = parseFloat((this.cart[idx].qty - step).toFixed(3));
            if (newQty <= 0) {
                this.cart.splice(idx, 1);
            } else {
                this.cart[idx].qty = newQty;
                this.triggerFlash(pid);
            }
            this.triggerTotalFlash();
        },

        // To'g'ridan-to'g'ri miqdor kiritish (mahsulot grid)
        setQty(product, value) {
            const v = parseFloat(value);
            if (isNaN(v) || v <= 0) { this.removeFromCart(product.id); return; }
            const capped = Math.min(v, parseFloat(product.quantity));
            if (capped <= 0) { this.showNotif(`${product.name}: omborda mahsulot qolmagan!`, 'error'); return; }
            if (capped < v) this.showNotif(`${product.name}: omborda faqat ${product.quantity} ${product.unit || 'dona'} mavjud!`, 'error');
            const existing = this.cart.find(i => i.id === product.id);
            if (existing) { existing.qty = parseFloat(capped.toFixed(3)); }
            else { this.cart.push({ id: product.id, name: product.name, price: parseFloat(product.price), unit: product.unit || 'dona', qty: parseFloat(capped.toFixed(3)) }); }
        },

        // Savatchadagi item uchun miqdor kiritish (ID bo'yicha)
        setQtyById(pid, value) {
            const v = parseFloat(value);
            const idx = this.cart.findIndex(i => i.id === pid);
            if (idx === -1) return;
            if (isNaN(v) || v <= 0) { this.cart.splice(idx, 1); return; }
            this.cart[idx].qty = parseFloat(v.toFixed(3));
        },

        // Savatchadagi item uchun +step (ID bo'yicha)
        incrementById(pid) {
            const idx = this.cart.findIndex(i => i.id === pid);
            if (idx !== -1) {
                const step = (this.cart[idx].unit === 'kg' || this.cart[idx].unit === 'litr') ? 0.1 : 1;
                this.cart[idx].qty = parseFloat((this.cart[idx].qty + step).toFixed(3));
                this.triggerFlash(pid);
            }
        },

        // Debounced version for @input on product grid qty field
        debouncedSetQty(product, value) {
            clearTimeout(this._qtyTimers[product.id]);
            this._qtyTimers[product.id] = setTimeout(() => {
                this.setQty(product, value);
                if (this.cartQty(product.id) > 0) this.triggerFlash(product.id);
                this.triggerTotalFlash();
            }, 300);
        },

        // Debounced version for @input on cart item qty field
        debouncedSetQtyById(pid, value) {
            clearTimeout(this._qtyTimers['c_' + pid]);
            this._qtyTimers['c_' + pid] = setTimeout(() => {
                this.setQtyById(pid, value);
                if (this.cartQty(pid) > 0) this.triggerFlash(pid);
                this.triggerTotalFlash();
            }, 300);
        },

        // Brief green flash on total sum row
        triggerTotalFlash() {
            this.totalFlash = true;
            setTimeout(() => { this.totalFlash = false; }, 650);
        },

        // Brief green flash on a specific cart item row
        triggerFlash(pid) {
            this.flashCartId = pid;
            setTimeout(() => { this.flashCartId = null; }, 650);
        },

        removeFromCart(pid) {
            this.cart = this.cart.filter(i => i.id !== pid);
        },
        
        clearCart() { 
            this.cart = []; 
        },

        /* ─── Sell ──────────────────────────────────────────────── */
        openSellModal() {
            this.sellForm.sale_date = new Date().toISOString().split('T')[0];
            this.sellForm.customer_id = '';
            this.sellForm.due_date = '';
            this.sellErrors = {};
            this.showNewCustomerForm = false;
            this.isSellOpen = true;
        },

        
        confirmSell() {
            this.sellErrors = {};
            if (!this.sellForm.sale_date) {
                this.showNotif('Sanani tanlang!', 'error');
                return;
            }
            if (!this.sellForm.payment_method) {
                this.showNotif("To'lov usulini tanlang (Naqd, Karta yoki Nasiya)!", 'error');
                return;
            }
            if (this.sellForm.payment_method === 'nasiya' && !this.sellForm.customer_id) {
                this.showNotif("Nasiya uchun mijoz tanlash shart! Iltimos, mijozni tanlang.", 'error');
                return;
            }
            this.loading = true;

            const payload = {
                customer_id:    this.sellForm.customer_id || null,
                sale_date:      this.sellForm.sale_date,
                payment_method: this.sellForm.payment_method,
                due_date:       (this.sellForm.payment_method === 'nasiya' && this.sellForm.due_date) ? this.sellForm.due_date : null,
                items: this.cart.map(i => ({ product_id: i.id, quantity: i.qty }))
            };

            fetch('/sales', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json', 
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content 
                },
                body: JSON.stringify(payload)
            })
            .then(async r => { 
                const d = await r.json(); 
                if (r.status === 422) {
                    this.sellErrors = d.errors || { general: d.message };
                    throw new Error('Validation error');
                }
                if (!r.ok) throw new Error(d.message || 'Xatolik yuz berdi'); 
                return d; 
            })
            .then(d => {
                if (d.success) {
                    this.sales.unshift(d.sale);
                    this.cart.forEach(ci => {
                        const p = this.products.find(pr => pr.id === ci.id);
                        if (p) { p.quantity -= ci.qty; }
                    });
                    this.clearCart();
                    this.isSellOpen = false;
                    this.showCartMobile = false;
                    // Chek modal ochish
                    this.pendingSaleId = d.sale.id;
                    this.showPrintModal = true;
                }
            })
            .catch(e => {
                if (e.message !== 'Validation error') this.showNotif(e.message, 'error');
            })
            .finally(() => { this.loading = false; });
        },

        /* ─── Print Receipt ─────────────────────────────────────── */
        printReceipt() {
            if (!this.pendingSaleId) return;
            this.printLoading = true;

            // Eski iframe bo'lsa o'chirish
            const old = document.getElementById('receipt-iframe');
            if (old) old.remove();

            const iframe = document.createElement('iframe');
            iframe.id = 'receipt-iframe';
            iframe.style.cssText = 'position:fixed;top:-9999px;left:-9999px;width:0;height:0;border:none;';
            iframe.src = '/receipts/' + this.pendingSaleId;
            document.body.appendChild(iframe);

            iframe.onload = () => {
                try {
                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();
                } catch(e) {
                    // Fallback: yangi tabda ochish
                    window.open('/receipts/' + this.pendingSaleId, '_blank');
                }
                this.printLoading = false;
                this.showPrintModal = false;
                this.isSellOpen = false;
                this.showCartMobile = false;
                this.pendingSaleId = null;
                this.showNotif('✓ Savdo amalga oshirildi!', 'success');
                // iframe ni biroz keyin o'chirish (print dialog yopilishi uchun)
                setTimeout(() => { iframe.remove(); }, 3000);
            };
        },

        skipPrint() {
            this.showPrintModal = false;
            this.isSellOpen = false;
            this.showCartMobile = false;
            this.pendingSaleId = null;
            this.showNotif('✓ Savdo amalga oshirildi!', 'success');
        },

        /* ─── History Delete ────────────────────────────────────── */
        deleteSaleHistory(id) {
            if(!confirm("Bu savdoni o'chirib tashlaysizmi? Mahsulotlar omborga qaytariladi.")) return;
            this.isDeletingId = id;
            
            fetch(`/sales/${id}`, {
                method: 'DELETE',
                headers: { 
                    'Content-Type': 'application/json', 
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content 
                }
            })
            .then(async r => { 
                const d = await r.json(); 
                if (!r.ok) throw new Error(d.message); 
                return d; 
            })
            .then(d => {
                if(d.success) {
                    const sale = this.sales.find(s => s.id === id);
                    if (sale) {
                        sale.items.forEach(item => {
                            const p = this.products.find(pr => pr.id === item.product_id);
                            if (p) { 
                                p.quantity = parseInt(p.quantity) + parseInt(item.quantity); 
                            } else if (item.product) {
                                this.products.push({
                                    id: item.product_id,
                                    name: item.product.name,
                                    price: item.unit_price,
                                    quantity: parseInt(item.quantity)
                                });
                            }
                        });
                    }
                    this.sales = this.sales.filter(s => s.id !== id);
                    this.showNotif('✓ Savdo o\'chirildi, mahsulotlar omborga qaytarildi', 'success');
                }
            })
            .catch(e => this.showNotif(e.message, 'error'))
            .finally(() => { this.isDeletingId = null; });
        },

        /* ─── Save Customer ─────────────────────────────────────── */
        saveNewCustomer() {
            if (!this.newCustomer.name || !this.newCustomer.company_name || !this.newCustomer.phone || !this.newCustomer.address) {
                this.showNotif("Barcha maydonlarni to'ldiring!", 'error');
                return;
            }
            this.customerLoading = true;
            this.sellErrors = {};
            
            const fd = new FormData();
            fd.append('name', this.newCustomer.name);
            fd.append('company_name', this.newCustomer.company_name);
            fd.append('phone', this.newCustomer.phone);
            fd.append('address', this.newCustomer.address);
            fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            
            fetch('/customers', {
                method: 'POST',
                headers: { 'Accept': 'application/json' },
                body: fd
            })
            .then(async r => { 
                const d = await r.json(); 
                if (r.status === 422) {
                    this.sellErrors = d.errors || { general: d.message };
                    throw new Error('Validation error');
                }
                if (!r.ok) throw new Error(d.message || "Xatolik yuz berdi"); 
                return d; 
            })
            .then(data => {
                if(data.success) {
                    this.customers.unshift(data.customer);
                    this.sellForm.customer_id = data.customer.id;
                    this.showNewCustomerForm = false;
                    this.newCustomer = { name: '', company_name: '', phone: '', address: '' };
                    this.showNotif('✓ ' + data.message, 'success');
                }
            })
            .catch(e => {
                if (e.message !== 'Validation error') this.showNotif(e.message, 'error');
            })
            .finally(() => { this.customerLoading = false; });
        },

        /* ─── Helpers ───────────────────────────────────────────── */
        formatMoney(n) {
            return Number(n || 0).toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
        },
        
        formatDate(d) {
            if (!d) return '';
            const dt = new Date(d);
            return `${String(dt.getDate()).padStart(2,'0')}.${String(dt.getMonth()+1).padStart(2,'0')}.${dt.getFullYear()}`;
        },
        
        showNotif(msg, type) {
            const isSuccess = type === 'success';
            const el = document.createElement('div');
            el.className = `fixed bottom-6 right-6 px-6 py-4 rounded-xl border shadow-2xl text-sm font-bold z-[9999] transition-all duration-500 transform translate-y-24 opacity-0 flex items-center gap-3 min-w-[320px] backdrop-blur-sm backdrop-blur-sm ${
                isSuccess 
                    ? 'bg-emerald-50 border-emerald-200 text-emerald-800' 
                    : 'bg-red-50 border-red-200 text-red-800'
            }`;
            
            const icon = isSuccess 
                ? '<svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>'
                : '<svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>';
            
            el.innerHTML = `${icon}<span>${msg}</span>`;
            document.body.appendChild(el);
            
            setTimeout(() => { el.classList.remove('translate-y-24', 'opacity-0'); }, 10);
            
            setTimeout(() => { 
                el.classList.add('translate-y-24', 'opacity-0');
                setTimeout(() => el.remove(), 500); 
            }, 4500);
        }
    };
}
</script>
@endsection
