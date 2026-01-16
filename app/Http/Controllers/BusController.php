<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use App\Models\CustomerTrip;
use App\Models\Trip;
use App\Http\Requests\BusStoreRequest;
use Inertia\Inertia;

class BusController extends Controller
{
    public function index(Trip $trip)
    {
        $buses = CustomerTrip::where('trip_id', $trip->id)
            ->whereNotNull('bus_id')
            ->with([
                'customer' => function ($query) {
                    $query->select(
                        'id',
                        'name',
                        'name_in_english',
                        'passport_number',
                    );
                },
                'bus' => function ($query) {
                    $query->select(
                        'id',
                        'name',
                    );
                }
            ])->get()
            ->groupBy('bus_id')
            ->map(function ($passengers) {
                $busDetails = $passengers->first()->bus;

                $totalPassengers = $passengers->count();

                $passengersData = $passengers->map(function ($passenger) {
                    return [
                        'id' => $passenger->customer->id,
                        'name' => $passenger->customer->name,
                        'name_in_english' => $passenger->customer->name_in_english,
                        'passport_number' => $passenger->customer->passport_number,
                    ];
                })->values()->all();


                return [
                    'id' => $busDetails->id,
                    'name' => $busDetails->name,
                    'total_passengers' => $totalPassengers,
                    'passengers' => $passengersData
                ];
            });

        return Inertia::render('Trips/Buses/Index', [
            'trip' => $trip,
            'buses' => $buses,
        ]);
    }

    public function store(Trip $trip, BusStoreRequest $request)
    {
        $bus = Bus::create([
            'name' => $request->name,
            'capacity' => $request->capacity,
            'bus_number' => $request->bus_number,
            'color_code' => $request->color_code,
            'trip_id' => $trip->id,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Bus created successfully');
    }

    public function show(Trip $trip, Bus $bus)
    {
        $busData = CustomerTrip::where('trip_id', $trip->id)
            ->where('bus_id', $bus->id)
            ->with([
                'customer:id,name,name_in_english,passport_number',
                'bus:id,name',
                ])
            ->get();

        return Inertia::render('Trips/Buses/Show', [
            'trip' => $trip,
            'bus' => $bus,
            'passengers' => $busData,
        ]);
    }

    public function update(Trip $trip, Bus $bus, BusStoreRequest $request)
    {
        $bus->update($request->all());

        return redirect()
            ->back()
            ->with('success', 'Bus updated successfully');
    }

    public function destroy(Trip $trip, Bus $bus)
    {
        $bus->delete();

        return redirect()
            ->back()
            ->with('success', 'Bus deleted successfully');
    }
}
