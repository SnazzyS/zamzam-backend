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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('national_id')->unique();
            $table->date('date_of_birth');
            $table->string('island');
            $table->string('phone_number');
            $table->string('address');
            $table->string('gender');
            $table->string('name_in_english')->nullable();
            $table->string('passport_number')->nullable()->unique();
            $table->string('passport_issued_date')->nullable();
            $table->string('passport_expiry_date')->nullable();
            $table->string('photo_url')->nullable();
            //            $table->foreignId('customer_id')->nullable()->constrained();
            //            $table->foreignId('trip_id')->constrained();
            //            $table->foreignId('bus_id')->nullable()->constrained();
            //            $table->foreignId('flight_id')->nullable()->constrained();
            //            $table->foreignId('room_id')->nullable()->constrained();
            $table->timestamps();
        });

  
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
