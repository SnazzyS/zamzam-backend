<?php

namespace App\Http\Controllers\Trip;

use App\Models\Trip;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TripStoreRequest;

class TripUpdateController extends Controller
{
    public function __invoke(TripStoreRequest $request, Trip $trip)
    {
        $trip->update($request->all());

        return response()->json(['message' => 'Trip updated successfully'], 200);
    }
}
