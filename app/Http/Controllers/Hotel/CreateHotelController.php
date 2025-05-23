<?php

namespace App\Http\Controllers\Hotel;

use App\Models\Trip;
use App\Models\Hotel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\HotelStoreRequest;
use App\Models\HotelTrip;

class CreateHotelController extends Controller
{
    public function __invoke(Trip $trip, HotelStoreRequest $request)
    {
        $hotel = Hotel::updateOrCreate([
            'name' => $request->name,
            'address' => $request->address,
            'phone_number' => $request->phone_number,
        ]);

    
        return response()->json([
            'message' => "ހޮޓާ ހެދިއްޖެ"
        ], 201);
    }
}
