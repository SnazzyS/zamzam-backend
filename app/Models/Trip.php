<?php

namespace App\Models;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    public function flights()
    {
        return $this->hasMany(Flight::class);
    }

    // public function invoices()
    // {
    //     return $this->hasMany(Invoice::class);
    // }

    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'room_trip', 'trip_id', 'room_id')
        ->withTimestamps();
    }

    public function invoices()
    {
        return $this->morphMany(Invoice::class, 'invoiceable');
    }

}
