<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return response()->json(Trip::withCount('customers')->get());
        
    }
}
