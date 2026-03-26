<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            // status: paid | partial | debt
            $table->string('status', 20)->default('paid')->after('payment_method');
            // To'langan summa (nasiya uchun qisman to'lovlarni kuzatish)
            $table->decimal('paid_amount', 15, 2)->default(0)->after('status');
            // Nasiya muddati (ixtiyoriy)
            $table->date('due_date')->nullable()->after('paid_amount');
        });

        // Mavjud naqd/karta savdolarini "paid" deb belgilaymiz
        DB::statement("UPDATE sales SET status='paid', paid_amount=total_price WHERE payment_method IN ('naqd', 'karta')");
        // Mavjud nasiya savdolarini "debt" deb belgilaymiz
        DB::statement("UPDATE sales SET status='debt', paid_amount=0 WHERE payment_method='nasiya'");
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['status', 'paid_amount', 'due_date']);
        });
    }
};
