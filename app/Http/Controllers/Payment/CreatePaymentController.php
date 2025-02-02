<?php

namespace App\Http\Controllers\Payment;

use App\Models\Trip;
use App\Models\Customer;
use App\Models\Payment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CreatePaymentController extends Controller
{
    public function __invoke(Trip $trip, Customer $customer, Request $request)
    {
        // dd($request);
        // payment if done, u should make a new logic?
        // balance amount full vejje tho belun

        $previousPayment = Payment::where('customer_id', $customer->id)
            ->where('trip_id', $trip->id)
            ->latest()
            ->first();
        // dd($previousPayment);

        $balanceAmount = $previousPayment
            ? $previousPayment->balance_amount - ($request->amount + $request->discount)
            : $trip->price - ($request->amount + $request->discount);

        if ($balanceAmount < 0) {
            return response()->json([
                'message' => 'Exceeding the balance amount'
            ], 400);
        }

        Payment::create([
            'customer_id' => $customer->id,
            'trip_id' => $trip->id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'transfer_reference_number' => $request->transfer_reference_number,
            'invoice_number' => 'LLL',
            'discount' => $request->discount,
            'details' => $request->details,
            'balance_amount' => $balanceAmount
            
        ]);
    }
}
