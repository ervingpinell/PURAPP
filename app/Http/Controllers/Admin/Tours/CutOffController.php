<?php

namespace App\Http\Controllers\Admin\Tours;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\Models\AppSetting;
use App\Models\Tour;

class CutOffController extends Controller
{
    public function edit(Request $request)
    {
        try {
            $cutoff = AppSetting::get('booking.cutoff_hour', config('booking.cutoff_hour', '18:00'));
            $lead   = (int) AppSetting::get('booking.lead_days', (int) config('booking.lead_days', 1));
            $tz     = config('app.timezone');
            $now    = now($tz);

            $tours = Tour::select('tour_id','name','cutoff_hour','lead_days')
                ->with(['schedules' => function ($q) {
                    $q->select('schedules.schedule_id','schedules.start_time','schedules.end_time');
                }])
                ->orderBy('name')
                ->get();

            return view('admin.cut-off.index', compact('cutoff','lead','tz','now','tours'));
        } catch (\Throwable $e) {
            Log::error('CutOffController@edit error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('admin.tours.cutoff.edit', ['tab' => 'global'])
                ->with('error', __('m_config.cut-off.flash.error_title'));
        }
    }

    /** Guardado GLOBAL */
    public function update(Request $request)
    {
        try {
            $data = $request->validate([
                'cutoff_hour' => ['required','regex:/^(?:[01]\d|2[0-3]):[0-5]\d$/'],
                'lead_days'   => ['required','integer','min:0','max:30'],
            ], [
                'cutoff_hour.regex' => __('m_config.cut-off.hints.cutoff_example', ['ex' => '18:00']),
            ]);

            AppSetting::set('booking.cutoff_hour', $data['cutoff_hour']);
            AppSetting::set('booking.lead_days',   (int) $data['lead_days']);

            return redirect()->route('admin.tours.cutoff.edit', ['tab' => 'global'])
                ->with('success', __('m_config.cut-off.actions.save_global'));
        } catch (\Illuminate\Validation\ValidationException $ve) {
            Log::warning('CutOffController@update validation', ['errors' => $ve->errors()]);
            throw $ve;
        } catch (\Throwable $e) {
            Log::error('CutOffController@update error', ['error' => $e->getMessage()]);
            return redirect()->route('admin.tours.cutoff.edit', ['tab' => 'global'])
                ->with('error', __('m_config.cut-off.flash.error_title'));
        }
    }

    /** Bloqueo por TOUR */
    public function updateTourOverrides(Request $request)
    {
        try {
            $data = $request->validate([
                'tour_id'     => ['required','exists:tours,tour_id'],
                'cutoff_hour' => ['nullable','regex:/^(?:[01]\d|2[0-3]):[0-5]\d$/'],
                'lead_days'   => ['nullable','integer','min:0','max:30'],
            ], [
                'cutoff_hour.regex' => __('m_config.cut-off.hints.cutoff_example', ['ex' => '18:00']),
            ]);

            $tour = Tour::where('tour_id', $data['tour_id'])->firstOrFail();
            $tour->cutoff_hour = ($data['cutoff_hour'] !== null && $data['cutoff_hour'] !== '') ? $data['cutoff_hour'] : null;
            $tour->lead_days   = ($data['lead_days']   !== null && $data['lead_days']   !== '') ? (int) $data['lead_days'] : null;
            $tour->save();

            $tab = $request->boolean('from_summary') ? 'summary' : 'tour';

            return redirect()->route('admin.tours.cutoff.edit', ['tab' => $tab])
                ->with('success', __('m_config.cut-off.actions.save_tour'));
        } catch (\Illuminate\Validation\ValidationException $ve) {
            Log::warning('CutOffController@updateTourOverrides validation', ['errors' => $ve->errors()]);
            throw $ve;
        } catch (\Throwable $e) {
            Log::error('CutOffController@updateTourOverrides error', ['error' => $e->getMessage()]);
            $tab = $request->boolean('from_summary') ? 'summary' : 'tour';
            return redirect()->route('admin.tours.cutoff.edit', ['tab' => $tab])
                ->with('error', __('m_config.cut-off.flash.error_title'));
        }
    }

    /** Bloqueo por HORARIO (pivot schedule_tour) */
    public function updateScheduleOverrides(Request $request)
    {
        try {
            $data = $request->validate([
                'tour_id'           => ['required', 'exists:tours,tour_id'],
                'schedule_id'       => ['required', 'exists:schedules,schedule_id'],
                'pivot_cutoff_hour' => ['nullable','regex:/^(?:[01]\d|2[0-3]):[0-5]\d$/'],
                'pivot_lead_days'   => ['nullable','integer','min:0','max:30'],
            ], [
                'pivot_cutoff_hour.regex' => __('m_config.cut-off.hints.cutoff_example', ['ex' => '18:00']),
            ]);

            $payload = [
                'cutoff_hour' => ($data['pivot_cutoff_hour'] !== null && $data['pivot_cutoff_hour'] !== '') ? $data['pivot_cutoff_hour'] : null,
                'lead_days'   => ($data['pivot_lead_days']   !== null && $data['pivot_lead_days']   !== '') ? (int) $data['pivot_lead_days'] : null,
                'updated_at'  => now(),
            ];

            if (Schema::hasColumn('schedule_tour', 'created_at')) {
                $payload['created_at'] = DB::raw("COALESCE(created_at, '" . now() . "')");
            }

            DB::table('schedule_tour')->updateOrInsert(
                ['tour_id' => $data['tour_id'], 'schedule_id' => $data['schedule_id']],
                $payload
            );

            $tab = $request->boolean('from_summary') ? 'summary' : 'schedule';

            return redirect()->route('admin.tours.cutoff.edit', ['tab' => $tab])
                ->with('success', __('m_config.cut-off.actions.save_schedule'));
        } catch (\Illuminate\Validation\ValidationException $ve) {
            Log::warning('CutOffController@updateScheduleOverrides validation', ['errors' => $ve->errors()]);
            throw $ve;
        } catch (\Throwable $e) {
            Log::error('CutOffController@updateScheduleOverrides error', ['error' => $e->getMessage()]);
            $tab = $request->boolean('from_summary') ? 'summary' : 'schedule';
            return redirect()->route('admin.tours.cutoff.edit', ['tab' => $tab])
                ->with('error', __('m_config.cut-off.flash.error_title'));
        }
    }
}
