<?php

namespace App\Http\Controllers\Trip;

use App\Models\Trip;
use App\Models\CustomerTrip;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ActiveTripsController extends Controller
{
    public function __invoke(Trip $trip)
    {
        return response()->json($trip->load('customers'));
    }
}
