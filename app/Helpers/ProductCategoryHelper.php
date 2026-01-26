<?php

namespace App\Helpers;

use App\Models\Product;

class ProductCategoryHelper
{
    /**
     * Get all categories configuration
     */
    public static function getAllCategories(): array
    {
        return config('product-categories.categories', []);
    }

    /**
     * Get specific category configuration
     */
    public static function getCategoryConfig(string $category): ?array
    {
        return config("product-categories.categories.{$category}");
    }

    /**
     * Get URL prefix for a category
     */
    public static function getUrlPrefix(string $category): string
    {
        $config = self::getCategoryConfig($category);
        return $config['url_prefix'] ?? 'products';
    }

    /**
     * Get category label (singular or plural)
     */
    public static function getCategoryLabel(string $category, bool $plural = true): string
    {
        $config = self::getCategoryConfig($category);
        
        if (!$config) {
            return ucfirst($category);
        }
        
        return $config[$plural ? 'plural' : 'singular'];
    }

    /**
     * Get all subcategories for a category
     */
    public static function getSubcategories(string $category): array
    {
        $config = self::getCategoryConfig($category);
        return $config['subcategories'] ?? [];
    }

    /**
     * Get specific subcategory configuration
     */
    public static function getSubcategoryConfig(string $category, string $subcategory): ?array
    {
        $subcategories = self::getSubcategories($category);
        return $subcategories[$subcategory] ?? null;
    }

    /**
     * Generate product URL
     */
    public static function productUrl($product): string
    {
        if (is_string($product)) {
            $slug = $product;
            $product = Product::where('slug', $slug)->first();
            
            if (!$product) {
                return url("/products/{$slug}");
            }
        }
        
        // Determinar categoría por product_type
        $category = self::getCategoryByProductType($product->product_type_id);
        $prefix = self::getUrlPrefix($category);
        
        return url("/{$prefix}/{$product->slug}");
    }

    /**
     * Generate category URL
     */
    public static function categoryUrl(string $category): string
    {
        $prefix = self::getUrlPrefix($category);
        return url("/{$prefix}");
    }

    /**
     * Generate subcategory URL
     */
    public static function subcategoryUrl(string $category, string $subcategory): string
    {
        $prefix = self::getUrlPrefix($category);
        return url("/{$prefix}/{$subcategory}");
    }

    /**
     * Check if category exists
     */
    public static function categoryExists(string $category): bool
    {
        return !is_null(self::getCategoryConfig($category));
    }

    /**
     * Check if subcategory exists
     */
    public static function subcategoryExists(string $category, string $subcategory): bool
    {
        $subcategories = self::getSubcategories($category);
        return isset($subcategories[$subcategory]);
    }

    /**
     * Get Schema.org type for category
     */
    public static function getSchemaType(string $category): string
    {
        $config = self::getCategoryConfig($category);
        return $config['schema_type'] ?? 'Product';
    }

    /**
     * Get meta keywords for category
     */
    public static function getMetaKeywords(string $category): array
    {
        $config = self::getCategoryConfig($category);
        return $config['meta_keywords'] ?? [];
    }

    /**
     * Get all subcategory slugs for a category
     */
    public static function getSubcategorySlugs(string $category): array
    {
        $subcategories = self::getSubcategories($category);
        return array_keys($subcategories);
    }

    /**
     * Get category by product type ID
     * Maps product_type_id to category
     */
    private static function getCategoryByProductType(?int $productTypeId): string
    {
        // Por ahora retornamos 'guided_tour' como default
        // TODO: Implementar mapeo real de product_type_id a category
        return 'guided_tour';
    }
}
