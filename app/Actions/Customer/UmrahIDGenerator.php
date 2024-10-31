<?php

namespace App\Actions\Customer;

use App\Models\Customer;
use App\Models\CustomerTrip;

class UmrahIDGenerator
{
    public function generateUmrahID($tripID)
    {

        $customerCount = CustomerTrip::where('trip_id', $tripID)->count();

        $generateUmrahID = 'T' . $tripID . '-' . (101 + $customerCount);

        return $generateUmrahID;
    }
}
