<?php

namespace App\Http\Controllers\Admin\Tours;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Models\ItineraryItem;
use App\Models\ItineraryItemTranslation;
use App\Services\Contracts\TranslatorInterface;
use App\Services\LoggerHelper;
use App\Http\Requests\Tour\ItineraryItem\StoreItineraryItemRequest;
use App\Http\Requests\Tour\ItineraryItem\UpdateItineraryItemRequest;
use App\Http\Requests\Tour\ItineraryItem\ToggleItineraryItemRequest;

class ItineraryItemController extends Controller
{
    protected string $controller = 'ItineraryItemController';

    public function index()
    {
        $activeItems = ItineraryItem::where('is_active', true)
            ->orderBy('title')
            ->get();

        return view('admin.tours.itinerary.items.crud', ['items' => $activeItems]);
    }

    public function store(StoreItineraryItemRequest $request, TranslatorInterface $translator)
    {
        try {
            DB::transaction(function () use ($request, $translator) {
                $title       = $request->string('title')->trim();
                $description = $request->string('description')->trim();
                $locales     = ['es', 'en', 'fr', 'pt', 'de'];

                $item = ItineraryItem::create([
                    'title'       => $title,
                    'description' => $description,
                    'is_active'   => true,
                ]);

                // Traducción automática
                $titleTr = $translator->translateAll($title);
                $descTr  = $translator->translateAll($description);

                foreach ($locales as $locale) {
                    ItineraryItemTranslation::create([
                        'item_id'     => $item->item_id,
                        'locale'      => $locale,
                        'title'       => $titleTr[$locale] ?? $title,
                        'description' => $descTr[$locale] ?? $description,
                    ]);
                }

                LoggerHelper::mutated($this->controller, 'store', 'itinerary_item', $item->item_id, [
                    'locales_saved' => count($locales),
                    'user_id'       => optional(request()->user())->getAuthIdentifier(),
                ]);
            });

            return back()->with('success', 'Itinerary item created successfully.');
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'store', 'itinerary_item', null, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);
            return back()->with('error', 'Could not create the item.');
        }
    }

public function update(UpdateItineraryItemRequest $request, ItineraryItem $itinerary_item)
{
    try {

        $payload = [
            'title'       => $request->string('title')->trim(),
            'description' => $request->string('description')->trim(),
        ];

        $itinerary_item->update($payload);

        LoggerHelper::mutated($this->controller, 'update', 'itinerary_item', $itinerary_item->item_id, [
            'is_active' => $itinerary_item->is_active, // solo para referencia en el log
            'user_id'   => optional($request->user())->getAuthIdentifier(),
        ]);

        return back()->with('success', 'Item updated successfully.');
    } catch (Exception $e) {
        LoggerHelper::exception($this->controller, 'update', 'itinerary_item', $itinerary_item->item_id, $e, [
            'user_id' => optional($request->user())->getAuthIdentifier(),
        ]);
        return back()->with('error', 'Could not update the item.');
    }
}


    /**
     * Alterna is_active y, si queda inactivo, lo desvincula de itinerarios.
     */
    public function destroy(ToggleItineraryItemRequest $request, ItineraryItem $itinerary_item)
    {
        try {
            $itinerary_item->update(['is_active' => ! $itinerary_item->is_active]);
            $itinerary_item->refresh();

            if (! $itinerary_item->is_active && method_exists($itinerary_item, 'itineraries')) {
                $itinerary_item->itineraries()->detach();
            }

            LoggerHelper::mutated($this->controller, 'destroy', 'itinerary_item', $itinerary_item->item_id, [
                'is_active' => $itinerary_item->is_active,
                'user_id'   => optional($request->user())->getAuthIdentifier(),
            ]);

            $message = $itinerary_item->is_active
                ? 'Item activated successfully.'
                : 'Item deactivated successfully.';

            return back()->with('success', $message);
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'destroy', 'itinerary_item', $itinerary_item->item_id, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);
            return back()->with('error', 'Could not change item status.');
        }
    }
}
