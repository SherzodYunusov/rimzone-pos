<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Barcha mahsulotlarni display qilish
     */
    public function index()
    {
        $products = Product::all();
        return view('products.index', compact('products'));
    }

    /**
     * Yangi mahsulot qo'shish (AJAX)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        // Mahsulot nomini katta harflarga aylantir
        $validated['name'] = strtoupper($validated['name']);

        $product = Product::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Mahsulot muvaffaqiyatli qo\'shildi!',
            'product' => $product,
        ]);
    }

    /**
     * Mahsulotni tahrirlash uchun ma'lumot olish
     */
    public function edit(Product $product)
    {
        return response()->json($product);
    }

    /**
     * Mahsulotni yangilash (AJAX)
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        // Mahsulot nomini katta harflarga aylantir
        $validated['name'] = strtoupper($validated['name']);

        $product->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Mahsulot muvaffaqiyatli yangilandi!',
            'product' => $product,
        ]);
    }

    /**
     * Mahsulotni o'chirish (AJAX)
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mahsulot muvaffaqiyatli o\'chirildi!',
        ]);
    }
}
