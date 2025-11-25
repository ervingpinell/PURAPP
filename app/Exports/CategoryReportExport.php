<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CategoryReportExport implements FromArray, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Booking ID',
            'Booking Reference',
            'Booking Date',
            'Tour Date',
            'Status',
            'Tour Name',
            'Language',
            'Category ID',
            'Category Name',
            'Category Slug',
            'Quantity',
            'Unit Price',
            'Line Total',
            'Booking Month',
            'Tour Month',
        ];
    }

    public function map($row): array
    {
        return [
            $row->booking_id,
            $row->booking_reference,
            $row->booking_date,
            $row->tour_date,
            $row->status,
            $row->tour_name ?? 'N/A',
            $row->language_name ?? 'N/A',
            $row->category_id,
            $row->category_name,
            $row->category_slug,
            $row->quantity,
            number_format((float)$row->unit_price, 2, '.', ''),
            number_format((float)$row->line_total, 2, '.', ''),
            $row->booking_month,
            $row->tour_month,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12,  // Booking ID
            'B' => 20,  // Booking Reference
            'C' => 15,  // Booking Date
            'D' => 15,  // Tour Date
            'E' => 12,  // Status
            'F' => 30,  // Tour Name
            'G' => 15,  // Language
            'H' => 12,  // Category ID
            'I' => 20,  // Category Name
            'J' => 15,  // Category Slug
            'K' => 10,  // Quantity
            'L' => 12,  // Unit Price
            'M' => 12,  // Line Total
            'N' => 15,  // Booking Month
            'O' => 15,  // Tour Month
        ];
    }
}
