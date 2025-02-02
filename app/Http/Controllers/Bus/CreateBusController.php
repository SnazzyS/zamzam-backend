<?php

namespace App\Http\Controllers\Bus;

use App\Models\Bus;
use App\Models\Trip;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\BusStoreRequest;

class CreateBusController extends Controller
{
    public function __invoke(Trip $trip, BusStoreRequest $request)
    {
        $bus = Bus::create([
            'name' => $request->name,
            'capacity' => $request->capacity,
            'bus_number' => $request->bus_number,
            'color_code' => $request->color_code,
            'trip_id' => $trip->id,
        ]);

        return response()->json([
            'message' => 'Bus created successfully',
        ], 201);
    }
}
