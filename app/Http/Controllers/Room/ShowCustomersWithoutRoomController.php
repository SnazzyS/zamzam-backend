<?php

namespace App\Http\Controllers\Room;

use App\Models\Trip;
use App\Models\Hotel;
use App\Models\Customer;
use App\Models\CustomerRoom;
use App\Models\CustomerTrip;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ShowCustomersWithoutRoomController extends Controller
{
    public function __invoke(Trip $trip, Hotel $hotel)
    {
        $customersWithoutRoom = $trip->customers()
            ->whereDoesntHave('rooms', function ($query) use ($trip, $hotel) {
                $query->where('rooms.hotel_id', $hotel->id)
                      ->where('customer_room.trip_id', $trip->id);
            })
            // ->with('trips')
            ->get()
            ->map(function ($customer) {
                // dd($customer);
                return [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'umrah_id' => $customer->pivot->umrah_id,
                ];
            });

        // ->map(function ($customer) {
        //     $customer->umrah_id = optional($customer->trips->first())->pivot->umrah_id;
        //     unset($customer->trips);
        //     return $customer;
        // });

        return response()->json($customersWithoutRoom);
    }
}
