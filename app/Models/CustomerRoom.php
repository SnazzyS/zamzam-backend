<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CustomerRoom extends Pivot
{
    protected $table = 'customer_room';

    public $timestamps = true;

    protected $fillable = [
        'customer_id',
        'room_id',
        'hotel_id'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

}
