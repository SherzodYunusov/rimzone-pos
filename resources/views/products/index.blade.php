@extends('layouts.app')
@section('title', 'Ombor')

@section('head')
<style>
    @keyframes cardIn {
        from { opacity: 0; transform: translateY(14px) scale(0.98); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }
    .card-enter { animation: cardIn 0.45s cubic-bezier(0.16, 1, 0.3, 1) both; }
    .card-enter:nth-child(1)  { animation-delay: 0.02s; }
    .card-enter:nth-child(2)  { animation-delay: 0.06s; }
    .card-enter:nth-child(3)  { animation-delay: 0.10s; }
    .card-enter:nth-child(4)  { animation-delay: 0.14s; }
    .card-enter:nth-child(5)  { animation-delay: 0.18s; }
    .card-enter:nth-child(6)  { animation-delay: 0.22s; }
    .card-enter:nth-child(7)  { animation-delay: 0.26s; }
    .card-enter:nth-child(8)  { animation-delay: 0.30s; }
    .card-enter:nth-child(n+9){ animation-delay: 0.34s; }

    .product-card {
        transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1),
                    box-shadow 0.25s cubic-bezier(0.4, 0, 0.2, 1),
                    border-color 0.25s ease;
    }
    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 28px -6px rgba(37, 99, 235, 0.13);
        border-color: rgba(147, 197, 253, 0.7);
    }
</style>
@endsection

