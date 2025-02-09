<?php
namespace App\Http\Controllers\Payment;

use App\Models\Customer;
use App\Models\Trip;
use App\Http\Controllers\Controller;

class ViewCustomerPaymentDetailsController extends Controller
{
    public function __invoke(Trip $trip, Customer $customer)
    {
        return response()->json($customer->payments()->where('trip_id', $trip->id)->get());
    }
}
