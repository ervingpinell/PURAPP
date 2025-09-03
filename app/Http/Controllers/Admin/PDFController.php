<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Booking;

class PDFController extends Controller
{
    public function generarComprobante($id)
    {
        $reserva = Booking::with(['user', 'tour'])->findOrFail($id);

        $pdf = Pdf::loadView('admin.pdf.comprobante', compact('reserva'));

        return $pdf->download('comprobante_reserva_' . $reserva->codigo_reserva . '.pdf');
    }
}
