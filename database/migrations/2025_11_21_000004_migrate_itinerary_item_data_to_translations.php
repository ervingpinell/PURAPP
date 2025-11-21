<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Migra los datos existentes de itinerary_items (title, description)
     * a itinerary_item_translations con locale 'es' (español).
     */
    public function up(): void
    {
        // Obtener todos los itinerary items existentes
        $items = DB::table('itinerary_items')->get();

        foreach ($items as $item) {
            // Verificar si ya existe una traducción en español
            $existingTranslation = DB::table('itinerary_item_translations')
                ->where('item_id', $item->item_id)
                ->where('locale', 'es')
                ->first();

            if (!$existingTranslation) {
                // Crear traducción en español con los datos actuales
                DB::table('itinerary_item_translations')->insert([
                    'item_id' => $item->item_id,
                    'locale' => 'es',
                    'title' => $item->title ?? '',
                    'description' => $item->description,
                    'created_at' => $item->created_at ?? now(),
                    'updated_at' => $item->updated_at ?? now(),
                ]);
            } else {
                // Si existe pero tiene campos vacíos, actualizarlos
                $updates = [];

                if (empty($existingTranslation->title) && !empty($item->title)) {
                    $updates['title'] = $item->title;
                }

                if (empty($existingTranslation->description) && !empty($item->description)) {
                    $updates['description'] = $item->description;
                }

                if (!empty($updates)) {
                    $updates['updated_at'] = now();
                    DB::table('itinerary_item_translations')
                        ->where('id', $existingTranslation->id)
                        ->update($updates);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     * 
     * No hay rollback porque no eliminamos datos, solo los copiamos.
     * Los datos originales permanecen en itinerary_items hasta la siguiente migración.
     */
    public function down(): void
    {
        // No se eliminan las traducciones creadas en el rollback
        // porque podrían haber sido editadas manualmente después de la migración
    }
};
