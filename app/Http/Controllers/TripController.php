<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\TripStoreRequest;
use App\Models\Invoice;
use App\Models\Setting;
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
            // Use invoice grand_total (price at registration), not current trip price
            $grandTotal = $invoice->grand_total ?? $trip->price;
            $balance = $grandTotal - $discount - $totalPaid;

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
            ->to("/office/trips/{$trip->id}")
            ->with('success', 'Trip created successfully');
    }

    public function update(TripStoreRequest $request, Trip $trip)
    {
        $trip->update($request->all());

        return redirect()
            ->back()
            ->with('success', 'Trip updated successfully');
    }

    public function idCards(Trip $trip)
    {
        // Get optional customer IDs filter from query parameter
        $customerIds = request()->query('customers');
        $filterIds = $customerIds ? explode(',', $customerIds) : null;

        $trip->load([
            'customers' => function ($query) use ($filterIds) {
                if ($filterIds) {
                    $query->whereIn('customers.id', $filterIds);
                }
                $query->with(['photos', 'rooms' => function ($q) use ($query) {
                    // We'll filter by trip_id in the map
                }])->orderBy('customer_trip.created_at');
            },
            'buses',
            'flights',
        ]);

        // Create maps for bus and flight data
        $busesMap = $trip->buses->keyBy('id');
        $flightsMap = $trip->flights->keyBy('id');

        // Get customer room assignments for this trip
        $customerRooms = \DB::table('customer_room')
            ->join('rooms', 'customer_room.room_id', '=', 'rooms.id')
            ->join('hotels', 'rooms.hotel_id', '=', 'hotels.id')
            ->where('customer_room.trip_id', $trip->id)
            ->select('customer_room.customer_id', 'rooms.room_number', 'hotels.name as hotel_name')
            ->get()
            ->keyBy('customer_id');

        // Get primary hotel for this trip
        $primaryHotel = $trip->primaryHotel();

        $customers = $trip->customers->map(function ($customer) use ($busesMap, $flightsMap, $customerRooms, $primaryHotel) {
            $profilePhoto = $customer->photos->where('type', 'profile')->first();
            $bus = $customer->pivot->bus_id ? $busesMap->get($customer->pivot->bus_id) : null;
            $flight = $customer->pivot->flight_id ? $flightsMap->get($customer->pivot->flight_id) : null;
            $room = $customerRooms->get($customer->id);

            return [
                'id' => $customer->id,
                'name' => $customer->name,
                'name_in_english' => $customer->name_in_english,
                'national_id' => $customer->national_id,
                'passport_number' => $customer->passport_number,
                'phone_number' => $customer->phone_number,
                'island' => $customer->island,
                'umrah_id' => $customer->pivot->umrah_id,
                'customer_type' => $customer->pivot->customer_type,
                'photo_path' => $profilePhoto ? '/storage/' . $profilePhoto->path : null,
                'bus_name' => $bus?->name,
                'bus_color' => $bus?->color_code,
                // QR code data
                'flight_name' => $flight?->name,
                'flight_departure' => $flight?->departure_date,
                'room_number' => $room?->room_number,
                'room_hotel' => $room?->hotel_name ?? $primaryHotel?->name,
            ];
        });

        // Get company settings
        $companySettings = [
            'address' => Setting::get('company_address', ''),
            'phone' => Setting::get('company_phone', ''),
        ];

        return Inertia::render('Trips/IdCards', [
            'trip' => $trip,
            'customers' => $customers,
            'primaryHotel' => $primaryHotel ? [
                'name' => $primaryHotel->name,
                'name_in_arabic' => $primaryHotel->name_in_arabic,
                'address' => $primaryHotel->address,
            ] : null,
            'companySettings' => $companySettings,
        ]);
    }

    public function customerList(Trip $trip)
    {
        $trip->load([
            'customers' => function ($query) {
                $query->orderBy('customer_trip.created_at');
            },
            'flights',
        ]);

        // Create a map of flight_id to flight data
        $flightsMap = $trip->flights->keyBy('id');

        $customers = $trip->customers->map(function ($customer) use ($flightsMap) {
            $flight = $customer->pivot->flight_id ? $flightsMap->get($customer->pivot->flight_id) : null;

            return [
                'id' => $customer->id,
                'name' => $customer->name,
                'name_in_english' => $customer->name_in_english,
                'national_id' => $customer->national_id,
                'passport_number' => $customer->passport_number,
                'phone_number' => $customer->phone_number,
                'address' => $customer->address,
                'island' => $customer->island,
                'date_of_birth' => $customer->date_of_birth,
                'gender' => $customer->gender,
                'umrah_id' => $customer->pivot->umrah_id,
                'customer_type' => $customer->pivot->customer_type,
                'flight_name' => $flight?->name,
                'departure_date' => $flight?->departure_date,
            ];
        });

        return Inertia::render('Trips/CustomerList', [
            'trip' => $trip,
            'customers' => $customers,
        ]);
    }
}
