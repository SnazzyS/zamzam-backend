<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Bus\CreateBusController;
use App\Http\Controllers\Bus\DeleteBusController;
use App\Http\Controllers\Bus\ListBusesController;
use App\Http\Controllers\Bus\UpdateBusController;
use App\Http\Controllers\Room\ListRoomsController;
use App\Http\Controllers\Room\CreateRoomController;
use App\Http\Controllers\Trip\CreateTripController;
use App\Http\Controllers\Trip\UpdateTripController;
use App\Http\Controllers\Hotel\ListHotelsController;
use App\Http\Controllers\Trip\ActiveTripsController;
use App\Http\Controllers\Hotel\CreateHotelController;
use App\Http\Controllers\Hotel\DeleteHotelController;
use App\Http\Controllers\Hotel\UpdateHotelController;
use App\Http\Controllers\Photo\DeletePhotoController;
use App\Http\Controllers\Photo\UploadPhotoController;
use App\Http\Controllers\Bus\ViewBusDetailsController;
use App\Http\Controllers\Flight\ListFlightsController;
use App\Http\Controllers\Flight\CreateFlightController;
use App\Http\Controllers\Flight\DeleteFlightController;
use App\Http\Controllers\Flight\UpdateFlightController;
use App\Http\Controllers\Invoice\AddDiscountController;
use App\Http\Controllers\Payment\CreatePaymentController;
use App\Http\Controllers\Customer\CreateCustomerController;
use App\Http\Controllers\Customer\UpdateCustomerController;
use App\Http\Controllers\Flight\ViewFlightDetailsController;
use App\Http\Controllers\Room\AssignCustomerToRoomController;
use App\Http\Controllers\Customer\AssignCustomerToBusController;
use App\Http\Controllers\Customer\ViewCustomerDetailsController;
use App\Http\Controllers\Customer\RemoveCustomerFromBusController;
use App\Http\Controllers\Customer\RemoveCustomerFromTripController;
use App\Http\Controllers\Room\RemoveCustomerFromRoomController;
use App\Http\Controllers\Room\ShowCustomersWithoutRoomController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/dashboard', DashboardController::class);

//trips
Route::get('/trips/{trip:id}', ActiveTripsController::class);
Route::post('/trips', CreateTripController::class);
Route::put('/trips/{trip}', UpdateTripController::class);

// customers
Route::post('/trips/{trip}/customer/{customer}/payment', CreatePaymentController::class);

// giving discount to customer
Route::post('/trips/{trip}/customer/{customer}/invoice/add-discount', AddDiscountController::class);

// creating customer
Route::post('trips/{trip}/customer', CreateCustomerController::class);
Route::get('trips/{trip:id}/customer/{customer:id}', ViewCustomerDetailsController::class);
Route::put('trips/{trip:id}/customer/{customer:id}', UpdateCustomerController::class);
Route::delete('trips/{trip}/customer/{customer}', RemoveCustomerFromTripController::class);

// upload photo
Route::post('trips/{trip}/customer/{customer}/photo', UploadPhotoController::class);
Route::delete('trips/{trip}/customer/{customer}/photo/{photo}', DeletePhotoController::class);

// hotel
Route::get('trips/{trip}/hotel', ListHotelsController::class);
Route::post('trips/{trip}/hotel', CreateHotelController::class);
Route::put('trips/{trip}/hotel/{hotel}', UpdateHotelController::class);
Route::delete('trips/{trip}/hotel/{hotel}', DeleteHotelController::class);

// room
Route::get('trips/{trip}/hotel/{hotel}/rooms', ListRoomsController::class);
Route::post('trips/{trip}/hotel/{hotel}/rooms/create', CreateRoomController::class);
Route::post('trips/{trip}/hotel/{hotel}/rooms/{room}/assign-customer', AssignCustomerToRoomController::class);
Route::get('trips/{trip}/hotel/{hotel}/customers-without-room', ShowCustomersWithoutRoomController::class);
Route::delete('trips/{trip}/hotel/{hotel}/rooms/{room}/remove-customer', RemoveCustomerFromRoomController::class);

// attach customer to bus
Route::post('/trips/{trip:id}/customer/{customer:id}/bus/{bus:id}', AssignCustomerToBusController::class);
Route::delete('/trips/{trip:id}/customer/{customer:id}/bus/{bus:id}', RemoveCustomerFromBusController::class);

Route::get('trips/{trip:id}/bus', ListBusesController::class);
Route::get('trips/{trip:id}/bus/{bus:id}', ViewBusDetailsController::class);
Route::post('trips/{trip:id}/bus', CreateBusController::class);
Route::put('trips/{trip:id}/bus/{bus:id}', UpdateBusController::class);
Route::delete('trips/{trip:id}/bus/{bus:id}', DeleteBusController::class);

// flight
Route::get('trips/{trip:id}/flight', ListFlightsController::class);
Route::get('trips/{trip:id}/flight/{flight:id}', ViewFlightDetailsController::class)->scopeBindings(false);
Route::post('trips/{trip:id}/flight', CreateFlightController::class);
Route::put('trips/{trip:id}/flight/{flight:id}', UpdateFlightController::class);
Route::delete('trips/{trip:id}/flight/{flight:id}', DeleteFlightController::class);
