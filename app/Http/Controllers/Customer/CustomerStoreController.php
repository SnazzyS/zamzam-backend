<?php

namespace App\Http\Controllers\Customer;

use App\Actions\Customer\UmrahIDGenerator;
use App\Models\Trip;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerStoreRequest;

class CustomerStoreController extends Controller
{
    public function __invoke(Trip $trip, CustomerStoreRequest $request, UmrahIDGenerator $idgenerator)
    {
        Customer::create([
            'name' => $request->name,
            'id_card' => $request->id_card,
            'date_of_birth' => $request->date_of_birth,
            'island' => $request->island,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'gender' => $request->gender,
            'umrah_id' => $idgenerator->generateUmrahID($trip->id),
            'trip_id' => $trip->id,
        ]);

        return response()->json(['message' => 'Customer created'], 201);

    }
}
