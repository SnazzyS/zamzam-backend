<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\TripStoreRequest;
use App\Models\Invoice;
use App\Models\Trip;
use Inertia\Inertia;

class TripController extends Controller
{
    public function show(Trip $trip)
    {
        $trip->load([
            'customers' => function ($query) {
                $query->orderBy('customer_trip.created_at');
            },
            'groups' => function ($query) {
                $query->orderBy('created_at');
            },
        ]);

        // Get invoice data for all customers in this trip
        $invoices = Invoice::where('trip_id', $trip->id)
            ->with('payments')
            ->get()
            ->keyBy('customer_id');

        // Add balance information to each customer
        $customersWithBalance = $trip->customers->map(function ($customer) use ($invoices, $trip) {
            $isStaff = $customer->pivot->customer_type === 'staff';

            // Staff don't have invoices or balances
            if ($isStaff) {
                return [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'national_id' => $customer->national_id,
                    'phone_number' => $customer->phone_number,
                    'address' => $customer->address,
                    'island' => $customer->island,
                    'date_of_birth' => $customer->date_of_birth,
                    'pivot' => $customer->pivot,
                    'invoice_id' => null,
                    'total_paid' => 0,
                    'discount' => 0,
                    'balance' => 0,
                    'is_staff' => true,
                ];
            }

            $invoice = $invoices->get($customer->id);
            $totalPaid = $invoice ? $invoice->payments->sum('amount') : 0;
            $discount = $invoice->discount ?? 0;
            $balance = $trip->price - $discount - $totalPaid;

            return [
                'id' => $customer->id,
                'name' => $customer->name,
                'national_id' => $customer->national_id,
                'phone_number' => $customer->phone_number,
                'address' => $customer->address,
                'island' => $customer->island,
                'date_of_birth' => $customer->date_of_birth,
                'pivot' => $customer->pivot,
                'invoice_id' => $invoice->id ?? null,
                'total_paid' => $totalPaid,
                'discount' => $discount,
                'balance' => $balance,
                'is_staff' => false,
            ];
        });

        return Inertia::render('Trips/Show', [
            'trip' => $trip,
            'customers' => $customersWithBalance,
            'groups' => $trip->groups,
        ]);
    }

    public function store(TripStoreRequest $request)
    {
        $trip = Trip::create($request->all());

        return redirect()
            ->to("/trips/{$trip->id}")
            ->with('success', 'Trip created successfully');
    }

    public function update(TripStoreRequest $request, Trip $trip)
    {
        $trip->update($request->all());

        return redirect()
            ->back()
            ->with('success', 'Trip updated successfully');
    }
}
