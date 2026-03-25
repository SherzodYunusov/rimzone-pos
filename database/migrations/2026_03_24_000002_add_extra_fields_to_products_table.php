<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('cost_price', 15, 2)->nullable()->after('price');   // Tannarx
            $table->string('unit_type', 20)->nullable()->after('quantity');     // kg, litr, dona, boshqa
            $table->decimal('unit_value', 10, 3)->nullable()->after('unit_type'); // Necha kg / litr
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['cost_price', 'unit_type', 'unit_value']);
        });
    }
};
