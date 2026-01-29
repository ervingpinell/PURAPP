<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Product;
use App\Models\TourLanguage;
use App\Models\CustomerCategory;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

/**
 * ReportsController
 *
 * Handles sales and booking reports using Eloquent queries.
 */
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

        $status = $request->input('status'); // paid|confirmed|completed|cancelled|pending|null

        // Multiple filters
        $productIds = collect((array) $request->input('product_id', []))->filter()->map(fn($v) => (int)$v)->values()->all();
        $langIds = collect((array) $request->input('tour_language_id', []))->filter()->map(fn($v) => (int)$v)->values()->all();

        // ====== Catalogs for selects ======
        $productsMap = Product::pluck('name', 'product_id');
        $langsMap = TourLanguage::pluck('name', 'tour_language_id');

        // ====== Base Query ======
        $baseQuery = $this->buildBaseQuery($from, $to, $groupBy, $status, $productIds, $langIds);

        // ====== KPIs ======
        // Get all details to calculate PAX from categories JSON
        $allDetails = (clone $baseQuery)->select('booking_details.categories')->get();
        $totalPax = $allDetails->sum(function ($detail) {
            $cats = is_string($detail->categories) ? json_decode($detail->categories, true) : $detail->categories;
            return collect($cats ?? [])->sum(fn($c) => (int)($c['quantity'] ?? 0));
        });

        $kpisData = (clone $baseQuery)
            ->selectRaw('
                SUM(booking_details.total) as revenue,
                COUNT(DISTINCT bookings.booking_id) as bookings
            ')
            ->first();

        $kpis = [
            'revenue'  => (float) ($kpisData->revenue ?? 0),
            'bookings' => (int)   ($kpisData->bookings ?? 0),
            'pax'      => (int)   $totalPax,
            'atv'      => ($kpisData->bookings ?? 0) ? round($kpisData->revenue / max(1, $kpisData->bookings), 2) : 0,
        ];

        // ====== Top Products (by revenue) ======
        $topProductsRaw = (clone $baseQuery)
            ->selectRaw('
                product2.product_id,
                product2.name as product_name,
                SUM(booking_details.total) as revenue,
                COUNT(DISTINCT bookings.booking_id) as bookings
            ')
            ->groupBy('product2.product_id')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        // Calculate PAX for each product
        $topProducts = $topProductsRaw->map(function ($product) use ($from, $to, $groupBy, $status, $productIds, $langIds) {
            $productQuery = $this->buildBaseQuery($from, $to, $groupBy, $status, [$product->product_id], $langIds);
            $details = $productQuery->select('booking_details.categories')->get();
            $product->pax = $details->sum(function ($detail) {
                $cats = is_string($detail->categories) ? json_decode($detail->categories, true) : $detail->categories;
                return collect($cats ?? [])->sum(fn($c) => (int)($c['quantity'] ?? 0));
            });
            return $product;
        });

        // ====== Confirmed Bookings ======
        $confirmedBookings = (clone $baseQuery)
            ->where('bookings.status', 'confirmed')
            ->distinct('bookings.booking_id')
            ->count('bookings.booking_id');

        // ====== Pending Bookings Widget ======
        $pendingQuery = $this->buildBaseQuery($from, $to, $groupBy, 'pending', $productIds, $langIds);

        $pendingCount = (clone $pendingQuery)
            ->distinct('bookings.booking_id')
            ->count('bookings.booking_id');

        $pendingItems = (clone $pendingQuery)
            ->selectRaw('
                bookings.booking_id,
                bookings.booking_reference,
                MIN(booking_details.tour_date) as tour_date,
                bookings.created_at as booking_date,
                bookings.total,
                users.email as customer_email,
                MIN(CAST(product2.name AS TEXT)) as product_name
            ')
            ->leftJoin('users', 'users.user_id', '=', 'bookings.user_id')
            ->groupBy(
                'bookings.booking_id',
                'bookings.booking_reference',
                'bookings.created_at',
                'bookings.total',
                'users.email'
            )
            ->orderBy('bookings.created_at', 'asc')
            ->limit(8)
            ->get()
            ->map(function ($item) {
                // Calculate PAX from booking details categories
                $details = BookingDetail::where('booking_id', $item->booking_id)->get();
                $item->pax = $details->sum(function ($detail) {
                    $cats = is_string($detail->categories) ? json_decode($detail->categories, true) : $detail->categories;
                    return collect($cats ?? [])->sum(fn($c) => (int)($c['quantity'] ?? 0));
                });
                return $item;
            });

        return view('admin.reports.index', compact(
            'from',
            'to',
            'status',
            'kpis',
            'topProducts',
            'productsMap',
            'langsMap',
            'confirmedBookings',
            'groupBy',
            'period',
            'pendingItems',
            'pendingCount'
        ));
    }

    public function chartMonthlySales(Request $request)
    {
        // === inputs ===
        $groupBy = $request->input('group_by', 'booking_date');
        $period  = $request->input('period', 'month');
        $from    = Carbon::parse($request->input('from', now()->copy()->startOfYear()))->startOfDay();
        $to      = Carbon::parse($request->input('to', now()))->endOfDay();
        $status  = $request->input('status');

        $productIds = collect((array)$request->input('product_id', []))->filter()->map(fn($v) => (int)$v)->values()->all();
        $langIds = collect((array)$request->input('tour_language_id', []))->filter()->map(fn($v) => (int)$v)->values()->all();

        // === base ===
        $baseQuery = $this->buildBaseQuery($from, $to, $groupBy, $status, $productIds, $langIds);

        // === Determine date column and format ===
        $dateColumn = $groupBy === 'tour_date' ? 'bookings.tour_date' : 'bookings.created_at';

        if ($period === 'day') {
            $dateFormat = 'Y-m-d';
            $step = 'day';
        } elseif ($period === 'week') {
            $dateFormat = 'o-W'; // ISO week
            $step = 'week';
        } else { // month
            $dateFormat = 'Y-m';
            $step = 'month';
        }

        // Get raw data
        $rawData = (clone $baseQuery)
            ->selectRaw("
                DATE_FORMAT({$dateColumn}, ?) as bucket,
                SUM(booking_details.total) as revenue,
                COUNT(DISTINCT bookings.booking_id) as bookings
            ", [$this->getMySQLDateFormat($period)])
            ->groupBy('bucket')
            ->orderBy('bucket')
            ->get()
            ->keyBy('bucket');

        // === Build complete axis with zeros ===
        $labels = [];
        $seriesRevenue = [];
        $seriesBookings = [];

        $cursorStart = (clone $from);
        if ($step === 'month') $cursorStart->startOfMonth();
        if ($step === 'week')  $cursorStart->startOfWeek();

        $periodIter = CarbonPeriod::create($cursorStart, "1 {$step}", $to);

        foreach ($periodIter as $d) {
            $label = $d->format($dateFormat);
            $labels[] = $label;

            $seriesRevenue[]  = isset($rawData[$label]) ? round((float)$rawData[$label]->revenue, 2) : 0;
            $seriesBookings[] = isset($rawData[$label]) ? (int)$rawData[$label]->bookings : 0;
        }

        return response()->json([
            'labels' => $labels,
            'series' => [
                'revenue'  => $seriesRevenue,
                'bookings' => $seriesBookings,
            ]
        ]);
    }

    public function chartByLanguage(Request $request)
    {
        $groupBy = $request->input('group_by', 'booking_date');
        $from    = Carbon::parse($request->input('from', now()->copy()->startOfYear()))->startOfDay();
        $to      = Carbon::parse($request->input('to', now()))->endOfDay();
        $status  = $request->input('status');

        $productIds = collect((array)$request->input('product_id', []))->filter()->map(fn($v) => (int)$v)->values()->all();
        $langIds = collect((array)$request->input('tour_language_id', []))->filter()->map(fn($v) => (int)$v)->values()->all();

        $baseQuery = $this->buildBaseQuery($from, $to, $groupBy, $status, $productIds, $langIds);

        $rows = (clone $baseQuery)
            ->selectRaw('
                booking_details.tour_language_id,
                tour_languages.name as language_name,
                SUM(booking_details.total) as revenue,
                COUNT(DISTINCT bookings.booking_id) as bookings
            ')
            ->leftJoin('tour_languages', 'tour_languages.tour_language_id', '=', 'booking_details.tour_language_id')
            ->groupBy('booking_details.tour_language_id', 'tour_languages.name')
            ->orderByDesc('revenue')
            ->get();

        return response()->json([
            'keys'   => $rows->pluck('tour_language_id'),
            'labels' => $rows->pluck('language_name')->map(fn($name) => $name ?? 'N/A'),
            'series' => [
                'revenue'  => $rows->pluck('revenue')->map(fn($v) => round((float)$v, 2)),
                'bookings' => $rows->pluck('bookings')->map(fn($v) => (int)$v),
            ]
        ]);
    }

    /**
     * Build base query for reports
     */
    private function buildBaseQuery(Carbon $from, Carbon $to, string $groupBy, ?string $status, array $productIds, array $langIds)
    {
        $dateColumn = $groupBy === 'tour_date' ? 'bookings.tour_date' : 'bookings.created_at';

        $query = BookingDetail::query()
            ->join('bookings', 'bookings.booking_id', '=', 'booking_details.booking_id')
            ->join('product2', 'product2.product_id', '=', 'booking_details.product_id')
            ->whereBetween($dateColumn, [$from, $to]);

        if ($status) {
            $query->where('bookings.status', $status);
        }

        if (!empty($productIds)) {
            $query->whereIn('booking_details.product_id', $productIds);
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
            'week' => '%x-%v', // ISO week
            'month' => '%Y-%m',
            default => '%Y-%m',
        };
    }

    /**
     * ========================================================================
     * CATEGORY REPORTS
     * ========================================================================
     */

    /**
     * Report by customer category with date range filtering
     */
    public function byCategory(Request $request)
    {
        $from = $request->input('from')
            ? Carbon::parse($request->input('from'))->startOfDay()
            : Carbon::now()->startOfMonth();

        $to = $request->input('to')
            ? Carbon::parse($request->input('to'))->endOfDay()
            : Carbon::now()->endOfDay();

        $status = $request->input('status');
        $productIds = array_filter((array) $request->input('product_id', []));
        $langIds = array_filter((array) $request->input('tour_language_id', []));
        $categoryIds = array_filter((array) $request->input('category_id', []));

        $groupBy = $request->input('group_by', 'booking_date');
        $dateColumn = $groupBy === 'tour_date' ? 'booking_details.tour_date' : 'bookings.created_at';

        // Get all customer categories for mapping
        $categoriesMap = CustomerCategory::pluck('name', 'category_id')->toArray();

        // Fetch booking details with categories JSON
        $bookingDetails = BookingDetail::query()
            ->join('bookings', 'bookings.booking_id', '=', 'booking_details.booking_id')
            ->join('product2', 'product2.product_id', '=', 'booking_details.product_id')
            ->whereBetween($dateColumn, [$from, $to])
            ->when($status, fn($q) => $q->where('bookings.status', $status))
            ->when(!empty($productIds), fn($q) => $q->whereIn('booking_details.product_id', $productIds))
            ->when(!empty($langIds), fn($q) => $q->whereIn('booking_details.tour_language_id', $langIds))
            ->select(
                'booking_details.booking_detail_id',
                'booking_details.categories',
                'booking_details.price',
                'bookings.booking_id',
                'bookings.total'
            )
            ->get();

        // Parse categories and aggregate stats
        $categoryStats = collect();
        $totalRevenue = 0;
        $totalQuantity = 0;
        $bookingIds = collect();

        foreach ($bookingDetails as $detail) {
            $categories = is_string($detail->categories)
                ? json_decode($detail->categories, true)
                : $detail->categories;

            if (!is_array($categories)) {
                continue;
            }

            foreach ($categories as $cat) {
                $categoryId = $cat['category_id'] ?? null;
                $quantity = (int)($cat['quantity'] ?? 0);
                $price = (float)($cat['price'] ?? 0);

                if (!$categoryId || $quantity <= 0) {
                    continue;
                }

                // Filter by category if specified
                if (!empty($categoryIds) && !in_array($categoryId, $categoryIds)) {
                    continue;
                }

                $revenue = $price * $quantity;
                $totalRevenue += $revenue;
                $totalQuantity += $quantity;
                $bookingIds->push($detail->booking_id);

                // Aggregate by category
                if (!$categoryStats->has($categoryId)) {
                    $categoryStats->put($categoryId, [
                        'category_id' => $categoryId,
                        'category_name' => $categoriesMap[$categoryId] ?? "Category #{$categoryId}",
                        'total_quantity' => 0,
                        'total_revenue' => 0,
                        'bookings_count' => collect(),
                        'prices' => collect(),
                    ]);
                }

                $stat = $categoryStats->get($categoryId);
                $stat['total_quantity'] += $quantity;
                $stat['total_revenue'] += $revenue;
                $stat['bookings_count']->push($detail->booking_id);
                $stat['prices']->push($price);
                $categoryStats->put($categoryId, $stat);
            }
        }

        // Finalize stats
        $categoryStats = $categoryStats->map(function ($stat) {
            return (object)[
                'category_id' => $stat['category_id'],
                'category_name' => $stat['category_name'],
                'total_quantity' => $stat['total_quantity'],
                'total_revenue' => round($stat['total_revenue'], 2),
                'bookings_count' => $stat['bookings_count']->unique()->count(),
                'avg_unit_price' => $stat['prices']->avg() ?? 0,
            ];
        })->sortByDesc('total_revenue')->values();

        // KPIs
        $kpis = [
            'total_revenue' => round($totalRevenue, 2),
            'total_quantity' => $totalQuantity,
            'total_bookings' => $bookingIds->unique()->count(),
            'categories_count' => $categoryStats->count(),
        ];

        return view('admin.reports.by-category', compact(
            'from',
            'to',
            'status',
            'categoryStats',
            'kpis',
            'categoriesMap',
            'productIds',
            'langIds',
            'categoryIds',
            'groupBy'
        ));
    }

    /**
     * Chart: Category breakdown (pie/donut chart)
     */
    public function chartCategoryBreakdown(Request $request)
    {
        $groupBy = $request->input('group_by', 'booking_date');
        $from = Carbon::parse($request->input('from', now()->copy()->startOfYear()))->startOfDay();
        $to = Carbon::parse($request->input('to', now()))->endOfDay();
        $status = $request->input('status');

        $productIds = collect((array)$request->input('product_id', []))->filter()->map(fn($v) => (int)$v)->values()->all();
        $langIds = collect((array)$request->input('tour_language_id', []))->filter()->map(fn($v) => (int)$v)->values()->all();

        $dateColumn = $groupBy === 'tour_date' ? 'bookings.tour_date' : 'bookings.created_at';

        $query = BookingDetail::query()
            ->join('bookings', 'bookings.booking_id', '=', 'booking_details.booking_id')
            ->join('booking_detail_categories', 'booking_detail_categories.booking_detail_id', '=', 'booking_details.booking_detail_id')
            ->join('customer_categories', 'customer_categories.category_id', '=', 'booking_detail_categories.category_id')
            ->whereBetween($dateColumn, [$from, $to]);

        if ($status) {
            $query->where('bookings.status', $status);
        }

        if (!empty($productIds)) {
            $query->whereIn('booking_details.product_id', $productIds);
        }

        if (!empty($langIds)) {
            $query->whereIn('booking_details.tour_language_id', $langIds);
        }

        // Get category names with translations
        $categoriesMap = CustomerCategory::with([])
            ->get()
            ->mapWithKeys(fn($cat) => [$cat->category_id => $cat->translated]);

        $rows = $query
            ->selectRaw('
                customer_categories.category_id,
                SUM(booking_detail_categories.price * booking_detail_categories.quantity) as revenue,
                SUM(booking_detail_categories.quantity) as quantity
            ')
            ->groupBy('customer_categories.category_id')
            ->orderByDesc('revenue')
            ->get()
            ->map(function ($row) use ($categoriesMap) {
                $row->category_name = $categoriesMap[$row->category_id] ?? "Category #{$row->category_id}";
                return $row;
            });

        return response()->json([
            'labels' => $rows->pluck('category_name'),
            'revenue' => $rows->pluck('revenue')->map(fn($v) => round((float)$v, 2)),
            'quantity' => $rows->pluck('quantity')->map(fn($v) => (int)$v),
        ]);
    }
}
