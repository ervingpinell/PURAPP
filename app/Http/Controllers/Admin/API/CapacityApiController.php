<?php

namespace App\Http\Controllers\Admin\API;

use App\Http\Controllers\Controller;
use App\Models\{Tour, Schedule, TourAvailability, TourExcludedDate};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class CapacityApiController extends Controller
{
    /**
     * PATCH /api/v1/capacity/schedules/{schedule}/increase
     * Body: { tour_id:int, date:YYYY-MM-DD, amount:int>=1 }  (amount = nueva capacidad máxima para ese día/horario)
     * Auth: sanctum
     */
    public function increase(Request $request, Schedule $schedule)
    {
        $rid = (string) Str::uuid();

        $data = $request->validate([
            'tour_id' => ['required','exists:tours,tour_id'],
            'date'    => ['required','date'],
            'amount'  => ['required','integer','min:1','max:9999'],
        ]);

        $tour = Tour::findOrFail($data['tour_id']);
        $date = Carbon::parse($data['date'])->toDateString();

        Log::info('[CAPACITY] increase() start', [
            'rid'         => $rid,
            'user_id'     => optional($request->user())->user_id,
            'ip'          => $request->ip(),
            'schedule_id' => $schedule->schedule_id,
            'tour_id'     => $tour->tour_id,
            'date'        => $date,
            'amount'      => (int) $data['amount'],
        ]);

        try {
            // Override puntual para ese día+horario (ABSOLUTO = amount)
            $availability = TourAvailability::updateOrCreate(
                [
                    'tour_id'     => $tour->tour_id,
                    'schedule_id' => $schedule->schedule_id,
                    'date'        => $date,
                ],
                [
                    'is_active'    => true,
                    'is_blocked'   => false,
                    'max_capacity' => (int) $data['amount'],
                ]
            );

            [$used, $max, $rem, $pct] = $this->metrics(
                $tour->tour_id,
                $schedule->schedule_id,
                $date,
                (int) ($availability->max_capacity ?? 0)
            );

            Log::info('[CAPACITY] increase() ok', [
                'rid'         => $rid,
                'used'        => $used,
                'max'         => $max,
                'remaining'   => $rem,
                'pct'         => $pct,
            ]);

            return response()->json([
                'ok'           => true,
                'used'         => $used,
                'max_capacity' => $max,
                'remaining'    => $rem,
                'pct'          => $pct,
            ]);
        } catch (Throwable $e) {
            report($e);
            Log::error('[CAPACITY] increase() failed', [
                'rid'         => $rid,
                'schedule_id' => $schedule->schedule_id ?? null,
                'tour_id'     => $tour->tour_id ?? null,
                'date'        => $date,
                'error'       => $e->getMessage(),
            ]);

            return response()->json([
                'ok'      => false,
                'message' => 'increase_failed',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * PATCH /api/v1/capacity/schedules/{schedule}/block
     * Body: { tour_id:int, date:YYYY-MM-DD, reason?:string }
     * Auth: sanctum
     */
    public function block(Request $request, Schedule $schedule)
    {
        $rid = (string) Str::uuid();

        $data = $request->validate([
            'tour_id' => ['required','exists:tours,tour_id'],
            'date'    => ['required','date'],
            'reason'  => ['nullable','string','max:255'],
        ]);

        $tour = Tour::findOrFail($data['tour_id']);
        $date = Carbon::parse($data['date'])->toDateString();

        Log::info('[CAPACITY] block() start', [
            'rid'         => $rid,
            'user_id'     => optional($request->user())->user_id,
            'ip'          => $request->ip(),
            'schedule_id' => $schedule->schedule_id,
            'tour_id'     => $tour->tour_id,
            'date'        => $date,
            'reason'      => $data['reason'] ?? null,
        ]);

        try {
            // Bloqueo puntual (override is_blocked=true y max_capacity=null)
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

            // Bitácora
            TourExcludedDate::firstOrCreate(
                [
                    'tour_id'     => $tour->tour_id,
                    'schedule_id' => $schedule->schedule_id,
                    'start_date'  => $date,
                    'end_date'    => $date,
                ],
                ['reason' => $data['reason'] ?? 'Bloqueo puntual']
            );

            $used = $this->countUsed($tour->tour_id, $schedule->schedule_id, $date);

            Log::info('[CAPACITY] block() ok', [
                'rid'   => $rid,
                'used'  => $used,
                'date'  => $date,
            ]);

            return response()->json([
                'ok'           => true,
                'used'         => $used,
                'max_capacity' => 0,
                'remaining'    => 0,
                'pct'          => 100,
            ]);
        } catch (Throwable $e) {
            report($e);
            Log::error('[CAPACITY] block() failed', [
                'rid'         => $rid,
                'schedule_id' => $schedule->schedule_id ?? null,
                'tour_id'     => $tour->tour_id ?? null,
                'date'        => $date,
                'error'       => $e->getMessage(),
            ]);

            return response()->json([
                'ok'      => false,
                'message' => 'block_failed',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * GET /api/v1/capacity/schedules/{schedule}/details?tour_id=&days=30[&start=YYYY-MM-DD]
     * Auth: sanctum
     */
    public function details(Request $request, Schedule $schedule)
    {
        $rid = (string) Str::uuid();

        $data = $request->validate([
            'tour_id' => ['required','exists:tours,tour_id'],
            'days'    => ['nullable','integer','min:1','max:90'],
            'start'   => ['nullable','date'],
        ]);

        $tour  = Tour::findOrFail($data['tour_id']);
        $days  = (int) ($data['days'] ?? 30);
        $start = isset($data['start']) ? Carbon::parse($data['start'])->startOfDay() : Carbon::today();

        Log::info('[CAPACITY] details() start', [
            'rid'         => $rid,
            'user_id'     => optional($request->user())->user_id,
            'ip'          => $request->ip(),
            'schedule_id' => $schedule->schedule_id,
            'tour_id'     => $tour->tour_id,
            'days'        => $days,
            'start'       => $start->toDateString(),
        ]);

        try {
            $rows = [];

            for ($d = 0; $d < $days; $d++) {
                $date = (clone $start)->addDays($d)->toDateString();

                // override puntual para esa fecha
                $override = TourAvailability::where('tour_id', $tour->tour_id)
                    ->where('schedule_id', $schedule->schedule_id)
                    ->whereDate('date', $date) // PG-safe
                    ->first();

                if ($override?->is_blocked) {
                    $max = 0;
                } elseif (!is_null($override?->max_capacity)) {
                    $max = (int) $override->max_capacity;
                } else {
                    $base = $this->resolveBaseCapacity($tour->tour_id, $schedule->schedule_id);
                    $max  = (int) ($base ?? $tour->max_capacity ?? 0);
                }

                $used = $this->countUsed($tour->tour_id, $schedule->schedule_id, $date);
                $rem  = max(0, $max - $used);
                $pct  = $max > 0 ? (int) floor(($used * 100) / $max) : 0;

                $rows[] = [
                    'date'      => $date,
                    'tour'      => $tour->name,
                    'used'      => $used,
                    'max'       => $max,
                    'remaining' => $rem,
                    'pct'       => $pct,
                ];
            }

            Log::info('[CAPACITY] details() ok', [
                'rid'   => $rid,
                'rows'  => count($rows),
            ]);

            return response()->json(['ok' => true, 'data' => $rows]);
        } catch (Throwable $e) {
            report($e);
            Log::error('[CAPACITY] details() failed', [
                'rid'         => $rid,
                'schedule_id' => $schedule->schedule_id ?? null,
                'tour_id'     => $tour->tour_id ?? null,
                'error'       => $e->getMessage(),
            ]);

            return response()->json([
                'ok'      => false,
                'message' => 'details_failed',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /* ======================= Helpers ======================= */

    /**
     * Capacidad base en el pivot `schedule_tour.base_capacity`.
     */
    private function resolveBaseCapacity(int $tourId, int $scheduleId): ?int
    {
        try {
            $base = DB::table('schedule_tour')
                ->where('tour_id', $tourId)
                ->where('schedule_id', $scheduleId)
                ->value('base_capacity');

            return is_null($base) ? null : (int) $base;
        } catch (Throwable $e) {
            Log::warning('[CAPACITY] resolveBaseCapacity() failed', [
                'tour_id'     => $tourId,
                'schedule_id' => $scheduleId,
                'error'       => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Cuenta “usados” consultando booking_details (evita usar schedule_id en bookings).
     * Ajusta nombres de tabla/columnas si difieren.
     */
    private function countUsed(int $tourId, int $scheduleId, string $date): int
    {
        try {
            // Asumimos tabla booking_details con: booking_id, tour_id, schedule_id, tour_date (DATE), deleted_at (soft deletes)
            // y tabla bookings con: booking_id, deleted_at (soft deletes), status (opcional)
            $q = DB::table('booking_details as bd')
                ->join('bookings as b', 'b.booking_id', '=', 'bd.booking_id')
                ->where('bd.tour_id', $tourId)
                ->where('bd.schedule_id', $scheduleId)
                ->whereDate('bd.tour_date', $date)
                ->whereNull('bd.deleted_at')
                ->whereNull('b.deleted_at');

            // Si manejas estados/cancelaciones, agrega filtros aquí, por ejemplo:
            // ->whereIn('b.status', ['paid','confirmed'])

            return (int) $q->count();
        } catch (Throwable $e) {
            Log::error('[CAPACITY] countUsed() failed', [
                'tour_id'     => $tourId,
                'schedule_id' => $scheduleId,
                'date'        => $date,
                'error'       => $e->getMessage(),
            ]);
            // Si falla la consulta, devolvemos 0 para no romper toda la respuesta
            return 0;
        }
    }

    /**
     * Calcula métricas con “max” ya resuelto.
     */
    private function metrics(int $tourId, int $scheduleId, string $date, int $max): array
    {
        $used = $this->countUsed($tourId, $scheduleId, $date);
        $rem  = max(0, $max - $used);
        $pct  = $max > 0 ? (int) floor(($used * 100) / $max) : 0;
        return [$used, $max, $rem, $pct];
    }
}
