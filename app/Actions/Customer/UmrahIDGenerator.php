<?php

namespace App\Actions\Customer;

use App\Models\Customer;
use App\Models\CustomerTrip;

class UmrahIDGenerator
{
    public function generateUmrahID($tripID)
    {
        // dd($tripID);
        $lastUmrahID = CustomerTrip::where('trip_id', $tripID)
            ->orderBy('umrah_id', 'desc')
            ->value('umrah_id');

        // dd($lastNumber);
        if ($lastUmrahID) {
            $lastNumber = (int) substr($lastUmrahID, strrpos($lastUmrahID, '-') + 1);
        } else {
            $lastNumber = 100;
        }

        $nextNumber = $lastNumber + 1;
        $generateUmrahID = 'T' . $tripID . '-' . $nextNumber;

        return $generateUmrahID;
    }
}
