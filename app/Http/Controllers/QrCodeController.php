<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Trip;
use Illuminate\Http\Request;
use Inertia\Inertia;

class QrCodeController extends Controller
{
    public function show(Trip $trip, Customer $customer)
    {
        // Get customer trip pivot data
        $customerTrip = $trip->customers()
            ->where('customer_id', $customer->id)
            ->first();

        if (!$customerTrip) {
            abort(404, 'Customer not found in this trip');
        }

        // Get flight details
        $flight = null;
        if ($customerTrip->pivot->flight_id) {
            $flight = $trip->flights()->find($customerTrip->pivot->flight_id);
        }

        // Get bus details
        $bus = null;
        if ($customerTrip->pivot->bus_id) {
            $bus = $trip->buses()->find($customerTrip->pivot->bus_id);
        }

        // Get room details
        $room = \DB::table('customer_room')
            ->join('rooms', 'customer_room.room_id', '=', 'rooms.id')
            ->join('hotels', 'rooms.hotel_id', '=', 'hotels.id')
            ->where('customer_room.trip_id', $trip->id)
            ->where('customer_room.customer_id', $customer->id)
            ->select('rooms.room_number', 'hotels.name as hotel_name', 'hotels.name_in_arabic as hotel_name_arabic', 'hotels.address as hotel_address')
            ->first();

        // Get primary hotel if no room assigned
        $primaryHotel = $trip->primaryHotel();

        return Inertia::render('Public/CustomerCard', [
            'trip' => [
                'name' => $trip->name,
                'departure_date' => $trip->departure_date,
                'phone_number' => $trip->phone_number,
            ],
            'customer' => [
                'name' => $customer->name,
                'name_in_english' => $customer->name_in_english,
                'passport_number' => $customer->passport_number,
                'phone_number' => $customer->phone_number,
                'umrah_id' => $customerTrip->pivot->umrah_id,
                'visa_path' => $customerTrip->pivot->visa_path ? '/storage/' . $customerTrip->pivot->visa_path : null,
            ],
            'flight' => $flight ? [
                'name' => $flight->name,
                'departure_date' => $flight->departure_date,
                'departure_time' => $flight->departure_time,
                'arrival_date' => $flight->arrival_date,
                'arrival_time' => $flight->arrival_time,
            ] : null,
            'bus' => $bus ? [
                'name' => $bus->name,
                'bus_number' => $bus->bus_number,
            ] : null,
            'room' => $room ? [
                'room_number' => $room->room_number,
                'hotel_name' => $room->hotel_name,
                'hotel_name_arabic' => $room->hotel_name_arabic,
                'hotel_address' => $room->hotel_address,
            ] : null,
            'primaryHotel' => $primaryHotel ? [
                'name' => $primaryHotel->name,
                'name_in_arabic' => $primaryHotel->name_in_arabic,
                'address' => $primaryHotel->address,
            ] : null,
        ]);
    }
}
