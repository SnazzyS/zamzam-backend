<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CustomerTripTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customerTrips = [
            [
                'customer_id' => 1,
                'trip_id' => 1,
                'bus_id' => 1,
                'flight_id' => null,
                // 'room_id' => null,
                'umrah_id' => 'T-101'
            ],
            [
                'customer_id' => 2,
                'trip_id' => 1,
                'bus_id' => 2,
                'flight_id' => null,
                // 'room_id' => null,
                'umrah_id' => 'T-102'
            ],
            [
                'customer_id' => 3,
                'trip_id' => 1,
                'bus_id' => 1,
                'flight_id' => null,
                // 'room_id' => null,
                'umrah_id' => 'T-103'
            ],
        ];

        foreach ($customerTrips as $customerTrip) {
            DB::table('customer_trip')->insert($customerTrip);
        }
    }
}
