<?php

namespace App\Console\Commands;

use App\Models\Product;
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
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete tour drafts older than X days that have not been completed';

    /**
     * Execute the console command
     */
    public function handle(): int
    {
        $days = $this->option('days');
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        $this->info("Searching for drafts older than {$days} days...");
        $this->newLine();

        // Calcular fecha de corte
        $cutoffDate = Carbon::now()->subDays($days);

        // Buscar drafts antiguos
        $oldDrafts = Product::where('is_draft', true)
            ->where('updated_at', '<', $cutoffDate)
            ->with(['tourType', 'languages', 'created_by_user'])
            ->get();

        $count = $oldDrafts->count();

        if ($count === 0) {
            $this->info('No old drafts found.');
            return Command::SUCCESS;
        }

        // Mostrar tabla con los drafts encontrados
        $this->warn("Found {$count} draft(s):");
        $this->newLine();

        $tableData = $oldDrafts->map(function ($draft) {
            return [
                $draft->product_id,
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
            $this->info('DRY-RUN Mode: Nothing will be deleted.');
            $this->info('   Ejecuta sin --dry-run para eliminar realmente.');
            return Command::SUCCESS;
        }

        // Confirmación (a menos que sea --force)
        if (!$force) {
            if (!$this->confirm("¿Deseas eliminar estos {$count} borrador(es)?", false)) {
                $this->info('Operation cancelled.');
                return Command::SUCCESS;
            }
        }

        // Procesar eliminación
        $this->info('Deleting drafts...');
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
                    $tourId = $draft->product_id;
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
                        'product_id' => $draft->product_id,
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
                $this->info("Successfully deleted {$deletedCount} draft(s).");
            }

            if (!empty($errors)) {
                $this->newLine();
                $this->error("Errors occurred while deleting " . count($errors) . " draft(s):");
                $this->table(
                    ['ID', 'Nombre', 'Error'],
                    collect($errors)->map(fn($e) => [$e['product_id'], $e['name'], $e['error']])->toArray()
                );
            }

            // Log final
            $this->newLine();
            $this->info("Summary:");
            $this->info("   • Total encontrados: {$count}");
            $this->info("   • Eliminados: {$deletedCount}");
            $this->info("   • Errores: " . count($errors));

            return Command::SUCCESS;
        } catch (\Exception $e) {
            DB::rollBack();

            $this->newLine(2);
            $this->error('Critical error during deletion:');
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
