<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $trips = Trip::withCount('customers')
            ->orderBy('departure_date', 'desc')
            ->get();

        $totalCustomers = $trips->sum('customers_count');
        $totalTrips = $trips->count();
        $activeTrips = $trips->where('status', 'active')->count();

        $totalRevenue = $trips->sum(function ($trip) {
            return $trip->price * $trip->customers_count;
        });

        return Inertia::render('Dashboard', [
            'trips' => $trips,
            'stats' => [
                'totalTrips' => $totalTrips,
                'totalCustomers' => $totalCustomers,
                'activeTrips' => $activeTrips,
                'totalRevenue' => $totalRevenue,
            ],
        ]);
    }
}
