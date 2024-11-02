<?php

namespace App\Http\Controllers\Bus;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BusDestroyController extends Controller
{
    public function __invoke(Trip $trip, Bus $bus)
    {
        $bus->delete();

        return response()->json([
            'message' => 'Bus deleted successfully',
        ], 200);
    }
}
