@extends('layouts.app')
@section('title', 'Hisobotlar')

@section('content')
<div x-data="reportsApp()">

    <!-- Modern Page Header -->
    <div class="bg-white px-8 py-6 relative overflow-hidden border-b border-slate-200">
        <!-- Abstract gradient background accent -->
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 rounded-full bg-gradient-to-br from-blue-300/30 to-purple-400/30 blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-10 w-40 h-40 rounded-full bg-gradient-to-tr from-emerald-300/20 to-teal-400/20 blur-2xl pointer-events-none"></div>

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 relative z-10 w-full max-w-7xl mx-auto">
            <div>
                <h1 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-slate-800 to-slate-500 tracking-tight">Hisobotlar</h1>
                <p class="text-sm text-slate-500 mt-1 font-medium flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    {{ \Carbon\Carbon::parse($startDate)->format('d.m.Y') }}
                    @if($startDate !== $endDate)
                        <span class="text-slate-300">—</span> {{ \Carbon\Carbon::parse($endDate)->format('d.m.Y') }}
                    @endif
                    oralig'idagi statistika
                </p>
            </div>

            <!-- Date Filter Form -->
            <form action="{{ url('/reports') }}" method="GET" class="flex items-center gap-3 bg-white/60 backdrop-blur-md p-1.5 rounded-2xl border border-slate-200/60 shadow-sm">
                <div class="flex items-center rounded-xl overflow-hidden divide-x divide-slate-100 bg-white">
                    <div class="relative group">
                        <input type="date" name="start_date" value="{{ $startDate }}"
                            class="pl-3 pr-2 py-2.5 text-sm font-medium text-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500/20 [color-scheme:light] bg-transparent cursor-pointer transition-all w-[135px]">
                    </div>
                    <div class="relative group">
                        <input type="date" name="end_date" value="{{ $endDate }}"
                            class="pl-3 pr-2 py-2.5 text-sm font-medium text-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500/20 [color-scheme:light] bg-transparent cursor-pointer transition-all w-[135px]">
                    </div>
                </div>
                <button type="submit"
                    class="flex items-center justify-center bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white p-2.5 rounded-xl transition-all shadow-md shadow-blue-500/30 group">
                    <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>

    <main class="p-8 max-w-7xl mx-auto space-y-8 relative">

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Jami tushum -->
            <div class="relative bg-white rounded-3xl p-6 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                <div class="absolute right-0 top-0 w-32 h-32 bg-gradient-to-br from-emerald-400/20 to-teal-500/10 rounded-full blur-2xl -mr-10 -mt-10 group-hover:scale-110 transition-transform duration-500"></div>
                <div class="relative z-10 flex items-start gap-5">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center shrink-0 shadow-lg shadow-emerald-500/30 text-white">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-1">Jami Tushum</p>
                        <h2 class="text-4xl font-black text-slate-800 tracking-tight">{{ number_format($totalRevenue, 0, ',', ' ') }} <span class="text-lg text-slate-400 font-semibold align-baseline">so'm</span></h2>
                    </div>
                </div>
            </div>

            <!-- Sotilgan mahsulotlar -->
            <div class="relative bg-white rounded-3xl p-6 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                <div class="absolute right-0 top-0 w-32 h-32 bg-gradient-to-br from-blue-400/20 to-indigo-500/10 rounded-full blur-2xl -mr-10 -mt-10 group-hover:scale-110 transition-transform duration-500"></div>
                <div class="relative z-10 flex items-start gap-5">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shrink-0 shadow-lg shadow-blue-500/30 text-white">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-1">Sotilgan Mahsulotlar</p>
                        <h2 class="text-4xl font-black text-slate-800 tracking-tight">{{ number_format($totalItemsSold, 0, ',', ' ') }} <span class="text-lg text-slate-400 font-semibold align-baseline">dona</span></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Breakdown Cards -->
        <div>
            <div class="flex items-center gap-3 mb-5">
                <div class="w-1.5 h-6 bg-gradient-to-b from-indigo-500 to-purple-500 rounded-full"></div>
                <h2 class="text-lg font-bold text-slate-800">Mahsulotlar kesimida statistika</h2>
            </div>

            @if(count($groupedProducts) > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                @foreach($groupedProducts as $stat)
                <div class="bg-white border text-center border-slate-200/60 rounded-2xl p-5 hover:shadow-[0_8px_30px_rgb(0,0,0,0.06)] hover:border-blue-200 transition-all group flex flex-col justify-center">
                    <div class="w-12 h-12 mx-auto rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center mb-3 group-hover:bg-blue-50 group-hover:border-blue-100 transition-colors">
                        <svg class="w-6 h-6 text-slate-400 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-bold text-slate-800 truncate mb-1" title="{{ $stat['product_name'] }}">{{ $stat['product_name'] }}</p>
                    <p class="text-xs font-semibold text-slate-400 mb-4 bg-slate-100 inline-block mx-auto px-2 py-0.5 rounded-full">{{ number_format($stat['total_sold'], 0) }} dona sotildi</p>
                    
                    <div class="pt-3 border-t border-slate-100 flex justify-between items-center">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Tushum</span>
                        <span class="text-sm font-black text-emerald-600">{{ number_format($stat['total_revenue'], 0, ',', ' ') }} so'm</span>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="bg-slate-50/50 border border-slate-200/60 rounded-3xl p-12 flex flex-col items-center justify-center text-center">
                <div class="w-16 h-16 rounded-full bg-white shadow-sm flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <h3 class="text-slate-800 font-semibold">Hech narsa topilmadi</h3>
                <p class="text-sm text-slate-500 mt-1 max-w-sm">Tanlangan davr oralig'ida sotilgan mahsulotlar mavjud emas. Boshqa sanani tanlab ko'ring.</p>
            </div>
            @endif
        </div>

        <!-- Detailed Sales Section -->
        <div>
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-1.5 h-6 bg-gradient-to-b from-teal-400 to-emerald-500 rounded-full"></div>
                    <h2 class="text-lg font-bold text-slate-800">Batafsil Savdolar</h2>
                    <span class="px-2.5 py-1 bg-slate-100 text-slate-600 font-bold text-xs rounded-lg">{{ count($sales) }} ta savdo</span>
                </div>
                
                <button @click="showDetailed = !showDetailed"
                    class="inline-flex items-center gap-2 bg-white border border-slate-200 shadow-sm hover:bg-slate-50 text-slate-700 text-sm font-semibold py-2 px-4 rounded-xl transition-all">
                    <span x-text="showDetailed ? 'Ro\'yxatni yashirish' : 'Barchasini ochish'"></span>
                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-300" :class="showDetailed ? 'rotate-180' : ''"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            </div>

            <div x-show="showDetailed" x-cloak
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="bg-white border border-slate-200/80 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden">

                @if(count($sales) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-bold text-slate-400 uppercase tracking-widest">
                                <th class="px-6 py-4 rounded-tl-3xl">Buyurtmachi & Sana</th>
                                <th class="px-6 py-4">Mahsulot nomi</th>
                                <th class="px-6 py-4 text-center">Miqdori</th>
                                <th class="px-6 py-4 text-right">Narxi (Dona)</th>
                                <th class="px-6 py-4 text-right">Summa</th>
                                <th class="px-6 py-4 text-right rounded-tr-3xl">Amallar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100/60">
                            @foreach($sales as $sale)
                                @foreach($sale->items as $idx => $item)
                                <tr class="hover:bg-blue-50/30 transition-colors {{ $idx === $sale->items->count() - 1 ? 'border-b-2 border-slate-100' : '' }}">
                                    <!-- Buyurtmachi & Sana -->
                                    <td class="px-6 py-3 min-w-[200px] align-top">
                                        @if($idx === 0)
                                        <div class="flex items-start gap-3 mt-1">
                                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-100 to-blue-50 border border-blue-100 flex items-center justify-center shrink-0 shadow-sm">
                                                <span class="text-sm font-black text-blue-700">
                                                    {{ mb_strtoupper(mb_substr($sale->customer->name ?? 'U', 0, 1)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-slate-800">{{ $sale->customer->name ?? 'Umumiy xaridor' }}</p>
                                                <div class="flex items-center gap-1.5 mt-0.5">
                                                    <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                    <span class="text-[11px] font-medium text-slate-500">{{ \Carbon\Carbon::parse($sale->sale_date)->format('d.m.Y') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        @else
                                        <div class="flex items-center pl-14 h-full">
                                            <div class="w-px h-8 bg-slate-200"></div>
                                            <div class="w-4 h-px bg-slate-200 mr-2"></div>
                                        </div>
                                        @endif
                                    </td>
                                    
                                    <!-- Mahsulot -->
                                    <td class="px-6 py-3">
                                        <span class="text-sm font-semibold {{ $item->product ? 'text-slate-700' : 'text-slate-400 italic' }}">
                                            {{ $item->product->name ?? 'O\'chirilgan mahsulot' }}
                                        </span>
                                    </td>
                                    
                                    <!-- Soni -->
                                    <td class="px-6 py-3 text-center">
                                        <span class="inline-flex items-center justify-center px-2.5 py-1 min-w-[3rem] bg-indigo-50 border border-indigo-100 text-indigo-700 font-bold text-xs rounded-lg shadow-sm">
                                            {{ $item->quantity }}
                                        </span>
                                    </td>
                                    
                                    <!-- Birlik Narxi -->
                                    <td class="px-6 py-3 text-right">
                                        <span class="text-sm font-medium text-slate-500">{{ number_format($item->unit_price, 0, ',', ' ') }} so'm</span>
                                    </td>
                                    
                                    <!-- Summa -->
                                    <td class="px-6 py-3 text-right">
                                        <span class="text-sm font-black text-emerald-600">{{ number_format($item->unit_price * $item->quantity, 0, ',', ' ') }} so'm</span>
                                    </td>
                                    
                                    <!-- Amallar faqat birinchi qatorda -->
                                    <td class="px-6 py-3 text-right align-top">
                                        @if($idx === 0)
                                        <div class="flex items-center justify-end">
                                            <button @click="confirmDelete({{ $sale->id }})" title="Ushbu savdoni o'chirish"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-xl text-slate-400 bg-white border border-slate-200 hover:text-red-600 hover:bg-red-50 hover:border-red-200 transition-all shadow-sm mt-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach

                                <!-- Sale Subtotal Footer -->
                                <tr class="bg-slate-50/50 border-b-[3px] border-slate-200/80">
                                    <td colspan="4" class="px-6 py-2.5">
                                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest ml-12">
                                            Savdo #{{ $sale->id }} • Jami {{ $sale->items->count() }} xil mahsulot • {{ $sale->items->sum('quantity') }} dona
                                        </p>
                                    </td>
                                    <td class="px-6 py-2.5 text-right font-black text-slate-800 text-sm">
                                        {{ number_format($sale->total_price, 0, ',', ' ') }} so'm
                                    </td>
                                    <td></td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white shadow-inner">
                            <tr>
                                <td colspan="4" class="px-6 py-5 text-sm font-bold uppercase tracking-widest opacity-90">Umumiy yig'indi davr uchun</td>
                                <td class="px-6 py-5 text-right text-xl font-black">
                                    {{ number_format($totalRevenue, 0, ',', ' ') }} so'm
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @else
                <div class="p-12 text-center text-slate-500 font-medium">Bu reyting davrida savdolar tushumi yozilmagan.</div>
                @endif
            </div>
        </div>
    </main>

    <!-- DELETE CONFIRMATION MODAL -->
    <div x-show="deleteId" x-cloak
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0">
        <div @click.outside="if(!isDeleting) deleteId = null" 
             class="bg-white rounded-3xl shadow-2xl w-full max-w-sm overflow-hidden"
             x-transition:enter="transition ease-out duration-300" 
             x-transition:enter-start="opacity-0 scale-90 translate-y-4" 
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200" 
             x-transition:leave-start="opacity-100 scale-100 translate-y-0" 
             x-transition:leave-end="opacity-0 scale-90 translate-y-4">
            
            <div class="p-6 text-center relative overflow-hidden">
                <div class="absolute inset-0 bg-red-50/50"></div>
                <div class="w-16 h-16 rounded-full bg-red-100 border-4 border-white shadow-sm flex items-center justify-center mx-auto mb-4 relative z-10 text-red-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
                <h3 class="text-lg font-black text-slate-800 mb-2 relative z-10">Savdoni o'chirish!</h3>
                <p class="text-sm font-medium text-slate-500 relative z-10 leading-relaxed px-2">Rostdan ham ushbu savdoni o'chirmoqchimisiz? O'chirilgach, mahsulotlar omborga qaytariladi va bu hisobotga o'zgarish kiritadi.</p>
            </div>
            
            <div class="p-5 flex gap-3 bg-slate-50 border-t border-slate-100">
                <button @click="deleteId = null" :disabled="isDeleting" 
                        class="flex-1 px-4 py-3 text-sm font-bold text-slate-600 bg-white border border-slate-200 shadow-sm rounded-xl hover:bg-slate-50 focus:ring-2 focus:ring-slate-200 transition-all">
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
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('reportsApp', () => ({
        showDetailed: true,
        deleteId: null,
        isDeleting: false,
        
        confirmDelete(id) {
            this.deleteId = id;
        },
        
        async executeDelete() {
            if (!this.deleteId) return;
            this.isDeleting = true;
            
            try {
                const response = await fetch(`/sales/${this.deleteId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Muvaffaqiyatli saqlangach sahifani qayta yuklaymiz.
                    window.location.reload();
                } else {
                    alert(data.message || 'Xatolik yuz berdi!');
                    this.isDeleting = false;
                    this.deleteId = null;
                }
            } catch (error) {
                alert('Xatolik: ' + error.message);
                this.isDeleting = false;
                this.deleteId = null;
            }
        }
    }));
});
</script>
<style>
[x-cloak] { display: none !important; }
</style>
@endsection
