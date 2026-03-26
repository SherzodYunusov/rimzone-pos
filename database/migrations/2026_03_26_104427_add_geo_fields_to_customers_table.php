<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->decimal('lat', 10, 8)->nullable()->after('photo');
            $table->decimal('lng', 11, 8)->nullable()->after('lat');
            $table->text('map_link')->nullable()->after('lng');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['lat', 'lng', 'map_link']);
        });
    }
};
