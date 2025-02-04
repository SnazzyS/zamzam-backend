<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'amount',
        'payment_method',
        'transfer_reference_number',
        'details',
    ];


    // public function customer()
    // {
    //     return $this->belongsTo(Customer::class);
    // }

    // public function trip()
    // {
    //     return $this->belongsTo(Trip::class);
    // }

    public function invoice()
    {
        return $this->hasMany(Invoice::class);
    }

}
