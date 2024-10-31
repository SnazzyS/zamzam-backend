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
        'photo_url'
    ];


    public function trips()
    {
        return $this->belongsToMany(Trip::class)
            ->withPivot(['bus_id', 'flight_id', 'room_id', 'umrah_id'])
            ->withTimestamps();
    }
}
