<?php

namespace App\Http\Controllers\Flight;

use App\Models\Trip;
use App\Models\Flight;
use App\Models\CustomerTrip;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ViewFlightDetailsController extends Controller
{
    public function __invoke(Trip $trip, Flight $flight)
    {

        $flightName = $flight->name;

        // $flight = CustomerTrip::where('trip_id', $trip->id)
        //             ->where('flight_id', $flight->id)
        //             ->with([
        //                 'customer:id,name_in_english,passport_number,date_of_birth,phone_number',
        //                 'flight:id,name'
        //                 ])
        //             ->get()
        //             ->groupBy('flight_id')
        //             ->map(function ($flightGroup) {
        //                 return $flightGroup->map(function ($customerTrip) {
        //                     return [
        //                         'umrah_id' => $customerTrip->umrah_id,
        //                         'name' => $customerTrip->customer->name_in_english,
        //                         'name_in_english' => $customerTrip->customer->passport_number,
        //                         'phone_number' => $customerTrip->customer->phone_number,
        //                         'date_of_birth' => $customerTrip->customer->date_of_birth
        //                     ];
        //                 });
        //             });

        $flight = CustomerTrip::where('trip_id', $trip->id)
        ->where('flight_id', $flight->id)
        ->with([
            'customer:id,name_in_english,passport_number,date_of_birth,phone_number',
            'flight:id,name'
            ])
        ->get()
        ->map(function ($customerTrip) {
            return [
                'umrah_id' => $customerTrip->umrah_id,
                'name' => $customerTrip->customer->name_in_english,
                'name_in_english' => $customerTrip->customer->passport_number,
                'phone_number' => $customerTrip->customer->phone_number,
                'date_of_birth' => $customerTrip->customer->date_of_birth
            ];
        });

        return response()->json([
            'flight' => $flight,
            'flightName' => $flightName
        ]);
    }
}
