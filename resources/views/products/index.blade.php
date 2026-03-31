@extends('layouts.app')
@section('title', 'Ombor')

@section('head')
<style>
    @keyframes cardIn {
        from { opacity: 0; transform: translateY(16px) scale(0.97); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }
    .card-enter { animation: cardIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) both; }
    .card-enter:nth-child(1)  { animation-delay: 0.02s; }
    .card-enter:nth-child(2)  { animation-delay: 0.05s; }
    .card-enter:nth-child(3)  { animation-delay: 0.08s; }
    .card-enter:nth-child(4)  { animation-delay: 0.11s; }
    .card-enter:nth-child(5)  { animation-delay: 0.14s; }
    .card-enter:nth-child(6)  { animation-delay: 0.17s; }
    .card-enter:nth-child(7)  { animation-delay: 0.20s; }
    .card-enter:nth-child(8)  { animation-delay: 0.23s; }
    .card-enter:nth-child(n+9){ animation-delay: 0.26s; }

    .product-card {
        transition: transform 0.22s cubic-bezier(0.4, 0, 0.2, 1),
                    box-shadow 0.22s cubic-bezier(0.4, 0, 0.2, 1),
                    border-color 0.22s ease;
    }
    .product-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 32px -6px rgba(37, 99, 235, 0.12);
        border-color: rgba(147, 197, 253, 0.8);
    }

    .unit-btn {
        transition: all 0.18s ease;
    }
    .unit-btn.active {
        background: #2563eb;
        color: #fff;
        border-color: #2563eb;
        box-shadow: 0 2px 8px rgba(37,99,235,0.25);
    }

    [x-cloak] { display: none !important; }
</style>
@endsection

