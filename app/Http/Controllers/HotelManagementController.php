<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class HotelManagementController extends Controller
{
    public function index()
    {
        $hotels = Hotel::query()
            ->with(['rooms' => function ($query) {
                $query->orderBy('room_number');
            }])
            ->orderBy('name')
            ->get();

        return Inertia::render('Hotels/Index', [
            'hotels' => $hotels,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('hotels', 'name')],
            'address' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'numeric'],
        ]);

        Hotel::create($data);

        return redirect()
            ->back()
            ->with('success', 'ހޮޓާ ހެދިއްޖެ');
    }

    public function update(Request $request, Hotel $hotel)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('hotels', 'name')->ignore($hotel->id)],
            'address' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'numeric'],
        ]);

        $nameChanged = $data['name'] !== $hotel->name;
        $hotel->update($data);

        if ($nameChanged) {
            $roomIds = $hotel->rooms()->pluck('id')->all();
            if (!empty($roomIds)) {
                DB::table('room_trip')
                    ->whereIn('room_id', $roomIds)
                    ->update([
                        'hotel_name' => $hotel->name,
                        'updated_at' => now(),
                    ]);
            }
        }

        return redirect()
            ->back()
            ->with('success', 'ހޮޓާ އަޕްޑޭޓުކުރެވިއްޖެ');
    }

    public function destroy(Hotel $hotel)
    {
        $hotel->delete();

        return redirect()
            ->back()
            ->with('success', 'ހޮޓާ ޑިލީޓް ކުރެވިއްޖެ');
    }

    public function storeRoom(Request $request, Hotel $hotel)
    {
        $data = $request->validate([
            'room_number' => ['required', 'integer', Rule::unique('rooms', 'room_number')],
            'bed_count' => ['required', 'integer', 'min:1'],
        ]);

        $room = $hotel->rooms()->create($data);

        foreach ($hotel->trips as $trip) {
            $trip->rooms()->syncWithoutDetaching([
                $room->id => ['hotel_name' => $hotel->name],
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'ރޫމް ހެދިއްޖެ');
    }

    public function updateRoom(Request $request, Hotel $hotel, Room $room)
    {
        if ($room->hotel_id !== $hotel->id) {
            abort(404);
        }

        $data = $request->validate([
            'room_number' => ['required', 'integer', Rule::unique('rooms', 'room_number')->ignore($room->id)],
            'bed_count' => ['required', 'integer', 'min:1'],
        ]);

        $room->update($data);

        return redirect()
            ->back()
            ->with('success', 'ރޫމް އަޕްޑޭޓުކުރެވިއްޖެ');
    }

    public function destroyRoom(Hotel $hotel, Room $room)
    {
        if ($room->hotel_id !== $hotel->id) {
            abort(404);
        }

        $room->delete();

        return redirect()
            ->back()
            ->with('success', 'ރޫމް ޑިލީޓް ކުރެވިއްޖެ');
    }
}
