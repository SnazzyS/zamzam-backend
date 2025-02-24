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
            'name' => $request->name,
            'bed_count' => $request->bed_count,
            'private' => $request->private,
            'price' => $request->price,
            'currency' => $request->currency,
            'hotel_id' => $hotel->id,
            'trip_id' => $trip->id
        ]);

        if ($request->private) {
            dd('here, create an invoice for the customer');
            
        }

      

        return response()->json($room, 201);
    }
}
