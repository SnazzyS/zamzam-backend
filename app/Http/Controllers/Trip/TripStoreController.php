<?php

namespace App\Http\Controllers\Trip;

use App\Models\Trip;
use App\Http\Controllers\Controller;
use App\Http\Requests\TripStoreRequest;

class TripStoreController extends Controller
{
    public function __invoke(TripStoreRequest $request)
    {
        Trip::create($request->validated());

        return response()->json(['message' => 'Trip created successfully'], 201);
    }
}
