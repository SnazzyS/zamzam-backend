<?php

namespace App\Http\Controllers\Customer;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CustomerShowController extends Controller
{
    public function __invoke()
    {
        $customers = Customer::all();

        return response()->json($customers);
    }
}
