<?php

namespace App\Exports;

use App\Models\Booking;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class BookingsExport implements FromView
{
    protected array $filters;
    protected $bookings; // opcional: colección precargada

    public function __construct(array $filters = [], $bookings = null)
    {
        $this->filters  = $filters;
        $this->bookings = $bookings;
    }

    public function view(): View
    {
        // Si ya te pasaron una colección, úsala tal cual
        if ($this->bookings !== null) {
            return view('admin.bookings.excel', [
                'bookings' => $this->bookings,
            ]);
        }

        // Caso normal: construir la query según filtros
        $q = Booking::with([
            'user',
            'product.productType',
            'detail.schedule',
            'detail.hotel',
            'detail.meetingPoint',
            'redemption.promoCode',
            'promoCodeLegacy',
        ]);

        // ====== Filtros ======
        $f = $this->filters;

        // Por referencia (ILIKE en Postgres)
        if (!empty($f['reference'])) {
            $q->where('booking_reference', 'ilike', '%' . trim($f['reference']) . '%');
        }

        // Por estado
        if (!empty($f['status'])) {
            $q->where('status', $f['status']);
        }

        // Por product_id
        if (!empty($f['product_id'])) {
            $q->where('product_id', (int)$f['product_id']);
        }

        // Por fecha de reserva (booking_date)
        if (!empty($f['booking_date_from'])) {
            $q->whereDate('booking_date', '>=', $f['booking_date_from']);
        }
        if (!empty($f['booking_date_to'])) {
            $q->whereDate('booking_date', '<=', $f['booking_date_to']);
        }

        // Por fecha de producto (detail.tour_date - legacy column name)
        if (!empty($f['tour_date_from'])) {
            $from = $f['tour_date_from'];
            $q->whereHas('detail', fn($d) => $d->whereDate('tour_date', '>=', $from));
        }
        if (!empty($f['tour_date_to'])) {
            $to = $f['tour_date_to'];
            $q->whereHas('detail', fn($d) => $d->whereDate('tour_date', '<=', $to));
        }

        // Por horario (schedule_id en el detail)
        if (!empty($f['schedule_id'])) {
            $sid = (int)$f['schedule_id'];
            $q->whereHas('detail', fn($d) => $d->where('schedule_id', $sid));
        }

        // Orden
        $bookings = $q->orderBy('booking_date', 'desc')->get();

        return view('admin.bookings.excel', compact('bookings'));
    }

    public static function generateFileName(array $filters): string
    {
        $name = 'Report';

        if (!empty($filters['product_id'])) {
            $product = \App\Models\Product::find($filters['product_id']);
            if ($product) {
                $name .= ' ' . preg_replace('/\s*\([^)]*\)/', '', $product->name);
            }
        }

        if (!empty($filters['tour_date_from']) || !empty($filters['tour_date_to'])) {
            $from = $filters['tour_date_from'] ?? null;
            $to   = $filters['tour_date_to']   ?? null;

            if ($from && $to && $from === $to)       $name .= " products on $from";
            elseif ($from && $to)                    $name .= " products from $from to $to";
            elseif ($from)                           $name .= " products from $from";
            elseif ($to)                             $name .= " products until $to";
        } elseif (!empty($filters['booking_date_from']) || !empty($filters['booking_date_to'])) {
            $from = $filters['booking_date_from'] ?? null;
            $to   = $filters['booking_date_to']   ?? null;

            if ($from && $to && $from === $to)       $name .= " booked on $from";
            elseif ($from && $to)                    $name .= " booked from $from to $to";
            elseif ($from)                           $name .= " booked from $from";
            elseif ($to)                             $name .= " booked until $to";
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
