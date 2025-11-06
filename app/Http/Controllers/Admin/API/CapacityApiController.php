<?php

namespace App\Http\Controllers\Admin\API;

use App\Http\Controllers\Controller;
use App\Models\{Tour, Schedule, TourAvailability, TourExcludedDate};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class CapacityApiController extends Controller
{
    /**
     * PATCH /api/v1/capacity/schedules/{schedule}/increase
     * Body: { tour_id:int, date:YYYY-MM-DD, amount:int>=1 }
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

        $tour   = Tour::findOrFail($data['tour_id']);
        $date   = Carbon::parse($data['date'])->toDateString();
        $amount = (int) $data['amount'];

        Log::info('[CAPACITY] increase() start', [
            'rid'         => $rid,
            'user_id'     => optional($request->user())->user_id,
            'ip'          => $request->ip(),
            'schedule_id' => $schedule->schedule_id,
            'tour_id'     => $tour->tour_id,
            'date'        => $date,
            'amount'      => $amount,
        ]);

        try {
            // (Opcional) Idempotency-Key de 60s
            $this->idempotencyGuard('capacity', (string)$request->header('Idempotency-Key',''), 60);

            return $this->withCapacityLock($tour->tour_id, $schedule->schedule_id, $date, function () use ($tour, $schedule, $date, $amount, $rid) {

                $availability = TourAvailability::updateOrCreate(
                    [
                        'tour_id'     => $tour->tour_id,
                        'schedule_id' => $schedule->schedule_id,
                        'date'        => $date,
                    ],
                    [
                        'is_active'    => true,
                        'is_blocked'   => false,
                        'max_capacity' => $amount,
                    ]
                );

                [$used, $max, $rem, $pct] = $this->metrics(
                    $tour->tour_id,
                    $schedule->schedule_id,
                    $date,
                    (int) ($availability->max_capacity ?? 0)
                );

                Log::info('[CAPACITY] increase() ok', compact('rid','used','max','rem','pct'));

                return response()->json([
                    'ok'           => true,
                    'used'         => $used,
                    'max_capacity' => $max,
                    'remaining'    => $rem,
                    'pct'          => $pct,
                ]);
            });

        } catch (Throwable $e) {
            report($e);
            Log::error('[CAPACITY] increase() failed', [
                'rid'         => $rid,
                'schedule_id' => $schedule->schedule_id ?? null,
                'tour_id'     => $tour->tour_id ?? null,
                'date'        => $date ?? null,
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

        $tour  = Tour::findOrFail($data['tour_id']);
        $date  = Carbon::parse($data['date'])->toDateString();
        $reason = $data['reason'] ?? 'Bloqueo puntual';

        Log::info('[CAPACITY] block() start', [
            'rid'         => $rid,
            'user_id'     => optional($request->user())->user_id,
            'ip'          => $request->ip(),
            'schedule_id' => $schedule->schedule_id,
            'tour_id'     => $tour->tour_id,
            'date'        => $date,
            'reason'      => $reason,
        ]);

        try {
            // (Opcional) Idempotency-Key de 60s
            $this->idempotencyGuard('capacity', (string)$request->header('Idempotency-Key',''), 60);

            return $this->withCapacityLock($tour->tour_id, $schedule->schedule_id, $date, function () use ($tour, $schedule, $date, $reason, $rid) {

                // Bloqueo puntual
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

                // Bitácora humana (TourExcludedDate)
                TourExcludedDate::firstOrCreate(
                    [
                        'tour_id'     => $tour->tour_id,
                        'schedule_id' => $schedule->schedule_id,
                        'start_date'  => $date,
                        'end_date'    => $date,
                    ],
                    ['reason' => $reason]
                );

                $used = $this->countUsed($tour->tour_id, $schedule->schedule_id, $date);

                Log::info('[CAPACITY] block() ok', compact('rid','used','date'));

                return response()->json([
                    'ok'           => true,
                    'used'         => $used,
                    'max_capacity' => 0,
                    'remaining'    => 0,
                    'pct'          => 100,
                ]);
            });

        } catch (Throwable $e) {
            report($e);
            Log::error('[CAPACITY] block() failed', [
                'rid'         => $rid,
                'schedule_id' => $schedule->schedule_id ?? null,
                'tour_id'     => $tour->tour_id ?? null,
                'date'        => $date ?? null,
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
                    ->whereDate('date', $date)
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
                    'tour'      => method_exists($tour, 'getTranslatedName')
                        ? $tour->getTranslatedName(app()->getLocale())
                        : $tour->name,
                    'used'      => $used,
                    'max'       => $max,
                    'remaining' => $rem,
                    'pct'       => $pct,
                ];
            }

            Log::info('[CAPACITY] details() ok', ['rid' => $rid, 'rows' => count($rows)]);

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

    /** Lock por tour+schedule+date + transacción */
    private function withCapacityLock(int $tourId, int $scheduleId, string $date, \Closure $callback)
    {
        $key  = "cap:lock:{$tourId}:{$scheduleId}:{$date}";
        $lock = Cache::lock($key, 5);           // TTL del lock 5s
        return $lock->block(3, function () use ($callback) { // esperar hasta 3s
            return DB::transaction(fn() => $callback(), 3); // 3 reintentos en deadlock
        });
    }

    /** Guard idempotencia simple (60s por defecto) */
    private function idempotencyGuard(string $scope, string $key, int $ttlSeconds = 60): void
    {
        if (!$key) return;
        $cacheKey = "idem:{$scope}:{$key}";
        // Si ya existe una en curso, responder 409
        if (!Cache::add($cacheKey, '1', $ttlSeconds)) {
            abort(response()->json(['ok'=>false,'message'=>'duplicate_request'], 409));
        }
        // Nota: si quisieras devolver la misma respuesta en reintentos,
        // guarda aquí el JSON y recupéralo antes con Cache::get($cacheKeyResp).
    }

    /** Capacidad base en schedule_tour.base_capacity */
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

    /** Cuenta “usados” en booking_details para fecha+horario */
    private function countUsed(int $tourId, int $scheduleId, string $date): int
    {
        try {
            $q = DB::table('booking_details as bd')
                ->join('bookings as b', 'b.booking_id', '=', 'bd.booking_id')
                ->where('bd.tour_id', $tourId)
                ->where('bd.schedule_id', $scheduleId)
                ->whereDate('bd.tour_date', $date)
                ->whereNull('bd.deleted_at')
                ->whereNull('b.deleted_at');
            // Si usas estados: ->whereIn('b.status', ['paid','confirmed'])
            return (int) $q->count();
        } catch (Throwable $e) {
            Log::error('[CAPACITY] countUsed() failed', [
                'tour_id'     => $tourId,
                'schedule_id' => $scheduleId,
                'date'        => $date,
                'error'       => $e->getMessage(),
            ]);
            return 0;
        }
    }

    /** Métricas básicas con $max ya resuelto */
    private function metrics(int $tourId, int $scheduleId, string $date, int $max): array
    {
        $used = $this->countUsed($tourId, $scheduleId, $date);
        $rem  = max(0, $max - $used);
        $pct  = $max > 0 ? (int) floor(($used * 100) / $max) : 0;
        return [$used, $max, $rem, $pct];
    }
}
