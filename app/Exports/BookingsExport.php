<?php

namespace App\Exports;

use App\Models\Booking;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class BookingsExport implements FromView
{
    protected $filters;
    protected $bookings;

    public function __construct(array $filters = [], $bookings = null)
    {
        $this->filters = $filters;
        $this->bookings = $bookings;
    }

    public function view(): View
    {
        return view('admin.bookings.excel', [
            'bookings' => $this->bookings ?? collect()
        ]);
    }

    public static function generateFileName(array $filters): string
    {
        $name = 'Report';

        if (!empty($filters['tour_id'])) {
            $tour = \App\Models\Tour::find($filters['tour_id']);
            if ($tour) {
                $name .= ' ' . preg_replace('/\s*\([^)]*\)/', '', $tour->name);
            }
        }

        if (!empty($filters['tour_date_from']) || !empty($filters['tour_date_to'])) {
            $from = $filters['tour_date_from'] ?? null;
            $to = $filters['tour_date_to'] ?? null;

            if ($from && $to && $from === $to) {
                $name .= " tours on $from";
            } elseif ($from && $to) {
                $name .= " tours from $from to $to";
            } elseif ($from) {
                $name .= " tours from $from";
            } elseif ($to) {
                $name .= " tours until $to";
            }
        } elseif (!empty($filters['booking_date_from']) || !empty($filters['booking_date_to'])) {
            $from = $filters['booking_date_from'] ?? null;
            $to = $filters['booking_date_to'] ?? null;

            if ($from && $to && $from === $to) {
                $name .= " booked on $from";
            } elseif ($from && $to) {
                $name .= " booked from $from to $to";
            } elseif ($from) {
                $name .= " booked from $from";
            } elseif ($to) {
                $name .= " booked until $to";
            }
        }

        if (!empty($filters['status'])) {
            $name .= " ({$filters['status']})";
        }

        if (!empty($filters['schedule_id'])) {
            $schedule = \App\Models\Schedule::find($filters['schedule_id']);
            if ($schedule) {
                $name .= " [" . $schedule->start_time . "]";
            }
        }

        if (!empty($filters['reference'])) {
            $name .= " [ref {$filters['reference']}]";
        }

        return $name . '.xlsx';
    }
}
