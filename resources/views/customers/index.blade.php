@extends('layouts.app')
@section('title', 'Mijozlar')

@section('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV/XN/sp38=" crossorigin=""></script>
@endsection

@section('content')
<div x-data="customerApp()" x-init="init()">

    <!-- Page Header -->
    <div class="h-14 md:h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 md:px-8">
        <div>
            <h1 class="text-lg font-semibold text-slate-800">Mijozlar</h1>
            <p class="text-xs text-slate-400" x-text="`${customers.length} ta mijoz`"></p>
        </div>
        <button @click="openAddModal()"
            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm py-2 px-3 md:px-4 rounded-lg transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span class="hidden sm:inline">Mijoz qo'shish</span>
        </button>
    </div>

    <!-- Main -->
    <main class="p-4 md:p-8">

        <!-- Empty state -->
        <div x-show="customers.length === 0" class="flex flex-col items-center justify-center py-24">
            <div class="w-14 h-14 rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-center mb-4">
                <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-slate-600 mb-1">Hozircha mijozlar yo'q</p>
            <p class="text-xs text-slate-400 mb-4">Birinchi mijozingizni qo'shing</p>
            <button @click="openAddModal()"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Qo'shish
            </button>
        </div>

        <!-- Customers Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-4">
            <template x-for="(customer, index) in customers" :key="customer.id">
                <div class="bg-white border border-slate-200 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden flex flex-col"
                     :style="`animation: fadeInUp .3s ease both; animation-delay: ${index * 40}ms`">

                    <!-- Avatar / Photo / Map area -->
                    <div class="relative flex-shrink-0 overflow-hidden bg-slate-50">
                        <!-- Leaflet mini-map (when lat/lng exist) -->
                        <template x-if="customer.lat && customer.lng">
                            <div>
                                <div :id="`map-${customer.id}`" class="w-full" style="height:120px; z-index:0;"></div>
                                <a :href="`https://www.google.com/maps/dir/?api=1&destination=${customer.lat},${customer.lng}`"
                                   target="_blank"
                                   class="absolute bottom-2 right-2 z-10 inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg shadow-lg transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Navigatsiya
                                </a>
                            </div>
                        </template>
                        <!-- Photo (when no geo but photo exists) -->
                        <template x-if="!customer.lat && customer.photo_url">
                            <div class="h-24">
                                <img :src="customer.photo_url" class="w-full h-full object-cover">
                            </div>
                        </template>
                        <!-- Avatar fallback -->
                        <template x-if="!customer.lat && !customer.photo_url">
                            <div class="h-24 flex items-center justify-center">
                                <div class="w-14 h-14 rounded-xl bg-blue-100 flex items-center justify-center">
                                    <span class="text-2xl font-bold text-blue-600" x-text="customer.name.charAt(0).toUpperCase()"></span>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Info -->
                    <div class="p-5 flex-1 space-y-3">
                        <div>
                            <h3 class="font-bold text-slate-800" x-text="customer.name"></h3>
                            <p class="text-[11px] font-bold text-blue-600 uppercase tracking-wider mt-0.5" x-text="customer.company_name"></p>
                        </div>

                        <div class="space-y-2">
                            <div class="flex items-center gap-3 p-2 bg-slate-50 rounded-xl border border-slate-100 group/item hover:bg-white hover:border-blue-200 transition-all">
                                <div class="w-8 h-8 rounded-lg bg-white shadow-sm flex items-center justify-center text-slate-400 group-hover/item:text-blue-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                </div>
                                <span class="text-xs font-semibold text-slate-600" x-text="customer.phone"></span>
                            </div>
                            <div class="flex items-center gap-3 p-2 bg-slate-50 rounded-xl border border-slate-100 group/item hover:bg-white hover:border-blue-200 transition-all">
                                <div class="w-8 h-8 rounded-lg bg-white shadow-sm flex items-center justify-center text-slate-400 group-hover/item:text-emerald-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <span class="text-xs font-semibold text-slate-600 truncate" x-text="customer.address"></span>
                            </div>

                            <!-- Xaritada ko'rish — faqat koordinata bo'lganda -->
                            <template x-if="customer.lat && customer.lng">
                                <a :href="`https://www.google.com/maps?q=${customer.lat},${customer.lng}`"
                                   target="_blank"
                                   class="flex items-center justify-center gap-2 w-full py-2 px-3 bg-blue-50 hover:bg-blue-100 border border-blue-200 hover:border-blue-300 text-blue-700 text-xs font-semibold rounded-xl transition-colors">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                    </svg>
                                    Xaritada ko'rish
                                </a>
                            </template>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="border-t border-slate-100 flex">
                        <button @click="openEditModal(customer)"
                            class="flex-1 flex items-center justify-center gap-1.5 py-2.5 text-xs font-medium text-slate-500 hover:text-blue-600 hover:bg-blue-50 transition-colors border-r border-slate-100">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Tahrirlash
                        </button>
                        <button @click="openDeleteModal(customer)"
                            class="flex-1 flex items-center justify-center gap-1.5 py-2.5 text-xs font-medium text-slate-500 hover:text-red-600 hover:bg-red-50 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            O'chirish
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </main>

    <!-- ===== ADD / EDIT MODAL ===== -->
    <div x-show="isFormOpen" style="display:none"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div @click.outside="closeForm()" @click.stop
             class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-lg overflow-hidden max-h-[90vh] flex flex-col"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-90 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-90 translate-y-4">

            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 flex-shrink-0">
                <h2 class="font-semibold text-slate-800" x-text="editingId ? 'Mijozni tahrirlash' : 'Yangi mijoz qo\'shish'"></h2>
                <button @click="closeForm()" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="p-4 sm:p-6 space-y-4 overflow-y-auto">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Ismi <span class="text-red-500">*</span></label>
                        <input type="text" x-model="form.name" placeholder="To'liq ismi"
                            class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 text-slate-700 placeholder-slate-400">
                        <p x-show="errors.name" class="text-red-500 text-xs mt-1" x-text="errors.name"></p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Telefon <span class="text-red-500">*</span></label>
                        <input type="tel" x-model="form.phone" placeholder="+998 90 123 45 67"
                            class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 text-slate-700 placeholder-slate-400">
                        <p x-show="errors.phone" class="text-red-500 text-xs mt-1" x-text="errors.phone"></p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Manzil <span class="text-red-500">*</span></label>
                        <input type="text" x-model="form.address" placeholder="Shahar, ko'cha"
                            class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 text-slate-700 placeholder-slate-400">
                        <p x-show="errors.address" class="text-red-500 text-xs mt-1" x-text="errors.address"></p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Korxona nomi <span class="text-red-500">*</span></label>
                        <input type="text" x-model="form.company_name" placeholder="OOO, AJ..."
                            class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 text-slate-700 placeholder-slate-400">
                        <p x-show="errors.company_name" class="text-red-500 text-xs mt-1" x-text="errors.company_name"></p>
                    </div>
                </div>

                <!-- Google Maps Link input -->
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">
                        Google Maps Linkini tashlang
                        <span class="text-slate-400">(ixtiyoriy — joylashuv avtomatik aniqlanadi)</span>
                    </label>
                    <div class="relative">
                        <input type="url" x-model="form.map_link" @input="parseMapLink($event.target.value)"
                               placeholder="https://maps.google.com/..."
                               class="w-full px-3 py-2.5 pr-10 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 text-slate-700 placeholder-slate-400">
                        <div x-show="form.lat && form.lng" class="absolute right-3 top-1/2 -translate-y-1/2">
                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    </div>
                    <!-- Muvaffaqiyatli: koordinatalar -->
                    <div x-show="form.lat && form.lng" class="mt-2 flex items-center gap-2 text-xs text-emerald-600 font-medium">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        </svg>
                        <span x-text="`${Number(form.lat).toFixed(6)}, ${Number(form.lng).toFixed(6)}`"></span>
                        <button type="button" @click="form.lat=''; form.lng=''; form.map_link=''; mapLinkHint=''"
                                class="ml-auto text-slate-400 hover:text-red-500 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Xato: qisqartirilgan havola (maps.app.goo.gl) -->
                    <div x-show="mapLinkHint === 'shortened'" class="mt-2 flex items-start gap-2 text-xs bg-amber-50 border border-amber-200 text-amber-800 rounded-lg px-3 py-2">
                        <svg class="w-3.5 h-3.5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                        <span>
                            <strong>maps.app.goo.gl</strong> qisqartirilgan havola — o'qib bo'lmaydi.<br>
                            Brauzerda Google Maps ni oching → manzil satridan <strong>to'liq URL</strong> ni ko'chiring.
                        </span>
                    </div>

                    <!-- Koordinata topilmadi -->
                    <div x-show="mapLinkHint === 'notfound'" class="mt-2 text-xs text-slate-400">
                        Koordinata topilmadi — Google Maps dan to'liq havola ko'chiring
                    </div>
                </div>

                <!-- Photo upload -->
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Rasm <span class="text-slate-400">(ixtiyoriy)</span></label>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden flex items-center justify-center shrink-0">
                            <img x-show="photoPreview" :src="photoPreview" class="w-full h-full object-cover">
                            <svg x-show="!photoPreview" class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <label class="flex-1 cursor-pointer flex items-center gap-2 px-3 py-2.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 border-dashed rounded-lg text-sm text-slate-500 hover:text-slate-700 transition-colors">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <span x-text="photoName || 'Rasm tanlang (jpg, png)'"></span>
                            <input type="file" id="photoInput" @change="handlePhoto($event)" accept="image/*" class="hidden">
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex gap-3 px-6 py-4 border-t border-slate-200 bg-slate-50 rounded-b-xl flex-shrink-0">
                <button @click="closeForm()" class="flex-1 px-4 py-2.5 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">Bekor qilish</button>
                <button @click="submitForm()" :disabled="loading"
                    class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors disabled:opacity-60 flex items-center justify-center gap-2">
                    <svg x-show="loading" class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    <span x-text="loading ? 'Saqlanmoqda...' : 'Saqlash'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- ===== DELETE MODAL ===== -->
    <div x-show="isDeleteOpen" style="display:none"
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
                <h3 class="font-semibold text-slate-800 mb-1">Rostdan ham o'chirmoqchimisiz?</h3>
                <p class="text-sm text-slate-500"><span class="font-medium text-slate-700" x-text="`«${deleteTarget?.name}»`"></span> mijozi butunlay o'chiriladi.</p>
            </div>
            <div class="flex gap-3 px-6 pb-6">
                <button @click="cancelDelete()" class="flex-1 px-4 py-2.5 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">Yo'q</button>
                <button @click="confirmDelete()" :disabled="loading" class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors disabled:opacity-60">Ha, o'chirish</button>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<style>
@keyframes fadeInUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
/* Prevent Leaflet tiles from interfering with layout */
.leaflet-container { font-size: 12px; }
</style>
<script>
// ── Google Maps URL → lat/lng extractor ──────────────────────────────────
// Prioritet: !3d!4d (aniq pin) > /@ (xarita markazi) > q= > query= > ll=
function extractLatLng(url) {
    if (!url) return null;

    // Qisqartirilgan havola — parse qilib bo'lmaydi
    if (/maps\.app\.goo\.gl|goo\.gl\/maps/i.test(url)) {
        return { error: 'shortened' };
    }

    // URL encoding ni ochish
    let u;
    try { u = decodeURIComponent(url); } catch(e) { u = url; }

    // 1. !3dlat!4dlng — ENG ANIQ (joylashuvning exact pin koordinatasi)
    let m = u.match(/!3d(-?\d+\.?\d*)!4d(-?\d+\.?\d*)/);
    if (m) return { lat: parseFloat(m[1]), lng: parseFloat(m[2]) };

    // 2. /@lat,lng — xarita ko'rinish markazi (viewport center)
    m = u.match(/@(-?\d+\.\d+),(-?\d+\.\d+)/);
    if (m) return { lat: parseFloat(m[1]), lng: parseFloat(m[2]) };

    // 3. ?q=lat,lng yoki &q=lat,lng
    m = u.match(/[?&]q=(-?\d+\.?\d*),(-?\d+\.?\d*)/);
    if (m) return { lat: parseFloat(m[1]), lng: parseFloat(m[2]) };

    // 4. query=lat,lng (Google Maps API format)
    m = u.match(/[?&]query=(-?\d+\.?\d*),(-?\d+\.?\d*)/);
    if (m) return { lat: parseFloat(m[1]), lng: parseFloat(m[2]) };

    // 5. ?ll=lat,lng (eski Google Maps format)
    m = u.match(/[?&]ll=(-?\d+\.?\d*),(-?\d+\.?\d*)/);
    if (m) return { lat: parseFloat(m[1]), lng: parseFloat(m[2]) };

    // 6. /lat,lng yo'l ichida (ba'zi mobil havolalar)
    m = u.match(/\/(-?\d{1,3}\.\d{4,}),(-?\d{1,3}\.\d{4,})/);
    if (m) return { lat: parseFloat(m[1]), lng: parseFloat(m[2]) };

    return null;
}

// ── Leaflet map registry (id → map instance) ─────────────────────────────
const _leafletMaps = {};

function initCustomerMap(customerId, lat, lng) {
    const containerId = `map-${customerId}`;
    // Destroy existing map if re-rendering
    if (_leafletMaps[customerId]) {
        _leafletMaps[customerId].remove();
        delete _leafletMaps[customerId];
    }
    const el = document.getElementById(containerId);
    if (!el) return;
    const map = L.map(containerId, {
        zoomControl: false,
        dragging: false,
        scrollWheelZoom: false,
        doubleClickZoom: false,
        touchZoom: false,
        attributionControl: false,
    }).setView([lat, lng], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    const icon = L.divIcon({
        html: `<div style="width:24px;height:24px;background:#2563eb;border:3px solid white;border-radius:50%;box-shadow:0 2px 8px rgba(0,0,0,.35);"></div>`,
        iconSize: [24, 24],
        iconAnchor: [12, 12],
        className: '',
    });
    L.marker([lat, lng], { icon }).addTo(map);
    _leafletMaps[customerId] = map;
}

function customerApp() {
    return {
        customers: {!! json_encode($customers) !!},
        isFormOpen: false,
        isDeleteOpen: false,
        loading: false,
        editingId: null,
        deleteTarget: null,
        photoPreview: null,
        photoName: null,
        photoFile: null,
        form: { name: '', phone: '', address: '', company_name: '', map_link: '', lat: '', lng: '' },
        errors: {},
        mapLinkHint: '',   // '' | 'shortened' | 'notfound'

        init() {
            // Render maps for all customers that have coordinates
            this.$nextTick(() => this.renderAllMaps());
        },

        renderAllMaps() {
            this.customers.forEach(c => {
                if (c.lat && c.lng) {
                    this.$nextTick(() => initCustomerMap(c.id, c.lat, c.lng));
                }
            });
        },

        parseMapLink(url) {
            if (!url) { this.form.lat = ''; this.form.lng = ''; this.mapLinkHint = ''; return; }
            const coords = extractLatLng(url);
            if (coords && coords.error === 'shortened') {
                this.form.lat = ''; this.form.lng = '';
                this.mapLinkHint = 'shortened';
            } else if (coords) {
                this.form.lat = coords.lat;
                this.form.lng = coords.lng;
                this.mapLinkHint = '';
            } else {
                this.form.lat = ''; this.form.lng = '';
                this.mapLinkHint = 'notfound';
            }
        },

        handlePhoto(event) {
            const file = event.target.files[0];
            if (!file) return;
            this.photoFile = file;
            this.photoName = file.name;
            const reader = new FileReader();
            reader.onload = e => { this.photoPreview = e.target.result; };
            reader.readAsDataURL(file);
        },
        resetPhoto() { this.photoPreview = null; this.photoName = null; this.photoFile = null; const inp = document.getElementById('photoInput'); if (inp) inp.value = ''; },
        openAddModal() {
            this.editingId = null;
            this.form = { name: '', phone: '', address: '', company_name: '', map_link: '', lat: '', lng: '' };
            this.errors = {};
            this.mapLinkHint = '';
            this.resetPhoto();
            this.isFormOpen = true;
        },
        openEditModal(customer) {
            this.editingId = customer.id;
            this.form = {
                name: customer.name,
                phone: customer.phone,
                address: customer.address,
                company_name: customer.company_name,
                map_link: customer.map_link || '',
                lat: customer.lat ?? '',
                lng: customer.lng ?? '',
            };
            this.errors = {};
            this.mapLinkHint = '';
            this.photoPreview = customer.photo_url || null;
            this.photoName = null;
            this.photoFile = null;
            this.isFormOpen = true;
        },
        closeForm() { this.isFormOpen = false; setTimeout(() => { this.editingId = null; this.errors = {}; this.resetPhoto(); }, 200); },
        submitForm() {
            this.errors = {};
            if (!this.form.name.trim()) { this.errors.name = 'Majburiy!'; return; }
            if (!this.form.phone.trim()) { this.errors.phone = 'Majburiy!'; return; }
            if (!this.form.address.trim()) { this.errors.address = 'Majburiy!'; return; }
            if (!this.form.company_name.trim()) { this.errors.company_name = 'Majburiy!'; return; }
            this.loading = true;
            const fd = new FormData();
            fd.append('name', this.form.name);
            fd.append('phone', this.form.phone);
            fd.append('address', this.form.address);
            fd.append('company_name', this.form.company_name);
            fd.append('map_link', this.form.map_link ?? '');
            fd.append('lat',      (this.form.lat !== null && this.form.lat !== undefined) ? this.form.lat : '');
            fd.append('lng',      (this.form.lng !== null && this.form.lng !== undefined) ? this.form.lng : '');
            fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            if (this.photoFile) fd.append('photo', this.photoFile);
            const url = this.editingId ? `/customers/${this.editingId}` : '/customers';
            if (this.editingId) fd.append('_method', 'PUT');
            fetch(url, {
                method: 'POST',
                body: fd,
                headers: { 'Accept': 'application/json' }
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
                        const idx = this.customers.findIndex(c => c.id === this.editingId);
                        if (idx !== -1) this.customers[idx] = data.customer;
                    } else {
                        this.customers.unshift(data.customer);
                    }
                    this.closeForm();
                    this.showNotif(data.message, 'success');
                    // Re-render maps after DOM updates
                    this.$nextTick(() => this.renderAllMaps());
                }
            }).catch(e => {
                if (e.message !== 'Validation error') this.showNotif(e.message, 'error');
            }).finally(() => { this.loading = false; });
        },
        openDeleteModal(customer) { this.deleteTarget = customer; this.isDeleteOpen = true; },
        cancelDelete() { this.isDeleteOpen = false; setTimeout(() => { this.deleteTarget = null; }, 200); },
        confirmDelete() {
            if (!this.deleteTarget) return; this.loading = true; const id = this.deleteTarget.id;
            fetch(`/customers/${id}`, {
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
                    this.customers = this.customers.filter(c => c.id !== id);
                    this.cancelDelete();
                    this.showNotif(data.message, 'success');
                }
            }).catch(e => this.showNotif(e.message, 'error')).finally(() => { this.loading = false; });
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
    };
}
</script>
@endsection
