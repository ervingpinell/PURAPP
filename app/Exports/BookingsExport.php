<?php

namespace App\Exports;

use App\Models\Booking;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class BookingsExport implements FromView
{
    protected $filters;
    protected $bookings; // ⬅️ Agregá esta propiedad

    public function __construct(array $filters = [], $bookings = null)
    {
    $this->filters = $filters;
    $this->bookings = $bookings;
    }

    public function view(): View
    {
        return view('admin.bookingDetails.excel', [
            'bookings' => $this->bookings ?? collect()
        ]);
    }

    /** ✅ Genera nombre dinámico para el archivo Excel */
    public static function generarNombre(array $filters): string
    {
        $nombre = 'Reporte';

        if (!empty($filters['tour_id'])) {
            $tour = \App\Models\Tour::find($filters['tour_id']);
            if ($tour) {
                $nombre .= ' ' . preg_replace('/\s*\([^)]*\)/', '', $tour->name);
            }
        }

        if (!empty($filters['tour_date_from']) || !empty($filters['tour_date_to'])) {
            $desde = $filters['tour_date_from'] ?? null;
            $hasta = $filters['tour_date_to'] ?? null;

            if ($desde && $hasta && $desde === $hasta) {
                $nombre .= " tours del $desde";
            } elseif ($desde && $hasta) {
                $nombre .= " tours del $desde al $hasta";
            } elseif ($desde) {
                $nombre .= " tours desde $desde";
            } elseif ($hasta) {
                $nombre .= " tours hasta $hasta";
            }
        } elseif (!empty($filters['booking_date_from']) || !empty($filters['booking_date_to'])) {
            $desde = $filters['booking_date_from'] ?? null;
            $hasta = $filters['booking_date_to'] ?? null;

            if ($desde && $hasta && $desde === $hasta) {
                $nombre .= " reservados del $desde";
            } elseif ($desde && $hasta) {
                $nombre .= " reservados del $desde al $hasta";
            } elseif ($desde) {
                $nombre .= " reservados desde $desde";
            } elseif ($hasta) {
                $nombre .= " reservados hasta $hasta";
            }
        }

        if (!empty($filters['status'])) {
            $nombre .= " ({$filters['status']})";
        }

        if (!empty($filters['schedule_id'])) {
            $horario = \App\Models\Schedule::find($filters['schedule_id']);
            if ($horario) {
                $nombre .= " [" . $horario->start_time . "]";
            }
        }

        if (!empty($filters['reference'])) {
            $nombre .= " [ref {$filters['reference']}]";
        }

        return $nombre . '.xlsx';
    }
}
