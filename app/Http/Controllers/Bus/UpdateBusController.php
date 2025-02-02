<?php

namespace App\Http\Controllers\Bus;

use App\Models\Bus;
use App\Models\Trip;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\BusStoreRequest;

class UpdateBusController extends Controller
{
    public function __invoke(Trip $trip, Bus $bus, BusStoreRequest $request)
    {
        $bus->update($request->all());

        return response()->json([
            'message' => 'Bus updated successfully',
        ], 200);
    }
}
