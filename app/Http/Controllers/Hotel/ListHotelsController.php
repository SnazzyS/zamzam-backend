<?php

namespace App\Http\Controllers\Hotel;

use App\Models\Trip;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ListHotelsController extends Controller
{
    public function __invoke(Trip $trip)
    {
        return response()->json($trip->load('hotels'));
    }
}
