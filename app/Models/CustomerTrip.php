<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerTrip extends Model
{
    use HasFactory;

    protected $table = 'customer_trip';

    protected $fillable = [
        'customer_id',
        'trip_id',
        'bus_id',
        'flight_id',
        'room_id',
        'umrah_id',
        'hotel_id'
    ];

    
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }

     

}
