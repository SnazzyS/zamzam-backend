<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerActivity extends Model
{
    protected $table = 'customer_activities';

    protected $fillable = [
        'customer_id',
        'activity_trip_id',
        'currency',
        'amount_paid',
        'payment_method',
        'receipt_number',
        'paid_at',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function activityTrip(): BelongsTo
    {
        return $this->belongsTo(ActivityTrip::class);
    }

    /**
     * Check if this customer activity has been paid
     */
    public function isPaid(): bool
    {
        return !is_null($this->paid_at);
    }
}
