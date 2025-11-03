<?php
// app/Http/Controllers/Admin/CapacityController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class CapacityController extends Controller
{
    /* ===========================
     * Aumentar/Desbloquear capacidad (por fecha)
     * Body JSON: { amount:int, date:"Y-m-d" | "d/m/Y" }
     * Respuesta: { ok, used, max_capacity, remaining, pct }
     * =========================== */
    public function increase(Schedule $schedule, Request $request)
    {
        $amount = (int) $request->input('amount', 0);
        $dateIn = (string) $request->input('date', '');

        if ($amount <= 0) {
            return response()->json(['ok' => false, 'message' => 'Cantidad inválida'], 422);
        }
        if (!$dateIn) {
            return response()->json(['ok' => false, 'message' => 'Fecha requerida'], 422);
        }

        try {
            $date = $this->normalizeDate($dateIn);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'message' => 'Fecha inválida', 'error' => $e->getMessage()], 422);
        }

        try {
            DB::beginTransaction();

            $this->ensureOverridesTable();

            // Capacidad base (del schedule)
            $baseMax = (int) ($schedule->max_capacity ?? 0);

            // Override actual (si existe)
            $over = DB::table('schedule_capacity_overrides')
                ->where('schedule_id', $schedule->schedule_id ?? $schedule->getKey())
                ->whereDate('date', $date)
                ->first();

            $currentMax = $over ? (int) $over->max_capacity : $baseMax;

            // Nuevo max para la fecha
            $newMax = max(0, $currentMax + $amount);

            DB::table('schedule_capacity_overrides')->upsert([
                [
                    'schedule_id'  => $schedule->schedule_id ?? $schedule->getKey(),
                    'date'         => $date,
                    'max_capacity' => $newMax,
                    'reason'       => 'manual_increase',
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]
            ], ['schedule_id', 'date'], ['max_capacity', 'reason', 'updated_at']);

            $used = $this->usedSeats($schedule, $date);
            $remaining = max(0, $newMax - $used);
            $pct = $newMax > 0 ? (int) floor(($used * 100) / $newMax) : 100;

            DB::commit();

            return response()->json([
                'ok'           => true,
                'used'         => $used,
                'max_capacity' => $newMax,
                'remaining'    => $remaining,
                'pct'          => $pct,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'ok'      => false,
                'message' => 'No se pudo actualizar la capacidad',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /* ===========================
     * Detalles de ocupación (lista)
     * Query: ?date=Y-m-d (opcional; si llega, devolver solo esa fecha)
     * Respuesta: { ok, data:[{date,tour,used,max,remaining,pct}], max_capacity?:int }
     * =========================== */
    public function show(Schedule $schedule, Request $request)
    {
        $tz = config('app.timezone', 'UTC');

        // Rango por defecto: hoy -> +30d
        $date = $request->query('date');
        if ($date) {
            try { $date = $this->normalizeDate($date); }
            catch (\Throwable $e) {
                return response()->json(['ok' => false, 'message' => 'Fecha inválida', 'error' => $e->getMessage()], 422);
            }
            $start = $date;
            $end   = $date;
        } else {
            $start = Carbon::now($tz)->toDateString();
            $end   = Carbon::now($tz)->addDays(30)->toDateString();
        }

        // Capacidad base del schedule
        $baseMax = (int) ($schedule->max_capacity ?? 0);
        $scheduleId = $schedule->schedule_id ?? $schedule->getKey();

        // Sumar reservas por fecha para el schedule
        $rows = DB::table('booking_details as d')
            ->leftJoin('bookings as b', 'b.booking_id', '=', 'd.booking_id')
            ->leftJoin('tours as t', 't.tour_id', '=', 'd.tour_id')
            ->where('d.schedule_id', $scheduleId)
            ->whereDate('d.tour_date', '>=', $start)
            ->whereDate('d.tour_date', '<=', $end)
            ->whereIn('b.status', ['confirmed', 'paid'])
            ->groupBy('d.tour_date', 't.name')
            ->orderBy('d.tour_date')
            ->select([
                DB::raw('DATE(d.tour_date) as date'),
                't.name as tour',
                DB::raw('SUM(COALESCE(d.adults_quantity,0)+COALESCE(d.kids_quantity,0)) as used'),
            ])
            ->get();

        // Traer overrides de capacidad en el rango
        $overs = [];
        if (Schema::hasTable('schedule_capacity_overrides')) {
            $overs = DB::table('schedule_capacity_overrides')
                ->where('schedule_id', $scheduleId)
                ->whereDate('date', '>=', $start)
                ->whereDate('date', '<=', $end)
                ->pluck('max_capacity', 'date')
                ->toArray();
        }

        $data = $rows->map(function ($r) use ($baseMax, $overs) {
            $date = (string) $r->date;
            $used = (int) $r->used;
            $max  = isset($overs[$date]) ? (int) $overs[$date] : $baseMax;
            $remaining = max(0, $max - $used);
            $pct = $max > 0 ? (int) floor(($used * 100) / $max) : 100;

            return [
                'date'      => $date,
                'tour'      => $r->tour ?? '—',
                'used'      => $used,
                'max'       => $max,
                'remaining' => $remaining,
                'pct'       => $pct,
            ];
        })->values();

        // Si viene ?date=, añadimos fila aunque no existan reservas, para que el modal muestre algo.
        if ($date && $data->isEmpty()) {
            $max = isset($overs[$date]) ? (int) $overs[$date] : $baseMax;
            $data = collect([[
                'date'      => $date,
                'tour'      => '—',
                'used'      => 0,
                'max'       => $max,
                'remaining' => $max,
                'pct'       => 0,
            ]]);
        }

        // Compatibilidad con tu JS actual (usa res.max_capacity a veces)
        // Si se pidió una fecha concreta, mandamos el max efectivo de esa fecha; si no, el base.
        $effectiveMax = $baseMax;
        if ($date) {
            $effectiveMax = isset($overs[$date]) ? (int) $overs[$date] : $baseMax;
        }

        return response()->json([
            'ok'           => true,
            'data'         => $data,
            'max_capacity' => $effectiveMax,
        ]);
    }

    /* ===========================
     * Bloquear fecha completa (capacidad = 0)
     * Usa tour_excluded_dates si existe; si no, override a 0.
     * Respuesta: { ok, used, max_capacity:0, remaining:0, pct:100 }
     * =========================== */
    public function block(Schedule $schedule, Request $request)
    {
        $dateIn = (string) $request->input('date');
        if (!$dateIn) {
            return response()->json(['ok' => false, 'message' => 'Fecha requerida'], 422);
        }

        try {
            $date = $this->normalizeDate($dateIn);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'message' => 'Fecha inválida', 'error' => $e->getMessage()], 422);
        }

        DB::beginTransaction();
        try {
            $blockedOk = false;

            // 1) Si existe el módulo de EXCLUDED DATES, úsalo para bloquear TODOS los tours asignados al schedule
            if (Schema::hasTable('tour_excluded_dates')) {
                $tourIds = $this->scheduleTourIds($schedule);
                if ($tourIds->isNotEmpty()) {
                    $rows = $tourIds->map(fn ($tid) => [
                        'tour_id'    => $tid,
                        'date'       => $date,
                        'is_active'  => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ])->all();

                    // Intento con upsert (si hay unique en (tour_id, date))
                    try {
                        DB::table('tour_excluded_dates')->upsert(
                            $rows,
                            ['tour_id', 'date'],
                            ['is_active', 'updated_at']
                        );
                    } catch (\Throwable $e) {
                        // Fallback: inserción sin duplicados
                        foreach ($rows as $r) {
                            $exists = DB::table('tour_excluded_dates')
                                ->where('tour_id', $r['tour_id'])
                                ->whereDate('date', $r['date'])
                                ->exists();
                            if (!$exists) {
                                DB::table('tour_excluded_dates')->insert($r);
                            }
                        }
                    }
                    $blockedOk = true;
                }
            }

            // 2) Fallback: override a 0 por fecha (sin migración; crea tabla si no existe)
            if (!$blockedOk) {
                $this->ensureOverridesTable();

                DB::table('schedule_capacity_overrides')->upsert([
                    [
                        'schedule_id'  => $schedule->schedule_id ?? $schedule->getKey(),
                        'date'         => $date,
                        'max_capacity' => 0,
                        'reason'       => 'blocked_by_admin',
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]
                ], ['schedule_id', 'date'], ['max_capacity', 'reason', 'updated_at']);
            }

            $used = $this->usedSeats($schedule, $date);
            DB::commit();

            return response()->json([
                'ok'           => true,
                'used'         => $used,
                'max_capacity' => 0,
                'remaining'    => 0,
                'pct'          => 100,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'ok'      => false,
                'message' => 'No se pudo bloquear la fecha',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /* ===========================
     * Helpers
     * =========================== */

    /** Normaliza "Y-m-d" o "d/m/Y" a Y-m-d */
    private function normalizeDate(string $input): string
    {
        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $input)) {
            return Carbon::createFromFormat('d/m/Y', $input)->toDateString();
        }
        return Carbon::parse($input)->toDateString();
    }

    /** Seats usados (adults+kids) para un schedule y fecha */
    private function usedSeats(Schedule $schedule, string $date): int
    {
        return (int) DB::table('booking_details as d')
            ->join('bookings as b', 'b.booking_id', '=', 'd.booking_id')
            ->where('d.schedule_id', $schedule->schedule_id ?? $schedule->getKey())
            ->whereDate('d.tour_date', $date)
            ->whereIn('b.status', ['confirmed', 'paid'])
            ->sum(DB::raw('COALESCE(d.adults_quantity,0)+COALESCE(d.kids_quantity,0)'));
    }

    /** Garantiza existencia de la tabla de overrides (sin migración manual) */
    private function ensureOverridesTable(): void
    {
        if (!Schema::hasTable('schedule_capacity_overrides')) {
            Schema::create('schedule_capacity_overrides', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('schedule_id')->index();
                $table->date('date')->index();
                $table->unsignedInteger('max_capacity')->default(0);
                $table->string('reason')->nullable();
                $table->timestamps();
                $table->unique(['schedule_id', 'date'], 'sco_schedule_date_unique');
            });
        }
    }

    /** IDs de tour asignados al schedule (detecta nombre real del pivot) */
    private function scheduleTourIds(Schedule $schedule)
    {
        $sid = $schedule->schedule_id ?? $schedule->getKey();
        $candidates = ['tour_schedule', 'tour_tour_schedule'];

        foreach ($candidates as $table) {
            if (Schema::hasTable($table)) {
                try {
                    $ids = DB::table($table)
                        ->where('schedule_id', $sid)
                        ->pluck('tour_id');
                    if ($ids->count() > 0) {
                        return $ids->unique()->values();
                    }
                } catch (\Throwable $e) {
                    // Ignorar y probar siguiente
                }
            }
        }
        return collect();
    }
}
