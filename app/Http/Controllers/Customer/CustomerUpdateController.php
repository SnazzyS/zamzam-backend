<?php

namespace App\Http\Controllers\Customer;

use App\Models\Trip;
use App\Models\Customer;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerStoreRequest;
use Carbon\Carbon;

class CustomerUpdateController extends Controller
{
    public function __invoke(Trip $trip, Customer $customer, CustomerStoreRequest $request)
    {

        $data = [
            'name' => $request->name,
            'id_card' => $request->id_card,
            'date_of_birth' => $request->date_of_birth,
            'island' => $request->island,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'gender' => $request->gender,
            'name_in_english' => $request->name_in_english,
            'passport_number' => $request->passport_number,
            'passport_issued_date' => $request->passport_issued_date,
        ];

        if ($request->filled('passport_expiry_date')) {
            $data['passport_expiry_date'] = $request->passport_expiry_date;
        } elseif ($request->filled('passport_issued_date')) {
            $data['passport_expiry_date'] = Carbon::parse($request->passport_issued_date)->addYears(5)->toDateString();
        }

        //        if($data['photo_url'])

        $customer->update($data);



        return response()->json([
            'messaage' => "Customer updated successfully"
        ], 200);
    }
}
