<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Reserva;

class PDFController extends Controller
{
    public function generarComprobante($id)
    {
        // Obtener la reserva por ID
        $reserva = Reserva::with(['cliente', 'tour'])->findOrFail($id);

        // Generar el PDF
        $pdf = Pdf::loadView('admin.pdf.comprobante', compact('reserva'));

        // Descargar el PDF
        return $pdf->download('comprobante_reserva_' . $reserva->codigo_reserva . '.pdf');
    }
}
