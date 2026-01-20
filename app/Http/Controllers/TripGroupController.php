<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TripGroupController extends Controller
{
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

    private function defaultGroupName(Trip $trip): string
    {
        $count = $trip->groups()->count();

        return 'G' . ($count + 1);
    }
}
