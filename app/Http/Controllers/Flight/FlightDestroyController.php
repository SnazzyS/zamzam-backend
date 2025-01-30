<?php

namespace App\Http\Controllers\Flight;

use App\Models\Trip;
use App\Models\Flight;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FlightDestroyController extends Controller
{
    public function __invoke(Trip $trip, Flight $flight)
    {
        $flight->delete();

        return response()->json([
            'message' => "Flight Deleted"
        ]);
    }
}
