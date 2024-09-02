<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerPhoto extends Model
{
    use HasFactory;

    protected $fillable = ['filename', 'mime_type', 'photo_url'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
