<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Product;
use App\Models\TourType;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * ToursReportController
 *
 * Handles tour performance analytics and reports
 */
class ProductsReportController extends Controller
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

        // ====== Base Query ======
        $baseQuery = BookingDetail::query()
            ->join('bookings', 'bookings.booking_id', '=', 'booking_details.booking_id')
            ->join('tours', 'tours.product_id', '=', 'booking_details.product_id')
            ->whereBetween('bookings.created_at', [$from, $to])
            ->when($status, fn($q) => $q->where('bookings.status', $status))
            ->when(!empty($tourIds), fn($q) => $q->whereIn('booking_details.product_id', $tourIds));

        // ====== KPIs ======
        $totalTours = Product::where('is_active', true)->count();

        $topToursByRevenue = (clone $baseQuery)
            ->selectRaw('
                tours.product_id,
                tours.name as tour_name,
                SUM(booking_details.total) as revenue,
                COUNT(DISTINCT bookings.booking_id) as bookings
            ')
            ->groupBy('tours.product_id', 'tours.name')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        $topToursByBookings = (clone $baseQuery)
            ->selectRaw('
                tours.product_id,
                tours.name as tour_name,
                COUNT(DISTINCT bookings.booking_id) as bookings,
                SUM(booking_details.total) as revenue
            ')
            ->groupBy('tours.product_id', 'tours.name')
            ->orderByDesc('bookings')
            ->limit(10)
            ->get();

        // Tours by type
        $toursByType = (clone $baseQuery)
            ->join('tour_types', 'tour_types.tour_type_id', '=', 'tours.tour_type_id')
            ->selectRaw('
                tour_types.tour_type_id,
                COUNT(DISTINCT tours.product_id) as tour_count,
                COUNT(DISTINCT bookings.booking_id) as bookings,
                SUM(booking_details.total) as revenue
            ')
            ->groupBy('tour_types.tour_type_id')
            ->get();

        // Calculate PAX for top tours
        $topToursByRevenue = $topToursByRevenue->map(function ($tour) use ($from, $to, $status) {
            $details = BookingDetail::query()
                ->join('bookings', 'bookings.booking_id', '=', 'booking_details.booking_id')
                ->where('booking_details.product_id', $tour->product_id)
                ->whereBetween('bookings.created_at', [$from, $to])
                ->when($status, fn($q) => $q->where('bookings.status', $status))
                ->select('booking_details.categories')
                ->get();

            $tour->pax = $details->sum(function ($detail) {
                $cats = is_string($detail->categories) ? json_decode($detail->categories, true) : $detail->categories;
                return collect($cats ?? [])->sum(fn($c) => (int)($c['quantity'] ?? 0));
            });
            return $tour;
        });

        $kpis = [
            'total_active_tours' => $totalTours,
            'tours_with_bookings' => $topToursByRevenue->count(),
            'most_profitable_tour' => $topToursByRevenue->first()?->tour_name ?? 'N/A',
            'most_booked_tour' => $topToursByBookings->first()?->tour_name ?? 'N/A',
        ];

        return view('admin.reports.tours', compact(
            'from',
            'to',
            'status',
            'kpis',
            'topToursByRevenue',
            'topToursByBookings',
            'toursByType'
        ));
    }

    /**
     * Chart: Top tours by revenue (horizontal bar chart)
     */
    public function chartTopToursByRevenue(Request $request)
    {
        $from = Carbon::parse($request->input('from', now()->copy()->startOfMonth()))->startOfDay();
        $to = Carbon::parse($request->input('to', now()))->endOfDay();
        $status = $request->input('status');
        $limit = (int) $request->input('limit', 20);

        $data = BookingDetail::query()
            ->join('bookings', 'bookings.booking_id', '=', 'booking_details.booking_id')
            ->join('tours', 'tours.product_id', '=', 'booking_details.product_id')
            ->whereBetween('bookings.created_at', [$from, $to])
            ->when($status, fn($q) => $q->where('bookings.status', $status))
            ->selectRaw('
                tours.name as tour_name,
                SUM(booking_details.total) as revenue,
                COUNT(DISTINCT bookings.booking_id) as bookings
            ')
            ->groupBy('tours.product_id', 'tours.name')
            ->orderByDesc('revenue')
            ->limit($limit)
            ->get();

        return response()->json([
            'labels' => $data->pluck('tour_name'),
            'revenue' => $data->pluck('revenue')->map(fn($v) => round((float)$v, 2)),
            'bookings' => $data->pluck('bookings'),
        ]);
    }

    /**
     * Chart: Top tours by bookings (horizontal bar chart)
     */
    public function chartTopToursByBookings(Request $request)
    {
        $from = Carbon::parse($request->input('from', now()->copy()->startOfMonth()))->startOfDay();
        $to = Carbon::parse($request->input('to', now()))->endOfDay();
        $status = $request->input('status');
        $limit = (int) $request->input('limit', 20);

        $data = BookingDetail::query()
            ->join('bookings', 'bookings.booking_id', '=', 'booking_details.booking_id')
            ->join('tours', 'tours.product_id', '=', 'booking_details.product_id')
            ->whereBetween('bookings.created_at', [$from, $to])
            ->when($status, fn($q) => $q->where('bookings.status', $status))
            ->selectRaw('
                tours.name as tour_name,
                COUNT(DISTINCT bookings.booking_id) as bookings,
                SUM(booking_details.total) as revenue
            ')
            ->groupBy('tours.product_id', 'tours.name')
            ->orderByDesc('bookings')
            ->limit($limit)
            ->get();

        return response()->json([
            'labels' => $data->pluck('tour_name'),
            'bookings' => $data->pluck('bookings'),
            'revenue' => $data->pluck('revenue')->map(fn($v) => round((float)$v, 2)),
        ]);
    }

    /**
     * Chart: Tour performance matrix (scatter plot: revenue vs bookings)
     */
    public function chartTourPerformanceMatrix(Request $request)
    {
        $from = Carbon::parse($request->input('from', now()->copy()->startOfMonth()))->startOfDay();
        $to = Carbon::parse($request->input('to', now()))->endOfDay();
        $status = $request->input('status');

        $data = BookingDetail::query()
            ->join('bookings', 'bookings.booking_id', '=', 'booking_details.booking_id')
            ->join('tours', 'tours.product_id', '=', 'booking_details.product_id')
            ->whereBetween('bookings.created_at', [$from, $to])
            ->when($status, fn($q) => $q->where('bookings.status', $status))
            ->selectRaw('
                tours.product_id,
                tours.name as tour_name,
                SUM(booking_details.total) as revenue,
                COUNT(DISTINCT bookings.booking_id) as bookings
            ')
            ->groupBy('tours.product_id', 'tours.name')
            ->having('bookings', '>', 0)
            ->get();

        return response()->json([
            'data' => $data->map(fn($tour) => [
                'x' => (int) $tour->bookings,
                'y' => round((float) $tour->revenue, 2),
                'label' => $tour->tour_name,
            ]),
        ]);
    }

    /**
     * Chart: Bookings by tour type (donut chart)
     */
    public function chartBookingsByTourType(Request $request)
    {
        $from = Carbon::parse($request->input('from', now()->copy()->startOfMonth()))->startOfDay();
        $to = Carbon::parse($request->input('to', now()))->endOfDay();
        $status = $request->input('status');

        $data = BookingDetail::query()
            ->join('bookings', 'bookings.booking_id', '=', 'booking_details.booking_id')
            ->join('tours', 'tours.product_id', '=', 'booking_details.product_id')
            ->join('tour_types', 'tour_types.tour_type_id', '=', 'tours.tour_type_id')
            ->whereBetween('bookings.created_at', [$from, $to])
            ->when($status, fn($q) => $q->where('bookings.status', $status))
            ->selectRaw('
                tour_types.tour_type_id,
                COUNT(DISTINCT bookings.booking_id) as bookings,
                SUM(booking_details.total) as revenue
            ')
            ->groupBy('tour_types.tour_type_id')
            ->orderByDesc('bookings')
            ->get();

        // Get type names with translations
        $typeNames = TourType::query()
            ->get()
            ->mapWithKeys(fn($type) => [$type->tour_type_id => $type->translated]);

        return response()->json([
            'labels' => $data->map(fn($item) => $typeNames[$item->tour_type_id] ?? "Type #{$item->tour_type_id}"),
            'bookings' => $data->pluck('bookings'),
            'revenue' => $data->pluck('revenue')->map(fn($v) => round((float)$v, 2)),
        ]);
    }

    /**
     * Chart: Capacity utilization by tour
     */
    public function chartCapacityUtilization(Request $request)
    {
        $from = Carbon::parse($request->input('from', now()->copy()->startOfMonth()))->startOfDay();
        $to = Carbon::parse($request->input('to', now()))->endOfDay();
        $limit = (int) $request->input('limit', 15);

        // Get bookings with PAX
        $tourBookings = BookingDetail::query()
            ->join('bookings', 'bookings.booking_id', '=', 'booking_details.booking_id')
            ->join('tours', 'tours.product_id', '=', 'booking_details.product_id')
            ->whereBetween('bookings.created_at', [$from, $to])
            ->whereIn('bookings.status', ['confirmed', 'paid', 'completed'])
            ->select('tours.product_id', 'tours.name as tour_name', 'tours.max_capacity', 'booking_details.categories')
            ->get()
            ->groupBy('product_id');

        $utilizationData = $tourBookings->map(function ($bookings, $tourId) {
            $tour = $bookings->first();
            $maxCapacity = $tour->max_capacity ?? 0;

            $totalPax = $bookings->sum(function ($detail) {
                $cats = is_string($detail->categories) ? json_decode($detail->categories, true) : $detail->categories;
                return collect($cats ?? [])->sum(fn($c) => (int)($c['quantity'] ?? 0));
            });

            $utilization = $maxCapacity > 0 ? ($totalPax / $maxCapacity) * 100 : 0;

            return [
                'tour_name' => $tour->tour_name,
                'utilization' => round($utilization, 2),
                'booked_pax' => $totalPax,
                'max_capacity' => $maxCapacity,
            ];
        })->sortByDesc('utilization')->take($limit)->values();

        return response()->json([
            'labels' => $utilizationData->pluck('tour_name'),
            'utilization' => $utilizationData->pluck('utilization'),
            'booked_pax' => $utilizationData->pluck('booked_pax'),
            'max_capacity' => $utilizationData->pluck('max_capacity'),
        ]);
    }
}
