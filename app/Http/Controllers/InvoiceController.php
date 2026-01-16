<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\DiscountStoreRequest;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Trip;

class InvoiceController extends Controller
{
    public function addDiscount(Trip $trip, Customer $customer, DiscountStoreRequest $request)
    {
        $invoice = Invoice::where('trip_id', $trip->id)
        ->where('customer_id', $customer->id)
        ->first();

        if (!$invoice) {
            return redirect()
                ->back()
                ->withErrors(['discount' => 'Invoice not found for the given trip and customer.']);
        }

        $invoice->update([
            'discount' => $request->discount + $invoice->discount
        ]);

        return redirect()
            ->back()
            ->with('success', 'Discount applied successfully');
    }

    public function removeDiscount(Trip $trip, Customer $customer)
    {
        return redirect()
            ->back()
            ->with('error', 'Remove discount not implemented');
    }
}
