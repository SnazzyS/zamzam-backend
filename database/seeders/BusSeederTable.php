<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BusSeederTable extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $buses = [[
            'name' => 'Azeebe',
            'capacity' => 50,
            'trip_id' => 1,
            'bus_number' => 1
        ],
        [
            'name' => 'Nasrulla',
            'capacity' => 50,
            'trip_id' => 1,
            'bus_number' => 2


            ]
        ];

        foreach ($buses as $bus) {
            DB::table('buses')->insert($bus);
        }
    }
}
