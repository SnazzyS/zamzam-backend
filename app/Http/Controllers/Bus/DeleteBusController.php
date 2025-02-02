<?php

namespace App\Http\Controllers\Bus;

use App\Models\Bus;
use App\Models\Trip;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DeleteBusController extends Controller
{
    public function __invoke(Trip $trip, Bus $bus)
    {
        $bus->delete();

        return response()->json([
            'message' => 'Bus deleted successfully',
        ], 200);
    }
}
