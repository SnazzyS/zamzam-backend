<?php

namespace App\Http\Controllers\Customer;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SearchCustomerController extends Controller
{
    public function __invoke(Customer $customer)
    {
        dd('hit');
    }
}
