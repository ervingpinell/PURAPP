<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Agregar columna JSON
        Schema::table('customer_categories', function (Blueprint $table) {
            $table->json('name')->nullable()->after('slug');
        });

        // 2. Data Migration logic handled by console command, but for safety can be here too.
        // For this task, we will rely on the Helper Command to be run separately or call it here?
        // Let's implement migration logic directly here to be self-contained as per guide "Paso 1: Migración"
        
        // However, standard Practice: Schema change in migration, Data migration in seeder or command.
        // The guide suggested doing it in migration "up". Let's follow the guide for consistency.
        
        $categories = DB::table('customer_categories')->get();

        foreach ($categories as $category) {
            // Check if translation table exists
            if (Schema::hasTable('customer_category_translations')) {
                $translations = DB::table('customer_category_translations')
                    ->where('category_id', $category->category_id)
                    ->get();

                $nameJson = [];
                foreach ($translations as $tr) {
                    $nameJson[$tr->locale] = $tr->name;
                }
                
                 // Fallback
                if (empty($nameJson['en'])) {
                    $nameJson['en'] = ucfirst(str_replace(['_', '-'], ' ', $category->slug));
                }

                if (!empty($nameJson)) {
                     DB::table('customer_categories')
                        ->where('category_id', $category->category_id)
                        ->update(['name' => json_encode($nameJson, JSON_UNESCAPED_UNICODE)]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::table('customer_categories', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }
};
