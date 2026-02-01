<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'discription',
    ];

    public function activityTrips()
    {
        return $this->hasMany(ActivityTrip::class);
    }

    public function trips()
    {
        return $this->belongsToMany(Trip::class, 'activity_trips')
            ->withPivot('price_usd', 'price_mvr', 'price_sar', 'date', 'state')
            ->withTimestamps();
    }
}
