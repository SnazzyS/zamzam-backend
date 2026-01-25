<?php

use App\Http\Controllers\BusController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FlightController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\HotelManagementController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\TripGroupController;
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

// Hotel Management
Route::get('/hotels', [HotelManagementController::class, 'index'])->name('hotels.index');
Route::post('/hotels', [HotelManagementController::class, 'store'])->name('hotels.store');
Route::put('/hotels/{hotel}', [HotelManagementController::class, 'update'])->name('hotels.update');
Route::delete('/hotels/{hotel}', [HotelManagementController::class, 'destroy'])->name('hotels.destroy');
Route::post('/hotels/{hotel}/rooms', [HotelManagementController::class, 'storeRoom'])->name('hotels.rooms.store');
Route::put('/hotels/{hotel}/rooms/{room}', [HotelManagementController::class, 'updateRoom'])->name('hotels.rooms.update');
Route::delete('/hotels/{hotel}/rooms/{room}', [HotelManagementController::class, 'destroyRoom'])->name('hotels.rooms.destroy');

// Trips
Route::resource('trips', TripController::class)->only(['show', 'store', 'update']);

Route::prefix('trips/{trip}')->name('trips.')->group(function () {

    // Buses
    Route::resource('buses', BusController::class);
    Route::post('buses/{bus}/assign-customer', [BusController::class, 'assignCustomer'])->name('buses.assign-customer');
    Route::delete('buses/{bus}/remove-customer', [BusController::class, 'removeCustomer'])->name('buses.remove-customer');

    // Flights
    Route::resource('flights', FlightController::class);
    Route::post('flights/{flight}/assign-customer', [FlightController::class, 'assignCustomer'])->name('flights.assign-customer');
    Route::delete('flights/{flight}/remove-customer', [FlightController::class, 'removeCustomer'])->name('flights.remove-customer');

    // Hotels
    Route::resource('hotels', HotelController::class);
    Route::post('hotels/{hotel}/attach', [HotelController::class, 'attach'])->name('hotels.attach');
    Route::delete('hotels/{hotel}/detach', [HotelController::class, 'detach'])->name('hotels.detach');

    // Hotel Rooms (Nested under Hotel)
    Route::prefix('hotels/{hotel}')->name('hotels.')->group(function () {
        Route::resource('rooms', RoomController::class)->only(['index', 'store']);

        // Room Custom Actions
        Route::post('rooms/{room}/assign-customer', [RoomController::class, 'assignCustomer'])->name('rooms.assign-customer');
        Route::delete('rooms/{room}/remove-customer', [RoomController::class, 'removeCustomer'])->name('rooms.remove-customer');
        Route::get('customers-without-room', [RoomController::class, 'customersWithoutRoom'])->name('customers-without-room');
    });

    // Customers
    Route::post('customers/search', [CustomerController::class, 'searchByNationalId'])->name('customers.search');
    Route::resource('customers', CustomerController::class);

    // Customer Custom Actions (Bus Assignment)
    Route::post('customers/{customer}/assign-bus', [CustomerController::class, 'assignBus'])->name('customers.assign-bus');
    Route::delete('customers/{customer}/detach-bus', [CustomerController::class, 'detachBus'])->name('customers.detach-bus');
    Route::put('customers/{customer}/assign-group', [CustomerController::class, 'assignGroup'])->name('customers.assign-group');

    Route::prefix('customers/{customer}')->name('customers.')->group(function () {
        // Payments
        Route::resource('payments', PaymentController::class)->only(['index', 'store']);

        // Photos
        Route::resource('photos', PhotoController::class)->only(['store', 'destroy']);

        // Invoices / Discounts
        Route::post('invoice/add-discount', [InvoiceController::class, 'addDiscount'])->name('invoice.add-discount');
        Route::post('invoice/remove-discount', [InvoiceController::class, 'removeDiscount'])->name('invoice.remove-discount');
    });

    // Groups
    Route::get('groups', [TripGroupController::class, 'index'])->name('groups.index');
    Route::post('groups', [TripGroupController::class, 'store'])->name('groups.store');
    Route::put('groups/{group}', [TripGroupController::class, 'update'])->name('groups.update');
    Route::delete('groups/{group}', [TripGroupController::class, 'destroy'])->name('groups.destroy');
});
