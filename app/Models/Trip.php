<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'price', 'departure_date', 'phone_number', 'hotel_address', 'trip_code'
    ];

    public function customers()
    {
        return $this->belongsToMany(Customer::class)
            ->withPivot(['bus_id', 'flight_id', 'room_id', 'umrah_id'])
            ->withTimestamps();
    }

    public function buses()
    {
        return $this->hasMany(Bus::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}
