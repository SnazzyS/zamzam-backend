<?php

namespace App\Models;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_number',
        'name',
        'bed_count',
        'private',
        'price',
        'currency',
        'hotel_id',
        'trip_id'
        
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

  
    public function trip()
    {
        return $this->belongsToMany(Trip::class, 'room_trip', 'room_id', 'trip_id')
        ->withTimestamps();
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function invoices()
    {
        return $this->morphMany(Invoice::class, 'invoiceable');
    }
}
