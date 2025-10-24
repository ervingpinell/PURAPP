<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Booking;

class PDFController extends Controller
{
    public function generateReceipt($id)
    {
        $booking = Booking::with(['user', 'tour'])->findOrFail($id);

        $pdf = Pdf::loadView('admin.pdf.receipt', compact('booking'));

        return $pdf->download('booking_receipt_' . $booking->booking_reference . '.pdf');
    }
}
