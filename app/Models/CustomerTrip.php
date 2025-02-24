<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CustomerTrip extends Pivot
{
    // use HasFactory;

    protected $table = 'customer_trip';

    public $timestamps = true;


    protected $fillable = [
        'customer_id',
        'trip_id',
        'bus_id',
        'flight_id',
        'umrah_id',
    ];

    
    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }

    public function flight()
    {
        return $this->belongsTo(Flight::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

     

}
