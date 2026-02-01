<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityTrip extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'trip_id',
        'price_usd',
        'price_mvr',
        'price_sar',
        'date',
        'state',
    ];

    protected $casts = [
        'date' => 'date',
        'price_usd' => 'decimal:2',
        'price_mvr' => 'decimal:2',
        'price_sar' => 'decimal:2',
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'customer_activities')
            ->withPivot('currency', 'amount_paid', 'payment_method', 'receipt_number', 'paid_at')
            ->withTimestamps();
    }

    public function customerActivities()
    {
        return $this->hasMany(CustomerActivity::class);
    }

    /**
     * Get price for a specific currency
     */
    public function getPriceForCurrency(string $currency): ?float
    {
        return match (strtoupper($currency)) {
            'USD' => $this->price_usd,
            'MVR' => $this->price_mvr,
            'SAR' => $this->price_sar,
            default => null,
        };
    }
}
