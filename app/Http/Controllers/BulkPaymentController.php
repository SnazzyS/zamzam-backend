<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class BulkPaymentController extends Controller
{
    /**
     * Generate a receipt number in format RCP-YYMMDD-NNN
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

    public function store(Trip $trip, Request $request)
    {
        $request->validate([
            'payments' => ['required', 'array', 'min:1'],
            'payments.*.customer_id' => ['required', 'exists:customers,id'],
            'payments.*.amount' => ['required', 'numeric', 'min:0'],
            'payments.*.discount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['required', 'string'],
            'details' => ['nullable', 'string'],
            'transfer_reference_number' => ['nullable', 'string'],
        ]);

        $batchId = $this->generateReceiptNumber();
        $paymentIds = [];

        foreach ($request->payments as $paymentData) {
            // Get or create invoice for this customer and trip
            $invoice = Invoice::firstOrCreate(
                [
                    'trip_id' => $trip->id,
                    'customer_id' => $paymentData['customer_id'],
                ],
                [
                    'balance' => $trip->price,
                    'amount' => 0,
                    'discount' => 0,
                    'invoice_number' => 'INV/' . date('Y') . '/' . $paymentData['customer_id'],
                    'grand_total' => $trip->price,
                    'invoiceable_id' => $trip->id,
                    'invoiceable_type' => Trip::class,
                ]
            );

            // Apply discount if provided
            $discount = $paymentData['discount'] ?? 0;
            if ($discount > 0) {
                $invoice->discount = ($invoice->discount ?? 0) + $discount;
                $invoice->save();
            }

            // Only create payment if amount > 0
            $amount = $paymentData['amount'] ?? 0;
            if ($amount > 0) {
                $payment = Payment::create([
                    'invoice_id' => $invoice->id,
                    'amount' => $amount,
                    'payment_method' => $request->payment_method,
                    'transfer_reference_number' => $request->transfer_reference_number,
                    'details' => $request->details ?? $trip->name,
                    'batch_id' => $batchId,
                ]);

                $paymentIds[] = $payment->id;
            }
        }

        // Only redirect with batch_id if payments were created
        if (count($paymentIds) > 0) {
            return redirect()
                ->back()
                ->with('success', 'ފައިސާ ބަލައި ގަނެވިއްޖެ')
                ->with('batch_id', $batchId);
        }

        return redirect()
            ->back()
            ->with('success', 'ޑިސްކައުންޓް އެޕްލައި ކުރެވިއްޖެ');
    }

    public function showBatch(Trip $trip, string $batchId)
    {
        $payments = Payment::where('batch_id', $batchId)
            ->with(['invoice.customer'])
            ->get();

        if ($payments->isEmpty()) {
            abort(404);
        }

        // Get customer details with their trip pivot data
        $paymentDetails = $payments->map(function ($payment) use ($trip) {
            $customer = $payment->invoice->customer;
            $tripCustomer = $customer->trips()->where('trip_id', $trip->id)->first();

            // Calculate balance after this payment
            $paymentsBeforeThis = Payment::where('invoice_id', $payment->invoice_id)
                ->where('id', '<=', $payment->id)
                ->sum('amount');

            $balanceAfterPayment = $payment->invoice->grand_total - $payment->invoice->discount - $paymentsBeforeThis;

            return [
                'payment' => $payment,
                'customer' => $customer,
                'umrah_id' => $tripCustomer?->pivot?->umrah_id,
                'balance_after' => $balanceAfterPayment,
            ];
        });

        $totalAmount = $payments->sum('amount');

        return Inertia::render('Trips/Payments/BulkReceipt', [
            'trip' => $trip,
            'batchId' => $batchId,
            'paymentDetails' => $paymentDetails,
            'totalAmount' => $totalAmount,
            'paymentMethod' => $payments->first()->payment_method,
            'paymentDate' => $payments->first()->created_at,
            'details' => $payments->first()->details,
            'transferReference' => $payments->first()->transfer_reference_number,
        ]);
    }
}
