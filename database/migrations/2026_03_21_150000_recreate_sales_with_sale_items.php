<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Eski sales jadvalini o'chirib, yangisini yaratamiz
        Schema::dropIfExists('sale_items'); // avval FK bor bolsa xato bermasin
        Schema::dropIfExists('sales');

        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->decimal('total_price', 15, 2)->default(0);
            $table->date('sale_date');
            $table->timestamps();
        });

        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('unit_price', 15, 2); // Sotilgan paytdagi narx
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');
    }
};
