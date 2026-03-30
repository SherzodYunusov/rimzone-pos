<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index()
    {
        $sales     = Sale::with(['customer', 'items.product'])->latest()->get();
        $products  = Product::orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();

        return view('sales.index', compact('sales', 'products', 'customers'));
    }

    /**
     * Savatcha asosida yangi savdo yaratish.
     * Body: { customer_id, sale_date, payment_method, due_date?, items: [{product_id, quantity}] }
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id'        => 'nullable|exists:customers,id',
            'sale_date'          => 'required|date',
            'payment_method'     => 'required|in:naqd,karta,nasiya',
            'due_date'           => 'nullable|date',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|numeric|min:0.001',
        ]);

        // Nasiya uchun mijoz tanlash majburiy
        if ($data['payment_method'] === 'nasiya' && empty($data['customer_id'])) {
            return response()->json([
                'success' => false,
                'message' => 'Nasiya to\'lovida mijoz tanlash shart! Iltimos, mijozni tanlang yoki yangi mijoz qo\'shing.',
            ], 422);
        }

        try {
            $sale = DB::transaction(function () use ($data) {

                $totalPrice = 0;
                $lines      = [];

                foreach ($data['items'] as $item) {
                    $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                    if ($product->quantity < $item['quantity']) {
                        $unit = $product->unit ?: 'dona';
                        throw new \Exception(
                            "«{$product->name}» uchun yetarli stok yo'q. Omborda: {$product->quantity} {$unit}."
                        );
                    }

                    $lines[] = [
                        'product'    => $product,
                        'quantity'   => (float) $item['quantity'],
                        'unit_price' => (float) $product->price,
                        'cost_price' => $product->cost_price ? (float) $product->cost_price : null,
                    ];
                    $totalPrice += $product->price * $item['quantity'];
                }

                $isNasiya = $data['payment_method'] === 'nasiya';

                $sale = Sale::create([
                    'customer_id'    => $data['customer_id'] ?? null,
                    'total_price'    => $totalPrice,
                    'sale_date'      => $data['sale_date'],
                    'payment_method' => $data['payment_method'],
                    'status'         => $isNasiya ? 'debt' : 'paid',
                    'paid_amount'    => $isNasiya ? 0 : $totalPrice,
                    'due_date'       => $isNasiya ? ($data['due_date'] ?? null) : null,
                ]);

                foreach ($lines as $line) {
                    $sale->items()->create([
                        'product_id' => $line['product']->id,
                        'quantity'   => $line['quantity'],
                        'unit_price' => $line['unit_price'],
                        'cost_price' => $line['cost_price'],
                    ]);

                    $line['product']->decrement('quantity', $line['quantity']);
                }

                return $sale;
            });

            $sale->load(['customer', 'items.product']);

            return response()->json([
                'success' => true,
                'message' => 'Savdo muvaffaqiyatli amalga oshirildi!',
                'sale'    => $sale,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function show(Sale $sale)
    {
        $sale->load(['customer', 'items.product']);
        return response()->json(['success' => true, 'sale' => $sale]);
    }

    /**
     * Nasiya qarzini qisman yoki to'liq to'lash.
     * Body: { amount, payment_date, notes? }
     */
    public function pay(Request $request, Sale $sale)
    {
        $data = $request->validate([
            'amount'       => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'notes'        => 'nullable|string|max:255',
        ]);

        if ($sale->status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Bu savdo allaqachon to\'liq to\'langan.',
            ], 422);
        }

        $remaining = $sale->remaining_debt;
        if ($remaining <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Qarz yo\'q — to\'lash shart emas.',
            ], 422);
        }

        DB::transaction(function () use ($sale, $data) {
            // To'lov miqdorini qolgan qarzdan oshirmaymiz
            $payAmount = min((float) $data['amount'], $sale->remaining_debt);

            SalePayment::create([
                'sale_id'      => $sale->id,
                'amount'       => $payAmount,
                'payment_date' => $data['payment_date'],
                'notes'        => $data['notes'] ?? null,
            ]);

            $newPaid = (float) $sale->paid_amount + $payAmount;
            $total   = (float) $sale->total_price;

            $sale->paid_amount = $newPaid;
            $sale->status      = $newPaid >= $total ? 'paid' : ($newPaid > 0 ? 'partial' : 'debt');
            $sale->save();
        });

        $sale->refresh();

        return response()->json([
            'success'        => true,
            'message'        => 'To\'lov muvaffaqiyatli qabul qilindi!',
            'remaining_debt' => $sale->remaining_debt,
            'status'         => $sale->status,
        ]);
    }

    public function destroy(Sale $sale)
    {
        try {
            DB::transaction(function () use ($sale) {
                $sale->load('items');
                foreach ($sale->items as $item) {
                    Product::whereKey($item->product_id)->increment('quantity', $item->quantity);
                }
                $sale->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Savdo o\'chirildi va mahsulotlar omborga qaytarildi.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
