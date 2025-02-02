<?php

namespace App\Http\Controllers\Flight;

use App\Models\Trip;
use App\Models\Flight;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\FlightStoreRequest;

class CreateFlightController extends Controller
{
    public function __invoke(Trip $trip, FlightStoreRequest $request)
    {
        Flight::create([
            'name' => $request->name,
            'trip_id' => $request->trip->id
        ]);

        return response()->json([
            'message' => 'ފްލައިޓް ހެދިއްޖެ'
        ], 201);
    }
}
