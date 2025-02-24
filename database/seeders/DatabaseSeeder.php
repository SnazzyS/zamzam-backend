<?php

namespace Database\Seeders;

use App\Models\CustomerTrip;
use App\Models\Trip;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        Trip::factory(3)->sequence(
            ['name' => 'Ramadan Umrah 2025'],
            ['name' => 'July Umrah 2025'],
            ['name' => 'December Umrah 2025']
        )->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            CustomerTableSeeder::class,
            BusSeederTable::class,
            CustomerTripTableSeeder::class,
            FlightTableSeeder::class,
            HotelTableSeeder::class,
            RoomTableSeeder::class,
      
        ]);
    }
}
