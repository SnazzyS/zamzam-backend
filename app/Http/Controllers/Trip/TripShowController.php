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
        // $passengersByBus = CustomerTrip::where('trip_id', $trip->id)
        //     ->whereNotNull('bus_id')
        //     ->with(['customer' => function ($query) {
        //         $query->select(
        //             'id',
        //             'name',
        //             'national_id',
        //             'phone_number',
        //             'island',
        //             'gender'
        //         );
        //     }])
        //     ->get()
        //     ->groupBy('bus_id');

        // dd($passengersByBus);
        
        

        return response()->json($trip->load('customers'));

        // return response()->json($trip->load(['customers' => function ($query) {
        //     $query->select('id', 'trip_id', 'name', 'date_of_birth', 'phone_number', 'id_card', 'island', 'address');
        // }]));
    }
}
