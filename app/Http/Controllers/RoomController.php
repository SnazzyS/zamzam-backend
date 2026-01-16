<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoomStoreRequest;
use App\Models\Customer;
use App\Models\CustomerRoom;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\Trip;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RoomController extends Controller
{
    public function index(Trip $trip, Hotel $hotel)
    {
        $rooms = $hotel->rooms()->where('trip_id', $trip->id)->get();
        return Inertia::render('Trips/Hotels/Rooms/Index', [
            'trip' => $trip,
            'hotel' => $hotel,
            'rooms' => $rooms,
        ]);
    }

    public function store(Trip $trip, Hotel $hotel, RoomStoreRequest $request)
    {
        $room = $hotel->rooms()->create([
            'room_number' => $request->room_number,
            'bed_count' => $request->bed_count,
            'hotel_id' => $hotel->id,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Room created successfully');
    }

    public function assignCustomer(Trip $trip, Hotel $hotel, Room $room, Request $request)
    {
        $customer = Customer::findOrFail($request->customer_id);

        $customerAlreadyAssigned = CustomerRoom::where('customer_id', $customer->id)
            ->where('trip_id', $trip->id)
            ->exists();

        if ($customerAlreadyAssigned) {
            return redirect()
                ->back()
                ->withErrors(['assignCustomer' => 'ކަސްޓަމަރު ހުރީ ކޮޓަރީގައި']);
        }

        if (CustomerRoom::where('room_id', $room->id)->count() >= $room->bed_count) {
            return redirect()
                ->back()
                ->withErrors(['assignCustomer' => 'ކޮޓަރި ފުރިފަ']);
        }

        CustomerRoom::create([
            'customer_id' => $customer->id,
            'room_id' => $room->id,
            'trip_id' => $trip->id,
        ]);

        return redirect()
            ->back()
            ->with('success', 'ކަސްޓަމަރު ކޮޓަރިއަށް ކަނޑައެޅިއްޖެ');
    }

    public function removeCustomer(Trip $trip, Hotel $hotel, Room $room, Request $request)
    {
        $customer = Customer::findOrFail($request->customer_id);

        CustomerRoom::where('customer_id', $customer->id)
            ->where('room_id', $room->id)
            ->where('trip_id', $trip->id)
            ->delete();

        return redirect()
            ->back()
            ->with('success', 'ކަސްޓަމަރު ކޮޓަރިން ނެރެވިއްޖެ');
    }

    public function customersWithoutRoom(Trip $trip, Hotel $hotel)
    {
        $customersWithoutRoom = $trip->customers()
            ->whereDoesntHave('rooms', function ($query) use ($trip, $hotel) {
                $query->where('rooms.hotel_id', $hotel->id)
                      ->where('customer_room.trip_id', $trip->id);
            })
            ->get()
            ->map(function ($customer) {
                return [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'umrah_id' => $customer->pivot->umrah_id,
                ];
            });

        return Inertia::render('Trips/Hotels/Rooms/CustomersWithoutRoom', [
            'trip' => $trip,
            'hotel' => $hotel,
            'customers' => $customersWithoutRoom,
        ]);
    }
}
