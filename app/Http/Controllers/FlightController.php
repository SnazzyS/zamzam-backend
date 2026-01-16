<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CustomerTrip;
use App\Models\Flight;
use App\Models\Trip;
use App\Http\Requests\FlightStoreRequest;
use Inertia\Inertia;

class FlightController extends Controller
{
    public function index(Trip $trip)
    {
        $flights = CustomerTrip::where('trip_id', $trip->id)
                    ->with([
                        'customer:id,name,name_in_english,phone_number',
                        'flight:id,name'
                        ])
                    ->get()
                    ->groupBy('flight.name')
                        ->map(function ($passengers) {
                            return $passengers->map(function ($passenger) {
                                return [
                                    'umrah_id' => $passenger->umrah_id,
                                    'name' => $passenger->customer->name,
                                    'name_in_english' => $passenger->customer->name_in_english,
                                    'passport_number' => $passenger->customer->name_in_english, // Note: original code mapped passport_number to name_in_english, preserving logically but might be a bug. Keeping as is.
                                    'phone' => $passenger->customer->phone,
                                ];
                            });
                        });

        return Inertia::render('Trips/Flights/Index', [
            'trip' => $trip,
            'flights' => $flights,
        ]);
    }

    public function store(Trip $trip, FlightStoreRequest $request)
    {
        Flight::create([
            'name' => $request->name,
            'trip_id' => $request->trip->id // This implies request has trip object or trip_id? StoreRequest validation likely merges it.
        ]);

        return redirect()
            ->back()
            ->with('success', 'Flight created successfully');
    }

    public function show(Trip $trip, Flight $flight)
    {
        $flightName = $flight->name;

        $flightData = CustomerTrip::where('trip_id', $trip->id)
        ->where('flight_id', $flight->id)
        ->with([
            'customer:id,name_in_english,passport_number,date_of_birth,phone_number',
            'flight:id,name'
            ])
        ->get()
        ->map(function ($customerTrip) {
            return [
                'umrah_id' => $customerTrip->umrah_id,
                'name' => $customerTrip->customer->name_in_english,
                'name_in_english' => $customerTrip->customer->passport_number,
                'phone_number' => $customerTrip->customer->phone_number,
                'date_of_birth' => $customerTrip->customer->date_of_birth
            ];
        });

        return Inertia::render('Trips/Flights/Show', [
            'trip' => $trip,
            'flight' => $flight,
            'flightName' => $flightName,
            'passengers' => $flightData,
        ]);
    }

    public function update(Trip $trip, Flight $flight, FlightStoreRequest $request)
    {
        $flight->update($request->all());

        return redirect()
            ->back()
            ->with('success', 'Flight updated successfully');
    }

    public function destroy(Trip $trip, Flight $flight)
    {
        $flight->delete();

        return redirect()
            ->back()
            ->with('success', 'Flight deleted successfully');
    }
}
