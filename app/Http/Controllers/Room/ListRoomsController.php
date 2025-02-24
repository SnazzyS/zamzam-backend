<?php

namespace App\Http\Controllers\Room;

use App\Models\Hotel;
use App\Models\Trip;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ListRoomsController extends Controller
{
    public function __invoke(Trip $trip, Hotel $hotel)
    {
        $rooms = $hotel->rooms()->where('trip_id', $trip->id)->get();
        
        return response()->json($rooms);
    }
}
