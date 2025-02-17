<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class HotelTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hotel = [
            [
            'name' => 'Loa Loa Al Azhar',
            'address' => 'Ajyad Road',
            'phone_number' => '875424',
            ]
        ];

        foreach ($hotel as $hotel) {
            DB::table('hotels')->insert($hotel);
        }
    }
}
