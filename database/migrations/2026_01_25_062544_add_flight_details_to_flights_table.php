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
        Schema::table('flights', function (Blueprint $table) {
            $table->date('departure_date')->nullable()->after('name');
            $table->date('return_date')->nullable()->after('departure_date');
            $table->string('departure_flight_number')->nullable()->after('return_date');
            $table->string('departure_transit_flight_number')->nullable()->after('departure_flight_number');
            $table->string('return_flight_number')->nullable()->after('departure_transit_flight_number');
            $table->string('return_transit_flight_number')->nullable()->after('return_flight_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('flights', function (Blueprint $table) {
            $table->dropColumn([
                'departure_date',
                'return_date',
                'departure_flight_number',
                'departure_transit_flight_number',
                'return_flight_number',
                'return_transit_flight_number',
            ]);
        });
    }
};
