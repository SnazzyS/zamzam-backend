<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone_number',
    
    ];


    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function trips()
    {
        return $this->belongsToMany(Trip::class)
            ->withTimestamps();
    }
}
