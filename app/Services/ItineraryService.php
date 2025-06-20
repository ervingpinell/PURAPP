<?php

namespace App\Services;

use App\Models\Itinerary;
use App\Models\ItineraryItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ItineraryService
{
public function createWithItems(string $name, array $items, string $description = null): Itinerary
{
    if (empty($items)) {
        Log::error('❌ No se proporcionaron ítems válidos para el itinerario.', [
            'name' => $name,
            'description' => $description,
        ]);
        throw new \Exception('No se proporcionaron ítems válidos para el itinerario.');
    }

    Log::info('📌 Creando itinerario con ítems:', [
        'name' => $name,
        'description' => $description,
        'items' => $items
    ]);

    return DB::transaction(function () use ($name, $items, $description) {
        $itinerary = Itinerary::create([
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
                $existing = ItineraryItem::where('title', $itemData['title'])->first();
                if ($existing) {
                    $itemId = $existing->item_id;
                } else {
                    $newItem = ItineraryItem::create([
                        'title' => $itemData['title'],
                        'description' => $itemData['description'] ?? '',
                        'is_active' => true
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
            return Itinerary::with('items')->find($requestData['itinerary_id']);
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
        return ItineraryItem::orderBy('title')->where('is_active', true)->get();
    }

    public function getAvailableItinerariesWithItems()
    {
        return Itinerary::with('items')->whereHas('items')->orderBy('name')->get();
    }
}
