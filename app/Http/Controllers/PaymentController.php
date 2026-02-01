<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Trip;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentController extends Controller
{
    /**
     * Generate a receipt number in format INV-YYMMDD-NNN
     */
    private function generateReceiptNumber(): string
    {
        $today = now();
        $datePrefix = $today->format('ymd');

        // Count receipts created today
        $todayCount = Payment::whereNotNull('batch_id')
            ->whereDate('created_at', $today->toDateString())
            ->distinct('batch_id')
            ->count('batch_id');

        $sequence = str_pad($todayCount + 1, 3, '0', STR_PAD_LEFT);

        return "INV-{$datePrefix}-{$sequence}";
    }

    public function store(Trip $trip, Customer $customer, Request $request)
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_method' => ['required', 'string'],
            'details' => ['nullable', 'string'],
            'transfer_reference_number' => ['nullable', 'string'],
        ]);

        $invoice = Invoice::where('trip_id', $trip->id)
            ->where('customer_id', $customer->id)
            ->first();

        if (!$invoice) {
            return redirect()
                ->back()
                ->withErrors(['payment' => 'Invoice not found for the given trip and customer.']);
        }

        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'transfer_reference_number' => $request->transfer_reference_number,
            'details' => $request->details ?? $trip->name,
            'batch_id' => $this->generateReceiptNumber(),
        ]);

        return redirect()
            ->back()
            ->with('success', 'ފައިސާ ބަލައި ގަނެވިއްޖެ')
            ->with('payment_id', $payment->id);
    }

    public function index(Trip $trip, Customer $customer)
    {
        $invoice = Invoice::where('trip_id', $trip->id)
            ->where('customer_id', $customer->id)
            ->first();

        $payments = $invoice ? $invoice->payments()->orderBy('created_at', 'desc')->get() : collect();

        return Inertia::render('Trips/Customers/Payments/Index', [
            'trip' => $trip,
            'customer' => $customer,
            'invoice' => $invoice,
            'payments' => $payments,
        ]);
    }

    public function show(Trip $trip, Customer $customer, Payment $payment)
    {
        $invoice = Invoice::where('trip_id', $trip->id)
            ->where('customer_id', $customer->id)
            ->first();

        if (!$invoice || $payment->invoice_id !== $invoice->id) {
            abort(404);
        }

        // Calculate balance at time of this payment
        $paymentsBeforeThis = Payment::where('invoice_id', $invoice->id)
            ->where('id', '<=', $payment->id)
            ->sum('amount');

        $balanceAfterPayment = $invoice->grand_total - $invoice->discount - $paymentsBeforeThis;

        return Inertia::render('Trips/Customers/Payments/Receipt', [
            'trip' => $trip,
            'customer' => $customer,
            'invoice' => $invoice,
            'payment' => $payment,
            'tripCustomer' => $customer->trips()->where('trip_id', $trip->id)->first()?->pivot,
            'balanceAfterPayment' => $balanceAfterPayment,
        ]);
    }
}
