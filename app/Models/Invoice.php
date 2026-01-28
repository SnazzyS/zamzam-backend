<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'trip_id',
        'amount',
        'balance',
        'discount',
        'invoice_number',
        'grand_total',
        'status',
        'invoiceable_id',
        'invoiceable_type'
    ];

 
    

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getTotalPaidAttribute()
    {
        return $this->payments()->sum('amount');
    }

    public function getBalanceRemainingAttribute()
    {
        return $this->grand_total - $this->discount - $this->total_paid;
    }

    public function invoiceable()
    {
        return $this->morphTo();
    }
}
