<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * CustomerReportController
 *
 * Handles customer analytics and demographics
 */
class CustomerReportController extends Controller
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

        // ====== KPIs ======
        // Count unique customers who made bookings in period
        $totalCustomers = Booking::query()
            ->whereBetween('created_at', [$from, $to])
            ->whereNotNull('user_id')
            ->distinct('user_id')
            ->count('user_id');

        // New customers = users who made their FIRST booking in this period
        $bookingsInPeriod = Booking::query()
            ->whereBetween('created_at', [$from, $to])
            ->whereNotNull('user_id')
            ->select('user_id', 'created_at')
            ->get();

        $newCustomers = 0;
        foreach ($bookingsInPeriod->groupBy('user_id') as $userId => $userBookings) {
            // Check if this user had any bookings BEFORE this period
            $hadPreviousBookings = Booking::where('user_id', $userId)
                ->where('created_at', '<', $from)
                ->exists();

            // If no previous bookings, this is a new customer
            if (!$hadPreviousBookings) {
                $newCustomers++;
            }
        }

        // Top countries from user data
        $topCountries = Booking::query()
            ->join('users', 'users.user_id', '=', 'bookings.user_id')
            ->whereBetween('bookings.created_at', [$from, $to])
            ->when($status, fn($q) => $q->where('bookings.status', $status))
            ->whereNotNull('users.country')
            ->selectRaw('
                users.country as billing_country,
                COUNT(DISTINCT bookings.booking_id) as bookings,
                SUM(bookings.total) as revenue
            ')
            ->groupBy('users.country')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        // Customer lifetime value (average)
        $clvData = Booking::query()
            ->whereNotNull('user_id')
            ->selectRaw('
                user_id,
                SUM(total) as lifetime_value
            ')
            ->groupBy('user_id')
            ->get();

        $avgCLV = $clvData->avg('lifetime_value') ?? 0;

        // Repeat customers (users with more than 1 booking ever)
        $repeatCustomers = Booking::query()
            ->whereNotNull('user_id')
            ->select('user_id')
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) > 1')
            ->get()
            ->count();

        $repeatRate = $totalCustomers > 0 ? ($repeatCustomers / $totalCustomers) * 100 : 0;

        $kpis = [
            'total_customers' => $totalCustomers,
            'new_customers' => $newCustomers,
            'avg_clv' => round($avgCLV, 2),
            'repeat_rate' => round($repeatRate, 2),
            'top_country' => $topCountries->first()?->billing_country ?? 'N/A',
        ];

        return view('admin.reports.customers', compact(
            'from',
            'to',
            'status',
            'kpis',
            'topCountries'
        ));
    }

    /**
     * Chart: Geographic distribution (top countries)
     */
    public function chartGeographicDistribution(Request $request)
    {
        $from = Carbon::parse($request->input('from', now()->copy()->startOfMonth()))->startOfDay();
        $to = Carbon::parse($request->input('to', now()))->endOfDay();
        $status = $request->input('status');
        $limit = (int) $request->input('limit', 15);

        $data = Booking::query()
            ->whereBetween('created_at', [$from, $to])
            ->when($status, fn($q) => $q->where('status', $status))
            ->whereNotNull('billing_country')
            ->selectRaw('
                billing_country,
                COUNT(DISTINCT booking_id) as bookings,
                SUM(total) as revenue,
                COUNT(DISTINCT user_id) as customers
            ')
            ->groupBy('billing_country')
            ->orderByDesc('revenue')
            ->limit($limit)
            ->get();

        return response()->json([
            'labels' => $data->pluck('billing_country'),
            'revenue' => $data->pluck('revenue')->map(fn($v) => round((float)$v, 2)),
            'bookings' => $data->pluck('bookings'),
            'customers' => $data->pluck('customers'),
        ]);
    }

    /**
     * Chart: Top countries by revenue (bar chart)
     */
    public function chartTopCountries(Request $request)
    {
        $from = Carbon::parse($request->input('from', now()->copy()->startOfMonth()))->startOfDay();
        $to = Carbon::parse($request->input('to', now()))->endOfDay();
        $metric = $request->input('metric', 'revenue'); // revenue or bookings
        $limit = (int) $request->input('limit', 10);

        $orderBy = $metric === 'bookings' ? 'bookings' : 'revenue';

        $data = Booking::query()
            ->whereBetween('created_at', [$from, $to])
            ->whereNotNull('billing_country')
            ->selectRaw('
                billing_country,
                COUNT(DISTINCT booking_id) as bookings,
                SUM(total) as revenue
            ')
            ->groupBy('billing_country')
            ->orderByDesc($orderBy)
            ->limit($limit)
            ->get();

        return response()->json([
            'labels' => $data->pluck('billing_country'),
            'revenue' => $data->pluck('revenue')->map(fn($v) => round((float)$v, 2)),
            'bookings' => $data->pluck('bookings'),
        ]);
    }

    /**
     * Chart: Customer growth trend
     */
    public function chartCustomerGrowth(Request $request)
    {
        $from = Carbon::parse($request->input('from', now()->copy()->startOfYear()))->startOfDay();
        $to = Carbon::parse($request->input('to', now()))->endOfDay();
        $period = $request->input('period', 'month'); // day, week, month

        $dateFormat = match ($period) {
            'day' => '%Y-%m-%d',
            'week' => '%x-%v',
            'month' => '%Y-%m',
            default => '%Y-%m',
        };

        $phpDateFormat = match ($period) {
            'day' => 'Y-m-d',
            'week' => 'o-W',
            'month' => 'Y-m',
            default => 'Y-m',
        };

        // New customers by period
        $newCustomers = User::query()
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw("DATE_FORMAT(created_at, ?) as period, COUNT(*) as count", [$dateFormat])
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->keyBy('period');

        // Total customers (cumulative)
        $allCustomers = User::where('created_at', '<=', $to)->count();

        $labels = [];
        $newSeries = [];
        $totalSeries = [];
        $runningTotal = User::where('created_at', '<', $from)->count();

        $step = $period === 'week' ? 'week' : ($period === 'month' ? 'month' : 'day');
        $cursorStart = (clone $from);
        if ($step === 'month') $cursorStart->startOfMonth();
        if ($step === 'week') $cursorStart->startOfWeek();

        $periodIter = \Carbon\CarbonPeriod::create($cursorStart, "1 {$step}", $to);

        foreach ($periodIter as $d) {
            $label = $d->format($phpDateFormat);
            $labels[] = $label;

            $newCount = isset($newCustomers[$label]) ? (int)$newCustomers[$label]->count : 0;
            $newSeries[] = $newCount;
            $runningTotal += $newCount;
            $totalSeries[] = $runningTotal;
        }

        return response()->json([
            'labels' => $labels,
            'new_customers' => $newSeries,
            'total_customers' => $totalSeries,
        ]);
    }

    /**
     * Chart: New vs returning customers
     */
    public function chartNewVsReturning(Request $request)
    {
        $from = Carbon::parse($request->input('from', now()->copy()->startOfMonth()))->startOfDay();
        $to = Carbon::parse($request->input('to', now()))->endOfDay();

        // Get all bookings in period
        $bookingsInPeriod = Booking::query()
            ->whereBetween('created_at', [$from, $to])
            ->whereNotNull('user_id')
            ->select('user_id', 'created_at')
            ->get();

        $newCustomers = 0;
        $returningCustomers = 0;

        foreach ($bookingsInPeriod->groupBy('user_id') as $userId => $userBookings) {
            // Check if user had bookings before this period
            $hadPreviousBookings = Booking::where('user_id', $userId)
                ->where('created_at', '<', $from)
                ->exists();

            if ($hadPreviousBookings) {
                $returningCustomers++;
            } else {
                $newCustomers++;
            }
        }

        return response()->json([
            'labels' => ['New Customers', 'Returning Customers'],
            'values' => [$newCustomers, $returningCustomers],
        ]);
    }

    /**
     * Chart: Customer distribution by city
     */
    public function chartCustomersByCity(Request $request)
    {
        $from = Carbon::parse($request->input('from', now()->copy()->startOfMonth()))->startOfDay();
        $to = Carbon::parse($request->input('to', now()))->endOfDay();
        $limit = (int) $request->input('limit', 15);

        $data = Booking::query()
            ->whereBetween('created_at', [$from, $to])
            ->whereNotNull('billing_city')
            ->selectRaw('
                billing_city,
                billing_country,
                COUNT(DISTINCT booking_id) as bookings,
                SUM(total) as revenue
            ')
            ->groupBy('billing_city', 'billing_country')
            ->orderByDesc('revenue')
            ->limit($limit)
            ->get();

        return response()->json([
            'labels' => $data->map(fn($item) => "{$item->billing_city}, {$item->billing_country}"),
            'revenue' => $data->pluck('revenue')->map(fn($v) => round((float)$v, 2)),
            'bookings' => $data->pluck('bookings'),
        ]);
    }
}
