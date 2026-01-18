<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_id',
        'name',
        'type',
    ];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'customer_trip', 'group_id', 'customer_id')
            ->withPivot(['trip_id', 'bus_id', 'flight_id', 'umrah_id', 'group_id'])
            ->withTimestamps();
    }
}
