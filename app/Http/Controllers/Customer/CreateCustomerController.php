<?php

namespace App\Http\Controllers\Customer;

use App\Models\Trip;
use App\Models\Invoice;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Actions\Customer\UmrahIDGenerator;
use App\Http\Requests\CustomerStoreRequest;
use Intervention\Image\Facades\Image;

class CreateCustomerController extends Controller
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
     
            ]
        );




        if (!$customer->trips->contains($trip->id)) {
            $customer->trips()->attach($trip->id, [
                'umrah_id' => $idgenerator->generateUmrahID($trip->id)
            ]);

            
            Invoice::create([
                'customer_id' => $customer->id,
                'trip_id' => $trip->id,
                'balance' => $trip->price,
                'amount' => 0,
                'discount' => 0,
                'invoice_number' => 'INV/2024/01',
                'grand_total' => $trip->price
            ]);

            
            return response()->json(['message' => 'Customer created and attached to trip'], 201);
        }


        return response()->json(['message' => 'Customer created'], 200);



    }
}
