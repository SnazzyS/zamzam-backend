<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoomTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $room = [
            [
                'room_number' => 101,
                // 'name' => 'isse room',
                'bed_count' => 4,
                // 'private' => 0,
                'hotel_id' => 1,
                // 'trip_id' => 1,
            ]
            ];

        foreach ($room as $room) {
            DB::table('rooms')->insert($room);
        }
    }
}
