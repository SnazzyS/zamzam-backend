<?php

namespace App\Http\Controllers\Invoice;

use App\Models\Trip;
use App\Models\Customer;

class RemoveDiscountController
{
    public function __invoke(Trip $trip, Customer $customer)
    {
        dd('here');
    }
}
