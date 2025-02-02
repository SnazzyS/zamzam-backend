<?php

namespace App\Http\Controllers\Customer;

use App\Models\Trip;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ViewCustomerDetailsController extends Controller
{
    public function __invoke(Trip $trip, Customer $customer)
    {
        return response()->json($customer);
    }
}
