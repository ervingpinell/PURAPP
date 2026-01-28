<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Force drop if exists (cleanup zombie state)
        Schema::dropIfExists('tour_type_tour_order');

        if (!Schema::hasTable('tour_type_tour_order')) {
            Schema::create('tour_type_tour_order', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tour_type_id');
                $table->unsignedBigInteger('tour_id');
                $table->unsignedInteger('position')->default(0);
                $table->timestamps();

                // New unique index name
                $table->unique(['tour_type_id', 'tour_id'], 'uniq_pt_p_order');
                
                // Explicit index name to avoid collision
                $table->index(['tour_type_id', 'position'], 'idx_tt_to_pos');

                // Foreign Keys apuntando a las tablas NUEVAS (product_types, product2)
                $table->foreign('tour_type_id', 'fk_tt_to_type')
                    ->references('product_type_id')->on('product_types')
                    ->onDelete('cascade');

                $table->foreign('tour_id', 'fk_tt_to_prod')
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
                        DB::table('tour_type_tour_order')->insert([
                            'tour_type_id' => $typeId,
                            'tour_id'      => $it->product_id,
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
        Schema::dropIfExists('tour_type_tour_order');
    }
};
