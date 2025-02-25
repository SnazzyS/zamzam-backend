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
        $invoice = $customer->invoices()->where('trip_id', $trip->id)->first();

        if (!$invoice) {
            return response()->json([
                'message' => 'Invoice not found for the given trip and customer.'
            ], 404);
        }

        
        $payment = Payment::create([
            'invoice_id' => 2, //$customer->invoices()->where('trip_id', $trip->id)->first()->id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'transfer_reference_number' => $request->transfer_reference_number,
            'details' => $request->details,
        ]);

        // $payment->load('invoice.customer', 'invoice.trip');

        return response()->json([
            'message' => 'ފައިސާ ބަލައި ގަނެވިއްޖެ'
        ]);

      
    }
}
