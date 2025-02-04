<?php

namespace App\Http\Controllers\Payment;

use App\Models\Trip;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentStoreRequest;

class CreatePaymentController extends Controller
{
    public function __invoke(Trip $trip, Customer $customer, PaymentStoreRequest $request)
    {

        Payment::create([
            'invoice_id' => $customer->invoices()->where('trip_id', $trip->id)->first()->id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'transfer_reference_number' => $request->transfer_reference_number,
            'details' => $request->details,
        ]);

        return response()->json([
            'message' => 'ފައިސާ ބަލައި ގަނެވިއްޖެ'
        ]);

      
    }
}
