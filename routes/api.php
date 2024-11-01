<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Bus\BusShowController;
use App\Http\Controllers\Bus\BusIndexController;
use App\Http\Controllers\Trip\TripShowController;
use App\Http\Controllers\Trip\TripStoreController;
use App\Http\Controllers\Trip\TripUpdateController;
use App\Http\Controllers\Customer\CustomerShowController;
use App\Http\Controllers\Customer\CustomerStoreController;
use App\Http\Controllers\Customer\CustomerUpdateController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/dashboard', DashboardController::class);

Route::post('/trips', TripStoreController::class);
Route::get('/trips/{trip:id}', TripShowController::class);
Route::put('/trips/{trip:id}', TripUpdateController::class);

Route::post('trips/{trip:id}/customer', CustomerStoreController::class);
// Route::get('trips/{trip:id}/{customer:id}', CustomerShowController::class);
Route::put('trips/{trip:id}/customer/{customer:id}', CustomerUpdateController::class);


// bus, have ability to attach users to bus here?
Route::get('trips/{trip:id}/bus', BusIndexController::class);

// bus show should show the bus details and the customers in the bus in printable format
Route::get('trips/{trip:id}/bus/{bus:id}', BusShowController::class);
