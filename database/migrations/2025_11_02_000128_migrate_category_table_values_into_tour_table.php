<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Tour;
use App\Models\TourPrice;
use App\Models\CustomerCategory;

return new class extends Migration
{
    /**
     * Migrar precios de adult_price y kid_price a la tabla tour_prices
     * Solo migra categorías: adult y kid
     */
    public function up(): void
    {
        // Buscar categorías por slug
        $adultCategory = CustomerCategory::where('slug', 'adult')->first();
        $kidCategory = CustomerCategory::where('slug', 'kid')->first();

        // Validar que existan las categorías
        if (!$adultCategory) {
            throw new \Exception('❌ Categoría "adult" no encontrada. Crea las categorías antes de migrar.');
        }

        if (!$kidCategory) {
            throw new \Exception('❌ Categoría "kid" no encontrada. Crea las categorías antes de migrar.');
        }

        // Contador de tours migrados
        $migratedTours = 0;
        $adultPricesMigrated = 0;
        $kidPricesMigrated = 0;

        // Migrar precios de todos los tours existentes (en bloques de 100)
        Tour::withTrashed()
            ->chunk(100, function ($tours) use ($adultCategory, $kidCategory, &$migratedTours, &$adultPricesMigrated, &$kidPricesMigrated) {
                foreach ($tours as $tour) {
                    $hasPrices = false;

                    // Migrar precio de ADULTOS
                    if (!is_null($tour->adult_price) && $tour->adult_price > 0) {
                        TourPrice::updateOrCreate(
                            [
                                'tour_id' => $tour->tour_id,
                                'category_id' => $adultCategory->category_id,
                            ],
                            [
                                'price' => $tour->adult_price,
                                'min_quantity' => 1,  // Mínimo 1 adulto
                                'max_quantity' => 12, // Máximo 12 adultos
                                'is_active' => true,
                            ]
                        );
                        $adultPricesMigrated++;
                        $hasPrices = true;
                    }

                    // Migrar precio de NIÑOS
                    if (!is_null($tour->kid_price) && $tour->kid_price > 0) {
                        TourPrice::updateOrCreate(
                            [
                                'tour_id' => $tour->tour_id,
                                'category_id' => $kidCategory->category_id,
                            ],
                            [
                                'price' => $tour->kid_price,
                                'min_quantity' => 0,  // Mínimo 0 niños (opcional)
                                'max_quantity' => 12, // Máximo 12 niños
                                'is_active' => true,
                            ]
                        );
                        $kidPricesMigrated++;
                        $hasPrices = true;
                    }

                    if ($hasPrices) {
                        $migratedTours++;
                    }
                }
            });

        // Log de resultados en la consola
        echo "\n✅ Migración completada:\n";
        echo "   - Tours procesados: {$migratedTours}\n";
        echo "   - Precios de adultos migrados: {$adultPricesMigrated}\n";
        echo "   - Precios de niños migrados: {$kidPricesMigrated}\n";
        echo "\n💡 Nota: Las categorías 'senior' e 'infant' no se migraron (solo adult y kid).\n\n";
    }

    /**
     * Rollback: Restaurar precios desde tour_prices a las columnas originales
     */
    public function down(): void
    {
        $adultCategory = CustomerCategory::where('slug', 'adult')->first();
        $kidCategory = CustomerCategory::where('slug', 'kid')->first();

        if (!$adultCategory || !$kidCategory) {
            echo "\n⚠️  No se pudieron encontrar las categorías para hacer rollback.\n";
            return;
        }

        $restored = 0;

        Tour::withTrashed()->chunk(100, function ($tours) use ($adultCategory, $kidCategory, &$restored) {
            foreach ($tours as $tour) {
                // Buscar precio de adulto
                $adultPrice = TourPrice::where('tour_id', $tour->tour_id)
                    ->where('category_id', $adultCategory->category_id)
                    ->first();

                // Buscar precio de niño
                $kidPrice = TourPrice::where('tour_id', $tour->tour_id)
                    ->where('category_id', $kidCategory->category_id)
                    ->first();

                // Restaurar a las columnas originales
                $tour->update([
                    'adult_price' => $adultPrice ? $adultPrice->price : 0,
                    'kid_price' => $kidPrice ? $kidPrice->price : 0,
                ]);

                $restored++;
            }
        });

        echo "\n✅ Rollback completado: {$restored} tours restaurados.\n\n";
    }
};
