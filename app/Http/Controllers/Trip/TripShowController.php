<?php

namespace App\Http\Controllers\Trip;

use App\Models\Trip;
use App\Models\CustomerTrip;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TripShowController extends Controller
{
    public function __invoke(Trip $trip)
    {
        $passengers = CustomerTrip::where('trip_id', 1)
        ->where('bus_id', 1)
        ->with(['customer']) // Eager load customer data
        ->get();

        dd($passengers);
        
        

        // return response()->json($trip->load('customers'));

        // return response()->json($trip->load(['customers' => function ($query) {
        //     $query->select('id', 'trip_id', 'name', 'date_of_birth', 'phone_number', 'id_card', 'island', 'address');
        // }]));
    }
}
