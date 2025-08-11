<?php

namespace App\Http\Controllers\Admin\Tours;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ItineraryItem;
use App\Models\ItineraryItemTranslation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Exception;
use App\Services\Contracts\TranslatorInterface;

class ItineraryItemController extends Controller
{
    public function index()
    {
        $items = ItineraryItem::where('is_active', true)
            ->orderBy('title')
            ->get();

        return view('admin.tours.itinerary.items.crud', compact('items'));
    }

    public function store(Request $request, TranslatorInterface $translator)
    {
        $validator = Validator::make($request->all(), [
            'title'       => 'required|string|max:255',
            'description' => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::transaction(function () use ($request, $translator) {
                $title = $request->string('title')->trim();
                $desc  = $request->string('description')->trim();

                $item = ItineraryItem::create([
                    'title'       => $title,
                    'description' => $desc,
                    'is_active'   => true,
                ]);

                // Traducción automática (ES, EN, FR, PT, DE)
                $titleTr = $translator->translateAll($title);
                $descTr  = $translator->translateAll($desc);

                foreach (['es', 'en', 'fr', 'pt', 'de'] as $lang) {
                    ItineraryItemTranslation::create([
                        'item_id'     => $item->item_id,
                        'locale'      => $lang,
                        'title'       => $titleTr[$lang] ?? $title,
                        'description' => $descTr[$lang] ?? $desc,
                    ]);
                }
            });

            return redirect()->back()->with('success', 'Ítem de itinerario creado exitosamente.');
        } catch (Exception $e) {
            Log::error('Error al crear ítem de itinerario: ' . $e->getMessage());
            return redirect()->back()->with('error', 'No se pudo crear el ítem.');
        }
    }

    public function update(Request $request, ItineraryItem $itinerary_item)
    {
        $item = $itinerary_item;

        $validator = Validator::make($request->all(), [
            'title'       => [
                'required',
                'string',
                'max:255',
                // si decides volver a exigir unicidad, descomenta:
                // Rule::unique('itinerary_items', 'title')->ignore($item->item_id, 'item_id'),
            ],
            'description' => 'required|string|max:2000',
            'is_active'   => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $item->update([
                'title'       => $request->string('title')->trim(),
                'description' => $request->string('description')->trim(),
                'is_active'   => $request->has('is_active') ? (bool) $request->is_active : $item->is_active,
            ]);

            // Nota: si quieres re-traducir automáticamente al editar, avísame y lo agregamos aquí.
            return redirect()->back()->with('success', 'Ítem actualizado correctamente.');
        } catch (Exception $e) {
            Log::error('Error al actualizar ítem de itinerario: ' . $e->getMessage());
            return redirect()->back()->with('error', 'No se pudo actualizar el ítem.');
        }
    }

    public function destroy(ItineraryItem $itinerary_item)
    {
        try {
            // Toggle estado
            $itinerary_item->update([
                'is_active' => ! $itinerary_item->is_active,
            ]);
            $itinerary_item->refresh();

            // Si quedó inactivo, desvincular de itinerarios
            if (! $itinerary_item->is_active && method_exists($itinerary_item, 'itineraries')) {
                $itinerary_item->itineraries()->detach();
            }

            $mensaje = $itinerary_item->is_active
                ? 'Ítem activado exitosamente.'
                : 'Ítem desactivado exitosamente.';

            return redirect()->back()->with('success', $mensaje);
        } catch (Exception $e) {
            Log::error('Error al cambiar estado del ítem de itinerario: ' . $e->getMessage());
            return back()->with('error', 'No se pudo cambiar el estado del ítem.');
        }
    }
}
