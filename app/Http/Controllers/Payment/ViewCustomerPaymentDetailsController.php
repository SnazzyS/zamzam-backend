<?php

use App\Models\Customer;
use App\Models\Trip;
use App\Http\Controllers\Controller;

class ViewCustomerPaymentDetailsController extends Controller
{
    public function __invoke(Trip $trip, Customer $customer)
    {
        $payments = $customer->payments;
        return response()->json($payments);
    }
}
