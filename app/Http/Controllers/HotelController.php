<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\Trip;
use App\Http\Requests\HotelStoreRequest;
use Inertia\Inertia;

class HotelController extends Controller
{
    public function index(Trip $trip)
    {
        $hotels = Hotel::query()->get();

        return Inertia::render('Trips/Hotels/Index', [
            'trip' => $trip,
            'hotels' => $hotels,
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
}
