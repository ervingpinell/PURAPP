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
     * Migra los datos existentes de tour_types (name, description, duration)
     * a tour_type_translations con locale 'es' (español).
     */
    public function up(): void
    {
        // Obtener todos los tour types existentes
        $tourTypes = DB::table('tour_types')->get();

        foreach ($tourTypes as $tourType) {
            // Verificar si ya existe una traducción en español
            $existingTranslation = DB::table('tour_type_translations')
                ->where('tour_type_id', $tourType->tour_type_id)
                ->where('locale', 'es')
                ->first();

            if (!$existingTranslation) {
                // Crear traducción en español con los datos actuales
                DB::table('tour_type_translations')->insert([
                    'tour_type_id' => $tourType->tour_type_id,
                    'locale' => 'es',
                    'name' => $tourType->name ?? '',
                    'description' => $tourType->description,
                    'duration' => $tourType->duration,
                    'created_at' => $tourType->created_at ?? now(),
                    'updated_at' => $tourType->updated_at ?? now(),
                ]);
            } else {
                // Si existe pero tiene campos vacíos, actualizarlos
                $updates = [];

                if (empty($existingTranslation->name) && !empty($tourType->name)) {
                    $updates['name'] = $tourType->name;
                }

                if (empty($existingTranslation->description) && !empty($tourType->description)) {
                    $updates['description'] = $tourType->description;
                }

                if (empty($existingTranslation->duration) && !empty($tourType->duration)) {
                    $updates['duration'] = $tourType->duration;
                }

                if (!empty($updates)) {
                    $updates['updated_at'] = now();
                    DB::table('tour_type_translations')
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
     * Los datos originales permanecen en tour_types hasta la siguiente migración.
     */
    public function down(): void
    {
        // No se eliminan las traducciones creadas en el rollback
        // porque podrían haber sido editadas manualmente después de la migración
    }
};
