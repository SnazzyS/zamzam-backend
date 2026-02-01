<?php

use App\Http\Controllers\BulkPaymentController;
use App\Http\Controllers\BusController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FlightController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\HotelManagementController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\PassengerController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\TripGroupController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Public Website Routes
Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/about', [PublicController::class, 'about'])->name('about');
Route::get('/card/{trip}/{customer}', [QrCodeController::class, 'show'])->name('card.show');

// Office Portal Routes (Admin)
Route::prefix('office')->group(function () {
    Route::get('/login', function () {
        return Inertia::render('Auth/Login');
    })->name('login');

    // Dashboard - no auth required for now
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // Customers
    Route::get('/customers', [PassengerController::class, 'index'])->name('customers.index');
    Route::put('/customers/{customer}', [PassengerController::class, 'update'])->name('customers.update');

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
    Route::get('trips/{trip}/customer-list', [TripController::class, 'customerList'])->name('trips.customer-list');
    Route::get('trips/{trip}/id-cards', [TripController::class, 'idCards'])->name('trips.id-cards');

    Route::prefix('trips/{trip}')->name('trips.')->group(function () {

        // Buses
        Route::resource('buses', BusController::class);
        Route::post('buses/{bus}/assign-customer', [BusController::class, 'assignCustomer'])->name('buses.assign-customer');
        Route::delete('buses/{bus}/remove-customer', [BusController::class, 'removeCustomer'])->name('buses.remove-customer');
        Route::get('buses/{bus}/passenger-list', [BusController::class, 'passengerList'])->name('buses.passenger-list');

        // Flights
        Route::resource('flights', FlightController::class);
        Route::post('flights/{flight}/assign-customer', [FlightController::class, 'assignCustomer'])->name('flights.assign-customer');
        Route::delete('flights/{flight}/remove-customer', [FlightController::class, 'removeCustomer'])->name('flights.remove-customer');
        Route::get('flights/{flight}/passenger-list', [FlightController::class, 'passengerList'])->name('flights.passenger-list');

        // Hotels
        Route::resource('hotels', HotelController::class);
        Route::post('hotels/{hotel}/attach', [HotelController::class, 'attach'])->name('hotels.attach');
        Route::delete('hotels/{hotel}/detach', [HotelController::class, 'detach'])->name('hotels.detach');
        Route::post('hotels/{hotel}/set-primary', [HotelController::class, 'setPrimary'])->name('hotels.set-primary');

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
            Route::resource('payments', PaymentController::class)->only(['index', 'store', 'show']);

            // Photos
            Route::resource('photos', PhotoController::class)->only(['store', 'destroy']);

            // Visa
            Route::post('visa', [CustomerController::class, 'uploadVisa'])->name('visa.upload');
            Route::delete('visa', [CustomerController::class, 'removeVisa'])->name('visa.remove');

            // Invoices / Discounts
            Route::post('invoice/add-discount', [InvoiceController::class, 'addDiscount'])->name('invoice.add-discount');
            Route::post('invoice/remove-discount', [InvoiceController::class, 'removeDiscount'])->name('invoice.remove-discount');
        });

        // Groups
        Route::get('groups', [TripGroupController::class, 'index'])->name('groups.index');
        Route::post('groups', [TripGroupController::class, 'store'])->name('groups.store');
        Route::put('groups/{group}', [TripGroupController::class, 'update'])->name('groups.update');
        Route::delete('groups/{group}', [TripGroupController::class, 'destroy'])->name('groups.destroy');

        // Bulk Payments
        Route::post('bulk-payments', [BulkPaymentController::class, 'store'])->name('bulk-payments.store');
        Route::get('bulk-payments/{batchId}', [BulkPaymentController::class, 'showBatch'])->name('bulk-payments.show');
    });
});
