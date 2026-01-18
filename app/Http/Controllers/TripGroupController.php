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
            'type' => ['required', Rule::in(['family', 'group'])],
        ]);

        $name = $data['name'] ?: $this->defaultGroupName($trip, $data['type']);

        $group = $trip->groups()->create([
            'name' => $name,
            'type' => $data['type'],
        ]);

        return redirect()
            ->back()
            ->with('success', 'Group created successfully');
    }

    private function defaultGroupName(Trip $trip, string $type): string
    {
        $count = $trip->groups()->where('type', $type)->count();
        $label = match ($type) {
            'family' => 'Family',
            default => 'Group',
        };

        return $label . ' ' . ($count + 1);
    }
}
