<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bus extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'capacity',
        'bus_number',
        'color_code',
        'trip_id',
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
