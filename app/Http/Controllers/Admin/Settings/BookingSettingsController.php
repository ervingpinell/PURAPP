<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BookingSettingsController extends Controller
{
    public function edit()
    {
        $cutoff = AppSetting::get('booking.cutoff_hour', config('booking.cutoff_hour', '18:00'));
        $lead   = (int) AppSetting::get('booking.lead_days', (int) config('booking.lead_days', 1));
        $tz     = config('app.timezone');

        return view('admin.settings.booking', compact('cutoff', 'lead', 'tz'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'cutoff_hour' => ['required','regex:/^(?:[01]\d|2[0-3]):[0-5]\d$/'],
            'lead_days'   => ['required','integer','min:0','max:30'],
        ], [
            'cutoff_hour.regex' => 'Formato inválido. Usa HH:MM en 24h (ej. 18:00).',
        ]);

        AppSetting::set('booking.cutoff_hour', $data['cutoff_hour']);
        AppSetting::set('booking.lead_days', $data['lead_days']);

        // Limpia caches por si acaso
        Cache::forget('setting:booking.cutoff_hour');
        Cache::forget('setting:booking.lead_days');

        return redirect()->back()->with('success', 'Booking settings updated.');
    }
}
