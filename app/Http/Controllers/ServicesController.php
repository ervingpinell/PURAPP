<?php

namespace App\Http\Controllers;

use App\Helpers\NavigationHelper;
use App\Helpers\ProductCategoryHelper;
use App\Models\Product;
use Illuminate\Http\Request;

class ServicesController extends Controller
{
    public function index(Request $request)
    {
        $categories = NavigationHelper::getActiveCategories();
        $loc = app()->getLocale();
        $fb = config('app.fallback_locale', 'es');
        
        // Get filter parameters
        $searchQuery = $request->input('q');
        $categoryFilter = $request->input('category');
        
        // Get sample products for each category (3 per category)
        $categoriesWithProducts = [];
        
        foreach ($categories as $key => $config) {
            // Build query for products
            $query = Product::active();
            
            // Apply search filter if present
            if ($searchQuery) {
                $query->where(function ($q) use ($searchQuery, $loc, $fb) {
                    $q->whereRaw("LOWER(name->>'$loc') LIKE ?", ['%' . strtolower($searchQuery) . '%'])
                      ->orWhereRaw("LOWER(name->>'$fb') LIKE ?", ['%' . strtolower($searchQuery) . '%'])
                      ->orWhereRaw("LOWER(description->>'$loc') LIKE ?", ['%' . strtolower($searchQuery) . '%'])
                      ->orWhereRaw("LOWER(description->>'$fb') LIKE ?", ['%' . strtolower($searchQuery) . '%']);
                });
            }
            
            // Apply category filter if present
            if ($categoryFilter && $categoryFilter === $key) {
                // Only show this category if it matches the filter
                $products = $query
                    ->orderByRaw("name->>'$loc' ASC")
                    ->limit(3)
                    ->get()
                    ->map(function ($product) use ($loc, $fb) {
                        $name = $product->getTranslation('name', $loc, false) 
                             ?: $product->getTranslation('name', $fb)
                             ?: $product->name;
                        $product->translated_name = $name;
                        return $product;
                    });
                
                $total = Product::active()->count();
                
                // Get category object with translations
                $categoryObj = (object)[
                    'name' => $config['name'] ?? $key,
                    'translated_name' => $config['name'] ?? $key,
                ];
                
                $categoriesWithProducts[$key] = [
                    'config' => $config,
                    'category' => $categoryObj,
                    'products' => $products,
                    'total' => $total
                ];
            } elseif (!$categoryFilter) {
                // No category filter, show all categories
                $products = $query
                    ->orderByRaw("name->>'$loc' ASC")
                    ->limit(3)
                    ->get()
                    ->map(function ($product) use ($loc, $fb) {
                        $name = $product->getTranslation('name', $loc, false) 
                             ?: $product->getTranslation('name', $fb)
                             ?: $product->name;
                        $product->translated_name = $name;
                        return $product;
                    });
                
                $total = Product::active()->count();
                
                // Get category object with translations
                $categoryObj = (object)[
                    'name' => $config['name'] ?? $key,
                    'translated_name' => $config['name'] ?? $key,
                ];
                
                $categoriesWithProducts[$key] = [
                    'config' => $config,
                    'category' => $categoryObj,
                    'products' => $products,
                    'total' => $total
                ];
            }
        }
        
        return view('services.index', compact('categoriesWithProducts'));
    }
}
