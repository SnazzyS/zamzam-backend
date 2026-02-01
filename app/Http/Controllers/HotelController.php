<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\Trip;
use App\Http\Requests\HotelStoreRequest;
use App\Models\CustomerRoom;
use Inertia\Inertia;

class HotelController extends Controller
{
    public function index(Trip $trip)
    {
        $hotels = Hotel::query()
            ->withCount('rooms')
            ->orderBy('name')
            ->get();

        $attachedHotels = $trip->hotels()
            ->withPivot('is_primary')
            ->get();

        $attachedHotelIds = $attachedHotels->pluck('id')->values();
        $primaryHotel = $attachedHotels->first(fn($h) => $h->pivot->is_primary);
        $primaryHotelId = $primaryHotel?->id;

        return Inertia::render('Trips/Hotels/Index', [
            'trip' => $trip,
            'hotels' => $hotels,
            'attachedHotelIds' => $attachedHotelIds,
            'primaryHotelId' => $primaryHotelId,
        ]);
    }

    public function store(Trip $trip, HotelStoreRequest $request)
    {
        Hotel::updateOrCreate([
            'name' => $request->name,
            'address' => $request->address,
            'phone_number' => $request->phone_number,
        ]);

        return redirect()
            ->back()
            ->with('success', 'ހޮޓާ ހެދިއްޖެ');
    }

    public function update(Trip $trip, Hotel $hotel, HotelStoreRequest $request)
    {
        $existingHotel = Hotel::where('name', $request->name)
        ->where('id', '!=', $hotel->id)
        ->first();

        if ($existingHotel) {
            return redirect()
                ->back()
                ->withErrors(['hotel' => 'ހޮޓަލެއް މިނަމުގައި ވަނީ ރަޖިސްޓްރީކުރެވިފައި']);
        }

        $hotel->update($request->all());

        return redirect()
            ->back()
            ->with('success', 'ހޮޓާ އަޕްޑޭޓުކުރެވިއްޖެ');
    }

    public function destroy(Trip $trip, Hotel $hotel)
    {
        $hotel->delete();

        return redirect()
            ->back()
            ->with('success', 'ހޮޓާ ޑިލީޓް ކުރެވިއްޖެ');
    }

    public function attach(Trip $trip, Hotel $hotel)
    {
        $trip->hotels()->syncWithoutDetaching([$hotel->id]);

        $rooms = $hotel->rooms()->pluck('id')->all();
        if (!empty($rooms)) {
            $pivotData = [];
            foreach ($rooms as $roomId) {
                $pivotData[$roomId] = ['hotel_name' => $hotel->name];
            }
            $trip->rooms()->syncWithoutDetaching($pivotData);
        }

        return redirect()
            ->back()
            ->with('success', 'ހޮޓާ ދަތުރަށް އެޅުއްވާލައިފި');
    }

    public function detach(Trip $trip, Hotel $hotel)
    {
        $roomIds = $hotel->rooms()->pluck('id')->all();

        if (!empty($roomIds)) {
            CustomerRoom::where('trip_id', $trip->id)
                ->whereIn('room_id', $roomIds)
                ->delete();

            $trip->rooms()->detach($roomIds);
        }

        $trip->hotels()->detach($hotel->id);

        return redirect()
            ->back()
            ->with('success', 'ހޮޓާ ދަތުރުން ނެގިއްޖެ');
    }

    public function setPrimary(Trip $trip, Hotel $hotel)
    {
        // First, unset all hotels as primary for this trip
        \DB::table('hotel_trip')
            ->where('trip_id', $trip->id)
            ->update(['is_primary' => false]);

        // Then set the selected hotel as primary
        \DB::table('hotel_trip')
            ->where('trip_id', $trip->id)
            ->where('hotel_id', $hotel->id)
            ->update(['is_primary' => true]);

        return redirect()
            ->back()
            ->with('success', 'Primary hotel set successfully');
    }
}
