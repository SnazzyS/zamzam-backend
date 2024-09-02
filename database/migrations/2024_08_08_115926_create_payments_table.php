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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->integer('amount');
            $table->foreignId('customer_id')->constrained();
            $table->foreignId('trip_id')->constrained();
            $table->string('payment_method');
            $table->string('transfer_reference_number');
            $table->string('invoice_number');
            $table->date('payment_date');
            $table->integer('discount')->default(0);
            $table->string('reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
