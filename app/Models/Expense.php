<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'trip_id',
        'title',
        'description',
        'amount',
        'currency',
        'category',
        'expense_date',
        'fiscal_year',
        'payment_method',
        'transfer_reference_number',
        'cheque_number',
        'document_path',
        'document_filename',
        'document_mime_type',
        'vendor_name',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
        'fiscal_year' => 'integer',
    ];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    // Scope for trip-specific expenses
    public function scopeForTrip($query, $tripId)
    {
        return $query->where('trip_id', $tripId);
    }

    // Scope for general expenses (not tied to trips)
    public function scopeGeneral($query)
    {
        return $query->whereNull('trip_id');
    }

    // Scope for fiscal year
    public function scopeForYear($query, $year)
    {
        return $query->where('fiscal_year', $year);
    }

    // Check if expense has an uploaded document
    public function hasDocument(): bool
    {
        return !empty($this->document_path);
    }

    // Get document URL
    public function getDocumentUrlAttribute(): ?string
    {
        return $this->document_path
            ? asset('storage/' . $this->document_path)
            : null;
    }
}
