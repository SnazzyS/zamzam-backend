<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }
}
