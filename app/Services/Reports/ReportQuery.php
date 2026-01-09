<?php

namespace App\Services\Reports;

use App\Models\Reports\BookingFact;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * ReportQuery
 *
 * Handles reportquery operations.
 */
class ReportQuery
{
    public function monthlySales(Carbon $from, Carbon $to, ?string $status = null): Collection
    {
        $q = BookingFact::query()
            ->selectRaw('month_bucket, SUM(detail_total) AS revenue, COUNT(DISTINCT booking_id) AS bookings, SUM(adults_qty + kids_qty) AS pax')
            ->whereBetween('booking_date', [$from, $to])
            ->groupBy('month_bucket')
            ->orderBy('month_bucket');

        if ($status) $q->where('status', $status);
        return $q->get();
    }

    public function topTours(Carbon $from, Carbon $to, ?string $status = null, int $limit = 10): Collection
    {
        $q = BookingFact::query()
            ->selectRaw('tour_id, SUM(detail_total) AS revenue, COUNT(DISTINCT booking_id) AS bookings, SUM(adults_qty + kids_qty) AS pax')
            ->whereBetween('booking_date', [$from, $to])
            ->groupBy('tour_id')
            ->orderByDesc('revenue')
            ->limit($limit);

        if ($status) $q->where('status', $status);
        return $q->get();
    }

    public function byLanguage(Carbon $from, Carbon $to, ?string $status = null): Collection
    {
        $q = BookingFact::query()
            ->selectRaw('tour_language_id, SUM(detail_total) AS revenue, COUNT(DISTINCT booking_id) AS bookings, SUM(adults_qty + kids_qty) AS pax')
            ->whereBetween('booking_date', [$from, $to])
            ->groupBy('tour_language_id')
            ->orderByDesc('revenue');

        if ($status) $q->where('status', $status);
        return $q->get();
    }

    public function kpis(Carbon $from, Carbon $to, ?string $status = null): array
    {
        $q = BookingFact::query()->whereBetween('booking_date', [$from, $to]);
        if ($status) $q->where('status', $status);

        $agg = $q->selectRaw('
            SUM(detail_total) AS revenue,
            COUNT(DISTINCT booking_id) AS bookings,
            SUM(adults_qty + kids_qty) AS pax
        ')->first();

        $ticket = $agg->bookings ? round($agg->revenue / $agg->bookings, 2) : 0.0;

        return [
            'revenue'  => (float) ($agg->revenue ?? 0),
            'bookings' => (int)   ($agg->bookings ?? 0),
            'pax'      => (int)   ($agg->pax ?? 0),
            'atv'      => $ticket,
        ];
    }
}
