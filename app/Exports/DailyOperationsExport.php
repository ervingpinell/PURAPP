<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DailyOperationsExport implements WithMultipleSheets
{
    protected $confirmed;
    protected $pending;
    protected $cancelled;
    protected $date;

    public function __construct($confirmed, $pending, $cancelled, $date)
    {
        $this->confirmed = $confirmed;
        $this->pending = $pending;
        $this->cancelled = $cancelled;
        $this->date = $date;
    }

    public function sheets(): array
    {
        return [
            new Sheets\ConfirmedBookingsSheet($this->confirmed, $this->date),
            new Sheets\PendingBookingsSheet($this->pending, $this->date),
            new Sheets\CancelledBookingsSheet($this->cancelled, $this->date),
        ];
    }
}
