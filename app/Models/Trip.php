<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'price', 'departure_date', 'phone_number', 'hotel_address'
    ];

    public function customers()
    {
        return $this->hasMany(Customer::class);
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
