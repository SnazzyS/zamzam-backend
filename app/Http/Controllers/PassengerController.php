<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PassengerController extends Controller
{
    public function index()
    {
        $customers = Customer::orderBy('created_at', 'desc')->get();

        return Inertia::render('Customers/Index', [
            'customers' => $customers,
        ]);
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'national_id' => 'nullable|string',
            'passport_number' => 'nullable|string',
            'phone_number' => 'required|integer',
            'island' => 'nullable|string',
            'address' => 'required|string',
            'gender' => 'required|in:Male,Female',
            'is_foreigner' => 'boolean',
            'country' => 'nullable|string|max:2',
        ]);

        $customer->update($validated);

        return redirect()->back()->with('success', 'Customer updated successfully');
    }
}
