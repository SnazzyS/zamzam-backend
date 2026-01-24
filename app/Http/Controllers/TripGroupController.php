<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class TripGroupController extends Controller
{
    public function index(Trip $trip)
    {
        $trip->load(['groups', 'customers']);

        return Inertia::render('Trips/Groups/Index', [
            'trip' => $trip,
            'groups' => $trip->groups,
            'customers' => $trip->customers,
        ]);
    }

    public function store(Trip $trip, Request $request)
    {
        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'type' => ['nullable', Rule::in(['group'])],
        ]);

        $name = $data['name'] ?: $this->defaultGroupName($trip);

        $group = $trip->groups()->create([
            'name' => $name,
            'type' => 'group',
        ]);

        return redirect()
            ->back()
            ->with('success', 'Group created successfully');
    }

    public function update(Trip $trip, int $groupId, Request $request)
    {
        $group = $trip->groups()->findOrFail($groupId);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $group->update($data);

        return redirect()
            ->back()
            ->with('success', 'Group updated successfully');
    }

    public function destroy(Trip $trip, int $groupId)
    {
        $group = $trip->groups()->findOrFail($groupId);

        // Remove customers from this group first
        \App\Models\CustomerTrip::where('group_id', $group->id)
            ->update(['group_id' => null]);

        $group->delete();

        return redirect()
            ->back()
            ->with('success', 'Group deleted successfully');
    }

    private function defaultGroupName(Trip $trip): string
    {
        $count = $trip->groups()->count();

        return 'G' . ($count + 1);
    }
}
