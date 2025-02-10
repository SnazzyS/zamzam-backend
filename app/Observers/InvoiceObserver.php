<?php

namespace App\Observers;

use App\Models\Invoice;

class InvoiceObserver
{
    public function updated(Invoice $invoice)
    {
        // $invoice->grand_total->update[($invoice->discount - $invoice->grand_total)];
        
        // $invoice->grand_total = $invoice->total - $invoice->discount;
        // $invoice->save();


    }
}
