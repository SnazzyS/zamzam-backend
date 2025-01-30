<?php

namespace App\Http\Controllers\Customer;

use App\Models\Bus;
use App\Models\Trip;
use App\Models\Customer;
use App\Models\CustomerTrip;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CustomerBusAttachController extends Controller
{
    public function __invoke(Trip $trip, Customer $customer, Request $request)
    {
   
        $bus = Bus::findOrFail($request->bus);

        if ($bus->customerTrips()->count() >= $bus->capacity) {
            return response()->json(['message' => 'Bus is full'], 400);
        }

        CustomerTrip::where([
            'customer_id' => $customer->id,
            'trip_id' => $trip->id,
        ])->update(['bus_id' => $request->bus]);

        return response()->json(['message' => 'Bus assigned successfully'], 200);


    }
}
