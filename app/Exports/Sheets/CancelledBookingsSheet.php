<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CancelledBookingsSheet implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $bookings;
    protected $date;

    public function __construct($bookings, $date)
    {
        $this->bookings = $bookings;
        $this->date = $date;
    }

    public function collection()
    {
        return $this->bookings->map(function ($booking) {
            $detail = $booking->details->first();

            return [
                'date_cancelled' => $booking->updated_at?->format('Y-m-d H:i') ?? '',
                'ref' => $booking->booking_reference,
                'tour' => $booking->tour->title ?? '',
                'pax' => $this->getTotalPax($detail),
                'cliente' => $booking->user->name ?? '',
                'reason' => $this->getCancellationReason($booking),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Cancelled',
            'Ref',
            'Tour',
            'Pax',
            'Cliente',
            'Reason',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'dc3545']]],
        ];
    }

    public function title(): string
    {
        return 'CANCELADAS';
    }

    private function getTotalPax($detail)
    {
        if (!$detail || !$detail->categories) {
            return 0;
        }

        $categories = is_string($detail->categories) ? json_decode($detail->categories, true) : $detail->categories;
        $total = 0;

        if (is_array($categories)) {
            foreach ($categories as $cat) {
                $total += (int)($cat['quantity'] ?? 0);
            }
        }

        return $total;
    }

    private function getCancellationReason($booking)
    {
        if (str_contains($booking->notes ?? '', '[AUTO-CANCELLED]')) {
            return 'Auto-cancelled (Payment not received)';
        }

        return 'Manual cancellation';
    }
}
