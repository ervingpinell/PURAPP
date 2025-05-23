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
        $reservas = Reserva::with(['cliente', 'tour'])->get();
        return view('admin.reservas', compact('reservas'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar solo los campos que vienen del formulario
        $validated = $request->validate([
            'id_cliente' => 'required|integer|exists:clientes,id_cliente',
            'id_tour' => 'required|integer|exists:tours,id_tour',
            'fecha_reserva' => 'required|date',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'estado_reserva' => 'required|string',
            'idioma_tour' => 'required|string',
            'cantidad_adultos' => 'required|integer|min:1',
            'cantidad_ninos' => 'required|integer|min:0',
        ]);

        // Obtener el tour desde la base de datos
        $tour = Tour::findOrFail($validated['id_tour']);

        // Agregar los precios al array validado
        $validated['precio_adulto'] = $tour->precio_adulto;
        $validated['precio_nino'] = $tour->precio_nino;

        // Calcular el total de la reserva
        $validated['total_pago'] = ($tour->precio_adulto * $validated['cantidad_adultos']) +
                                ($tour->precio_nino * $validated['cantidad_ninos']);

        // Generar código de reserva
        $validated['codigo_reserva'] = strtoupper(Str::random(10));

        // Crear la reserva
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
        $reservas = Reserva::with(['cliente', 'tour'])->get();

        $pdf = Pdf::loadView('admin.reservas.pdf_resumen', compact('reservas'));
        return $pdf->download('reporte_reservas.pdf');
    }

    // Generate PDF for the specified reserva.
    public function generarComprobante(Reserva $reserva)
    {
        try {
            $pdf = Pdf::loadView('admin.reservas.comprobante', compact('reserva'));
            $nombreCliente = preg_replace('/[^A-Za-z0-9_]/', '_', $reserva->cliente->nombre ?? 'Cliente');
            $codigo = $reserva->codigo_reserva ?? $reserva->id;
            $nombreArchivo = "Comprobante_{$nombreCliente}_GV-{$codigo}.pdf";

            return $pdf->download($nombreArchivo);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'No se pudo generar el comprobante: ' . $e->getMessage());
        }
    }
}

