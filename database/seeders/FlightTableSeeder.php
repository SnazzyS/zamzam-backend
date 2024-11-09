<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class FlightTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $flights = [
            [
                'name' => 'Emirates',
                'trip_id' => 1
            ],
            [
                'name' => 'Gulf Air',
                'trip_id' => 1
            ]
        ];

        foreach ($flights as $flight) {
            DB::table('flights')->insert($flight);
        }
        
    }
}
