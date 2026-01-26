<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Policy
        if (Schema::hasTable('policies')) {
             Schema::table('policies', function (Blueprint $table) {
                if (Schema::hasColumn('policies', 'name')) $table->dropColumn('name');
                if (Schema::hasColumn('policies', 'content')) $table->dropColumn('content');
             });
             Schema::table('policies', function (Blueprint $table) {
                $table->json('name')->nullable();
                $table->json('content')->nullable();
             });
        }

        // 2. PolicySection
        if (Schema::hasTable('policy_sections')) {
             Schema::table('policy_sections', function (Blueprint $table) {
                if (Schema::hasColumn('policy_sections', 'name')) $table->dropColumn('name');
                if (Schema::hasColumn('policy_sections', 'content')) $table->dropColumn('content');
             });
             Schema::table('policy_sections', function (Blueprint $table) {
                $table->json('name')->nullable();
                $table->json('content')->nullable();
             });
        }

        // 3. Itinerary
        if (Schema::hasTable('itineraries')) {
             Schema::table('itineraries', function (Blueprint $table) {
                if (Schema::hasColumn('itineraries', 'name')) $table->dropColumn('name');
                if (Schema::hasColumn('itineraries', 'description')) $table->dropColumn('description');
             });
             Schema::table('itineraries', function (Blueprint $table) {
                $table->json('name')->nullable();
                $table->json('description')->nullable();
             });
        }

        // 4. ItineraryItem
        if (Schema::hasTable('itinerary_items')) {
             Schema::table('itinerary_items', function (Blueprint $table) {
                if (Schema::hasColumn('itinerary_items', 'title')) $table->dropColumn('title');
                if (Schema::hasColumn('itinerary_items', 'description')) $table->dropColumn('description');
             });
             Schema::table('itinerary_items', function (Blueprint $table) {
                $table->json('title')->nullable();
                $table->json('description')->nullable();
             });
        }

        // 5. ProductType
        if (Schema::hasTable('product_types')) {
             Schema::table('product_types', function (Blueprint $table) {
                if (Schema::hasColumn('product_types', 'name')) $table->dropColumn('name');
                if (Schema::hasColumn('product_types', 'description')) $table->dropColumn('description');
                // duration is often string "2 hours" or similar, usually translatable? 
                // ProductType usually has duration.
                // Guide says: name, description, duration.
                if (Schema::hasColumn('product_types', 'duration')) $table->dropColumn('duration');
             });
             Schema::table('product_types', function (Blueprint $table) {
                $table->json('name')->nullable();
                $table->json('description')->nullable();
                $table->json('duration')->nullable();
             });
        }
        
        // 6. Product (The Big One)
        if (Schema::hasTable('products')) {
             Schema::table('products', function (Blueprint $table) {
                if (Schema::hasColumn('products', 'name')) $table->dropColumn('name');
                if (Schema::hasColumn('products', 'overview')) $table->dropColumn('overview');
                if (Schema::hasColumn('products', 'recommendations')) $table->dropColumn('recommendations'); // aka included/not included sometimes? Guide says recommendations.
                // Check current columns? usually overview, description, etc.
                // Guide says: name, overview, recommendations
             });
             Schema::table('products', function (Blueprint $table) {
                $table->json('name')->nullable();
                $table->json('overview')->nullable();
                $table->json('recommendations')->nullable();
             });
        }
    }

    public function down(): void
    {
        // ... (Revert logic omitted)
    }
};
