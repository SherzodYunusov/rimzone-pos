<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'name' => 'Samsung Galaxy S24',
            'price' => 450.00,
            'quantity' => 15,
            'description' => 'Eng yangi Samsung flagship telefon, 200MP kamera bilan'
        ]);

        Product::create([
            'name' => 'MacBook Pro M3',
            'price' => 1299.00,
            'quantity' => 8,
            'description' => 'Professional laptop, programming va video editing uchun ideal'
        ]);

        Product::create([
            'name' => 'Sony WH-1000XM5',
            'price' => 349.99,
            'quantity' => 25,
            'description' => 'Premium wireless headphones simsiz shovqin bilan'
        ]);

        Product::create([
            'name' => 'iPad Air 11-inch',
            'price' => 799.00,
            'quantity' => 12,
            'description' => 'M2 chipsiz yangi iPad Air, sifli rangli displey'
        ]);

        Product::create([
            'name' => 'DJI Air 3S',
            'price' => 999.00,
            'quantity' => 5,
            'description' => 'Professional drone 4K camera va 31 minutli flight time'
        ]);

        Product::create([
            'name' => 'Canon EOS R6',
            'price' => 2499.00,
            'quantity' => 3,
            'description' => 'Full-frame mirrorless camera, professional photography uchun'
        ]);

        Product::create([
            'name' => 'Apple Watch Ultra',
            'price' => 799.00,
            'quantity' => 18,
            'description' => 'Titanium smartwatch, adventure va sports uchun built'
        ]);

        Product::create([
            'name' => 'Logitech MX Master 3S',
            'price' => 99.99,
            'quantity' => 32,
            'description' => 'Professional mouse, productivity uchun optimizlangan'
        ]);
    }
}
