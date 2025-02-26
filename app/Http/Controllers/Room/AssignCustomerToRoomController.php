<?php

namespace App\Http\Controllers\Room;

use App\Models\Room;
use App\Models\Trip;
use App\Models\Hotel;
use App\Models\Customer;
use App\Models\CustomerRoom;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AssignCustomerToRoomController extends Controller
{
    public function __invoke(Trip $trip, Hotel $hotel, Room $room, Request $request)
    {
        // add validation?

        $customer = Customer::findOrFail($request->customer_id);

        $customerAlreadyAssigned = CustomerRoom::where('customer_id', $customer->id)
            ->where('trip_id', $trip->id)
            ->exists();

        if ($customerAlreadyAssigned) {
            return response()->json(['message' => 'ކަސްޓަމަރު ހުރީ ކޮޓަރީގައި'], 400);
        }

        if (CustomerRoom::where('room_id', $room->id)->count() >= $room->bed_count) {
            return response()->json(['message' => 'ކޮޓަރި ފުރިފަ'], 400);
        }

        CustomerRoom::create([
            'customer_id' => $customer->id,
            'room_id' => $room->id,
            'trip_id' => $trip->id,
        ]);

        return response()->json(['message' => 'ކަސްޓަމަރު ކޮޓަރިއަށް ކަނޑައެޅިއްޖެ'], 200);
    }
}
