<?php

namespace App\Console\Commands;

use App\Models\Tour;
use App\Models\TourAuditLog;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanOldDrafts extends Command
{
    /**
     * Nombre y firma del comando
     *
     * @var string
     */
    protected $signature = 'tours:clean-old-drafts
                            {--days=30 : Eliminar drafts más antiguos que X días}
                            {--dry-run : Simular sin eliminar realmente}
                            {--force : Forzar eliminación sin confirmación}';

    /**
     * Descripción del comando
     *
     * @var string
     */
    protected $description = 'Elimina borradores de tours más antiguos que X días que no han sido completados';

    /**
     * Execute the console command
     */
    public function handle(): int
    {
        $days = $this->option('days');
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        $this->info("🔍 Buscando borradores más antiguos que {$days} días...");
        $this->newLine();

        // Calcular fecha de corte
        $cutoffDate = Carbon::now()->subDays($days);

        // Buscar drafts antiguos
        $oldDrafts = Tour::where('is_draft', true)
            ->where('updated_at', '<', $cutoffDate)
            ->with(['tourType', 'languages', 'created_by_user'])
            ->get();

        $count = $oldDrafts->count();

        if ($count === 0) {
            $this->info('✓ No se encontraron borradores antiguos.');
            return Command::SUCCESS;
        }

        // Mostrar tabla con los drafts encontrados
        $this->warn("⚠️  Se encontraron {$count} borrador(es):");
        $this->newLine();

        $tableData = $oldDrafts->map(function ($draft) {
            return [
                $draft->tour_id,
                \Illuminate\Support\Str::limit($draft->name, 30),
                $draft->tourType?->name ?? 'N/A',
                $draft->current_step ?? 1,
                $draft->updated_at->format('d/m/Y'),
                $draft->updated_at->diffForHumans(),
                $draft->created_by_user?->name ?? 'Desconocido',
            ];
        })->toArray();

        $this->table(
            ['ID', 'Nombre', 'Tipo', 'Paso', 'Actualizado', 'Hace', 'Creador'],
            $tableData
        );

        $this->newLine();

        // Si es dry-run, solo mostrar y salir
        if ($dryRun) {
            $this->info('🏃 Modo DRY-RUN: No se eliminará nada.');
            $this->info('   Ejecuta sin --dry-run para eliminar realmente.');
            return Command::SUCCESS;
        }

        // Confirmación (a menos que sea --force)
        if (!$force) {
            if (!$this->confirm("¿Deseas eliminar estos {$count} borrador(es)?", false)) {
                $this->info('❌ Operación cancelada.');
                return Command::SUCCESS;
            }
        }

        // Procesar eliminación
        $this->info('🗑️  Eliminando borradores...');
        $this->newLine();

        $progressBar = $this->output->createProgressBar($count);
        $progressBar->start();

        $deletedCount = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            foreach ($oldDrafts as $draft) {
                try {
                    // Guardar info para log de auditoría
                    $tourId = $draft->tour_id;
                    $tourName = $draft->name;
                    $userId = $draft->created_by;

                    // Eliminar relaciones
                    $draft->languages()->detach();
                    $draft->amenities()->detach();
                    $draft->schedules()->detach();
                    $draft->prices()->delete();

                    // Eliminar itinerario si existe
                    if ($draft->itinerary_id && $draft->itinerary) {
                        $draft->itinerary->delete();
                    }

                    // Eliminar el draft
                    $draft->forceDelete();

                    // Registrar en auditoría
                    TourAuditLog::logAction(
                        action: 'draft_deleted',
                        tourId: $tourId,
                        userId: null, // Sistema
                        description: "Borrador '{$tourName}' eliminado automáticamente por antigüedad ({$days}+ días)",
                        context: 'system',
                        tags: ['auto-cleanup', 'scheduled']
                    );

                    $deletedCount++;
                } catch (\Exception $e) {
                    $errors[] = [
                        'tour_id' => $draft->tour_id,
                        'name' => $draft->name,
                        'error' => $e->getMessage(),
                    ];
                }

                $progressBar->advance();
            }

            DB::commit();

            $progressBar->finish();
            $this->newLine(2);

            // Resultados
            if ($deletedCount > 0) {
                $this->info("✓ Se eliminaron {$deletedCount} borrador(es) exitosamente.");
            }

            if (!empty($errors)) {
                $this->newLine();
                $this->error("⚠️  Hubo errores al eliminar " . count($errors) . " borrador(es):");
                $this->table(
                    ['ID', 'Nombre', 'Error'],
                    collect($errors)->map(fn($e) => [$e['tour_id'], $e['name'], $e['error']])->toArray()
                );
            }

            // Log final
            $this->newLine();
            $this->info("📊 Resumen:");
            $this->info("   • Total encontrados: {$count}");
            $this->info("   • Eliminados: {$deletedCount}");
            $this->info("   • Errores: " . count($errors));

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();

            $this->newLine(2);
            $this->error('❌ Error crítico durante la eliminación:');
            $this->error('   ' . $e->getMessage());

            return Command::FAILURE;
        }
    }

    /**
     * Definir el schedule en Kernel.php:
     *
     * $schedule->command('tours:clean-old-drafts --days=30 --force')
     *          ->weekly()
     *          ->sundays()
     *          ->at('02:00')
     *          ->appendOutputTo(storage_path('logs/draft-cleanup.log'));
     */
}
