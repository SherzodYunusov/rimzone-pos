<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::latest()->get()->map(function ($c) {
            $c->photo_url = $c->photo ? asset('storage/' . $c->photo) : null;
            return $c;
        });
        return view('customers.index', compact('customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'phone'        => 'required|string|max:50',
            'address'      => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'photo'        => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:4096',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('customers', 'public');
        }

        $customer = Customer::create($validated);
        $customer->photo_url = $customer->photo ? asset('storage/' . $customer->photo) : null;

        return response()->json([
            'success'  => true,
            'message'  => "Mijoz muvaffaqiyatli qo'shildi!",
            'customer' => $customer,
        ]);
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'phone'        => 'required|string|max:50',
            'address'      => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'photo'        => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:4096',
        ]);

        if ($request->hasFile('photo')) {
            // Eski rasmni o'chirish
            if ($customer->photo) {
                Storage::disk('public')->delete($customer->photo);
            }
            $validated['photo'] = $request->file('photo')->store('customers', 'public');
        }

        $customer->update($validated);
        $customer->photo_url = $customer->photo ? asset('storage/' . $customer->photo) : null;

        return response()->json([
            'success'  => true,
            'message'  => 'Mijoz muvaffaqiyatli yangilandi!',
            'customer' => $customer,
        ]);
    }

    public function destroy(Customer $customer)
    {
        if ($customer->photo) {
            Storage::disk('public')->delete($customer->photo);
        }
        $customer->delete();

        return response()->json([
            'success' => true,
            'message' => "Mijoz muvaffaqiyatli o'chirildi!",
        ]);
    }
}
