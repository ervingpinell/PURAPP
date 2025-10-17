<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tour_type_tour_order', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tour_type_id');
            $table->unsignedBigInteger('tour_id');
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->unique(['tour_type_id', 'tour_id'], 'uniq_type_tour');
            $table->index(['tour_type_id', 'position']);

            $table->foreign('tour_type_id')
                ->references('tour_type_id')->on('tour_types')
                ->onDelete('cascade');

            $table->foreign('tour_id')
                ->references('tour_id')->on('tours')
                ->onDelete('cascade');
        });

        // Backfill inicial: por cada tour con tour_type_id, crear su fila con position secuencial
        // (orden por nombre para que sea determinístico)
        $rows = DB::table('tours')
            ->select('tour_id','tour_type_id','name')
            ->whereNotNull('tour_type_id')
            ->orderBy('tour_type_id')
            ->orderBy('name', 'asc')
            ->get()
            ->groupBy('tour_type_id');

        foreach ($rows as $typeId => $items) {
            $pos = 1;
            foreach ($items as $it) {
                DB::table('tour_type_tour_order')->insert([
                    'tour_type_id' => $typeId,
                    'tour_id'      => $it->tour_id,
                    'position'     => $pos++,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_type_tour_order');
    }
};
