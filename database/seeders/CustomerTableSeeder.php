<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CustomerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers =  [[
             // 'umrah_id' => 'T1-101',
             'name' => 'Mohamed Suhail',
             'national_id' => 'A249836',
             'date_of_birth' => '1994-12-06',
             'island' => 'Hithaadhoo',
             'phone_number' => 7999065,
             'address' => 'Rihiveli',
             'gender' => 'Male',
             // 'trip_id' => 1

         ],
         [
             // 'umrah_id' => 'T1-102',
             'name' => 'Aminath Mohamed',
             'national_id' => 'A249837',
             'date_of_birth' => '1994-12-06',
             'island' => 'Hithaadhoo',
             'phone_number' => 7999065,
             'address' => 'Rihiv',
             'gender' => "Female"
         ]];

        foreach ($customers as $customer) {
            DB::table('customers')->insert($customer);
        }
    }
}
