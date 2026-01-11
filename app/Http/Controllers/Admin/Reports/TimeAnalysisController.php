<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingDetail;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

/**
 * TimeAnalysisController
 *
 * Handles temporal pattern analysis and peak time reports
 */
class TimeAnalysisController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->filled('from')
            ? Carbon::parse($request->input('from'))->startOfDay()
            : Carbon::now()->copy()->startOfMonth();

        $to = $request->filled('to')
            ? Carbon::parse($request->input('to'))->endOfDay()
            : Carbon::now()->endOfDay();

        $status = $request->input('status');

        // ====== KPIs ======
        // PostgreSQL: EXTRACT(DOW FROM date) returns 0-6 (Sunday=0)
        $bookingsByDayOfWeek = Booking::query()
            ->whereBetween('created_at', [$from, $to])
            ->when($status, fn($q) => $q->where('status', $status))
            ->selectRaw('EXTRACT(DOW FROM created_at) as day_of_week, COUNT(*) as count')
            ->groupBy('day_of_week')
            ->orderByDesc('count')
            ->get();

        $busiestDay = $bookingsByDayOfWeek->first();
        $busiestDayName = $busiestDay ? $this->getDayName((int)$busiestDay->day_of_week + 1) : 'N/A'; // +1 to match MySQL

        $bookingsByHour = Booking::query()
            ->whereBetween('created_at', [$from, $to])
            ->when($status, fn($q) => $q->where('status', $status))
            ->selectRaw('EXTRACT(HOUR FROM created_at) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderByDesc('count')
            ->get();

        $peakHour = $bookingsByHour->first()?->hour ?? 'N/A';

        $bookingsByMonth = Booking::query()
            ->whereBetween('created_at', [$from, $to])
            ->when($status, fn($q) => $q->where('status', $status))
            ->selectRaw('EXTRACT(MONTH FROM created_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderByDesc('count')
            ->get();

        $peakMonth = $bookingsByMonth->first();
        $peakMonthName = $peakMonth ? $this->getMonthName((int)$peakMonth->month) : 'N/A';

        $kpis = [
            'busiest_day' => $busiestDayName,
            'peak_hour' => $peakHour !== 'N/A' ? sprintf('%02d:00', $peakHour) : 'N/A',
            'peak_month' => $peakMonthName,
            'total_bookings' => Booking::whereBetween('created_at', [$from, $to])->count(),
        ];

        return view('admin.reports.time-analysis', compact(
            'from',
            'to',
            'status',
            'kpis'
        ));
    }

    /**
     * Chart: Bookings by day of week
     */
    public function chartBookingsByDayOfWeek(Request $request)
    {
        $from = Carbon::parse($request->input('from', now()->copy()->startOfMonth()))->startOfDay();
        $to = Carbon::parse($request->input('to', now()))->endOfDay();
        $status = $request->input('status');

        $data = Booking::query()
            ->whereBetween('created_at', [$from, $to])
            ->when($status, fn($q) => $q->where('status', $status))
            ->selectRaw('
                EXTRACT(DOW FROM created_at) as day_of_week,
                COUNT(*) as bookings,
                SUM(total) as revenue
            ')
            ->groupBy('day_of_week')
            ->orderBy('day_of_week')
            ->get();

        $labels = [];
        $bookings = [];
        $revenue = [];

        for ($i = 0; $i <= 6; $i++) { // PostgreSQL: 0=Sunday, 6=Saturday
            $dayData = $data->firstWhere('day_of_week', $i);
            $labels[] = $this->getDayName($i + 1); // +1 to match MySQL format
            $bookings[] = $dayData ? (int)$dayData->bookings : 0;
            $revenue[] = $dayData ? round((float)$dayData->revenue, 2) : 0;
        }

        return response()->json([
            'labels' => $labels,
            'bookings' => $bookings,
            'revenue' => $revenue,
        ]);
    }

    /**
     * Chart: Bookings by hour of day
     */
    public function chartBookingsByHour(Request $request)
    {
        $from = Carbon::parse($request->input('from', now()->copy()->startOfMonth()))->startOfDay();
        $to = Carbon::parse($request->input('to', now()))->endOfDay();
        $status = $request->input('status');

        $data = Booking::query()
            ->whereBetween('created_at', [$from, $to])
            ->when($status, fn($q) => $q->where('status', $status))
            ->selectRaw('
                EXTRACT(HOUR FROM created_at) as hour,
                COUNT(*) as bookings,
                SUM(total) as revenue
            ')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->keyBy('hour');

        $labels = [];
        $bookings = [];
        $revenue = [];

        for ($h = 0; $h < 24; $h++) {
            $labels[] = sprintf('%02d:00', $h);
            $bookings[] = isset($data[$h]) ? (int)$data[$h]->bookings : 0;
            $revenue[] = isset($data[$h]) ? round((float)$data[$h]->revenue, 2) : 0;
        }

        return response()->json([
            'labels' => $labels,
            'bookings' => $bookings,
            'revenue' => $revenue,
        ]);
    }

    /**
     * Chart: Monthly seasonality
     */
    public function chartMonthlySeasonality(Request $request)
    {
        $from = Carbon::parse($request->input('from', now()->copy()->startOfYear()))->startOfDay();
        $to = Carbon::parse($request->input('to', now()))->endOfDay();
        $status = $request->input('status');

        $data = Booking::query()
            ->whereBetween('created_at', [$from, $to])
            ->when($status, fn($q) => $q->where('status', $status))
            ->selectRaw('
                DATE_FORMAT(created_at, "%Y-%m") as month,
                COUNT(*) as bookings,
                SUM(total) as revenue
            ')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $labels = [];
        $bookings = [];
        $revenue = [];

        $period = CarbonPeriod::create($from->copy()->startOfMonth(), '1 month', $to);

        foreach ($period as $date) {
            $monthKey = $date->format('Y-m');
            $labels[] = $date->format('M Y');
            $bookings[] = isset($data[$monthKey]) ? (int)$data[$monthKey]->bookings : 0;
            $revenue[] = isset($data[$monthKey]) ? round((float)$data[$monthKey]->revenue, 2) : 0;
        }

        return response()->json([
            'labels' => $labels,
            'bookings' => $bookings,
            'revenue' => $revenue,
        ]);
    }

    /**
     * Chart: Heatmap (day of week vs hour)
     */
    public function chartHeatmap(Request $request)
    {
        $from = Carbon::parse($request->input('from', now()->copy()->startOfMonth()))->startOfDay();
        $to = Carbon::parse($request->input('to', now()))->endOfDay();
        $status = $request->input('status');

        $data = Booking::query()
            ->whereBetween('created_at', [$from, $to])
            ->when($status, fn($q) => $q->where('status', $status))
            ->selectRaw('
                EXTRACT(DOW FROM created_at) as day_of_week,
                EXTRACT(HOUR FROM created_at) as hour,
                COUNT(*) as bookings
            ')
            ->groupBy('day_of_week', 'hour')
            ->get();

        // Build matrix
        $matrix = [];
        for ($day = 0; $day <= 6; $day++) { // PostgreSQL: 0=Sunday, 6=Saturday
            $dayData = [];
            for ($hour = 0; $hour < 24; $hour++) {
                $cell = $data->first(function ($item) use ($day, $hour) {
                    return $item->day_of_week == $day && $item->hour == $hour;
                });
                $dayData[] = $cell ? (int)$cell->bookings : 0;
            }
            $matrix[] = $dayData;
        }

        $dayLabels = [];
        for ($i = 0; $i <= 6; $i++) {
            $dayLabels[] = $this->getDayName($i + 1); // +1 to match MySQL
        }

        $hourLabels = [];
        for ($h = 0; $h < 24; $h++) {
            $hourLabels[] = sprintf('%02d:00', $h);
        }

        return response()->json([
            'days' => $dayLabels,
            'hours' => $hourLabels,
            'data' => $matrix,
        ]);
    }

    private function getDayName(int $dayOfWeek): string
    {
        return match ($dayOfWeek) {
            1 => 'Sunday',
            2 => 'Monday',
            3 => 'Tuesday',
            4 => 'Wednesday',
            5 => 'Thursday',
            6 => 'Friday',
            7 => 'Saturday',
            default => 'Unknown',
        };
    }

    private function getMonthName(int $month): string
    {
        return Carbon::create(null, $month, 1)->format('F');
    }
}
