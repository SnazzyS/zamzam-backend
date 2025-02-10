<?php

namespace App\Http\Controllers\Hotel;

use App\Models\Trip;
use App\Models\Hotel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\HotelStoreRequest;

class CreateHotelController extends Controller
{
    public function __invoke(Trip $trip, HotelStoreRequest $request)
    {
        Hotel::create([
            'name' => $request->name,
            'address' => $request->address,
            'phone_number' => $request->phone_number,
            'trip_id' => $trip->id
        ]);

        return response()->json([
            'message' => "ހޮޓާ ހެދިއްޖެ"
        ], 201);
    }
}
