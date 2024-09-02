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
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('price');
            $table->date('departure_date');
            $table->integer('phone_number')->nullable();
            $table->string('hotel_address')->nullable();
            $table->timestamps();
        });

        DB::table('trips')->insert([
            'name' => 'Ramadan Umrah',
            'departure_date' => '2025-01-25',
            'price' => 40000,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
