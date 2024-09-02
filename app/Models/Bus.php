<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bus extends Model
{
    use HasFactory;

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function customers()
    {
        return $this->hasMany(Trip::class);
    }

    
}
