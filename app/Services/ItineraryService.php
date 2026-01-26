<?php

namespace App\Services;

use App\Models\Itinerary;
use App\Models\ItineraryItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * ItineraryService
 *
 * Handles itinerary operations.
 */
class ItineraryService
{
    /**
     * Create an itinerary with its items.
     *
     * @param string $name Itinerary name
     * @param array $items Array of items to add to the itinerary
     * @param string|null $description Optional description
     * @return Itinerary
     * @throws Exception If no valid items are provided
     */
    public function createWithItems(string $name, array $items, string $description = null): Itinerary
    {
        if (empty($items)) {
            Log::error('No valid items provided for itinerary.', [
                'name' => $name,
                'description' => $description,
            ]);
            throw new \Exception('No valid items provided for itinerary.');
        }

        if (config('app.debug')) {
            Log::info('Creating itinerary with items:', [
                'name' => $name,
                'description' => $description,
                'items' => $items
            ]);
        }

        return DB::transaction(function () use ($name, $items, $description) {
            // Spatie Translatable: Create with JSON attributes directly
            // We set 'name' => ['es' => $name] automatically if we pass array, 
            // or just assignment if the model casts it, but the model fills 'name'.
            // To ensure specific locale 'es', we can set the attribute manually afterwards or pass array if model supports it.
            // Assuming HasTranslations trait handles simple assignment as default locale or we should be explicit.
            
            $itinerary = new Itinerary();
            $itinerary->is_active = true;
            $itinerary->setTranslation('name', 'es', $name);
            if ($description) {
                $itinerary->setTranslation('description', 'es', $description);
            }
            $itinerary->save();

            foreach ($items as $index => $itemData) {
                if (is_numeric($itemData)) {
                    $item = ItineraryItem::find($itemData);
                    if ($item) {
                        $itinerary->items()->attach($item->item_id, [
                            'item_order' => $index,
                            'is_active' => true
                        ]);
                    }
                } elseif (is_array($itemData) && !empty($itemData['title'])) {
                    // Check if item exists by JSON search for Spanish title
                    // JSON: title->>'es' = 'Value'
                    $existingItem = ItineraryItem::whereRaw("LOWER(title->>'es') = ?", [strtolower($itemData['title'])])->first();

                    if ($existingItem) {
                        $itemId = $existingItem->item_id;
                    } else {
                        $newItem = new ItineraryItem();
                        $newItem->is_active = true;
                        $newItem->setTranslation('title', 'es', $itemData['title']);
                        if (!empty($itemData['description'])) {
                            $newItem->setTranslation('description', 'es', $itemData['description']);
                        }
                        $newItem->save();

                        $itemId = $newItem->item_id;
                    }
                    $itinerary->items()->attach($itemId, [
                        'item_order' => $index,
                        'is_active' => true
                    ]);
                }
            }

            return $itinerary;
        });
    }


    public function handleCreationOrAssignment(array $requestData): ?Itinerary
    {
        if (($requestData['itinerary_id'] ?? null) === 'new') {
            $items = $requestData['itinerary_combined'] ?? [];
            return $this->createWithItems(
                $requestData['new_itinerary_name'] ?? 'Itinerario generado',
                $items,
                $requestData['new_itinerary_description'] ?? ''
            );
        } elseif (!empty($requestData['itinerary_id']) && is_numeric($requestData['itinerary_id'])) {
            return Itinerary::with(['items'])->find($requestData['itinerary_id']);
        }

        return null;
    }

    public function replaceItemsWithOrder(Itinerary $itinerary, array $itemIds): void
    {
        DB::transaction(function () use ($itinerary, $itemIds) {
            $syncData = [];
            foreach ($itemIds as $index => $itemId) {
                $syncData[$itemId] = [
                    'item_order' => $index,
                    'is_active' => true
                ];
            }
            $itinerary->items()->sync($syncData);
        });
    }

    public function getAvailableItems()
    {
        $query = ItineraryItem::query();

        if (request('estado') === 'activos') {
            $query->where('is_active', true);
        } elseif (request('estado') === 'inactivos') {
            $query->where('is_active', false);
        }

        // Get items and sort by translated title in memory
        return $query->get()->sortBy(function ($item) {
            return $item->title; // Uses magic accessor
        })->values();
    }

    public function getAvailableItinerariesWithItems()
    {
        // Get itineraries with translations and sort by translated name in memory
        return Itinerary::with(['items'])
            ->whereHas('items')
            ->get()
            ->sortBy(function ($itinerary) {
                return $itinerary->name; // Uses magic accessor
            })
            ->values();
    }
}
