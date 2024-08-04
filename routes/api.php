<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Trip\TripShowController;
use App\Http\Controllers\Trip\TripStoreController;
use App\Http\Controllers\Trip\TripUpdateController;
use App\Http\Controllers\Customer\CustomerShowController;
use App\Http\Controllers\Customer\CustomerStoreController;
use App\Http\Controllers\Customer\CustomerUpdateController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');




Route::post('/trips', TripStoreController::class);
Route::put('/trips/{trip:id}', TripUpdateController::class);
Route::get('/trips/{trip:id}', TripShowController::class);

Route::post('/customers', CustomerStoreController::class);
Route::put('/customers/{customer:id}', CustomerUpdateController::class);
Route::get('/customers/{customer:id}', CustomerShowController::class);
