<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\TourLanguage;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        // ====== Inputs & defaults ======
        $groupBy = $request->string('group_by')->isNotEmpty()
            ? $request->string('group_by')->toString()
            : 'booking_date'; // booking_date | tour_date

        $period  = $request->string('period')->isNotEmpty()
            ? $request->string('period')->toString()
            : 'month'; // day | week | month

        $from = $request->filled('from')
            ? Carbon::parse($request->input('from'))->startOfDay()
            : Carbon::now()->copy()->startOfYear();

        $to = $request->filled('to')
            ? Carbon::parse($request->input('to'))->endOfDay()
            : Carbon::now()->endOfDay();

        $status = $request->input('status'); // paid|confirmed|completed|cancelled|null

        // Filtros múltiples
        $tourIds = collect((array) $request->input('tour_id', []))->filter()->map(fn($v)=>(int)$v)->values()->all();
        $langIds = collect((array) $request->input('tour_language_id', []))->filter()->map(fn($v)=>(int)$v)->values()->all();

        // ====== Catálogos para selects ======
        $toursMap = Tour::pluck('name', 'tour_id');
        $langsMap = TourLanguage::pluck('name', 'tour_language_id');

        // ====== KPIs ======
        $factsQ = $this->baseFactsQuery($from, $to, $groupBy, $status, $tourIds, $langIds);

        $kpisRow = (clone $factsQ)->selectRaw("
                COALESCE(SUM(detail_total),0) AS revenue,
                COUNT(DISTINCT booking_id)    AS bookings,
                COALESCE(SUM(adults_qty + kids_qty),0) AS pax
            ")->first();

        $kpis = [
            'revenue'  => (float) ($kpisRow->revenue ?? 0),
            'bookings' => (int)   ($kpisRow->bookings ?? 0),
            'pax'      => (int)   ($kpisRow->pax ?? 0),
            'atv'      => ($kpisRow->bookings ?? 0) ? round($kpisRow->revenue / max(1, $kpisRow->bookings), 2) : 0,
        ];

        // ====== Top Tours (por ingresos) ======
        $topTours = (clone $factsQ)
            ->selectRaw("tour_id,
                         SUM(detail_total) AS revenue,
                         COUNT(DISTINCT booking_id) AS bookings,
                         SUM(adults_qty + kids_qty) AS pax")
            ->when(!empty($tourIds), fn($q)=>$q->whereIn('tour_id', $tourIds))
            ->when(!empty($langIds), fn($q)=>$q->whereIn('tour_language_id', $langIds))
            ->groupBy('tour_id')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        // ====== Confirmadas (sólo estatus 'confirmed' en rango y filtros) ======
        $confirmedBookings = DB::table('bookings as b')
            ->leftJoin('booking_details as bd', 'bd.booking_id', '=', 'b.booking_id')
            ->when($groupBy === 'tour_date',
                fn($q) => $q->whereBetween('bd.tour_date', [$from, $to]),
                fn($q) => $q->whereBetween('b.booking_date', [$from, $to])
            )
            ->where('b.status', 'confirmed')
            ->when(!empty($tourIds), fn($q)=>$q->whereIn('bd.tour_id', $tourIds))
            ->when(!empty($langIds), fn($q)=>$q->whereIn('bd.tour_language_id', $langIds))
            ->distinct('b.booking_id')->count('b.booking_id');

        // ====== Pendientes (widget) ======
        $pendingBase = DB::table('bookings as b')
            ->leftJoin('booking_details as bd', 'bd.booking_id', '=', 'b.booking_id')
            ->leftJoin('tours as t', 't.tour_id', '=', 'bd.tour_id')
            ->leftJoin('users as u', 'u.user_id', '=', 'b.user_id')
            ->where('b.status', 'pending')
            ->when($groupBy === 'tour_date',
                fn($q) => $q->whereBetween('bd.tour_date', [$from, $to]),
                fn($q) => $q->whereBetween('b.booking_date', [$from, $to])
            )
            ->when(!empty($tourIds), fn($q)=>$q->whereIn('bd.tour_id', $tourIds))
            ->when(!empty($langIds), fn($q)=>$q->whereIn('bd.tour_language_id', $langIds));

        $pendingCount = (clone $pendingBase)
            ->distinct('b.booking_id')->count('b.booking_id');

        $pendingItems = (clone $pendingBase)
            ->selectRaw("
                b.booking_id,
                b.booking_reference,
                MIN(bd.tour_date)                        AS tour_date,
                MIN(b.booking_date)                      AS booking_date,
                COALESCE(MIN(b.total),
                         SUM(COALESCE(bd.total, (bd.adult_price*bd.adults_quantity)+(bd.kid_price*bd.kids_quantity)))
                )                                        AS total,
                SUM(COALESCE(bd.adults_quantity,0) + COALESCE(bd.kids_quantity,0)) AS pax,
                MIN(u.email)                             AS customer_email,
                MIN(t.name)                              AS tour_name
            ")
            ->groupBy('b.booking_id','b.booking_reference')
            ->orderByRaw('MIN(b.booking_date) ASC NULLS LAST')
            ->limit(8)
            ->get();

        return view('admin.reports.index', compact(
            'from','to','status','kpis','topTours','toursMap','langsMap',
            'confirmedBookings','groupBy','period',
            'pendingItems','pendingCount'
        ));
    }

    public function chartMonthlySales(Request $request)
    {
        // === inputs ===
        $groupBy = $request->input('group_by', 'booking_date'); // booking_date|tour_date
        $period  = $request->input('period', 'month');          // day|week|month
        $from    = Carbon::parse($request->input('from', now()->copy()->startOfYear()))->startOfDay();
        $to      = Carbon::parse($request->input('to', now()))->endOfDay();
        $status  = $request->input('status');

        $tourIds = collect((array)$request->input('tour_id', []))->filter()->map(fn($v)=>(int)$v)->values()->all();
        $langIds = collect((array)$request->input('tour_language_id', []))->filter()->map(fn($v)=>(int)$v)->values()->all();

        // === base ===
        $factsQ = $this->baseFactsQuery($from, $to, $groupBy, $status, $tourIds, $langIds);

        // === bucket & label SQL (PostgreSQL) ===
        $dateCol = $groupBy === 'tour_date' ? 'tour_date' : 'booking_date';

        if ($period === 'day') {
            $bucketExpr = "to_char({$dateCol}, 'YYYY-MM-DD')";
            $step = 'day';
        } elseif ($period === 'week') {
            // ISO week
            $bucketExpr = "to_char(date_trunc('week', {$dateCol}), 'IYYY-IW')";
            $step = 'week';
        } else { // month
            $bucketExpr = "to_char(date_trunc('month', {$dateCol}), 'YYYY-MM')";
            $step = 'month';
        }

        $rows = (clone $factsQ)
            ->selectRaw("$bucketExpr as bucket,
                         COALESCE(SUM(detail_total),0) AS revenue,
                         COUNT(DISTINCT booking_id)    AS bookings,
                         COALESCE(SUM(adults_qty + kids_qty),0) AS pax")
            ->groupBy('bucket')
            ->orderBy('bucket')
            ->get()
            ->keyBy('bucket');

        // === construir eje completo con ceros ===
        $labels = [];
        $seriesRevenue = [];
        $seriesBookings = [];
        $seriesPax = [];

        $cursorStart = (clone $from);
        if ($step === 'month') $cursorStart->startOfMonth();
        if ($step === 'week')  $cursorStart->startOfWeek(); // ISO (lunes)

        $periodIter = CarbonPeriod::create($cursorStart, "1 {$step}", $to);

        foreach ($periodIter as $d) {
            if ($step === 'day') {
                $label = $d->format('Y-m-d');
            } elseif ($step === 'week') {
                $label = $d->isoFormat('GGGG-[W]WW'); // ej: 2025-W44
            } else {
                $label = $d->format('Y-m');
            }

            // Normalizar label para que coincida con SQL (semana)
            $sqlKey = $label;
            if ($step === 'week') {
                $sqlKey = $d->isoFormat('GGGG-[W]WW'); // coincide con IYYY-IW
            }

            $labels[]        = $label;
            $seriesRevenue[] = isset($rows[$sqlKey]) ? round((float)$rows[$sqlKey]->revenue, 2) : 0;
            $seriesBookings[] = isset($rows[$sqlKey]) ? (int)$rows[$sqlKey]->bookings : 0;
            $seriesPax[]      = isset($rows[$sqlKey]) ? (int)$rows[$sqlKey]->pax : 0;
        }

        return response()->json([
            'labels' => $labels,
            'series' => [
                'revenue'  => $seriesRevenue,
                'bookings' => $seriesBookings,
                'pax'      => $seriesPax,
            ]
        ]);
    }

    public function chartByLanguage(Request $request)
    {
        $groupBy = $request->input('group_by', 'booking_date');
        $from    = Carbon::parse($request->input('from', now()->copy()->startOfYear()))->startOfDay();
        $to      = Carbon::parse($request->input('to', now()))->endOfDay();
        $status  = $request->input('status');

        $tourIds = collect((array)$request->input('tour_id', []))->filter()->map(fn($v)=>(int)$v)->values()->all();
        $langIds = collect((array)$request->input('tour_language_id', []))->filter()->map(fn($v)=>(int)$v)->values()->all();

        $factsQ = $this->baseFactsQuery($from, $to, $groupBy, $status, $tourIds, $langIds);

        $rows = (clone $factsQ)
            ->selectRaw("tour_language_id,
                         COALESCE(SUM(detail_total),0) AS revenue,
                         COUNT(DISTINCT booking_id)    AS bookings")
            ->when(!empty($langIds), fn($q)=>$q->whereIn('tour_language_id', $langIds))
            ->groupBy('tour_language_id')
            ->orderByDesc('revenue')
            ->get();

        return response()->json([
            'labels' => $rows->pluck('tour_language_id'),
            'series' => [
                'revenue'  => $rows->pluck('revenue')->map(fn($v)=>round((float)$v,2)),
                'bookings' => $rows->pluck('bookings')->map(fn($v)=>(int)$v),
            ]
        ]);
    }

    /**
     * Base query sobre v_booking_facts con filtros comunes.
     */
    private function baseFactsQuery(Carbon $from, Carbon $to, string $groupBy, ?string $status, array $tourIds, array $langIds)
    {
        $dateCol = $groupBy === 'tour_date' ? 'tour_date' : 'booking_date';

        return DB::table('v_booking_facts')
            ->when($status, fn($q)=>$q->where('status', $status))
            ->when(!empty($tourIds), fn($q)=>$q->whereIn('tour_id', $tourIds))
            ->when(!empty($langIds), fn($q)=>$q->whereIn('tour_language_id', $langIds))
            ->whereBetween($dateCol, [$from, $to]);
    }
}
