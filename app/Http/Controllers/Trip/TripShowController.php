<?php

namespace App\Http\Controllers\Trip;

use App\Models\Trip;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TripShowController extends Controller
{
    public function __invoke(Trip $trip)
    {
        // return response()->json($trip->load('customers'));

        return response()->json($trip->load(['customers' => function ($query) {
            $query->select('id', 'trip_id', 'name', 'date_of_birth', 'phone_number', 'id_card', 'island', 'address');
        }]));
    }
}
