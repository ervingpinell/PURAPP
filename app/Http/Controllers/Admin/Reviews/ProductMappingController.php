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
 * Manages product code mappings between external providers and internal products.
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

        // Load products for the mapped IDs
        $productIds = array_keys($productMap);
        $products = Product::whereIn('product_id', $productIds)
            ->get()
            ->keyBy('product_id');

        // Build mappings array with product details
        $mappings = [];
        foreach ($productMap as $productId => $productCode) {
            $product = $products->get($productId);
            if ($product) {
                $mappings[] = [
                    'product_id' => $productId,
                    'product_name' => $product->getTranslatedName(),
                    'product_code' => $productCode,
                ];
            }
        }

        // Get all products for dropdown
        $allProducts = Product::where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->product_id,
                    'name' => $product->getTranslatedName(),
                ];
            });

        return view('admin.reviews.providers.product-map', compact('provider', 'mappings', 'allProducts'));
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
                'exists:product2,product_id',
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
    public function update(Request $request, ReviewProvider $provider, string $productId)
    {
        $validated = $request->validate([
            'product_code' => 'required|string|max:255',
        ]);

        $settings = is_array($provider->settings) ? $provider->settings : [];
        $productMap = (array) ($settings['product_map'] ?? []);

        if (!isset($productMap[$productId])) {
            return back()->with('error', 'Mapping not found.');
        }

        // Update mapping
        $productMap[$productId] = trim($validated['product_code']);
        $settings['product_map'] = $productMap;

        $provider->settings = $settings;
        $provider->save();

        Cache::flush();

        return back()->with('ok', __('reviews.providers.messages.mapping_updated'));
    }

    /**
     * Delete a product mapping
     */
    public function destroy(ReviewProvider $provider, string $productId)
    {
        $settings = is_array($provider->settings) ? $provider->settings : [];
        $productMap = (array) ($settings['product_map'] ?? []);

        if (!isset($productMap[$productId])) {
            return back()->with('error', 'Mapping not found.');
        }

        // Remove mapping
        unset($productMap[$productId]);
        $settings['product_map'] = $productMap;

        $provider->settings = $settings;
        $provider->save();

        Cache::flush();

        return back()->with('ok', __('reviews.providers.messages.mapping_deleted'));
    }
}
