<?php

namespace App\Http\Controllers\Admin\Tours;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Exception;
use App\Models\Itinerary;
use App\Models\ItineraryItem;
use App\Models\ItineraryTranslation;
use App\Services\ItineraryService;
use App\Services\Contracts\TranslatorInterface;

class ItineraryController extends Controller
{
    public function index(ItineraryService $service)
    {
        $itineraries = Itinerary::with('items')
            ->orderBy('name')
            ->get();

        $items = $service->getAvailableItems();

        return view('admin.tours.itinerary.index', compact('itineraries', 'items'));
    }

    public function store(Request $request, TranslatorInterface $translator)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:itineraries,name',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            DB::transaction(function () use ($request, $translator) {
                $name = $request->string('name')->trim();
                $description = $request->filled('description')
                    ? $request->string('description')->trim()
                    : '';

                $itinerary = Itinerary::create([
                    'name'        => $name,
                    'description' => $description,
                    'is_active'   => true,
                ]);

                // Automatic translations (ES, EN, FR, PT, DE)
                $nameTranslations = $translator->translateAll($name);
                $descriptionTranslations = $translator->translateAll($description);

                foreach (['es', 'en', 'fr', 'pt', 'de'] as $lang) {
                    ItineraryTranslation::create([
                        'itinerary_id' => $itinerary->itinerary_id,
                        'locale'       => $lang,
                        'name'         => $nameTranslations[$lang] ?? $name,
                        'description'  => $descriptionTranslations[$lang] ?? $description,
                    ]);
                }
            });

            return redirect()
                ->back()
                ->with('success', 'Itinerary created successfully.');
        } catch (Exception $e) {
            Log::error('Error creating itinerary: ' . $e->getMessage());
            return back()->with('error', 'Failed to create itinerary.');
        }
    }

    public function update(Request $request, Itinerary $itinerary)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('itineraries', 'name')->ignore($itinerary->itinerary_id, 'itinerary_id'),
            ],
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            $name = $request->string('name')->trim();
            $description = $request->filled('description')
                ? $request->string('description')->trim()
                : '';

            $itinerary->update([
                'name'        => $name,
                'description' => $description,
            ]);

            return redirect()
                ->route('admin.tours.itinerary.index')
                ->with('success', 'Itinerary updated successfully.');
        } catch (Exception $e) {
            Log::error('Error updating itinerary: ' . $e->getMessage());
            return back()->with('error', 'Failed to update itinerary.');
        }
    }

    public function destroy(Itinerary $itinerary)
    {
        try {
            if ($itinerary->is_active) {
                $itinerary->items()->detach();
                $itinerary->update(['is_active' => false]);

                return redirect()
                    ->back()
                    ->with('success', 'Itinerary deactivated successfully.');
            }

            $itinerary->update(['is_active' => true]);

            return redirect()
                ->back()
                ->with('success', 'Itinerary activated successfully.');
        } catch (Exception $e) {
            Log::error('Error changing itinerary status: ' . $e->getMessage());
            return back()->with('error', 'Failed to change itinerary status.');
        }
    }

    public function assignItems(Request $request, Itinerary $itinerary)
    {
        // item_ids arrives as associative array: [item_id => order, ...]
        $rawData = $request->input('item_ids', []);

        $itemIds = collect($rawData)
            ->keys()
            ->filter(fn ($id) => $id !== 'dummy')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        if (count($itemIds) === 0) {
            return back()
                ->withErrors(['item_ids' => 'You must select at least one item.'])
                ->withInput()
                ->with('showAssignModal', $itinerary->itinerary_id);
        }

        // Check if all selected items exist and are active
        $activeItems = ItineraryItem::whereIn('item_id', $itemIds)
            ->where('is_active', true)
            ->pluck('item_id')
            ->all();

        $inactiveItems = array_diff($itemIds, $activeItems);
        if (count($inactiveItems)) {
            return back()
                ->withErrors(['item_ids' => 'You cannot assign an inactive item.'])
                ->withInput()
                ->with('showAssignModal', $itinerary->itinerary_id);
        }

        // Build pivot data: item_order, is_active
        $syncData = [];
        foreach ($rawData as $itemId => $order) {
            if ($itemId === 'dummy') {
                continue;
            }
            $syncData[(int) $itemId] = [
                'item_order' => (int) $order,
                'is_active'  => true,
            ];
        }

        $itinerary->items()->sync($syncData);

        return redirect()
            ->route('admin.tours.itinerary.index')
            ->with('success', 'Items assigned successfully.');
    }
}
