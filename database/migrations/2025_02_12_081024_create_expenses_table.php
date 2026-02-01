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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();

            // Trip relationship (nullable for general expenses)
            $table->foreignId('trip_id')->nullable()->constrained()->onDelete('set null');

            // Expense details
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('amount', 12, 2);
            $table->enum('currency', ['MVR', 'USD', 'SAR'])->default('MVR');

            // Expense categorization
            $table->string('category')->nullable();
            $table->date('expense_date');
            $table->year('fiscal_year');

            // Payment details
            $table->enum('payment_method', ['transfer', 'cash', 'cheque']);
            $table->string('transfer_reference_number')->nullable();
            $table->string('cheque_number')->nullable();

            // Document upload
            $table->string('document_path')->nullable();
            $table->string('document_filename')->nullable();
            $table->string('document_mime_type')->nullable();

            // Vendor/Payee information
            $table->string('vendor_name')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
