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
            $table->string('umrah_id');
            $table->string('name');
            $table->string('id_card');
            $table->date('date_of_birth');
            $table->string('island');
            $table->string('phone_number');
            $table->string('address');
            $table->string('gender');
            $table->string('name_in_english')->nullable();
            $table->string('passport_number')->nullable();
            $table->string('passport_issued_date')->nullable();
            $table->string('passport_expiry_date')->nullable();
            // $table->string('photo')->nullable();
            $table->foreignId('customer_id')->nullable()->constrained();
            $table->foreignId('trip_id')->constrained();
            $table->foreignId('bus_id')->nullable()->constrained();
            $table->foreignId('flight_id')->nullable()->constrained();
            $table->foreignId('room_id')->nullable()->constrained();
            $table->timestamps();
        });

        DB::table('customers')->insert([
            'umrah_id' => 'T1-101',
            'name' => 'Mohamed Suhail',
            'id_card' => 'A249836',
            'date_of_birth' => '1994-12-06',
            'island' => 'Hithaadhoo',
            'phone_number' => 7999065,
            'address' => 'Rihiveli',
            'gender' => 'Male',
            'trip_id' => 1

        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
