<?php

namespace App\Http\Controllers\Hotel;

use App\Models\Trip;
use App\Models\Hotel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DeleteHotelController extends Controller
{
    public function __invoke(Trip $trip, Hotel $hotel)
    {
        $hotel->delete();

        return response()->json([
            'message' => 'ހޮޓާ ޑިލީޓް ކުރެވިއްޖެ'
        ]);
    }
}
