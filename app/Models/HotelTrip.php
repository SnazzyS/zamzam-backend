<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class HotelTrip extends Pivot
{

    protected $table = 'hotel_trip';

    protected $fillable = [
        'hotel_id',
        'trip_id'
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

}
