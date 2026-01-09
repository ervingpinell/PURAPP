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
            $itinerary = Itinerary::create([
                'is_active' => true,
            ]);

            // Create Spanish translation
            $itinerary->translations()->create([
                'locale' => 'es',
                'name' => $name,
                'description' => $description ?? '',
            ]);

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
                    // Check if item exists by looking for a Spanish translation with this title
                    $existingTranslation = \App\Models\ItineraryItemTranslation::where('locale', 'es')
                        ->where('title', $itemData['title'])
                        ->first();

                    if ($existingTranslation) {
                        $itemId = $existingTranslation->item_id;
                    } else {
                        $newItem = ItineraryItem::create([
                            'is_active' => true
                        ]);

                        // Create Spanish translation
                        $newItem->translations()->create([
                            'locale' => 'es',
                            'title' => $itemData['title'],
                            'description' => $itemData['description'] ?? '',
                        ]);

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
            return Itinerary::with(['items.translations', 'translations'])->find($requestData['itinerary_id']);
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
        $query = ItineraryItem::query()->with('translations');

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
        return Itinerary::with(['items.translations', 'translations'])
            ->whereHas('items')
            ->get()
            ->sortBy(function ($itinerary) {
                return $itinerary->name; // Uses magic accessor
            })
            ->values();
    }
}
