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

        // Yangi arxitektura: sale → items → product
        $sales = Sale::with(['customer', 'items.product'])
            ->whereDate('sale_date', '>=', $startDate)
            ->whereDate('sale_date', '<=', $endDate)
            ->latest()
            ->get();

        // 1. Jami tushum va sotilgan soni (total_price dan olamiz — allaqachon hisoblangan)
        $totalRevenue   = $sales->sum('total_price');
        $totalItemsSold = $sales->sum(fn($s) => $s->items->sum('quantity'));

        // To'lov usullari bo'yicha breakdown
        $paymentSummary = [
            'naqd'   => $sales->where('payment_method', 'naqd')->sum('total_price'),
            'karta'  => $sales->where('payment_method', 'karta')->sum('total_price'),
            'nasiya' => $sales->where('payment_method', 'nasiya')->sum('total_price'),
        ];

        // Foyda hisobi: jami sotish narxi - jami tannarx
        $totalCost = $sales->sum(function ($sale) {
            return $sale->items->sum(function ($item) {
                return ($item->cost_price ?? 0) * $item->quantity;
            });
        });
        $totalProfit    = $totalRevenue - $totalCost;
        $profitPercent  = $totalRevenue > 0 ? round(($totalProfit / $totalRevenue) * 100, 1) : 0;

        // 2. Mahsulotlar kesimida guruhlash (sale_items orqali)
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
        // Har bir mahsulot uchun foydani hisoblash
        foreach ($productStats as &$stat) {
            $stat['total_profit'] = $stat['total_revenue'] - $stat['total_cost'];
        }
        unset($stat);
        arsort($productStats); // eng ko'p sotilgandan saralash
        $groupedProducts = array_values($productStats);

        return view('reports.index', compact(
            'sales', 'startDate', 'endDate',
            'totalRevenue', 'totalItemsSold', 'groupedProducts', 'paymentSummary',
            'totalCost', 'totalProfit', 'profitPercent'
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
            'success' => true,
            'message' => Carbon::parse($date)->format('d.m.Y') . " sanasidagi {$count} ta savdo o'chirildi. Mahsulotlar omborga qaytarildi.",
            'deleted_count' => $count,
        ]);
    }
}
