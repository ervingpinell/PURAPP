<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Reserva;
use App\Models\Cliente;
use App\Models\Tour;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class ReservaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reservas = Reserva::with(['user', 'tour'])->get();
        return view('admin.reservas', compact('reservas'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,user_id',
            'id_tour' => 'required|integer|exists:tours,id_tour',
            'fecha_reserva' => 'required|date',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'estado_reserva' => 'required|string',
            'idioma_tour' => 'required|string',
            'cantidad_adultos' => 'required|integer|min:1',
            'cantidad_ninos' => 'required|integer|min:0',
        ]);

        $tour = Tour::findOrFail($validated['id_tour']);

        $validated['precio_adulto'] = $tour->precio_adulto;
        $validated['precio_nino'] = $tour->precio_nino;
        $validated['total_pago'] = ($tour->precio_adulto * $validated['cantidad_adultos']) +
                                    ($tour->precio_nino * $validated['cantidad_ninos']);
        $validated['codigo_reserva'] = strtoupper(Str::random(10));

        $validated['user_id'] = $request->user_id;

        Reserva::create($validated);

        return redirect()->back()->with('success', 'Reserva agregada correctamente.');
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'cantidad_adultos' => 'required|integer|min:1',
            'cantidad_ninos' => 'required|integer|min:0',
            'estado_reserva' => 'required|string',
            'notas' => 'nullable|string',
        ]);

        // Obtener la reserva y su tour relacionado
        $reserva = Reserva::findOrFail($id);
        $tour = $reserva->tour; // Asumiendo que existe la relación en el modelo

        // Asignar precios según el tour
        $validated['precio_adulto'] = $tour->precio_adulto;
        $validated['precio_nino'] = $tour->precio_nino;

        // Calcular el nuevo total
        $validated['total_pago'] = ($tour->precio_adulto * $validated['cantidad_adultos']) +
                                ($tour->precio_nino * $validated['cantidad_ninos']);

        // Actualizar la reserva
        $reserva->update($validated);

        return redirect()->back()->with('success', 'Reserva actualizada correctamente.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $reserva = Reserva::findOrFail($id);
        $reserva->delete();

        return redirect()->back()->with('success', 'Reserva eliminada.');
    }

    /**
     * Generate PDF for the specified reserva.
     */
    public function generarPDF()
    {
        $reservas = Reserva::with(['user', 'tour'])->get();

        $pdf = Pdf::loadView('admin.reservas.pdf_resumen', compact('reservas'));
        return $pdf->download('reporte_reservas.pdf');
    }

    // Generate PDF for the specified reserva.
    public function generarComprobante(Reserva $reserva)
    {
        try {
            $pdf = Pdf::loadView('admin.reservas.comprobante', compact('reserva'));
            $nombreCliente = preg_replace('/[^A-Za-z0-9_]/', '_', $reserva->user->full_name ?? 'Cliente');
            $codigo = $reserva->codigo_reserva ?? $reserva->id;
            $nombreArchivo = "Comprobante_{$nombreCliente}_GV-{$codigo}.pdf";

            return $pdf->download($nombreArchivo);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'No se pudo generar el comprobante: ' . $e->getMessage());
        }
    }
}

