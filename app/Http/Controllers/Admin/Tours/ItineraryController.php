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
            $data       = $request->validated();
            $name       = $data['name'];
            $description= (string) ($data['description'] ?? '');
            $locales    = ['es', 'en', 'fr', 'pt', 'de'];

            DB::transaction(function () use ($name, $description, $locales, $translator, $request) {
                $itinerary = Itinerary::create([
                    'name'        => $name,
                    'description' => $description,
                    'is_active'   => true,
                ]);

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

                LoggerHelper::mutated($this->controller, 'store', 'itinerary', $itinerary->itinerary_id, [
                    'locales_saved' => count($locales),
                    'user_id'       => optional($request->user())->getAuthIdentifier(),
                ]);
            });

            return back()->with('success', 'Itinerary created successfully.');
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'store', 'itinerary', null, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', 'Could not create the itinerary.');
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
                ->with('success', 'Itinerary updated successfully.');
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'update', 'itinerary', $itinerary->itinerary_id, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', 'Could not update the itinerary.');
        }
    }
    public function destroy(Itinerary $itinerary)
    {
        try {
            $itinerary->update(['is_active' => ! $itinerary->is_active]);

            LoggerHelper::mutated($this->controller, 'destroy', 'itinerary', $itinerary->itinerary_id, [
                'is_active' => $itinerary->is_active,
                'user_id'   => optional(request()->user())->getAuthIdentifier(),
            ]);

            $msg = $itinerary->is_active
                ? 'Itinerary activated successfully.'
                : 'Itinerary deactivated successfully (items preserved).';

            return back()->with('success', $msg);
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'destroy', 'itinerary', $itinerary->itinerary_id ?? null, $e, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', 'Could not change itinerary status.');
        }
    }

public function assignItems(AssignItineraryItemsRequest $request, Itinerary $itinerary)
{
    try {
        $data     = $request->validated();
        $rawMap   = $data['items'] ?? [];
        $pivotData = [];

        foreach ($rawMap as $itemId => $order) {
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
            ->with('success', 'Items assigned successfully.');
    } catch (Exception $e) {
        LoggerHelper::exception($this->controller, 'assignItems', 'itinerary', $itinerary->itinerary_id, $e, [
            'user_id' => optional($request->user())->getAuthIdentifier(),
        ]);

        return back()
            ->withErrors(['items' => 'Could not assign items.'])
            ->with('error', 'Could not assign items.')
            ->with('showAssignModal', $itinerary->itinerary_id);
    }
}

}
