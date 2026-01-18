<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\TripStoreRequest;
use App\Models\Trip;
use Inertia\Inertia;

class TripController extends Controller
{
    public function show(Trip $trip)
    {
        return Inertia::render('Trips/Show', [
            'trip' => $trip->load([
                'customers' => function ($query) {
                    $query->orderBy('name');
                },
                'groups' => function ($query) {
                    $query->orderBy('type')->orderBy('name');
                },
            ]),
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
