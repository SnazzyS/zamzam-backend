<?php

namespace App\Http\Controllers\Room;

use App\Models\Room;
use App\Models\Trip;
use App\Models\Hotel;
use App\Models\Customer;
use App\Models\CustomerRoom;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RemoveCustomerFromRoomController extends Controller
{
    public function __invoke(Trip $trip, Hotel $hotel, Room $room, Request $request)
    {
        // should validate? should request have customer id?

        $customer = Customer::findOrFail($request->customer_id);

        CustomerRoom::where('customer_id', $customer->id)
            ->where('room_id', $room->id)
            ->where('trip_id', $trip->id)
            ->delete();

        return response()->json([
            'message' => 'ކަސްޓަމަރު ކޮޓަރިން ނެރެވިއްޖެ'
        ]);
    }
}
