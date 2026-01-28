<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CustomerTrip;
use App\Models\Flight;
use App\Models\Trip;
use App\Http\Requests\FlightStoreRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FlightController extends Controller
{
    public function index(Trip $trip)
    {
        $flights = $trip->flights()
            ->withCount(['customerTrips as customers_count'])
            ->orderBy('departure_date')
            ->get();

        // Get customers in this trip who are not assigned to any flight
        $customersWithoutFlight = CustomerTrip::where('trip_id', $trip->id)
            ->whereNull('flight_id')
            ->with('customer:id,name,national_id')
            ->get()
            ->map(fn($ct) => [
                'id' => $ct->customer->id,
                'name' => $ct->customer->name,
                'national_id' => $ct->customer->national_id,
                'customer_trip_id' => $ct->id,
            ]);

        // Get all customers in trip for assignment modal and passenger list
        $allCustomers = CustomerTrip::where('trip_id', $trip->id)
            ->with('customer:id,name,national_id')
            ->get()
            ->map(fn($ct) => [
                'id' => $ct->customer->id,
                'name' => $ct->customer->name,
                'national_id' => $ct->customer->national_id,
                'customer_trip_id' => $ct->id,
                'flight_id' => $ct->flight_id,
                'umrah_id' => $ct->umrah_id,
            ]);

        return Inertia::render('Trips/Flights/Index', [
            'trip' => $trip,
            'flights' => $flights,
            'customersWithoutFlight' => $customersWithoutFlight,
            'allCustomers' => $allCustomers,
        ]);
    }

    public function store(Trip $trip, FlightStoreRequest $request)
    {
        $trip->flights()->create([
            'name' => $request->name,
            'departure_date' => $request->departure_date,
            'departure_time' => $request->departure_time,
            'return_date' => $request->return_date,
            'return_time' => $request->return_time,
            'departure_flight_number' => $request->departure_flight_number,
            'departure_transit_flight_number' => $request->departure_transit_flight_number,
            'return_flight_number' => $request->return_flight_number,
            'return_transit_flight_number' => $request->return_transit_flight_number,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Flight created successfully');
    }

    public function show(Trip $trip, Flight $flight)
    {
        $passengers = CustomerTrip::where('trip_id', $trip->id)
            ->where('flight_id', $flight->id)
            ->with('customer:id,name,name_in_english,national_id,passport_number,phone_number,date_of_birth')
            ->get()
            ->map(fn($ct) => [
                'id' => $ct->customer->id,
                'customer_trip_id' => $ct->id,
                'umrah_id' => $ct->umrah_id,
                'name' => $ct->customer->name,
                'name_in_english' => $ct->customer->name_in_english,
                'national_id' => $ct->customer->national_id,
                'passport_number' => $ct->customer->passport_number,
                'phone_number' => $ct->customer->phone_number,
                'date_of_birth' => $ct->customer->date_of_birth,
            ]);

        return Inertia::render('Trips/Flights/Show', [
            'trip' => $trip,
            'flight' => $flight,
            'passengers' => $passengers,
        ]);
    }

    public function update(Trip $trip, Flight $flight, FlightStoreRequest $request)
    {
        $flight->update([
            'name' => $request->name,
            'departure_date' => $request->departure_date,
            'departure_time' => $request->departure_time,
            'return_date' => $request->return_date,
            'return_time' => $request->return_time,
            'departure_flight_number' => $request->departure_flight_number,
            'departure_transit_flight_number' => $request->departure_transit_flight_number,
            'return_flight_number' => $request->return_flight_number,
            'return_transit_flight_number' => $request->return_transit_flight_number,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Flight updated successfully');
    }

    public function destroy(Trip $trip, Flight $flight)
    {
        // Remove all customer assignments first
        CustomerTrip::where('flight_id', $flight->id)->update(['flight_id' => null]);

        $flight->delete();

        return redirect()
            ->back()
            ->with('success', 'Flight deleted successfully');
    }

    /**
     * Assign a customer to a flight
     */
    public function assignCustomer(Trip $trip, Flight $flight, Request $request)
    {
        $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
        ]);

        $customerTrip = CustomerTrip::where('trip_id', $trip->id)
            ->where('customer_id', $request->customer_id)
            ->first();

        if (!$customerTrip) {
            return redirect()
                ->back()
                ->withErrors(['customer_id' => 'Customer is not part of this trip']);
        }

        $customerTrip->update(['flight_id' => $flight->id]);

        return redirect()
            ->back()
            ->with('success', 'Customer assigned to flight');
    }

    /**
     * Remove a customer from a flight
     */
    public function removeCustomer(Trip $trip, Flight $flight, Request $request)
    {
        $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
        ]);

        CustomerTrip::where('trip_id', $trip->id)
            ->where('customer_id', $request->customer_id)
            ->where('flight_id', $flight->id)
            ->update(['flight_id' => null]);

        return redirect()
            ->back()
            ->with('success', 'Customer removed from flight');
    }

    /**
     * Printable passenger list for a flight with customizable columns
     */
    public function passengerList(Trip $trip, Flight $flight)
    {
        $passengers = CustomerTrip::where('trip_id', $trip->id)
            ->where('flight_id', $flight->id)
            ->with('customer:id,name,name_in_english,national_id,passport_number,phone_number,date_of_birth,island,gender')
            ->orderBy('umrah_id')
            ->get()
            ->map(fn($ct) => [
                'id' => $ct->customer->id,
                'umrah_id' => $ct->umrah_id,
                'name' => $ct->customer->name,
                'name_in_english' => $ct->customer->name_in_english,
                'national_id' => $ct->customer->national_id,
                'passport_number' => $ct->customer->passport_number,
                'phone_number' => $ct->customer->phone_number,
                'date_of_birth' => $ct->customer->date_of_birth,
                'island' => $ct->customer->island,
                'gender' => $ct->customer->gender,
            ]);

        return Inertia::render('Trips/Flights/PassengerList', [
            'trip' => $trip,
            'flight' => $flight,
            'passengers' => $passengers,
        ]);
    }
}
