<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'file_name',
        'mime_type',
        'path'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
