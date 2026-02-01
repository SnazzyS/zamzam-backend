<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('activity_trips', function (Blueprint $table) {
            // Remove old single price/currency columns
            $table->dropColumn(['price', 'currency']);

            // Add multi-currency price columns
            $table->decimal('price_usd', 10, 2)->nullable()->after('activity_id');
            $table->decimal('price_mvr', 10, 2)->nullable()->after('price_usd');
            $table->decimal('price_sar', 10, 2)->nullable()->after('price_mvr');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_trips', function (Blueprint $table) {
            $table->dropColumn(['price_usd', 'price_mvr', 'price_sar']);
            $table->decimal('price');
            $table->enum('currency', ['USD', 'MVR', 'SAR']);
        });
    }
};
