<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return response()->json([
            'company_address' => Setting::get('company_address', ''),
            'company_phone' => Setting::get('company_phone', ''),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'company_address' => ['nullable', 'string', 'max:500'],
            'company_phone' => ['nullable', 'string', 'max:50'],
        ]);

        Setting::set('company_address', $data['company_address'] ?? '');
        Setting::set('company_phone', $data['company_phone'] ?? '');

        return redirect()
            ->back()
            ->with('success', 'Settings updated successfully');
    }
}
