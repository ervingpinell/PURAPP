<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * ProductsReportController
 *
 * Handles product performance analytics and reports
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
        $productIds = collect((array) $request->input('product_id', []))->filter()->map(fn($v) => (int)$v)->values()->all();

        // ====== Base Query ======
        $baseQuery = BookingDetail::query()
            ->join('bookings', 'bookings.booking_id', '=', 'booking_details.booking_id')
            ->join('products', 'products.product_id', '=', 'booking_details.product_id')
            ->whereBetween('bookings.created_at', [$from, $to])
            ->when($status, fn($q) => $q->where('bookings.status', $status))
            ->when(!empty($productIds), fn($q) => $q->whereIn('booking_details.product_id', $productIds));

        // ====== KPIs ======
        $totalProducts = Product::where('is_active', true)->count();

        $topProductsByRevenue = (clone $baseQuery)
            ->selectRaw('
                products.product_id,
                products.name as product_name,
                SUM(booking_details.total) as revenue,
                COUNT(DISTINCT bookings.booking_id) as bookings
            ')
            ->groupBy('products.product_id', 'products.name')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        $topProductsByBookings = (clone $baseQuery)
            ->selectRaw('
                products.product_id,
                products.name as product_name,
                COUNT(DISTINCT bookings.booking_id) as bookings,
                SUM(booking_details.total) as revenue
            ')
            ->groupBy('products.product_id', 'products.name')
            ->orderByDesc('bookings')
            ->limit(10)
            ->get();

        // Products by type
        $productsByType = (clone $baseQuery)
            ->join('product_types', 'product_types.product_type_id', '=', 'products.product_type_id')
            ->selectRaw('
                product_types.product_type_id,
                COUNT(DISTINCT products.product_id) as product_count,
                COUNT(DISTINCT bookings.booking_id) as bookings,
                SUM(booking_details.total) as revenue
            ')
            ->groupBy('product_types.product_type_id')
            ->get();

        // Calculate PAX for top products
        $topProductsByRevenue = $topProductsByRevenue->map(function ($product) use ($from, $to, $status) {
            $details = BookingDetail::query()
                ->join('bookings', 'bookings.booking_id', '=', 'booking_details.booking_id')
                ->where('booking_details.product_id', $product->product_id)
                ->whereBetween('bookings.created_at', [$from, $to])
                ->when($status, fn($q) => $q->where('bookings.status', $status))
                ->select('booking_details.categories')
                ->get();

            $product->pax = $details->sum(function ($detail) {
                $cats = is_string($detail->categories) ? json_decode($detail->categories, true) : $detail->categories;
                return collect($cats ?? [])->sum(fn($c) => (int)($c['quantity'] ?? 0));
            });
            return $product;
        });

        $kpis = [
            'total_active_products' => $totalProducts,
            'products_with_bookings' => $topProductsByRevenue->count(),
            'most_profitable_product' => $topProductsByRevenue->first()?->product_name ?? 'N/A',
            'most_booked_product' => $topProductsByBookings->first()?->product_name ?? 'N/A',
        ];

        return view('admin.reports.products', compact(
            'from',
            'to',
            'status',
            'kpis',
            'topProductsByRevenue',
            'topProductsByBookings',
            'productsByType'
        ));
    }

    /**
     * Chart: Top products by revenue (horizontal bar chart)
     */
    public function chartTopProductsByRevenue(Request $request)
    {
        $from = Carbon::parse($request->input('from', now()->copy()->startOfMonth()))->startOfDay();
        $to = Carbon::parse($request->input('to', now()))->endOfDay();
        $status = $request->input('status');
        $limit = (int) $request->input('limit', 20);

        $data = BookingDetail::query()
            ->join('bookings', 'bookings.booking_id', '=', 'booking_details.booking_id')
            ->join('products', 'products.product_id', '=', 'booking_details.product_id')
            ->whereBetween('bookings.created_at', [$from, $to])
            ->when($status, fn($q) => $q->where('bookings.status', $status))
            ->selectRaw('
                products.name as product_name,
                SUM(booking_details.total) as revenue,
                COUNT(DISTINCT bookings.booking_id) as bookings
            ')
            ->groupBy('products.product_id', 'products.name')
            ->orderByDesc('revenue')
            ->limit($limit)
            ->get();

        return response()->json([
            'labels' => $data->pluck('product_name'),
            'revenue' => $data->pluck('revenue')->map(fn($v) => round((float)$v, 2)),
            'bookings' => $data->pluck('bookings'),
        ]);
    }

    /**
     * Chart: Top products by bookings (horizontal bar chart)
     */
    public function chartTopProductsByBookings(Request $request)
    {
        $from = Carbon::parse($request->input('from', now()->copy()->startOfMonth()))->startOfDay();
        $to = Carbon::parse($request->input('to', now()))->endOfDay();
        $status = $request->input('status');
        $limit = (int) $request->input('limit', 20);

        $data = BookingDetail::query()
            ->join('bookings', 'bookings.booking_id', '=', 'booking_details.booking_id')
            ->join('products', 'products.product_id', '=', 'booking_details.product_id')
            ->whereBetween('bookings.created_at', [$from, $to])
            ->when($status, fn($q) => $q->where('bookings.status', $status))
            ->selectRaw('
                products.name as product_name,
                COUNT(DISTINCT bookings.booking_id) as bookings,
                SUM(booking_details.total) as revenue
            ')
            ->groupBy('products.product_id', 'products.name')
            ->orderByDesc('bookings')
            ->limit($limit)
            ->get();

        return response()->json([
            'labels' => $data->pluck('product_name'),
            'bookings' => $data->pluck('bookings'),
            'revenue' => $data->pluck('revenue')->map(fn($v) => round((float)$v, 2)),
        ]);
    }

    /**
     * Chart: Product performance matrix (scatter plot: revenue vs bookings)
     */
    public function chartProductPerformanceMatrix(Request $request)
    {
        $from = Carbon::parse($request->input('from', now()->copy()->startOfMonth()))->startOfDay();
        $to = Carbon::parse($request->input('to', now()))->endOfDay();
        $status = $request->input('status');

        $data = BookingDetail::query()
            ->join('bookings', 'bookings.booking_id', '=', 'booking_details.booking_id')
            ->join('products', 'products.product_id', '=', 'booking_details.product_id')
            ->whereBetween('bookings.created_at', [$from, $to])
            ->when($status, fn($q) => $q->where('bookings.status', $status))
            ->selectRaw('
                products.product_id,
                products.name as product_name,
                SUM(booking_details.total) as revenue,
                COUNT(DISTINCT bookings.booking_id) as bookings
            ')
            ->groupBy('products.product_id', 'products.name')
            ->having('bookings', '>', 0)
            ->get();

        return response()->json([
            'data' => $data->map(fn($product) => [
                'x' => (int) $product->bookings,
                'y' => round((float) $product->revenue, 2),
                'label' => $product->product_name,
            ]),
        ]);
    }

    /**
     * Chart: Bookings by product type (donut chart)
     */
    public function chartBookingsByProductType(Request $request)
    {
        $from = Carbon::parse($request->input('from', now()->copy()->startOfMonth()))->startOfDay();
        $to = Carbon::parse($request->input('to', now()))->endOfDay();
        $status = $request->input('status');

        $data = BookingDetail::query()
            ->join('bookings', 'bookings.booking_id', '=', 'booking_details.booking_id')
            ->join('products', 'products.product_id', '=', 'booking_details.product_id')
            ->join('product_types', 'product_types.product_type_id', '=', 'products.product_type_id')
            ->whereBetween('bookings.created_at', [$from, $to])
            ->when($status, fn($q) => $q->where('bookings.status', $status))
            ->selectRaw('
                product_types.product_type_id,
                COUNT(DISTINCT bookings.booking_id) as bookings,
                SUM(booking_details.total) as revenue
            ')
            ->groupBy('product_types.product_type_id')
            ->orderByDesc('bookings')
            ->get();

        // Get type names with translations
        $typeNames = ProductType::query()
            ->get()
            ->mapWithKeys(fn($type) => [$type->product_type_id => $type->translated]);

        return response()->json([
            'labels' => $data->map(fn($item) => $typeNames[$item->product_type_id] ?? "Type #{$item->product_type_id}"),
            'bookings' => $data->pluck('bookings'),
            'revenue' => $data->pluck('revenue')->map(fn($v) => round((float)$v, 2)),
        ]);
    }

    /**
     * Chart: Capacity utilization by product
     */
    public function chartCapacityUtilization(Request $request)
    {
        $from = Carbon::parse($request->input('from', now()->copy()->startOfMonth()))->startOfDay();
        $to = Carbon::parse($request->input('to', now()))->endOfDay();
        $limit = (int) $request->input('limit', 15);

        // Get bookings with PAX
        $productBookings = BookingDetail::query()
            ->join('bookings', 'bookings.booking_id', '=', 'booking_details.booking_id')
            ->join('products', 'products.product_id', '=', 'booking_details.product_id')
            ->whereBetween('bookings.created_at', [$from, $to])
            ->whereIn('bookings.status', ['confirmed', 'paid', 'completed'])
            ->select('products.product_id', 'products.name as product_name', 'products.max_capacity', 'booking_details.categories')
            ->get()
            ->groupBy('product_id');

        $utilizationData = $productBookings->map(function ($bookings, $productId) {
            $product = $bookings->first();
            $maxCapacity = $product->max_capacity ?? 0;

            $totalPax = $bookings->sum(function ($detail) {
                $cats = is_string($detail->categories) ? json_decode($detail->categories, true) : $detail->categories;
                return collect($cats ?? [])->sum(fn($c) => (int)($c['quantity'] ?? 0));
            });

            $utilization = $maxCapacity > 0 ? ($totalPax / $maxCapacity) * 100 : 0;

            return [
                'product_name' => $product->product_name,
                'utilization' => round($utilization, 2),
                'booked_pax' => $totalPax,
                'max_capacity' => $maxCapacity,
            ];
        })->sortByDesc('utilization')->take($limit)->values();

        return response()->json([
            'labels' => $utilizationData->pluck('product_name'),
            'utilization' => $utilizationData->pluck('utilization'),
            'booked_pax' => $utilizationData->pluck('booked_pax'),
            'max_capacity' => $utilizationData->pluck('max_capacity'),
        ]);
    }
}
