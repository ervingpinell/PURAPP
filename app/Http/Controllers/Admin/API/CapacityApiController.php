<?php

namespace App\Http\Controllers\Admin\API;

use App\Http\Controllers\Controller;
use App\Models\{Tour, Schedule, TourAvailability, TourExcludedDate};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Log, Cache};
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
        $this->maybeSleep();
        $rid = (string) Str::uuid();

        $data = $request->validate([
            'tour_id' => ['required','exists:tours,tour_id'],
            'date'    => ['required','date'],
            'amount'  => ['required','integer','min:1','max:9999'],
        ]);

        $tour = Tour::findOrFail($data['tour_id']);
        $date = Carbon::parse($data['date'])->toDateString();

        if (! $this->scheduleLinkedToTour($tour->tour_id, $schedule->schedule_id)) {
            return response()->json(['ok'=>false,'message'=>'schedule_not_linked_to_tour'], 404);
        }

        Log::info('[CAPACITY] increase() start', [
            'rid'         => $rid,
            'user_id'     => optional($request->user())->user_id,
            'ip'          => $request->ip(),
            'schedule_id' => $schedule->schedule_id,
            'tour_id'     => $tour->tour_id,
            'date'        => $date,
            'amount'      => (int) $data['amount'],
        ]);

        // Lock tolerante (no falla si el store no soporta locks)
        $lock = $this->lockOrNull("cap:increase:{$tour->tour_id}:{$schedule->schedule_id}:{$date}", 10);

        try {
            if ($lock && ! $lock->block(5)) {
                return response()->json(['ok'=>false,'message'=>'increase_locked'], 423);
            }

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

            Log::info('[CAPACITY] increase() ok', compact('rid','used','max','rem','pct'));

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
            ], 422);
        } finally {
            $this->releaseLock($lock);
        }
    }

    /**
     * PATCH /api/v1/capacity/schedules/{schedule}/block
     * Body: { tour_id:int, date:YYYY-MM-DD, reason?:string }
     * Auth: sanctum
     */
    public function block(Request $request, Schedule $schedule)
    {
        $this->maybeSleep();
        $rid = (string) Str::uuid();

        $data = $request->validate([
            'tour_id' => ['required','exists:tours,tour_id'],
            'date'    => ['required','date'],
            'reason'  => ['nullable','string','max:255'],
        ]);

        $tour = Tour::findOrFail($data['tour_id']);
        $date = Carbon::parse($data['date'])->toDateString();

        if (! $this->scheduleLinkedToTour($tour->tour_id, $schedule->schedule_id)) {
            return response()->json(['ok'=>false,'message'=>'schedule_not_linked_to_tour'], 404);
        }

        Log::info('[CAPACITY] block() start', [
            'rid'         => $rid,
            'user_id'     => optional($request->user())->user_id,
            'ip'          => $request->ip(),
            'schedule_id' => $schedule->schedule_id,
            'tour_id'     => $tour->tour_id,
            'date'        => $date,
            'reason'      => $data['reason'] ?? null,
        ]);

        $lock = $this->lockOrNull("cap:block:{$tour->tour_id}:{$schedule->schedule_id}:{$date}", 10);

        try {
            if ($lock && ! $lock->block(5)) {
                return response()->json(['ok'=>false,'message'=>'block_locked'], 423);
            }

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

            Log::info('[CAPACITY] block() ok', compact('rid','used','date'));

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
            ], 422);
        } finally {
            $this->releaseLock($lock);
        }
    }

    /**
     * GET /api/v1/capacity/schedules/{schedule}/details?tour_id=&days=30[&start=YYYY-MM-DD]
     * Auth: sanctum
     */
 public function details(Request $request, Schedule $schedule)
{
    $rid = (string) Str::uuid();

    try {
        // Validación
        $data = $request->validate([
            'tour_id' => ['required','integer','exists:tours,tour_id'],
            'days'    => ['nullable','integer','min:1','max:90'],
            'start'   => ['nullable','date'],
        ]);

        // Verifica que el schedule esté activo (opcional)
        if (property_exists($schedule, 'is_active') && $schedule->is_active === false) {
            return response()->json([
                'ok' => false,
                'message' => 'schedule_inactive',
            ], 422);
        }

        $tourId = (int) $data['tour_id'];
        $tour   = Tour::find($tourId);
        if (!$tour) {
            return response()->json([
                'ok' => false,
                'message' => 'tour_not_found',
            ], 404);
        }

        $days  = (int) ($data['days'] ?? 30);
        $start = isset($data['start'])
            ? Carbon::parse($data['start'])->startOfDay()
            : Carbon::today();

        Log::info('[CAPACITY] details() start', [
            'rid'         => $rid,
            'user_id'     => optional($request->user())->user_id,
            'ip'          => $request->ip(),
            'schedule_id' => $schedule->schedule_id,
            'tour_id'     => $tour->tour_id,
            'days'        => $days,
            'start'       => $start->toDateString(),
        ]);

        // Pre-calc base capacity (evita consultarlo 90 veces)
        $base = $this->resolveBaseCapacity($tour->tour_id, $schedule->schedule_id);
        $fallbackMax = (int) ($base ?? $tour->max_capacity ?? 0);

        $rows = [];
        for ($d = 0; $d < $days; $d++) {
            $date = (clone $start)->addDays($d)->toDateString();

            // Busca override puntual
            $override = TourAvailability::where('tour_id', $tour->tour_id)
                ->where('schedule_id', $schedule->schedule_id)
                ->whereDate('date', $date)
                ->first();

            if ($override?->is_blocked) {
                $max = 0;
            } elseif (!is_null($override?->max_capacity)) {
                $max = (int) $override->max_capacity;
            } else {
                $max = $fallbackMax;
            }

            // Cuenta usados — cualquier fallo devuelve 0 y loguea
            $used = $this->countUsed($tour->tour_id, $schedule->schedule_id, $date);
            $rem  = max(0, $max - $used);
            $pct  = $max > 0 ? (int) floor(($used * 100) / $max) : 0;

            $rows[] = [
                'date'      => $date,
                'tour'      => $tour->name,           // si quieres el traducido, cámbialo por getTranslatedName(app()->getLocale())
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

    } catch (\Illuminate\Validation\ValidationException $ve) {
        // Errores 422 bien formateados
        Log::warning('[CAPACITY] details() validation', [
            'rid'    => $rid,
            'errors' => $ve->errors(),
        ]);
        return response()->json(['ok' => false, 'errors' => $ve->errors()], 422);

    } catch (\Throwable $e) {
        // Nunca reventar en 500 silencioso: log detallado + mensaje útil
        Log::error('[CAPACITY] details() failed', [
            'rid'         => $rid,
            'schedule_id' => $schedule->schedule_id ?? null,
            'tour_id'     => $request->input('tour_id'),
            'error'       => $e->getMessage(),
            'trace'       => str_contains(app()->environment(), 'local') ? $e->getTraceAsString() : null,
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
     * Devuelve un lock si el store lo soporta; si no, devuelve null para que no falle en local.
     */
    private function lockOrNull(string $key, int $seconds)
    {
        try {
            // Si estás seguro de tener Redis, puedes forzar: Cache::store('redis')->lock(...)
            $lock = Cache::lock($key, $seconds);
            // Verifica que el objeto tenga los métodos esperados
            if (method_exists($lock, 'block') && method_exists($lock, 'release')) {
                return $lock;
            }
        } catch (\BadMethodCallException $e) {
            // Store sin locks (file, array, etc.)
            Log::notice('[CAPACITY] cache store without locks; proceeding without lock', ['key'=>$key]);
        } catch (Throwable $e) {
            Log::warning('[CAPACITY] lock creation failed; proceeding without lock', ['key'=>$key,'error'=>$e->getMessage()]);
        }
        return null; // sin lock
    }

    private function releaseLock($lock): void
    {
        if ($lock && method_exists($lock, 'release')) {
            try { $lock->release(); } catch (\Throwable $e) {}
        }
    }

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

            // ->whereIn('b.status', ['paid','confirmed']) // si aplica
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

    private function metrics(int $tourId, int $scheduleId, string $date, int $max): array
    {
        $used = $this->countUsed($tourId, $scheduleId, $date);
        $rem  = max(0, $max - $used);
        $pct  = $max > 0 ? (int) floor(($used * 100) / $max) : 0;
        return [$used, $max, $rem, $pct];
    }

    private function scheduleLinkedToTour(int $tourId, int $scheduleId): bool
    {
        try {
            return DB::table('schedule_tour')
                ->where('tour_id', $tourId)
                ->where('schedule_id', $scheduleId)
                ->exists();
        } catch (Throwable $e) {
            Log::warning('[CAPACITY] scheduleLinkedToTour() failed', [
                'tour_id'     => $tourId,
                'schedule_id' => $scheduleId,
                'error'       => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Sleep opcional con jitter, configurable por config('api.sleep_ms', 0)
     */
    private function maybeSleep(): void
    {
        $base = (int) (config('api.sleep_ms', 0) ?: 0);
        if ($base > 0) {
            $jitter = random_int(0, (int) floor($base * 0.35));
            usleep(($base + $jitter) * 1000);
        }
    }
}
