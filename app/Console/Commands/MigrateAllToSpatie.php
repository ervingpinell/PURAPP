<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateAllToSpatie extends Command
{
    protected $signature = 'spatie:migrate-all';
    protected $description = 'Migrate all translation tables to Spatie JSON';

    // Configuración de modelos a migrar
    protected $models = [
        [
            'table' => 'customer_categories',
            'translation_table' => 'customer_category_translations',
            'pk' => 'category_id',
            'fields' => ['name']
        ],
        [
            'table' => 'amenities',
            'translation_table' => 'amenity_translations',
            'pk' => 'amenity_id',
            'fields' => ['name']
        ],
        [
            'table' => 'faqs',
            'translation_table' => 'faq_translations',
            'pk' => 'faq_id',
            'fields' => ['question', 'answer']
        ],
        [
            'table' => 'meeting_points',
            'translation_table' => 'meeting_point_translations',
            'pk' => 'id', // MeetingPoint usa 'id' como PK
            'fields' => ['name', 'description', 'instructions']
        ],
        [
            'table' => 'policy_sections',
            'translation_table' => 'policy_section_translations',
            'pk' => 'section_id',
            'fields' => ['name', 'content']
        ],
        [
            'table' => 'policies',
            'translation_table' => 'policies_translations',
            'pk' => 'policy_id',
            'fields' => ['name', 'content']
        ],
        [
            'table' => 'itinerary_items',
            'translation_table' => 'itinerary_item_translations',
            'pk' => 'item_id',
            'fields' => ['title', 'description']
        ],
        [
            'table' => 'itineraries',
            'translation_table' => 'itinerary_translations',
            'pk' => 'itinerary_id',
            'fields' => ['name', 'description']
        ],
        [
            'table' => 'product_types', // Tabla actualizada en Product2
            'translation_table' => 'product_type_translations',
            'pk' => 'product_type_id',
            'fields' => ['name', 'description', 'duration']
        ],
        [
            'table' => 'product2', // Renombrado de tours
            'translation_table' => 'product_translations', // Renombrado de tour_translations
            'pk' => 'product_id',
            'fields' => ['name', 'description', 'overview', 'recommendations']
        ],
    ];

    public function handle()
    {
        $this->info('🚀 Starting migration to Spatie...');

        foreach ($this->models as $config) {
            $this->migrateModel($config);
        }

        $this->info('✅ All migrations completed!');
    }

    protected function migrateModel(array $config)
    {
        $this->info("Migrating {$config['table']}...");

        // Verificar existencia de tablas
        if (!Schema::hasTable($config['table']) || !Schema::hasTable($config['translation_table'])) {
            $this->error("❌ Table {$config['table']} or {$config['translation_table']} does not exist. Skipping.");
            return;
        }

        $records = DB::table($config['table'])->get();

        foreach ($records as $record) {
            $translations = DB::table($config['translation_table'])
                ->where($config['pk'], $record->{$config['pk']})
                ->get();

            $updates = [];

            foreach ($config['fields'] as $field) {
                // Verificar si la columa JSON existe en la tabla principal
                if (!Schema::hasColumn($config['table'], $field)) {
                    // Si no existe, advertir (la migración de columna debe correr antes)
                    // Opcionalmente podríamos crearla on-the-fly pero mejor seguir el proceso de migración de Laravel
                    $this->warn("⚠️ Column '$field' does not exist in '{$config['table']}'. Skipping field.");
                    continue;
                }

                $json = [];
                foreach ($translations as $tr) {
                    if (isset($tr->$field)) {
                        $json[$tr->locale] = $tr->$field;
                    }
                }
                
                // Fallback para inglés si no existe (opcional, pero útil)
                if (empty($json['en']) && !empty($json)) {
                     // Si no hay inglés pero hay otros, usamos el primero como inglés (o dejamos vacío)
                     // En este caso, solo guardamos lo que tenemos.
                }

                if (!empty($json)) {
                    $updates[$field] = json_encode($json, JSON_UNESCAPED_UNICODE);
                }
            }

            if (!empty($updates)) {
                DB::table($config['table'])
                    ->where($config['pk'], $record->{$config['pk']})
                    ->update($updates);
            }
        }

        $this->info("✅ {$records->count()} records processed for {$config['table']}");
    }
}
