<?php

namespace App\Observers;

use App\Models\Payment;
use App\Models\Invoice;

class PaymentObserver
{
    /**
     * Handle the Payment "created" event.
     */
    public function created(Payment $payment): void
    {
        
        $invoice = $payment->invoice;
        $invoice->amount = $invoice->amount + $payment->amount;
        $invoice->balance = $invoice->grand_total - $invoice->amount;
        $invoice->save();
        
    }

    /**
     * Handle the Payment "updated" event.
     */
    public function updated(Payment $payment): void
    {
        //
    }

    /**
     * Handle the Payment "deleted" event.
     */
    public function deleted(Payment $payment): void
    {
        //
    }

    /**
     * Handle the Payment "restored" event.
     */
    public function restored(Payment $payment): void
    {
        //
    }

    /**
     * Handle the Payment "force deleted" event.
     */
    public function forceDeleted(Payment $payment): void
    {
        //
    }
}
