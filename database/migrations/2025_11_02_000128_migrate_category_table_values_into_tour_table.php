<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migrar precios de adult_price y kid_price a la tabla tour_prices
     * Solo migra categorías: adult y kid
     */
    public function up(): void
    {
        // Asegurar que las tablas existan
        if (!Schema::hasTable('customer_categories') || !Schema::hasTable('tours') || !Schema::hasTable('tour_prices')) {
            echo "\n⚠️  Saltando migración: faltan tablas requeridas.\n";
            return;
        }

        $now = now();

        /**
         * Inserta una categoría si no existe.
         * Rellena condicionalmente columnas presentes en el esquema para evitar NOT NULL.
         */
        $ensureCategory = function (string $slug, string $name, array $defaults = []) use ($now) {
            $row = DB::table('customer_categories')->where('slug', $slug)->first();
            if ($row) return $row;

            $data = [
                'slug'       => $slug,
                'name'       => $name,
            ];

            // Agregar condicionalmente columnas si existen en la tabla
            if (Schema::hasColumn('customer_categories', 'display_name')) {
                $data['display_name'] = $defaults['display_name'] ?? $name;
            }
            if (Schema::hasColumn('customer_categories', 'age_from')) {
                // Evita NOT NULL: usa default seguro si no viene en $defaults
                $data['age_from'] = $defaults['age_from'] ?? 0;
            }
            if (Schema::hasColumn('customer_categories', 'age_to')) {
                $data['age_to'] = $defaults['age_to'] ?? 120;
            }
            if (Schema::hasColumn('customer_categories', 'priority')) {
                $data['priority'] = $defaults['priority'] ?? 0;
            }
            if (Schema::hasColumn('customer_categories', 'is_default')) {
                $data['is_default'] = $defaults['is_default'] ?? false;
            }
            if (Schema::hasColumn('customer_categories', 'is_active')) {
                $data['is_active'] = $defaults['is_active'] ?? true;
            }
            if (Schema::hasColumn('customer_categories', 'created_at')) {
                $data['created_at'] = $now;
            }
            if (Schema::hasColumn('customer_categories', 'updated_at')) {
                $data['updated_at'] = $now;
            }

            DB::table('customer_categories')->insert($data);

            return DB::table('customer_categories')->where('slug', $slug)->first();
        };

        // 1) Garantizar categorías mínimas con rangos de edad seguros
        //    (ajusta si tus reglas son otras; aquí: kid 0-12, adult 13-120)
        $adult = $ensureCategory('adult', 'Adult', [
            'display_name' => 'Adult',
            'age_from'     => 13,
            'age_to'       => 120,
            'priority'     => 0,
            'is_default'   => false,
            'is_active'    => true,
        ]);

        $kid = $ensureCategory('kid', 'Kid', [
            'display_name' => 'Child',
            'age_from'     => 0,
            'age_to'       => 12,
            'priority'     => 0,
            'is_default'   => false,
            'is_active'    => true,
        ]);

        // 2) Resolver PK de customer_categories (category_id o id)
        $catIdCol   = Schema::hasColumn('customer_categories', 'category_id') ? 'category_id' : 'id';
        $adultCatId = $adult->{$catIdCol} ?? null;
        $kidCatId   = $kid->{$catIdCol}   ?? null;

        if (!$adultCatId || !$kidCatId) {
            echo "\n❌ No fue posible resolver los IDs de categorías (adult/kid). Abortando migración.\n";
            return;
        }

        // 3) Preparar columnas opcionales de tour_prices
        $hasMinQty   = Schema::hasColumn('tour_prices', 'min_quantity');
        $hasMaxQty   = Schema::hasColumn('tour_prices', 'max_quantity');
        $hasIsActive = Schema::hasColumn('tour_prices', 'is_active');
        $hasCreated  = Schema::hasColumn('tour_prices', 'created_at');
        $hasUpdated  = Schema::hasColumn('tour_prices', 'updated_at');

        // 4) Contadores
        $migratedTours = 0;
        $adultPricesMigrated = 0;
        $kidPricesMigrated   = 0;

        // 5) Migrar por lotes sin Eloquent
        $tourIdCol = Schema::hasColumn('tours', 'tour_id') ? 'tour_id' : 'id';

        DB::table('tours')
            ->orderBy($tourIdCol)
            ->select($tourIdCol, 'adult_price', 'kid_price')
            ->chunk(100, function ($tours) use (
                $adultCatId, $kidCatId, $now,
                $hasMinQty, $hasMaxQty, $hasIsActive, $hasCreated, $hasUpdated,
                $tourIdCol, &$migratedTours, &$adultPricesMigrated, &$kidPricesMigrated
            ) {
                foreach ($tours as $tour) {
                    $hasAnyPrice = false;

                    // Adult
                    if (!is_null($tour->adult_price) && (float)$tour->adult_price > 0) {
                        $update = ['price' => $tour->adult_price];
                        if ($hasMinQty)   $update['min_quantity'] = 1;
                        if ($hasMaxQty)   $update['max_quantity'] = 12;
                        if ($hasIsActive) $update['is_active']    = true;
                        if ($hasUpdated)  $update['updated_at']   = $now;
                        if ($hasCreated)  $update['created_at']   = $now;

                        DB::table('tour_prices')->updateOrInsert(
                            [
                                'tour_id'     => $tour->{$tourIdCol},
                                'category_id' => $adultCatId,
                            ],
                            $update
                        );
                        $adultPricesMigrated++;
                        $hasAnyPrice = true;
                    }

                    // Kid
                    if (!is_null($tour->kid_price) && (float)$tour->kid_price > 0) {
                        $update = ['price' => $tour->kid_price];
                        if ($hasMinQty)   $update['min_quantity'] = 0;
                        if ($hasMaxQty)   $update['max_quantity'] = 12;
                        if ($hasIsActive) $update['is_active']    = true;
                        if ($hasUpdated)  $update['updated_at']   = $now;
                        if ($hasCreated)  $update['created_at']   = $now;

                        DB::table('tour_prices')->updateOrInsert(
                            [
                                'tour_id'     => $tour->{$tourIdCol},
                                'category_id' => $kidCatId,
                            ],
                            $update
                        );
                        $kidPricesMigrated++;
                        $hasAnyPrice = true;
                    }

                    if ($hasAnyPrice) {
                        $migratedTours++;
                    }
                }
            });

        // 6) Log consola
        echo "\n✅ Migración completada:\n";
        echo "   - Tours procesados: {$migratedTours}\n";
        echo "   - Precios de adultos migrados: {$adultPricesMigrated}\n";
        echo "   - Precios de niños migrados: {$kidPricesMigrated}\n";
        echo "\n💡 Nota: Solo se migraron 'adult' y 'kid'.\n\n";
    }

    /**
     * Rollback: Restaurar precios desde tour_prices a las columnas originales
     */
    public function down(): void
    {
        if (!Schema::hasTable('customer_categories') || !Schema::hasTable('tours') || !Schema::hasTable('tour_prices')) {
            echo "\n⚠️  Saltando rollback: faltan tablas requeridas.\n";
            return;
        }

        $catIdCol  = Schema::hasColumn('customer_categories', 'category_id') ? 'category_id' : 'id';
        $tourIdCol = Schema::hasColumn('tours', 'tour_id') ? 'tour_id' : 'id';

        $adult = DB::table('customer_categories')->where('slug', 'adult')->first();
        $kid   = DB::table('customer_categories')->where('slug', 'kid')->first();
        if (!$adult || !$kid) {
            echo "\n⚠️  No se pudieron encontrar las categorías para hacer rollback.\n";
            return;
        }

        $adultCatId = $adult->{$catIdCol} ?? null;
        $kidCatId   = $kid->{$catIdCol}   ?? null;

        if (!$adultCatId || !$kidCatId) {
            echo "\n⚠️  No se pudieron resolver los IDs de categorías para rollback.\n";
            return;
        }

        $restored = 0;

        DB::table('tours')->orderBy($tourIdCol)->select($tourIdCol)->chunk(100, function ($tours) use ($adultCatId, $kidCatId, $tourIdCol, &$restored) {
            foreach ($tours as $tour) {
                $adultPrice = DB::table('tour_prices')
                    ->where('tour_id', $tour->{$tourIdCol})
                    ->where('category_id', $adultCatId)
                    ->value('price');

                $kidPrice = DB::table('tour_prices')
                    ->where('tour_id', $tour->{$tourIdCol})
                    ->where('category_id', $kidCatId)
                    ->value('price');

                DB::table('tours')->where($tourIdCol, $tour->{$tourIdCol})->update([
                    'adult_price' => $adultPrice ?? 0,
                    'kid_price'   => $kidPrice ?? 0,
                    'updated_at'  => now(),
                ]);

                $restored++;
            }
        });

        echo "\n✅ Rollback completado: {$restored} tours restaurados.\n\n";
    }
};
