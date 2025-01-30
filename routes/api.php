<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Bus\BusShowController;
use App\Http\Controllers\Bus\BusIndexController;
use App\Http\Controllers\Bus\BusStoreController;
use App\Http\Controllers\Bus\BusUpdateController;
use App\Http\Controllers\Trip\TripShowController;
use App\Http\Controllers\Bus\BusDestroyController;
use App\Http\Controllers\Trip\TripStoreController;
use App\Http\Controllers\Trip\TripUpdateController;
use App\Http\Controllers\Flight\FlightShowController;
use App\Http\Controllers\Flight\FlightIndexController;
use App\Http\Controllers\Flight\FlightStoreController;
use App\Http\Controllers\Flight\FlightUpdateController;
use App\Http\Controllers\Flight\FlightDestroyController;
use App\Http\Controllers\Customer\CustomerShowController;
use App\Http\Controllers\Customer\CustomerStoreController;
use App\Http\Controllers\Customer\CustomerUpdateController;
use App\Http\Controllers\Customer\CustomerBusAttachController;
use App\Http\Controllers\Customer\CustomerBusDetachController;
use App\Http\Controllers\Customer\CustomerDetachFromTripController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/dashboard', DashboardController::class);

//trips
Route::get('/trips/{trip:id}', TripShowController::class);
Route::post('/trips', TripStoreController::class);
Route::put('/trips/{trip:id}', TripUpdateController::class);

// customers
Route::post('trips/{trip}/customer', CustomerStoreController::class);
Route::get('trips/{trip:id}/customer/{customer:id}', CustomerShowController::class);
Route::put('trips/{trip:id}/customer/{customer:id}', CustomerUpdateController::class);
Route::delete('trips/{trip}/customer/{customer}', CustomerDetachFromTripController::class);

// attach customer to bus
Route::post('/trips/{trip:id}/customer/{customer:id}/bus/{bus:id}', CustomerBusAttachController::class);
Route::delete('/trips/{trip:id}/customer/{customer:id}/bus/{bus:id}', CustomerBusDetachController::class);

Route::get('trips/{trip:id}/bus', BusIndexController::class);
Route::get('trips/{trip:id}/bus/{bus:id}', BusShowController::class);
Route::post('trips/{trip:id}/bus', BusStoreController::class);
Route::put('trips/{trip:id}/bus/{bus:id}', BusUpdateController::class);
Route::delete('trips/{trip:id}/bus/{bus:id}', BusDestroyController::class);

// flight
Route::get('trips/{trip:id}/flight', FlightIndexController::class);
Route::get('trips/{trip:id}/flight/{flight:id}', FlightShowController::class)->scopeBindings(false);
Route::post('trips/{trip:id}/flight', FlightStoreController::class);
Route::put('trips/{trip:id}/flight/{flight:id}', FlightUpdateController::class);
Route::delete('trips/{trip:id}/flight/{flight:id}', FlightDestroyController::class);
