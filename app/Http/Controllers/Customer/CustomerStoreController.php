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

        $customer = Customer::firstOrCreate(
            ['national_id' => $request->national_id],
            [
                'name' => $request->name,
                'date_of_birth' => $request->date_of_birth,
                'island' => $request->island,
                'phone_number' => $request->phone_number,
                'address' => $request->address,
                'gender' => $request->gender,
                // 'name_in_english' => $request->name_in_english,
                // 'passport_number' => $request->passport_number,
                // 'passport_issued_date' => $request->passport_issued_date,
                // 'passport_expiry_date' => $request->passport_expiry_date,
            ]
        );

        if (!$customer->trips->contains($trip->id)) {
            $customer->trips()->attach($trip->id, [
                'umrah_id' => $idgenerator->generateUmrahID($trip->id)
            ]);
            return response()->json(['message' => 'Customer created and attached to trip'], 201);
        }

        return response()->json(['message' => 'Customer updated and attached to trip'], 200);



    }
}
