<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'umrah_id',
        'name',
        'id_card',
        'date_of_birth',
        'island',
        'phone_number',
        'address',
        'gender',
        'name_in_english',
        'passport_number',
        'passport_issued_date',
        'passport_expiry_date',
        'photo',
        'trip_id'
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

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function localtrip()
    {
        return $this->belongsTo(LocalTrip::class);
    }

    public function photo()
    {
        return $this->hasOne(CustomerPhoto::class);
    }
}
