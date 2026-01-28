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
        Schema::table('customer_trip', function (Blueprint $table) {
            $table->enum('customer_type', ['customer', 'staff'])->default('customer')->after('umrah_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_trip', function (Blueprint $table) {
            $table->dropColumn('customer_type');
        });
    }
};