@section('content')
<div x-data="productApp()">

    <!-- Page Header -->
    <div class="bg-gradient-to-r from-white via-white to-blue-50/50 border-b border-slate-200">
        <div class="h-14 md:h-16 flex items-center justify-between px-4 md:px-8">
            <div>
                <h1 class="text-base md:text-lg font-semibold text-slate-800">Ombor</h1>
                <p class="text-xs text-slate-400" x-text="`${filteredProducts.length} ta mahsulot`"></p>
            </div>
            <div class="flex items-center gap-2">
                <div class="relative">
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" x-model="searchTerm" placeholder="Qidirish..."
                        class="pl-8 pr-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 text-slate-700 placeholder-slate-400 w-28 sm:w-44 xl:w-56 transition-all">
                </div>
                <button @click="openNewModal()"
                    class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-medium text-sm py-2 px-3 md:px-4 rounded-lg transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span class="hidden sm:inline">Mahsulot qo'shish</span>
                </button>
            </div>
        </div>
        <!-- Filter chips — mobile horizontal scroll + desktop inline -->
        <div class="px-4 md:px-8 pb-2.5 flex items-center gap-2 overflow-x-auto scrollbar-hide">
            <button @click="filterType = 'all'"
                :class="filterType === 'all'
                    ? 'bg-blue-600 text-white border-blue-600 shadow-sm'
                    : 'bg-white text-slate-500 border-slate-200 hover:border-blue-300 hover:text-blue-600'"
                class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold border rounded-full transition-all">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                Barchasi
            </button>
            <button @click="filterType = 'low'"
                :class="filterType === 'low'
                    ? 'bg-amber-500 text-white border-amber-500 shadow-sm'
                    : 'bg-white text-slate-500 border-slate-200 hover:border-amber-300 hover:text-amber-600'"
                class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold border rounded-full transition-all">
                <span class="w-2 h-2 rounded-full bg-current inline-block"></span>
                Kam qoldi (&lt;5)
            </button>
            <button @click="filterType = 'out'"
                :class="filterType === 'out'
                    ? 'bg-red-600 text-white border-red-600 shadow-sm'
                    : 'bg-white text-slate-500 border-slate-200 hover:border-red-300 hover:text-red-600'"
                class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold border rounded-full transition-all">
                <span class="w-2 h-2 rounded-full bg-current inline-block"></span>
                Tugagan (0)
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <main class="p-3 md:p-8 pb-24 md:pb-8">

        <!-- Search result detail card -->
        <template x-if="selectedProductId">
            <div class="mb-6 bg-white border border-slate-200 rounded-xl shadow-sm p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-slate-800" x-text="products.find(p => p.id === selectedProductId)?.name"></h3>
                    <button @click="selectedProductId = null" class="text-slate-400 hover:text-slate-600 text-xs flex items-center gap-1 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Yopish
                    </button>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
                        <p class="text-xs text-slate-500 font-medium mb-1">Birlik narxi</p>
                        <p class="text-lg font-bold text-slate-800" x-text="calculateUnitPrice(selectedProductId)"></p>
                    </div>
                    <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
                        <p class="text-xs text-slate-500 font-medium mb-1">Qoldiq</p>
                        <p class="text-lg font-bold text-slate-800" x-text="(products.find(p => p.id === selectedProductId)?.quantity || 0) + ' dona'"></p>
                    </div>
                    <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
                        <p class="text-xs text-slate-500 font-medium mb-1">Jami summa</p>
                        <p class="text-lg font-bold text-slate-800" x-text="calculateTotal(selectedProductId)"></p>
                    </div>
                </div>
            </div>
        </template>

        <!-- Products Grid -->
        <div class="grid grid-cols-2 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-2 md:gap-4">
            <template x-for="product in filteredProducts" :key="product.id">
                <div class="product-card card-enter bg-white rounded-xl shadow-sm flex flex-col overflow-hidden border"
                     :class="parseInt(product.quantity) === 0 ? 'border-red-200 bg-red-50/20' : 'border-slate-200'">

                    <!-- Header: name + badge -->
                    <div class="flex items-start justify-between px-3 md:px-4 pt-3 md:pt-4 pb-1.5 gap-1.5">
                        <span class="text-xs md:text-sm font-semibold text-slate-800 line-clamp-2 leading-tight flex-1" x-text="product.name"></span>
                        <div class="shrink-0 flex flex-col items-end gap-1 mt-0.5">
                            <template x-if="parseInt(product.quantity) === 0">
                                <span class="text-[9px] md:text-[10px] font-black bg-red-500 text-white px-1.5 py-0.5 rounded-full shadow-sm whitespace-nowrap">Tugagan</span>
                            </template>
                            <template x-if="parseInt(product.quantity) > 0 && parseInt(product.quantity) < 5">
                                <span class="inline-flex items-center gap-0.5 text-[9px] md:text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200 px-1.5 py-0.5 rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse inline-block"></span>
                                    Kam!
                                </span>
                            </template>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="px-3 md:px-4 pb-2 md:pb-3 flex-1 space-y-1.5 md:space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] md:text-xs text-slate-400">Narxi</span>
                            <span class="text-[11px] md:text-sm font-semibold text-slate-700" x-text="parseFloat(product.price).toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ' ') + ' so\'m'"></span>
                        </div>
                        <template x-if="product.cost_price && parseFloat(product.cost_price) > 0">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] md:text-xs text-slate-400">Tannarx</span>
                                <span class="text-[10px] md:text-xs font-medium text-slate-500" x-text="parseFloat(product.cost_price).toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ' ') + ' so\'m'"></span>
                            </div>
                        </template>
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] md:text-xs text-slate-400">Qoldiq</span>
                            <div class="flex items-center gap-1">
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] md:text-xs font-semibold border"
                                      :class="parseInt(product.quantity) === 0
                                        ? 'bg-red-50 text-red-700 border-red-200'
                                        : parseInt(product.quantity) < 5
                                          ? 'bg-amber-50 text-amber-700 border-amber-200'
                                          : 'bg-blue-50 text-blue-700 border-blue-100'"
                                      x-text="product.quantity + ' dona'"></span>
                            </div>
                        </div>
                        <template x-if="product.description">
                            <p class="text-[10px] md:text-xs text-slate-400 pt-1 border-t border-slate-100 leading-relaxed line-clamp-2" x-text="product.description"></p>
                        </template>
                    </div>

                    <!-- Updated at -->
                    <div class="hidden md:flex px-4 pb-2.5 items-center gap-1.5">
                        <svg class="w-3 h-3 text-slate-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-[10px] text-slate-400" x-text="formatDate(product.updated_at)"></span>
                    </div>

                    <!-- Actions -->
                    <div class="border-t flex" :class="parseInt(product.quantity) === 0 ? 'border-red-100' : 'border-slate-100'">
                        <button @click="editProduct(product)"
                            class="flex-1 flex items-center justify-center gap-1 md:gap-1.5 py-2.5 md:py-3 text-[11px] md:text-xs font-medium transition-colors border-r active:scale-95"
                            :class="parseInt(product.quantity) === 0
                                ? 'text-red-600 bg-red-50/50 hover:bg-red-100 border-red-100 font-bold'
                                : 'text-slate-500 hover:text-blue-600 hover:bg-blue-50 border-slate-100'">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            <span class="hidden sm:inline">Tahrirlash</span>
                            <span class="sm:hidden">Tahrirla</span>
                        </button>
                        <button @click="deleteProduct(product.id)"
                            class="flex-1 flex items-center justify-center gap-1 md:gap-1.5 py-2.5 md:py-3 text-[11px] md:text-xs font-medium text-slate-500 hover:text-red-600 hover:bg-red-50 transition-colors active:scale-95">
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
            <div class="w-14 h-14 rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-center mb-4">
                <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-slate-600 mb-1">Mahsulotlar topilmadi</p>
            <p class="text-xs text-slate-400 mb-4">Omboringiz bo'sh yoki qidiruv natijasi yo'q</p>
            <button @click="openNewModal()"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Birinchi mahsulotni qo'shish
            </button>
        </div>
    </main>

    <!-- ===== ADD / EDIT MODAL ===== -->
    <div x-show="isModalOpen" style="display:none"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div @click.outside="closeModal()" @click.stop
             class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-md overflow-hidden"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-90 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-90 translate-y-4">

            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">
                <h2 class="font-semibold text-slate-800" x-text="editingId ? 'Mahsulotni tahrirlash' : 'Yangi mahsulot qo\'shish'"></h2>
                <button @click="closeModal()" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Body -->
            <div class="p-4 sm:p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Mahsulot nomi <span class="text-red-500">*</span></label>
                    <input type="text" x-model="form.name" placeholder="Masalan: Shampun..."
                        class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 text-slate-700 placeholder-slate-400">
                    <p x-show="errors.name" class="text-red-500 text-xs mt-1" x-text="errors.name"></p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Sotish narxi (so'm) <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" x-model="form.price" placeholder="0"
                            class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 text-slate-700">
                        <p x-show="errors.price" class="text-red-500 text-xs mt-1" x-text="errors.price"></p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Tannarx (so'm) <span class="text-slate-400">(ixtiyoriy)</span></label>
                        <input type="number" step="0.01" x-model="form.cost_price" placeholder="0"
                            class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 text-slate-700">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Soni (dona) <span class="text-red-500">*</span></label>
                        <input type="number" x-model="form.quantity" placeholder="0"
                            class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 text-slate-700">
                        <p x-show="errors.quantity" class="text-red-500 text-xs mt-1" x-text="errors.quantity"></p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">O'lchov turi <span class="text-slate-400">(ixtiyoriy)</span></label>
                        <select x-model="form.unit_type"
                            class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 text-slate-700 bg-white">
                            <option value="">— yo'q —</option>
                            <option value="kg">kg</option>
                            <option value="litr">litr</option>
                        </select>
                    </div>
                </div>
                <template x-if="form.unit_type === 'kg' || form.unit_type === 'litr'">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">
                            Miqdor (<span x-text="form.unit_type"></span>) <span class="text-slate-400">(ixtiyoriy)</span>
                        </label>
                        <input type="number" step="0.001" x-model="form.unit_value" placeholder="0.000"
                            class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 text-slate-700">
                    </div>
                </template>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Tavsif <span class="text-slate-400">(ixtiyoriy)</span></label>
                    <textarea x-model="form.description" placeholder="Mahsulot haqida qisqacha..."
                        class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 text-slate-700 placeholder-slate-400 resize-none h-20"></textarea>
                </div>
            </div>

            <!-- Footer -->
            <div class="flex gap-3 px-6 py-4 border-t border-slate-200 bg-slate-50 rounded-b-xl">
                <button @click="closeModal()"
                    class="flex-1 px-4 py-2.5 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">
                    Bekor qilish
                </button>
                <button @click="submitForm()"
                    class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors shadow-sm">
                    Saqlash
                </button>
            </div>
        </div>
    </div>

    <!-- ===== DELETE MODAL ===== -->
    <div x-show="isDeleteModalOpen" style="display:none"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div @click.outside="cancelDelete()" @click.stop
             class="bg-white rounded-xl shadow-xl border border-slate-200 w-full max-w-sm"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
            <div class="p-6 text-center">
                <div class="w-12 h-12 rounded-full bg-red-50 border border-red-200 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-slate-800 mb-1">O'chirishni tasdiqlang</h3>
                <p class="text-sm text-slate-500">Mahsulot bazadan butunlay o'chiriladi. Bu amalni qaytarib bo'lmaydi.</p>
            </div>
            <div class="flex gap-3 px-6 pb-6">
                <button @click="cancelDelete()" class="flex-1 px-4 py-2.5 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">Bekor qilish</button>
                <button @click="confirmDelete()" class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">O'chirish</button>
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
        selectedProductId: null,
        searchTerm: '',
        form: { name: '', price: '', cost_price: '', quantity: '', unit_type: '', unit_value: '', description: '' },
        errors: {},
        filterType: 'all',

        get filteredProducts() {
            let res = this.products;
            if (this.filterType === 'low') {
                res = res.filter(p => parseInt(p.quantity) > 0 && parseInt(p.quantity) < 5);
            } else if (this.filterType === 'out') {
                res = res.filter(p => parseInt(p.quantity) === 0);
            }
            if (this.searchTerm) res = res.filter(p => p.name.toLowerCase().includes(this.searchTerm.toLowerCase()));
            return res;
        },

        openNewModal() {
            this.editingId = null;
            this.form = { name: '', price: '', cost_price: '', quantity: '', unit_type: '', unit_value: '', description: '' };
            this.errors = {};
            this.isModalOpen = true;
        },

        editProduct(product) {
            this.editingId = product.id;
            this.form = {
                name: product.name,
                price: product.price,
                cost_price: product.cost_price || '',
                quantity: product.quantity,
                unit_type: product.unit_type || '',
                unit_value: product.unit_value || '',
                description: product.description || ''
            };
            this.errors = {};
            this.isModalOpen = true;
        },

        closeModal() {
            this.isModalOpen = false;
            setTimeout(() => {
                this.editingId = null;
                this.form = { name: '', price: '', cost_price: '', quantity: '', unit_type: '', unit_value: '', description: '' };
                this.errors = {};
            }, 200);
        },

        deleteProduct(id) { this.deleteProductId = id; this.isDeleteModalOpen = true; },
        cancelDelete() { this.isDeleteModalOpen = false; this.deleteProductId = null; },

        confirmDelete() {
            fetch(`/products/${this.deleteProductId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            }).then(async r => {
                const data = await r.json();
                if (!r.ok) throw new Error(data.message || 'Xatolik yuz berdi');
                return data;
            }).then(data => {
                if (data.success) {
                    this.products = this.products.filter(p => p.id !== this.deleteProductId);
                    this.isDeleteModalOpen = false;
                    this.deleteProductId = null;
                    this.showNotif(data.message, 'success');
                }
            }).catch(e => this.showNotif(e.message, 'error'));
        },

        submitForm() {
            this.errors = {};
            const url = this.editingId ? `/products/${this.editingId}` : '/products';
            const method = this.editingId ? 'PUT' : 'POST';
            fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(this.form)
            }).then(async r => {
                const data = await r.json();
                if (r.status === 422) {
                    this.errors = data.errors || { general: data.message };
                    throw new Error('Validation error');
                }
                if (!r.ok) throw new Error(data.message || 'Xatolik yuz berdi');
                return data;
            }).then(data => {
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
            }).catch(e => {
                if (e.message !== 'Validation error') this.showNotif(e.message, 'error');
            });
        },

        performSearch() {
            const found = this.filteredProducts;
            if (!this.searchTerm.trim()) return;
            if (found.length === 1) this.selectedProductId = found[0].id;
        },

        calculateUnitPrice(id) {
            const p = this.products.find(p => p.id === id);
            if (!p) return '—';
            return parseFloat(p.price).toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ' ') + ' so\'m';
        },

        calculateTotal(id) {
            const p = this.products.find(p => p.id === id);
            if (!p) return '—';
            return (parseInt(p.quantity) * parseFloat(p.price)).toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ' ') + ' so\'m';
        },

        formatDate(dateStr) {
            if (!dateStr) return '—';
            const d = new Date(dateStr);
            const p = n => String(n).padStart(2, '0');
            return `${p(d.getDate())}.${p(d.getMonth()+1)}.${d.getFullYear()} ${p(d.getHours())}:${p(d.getMinutes())}`;
        },

        showNotif(msg, type) {
            const el = document.createElement('div');
            el.className = `fixed bottom-8 right-8 px-6 py-4 rounded-2xl border shadow-2xl text-sm font-bold z-[9999] transition-all duration-500 transform translate-y-20 opacity-0 flex items-center gap-3 min-w-[300px] ${type === 'success' ? 'bg-white border-emerald-100 text-emerald-700' : 'bg-white border-red-100 text-red-600'}`;

            const icon = type === 'success'
                ? '<svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>'
                : '<svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>';

            el.innerHTML = `${icon} <span>${msg}</span>`;
            document.body.appendChild(el);

            setTimeout(() => { el.classList.remove('translate-y-20', 'opacity-0'); }, 10);

            setTimeout(() => {
                el.classList.add('translate-y-20', 'opacity-0');
                setTimeout(() => el.remove(), 500);
            }, 4000);
        }
    }
}
</script>
@endsection
