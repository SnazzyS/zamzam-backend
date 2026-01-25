<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flight extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'trip_id',
        'departure_date',
        'departure_time',
        'return_date',
        'return_time',
        'departure_flight_number',
        'departure_transit_flight_number',
        'return_flight_number',
        'return_transit_flight_number',
    ];

    protected $casts = [
        'departure_date' => 'date',
        'return_date' => 'date',
    ];


    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function customerTrips()
    {
        return $this->hasMany(CustomerTrip::class);
    }
}
