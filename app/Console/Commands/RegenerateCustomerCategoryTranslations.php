<?php

namespace App\Console\Commands;

use App\Models\CustomerCategory;
use App\Models\CustomerCategoryTranslation;
use App\Services\DeepLTranslator;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class RegenerateCustomerCategoryTranslations extends Command
{
    protected $signature = 'customer-categories:translate
        {--from= : Locale semilla (ej: es, en, fr, pt, de) o "auto" para detectar desde la primera traducción disponible}
        {--locales= : Lista destino separada por coma (ej: es,en,fr,pt,de). Por defecto supported_locales()}
        {--only-missing : Sólo completar locales faltantes (no pisa las existentes)}
        {--reset : Elimina traducciones existentes en los locales destino antes de regenerar}
        {--id= : ID específico de category_id (o varios separados por coma) a procesar; si no, procesa todas}
        {--chunk=100 : Tamaño de chunk para iterar}
        {--dry : Dry-run: no escribe cambios, sólo muestra lo que haría}';

    protected $description = 'Regenera/completa traducciones de CustomerCategory usando DeepL.';

    public function handle(DeepLTranslator $translator): int
    {
        // === Resolver opciones ===
        $fromOpt     = trim((string) $this->option('from') ?: '');
        $fromLocale  = $fromOpt !== '' ? strtolower(substr($fromOpt, 0, 2)) : null;

        $destOpt     = trim((string) $this->option('locales') ?: '');
        $destLocales = $destOpt !== ''
            ? array_values(array_unique(array_filter(array_map(fn($x)=> substr(strtolower(trim($x)),0,2), explode(',', $destOpt)))))
            : (function_exists('supported_locales') ? supported_locales() : ['es','en','fr','pt','de']);

        $onlyMissing = (bool) $this->option('only-missing');
        $reset       = (bool) $this->option('reset');
        $dry         = (bool) $this->option('dry');

        $chunkSize   = (int) $this->option('chunk') ?: 100;

        $idsOpt      = trim((string) $this->option('id') ?: '');
        $idsFilter   = $idsOpt !== '' ? array_filter(array_map('intval', explode(',', $idsOpt))) : null;

        // Mostrar config
        $this->info('=== Customer Categories Translation Regenerator ===');
        $this->line('From locale     : ' . ($fromLocale ?: 'auto'));
        $this->line('Target locales  : ' . implode(',', $destLocales));
        $this->line('Only missing    : ' . ($onlyMissing ? 'yes' : 'no'));
        $this->line('Reset existing  : ' . ($reset ? 'yes' : 'no'));
        $this->line('Dry run         : ' . ($dry ? 'yes' : 'no'));
        $this->line('Chunk size      : ' . $chunkSize);
        if ($idsFilter) $this->line('IDs             : ' . implode(',', $idsFilter));
        $this->newLine();

        // Query base
        $query = CustomerCategory::query()->with('translations');
        if ($idsFilter) $query->whereIn('category_id', $idsFilter);

        $total = (clone $query)->count();
        if ($total === 0) {
            $this->warn('No hay categorías que procesar.');
            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $created = 0;
        $updated = 0;
        $skipped = 0;

        $query->orderBy('category_id')->chunk($chunkSize, function ($chunk) use (
            $translator, $fromLocale, $destLocales, $onlyMissing, $reset, $dry, &$created, &$updated, &$skipped, $bar
        ) {
            foreach ($chunk as $category) {
                /** @var \App\Models\CustomerCategory $category */
                $category->loadMissing('translations');

                // 1) Determinar texto semilla
                [$seedText, $seedLoc] = $this->resolveSeed($category, $fromLocale);
                if ($seedText === null || trim($seedText) === '') {
                    $skipped++;
                    $bar->advance();
                    continue;
                }

                // 2) Reset opcional (borra destinos)
                if ($reset && !$dry) {
                    CustomerCategoryTranslation::where('category_id', $category->category_id)
                        ->whereIn('locale', $destLocales)
                        ->delete();
                }

                // 3) Generar/actualizar destinos
                foreach ($destLocales as $loc) {
                    $loc = substr($loc, 0, 2);
                    $existing = $category->translations->firstWhere('locale', $loc);

                    if ($onlyMissing && $existing && !empty($existing->name)) {
                        // Mantener existente
                        continue;
                    }

                    // Si destino == seed locale, usar texto semilla tal cual
                    $value = ($loc === $seedLoc) ? $seedText : $translator->translate($seedText, $loc);

                    if ($dry) {
                        $this->line(sprintf(
                            '[dry] category_id=%d  %s => %s : "%s"',
                            $category->category_id, strtoupper($seedLoc), strtoupper($loc), $value
                        ));
                        continue;
                    }

                    // Upsert
                    $res = CustomerCategoryTranslation::updateOrCreate(
                        ['category_id' => $category->category_id, 'locale' => $loc],
                        ['name' => $value]
                    );

                    if ($existing) {
                        if ($res->wasChanged('name')) $updated++;
                    } else {
                        $created++;
                    }
                }

                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);
        $this->info("Hecho. created={$created}, updated={$updated}, skipped={$skipped}");

        return self::SUCCESS;
    }

    /**
     * Determina el texto semilla y su locale.
     * - Si $fromLocale viene, intenta ese primero.
     * - Si no, usa la primera traducción disponible en orden de preferencia: app()->getLocale(), 'es', cualquiera.
     */
    private function resolveSeed(CustomerCategory $category, ?string $fromLocale): array
    {
        $pick = fn($l) => optional($category->translations->firstWhere('locale', substr($l,0,2)))->name;

        $seedText = null;
        $seedLoc  = null;

        if ($fromLocale) {
            $seedText = $pick($fromLocale);
            $seedLoc  = substr($fromLocale, 0, 2);
        }

        if (!$seedText) {
            $pref = [
                substr(app()->getLocale() ?? 'es', 0, 2),
                'es',
            ];
            foreach ($pref as $l) {
                $t = $pick($l);
                if ($t) { $seedText = $t; $seedLoc = $l; break; }
            }
        }

        if (!$seedText) {
            // Último recurso: cualquiera existente
            $first = $category->translations->first();
            if ($first) {
                $seedText = $first->name;
                $seedLoc  = substr($first->locale, 0, 2);
            }
        }

        if ($seedText) {
            // Normaliza seedLoc si viene null
            $seedLoc = $seedLoc ?: 'es';
            return [$seedText, substr($seedLoc,0,2)];
        }

        return [null, null];
    }
}
