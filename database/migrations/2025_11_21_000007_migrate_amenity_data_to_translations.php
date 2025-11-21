<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get all amenities
        $amenities = DB::table('amenities')->get();

        foreach ($amenities as $amenity) {
            // Check if translation already exists
            $existingTranslation = DB::table('amenity_translations')
                ->where('amenity_id', $amenity->amenity_id)
                ->where('locale', 'es')
                ->first();

            if ($existingTranslation) {
                // Update only if the existing translation fields are empty
                $updates = [];

                if (empty($existingTranslation->name) && !empty($amenity->name)) {
                    $updates['name'] = $amenity->name;
                }

                if (!empty($updates)) {
                    $updates['updated_at'] = now();
                    DB::table('amenity_translations')
                        ->where('amenity_id', $amenity->amenity_id)
                        ->where('locale', 'es')
                        ->update($updates);
                }
            } else {
                // Create new translation
                DB::table('amenity_translations')->insert([
                    'amenity_id' => $amenity->amenity_id,
                    'locale'     => 'es',
                    'name'       => $amenity->name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse - we're only copying data, not removing it
    }
};
