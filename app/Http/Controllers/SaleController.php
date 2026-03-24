<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index()
    {
        $sales    = Sale::with(['customer', 'items.product'])->latest()->get();
        $products = Product::orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();

        return view('sales.index', compact('sales', 'products', 'customers'));
    }

    /**
     * Savatcha asosida yangi savdo yaratish.
     * Body: { customer_id, sale_date, items: [{product_id, quantity}, ...] }
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id'        => 'nullable|exists:customers,id',
            'sale_date'          => 'required|date',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
        ]);

        try {
            // &$sale o'rniga DB::transaction dan qaytaramiz (to'g'ri usul)
            $sale = DB::transaction(function () use ($data) {

                $totalPrice = 0;
                $lines      = [];

                foreach ($data['items'] as $item) {
                    // Pessimistic locking — bir vaqtda 2 foydalanuvchi xuddi shu mahsulotni sotmasin
                    $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                    if ($product->quantity < $item['quantity']) {
                        throw new \Exception(
                            "«{$product->name}» uchun yetarli stok yo'q. Omborda: {$product->quantity} dona."
                        );
                    }

                    $lines[] = [
                        'product'    => $product,
                        'quantity'   => (int) $item['quantity'],
                        'unit_price' => (float) $product->price,
                    ];
                    $totalPrice += $product->price * $item['quantity'];
                }

                $sale = Sale::create([
                    'customer_id' => $data['customer_id'] ?? null,
                    'total_price' => $totalPrice,
                    'sale_date'   => $data['sale_date'],
                ]);

                foreach ($lines as $line) {
                    $sale->items()->create([
                        'product_id' => $line['product']->id,
                        'quantity'   => $line['quantity'],
                        'unit_price' => $line['unit_price'],
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

    public function destroy(Sale $sale)
    {
        try {
            DB::transaction(function () use ($sale) {
                // Eager load before delete so we have item data available
                $sale->load('items');
                foreach ($sale->items as $item) {
                    Product::whereKey($item->product_id)->increment('quantity', $item->quantity);
                }
                $sale->delete(); // onDelete('cascade') ile SaleItems ham o'chadi
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
