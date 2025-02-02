<?php

namespace App\Http\Controllers\Flight;

use App\Models\Trip;
use App\Models\Flight;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\FlightStoreRequest;

class UpdateFlightController extends Controller
{
    public function __invoke(Trip $trip, Flight $flight, FlightStoreRequest $request)
    {
        $flight->update($request->all());

        return response()->json([
            'message' => 'Flight updated successfully'
        ], 200);
    }
}
