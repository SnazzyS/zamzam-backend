<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityTrip;
use App\Models\Customer;
use App\Models\CustomerActivity;
use App\Models\CustomerTrip;
use App\Models\Trip;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ActivityController extends Controller
{
    /**
     * Display list of activities for a trip
     */
    public function index(Trip $trip)
    {
        $activityTrips = $trip->activityTrips()
            ->with(['activity', 'customerActivities.customer'])
            ->get()
            ->map(function ($activityTrip) {
                return [
                    'id' => $activityTrip->id,
                    'name' => $activityTrip->activity->name,
                    'activity_id' => $activityTrip->activity_id,
                    'price_usd' => $activityTrip->price_usd,
                    'price_mvr' => $activityTrip->price_mvr,
                    'price_sar' => $activityTrip->price_sar,
                    'date' => $activityTrip->date,
                    'state' => $activityTrip->state,
                    'customer_count' => $activityTrip->customerActivities->count(),
                    'paid_count' => $activityTrip->customerActivities->filter(fn($ca) => $ca->paid_at)->count(),
                ];
            });

        // Get all existing activities for dropdown
        $activities = Activity::all(['id', 'name']);

        return Inertia::render('Trips/Activities/Index', [
            'trip' => $trip,
            'activityTrips' => $activityTrips,
            'activities' => $activities,
        ]);
    }

    /**
     * Store a new activity for a trip
     */
    public function store(Trip $trip, Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price_usd' => 'nullable|numeric|min:0',
            'price_mvr' => 'nullable|numeric|min:0',
            'price_sar' => 'nullable|numeric|min:0',
            'date' => 'nullable|date',
        ]);

        // Create or find the activity
        $activity = Activity::firstOrCreate(
            ['name' => $data['name']],
            ['discription' => '']
        );

        // Create the activity trip
        ActivityTrip::create([
            'activity_id' => $activity->id,
            'trip_id' => $trip->id,
            'price_usd' => $data['price_usd'] ?? null,
            'price_mvr' => $data['price_mvr'] ?? null,
            'price_sar' => $data['price_sar'] ?? null,
            'date' => $data['date'] ?? null,
            'state' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Activity added successfully');
    }

    /**
     * Show activity details with customers
     */
    public function show(Trip $trip, ActivityTrip $activity)
    {
        $activity->load(['activity', 'customerActivities.customer']);

        $customers = $activity->customerActivities->map(function ($ca) use ($trip) {
            // Get umrah_id from customer_trip
            $customerTrip = CustomerTrip::where('trip_id', $trip->id)
                ->where('customer_id', $ca->customer_id)
                ->first();

            return [
                'id' => $ca->id,
                'customer_id' => $ca->customer->id,
                'name' => $ca->customer->name,
                'name_in_english' => $ca->customer->name_in_english,
                'umrah_id' => $customerTrip?->umrah_id,
                'currency' => $ca->currency,
                'amount_paid' => $ca->amount_paid,
                'payment_method' => $ca->payment_method,
                'receipt_number' => $ca->receipt_number,
                'paid_at' => $ca->paid_at,
                'is_paid' => !is_null($ca->paid_at),
            ];
        });

        // Get trip customers not in this activity for assignment dropdown
        $tripCustomerIds = $activity->customerActivities->pluck('customer_id')->toArray();
        $availableCustomers = $trip->customers()
            ->whereNotIn('customers.id', $tripCustomerIds)
            ->get()
            ->map(function ($customer) {
                return [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'umrah_id' => $customer->pivot->umrah_id,
                ];
            });

        return Inertia::render('Trips/Activities/Show', [
            'trip' => $trip,
            'activityTrip' => [
                'id' => $activity->id,
                'name' => $activity->activity->name,
                'price_usd' => $activity->price_usd,
                'price_mvr' => $activity->price_mvr,
                'price_sar' => $activity->price_sar,
                'date' => $activity->date,
                'state' => $activity->state,
            ],
            'customers' => $customers,
            'availableCustomers' => $availableCustomers,
        ]);
    }

    /**
     * Update an activity
     */
    public function update(Trip $trip, ActivityTrip $activity, Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price_usd' => 'nullable|numeric|min:0',
            'price_mvr' => 'nullable|numeric|min:0',
            'price_sar' => 'nullable|numeric|min:0',
            'date' => 'nullable|date',
            'state' => 'nullable|in:pending,completed,cancelled',
        ]);

        // Update or create activity name
        $existingActivity = Activity::firstOrCreate(
            ['name' => $data['name']],
            ['discription' => '']
        );

        $activity->update([
            'activity_id' => $existingActivity->id,
            'price_usd' => $data['price_usd'] ?? null,
            'price_mvr' => $data['price_mvr'] ?? null,
            'price_sar' => $data['price_sar'] ?? null,
            'date' => $data['date'] ?? null,
            'state' => $data['state'] ?? $activity->state,
        ]);

        return redirect()->back()->with('success', 'Activity updated successfully');
    }

    /**
     * Delete an activity from trip
     */
    public function destroy(Trip $trip, ActivityTrip $activity)
    {
        $activity->delete();

        return redirect()->route('trips.activities.index', $trip->id)
            ->with('success', 'Activity removed successfully');
    }

    /**
     * Assign a customer to an activity
     */
    public function assignCustomer(Trip $trip, ActivityTrip $activity, Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
        ]);

        // Check if customer is in the trip
        $isInTrip = $trip->customers()->where('customers.id', $data['customer_id'])->exists();
        if (!$isInTrip) {
            return redirect()->back()->withErrors(['customer_id' => 'Customer is not in this trip']);
        }

        // Check if already assigned
        $exists = CustomerActivity::where('activity_trip_id', $activity->id)
            ->where('customer_id', $data['customer_id'])
            ->exists();

        if ($exists) {
            return redirect()->back()->withErrors(['customer_id' => 'Customer already assigned to this activity']);
        }

        CustomerActivity::create([
            'activity_trip_id' => $activity->id,
            'customer_id' => $data['customer_id'],
        ]);

        return redirect()->back()->with('success', 'Customer assigned successfully');
    }

    /**
     * Remove a customer from an activity
     */
    public function removeCustomer(Trip $trip, ActivityTrip $activity, Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
        ]);

        CustomerActivity::where('activity_trip_id', $activity->id)
            ->where('customer_id', $data['customer_id'])
            ->delete();

        return redirect()->back()->with('success', 'Customer removed from activity');
    }

    /**
     * Accept payment for a customer's activity
     */
    public function acceptPayment(Trip $trip, ActivityTrip $activity, Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'currency' => 'required|in:USD,MVR,SAR',
            'payment_method' => 'required|in:cash,transfer',
        ]);

        $customerActivity = CustomerActivity::where('activity_trip_id', $activity->id)
            ->where('customer_id', $data['customer_id'])
            ->first();

        if (!$customerActivity) {
            return redirect()->back()->withErrors(['customer_id' => 'Customer not found in this activity']);
        }

        if ($customerActivity->paid_at) {
            return redirect()->back()->withErrors(['customer_id' => 'Payment already recorded']);
        }

        // Get the price for selected currency
        $amount = $activity->getPriceForCurrency($data['currency']);
        if (!$amount) {
            return redirect()->back()->withErrors(['currency' => 'Price not set for this currency']);
        }

        // Generate receipt number (ACT-{year}-{id})
        $receiptNumber = 'ACT-' . date('Y') . '-' . str_pad($customerActivity->id, 5, '0', STR_PAD_LEFT);

        $customerActivity->update([
            'currency' => $data['currency'],
            'amount_paid' => $amount,
            'payment_method' => $data['payment_method'],
            'receipt_number' => $receiptNumber,
            'paid_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Payment recorded successfully')
            ->with('receipt_id', $customerActivity->id);
    }

    /**
     * Accept bulk payment for multiple customers
     */
    public function acceptBulkPayment(Trip $trip, ActivityTrip $activity, Request $request)
    {
        $data = $request->validate([
            'customer_ids' => 'required|array|min:1',
            'customer_ids.*' => 'exists:customers,id',
            'currency' => 'required|in:USD,MVR,SAR',
            'payment_method' => 'required|in:cash,transfer',
        ]);

        // Get the price for selected currency
        $amount = $activity->getPriceForCurrency($data['currency']);
        if (!$amount) {
            return redirect()->back()->withErrors(['currency' => 'Price not set for this currency']);
        }

        $paidCount = 0;
        $paidIds = [];

        foreach ($data['customer_ids'] as $customerId) {
            $customerActivity = CustomerActivity::where('activity_trip_id', $activity->id)
                ->where('customer_id', $customerId)
                ->whereNull('paid_at')
                ->first();

            if ($customerActivity) {
                $receiptNumber = 'ACT-' . date('Y') . '-' . str_pad($customerActivity->id, 5, '0', STR_PAD_LEFT);

                $customerActivity->update([
                    'currency' => $data['currency'],
                    'amount_paid' => $amount,
                    'payment_method' => $data['payment_method'],
                    'receipt_number' => $receiptNumber,
                    'paid_at' => now(),
                ]);

                $paidCount++;
                $paidIds[] = $customerActivity->id;
            }
        }

        if ($paidCount === 0) {
            return redirect()->back()->withErrors(['customer_ids' => 'No unpaid customers selected']);
        }

        return redirect()->back()
            ->with('success', "Payment recorded for {$paidCount} customers")
            ->with('bulk_receipt_ids', $paidIds);
    }

    /**
     * Show bulk receipt for multiple activity payments
     */
    public function showBulkReceipt(Trip $trip, ActivityTrip $activity, Request $request)
    {
        $ids = explode(',', $request->query('ids', ''));

        $customerActivities = CustomerActivity::whereIn('id', $ids)
            ->where('activity_trip_id', $activity->id)
            ->with(['customer'])
            ->get();

        $receipts = $customerActivities->map(function ($ca) use ($trip) {
            $customerTrip = CustomerTrip::where('trip_id', $trip->id)
                ->where('customer_id', $ca->customer_id)
                ->first();

            return [
                'receipt_number' => $ca->receipt_number,
                'customer_name' => $ca->customer->name,
                'customer_name_english' => $ca->customer->name_in_english,
                'umrah_id' => $customerTrip?->umrah_id,
                'currency' => $ca->currency,
                'amount' => $ca->amount_paid,
                'payment_method' => $ca->payment_method,
                'paid_at' => $ca->paid_at,
            ];
        });

        return Inertia::render('Trips/Activities/BulkReceipt', [
            'trip' => $trip,
            'activity' => [
                'name' => $activity->activity->name,
            ],
            'receipts' => $receipts,
        ]);
    }

    /**
     * Show receipt for activity payment
     */
    public function showReceipt(Trip $trip, ActivityTrip $activity, CustomerActivity $customerActivity)
    {
        $customerActivity->load(['customer', 'activityTrip.activity']);

        // Get umrah_id from customer_trip
        $customerTrip = CustomerTrip::where('trip_id', $trip->id)
            ->where('customer_id', $customerActivity->customer_id)
            ->first();

        return Inertia::render('Trips/Activities/Receipt', [
            'trip' => $trip,
            'receipt' => [
                'receipt_number' => $customerActivity->receipt_number,
                'customer_name' => $customerActivity->customer->name,
                'customer_name_english' => $customerActivity->customer->name_in_english,
                'umrah_id' => $customerTrip?->umrah_id,
                'activity_name' => $customerActivity->activityTrip->activity->name,
                'currency' => $customerActivity->currency,
                'amount' => $customerActivity->amount_paid,
                'payment_method' => $customerActivity->payment_method,
                'paid_at' => $customerActivity->paid_at,
            ],
        ]);
    }

    /**
     * Print passenger list for activity
     */
    public function passengerList(Trip $trip, ActivityTrip $activity)
    {
        $activity->load(['activity', 'customerActivities.customer']);

        $customers = $activity->customerActivities->map(function ($ca) use ($trip) {
            $customerTrip = CustomerTrip::where('trip_id', $trip->id)
                ->where('customer_id', $ca->customer_id)
                ->first();

            return [
                'id' => $ca->customer->id,
                'name' => $ca->customer->name,
                'umrah_id' => $customerTrip?->umrah_id,
                'is_paid' => !is_null($ca->paid_at),
            ];
        })->sortBy('umrah_id')->values();

        return Inertia::render('Trips/Activities/PassengerList', [
            'trip' => $trip,
            'activity' => [
                'name' => $activity->activity->name,
                'date' => $activity->date,
            ],
            'customers' => $customers,
        ]);
    }
}
