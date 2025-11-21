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
     * Migra los datos existentes de itineraries (name, description)
     * a itinerary_translations con locale 'es' (español).
     */
    public function up(): void
    {
        // Obtener todos los itinerarios existentes
        $itineraries = DB::table('itineraries')->get();

        foreach ($itineraries as $itinerary) {
            // Verificar si ya existe una traducción en español
            $existingTranslation = DB::table('itinerary_translations')
                ->where('itinerary_id', $itinerary->itinerary_id)
                ->where('locale', 'es')
                ->first();

            if (!$existingTranslation) {
                // Crear traducción en español con los datos actuales
                DB::table('itinerary_translations')->insert([
                    'itinerary_id' => $itinerary->itinerary_id,
                    'locale' => 'es',
                    'name' => $itinerary->name ?? '',
                    'description' => $itinerary->description,
                    'created_at' => $itinerary->created_at ?? now(),
                    'updated_at' => $itinerary->updated_at ?? now(),
                ]);
            } else {
                // Si existe pero tiene campos vacíos, actualizarlos
                $updates = [];

                if (empty($existingTranslation->name) && !empty($itinerary->name)) {
                    $updates['name'] = $itinerary->name;
                }

                if (empty($existingTranslation->description) && !empty($itinerary->description)) {
                    $updates['description'] = $itinerary->description;
                }

                if (!empty($updates)) {
                    $updates['updated_at'] = now();
                    DB::table('itinerary_translations')
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
     * Los datos originales permanecen en itineraries hasta la siguiente migración.
     */
    public function down(): void
    {
        // No se eliminan las traducciones creadas en el rollback
        // porque podrían haber sido editadas manualmente después de la migración
    }
};
