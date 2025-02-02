<?php

namespace App\Http\Controllers\Flight;

use App\Models\Flight;
use App\Models\Trip;
use App\Models\CustomerTrip;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ListFlightsController extends Controller
{
    public function __invoke(Trip $trip)
    {
        $flights = CustomerTrip::where('trip_id', $trip->id)
                    ->with([
                        'customer:id,name,name_in_english,phone_number',
                        'flight:id,name'
                        ])
                    ->get()
                    ->groupBy('flight.name')
                        ->map(function ($passengers) {
                            return $passengers->map(function ($passenger) {
                                return [
                                    'umrah_id' => $passenger->umrah_id,
                                    'name' => $passenger->customer->name,
                                    'name_in_english' => $passenger->customer->name_in_english,
                                    'passport_number' => $passenger->customer->name_in_english,
                                    'phone' => $passenger->customer->phone,

                                ];
                            });
                        });
                    


        return response()->json($flights);






















        // $flights = CustomerTrip::where('trip_id', $trip->id)
        // ->with([
        //     'customer:id,name,name_in_english',
        //     'flight:id,name'
        // ])
        // ->get()
        // ->groupBy('flight.name')
        // ->map(function ($passengers) {
        //     return $passengers->map(function ($passenger) {
        //         return [
        //             'flight_id' => $passenger->flight->id,
        //             'name' => $passenger->customer->name,
        //             'name_in_english' => $passenger->customer->name_in_english
        //         ];
        //     });
        // });

        return response()->json($flights);


    }
}
