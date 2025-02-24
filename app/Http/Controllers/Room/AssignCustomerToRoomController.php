<?php

namespace App\Http\Controllers\Room;

use App\Models\Room;
use App\Models\Trip;
use App\Models\Hotel;
use App\Models\Customer;
use App\Models\CustomerRoom;
use App\Models\CustomerTrip;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AssignCustomerToRoomController extends Controller
{
    public function __invoke(Trip $trip, Hotel $hotel, Room $room, Request $request)
    {
        $customer = Customer::findOrFail($request->customer_id);

        $numberOfCustomersInRoom = CustomerRoom::where('room_id', $room->id)->count();
        
        $roomCapacity = $room->bed_count;

        dd($numberOfCustomersInRoom);

        if ($numberOfCustomersInRoom >= $roomCapacity) {
            return response()->json([
                'message' => 'Room is full',
            ], 400);
        }
        // dd($roomCapacity);

        // dd($room->customers());
        
        
    }
}
