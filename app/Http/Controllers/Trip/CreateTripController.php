<?php

namespace App\Http\Controllers\Trip;

use App\Models\Trip;
use App\Http\Controllers\Controller;
use App\Http\Requests\TripStoreRequest;
use App\Actions\Trip\TripIDGenerator;

class CreateTripController extends Controller
{
    public function __invoke(TripStoreRequest $request)
    {

        // Trip::create([
        //     'name' => $request->name,
        //     'price' => $request->price,
        //     'departure_date' => $request->departure_date,
        //     'phone_number' => $request->phone_number,
        //     'hotel_address' => $request->hotel_address,

        // ]);

        Trip::create($request->all());

        return response()->json(['message' => 'Trip created successfully'], 201);
    }
}
