<?php

namespace App\Helpers;

use App\Helpers\ProductCategoryHelper;
use App\Models\Product;

class NavigationHelper
{
    /**
     * Get active categories for navigation
     * Only categories with active products
     */
    public static function getActiveCategories(): array
    {
        $allCategories = ProductCategoryHelper::getAllCategories();
        $activeCategories = [];
        
        foreach ($allCategories as $key => $config) {
            // Check if category has active products
            // Note: Using product_type_id for now since product_category doesn't exist yet
            $hasProducts = Product::active()
                ->where('product_type_id', '!=', null)
                ->exists();
            
            // For now, we'll assume all categories are active if products exist
            // TODO: Map product_type_id to category properly
            if ($hasProducts) {
                $activeCategories[$key] = $config;
            }
        }
        
        return $activeCategories;
    }

    /**
     * Should show services dropdown?
     * True if more than 2 categories
     */
    public static function shouldShowServicesDropdown(): bool
    {
        return count(self::getActiveCategories()) > 2;
    }

    /**
     * Get primary category (for operators with 1-2 categories)
     */
    public static function getPrimaryCategory(): ?array
    {
        $categories = self::getActiveCategories();
        
        if (empty($categories)) {
            return null;
        }
        
        return reset($categories);
    }

    /**
     * Get all services URL
     */
    public static function getAllServicesUrl(): string
    {
        return url('/services');
    }

    /**
     * Get services for footer (top N if many)
     */
    public static function getFooterCategories(int $limit = 3): array
    {
        $categories = self::getActiveCategories();
        
        if (count($categories) <= $limit) {
            return $categories;
        }
        
        return array_slice($categories, 0, $limit, true);
    }

    /**
     * Count remaining categories not shown in footer
     */
    public static function getRemainingCategoriesCount(int $shown = 3): int
    {
        $total = count(self::getActiveCategories());
        return max(0, $total - $shown);
    }
}
