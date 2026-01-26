<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class ProductSubcategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔄 Asignando subcategorías a productos...');
        
        // Tours full-day (6+ horas)
        $updated = DB::table('product2')
            ->whereNull('subcategory')
            ->where('length', '>=', 6)
            ->update(['subcategory' => 'full-day']);
        $this->command->info("✅ {$updated} tours full-day asignados");
        
        // Tours half-day (2-5 horas)
        $updated = DB::table('product2')
            ->whereNull('subcategory')
            ->where('length', '<', 6)
            ->where('length', '>=', 2)
            ->update(['subcategory' => 'half-day']);
        $this->command->info("✅ {$updated} tours half-day asignados");
        
        // Tours multi-day (si existe campo days o duration > 24 horas)
        $updated = DB::table('product2')
            ->whereNull('subcategory')
            ->where('length', '>', 24)
            ->update(['subcategory' => 'multi-day']);
        $this->command->info("✅ {$updated} tours multi-day asignados");
        
        $this->command->info('✅ Subcategorías asignadas correctamente');
    }
}
