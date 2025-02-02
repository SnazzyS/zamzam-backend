<?php

namespace App\Http\Controllers\Customer;

use App\Models\Trip;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RemoveCustomerFromTripController extends Controller
{
    public function __invoke(Trip $trip, Customer $customer)
    {
        $trip->customers()->detach($customer);

        return response()->json([
            'message' => 'Customer detached from trip successfully'
        ], 200);
    }

}
