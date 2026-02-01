<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'national_id',
        'date_of_birth',
        'island',
        'phone_number',
        'address',
        'gender',
        'name_in_english',
        'passport_number',
        'passport_issued_date',
        'passport_expiry_date',
        'is_foreigner',
        'country',
    ];

    protected $casts = [
        'is_foreigner' => 'boolean',
    ];



    public function trips()
    {
        return $this->belongsToMany(Trip::class)
            ->withPivot(['bus_id', 'flight_id', 'umrah_id', 'group_id', 'visa_path'])
            ->withTimestamps();
    }

    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'customer_room')
            ->withPivot('trip_id')
            ->withTimestamps();
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function photos()
    {
        return $this->hasMany(Photo::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function customerActivities()
    {
        return $this->hasMany(CustomerActivity::class);
    }

    public function activityTrips()
    {
        return $this->belongsToMany(ActivityTrip::class, 'customer_activities')
            ->withPivot('currency', 'amount_paid', 'payment_method', 'receipt_number', 'paid_at')
            ->withTimestamps();
    }
}
