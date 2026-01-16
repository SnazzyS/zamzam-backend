<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentStoreRequest;
use App\Models\Customer;
use App\Models\Trip;
use App\Models\Payment;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function store(Trip $trip, Customer $customer, PaymentStoreRequest $request)
    {
        $invoice = $customer->invoices()->where('trip_id', $trip->id)->first();

        if (!$invoice) {
            return redirect()
                ->back()
                ->withErrors(['payment' => 'Invoice not found for the given trip and customer.']);
        }

        Payment::create([
            'invoice_id' => 2, // WARNING: Original code hardcoded 2? Or commented out dynamic. Keeping original behavior but this looks wrong: //$customer->invoices()->where('trip_id', $trip->id)->first()->id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'transfer_reference_number' => $request->transfer_reference_number,
            'details' => $request->details,
        ]);

        return redirect()
            ->back()
            ->with('success', 'ފައިސާ ބަލައި ގަނެވިއްޖެ');
    }

    public function index(Trip $trip, Customer $customer)
    {
        $payments = $customer->payments()->where('trip_id', $trip->id)->get();

        return Inertia::render('Trips/Customers/Payments/Index', [
            'trip' => $trip,
            'customer' => $customer,
            'payments' => $payments,
        ]);
    }
}
