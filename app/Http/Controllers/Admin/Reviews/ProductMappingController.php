<?php

namespace App\Http\Controllers\Admin\Reviews;

use App\Http\Controllers\Controller;
use App\Models\ReviewProvider;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

/**
 * ProductMappingController
 *
 * Manages product code mappings between external providers and internal tours.
 */
class ProductMappingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['can:edit-review-providers']);
    }

    /**
     * Display product mappings for a provider
     */
    public function index(ReviewProvider $provider)
    {
        // Only external providers need product mapping
        if ($provider->driver !== 'http_json') {
            return redirect()
                ->route('admin.review-providers.index')
                ->with('error', 'Product mapping is only available for external providers.');
        }

        $settings = is_array($provider->settings) ? $provider->settings : [];
        $productMap = (array) ($settings['product_map'] ?? []);

        // Load tours for the mapped IDs
        $tourIds = array_keys($productMap);
        $tours = Product::whereIn('product_id', $tourIds)
            ->get()
            ->keyBy('product_id');

        // Build mappings array with tour details
        $mappings = [];
        foreach ($productMap as $tourId => $productCode) {
            $tour = $tours->get($tourId);
            if ($tour) {
                $mappings[] = [
                    'product_id' => $tourId,
                    'tour_name' => $tour->getTranslatedName(),
                    'product_code' => $productCode,
                ];
            }
        }

        // Get all tours for dropdown
        $allTours = Product::where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function ($tour) {
                return [
                    'id' => $tour->product_id,
                    'name' => $tour->getTranslatedName(),
                ];
            });

        return view('admin.reviews.providers.product-map', compact('provider', 'mappings', 'allTours'));
    }

    /**
     * Store a new product mapping
     */
    public function store(Request $request, ReviewProvider $provider)
    {
        $validated = $request->validate([
            'product_id' => [
                'required',
                'integer',
                'exists:tours,product_id',
                Rule::unique('review_providers', 'settings->product_map->' . $request->product_id)
                    ->where('id', '!=', $provider->id),
            ],
            'product_code' => 'required|string|max:255',
        ]);

        $settings = is_array($provider->settings) ? $provider->settings : [];
        $productMap = (array) ($settings['product_map'] ?? []);

        // Add new mapping
        $productMap[(string) $validated['product_id']] = trim($validated['product_code']);
        $settings['product_map'] = $productMap;

        $provider->settings = $settings;
        $provider->save();

        Cache::flush();

        return back()->with('ok', __('reviews.providers.messages.mapping_added'));
    }

    /**
     * Update an existing product mapping
     */
    public function update(Request $request, ReviewProvider $provider, string $tourId)
    {
        $validated = $request->validate([
            'product_code' => 'required|string|max:255',
        ]);

        $settings = is_array($provider->settings) ? $provider->settings : [];
        $productMap = (array) ($settings['product_map'] ?? []);

        if (!isset($productMap[$tourId])) {
            return back()->with('error', 'Mapping not found.');
        }

        // Update mapping
        $productMap[$tourId] = trim($validated['product_code']);
        $settings['product_map'] = $productMap;

        $provider->settings = $settings;
        $provider->save();

        Cache::flush();

        return back()->with('ok', __('reviews.providers.messages.mapping_updated'));
    }

    /**
     * Delete a product mapping
     */
    public function destroy(ReviewProvider $provider, string $tourId)
    {
        $settings = is_array($provider->settings) ? $provider->settings : [];
        $productMap = (array) ($settings['product_map'] ?? []);

        if (!isset($productMap[$tourId])) {
            return back()->with('error', 'Mapping not found.');
        }

        // Remove mapping
        unset($productMap[$tourId]);
        $settings['product_map'] = $productMap;

        $provider->settings = $settings;
        $provider->save();

        Cache::flush();

        return back()->with('ok', __('reviews.providers.messages.mapping_deleted'));
    }
}
