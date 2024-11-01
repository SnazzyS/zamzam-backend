<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('buses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('bus_number');
            $table->integer('capacity');
            $table->foreignId('trip_id')->constrained();
            $table->timestamps();
        });

        DB::table('buses')->insert([
            'name' => 'Azeebe',
            'bus_number' => 1,
            'capacity' => 50,
            'trip_id' => 1
            // 'trip_code' => (new TripIDGenerator())->generateTripID()
        ]);
    }

    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buses');
    }
};
