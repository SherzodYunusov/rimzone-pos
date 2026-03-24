<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
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
                    ];
                }
                $productStats[$pid]['total_sold']    += $item->quantity;
                $productStats[$pid]['total_revenue'] += $item->unit_price * $item->quantity;
            }
        }
        arsort($productStats); // eng ko'p sotilgandan saralash
        $groupedProducts = array_values($productStats);

        return view('reports.index', compact(
            'sales', 'startDate', 'endDate',
            'totalRevenue', 'totalItemsSold', 'groupedProducts'
        ));
    }
}
