<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\Models\Product;
use App\Services\LoggerHelper;

/**
 * CutOffController
 *
 * Handles cutoff operations.
 */
class CutOffController extends Controller
{
    public function edit(Request $request)
    {
        try {
            $cutoff = setting('booking.cutoff_hour', config('booking.cutoff_hour', '18:00'));
            $lead   = (int) setting('booking.lead_days', (int) config('booking.lead_days', 1));
            $tz     = config('app.timezone');
            $now    = now($tz);

            $tours = Product::select('product_id', 'name', 'cutoff_hour', 'lead_days')
                ->with(['schedules' => function ($q) {
                    $q->select('schedules.schedule_id', 'schedules.start_time', 'schedules.end_time');
                }])
                ->orderByRaw("name->>'" . app()->getLocale() . "' ASC")
                ->get();

            return view('admin.cut-off.index', compact('cutoff', 'lead', 'tz', 'now', 'tours'));
        } catch (\Throwable $e) {
            Log::error('CutOffController@edit error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('admin.products.cutoff.edit', ['tab' => 'global'])
                ->with('error', __('m_config.cut-off.flash.error_title'));
        }
    }

    /** Guardado GLOBAL */
    public function update(Request $request)
    {
        // Log entry point
        Log::info('CutOffController@update called', [
            'method' => $request->method(),
            'all_data' => $request->all(),
            'has_method_field' => $request->has('_method'),
            'method_field_value' => $request->input('_method'),
        ]);

        try {
            $data = $request->validate([
                'cutoff_hour' => ['required', 'regex:/^(?:[01]\d|2[0-3]):[0-5]\d$/'],
                'lead_days'   => ['required', 'integer', 'min:0', 'max:30'],
            ], [
                'cutoff_hour.regex' => __('m_config.cut-off.hints.cutoff_example', ['ex' => '18:00']),
            ]);

            Log::info('CutOffController@update validated data', $data);

            $result1 = setting_update('booking.cutoff_hour', $data['cutoff_hour']);
            $result2 = setting_update('booking.lead_days',   (int) $data['lead_days']);

            LoggerHelper::mutated('CutOffController', 'update', 'Setting', 'global_cutoff', [
                'cutoff_hour_updated' => $result1,
                'lead_days_updated' => $result2,
            ]);

            return redirect()->route('admin.products.cutoff.edit', ['tab' => 'global'])
                ->with('success', __('m_config.cut-off.actions.save_global'));
        } catch (\Illuminate\Validation\ValidationException $ve) {
            LoggerHelper::validationFailed('CutOffController', 'update', $ve->errors());
            throw $ve;
        } catch (\Throwable $e) {
            LoggerHelper::exception('CutOffController', 'update', 'Setting', 'global_cutoff', $e);
            return redirect()->route('admin.products.cutoff.edit', ['tab' => 'global'])
                ->with('error', __('m_config.cut-off.flash.error_title'));
        }
    }

    /** Bloqueo por TOUR */
    public function updateTourOverrides(Request $request)
    {
        try {
            $data = $request->validate([
                'product_id'     => ['required', 'exists:tours,product_id'],
                'cutoff_hour' => ['nullable', 'regex:/^(?:[01]\d|2[0-3]):[0-5]\d$/'],
                'lead_days'   => ['nullable', 'integer', 'min:0', 'max:30'],
            ], [
                'cutoff_hour.regex' => __('m_config.cut-off.hints.cutoff_example', ['ex' => '18:00']),
            ]);

            $product = Product::where('product_id', $data['product_id'])->firstOrFail();
            $product->cutoff_hour = ($data['cutoff_hour'] !== null && $data['cutoff_hour'] !== '') ? $data['cutoff_hour'] : null;
            $product->lead_days   = ($data['lead_days']   !== null && $data['lead_days']   !== '') ? (int) $data['lead_days'] : null;
            $product->save();

            LoggerHelper::mutated('CutOffController', 'updateTourOverrides', 'Tour', $product->product_id, ['cutoff_hour' => $product->cutoff_hour, 'lead_days' => $product->lead_days]);

            $tab = $request->boolean('from_summary') ? 'summary' : 'tour';

            return redirect()->route('admin.products.cutoff.edit', ['tab' => $tab])
                ->with('success', __('m_config.cut-off.actions.save_tour'));
        } catch (\Illuminate\Validation\ValidationException $ve) {
            LoggerHelper::validationFailed('CutOffController', 'updateTourOverrides', $ve->errors());
            throw $ve;
        } catch (\Throwable $e) {
            LoggerHelper::exception('CutOffController', 'updateTourOverrides', 'Tour', $request->input('product_id'), $e);
            $tab = $request->boolean('from_summary') ? 'summary' : 'tour';
            return redirect()->route('admin.products.cutoff.edit', ['tab' => $tab])
                ->with('error', __('m_config.cut-off.flash.error_title'));
        }
    }

    /** Bloqueo por HORARIO (pivot schedule_tour) */
    public function updateScheduleOverrides(Request $request)
    {
        try {
            $data = $request->validate([
                'product_id'           => ['required', 'exists:tours,product_id'],
                'schedule_id'       => ['required', 'exists:schedules,schedule_id'],
                'pivot_cutoff_hour' => ['nullable', 'regex:/^(?:[01]\d|2[0-3]):[0-5]\d$/'],
                'pivot_lead_days'   => ['nullable', 'integer', 'min:0', 'max:30'],
            ], [
                'pivot_cutoff_hour.regex' => __('m_config.cut-off.hints.cutoff_example', ['ex' => '18:00']),
            ]);

            $payload = [
                'cutoff_hour' => ($data['pivot_cutoff_hour'] !== null && $data['pivot_cutoff_hour'] !== '') ? $data['pivot_cutoff_hour'] : null,
                'lead_days'   => ($data['pivot_lead_days']   !== null && $data['pivot_lead_days']   !== '') ? (int) $data['pivot_lead_days'] : null,
                'updated_at'  => now(),
            ];

            if (Schema::hasColumn('schedule_product', 'created_at')) {
                $payload['created_at'] = DB::raw("COALESCE(created_at, '" . now() . "')");
            }

            DB::table('schedule_product')->updateOrInsert(
                ['product_id' => $data['product_id'], 'schedule_id' => $data['schedule_id']],
                $payload
            );

            LoggerHelper::mutated('CutOffController', 'updateScheduleOverrides', 'ScheduleTour', $data['schedule_id'], ['product_id' => $data['product_id'], 'payload' => $payload]);

            $tab = $request->boolean('from_summary') ? 'summary' : 'schedule';

            return redirect()->route('admin.products.cutoff.edit', ['tab' => $tab])
                ->with('success', __('m_config.cut-off.actions.save_schedule'));
        } catch (\Illuminate\Validation\ValidationException $ve) {
            LoggerHelper::validationFailed('CutOffController', 'updateScheduleOverrides', $ve->errors());
            throw $ve;
        } catch (\Throwable $e) {
            LoggerHelper::exception('CutOffController', 'updateScheduleOverrides', 'ScheduleTour', $data['schedule_id'] ?? null, $e);
            $tab = $request->boolean('from_summary') ? 'summary' : 'schedule';
            return redirect()->route('admin.products.cutoff.edit', ['tab' => $tab])
                ->with('error', __('m_config.cut-off.flash.error_title'));
        }
    }
}
