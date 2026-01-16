<?php

use App\Http\Controllers\BusController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FlightController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\TripController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return Inertia::render('Auth/Login');
})->name('login');

// Dashboard - no auth required for now
Route::get('/dashboard', DashboardController::class)->name('dashboard');

// Trips
Route::resource('trips', TripController::class)->only(['show', 'store', 'update']);

Route::prefix('trips/{trip}')->name('trips.')->group(function () {

    // Buses
    Route::resource('buses', BusController::class);

    // Flights
    Route::resource('flights', FlightController::class);

    // Hotels
    Route::resource('hotels', HotelController::class);

    // Hotel Rooms (Nested under Hotel)
    Route::prefix('hotels/{hotel}')->name('hotels.')->group(function () {
        Route::resource('rooms', RoomController::class)->only(['index', 'store']);

        // Room Custom Actions
        Route::post('rooms/{room}/assign-customer', [RoomController::class, 'assignCustomer'])->name('rooms.assign-customer');
        Route::delete('rooms/{room}/remove-customer', [RoomController::class, 'removeCustomer'])->name('rooms.remove-customer');
        Route::get('customers-without-room', [RoomController::class, 'customersWithoutRoom'])->name('customers-without-room');
    });

    // Customers
    Route::resource('customers', CustomerController::class);

    // Customer Custom Actions (Bus Assignment)
    Route::post('customers/{customer}/assign-bus', [CustomerController::class, 'assignBus'])->name('customers.assign-bus');
    Route::delete('customers/{customer}/detach-bus', [CustomerController::class, 'detachBus'])->name('customers.detach-bus');

    Route::prefix('customers/{customer}')->name('customers.')->group(function () {
        // Payments
        Route::resource('payments', PaymentController::class)->only(['index', 'store']);

        // Photos
        Route::resource('photos', PhotoController::class)->only(['store', 'destroy']);

        // Invoices / Discounts
        Route::post('invoice/add-discount', [InvoiceController::class, 'addDiscount'])->name('invoice.add-discount');
        Route::post('invoice/remove-discount', [InvoiceController::class, 'removeDiscount'])->name('invoice.remove-discount');
    });
});
