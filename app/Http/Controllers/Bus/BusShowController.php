<?php

namespace App\Http\Controllers\Bus;

use App\Models\Bus;
use App\Models\Trip;
use App\Models\CustomerTrip;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BusShowController extends Controller
    // show is used for single bus, not to all buses
{
    public function __invoke(Trip $trip, Bus $bus)
    {
        // $bus = Bus::with(['customerTrips' => function ($query) use ($trip) {
        //     $query->where('trip_id', $trip->id)
        //         ->with(['customer:id,name,name_in_english,passport_number']);
        // }])
        // ->findOrFail($bus->id);


        $bus = CustomerTrip::where('trip_id', $trip->id)
            ->where('bus_id', $bus->id)
            ->with([
                'customer:id,name,name_in_english,passport_number',
                'bus:id,name',
                ])
            ->get();


        return response()->json([
            'trip_name' => $trip->name,
            'bus' => $bus
        ]);
    }
}
