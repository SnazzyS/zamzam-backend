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
        Schema::table('customer_trip', function (Blueprint $table) {
            $table->foreignId('group_id')
                ->nullable()
                ->constrained('trip_groups')
                ->nullOnDelete()
                ->after('flight_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_trip', function (Blueprint $table) {
            $table->dropConstrainedForeignId('group_id');
        });
    }
};
