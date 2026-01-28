<?php

namespace App\Http\Controllers;

use App\Actions\Customer\UmrahIDGenerator;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerStoreRequest;
use App\Models\Bus;
use App\Models\Customer;
use App\Models\CustomerTrip;
use App\Models\Invoice;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class CustomerController extends Controller
{
    public function store(Trip $trip, CustomerStoreRequest $request, UmrahIDGenerator $idgenerator)
    {
        $customer = Customer::firstOrCreate(
            ['national_id' => $request->national_id],
            [
                'name' => $request->name,
                'date_of_birth' => $request->date_of_birth,
                'island' => $request->island,
                'phone_number' => $request->phone_number,
                'address' => $request->address,
                'gender' => $request->gender,
            ]
        );

        if (!$customer->trips->contains($trip->id)) {
            $customerType = $request->input('customer_type', 'customer');

            $customer->trips()->attach($trip->id, [
                'umrah_id' => $idgenerator->generateUmrahID($trip->id),
                'customer_type' => $customerType,
            ]);

            // Only create invoice for customers, not staff
            if ($customerType === 'customer') {
                Invoice::create([
                    'customer_id' => $customer->id,
                    'trip_id' => $trip->id,
                    'balance' => $trip->price,
                    'amount' => 0,
                    'discount' => 0,
                    'invoice_number' => 'INV/2024/01',
                    'grand_total' => $trip->price,
                    'invoiceable_id' => $trip->id,
                    'invoiceable_type' => get_class($trip)
                ]);
            }

            return redirect()
                ->to("/trips/{$trip->id}")
                ->with('success', $customerType === 'staff' ? 'ސްޓާފް އެޓޭޗް ކުރެވިއްޖެ' : 'ކަސްޓަމަރު ރެޖިސްޓަރ ކުރެވިއްޖެ');
        }

        return redirect()
            ->back()
            ->with('success', 'Customer created');
    }

    public function show(Trip $trip, Customer $customer)
    {
        $customer->load('photos');
        $tripCustomer = CustomerTrip::where([
            'trip_id' => $trip->id,
            'customer_id' => $customer->id,
        ])->first();

        $isStaff = $tripCustomer?->customer_type === 'staff';

        // Get invoice for this customer and trip (staff don't have invoices)
        $invoice = null;
        $payments = collect();
        $totalPaid = 0;
        $balance = 0;

        if (!$isStaff) {
            $invoice = Invoice::where('trip_id', $trip->id)
                ->where('customer_id', $customer->id)
                ->first();

            $payments = $invoice ? $invoice->payments()->orderBy('created_at', 'desc')->get() : collect();
            $totalPaid = $payments->sum('amount');
            $balance = $invoice ? ($invoice->grand_total - $invoice->discount - $totalPaid) : $trip->price;
        }

        return Inertia::render('Trips/Customers/Show', [
            'trip' => $trip,
            'customer' => $customer,
            'tripCustomer' => $tripCustomer,
            'invoice' => $invoice,
            'payments' => $payments,
            'isStaff' => $isStaff,
            'paymentSummary' => [
                'tripPrice' => $trip->price,
                'discount' => $invoice->discount ?? 0,
                'totalPaid' => $totalPaid,
                'balance' => $balance,
            ],
        ]);
    }

    public function update(Trip $trip, Customer $customer, CustomerStoreRequest $request)
    {
        $data = [
            'name' => $request->name,
            'id_card' => $request->id_card,
            'date_of_birth' => $request->date_of_birth,
            'island' => $request->island,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'gender' => $request->gender,
            'name_in_english' => $request->name_in_english,
            'passport_number' => $request->passport_number,
            'passport_issued_date' => $request->passport_issued_date,
        ];

        if ($request->filled('passport_expiry_date')) {
            $data['passport_expiry_date'] = $request->passport_expiry_date;
        } elseif ($request->filled('passport_issued_date')) {
            $data['passport_expiry_date'] = Carbon::parse($request->passport_issued_date)->addYears(5)->toDateString();
        }

        $customer->update($data);

        // Handle customer_type change
        $newCustomerType = $request->input('customer_type');
        if ($newCustomerType) {
            $tripCustomer = CustomerTrip::where([
                'trip_id' => $trip->id,
                'customer_id' => $customer->id,
            ])->first();

            if ($tripCustomer && $tripCustomer->customer_type !== $newCustomerType) {
                $tripCustomer->update(['customer_type' => $newCustomerType]);

                // If changing from staff to customer, create invoice if doesn't exist
                if ($newCustomerType === 'customer') {
                    Invoice::firstOrCreate(
                        [
                            'trip_id' => $trip->id,
                            'customer_id' => $customer->id,
                        ],
                        [
                            'balance' => $trip->price,
                            'amount' => 0,
                            'discount' => 0,
                            'invoice_number' => 'INV/' . date('Y') . '/' . $customer->id,
                            'grand_total' => $trip->price,
                            'invoiceable_id' => $trip->id,
                            'invoiceable_type' => Trip::class,
                        ]
                    );
                }
            }
        }

        return redirect()
            ->back()
            ->with('success', 'Customer updated successfully');
    }

    public function destroy(Trip $trip, Customer $customer)
    {
        $trip->customers()->detach($customer);

        return redirect()
            ->back()
            ->with('success', 'Customer detached from trip successfully');
    }

    public function assignBus(Trip $trip, Customer $customer, Request $request)
    {
        $bus = Bus::findOrFail($request->bus);

        if ($bus->customerTrips()->count() >= $bus->capacity) {
            return redirect()
                ->back()
                ->withErrors(['assignBus' => 'Bus is full']);
        }

        CustomerTrip::where([
            'customer_id' => $customer->id,
            'trip_id' => $trip->id,
        ])->update(['bus_id' => $request->bus]);

        return redirect()
            ->back()
            ->with('success', 'Bus assigned successfully');
    }

    public function detachBus(Trip $trip, Customer $customer, Request $request)
    {
        CustomerTrip::where([
            'trip_id' => $trip->id,
            'customer_id' => $customer->id
        ])->update(['bus_id' => null]);

        return redirect()
            ->back()
            ->with('success', 'Customer detached from bus');
    }

    public function assignGroup(Trip $trip, Customer $customer, Request $request)
    {
        $data = $request->validate([
            'group_id' => [
                'nullable',
                'integer',
                Rule::exists('trip_groups', 'id')->where('trip_id', $trip->id),
            ],
        ]);

        CustomerTrip::where([
            'customer_id' => $customer->id,
            'trip_id' => $trip->id,
        ])->update([
            'group_id' => $data['group_id'] ?? null,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Customer group updated');
    }

    /**
     * Search for a customer by national ID.
     * Returns customer data if found, used for auto-fill in registration.
     */
    public function searchByNationalId(Trip $trip, Request $request)
    {
        $request->validate([
            'national_id' => ['required', 'string'],
        ]);

        $customer = Customer::where('national_id', $request->national_id)->first();

        if (!$customer) {
            return response()->json([
                'found' => false,
                'customer' => null,
                'already_attached' => false,
            ]);
        }

        // Check if customer is already attached to this trip
        $alreadyAttached = $customer->trips()->where('trip_id', $trip->id)->exists();

        return response()->json([
            'found' => true,
            'customer' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'national_id' => $customer->national_id,
                'date_of_birth' => $customer->date_of_birth,
                'island' => $customer->island,
                'phone_number' => $customer->phone_number,
                'address' => $customer->address,
                'gender' => $customer->gender,
            ],
            'already_attached' => $alreadyAttached,
        ]);
    }
}
