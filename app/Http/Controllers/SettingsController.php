<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function edit()
    {
        return view('admin.settings', [
            'walkinPin' => Setting::getValue('walkin_pin', '1981'),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'walkin_pin' => ['required', 'regex:/^\d{4,6}$/'],
        ], [
            'walkin_pin.regex' => 'PIN must be 4 to 6 digits.',
        ]);

        Setting::setValue('walkin_pin', $data['walkin_pin']);

        return redirect()
            ->route('admin.settings.edit')
            ->with('status', 'Kiosk walk-in PIN updated.');
    }
}
