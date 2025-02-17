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
        Schema::create('room_trip', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained()->onDelete('cascade');
            $table->string('hotel_name');
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
        
        DB::statement("alter TABLE room_trip add UNIQUE unique_room_trip(room_id, trip_id)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_trip');
    }
};