@section('content')
<div x-data="productApp()">

    <!-- ── Page Header ──────────────────────────────────────────── -->
    <div class="bg-white border-b border-slate-200">
        <div class="h-14 md:h-16 flex items-center justify-between px-4 md:px-8">
            <div>
                <h1 class="text-base md:text-lg font-bold text-slate-800">Ombor</h1>
                <p class="text-xs text-slate-400" x-text="`${filteredProducts.length} ta mahsulot`"></p>
            </div>
            <div class="flex items-center gap-2">
                <!-- Search -->
                <div class="relative">
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" x-model="searchTerm" placeholder="Qidirish..."
                        class="pl-8 pr-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 text-slate-700 placeholder-slate-400 w-28 sm:w-44 transition-all">
                </div>
                <!-- Add button -->
                <button @click="openNewModal()"
                    class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-semibold text-sm py-2 px-3 md:px-4 rounded-lg transition-all shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span class="hidden sm:inline">Qo'shish</span>
                </button>
            </div>
        </div>

        <!-- Filter chips -->
        <div class="px-4 md:px-8 pb-3 flex items-center gap-2 overflow-x-auto scrollbar-hide">
            <button @click="filterType = 'all'"
                :class="filterType === 'all' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-slate-500 border-slate-200 hover:border-blue-300 hover:text-blue-600'"
                class="shrink-0 px-3 py-1.5 text-xs font-semibold border rounded-full transition-all">
                Barchasi
            </button>
            <button @click="filterType = 'low'"
                :class="filterType === 'low' ? 'bg-amber-500 text-white border-amber-500' : 'bg-white text-slate-500 border-slate-200 hover:border-amber-300 hover:text-amber-600'"
                class="shrink-0 px-3 py-1.5 text-xs font-semibold border rounded-full transition-all">
                Kam qoldi (&lt;5)
            </button>
            <button @click="filterType = 'out'"
                :class="filterType === 'out' ? 'bg-red-600 text-white border-red-600' : 'bg-white text-slate-500 border-slate-200 hover:border-red-300 hover:text-red-600'"
                class="shrink-0 px-3 py-1.5 text-xs font-semibold border rounded-full transition-all">
                Tugagan (0)
            </button>
        </div>
    </div>

    <!-- ── Main Content ──────────────────────────────────────────── -->
    <main class="p-3 md:p-6 pb-24 md:pb-8">

        <!-- Product Grid -->
        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-2 md:gap-3">
            <template x-for="product in filteredProducts" :key="product.id">

                <div class="product-card card-enter bg-white rounded-xl border flex flex-col overflow-hidden"
                     :class="parseFloat(product.quantity) === 0
                        ? 'border-red-200'
                        : 'border-slate-200'">

                    <!-- ── Card Top: nom + badge ── -->
                    <div class="px-3 pt-3 pb-2 flex items-start justify-between gap-1">
                        <span class="text-xs md:text-sm font-bold text-slate-800 leading-snug line-clamp-2 flex-1"
                              x-text="product.name"></span>
                        <div class="shrink-0 ml-1">
                            <template x-if="parseFloat(product.quantity) === 0">
                                <span class="text-[9px] font-black bg-red-500 text-white px-1.5 py-0.5 rounded-full whitespace-nowrap">Tugagan</span>
                            </template>
                            <template x-if="parseFloat(product.quantity) > 0 && parseFloat(product.quantity) < 5">
                                <span class="text-[9px] font-bold bg-amber-100 text-amber-700 border border-amber-200 px-1.5 py-0.5 rounded-full whitespace-nowrap flex items-center gap-0.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse inline-block"></span>
                                    Kam!
                                </span>
                            </template>
                        </div>
                    </div>

                    <!-- ── Card Body: ma'lumotlar ── -->
                    <div class="px-3 pb-2 flex-1 space-y-1.5">

                        <!-- Narxi -->
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] text-slate-400 font-medium">Narxi</span>
                            <span class="text-[11px] md:text-xs font-bold text-blue-600"
                                  x-text="fmtMoney(product.price) + ' so\'m'"></span>
                        </div>

                        <!-- Tannarxi — faqat mavjud bo'lsa -->
                        <template x-if="product.cost_price && parseFloat(product.cost_price) > 0">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] text-slate-400 font-medium">Tannarxi</span>
                                <span class="text-[10px] md:text-xs font-semibold text-slate-500"
                                      x-text="fmtMoney(product.cost_price) + ' so\'m'"></span>
                            </div>
                        </template>

                        <!-- Separator -->
                        <div class="border-t border-slate-100 my-1"></div>

                        <!-- Soni -->
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] text-slate-400 font-medium">Soni</span>
                            <span class="text-[10px] md:text-xs font-bold px-2 py-0.5 rounded-full border"
                                  :class="parseFloat(product.quantity) === 0
                                    ? 'bg-red-50 text-red-700 border-red-200'
                                    : parseFloat(product.quantity) < 5
                                      ? 'bg-amber-50 text-amber-700 border-amber-200'
                                      : 'bg-emerald-50 text-emerald-700 border-emerald-200'"
                                  x-text="parseFloat(product.quantity) + ' ta'"></span>
                        </div>

                        <!-- Kg yoki Litr — faqat mavjud bo'lsa -->
                        <template x-if="product.unit && product.unit !== 'dona' && product.unit_value && parseFloat(product.unit_value) > 0">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] text-slate-400 font-medium capitalize" x-text="product.unit === 'kg' ? 'Kg' : 'Litr'"></span>
                                <span class="text-[10px] md:text-xs font-bold text-indigo-600"
                                      x-text="parseFloat(product.unit_value) + ' ' + product.unit"></span>
                            </div>
                        </template>

                    </div>

                    <!-- ── Card Footer: sana + tugmalar ── -->
                    <div class="px-3 pb-2 hidden md:flex items-center gap-1">
                        <svg class="w-3 h-3 text-slate-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-[9px] text-slate-400" x-text="formatDate(product.updated_at)"></span>
                    </div>

                    <div class="border-t flex"
                         :class="parseFloat(product.quantity) === 0 ? 'border-red-100' : 'border-slate-100'">
                        <!-- Tahrirlash -->
                        <button @click="editProduct(product)"
                            class="flex-1 flex items-center justify-center gap-1 py-2.5 text-[11px] font-semibold border-r transition-colors active:scale-95"
                            :class="parseFloat(product.quantity) === 0
                                ? 'text-red-600 hover:bg-red-50 border-red-100'
                                : 'text-slate-500 hover:text-blue-600 hover:bg-blue-50 border-slate-100'">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            <span>Tahrirla</span>
                        </button>
                        <!-- O'chirish -->
                        <button @click="deleteProduct(product.id)"
                            class="flex-1 flex items-center justify-center gap-1 py-2.5 text-[11px] font-semibold text-slate-500 hover:text-red-600 hover:bg-red-50 transition-colors active:scale-95">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            O'chirish
                        </button>
                    </div>

                </div>
            </template>
        </div>

        <!-- Empty state -->
        <div x-show="filteredProducts.length === 0" class="flex flex-col items-center justify-center py-24 text-center">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
            </div>
            <p class="text-sm font-semibold text-slate-600 mb-1">Mahsulot topilmadi</p>
            <p class="text-xs text-slate-400 mb-4">Ombor bo'sh yoki qidiruv natijasi yo'q</p>
            <button @click="openNewModal()"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Mahsulot qo'shish
            </button>
        </div>

    </main>

    <!-- ══════════════════════════════════════════════════════════
         ADD / EDIT MODAL
    ══════════════════════════════════════════════════════════ -->
    <div x-show="isModalOpen" style="display:none"
         class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-slate-900/50 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-250"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-180"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <div @click.outside="closeModal()" @click.stop
             class="bg-white w-full sm:max-w-md rounded-t-2xl sm:rounded-2xl shadow-2xl border border-slate-200 overflow-hidden"
             x-transition:enter="transition ease-out duration-280"
             x-transition:enter-start="opacity-0 translate-y-6 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-180"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-6 sm:scale-95">

            <!-- Modal Header -->
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-blue-50">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                  x-show="!editingId" d="M12 4v16m8-8H4"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  x-show="editingId" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <h2 class="font-bold text-slate-800 text-sm"
                        x-text="editingId ? 'Mahsulotni tahrirlash' : 'Yangi mahsulot qo\'shish'"></h2>
                </div>
                <button @click="closeModal()"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-5 space-y-4 max-h-[75dvh] overflow-y-auto">

                <!-- 1. Mahsulot nomi -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                        Mahsulot nomi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" x-model="form.name"
                        placeholder="Masalan: Aktiv pena..."
                        class="w-full px-3 py-2.5 text-sm border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 text-slate-700 placeholder-slate-400 transition-colors"
                        :class="errors.name ? 'border-red-400 bg-red-50' : 'border-slate-200'">
                    <p x-show="errors.name" class="text-red-500 text-xs mt-1" x-text="errors.name"></p>
                </div>

                <!-- 2. Sotish narxi -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                        Sotish narxi (so'm) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" step="0.01" inputmode="decimal" x-model="form.price"
                        placeholder="0"
                        class="w-full px-3 py-2.5 text-sm border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 text-slate-700 transition-colors"
                        :class="errors.price ? 'border-red-400 bg-red-50' : 'border-slate-200'">
                    <p x-show="errors.price" class="text-red-500 text-xs mt-1" x-text="errors.price"></p>
                </div>

                <!-- 3. Tannarxi -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                        Tannarxi (so'm)
                        <span class="text-slate-400 font-normal">(ixtiyoriy)</span>
                    </label>
                    <input type="number" step="0.01" inputmode="decimal" x-model="form.cost_price"
                        placeholder="0"
                        class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 text-slate-700 transition-colors">
                </div>

                <!-- 4. Soni -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                        Soni (ta) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" step="1" inputmode="numeric" x-model="form.quantity"
                        placeholder="0"
                        class="w-full px-3 py-2.5 text-sm border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 text-slate-700 transition-colors"
                        :class="errors.quantity ? 'border-red-400 bg-red-50' : 'border-slate-200'">
                    <p x-show="errors.quantity" class="text-red-500 text-xs mt-1" x-text="errors.quantity"></p>
                </div>

                <!-- 5. Kg / Litr (ixtiyoriy) -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                        O'lchov
                        <span class="text-slate-400 font-normal">(ixtiyoriy — kg yoki litr)</span>
                    </label>
                    <!-- Toggle tugmalar -->
                    <div class="flex gap-2">
                        <!-- Kg tugmasi -->
                        <button type="button"
                            @click="form.unit = (form.unit === 'kg') ? '' : 'kg'; form.unit_value = ''"
                            :class="form.unit === 'kg' ? 'active' : 'border-slate-200 text-slate-500 hover:border-blue-300 hover:text-blue-600'"
                            class="unit-btn flex-1 flex items-center justify-center gap-1.5 py-2.5 text-sm font-bold border rounded-xl">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                            </svg>
                            Kg
                        </button>
                        <!-- Litr tugmasi -->
                        <button type="button"
                            @click="form.unit = (form.unit === 'litr') ? '' : 'litr'; form.unit_value = ''"
                            :class="form.unit === 'litr' ? 'active' : 'border-slate-200 text-slate-500 hover:border-blue-300 hover:text-blue-600'"
                            class="unit-btn flex-1 flex items-center justify-center gap-1.5 py-2.5 text-sm font-bold border rounded-xl">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                            </svg>
                            Litr
                        </button>
                    </div>

                    <!-- Necha kg / litr? — faqat tanlanganda ko'rinadi -->
                    <div x-show="form.unit === 'kg' || form.unit === 'litr'"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="mt-3">
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                            Necha <span x-text="form.unit"></span>?
                            <span class="text-slate-400 font-normal">(ixtiyoriy)</span>
                        </label>
                        <div class="relative">
                            <input type="number" step="0.001" inputmode="decimal" x-model="form.unit_value"
                                placeholder="0"
                                class="w-full px-3 py-2.5 pr-12 text-sm border border-blue-200 bg-blue-50/40 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 text-slate-700 transition-colors">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-blue-500"
                                  x-text="form.unit"></span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="flex gap-3 px-5 py-4 border-t border-slate-100 bg-slate-50">
                <button @click="closeModal()"
                    class="flex-1 px-4 py-2.5 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
                    Bekor qilish
                </button>
                <button @click="submitForm()"
                    class="flex-1 px-4 py-2.5 text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 active:scale-[0.98] rounded-xl transition-all shadow-sm">
                    <span x-text="editingId ? 'Saqlash' : 'Qo\'shish'"></span>
                </button>
            </div>

        </div>
    </div>

    <!-- ══════════════════════════════════════════════════════════
         DELETE CONFIRM MODAL
    ══════════════════════════════════════════════════════════ -->
    <div x-show="isDeleteModalOpen" style="display:none"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

        <div @click.outside="cancelDelete()" @click.stop
             class="bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-sm overflow-hidden"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">

            <div class="p-6 text-center">
                <div class="w-12 h-12 rounded-full bg-red-50 border border-red-100 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <h3 class="font-bold text-slate-800 mb-1">O'chirishni tasdiqlang</h3>
                <p class="text-sm text-slate-500">Bu mahsulot bazadan butunlay o'chiriladi.</p>
            </div>
            <div class="flex gap-3 px-5 pb-5">
                <button @click="cancelDelete()"
                    class="flex-1 px-4 py-2.5 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
                    Bekor
                </button>
                <button @click="confirmDelete()"
                    class="flex-1 px-4 py-2.5 text-sm font-bold text-white bg-red-600 hover:bg-red-700 rounded-xl transition-colors">
                    O'chirish
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
function productApp() {
    return {
        products: @json($products ?? []),
        isModalOpen: false,
        isDeleteModalOpen: false,
        deleteProductId: null,
        editingId: null,
        searchTerm: '',
        filterType: 'all',
        errors: {},

        form: {
            name: '',
            price: '',
            cost_price: '',
            quantity: '',
            unit: '',
            unit_value: ''
        },

        /* ─── Computed ─────────────────────────────────────────── */
        get filteredProducts() {
            let res = this.products;
            if (this.filterType === 'low')
                res = res.filter(p => parseFloat(p.quantity) > 0 && parseFloat(p.quantity) < 5);
            else if (this.filterType === 'out')
                res = res.filter(p => parseFloat(p.quantity) === 0);
            if (this.searchTerm.trim())
                res = res.filter(p => p.name.toLowerCase().includes(this.searchTerm.toLowerCase().trim()));
            return res;
        },

        /* ─── Modal helpers ────────────────────────────────────── */
        openNewModal() {
            this.editingId = null;
            this.form = { name: '', price: '', cost_price: '', quantity: '', unit: '', unit_value: '' };
            this.errors = {};
            this.isModalOpen = true;
        },

        editProduct(product) {
            this.editingId = product.id;
            this.form = {
                name:       product.name,
                price:      product.price,
                cost_price: product.cost_price || '',
                quantity:   product.quantity,
                unit:       (product.unit && product.unit !== 'dona') ? product.unit : '',
                unit_value: product.unit_value || ''
            };
            this.errors = {};
            this.isModalOpen = true;
        },

        closeModal() {
            this.isModalOpen = false;
            setTimeout(() => {
                this.editingId = null;
                this.form = { name: '', price: '', cost_price: '', quantity: '', unit: '', unit_value: '' };
                this.errors = {};
            }, 200);
        },

        /* ─── Submit form ──────────────────────────────────────── */
        submitForm() {
            this.errors = {};

            // Oddiy validatsiya
            if (!this.form.name.trim()) { this.errors.name = 'Nomi kiritilishi shart'; return; }
            if (!this.form.price)       { this.errors.price = 'Narxi kiritilishi shart'; return; }
            if (!this.form.quantity && this.form.quantity !== 0) { this.errors.quantity = 'Soni kiritilishi shart'; return; }

            const url    = this.editingId ? `/products/${this.editingId}` : '/products';
            const method = this.editingId ? 'PUT' : 'POST';

            // unit maydoniga 'dona' yozamiz agar bo'sh bo'lsa (backend uchun)
            const payload = {
                ...this.form,
                unit: this.form.unit || 'dona',
                unit_value: (this.form.unit && this.form.unit_value) ? this.form.unit_value : null
            };

            fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(payload)
            })
            .then(async r => {
                const data = await r.json();
                if (r.status === 422) {
                    this.errors = data.errors || { general: data.message };
                    throw new Error('Validation error');
                }
                if (!r.ok) throw new Error(data.message || 'Xatolik yuz berdi');
                return data;
            })
            .then(data => {
                if (data.success) {
                    if (this.editingId) {
                        const idx = this.products.findIndex(p => p.id === this.editingId);
                        if (idx !== -1) this.products[idx] = data.product;
                    } else {
                        this.products.unshift(data.product);
                    }
                    this.closeModal();
                    this.showNotif(data.message, 'success');
                }
            })
            .catch(e => {
                if (e.message !== 'Validation error') this.showNotif(e.message, 'error');
            });
        },

        /* ─── Delete ───────────────────────────────────────────── */
        deleteProduct(id)  { this.deleteProductId = id; this.isDeleteModalOpen = true; },
        cancelDelete()     { this.isDeleteModalOpen = false; this.deleteProductId = null; },

        confirmDelete() {
            fetch(`/products/${this.deleteProductId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(async r => {
                const data = await r.json();
                if (!r.ok) throw new Error(data.message || 'Xatolik yuz berdi');
                return data;
            })
            .then(data => {
                if (data.success) {
                    this.products = this.products.filter(p => p.id !== this.deleteProductId);
                    this.isDeleteModalOpen = false;
                    this.deleteProductId = null;
                    this.showNotif(data.message, 'success');
                }
            })
            .catch(e => this.showNotif(e.message, 'error'));
        },

        /* ─── Helpers ──────────────────────────────────────────── */
        fmtMoney(n) {
            return Number(n || 0).toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
        },

        formatDate(dateStr) {
            if (!dateStr) return '';
            const d = new Date(dateStr);
            const p = n => String(n).padStart(2, '0');
            return `${p(d.getDate())}.${p(d.getMonth()+1)}.${d.getFullYear()} ${p(d.getHours())}:${p(d.getMinutes())}`;
        },

        showNotif(msg, type) {
            const ok = type === 'success';
            const el = document.createElement('div');
            el.className = `fixed bottom-8 right-6 px-5 py-3.5 rounded-xl border shadow-2xl text-sm font-bold z-[9999] transition-all duration-400 transform translate-y-20 opacity-0 flex items-center gap-3 min-w-[280px] ${
                ok ? 'bg-white border-emerald-100 text-emerald-700' : 'bg-white border-red-100 text-red-600'
            }`;
            const icon = ok
                ? '<svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>'
                : '<svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>';
            el.innerHTML = `${icon}<span>${msg}</span>`;
            document.body.appendChild(el);
            setTimeout(() => el.classList.remove('translate-y-20', 'opacity-0'), 10);
            setTimeout(() => {
                el.classList.add('translate-y-20', 'opacity-0');
                setTimeout(() => el.remove(), 400);
            }, 3800);
        }
    }
}
</script>
@endsection
