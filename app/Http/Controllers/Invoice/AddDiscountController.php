<?php

namespace App\Http\Controllers\Invoice;

use App\Http\Requests\DiscountStoreRequest;
use App\Models\Trip;
use App\Models\Invoice;
use App\Models\Customer;
use Illuminate\Http\Request;

class AddDiscountController
{
    public function __invoke(Trip $trip, Customer $customer, DiscountStoreRequest $request)
    {

        // dd($customer->invoices()->where('trip_id', $trip->id)->value('discount'));

        // $invoice = Invoice::where('trip_id', $trip->id)
        //     ->where('customer_id', $customer->id)
        //     ->update([
        //     'discount' => $request->discount + $customer->invoices()->where('trip_id', $trip->id)->value('discount')
        //     ]);


        $invoice = Invoice::where('trip_id', $trip->id)
        ->where('customer_id', $customer->id)
        ->first();
            
        $invoice->update([
            'discount' => $request->discount + $invoice->discount
        ]);
        
            
    }
}
