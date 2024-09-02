<?php

namespace App\Actions\Customer;

use App\Models\Customer;

class UmrahIDGenerator
{
    public function generateUmrahID($tripID)
    {
        $customerCount = Customer::where('trip_id', $tripID)->count();

        $generateUmrahID = 'T' . $tripID . '-' . (101 + $customerCount);

        return $generateUmrahID;
    }
}
