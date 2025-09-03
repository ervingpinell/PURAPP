<?php

namespace App\Http\Controllers\Admin\Tours;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Models\Itinerary;
use App\Models\ItineraryTranslation;
use App\Services\ItineraryService;
use App\Services\Contracts\TranslatorInterface;
use App\Services\LoggerHelper;
use App\Http\Requests\Tour\Itinerary\StoreItineraryRequest;
use App\Http\Requests\Tour\Itinerary\UpdateItineraryRequest;
use App\Http\Requests\Tour\Itinerary\AssignItineraryItemsRequest;

class ItineraryController extends Controller
{
    protected string $controller = 'ItineraryController';

    public function index(ItineraryService $service)
    {
        $itineraryList  = Itinerary::with('items')->orderBy('name')->get();
        $availableItems = $service->getAvailableItems();

        return view('admin.tours.itinerary.index', [
            'itineraries' => $itineraryList,
            'items'       => $availableItems,
        ]);
    }

    public function store(StoreItineraryRequest $request, TranslatorInterface $translator)
    {
        try {
            $data        = $request->validated();
            $name        = $data['name'];
            $description = (string) ($data['description'] ?? '');
            $locales     = config('i18n.supported_locales', ['es','en','fr','pt','de']);

            $itinerary = DB::transaction(function () use ($name, $description, $locales, $translator) {
                $itinerary = Itinerary::create([
                    'name'        => $name,
                    'description' => $description,
                    'is_active'   => true,
                ]);

                // Traducciones automáticas
                $nameTr = $translator->translateAll($name);
                $descTr = $translator->translateAll($description);

                foreach ($locales as $locale) {
                    ItineraryTranslation::create([
                        'itinerary_id' => $itinerary->itinerary_id,
                        'locale'       => $locale,
                        'name'         => $nameTr[$locale] ?? $name,
                        'description'  => $descTr[$locale] ?? $description,
                    ]);
                }

                return $itinerary;
            });

            if (! $itinerary) {
                LoggerHelper::error($this->controller, 'store', 'Itinerary null after commit', [
                    'entity'  => 'itinerary',
                    'user_id' => optional($request->user())->getAuthIdentifier(),
                ]);
                return back()->with('error', __('m_tours.itinerary.error.create'));
            }

            LoggerHelper::mutated($this->controller, 'store', 'itinerary', $itinerary->itinerary_id, [
                'locales_saved' => count($locales),
                'user_id'       => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('success', __('m_tours.itinerary.success.created'));

        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'store', 'itinerary', null, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', __('m_tours.itinerary.error.create'));
        }
    }

    public function update(UpdateItineraryRequest $request, Itinerary $itinerary)
    {
        try {
            $data = $request->validated();

            $itinerary->update([
                'name'        => $data['name'],
                'description' => $data['description'] ?? null,
            ]);

            LoggerHelper::mutated($this->controller, 'update', 'itinerary', $itinerary->itinerary_id, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return redirect()
                ->route('admin.tours.itinerary.index')
                ->with('success', __('m_tours.itinerary.success.updated'));

        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'update', 'itinerary', $itinerary->itinerary_id, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', __('m_tours.itinerary.error.update'));
        }
    }

    /**
     * Toggle activar/desactivar itinerario
     */
    public function toggle(Itinerary $itinerary)
    {
        try {
            // Race-safe: NOT is_active a nivel de DB
            Itinerary::whereKey($itinerary->getKey())->update(['is_active' => DB::raw('NOT is_active')]);
            $itinerary->refresh();

            LoggerHelper::mutated($this->controller, 'toggle', 'itinerary', $itinerary->itinerary_id, [
                'is_active' => $itinerary->is_active,
                'user_id'   => optional(request()->user())->getAuthIdentifier(),
            ]);

            $msg = $itinerary->is_active
                ? __('m_tours.itinerary.success.activated')
                : __('m_tours.itinerary.success.deactivated');

            return back()->with('success', $msg);

        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'toggle', 'itinerary', $itinerary->itinerary_id ?? null, $e, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', __('m_tours.itinerary.error.toggle'));
        }
    }

    /**
     * Eliminación definitiva (NO usar aún).
     * Dejo el método comentado para cuando quieras habilitarlo.
     */
    /*
    public function destroy(Itinerary $itinerary)
    {
        try {
            $id = $itinerary->itinerary_id;
            $itinerary->delete();

            LoggerHelper::mutated($this->controller, 'destroy', 'itinerary', $id, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return back()->with('success', __('m_tours.itinerary.success.deleted'));

        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'destroy', 'itinerary', $itinerary->itinerary_id ?? null, $e, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', __('m_tours.itinerary.error.delete'));
        }
    }
    */

    /**
     * Asignar ítems a un itinerario (recibe items[ID]=orden desde el modal)
     */
    public function assignItems(AssignItineraryItemsRequest $request, Itinerary $itinerary)
    {
        try {
            $data = $request->validated();
            $map  = $data['items'] ?? []; // normalizado por el Request

            $pivotData = [];
            foreach ($map as $itemId => $order) {
                $pivotData[(int) $itemId] = [
                    'item_order' => (int) $order,
                    'is_active'  => true,
                ];
            }

            $itinerary->items()->sync($pivotData);

            LoggerHelper::mutated($this->controller, 'assignItems', 'itinerary', $itinerary->itinerary_id, [
                'items_assigned' => count($pivotData),
                'user_id'        => optional($request->user())->getAuthIdentifier(),
            ]);

            return redirect()
                ->route('admin.tours.itinerary.index')
                ->with('success', __('m_tours.itinerary.success.items_assigned'));

        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'assignItems', 'itinerary', $itinerary->itinerary_id, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()
                ->withErrors(['items' => __('m_tours.itinerary.error.assign')])
                ->with('error', __('m_tours.itinerary.error.assign'))
                ->with('showAssignModal', $itinerary->itinerary_id);
        }
    }
}
