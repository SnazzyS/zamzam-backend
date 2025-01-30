<?php

namespace App\Http\Controllers\Bus;

use App\Models\CustomerTrip;
use App\Models\Trip;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BusIndexController extends Controller
{
    public function __invoke(Trip $trip)
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

        return response()->json([
            'trip_id' => $trip->id,
            'trip_name' => $trip->name,
            'buses' => $buses
        ]);
    }
}
