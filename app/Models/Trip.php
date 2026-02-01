<?php

namespace App\Models;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'price', 'departure_date', 'phone_number', 'hotel_address', 'trip_code'
    ];

    public function customers()
    {
        return $this->belongsToMany(Customer::class)
            ->withPivot(['bus_id', 'flight_id', 'umrah_id', 'group_id', 'customer_type', 'visa_path'])
            ->withTimestamps();
    }

    public function groups()
    {
        return $this->hasMany(TripGroup::class);
    }

    public function buses()
    {
        return $this->hasMany(Bus::class);
    }

    public function flights()
    {
        return $this->hasMany(Flight::class);
    }

    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'room_trip', 'trip_id', 'room_id')
        ->withTimestamps();
    }

    public function invoices()
    {
        return $this->morphMany(Invoice::class, 'invoiceable');
    }

    public function hotels()
    {
        return $this->belongsToMany(Hotel::class)
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    public function primaryHotel()
    {
        return $this->hotels()->wherePivot('is_primary', true)->first();
    }

    public function activityTrips()
    {
        return $this->hasMany(ActivityTrip::class);
    }

    public function activities()
    {
        return $this->belongsToMany(Activity::class, 'activity_trips')
            ->withPivot('id', 'price_usd', 'price_mvr', 'price_sar', 'date', 'state')
            ->withTimestamps();
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function getTotalExpensesAttribute()
    {
        return $this->expenses()->sum('amount');
    }

    public function getTotalRevenueAttribute()
    {
        // Revenue from customer invoices/payments
        $invoiceIds = Invoice::where('trip_id', $this->id)->pluck('id');
        $invoiceRevenue = \App\Models\Payment::whereIn('invoice_id', $invoiceIds)->sum('amount');

        // Revenue from activities
        $activityRevenue = \App\Models\CustomerActivity::whereHas('activityTrip', function ($q) {
            $q->where('trip_id', $this->id);
        })->whereNotNull('paid_at')->sum('amount_paid');

        return $invoiceRevenue + $activityRevenue;
    }

    public function getNetProfitAttribute()
    {
        return $this->total_revenue - $this->total_expenses;
    }
}
