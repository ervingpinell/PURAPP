<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PendingBookingsSheet implements FromCollection, WithHeadings, WithStyles, WithTitle
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
            $paymentUrl = route('booking.payment', $booking->booking_reference);

            return [
                'pickup' => $detail?->pickup_time ?? '',
                'ref' => $booking->booking_reference,
                'product' => $booking->product->title ?? '',
                'pax' => $this->getTotalPax($detail),
                'cliente' => $booking->user->name ?? '',
                'hotel' => $this->getPickupLocation($detail),
                'payment_link' => $paymentUrl,
                'expiry' => $booking->pending_expires_at?->format('Y-m-d H:i') ?? '',
                'notas' => $booking->notes ?? '',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Pickup',
            'Ref',
            'Product',
            'Pax',
            'Cliente',
            'Hotel/Meeting',
            'Payment Link',
            'Expiry',
            'Notas',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => '000000']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'ffc107']]],
        ];
    }

    public function title(): string
    {
        return 'PENDIENTES';
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

    private function getPickupLocation($detail)
    {
        if (!$detail) {
            return '';
        }

        if ($detail->hotel_id) {
            return $detail->hotel->name ?? 'Hotel';
        }

        if ($detail->is_other_hotel) {
            return $detail->other_hotel_name ?? 'Other Hotel';
        }

        if ($detail->meeting_point_id) {
            return $detail->meetingPoint->name ?? 'Meeting Point';
        }

        return '';
    }
}
