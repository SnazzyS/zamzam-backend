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
        Schema::table('expenses', function (Blueprint $table) {
            // Trip relationship (nullable for general expenses)
            $table->foreignId('trip_id')->nullable()->after('id')->constrained()->onDelete('set null');

            // Expense details
            $table->string('title')->after('trip_id');
            $table->text('description')->nullable()->after('title');
            $table->decimal('amount', 12, 2)->after('description');
            $table->enum('currency', ['MVR', 'USD', 'SAR'])->default('MVR')->after('amount');

            // Expense categorization
            $table->string('category')->nullable()->after('currency');
            $table->date('expense_date')->after('category');
            $table->year('fiscal_year')->after('expense_date');

            // Payment details
            $table->enum('payment_method', ['transfer', 'cash', 'cheque'])->after('fiscal_year');
            $table->string('transfer_reference_number')->nullable()->after('payment_method');
            $table->string('cheque_number')->nullable()->after('transfer_reference_number');

            // Document upload
            $table->string('document_path')->nullable()->after('cheque_number');
            $table->string('document_filename')->nullable()->after('document_path');
            $table->string('document_mime_type')->nullable()->after('document_filename');

            // Vendor/Payee information
            $table->string('vendor_name')->nullable()->after('document_mime_type');

            $table->softDeletes()->after('vendor_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['trip_id']);
            $table->dropColumn([
                'trip_id',
                'title',
                'description',
                'amount',
                'currency',
                'category',
                'expense_date',
                'fiscal_year',
                'payment_method',
                'transfer_reference_number',
                'cheque_number',
                'document_path',
                'document_filename',
                'document_mime_type',
                'vendor_name',
                'deleted_at',
            ]);
        });
    }
};
