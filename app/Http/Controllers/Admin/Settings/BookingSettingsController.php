<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\AppSetting;
use App\Models\Tour;
use Illuminate\Support\Facades\Schema;


class BookingSettingsController extends Controller
{
    public function edit()
    {
        $cutoff = AppSetting::get('booking.cutoff_hour', config('booking.cutoff_hour', '18:00'));
        $lead   = (int) AppSetting::get('booking.lead_days', (int) config('booking.lead_days', 1));
        $tz     = config('app.timezone');

        // Trae tours con sus schedules y columnas pivot (cutoff/lead) si existen
        $tours = Tour::select('tour_id','name','cutoff_hour','lead_days')
            ->with(['schedules' => function ($q) {
                $q->select('schedules.schedule_id','schedules.start_time','schedules.end_time');
            }])
            ->orderBy('name')
            ->get();

        return view('admin.settings.booking', compact('cutoff','lead','tz','tours'));
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
        AppSetting::set('booking.lead_days',   (int) $data['lead_days']);

        return back()->with('success', 'Global booking settings saved.');
    }

    /**
     * Override por TOUR (guarda en columnas de tours: cutoff_hour / lead_days).
     * Si vienen vacíos, se setean como NULL para heredar los globales.
     */
    public function updateTourOverrides(Request $request)
    {
        $data = $request->validate([
            'tour_id'     => ['required','exists:tours,tour_id'],
            'cutoff_hour' => ['nullable','regex:/^(?:[01]\d|2[0-3]):[0-5]\d$/'],
            'lead_days'   => ['nullable','integer','min:0','max:30'],
        ], [
            'cutoff_hour.regex' => 'Formato inválido. Usa HH:MM en 24h (ej. 18:00).',
        ]);

        $tour = Tour::where('tour_id', $data['tour_id'])->firstOrFail();

        $tour->cutoff_hour = $data['cutoff_hour'] !== null && $data['cutoff_hour'] !== '' ? $data['cutoff_hour'] : null;
        $tour->lead_days   = $data['lead_days']   !== null && $data['lead_days']   !== '' ? (int) $data['lead_days'] : null;
        $tour->save();

        return back()->with('success', 'Tour override saved.');
    }

    /**
     * Override por HORARIO (pivot schedule_tour: cutoff_hour / lead_days).
     * Inserta si no existe, actualiza si ya existe. Si vienen vacíos, pone NULL.
     */
    public function updateScheduleOverrides(Request $request)
    {
        $data = $request->validate([
            'tour_id'         => ['required', 'exists:tours,tour_id'],
            'schedule_id'     => ['required', 'exists:schedules,schedule_id'],
            'pivot_cutoff_hour' => ['nullable','regex:/^(?:[01]\d|2[0-3]):[0-5]\d$/'],
            'pivot_lead_days'   => ['nullable','integer','min:0','max:30'],
        ], [
            'pivot_cutoff_hour.regex' => 'Formato inválido. Usa HH:MM en 24h (ej. 18:00).',
        ]);

        // Normaliza a null cuando vienen vacíos (para “heredar”)
        $payload = [
            'cutoff_hour' => ($data['pivot_cutoff_hour'] !== null && $data['pivot_cutoff_hour'] !== '') ? $data['pivot_cutoff_hour'] : null,
            'lead_days'   => ($data['pivot_lead_days']   !== null && $data['pivot_lead_days']   !== '') ? (int) $data['pivot_lead_days'] : null,
            'updated_at'  => now(),
        ];

        // Si tu tabla pivot tiene timestamps, seteamos created_at solo cuando no exista
        if (Schema::hasColumn('schedule_tour', 'created_at')) {
            // COALESCE mantiene created_at existente en updates y lo pone ahora() en inserts
            $payload['created_at'] = DB::raw("COALESCE(created_at, '" . now() . "')");
        }

        DB::table('schedule_tour')->updateOrInsert(
            ['tour_id' => $data['tour_id'], 'schedule_id' => $data['schedule_id']],
            $payload
        );

        return back()->with('success', 'Schedule override saved.');
    }

}
