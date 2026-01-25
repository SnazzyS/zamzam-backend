<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use App\Models\CustomerTrip;
use App\Models\Trip;
use App\Http\Requests\BusStoreRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BusController extends Controller
{
    public function index(Trip $trip)
    {
        $buses = $trip->buses()
            ->withCount(['customerTrips as customers_count'])
            ->orderBy('bus_number')
            ->get();

        // Get customers in this trip who are not assigned to any bus
        $customersWithoutBus = CustomerTrip::where('trip_id', $trip->id)
            ->whereNull('bus_id')
            ->with('customer:id,name,national_id')
            ->get()
            ->map(fn($ct) => [
                'id' => $ct->customer->id,
                'name' => $ct->customer->name,
                'national_id' => $ct->customer->national_id,
                'customer_trip_id' => $ct->id,
            ]);

        // Get all customers in trip for passenger list
        $allCustomers = CustomerTrip::where('trip_id', $trip->id)
            ->with('customer:id,name,national_id')
            ->get()
            ->map(fn($ct) => [
                'id' => $ct->customer->id,
                'name' => $ct->customer->name,
                'national_id' => $ct->customer->national_id,
                'customer_trip_id' => $ct->id,
                'bus_id' => $ct->bus_id,
                'umrah_id' => $ct->umrah_id,
            ]);

        return Inertia::render('Trips/Buses/Index', [
            'trip' => $trip,
            'buses' => $buses,
            'customersWithoutBus' => $customersWithoutBus,
            'allCustomers' => $allCustomers,
        ]);
    }

    public function store(Trip $trip, BusStoreRequest $request)
    {
        $trip->buses()->create([
            'name' => $request->name,
            'capacity' => $request->capacity,
            'bus_number' => $request->bus_number,
            'color_code' => $request->color_code,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Bus created successfully');
    }

    public function show(Trip $trip, Bus $bus)
    {
        $passengers = CustomerTrip::where('trip_id', $trip->id)
            ->where('bus_id', $bus->id)
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

        return Inertia::render('Trips/Buses/Show', [
            'trip' => $trip,
            'bus' => $bus,
            'passengers' => $passengers,
        ]);
    }

    public function update(Trip $trip, Bus $bus, BusStoreRequest $request)
    {
        $bus->update([
            'name' => $request->name,
            'capacity' => $request->capacity,
            'bus_number' => $request->bus_number,
            'color_code' => $request->color_code,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Bus updated successfully');
    }

    public function destroy(Trip $trip, Bus $bus)
    {
        // Remove all customer assignments first
        CustomerTrip::where('bus_id', $bus->id)->update(['bus_id' => null]);

        $bus->delete();

        return redirect()
            ->back()
            ->with('success', 'Bus deleted successfully');
    }

    /**
     * Assign a customer to a bus
     */
    public function assignCustomer(Trip $trip, Bus $bus, Request $request)
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

        $customerTrip->update(['bus_id' => $bus->id]);

        return redirect()
            ->back()
            ->with('success', 'Customer assigned to bus');
    }

    /**
     * Remove a customer from a bus
     */
    public function removeCustomer(Trip $trip, Bus $bus, Request $request)
    {
        $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
        ]);

        CustomerTrip::where('trip_id', $trip->id)
            ->where('customer_id', $request->customer_id)
            ->where('bus_id', $bus->id)
            ->update(['bus_id' => null]);

        return redirect()
            ->back()
            ->with('success', 'Customer removed from bus');
    }
}
