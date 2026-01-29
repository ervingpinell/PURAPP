<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Force drop if exists (cleanup zombie state)
        Schema::dropIfExists('product_type_product_order');

        if (!Schema::hasTable('product_type_product_order')) {
            Schema::create('product_type_product_order', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_type_id');
                $table->unsignedBigInteger('product_id');
                $table->unsignedInteger('position')->default(0);
                $table->timestamps();

                // New unique index name
                $table->unique(['product_type_id', 'product_id'], 'uniq_pt_p_order');
                
                // Explicit index name to avoid collision
                $table->index(['product_type_id', 'position'], 'idx_tt_to_pos');

                // Foreign Keys apuntando a las tablas NUEVAS (product_types, product2)
                $table->foreign('product_type_id', 'fk_tt_to_type')
                    ->references('product_type_id')->on('product_types')
                    ->onDelete('cascade');

                $table->foreign('product_id', 'fk_tt_to_prod')
                    ->references('product_id')->on('product2')
                    ->onDelete('cascade');
            });

            // Backfill inicial
            // Asegurar que hay datos de orden iniciales
            try {
                // Obtener productos agrupados por tipo
                // Nota: product2.name es JSON, ordenamos simple por ahora o por created_at
                $rows = DB::table('product2')
                    ->select('product_id', 'product_type_id', 'name')
                    ->whereNotNull('product_type_id')
                    ->whereNull('deleted_at')
                    ->orderBy('product_type_id')
                    ->orderBy('product_id') 
                    ->get()
                    ->groupBy('product_type_id');

                foreach ($rows as $typeId => $items) {
                    $pos = 1;
                    foreach ($items as $it) {
                        DB::table('product_type_product_order')->insert([
                            'product_type_id' => $typeId,
                            'product_id'      => $it->product_id,
                            'position'     => $pos++,
                            'created_at'   => now(),
                            'updated_at'   => now(),
                        ]);
                    }
                }
            } catch (\Throwable $e) {
                // Si falla el backfill (e.g. datos corruptos), no detener la migración
                // Loguear error opcionalmente
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_type_product_order');
    }
};
