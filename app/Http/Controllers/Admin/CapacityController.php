<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Schedule, Tour, TourAvailability, TourExcludedDate};
use App\Services\Bookings\BookingCapacityService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * CapacityController
 *
 * Handles capacity operations.
 */
class CapacityController extends Controller
{
    protected BookingCapacityService $capacityService;

    public function __construct(BookingCapacityService $capacityService)
    {
        $this->capacityService = $capacityService;
    }

    /**
     * AUMENTAR capacidad (incremento relativo)
     * Body: { amount:int, date:"Y-m-d", tour_id:int }
     */
public function increase(Schedule $schedule, Request $request)
{
    $data = $request->validate([
        'tour_id' => ['required', 'exists:tours,tour_id'],
        'amount'  => ['required', 'integer', 'min:-999', 'max:999'], // Permite negativos
        'date'    => ['required', 'date'],
    ]);

    $tour = Tour::findOrFail($data['tour_id']);
    $date = Carbon::parse($data['date'])->toDateString();

    try {
        DB::beginTransaction();

        $override = TourAvailability::where('tour_id', $tour->tour_id)
            ->where('schedule_id', $schedule->schedule_id)
            ->where('date', $date)
            ->first();

        $wasBlocked = $override && $override->is_blocked;
        $snapshot = $this->capacityService->capacitySnapshot($tour, $schedule, $date);
        $confirmed = (int)$snapshot['confirmed'];
        $currentMax = (int)$snapshot['max'];

        // Desbloquear o incrementar/decrementar
        if ($wasBlocked || $currentMax === 0) {
            $newMax = max($confirmed, $confirmed + (int)$data['amount']); // Mínimo = confirmados

            TourExcludedDate::where('tour_id', $tour->tour_id)
                ->where('schedule_id', $schedule->schedule_id)
                ->whereDate('start_date', $date)
                ->delete();

            TourAvailability::updateOrCreate(
                [
                    'tour_id'     => $tour->tour_id,
                    'schedule_id' => $schedule->schedule_id,
                    'date'        => $date,
                ],
                [
                    'is_active'    => true,
                    'is_blocked'   => false,
                    'max_capacity' => $newMax,
                ]
            );
        } else {
            $newMax = max($confirmed, $currentMax + (int)$data['amount']); // Permite sumar/restar

            TourAvailability::updateOrCreate(
                [
                    'tour_id'     => $tour->tour_id,
                    'schedule_id' => $schedule->schedule_id,
                    'date'        => $date,
                ],
                [
                    'is_active'    => true,
                    'is_blocked'   => false,
                    'max_capacity' => $newMax,
                ]
            );
        }

        DB::commit();

        $updated = $this->capacityService->capacitySnapshot($tour, $schedule, $date);
        $pct = $updated['max'] > 0 ? (int)floor(($updated['confirmed'] * 100) / $updated['max']) : 0;

        return response()->json([
            'ok'           => true,
            'used'         => $updated['confirmed'],
            'max_capacity' => $updated['max'],
            'remaining'    => $updated['available'],
            'pct'          => $pct,
        ]);

    } catch (\Throwable $e) {
        DB::rollBack();
        Log::error('[CAPACITY] increase() failed', [
            'error' => $e->getMessage(),
        ]);

        return response()->json([
            'ok'      => false,
            'message' => 'No se pudo aumentar la capacidad',
            'error'   => config('app.debug') ? $e->getMessage() : null,
        ], 500);
    }
}

    /**
     * VER DETALLES (próximos 30 días o fecha específica)
     * Query: ?tour_id=X&date=Y (opcional)
     */
    public function show(Schedule $schedule, Request $request)
    {
        $data = $request->validate([
            'tour_id' => ['required', 'exists:tours,tour_id'],
            'date'    => ['nullable', 'date'],
        ]);

        $tour = Tour::findOrFail($data['tour_id']);
        $tz   = config('app.timezone', 'UTC');

        if (!empty($data['date'])) {
            $start = Carbon::parse($data['date'], $tz)->toDateString();
            $end   = $start;
        } else {
            $start = Carbon::now($tz)->toDateString();
            $end   = Carbon::now($tz)->addDays(30)->toDateString();
        }

        try {
            $dates = collect(\Carbon\CarbonPeriod::create($start, $end))->map->toDateString();
            $rows  = [];

            foreach ($dates as $date) {
                $snap = $this->capacityService->capacitySnapshot($tour, $schedule, $date);
                $pct  = $snap['max'] > 0 ? (int)floor(($snap['confirmed'] * 100) / $snap['max']) : 0;

                $rows[] = [
                    'date'      => $date,
                    'tour'      => $tour->name,
                    'used'      => $snap['confirmed'],
                    'max'       => $snap['max'],
                    'remaining' => $snap['available'],
                    'pct'       => $pct,
                ];
            }

            return response()->json(['ok' => true, 'data' => $rows]);

        } catch (\Throwable $e) {
            Log::error('[CAPACITY] show() failed', ['error' => $e->getMessage()]);

            return response()->json([
                'ok'      => false,
                'message' => 'No se pudieron cargar los detalles',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * BLOQUEAR fecha completa
     * Body: { date:"Y-m-d", tour_id:int, reason?:string }
     */
    public function block(Schedule $schedule, Request $request)
    {
        $data = $request->validate([
            'tour_id' => ['required', 'exists:tours,tour_id'],
            'date'    => ['required', 'date'],
            'reason'  => ['nullable', 'string', 'max:255'],
        ]);

        $tour = Tour::findOrFail($data['tour_id']);
        $date = Carbon::parse($data['date'])->toDateString();

        try {
            DB::beginTransaction();

            // Verificar si ya está bloqueado
            $existing = TourAvailability::where('tour_id', $tour->tour_id)
                ->where('schedule_id', $schedule->schedule_id)
                ->where('date', $date)
                ->first();

            if ($existing && $existing->is_blocked) {
                DB::rollBack();
                return response()->json([
                    'ok'      => false,
                    'message' => 'Esta fecha ya está bloqueada',
                ], 422);
            }

            // Crear override de bloqueo
            TourAvailability::updateOrCreate(
                [
                    'tour_id'     => $tour->tour_id,
                    'schedule_id' => $schedule->schedule_id,
                    'date'        => $date,
                ],
                [
                    'is_active'    => true,
                    'is_blocked'   => true,
                    'max_capacity' => null,
                ]
            );

            // Bitácora en excluded_dates
            TourExcludedDate::firstOrCreate(
                [
                    'tour_id'     => $tour->tour_id,
                    'schedule_id' => $schedule->schedule_id,
                    'start_date'  => $date,
                    'end_date'    => $date,
                ],
                ['reason' => $data['reason'] ?? 'Bloqueo manual']
            );

            DB::commit();

            $snap = $this->capacityService->capacitySnapshot($tour, $schedule, $date);

            Log::info('[CAPACITY] block() ok', [
                'tour_id'     => $tour->tour_id,
                'schedule_id' => $schedule->schedule_id,
                'date'        => $date,
            ]);

            return response()->json([
                'ok'           => true,
                'used'         => $snap['confirmed'],
                'max_capacity' => 0,
                'remaining'    => 0,
                'pct'          => 100,
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('[CAPACITY] block() failed', ['error' => $e->getMessage()]);

            return response()->json([
                'ok'      => false,
                'message' => 'No se pudo bloquear',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
