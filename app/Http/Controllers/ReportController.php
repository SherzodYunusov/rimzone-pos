<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::today()->format('Y-m-d'));
        $endDate   = $request->input('end_date',   Carbon::today()->format('Y-m-d'));

        // Tanlangan davr uchun savdolar (period-filtered)
        $sales = Sale::with(['customer', 'items.product'])
            ->whereDate('sale_date', '>=', $startDate)
            ->whereDate('sale_date', '<=', $endDate)
            ->latest()
            ->get();

        // ── Davr bo'yicha asosiy ko'rsatkichlar ─────────────────
        $totalRevenue   = $sales->sum('total_price');
        $totalItemsSold = $sales->sum(fn($s) => $s->items->sum('quantity'));

        // To'lov usullari bo'yicha breakdown
        $paymentSummary = [
            'naqd'   => $sales->where('payment_method', 'naqd')->sum('total_price'),
            'karta'  => $sales->where('payment_method', 'karta')->sum('total_price'),
            'nasiya' => $sales->where('payment_method', 'nasiya')->sum('total_price'),
        ];

        // Foyda hisobi
        $totalCost = $sales->sum(function ($sale) {
            return $sale->items->sum(function ($item) {
                return ($item->cost_price ?? 0) * $item->quantity;
            });
        });
        $totalProfit   = $totalRevenue - $totalCost;
        $profitPercent = $totalRevenue > 0 ? round(($totalProfit / $totalRevenue) * 100, 1) : 0;

        // ── Haqiqiy naqd foyda (Real Cash Profit) ───────────────────
        // Formula: (paid_amount / total_price) * sale_profit for each sale
        $realCashProfit = $sales->sum(function ($sale) {
            $saleRevenue = (float) $sale->total_price;
            if ($saleRevenue <= 0) return 0;
            $saleCost   = $sale->items->sum(fn($item) => ($item->cost_price ?? 0) * $item->quantity);
            $saleProfit = $saleRevenue - $saleCost;
            $paidRatio  = min(1.0, (float) $sale->paid_amount / $saleRevenue);
            return $paidRatio * $saleProfit;
        });

        // ── Kassa ko'rsatkichlari ─────────────────────────────────────
        $cashReceived = $sales->sum(fn($s) => (float) $s->paid_amount);
        $debtInPeriod = $totalRevenue - $cashReceived;
        $cashRatio    = $totalRevenue > 0 ? round(($cashReceived / $totalRevenue) * 100, 1) : 0;
        $profitInDebt = $totalProfit - $realCashProfit;

        // ── Mahsulotlar kesimida statistika ──────────────────────
        $productStats = [];
        foreach ($sales as $sale) {
            foreach ($sale->items as $item) {
                $pid = $item->product_id;
                if (!isset($productStats[$pid])) {
                    $productStats[$pid] = [
                        'product_name'  => $item->product->name ?? 'O\'chirilgan',
                        'total_sold'    => 0,
                        'total_revenue' => 0,
                        'total_cost'    => 0,
                    ];
                }
                $productStats[$pid]['total_sold']    += $item->quantity;
                $productStats[$pid]['total_revenue'] += $item->unit_price * $item->quantity;
                $productStats[$pid]['total_cost']    += ($item->cost_price ?? 0) * $item->quantity;
            }
        }
        foreach ($productStats as &$stat) {
            $stat['total_profit'] = $stat['total_revenue'] - $stat['total_cost'];
        }
        unset($stat);
        arsort($productStats);
        $groupedProducts = array_values($productStats);

        // ── Top 5 foydali mahsulotlar (davr bo'yicha) ───────────
        $topProfitProducts = collect($groupedProducts)
            ->sortByDesc('total_profit')
            ->take(5)
            ->values()
            ->toArray();

        // ── Ombor qiymati (barcha mahsulotlar) ──────────────────
        $inventoryValue = Product::whereNotNull('cost_price')
            ->where('cost_price', '>', 0)
            ->get()
            ->sum(fn($p) => (float) $p->cost_price * (float) $p->quantity);

        $inventoryCount = Product::count();
        $inventoryItems = Product::select('name', 'cost_price', 'quantity')
            ->whereNotNull('cost_price')
            ->where('cost_price', '>', 0)
            ->where('quantity', '>', 0)
            ->orderByDesc(DB::raw('cost_price * quantity'))
            ->take(5)
            ->get();

        // ── Nasiya qarzlari (barcha vaqt, period filtrsiz) ──────
        $allDebtSales = Sale::with(['customer', 'payments'])
            ->where('payment_method', 'nasiya')
            ->whereIn('status', ['debt', 'partial'])
            ->orderBy('due_date')
            ->orderBy('created_at')
            ->get();

        $today        = Carbon::today();
        $totalNasiya  = $allDebtSales->sum(fn($s) => $s->remaining_debt);
        $overdueNasiya = $allDebtSales
            ->filter(fn($s) => $s->due_date && $s->due_date->lt($today))
            ->sum(fn($s) => $s->remaining_debt);

        // Bu oy yig'ib olingan qarz to'lovlari
        $totalCollectedDebt = \App\Models\SalePayment::where('payment_date', '>=', Carbon::now()->startOfMonth())
            ->sum('amount');

        // Eng katta qarzdor
        $biggestDebtor = $allDebtSales->sortByDesc(fn($s) => $s->remaining_debt)->first();

        return view('reports.index', compact(
            'sales', 'startDate', 'endDate',
            'totalRevenue', 'totalItemsSold', 'groupedProducts', 'paymentSummary',
            'totalCost', 'totalProfit', 'profitPercent',
            'realCashProfit', 'cashReceived', 'debtInPeriod', 'cashRatio', 'profitInDebt',
            'topProfitProducts', 'inventoryValue', 'inventoryCount', 'inventoryItems',
            'allDebtSales', 'totalNasiya', 'overdueNasiya', 'today',
            'totalCollectedDebt', 'biggestDebtor'
        ));
    }

    /**
     * Tanlangan sana uchun barcha savdolarni o'chirish va mahsulotlarni omborga qaytarish.
     */
    public function clearDay(Request $request)
    {
        $request->validate(['date' => 'required|date']);
        $date = $request->input('date');

        $sales = Sale::with('items')->whereDate('sale_date', $date)->get();

        if ($sales->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => Carbon::parse($date)->format('d.m.Y') . " sanasida hech qanday savdo topilmadi.",
            ], 404);
        }

        $count = $sales->count();

        DB::transaction(function () use ($sales) {
            foreach ($sales as $sale) {
                foreach ($sale->items as $item) {
                    Product::whereKey($item->product_id)->increment('quantity', $item->quantity);
                }
                $sale->delete();
            }
        });

        return response()->json([
            'success'       => true,
            'message'       => Carbon::parse($date)->format('d.m.Y') . " sanasidagi {$count} ta savdo o'chirildi. Mahsulotlar omborga qaytarildi.",
            'deleted_count' => $count,
        ]);
    }
}
