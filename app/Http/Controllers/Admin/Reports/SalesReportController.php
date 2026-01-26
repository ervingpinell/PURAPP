<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Product;
use App\Models\TourLanguage;
use App\Models\Payment;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * SalesReportController
 *
 * Handles sales analytics and revenue reports
 */
class SalesReportController extends Controller
{
    public function index(Request $request)
    {
        // ====== Inputs & defaults ======
        $from = $request->filled('from')
            ? Carbon::parse($request->input('from'))->startOfDay()
            : Carbon::now()->copy()->startOfMonth();

        $to = $request->filled('to')
            ? Carbon::parse($request->input('to'))->endOfDay()
            : Carbon::now()->endOfDay();

        $status = $request->input('status');
        $tourIds = collect((array) $request->input('product_id', []))->filter()->map(fn($v) => (int)$v)->values()->all();
        $langIds = collect((array) $request->input('tour_language_id', []))->filter()->map(fn($v) => (int)$v)->values()->all();

        // ====== Catalogs ======
        $toursMap = Product::pluck('name', 'product_id');
        $langsMap = TourLanguage::pluck('name', 'tour_language_id');

        // ====== Base Query ======
        $baseQuery = $this->buildBaseQuery($from, $to, $status, $tourIds, $langIds);

        // ====== KPIs ======
        $kpisData = (clone $baseQuery)
            ->selectRaw('
                SUM(booking_details.total) as revenue,
                COUNT(DISTINCT bookings.booking_id) as bookings,
                AVG(booking_details.total) as avg_booking_value
            ')
            ->first();

        // Revenue by payment gateway
        $revenueByPaymentMethod = Payment::query()
            ->join('bookings', 'bookings.booking_id', '=', 'payments.booking_id')
            ->whereBetween('payments.created_at', [$from, $to])
            ->whereIn('payments.status', ['paid', 'completed'])
            ->when($status, fn($q) => $q->where('bookings.status', $status))
            ->when(!empty($tourIds), function ($q) use ($tourIds) {
                $q->whereHas('booking.details', fn($q2) => $q2->whereIn('product_id', $tourIds));
            })
            ->selectRaw('
                payments.gateway as payment_method,
                SUM(payments.amount) as total_revenue,
                COUNT(DISTINCT payments.payment_id) as payment_count
            ')
            ->groupBy('payments.gateway')
            ->get();

        // Revenue by language
        $revenueByLanguage = (clone $baseQuery)
            ->join('tour_languages', 'tour_languages.tour_language_id', '=', 'booking_details.tour_language_id')
            ->selectRaw('
                tour_languages.name as language_name,
                SUM(booking_details.total) as revenue,
                COUNT(DISTINCT bookings.booking_id) as bookings
            ')
            ->groupBy('tour_languages.tour_language_id', 'tour_languages.name')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        // Revenue by status
        $revenueByStatus = BookingDetail::query()
            ->join('bookings', 'bookings.booking_id', '=', 'booking_details.booking_id')
            ->whereBetween('bookings.created_at', [$from, $to])
            ->when(!empty($tourIds), fn($q) => $q->whereIn('booking_details.product_id', $tourIds))
            ->when(!empty($langIds), fn($q) => $q->whereIn('booking_details.tour_language_id', $langIds))
            ->selectRaw('
                bookings.status,
                SUM(booking_details.total) as revenue,
                COUNT(DISTINCT bookings.booking_id) as bookings
            ')
            ->groupBy('bookings.status')
            ->get();

        $kpis = [
            'total_revenue' => (float) ($kpisData->revenue ?? 0),
            'total_bookings' => (int) ($kpisData->bookings ?? 0),
            'avg_booking_value' => (float) ($kpisData->avg_booking_value ?? 0),
            'payment_methods_count' => $revenueByPaymentMethod->count(),
        ];

        return view('admin.reports.sales', compact(
            'from',
            'to',
            'status',
            'kpis',
            'revenueByPaymentMethod',
            'revenueByLanguage',
            'revenueByStatus',
            'toursMap',
            'langsMap'
        ));
    }

    /**
     * Chart: Revenue trend over time
     */
    public function chartRevenueTrend(Request $request)
    {
        $period = $request->input('period', 'day'); // day, week, month
        $from = Carbon::parse($request->input('from', now()->copy()->startOfMonth()))->startOfDay();
        $to = Carbon::parse($request->input('to', now()))->endOfDay();
        $status = $request->input('status');
        $tourIds = collect((array)$request->input('product_id', []))->filter()->map(fn($v) => (int)$v)->values()->all();
        $langIds = collect((array)$request->input('tour_language_id', []))->filter()->map(fn($v) => (int)$v)->values()->all();

        $baseQuery = $this->buildBaseQuery($from, $to, $status, $tourIds, $langIds);

        $dateFormat = match ($period) {
            'day' => 'Y-m-d',
            'week' => 'o-W',
            'month' => 'Y-m',
            default => 'Y-m-d',
        };

        $rawData = (clone $baseQuery)
            ->selectRaw("
                DATE_FORMAT(bookings.created_at, ?) as bucket,
                SUM(booking_details.total) as revenue,
                COUNT(DISTINCT bookings.booking_id) as bookings
            ", [$this->getMySQLDateFormat($period)])
            ->groupBy('bucket')
            ->orderBy('bucket')
            ->get()
            ->keyBy('bucket');

        // Build complete axis
        $labels = [];
        $seriesRevenue = [];
        $seriesBookings = [];

        $step = $period === 'week' ? 'week' : ($period === 'month' ? 'month' : 'day');
        $cursorStart = (clone $from);
        if ($step === 'month') $cursorStart->startOfMonth();
        if ($step === 'week') $cursorStart->startOfWeek();

        $periodIter = CarbonPeriod::create($cursorStart, "1 {$step}", $to);

        foreach ($periodIter as $d) {
            $label = $d->format($dateFormat);
            $labels[] = $label;

            $seriesRevenue[] = isset($rawData[$label]) ? round((float)$rawData[$label]->revenue, 2) : 0;
            $seriesBookings[] = isset($rawData[$label]) ? (int)$rawData[$label]->bookings : 0;
        }

        return response()->json([
            'labels' => $labels,
            'series' => [
                'revenue' => $seriesRevenue,
                'bookings' => $seriesBookings,
            ]
        ]);
    }

    /**
     * Chart: Revenue by payment method (pie chart)
     */
    public function chartRevenueByPaymentMethod(Request $request)
    {
        $from = Carbon::parse($request->input('from', now()->copy()->startOfMonth()))->startOfDay();
        $to = Carbon::parse($request->input('to', now()))->endOfDay();

        $data = Payment::query()
            ->whereBetween('created_at', [$from, $to])
            ->whereIn('status', ['paid', 'completed'])
            ->selectRaw('
                gateway as payment_method,
                SUM(amount) as total_revenue,
                COUNT(*) as payment_count
            ')
            ->groupBy('gateway')
            ->orderByDesc('total_revenue')
            ->get();

        return response()->json([
            'labels' => $data->pluck('payment_method')->map(fn($m) => ucfirst($m ?? 'Unknown')),
            'revenue' => $data->pluck('total_revenue')->map(fn($v) => round((float)$v, 2)),
            'count' => $data->pluck('payment_count'),
        ]);
    }

    /**
     * Chart: Revenue by language (bar chart)
     */
    public function chartRevenueByLanguage(Request $request)
    {
        $from = Carbon::parse($request->input('from', now()->copy()->startOfMonth()))->startOfDay();
        $to = Carbon::parse($request->input('to', now()))->endOfDay();
        $status = $request->input('status');
        $tourIds = collect((array)$request->input('product_id', []))->filter()->map(fn($v) => (int)$v)->values()->all();

        $baseQuery = $this->buildBaseQuery($from, $to, $status, $tourIds, []);

        $data = (clone $baseQuery)
            ->join('tour_languages', 'tour_languages.tour_language_id', '=', 'booking_details.tour_language_id')
            ->selectRaw('
                tour_languages.name as language_name,
                SUM(booking_details.total) as revenue,
                COUNT(DISTINCT bookings.booking_id) as bookings
            ')
            ->groupBy('tour_languages.tour_language_id', 'tour_languages.name')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        return response()->json([
            'labels' => $data->pluck('language_name'),
            'revenue' => $data->pluck('revenue')->map(fn($v) => round((float)$v, 2)),
            'bookings' => $data->pluck('bookings'),
        ]);
    }

    /**
     * Chart: Daily revenue comparison (current vs previous period)
     */
    public function chartDailyComparison(Request $request)
    {
        $from = Carbon::parse($request->input('from', now()->copy()->startOfMonth()))->startOfDay();
        $to = Carbon::parse($request->input('to', now()))->endOfDay();

        $days = $from->diffInDays($to) + 1;
        $previousFrom = (clone $from)->subDays($days);
        $previousTo = (clone $from)->subDay()->endOfDay();

        // Current period
        $currentData = BookingDetail::query()
            ->join('bookings', 'bookings.booking_id', '=', 'booking_details.booking_id')
            ->whereBetween('bookings.created_at', [$from, $to])
            ->whereIn('bookings.status', ['paid', 'completed'])
            ->selectRaw('
                DATE(bookings.created_at) as date,
                SUM(booking_details.total) as revenue
            ')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Previous period
        $previousData = BookingDetail::query()
            ->join('bookings', 'bookings.booking_id', '=', 'booking_details.booking_id')
            ->whereBetween('bookings.created_at', [$previousFrom, $previousTo])
            ->whereIn('bookings.status', ['paid', 'completed'])
            ->selectRaw('
                DATE(bookings.created_at) as date,
                SUM(booking_details.total) as revenue
            ')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $labels = [];
        $currentSeries = [];
        $previousSeries = [];

        $period = CarbonPeriod::create($from, '1 day', $to);
        $previousPeriod = CarbonPeriod::create($previousFrom, '1 day', $previousTo);

        foreach ($period as $index => $date) {
            $dateStr = $date->format('Y-m-d');
            $labels[] = $date->format('M d');
            $currentSeries[] = isset($currentData[$dateStr]) ? round((float)$currentData[$dateStr]->revenue, 2) : 0;
        }

        foreach ($previousPeriod as $date) {
            $dateStr = $date->format('Y-m-d');
            $previousSeries[] = isset($previousData[$dateStr]) ? round((float)$previousData[$dateStr]->revenue, 2) : 0;
        }

        return response()->json([
            'labels' => $labels,
            'current' => $currentSeries,
            'previous' => $previousSeries,
        ]);
    }

    /**
     * Build base query for sales reports
     */
    private function buildBaseQuery(Carbon $from, Carbon $to, ?string $status, array $tourIds, array $langIds)
    {
        $query = BookingDetail::query()
            ->join('bookings', 'bookings.booking_id', '=', 'booking_details.booking_id')
            ->join('tours', 'tours.product_id', '=', 'booking_details.product_id')
            ->whereBetween('bookings.created_at', [$from, $to]);

        if ($status) {
            $query->where('bookings.status', $status);
        }

        if (!empty($tourIds)) {
            $query->whereIn('booking_details.product_id', $tourIds);
        }

        if (!empty($langIds)) {
            $query->whereIn('booking_details.tour_language_id', $langIds);
        }

        return $query;
    }

    /**
     * Get MySQL date format string for period
     */
    private function getMySQLDateFormat(string $period): string
    {
        return match ($period) {
            'day' => '%Y-%m-%d',
            'week' => '%x-%v',
            'month' => '%Y-%m',
            default => '%Y-%m-%d',
        };
    }
}
