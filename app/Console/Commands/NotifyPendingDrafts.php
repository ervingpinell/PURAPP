<?php

namespace App\Console\Commands;

use App\Models\Tour;
use App\Models\User;
use App\Notifications\PendingDraftsReminder;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class NotifyPendingDrafts extends Command
{
    /**
     * Nombre y firma del comando
     *
     * @var string
     */
    protected $signature = 'tours:notify-pending-drafts
                            {--days=7 : Notificar drafts más antiguos que X días}
                            {--dry-run : Simular sin enviar notificaciones}';

    /**
     * Descripción del comando
     *
     * @var string
     */
    protected $description = 'Envía notificaciones a usuarios con drafts pendientes';

    /**
     * Execute the console command
     */
    public function handle(): int
    {
        $days = $this->option('days');
        $dryRun = $this->option('dry-run');

        $this->info("📧 Buscando usuarios con drafts pendientes (más de {$days} días)...");
        $this->newLine();

        $cutoffDate = Carbon::now()->subDays($days);

        // Obtener drafts antiguos agrupados por usuario
        $draftsByUser = Tour::where('is_draft', true)
            ->where('updated_at', '<', $cutoffDate)
            ->whereNotNull('created_by')
            ->with(['tourType', 'created_by_user'])
            ->get()
            ->groupBy('created_by');

        $usersCount = $draftsByUser->count();
        $totalDrafts = $draftsByUser->flatten()->count();

        if ($usersCount === 0) {
            $this->info('✓ No hay drafts pendientes que requieran notificación.');
            return Command::SUCCESS;
        }

        $this->warn("⚠️  Encontrados:");
        $this->info("   • {$usersCount} usuario(s) con drafts pendientes");
        $this->info("   • {$totalDrafts} draft(s) en total");
        $this->newLine();

        // Mostrar resumen por usuario
        $tableData = [];
        foreach ($draftsByUser as $userId => $drafts) {
            $user = $drafts->first()->created_by_user;
            if (!$user) continue;

            $tableData[] = [
                $user->name,
                $user->email,
                $drafts->count(),
                $drafts->min('updated_at')->format('d/m/Y'),
                $drafts->min('updated_at')->diffForHumans(),
            ];
        }

        $this->table(
            ['Usuario', 'Email', 'Drafts', 'Más Antiguo', 'Hace'],
            $tableData
        );

        $this->newLine();

        if ($dryRun) {
            $this->info('🏃 Modo DRY-RUN: No se enviarán notificaciones.');
            return Command::SUCCESS;
        }

        // Enviar notificaciones
        $this->info('📨 Enviando notificaciones...');
        $this->newLine();

        $progressBar = $this->output->createProgressBar($usersCount);
        $progressBar->start();

        $sentCount = 0;
        $errors = [];

        foreach ($draftsByUser as $userId => $drafts) {
            try {
                $user = User::find($userId);

                if (!$user || !$user->email) {
                    $errors[] = [
                        'user_id' => $userId,
                        'error' => 'Usuario no encontrado o sin email',
                    ];
                    continue;
                }

                // Enviar notificación
                $user->notify(new PendingDraftsReminder($drafts, $days));

                $sentCount++;

            } catch (\Exception $e) {
                $errors[] = [
                    'user_id' => $userId,
                    'error' => $e->getMessage(),
                ];
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Resultados
        if ($sentCount > 0) {
            $this->info("✓ Se enviaron {$sentCount} notificación(es) exitosamente.");
        }

        if (!empty($errors)) {
            $this->newLine();
            $this->error("⚠️  Hubo errores al enviar " . count($errors) . " notificación(es):");
            $this->table(
                ['User ID', 'Error'],
                collect($errors)->map(fn($e) => [$e['user_id'], $e['error']])->toArray()
            );
        }

        // Resumen final
        $this->newLine();
        $this->info("📊 Resumen:");
        $this->info("   • Usuarios notificados: {$sentCount}");
        $this->info("   • Errores: " . count($errors));

        return Command::SUCCESS;
    }

    /**
     * Definir el schedule en Kernel.php:
     *
     * // Enviar recordatorio semanal
     * $schedule->command('tours:notify-pending-drafts --days=7')
     *          ->weekly()
     *          ->mondays()
     *          ->at('09:00')
     *          ->appendOutputTo(storage_path('logs/draft-notifications.log'));
     *
     * // O recordatorio quincenal
     * $schedule->command('tours:notify-pending-drafts --days=14')
     *          ->twiceMonthly(1, 16, '09:00')
     *          ->appendOutputTo(storage_path('logs/draft-notifications.log'));
     */
}
