<?php

namespace App\Http\Controllers\Room;

use App\Models\Trip;
use App\Models\Hotel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\RoomStoreRequest;

class CreateRoomController extends Controller
{
    public function __invoke(Trip $trip, Hotel $hotel, RoomStoreRequest $request)
    {

        $room = $hotel->rooms()->create([
            'room_number' => $request->room_number,
            'bed_count' => $request->bed_count,
            'hotel_id' => $hotel->id,
        ]);

        return response()->json($room, 201);
    }
}
