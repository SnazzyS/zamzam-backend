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
            ->withPivot(['bus_id', 'flight_id', 'umrah_id', 'group_id', 'customer_type', 'visa_path'])
            ->withTimestamps();
    }

    public function groups()
    {
        return $this->hasMany(TripGroup::class);
    }

    public function buses()
    {
        return $this->hasMany(Bus::class);
    }

    public function flights()
    {
        return $this->hasMany(Flight::class);
    }

    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'room_trip', 'trip_id', 'room_id')
        ->withTimestamps();
    }

    public function invoices()
    {
        return $this->morphMany(Invoice::class, 'invoiceable');
    }

    public function hotels()
    {
        return $this->belongsToMany(Hotel::class)
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    public function primaryHotel()
    {
        return $this->hotels()->wherePivot('is_primary', true)->first();
    }

    public function activityTrips()
    {
        return $this->hasMany(ActivityTrip::class);
    }

    public function activities()
    {
        return $this->belongsToMany(Activity::class, 'activity_trips')
            ->withPivot('id', 'price_usd', 'price_mvr', 'price_sar', 'date', 'state')
            ->withTimestamps();
    }
}
