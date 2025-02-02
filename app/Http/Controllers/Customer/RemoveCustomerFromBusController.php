<?php

namespace App\Http\Controllers\Customer;

use App\Models\Trip;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CustomerTrip;

class RemoveCustomerFromBusController extends Controller
{
    public function __invoke(Trip $trip, Customer $customer, Request $request)
    {
        CustomerTrip::where([
            'trip_id' => $trip->id,
            'customer_id' => $customer->id
        ])->update(['bus_id' => null]);

        return response()->json(['message' => 'Customed detached from bus'], 200);
    }
}
