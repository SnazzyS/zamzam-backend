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
        Schema::table('customer_activities', function (Blueprint $table) {
            // Remove old is_completed column
            $table->dropColumn('is_completed');

            // Add payment tracking fields
            $table->enum('currency', ['USD', 'MVR', 'SAR'])->nullable()->after('activity_trip_id');
            $table->decimal('amount_paid', 10, 2)->nullable()->after('currency');
            $table->enum('payment_method', ['cash', 'transfer'])->nullable()->after('amount_paid');
            $table->string('receipt_number')->nullable()->after('payment_method');
            $table->timestamp('paid_at')->nullable()->after('receipt_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_activities', function (Blueprint $table) {
            $table->dropColumn(['currency', 'amount_paid', 'payment_method', 'receipt_number', 'paid_at']);
            $table->boolean('is_completed')->default(false);
        });
    }
};
