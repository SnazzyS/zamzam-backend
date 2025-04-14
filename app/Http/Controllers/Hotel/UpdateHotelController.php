<?php

namespace App\Http\Controllers\Hotel;

use App\Models\Trip;
use App\Models\Hotel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\HotelStoreRequest;

class UpdateHotelController extends Controller
{
    public function __invoke(Trip $trip, Hotel $hotel, HotelStoreRequest $request)
    {
        // $hotel->update($request->all());

        $existingHotel = Hotel::where('name', $request->name)
        ->where('id', '!=', $hotel->id)
        ->first();
    
        if ($existingHotel) {
            return response()->json([
                'message' => 'ހޮޓަލެއް މިނަމުގައި ވަނީ ރަޖިސްޓްރީކުރެވިފައި',
            ], 422);
        }
    
        $hotel->update($request->all());

        return response()->json([
            'message' => 'ހޮޓާ އަޕްޑޭޓުކުރެވިއްޖެ'
        ]);

        return response()->json([
            'message' => 'ހޮޓާ އަޕްޑޭޓުކުރެވިއްޖެ '
        ]);
    }
}
