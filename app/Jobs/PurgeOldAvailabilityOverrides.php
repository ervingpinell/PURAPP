<?php

namespace App\Jobs;

use App\Models\TourAvailability;
use App\Services\LoggerHelper;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class PurgeOldAvailabilityOverrides implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var int días hacia atrás para considerar “pasadas estrictamente” */
    public int $daysAgo = 0;
    /** @var bool si true, sólo toca overrides inactivos */
    public bool $onlyInactive = false;
    /** @var bool si true, conserva bloqueados históricos */
    public bool $keepBlocked = true;
    /** @var int tope de borrado por corrida */
    public int $limit = 20000;
    /** @var int tamaño de chunk para procesar */
    public int $chunk = 1000;
    /** @var bool si true, no borra: sólo simula */
    public bool $dryRun = false;

    public function __construct(array $opts = [])
    {
        foreach ($opts as $k => $v) {
            if (property_exists($this, $k)) {
                $this->{$k} = $v;
            }
        }

        // Asignar cola si viene en opciones, o dejar que el scheduler la ponga con ->onQueue('maintenance')
        if (!empty($opts['queue'])) {
            $this->onQueue((string) $opts['queue']);
        }
    }

    public function handle(): void
    {
        $cutoff = Carbon::today()->subDays($this->daysAgo)->toDateString();

        $q = TourAvailability::query()
            ->whereDate('date', '<', $cutoff);

        if ($this->onlyInactive) {
            $q->where('is_active', false);
        }

        if ($this->keepBlocked) {
            // Conserva bloqueados históricos (is_blocked = 1 o max_capacity = 0)
            $q->where(function ($qq) {
                $qq->where(function ($q2) {
                    // caso normal elegible
                    $q2->whereNull('max_capacity')->where('is_blocked', false);
                })->orWhere(function ($q3) {
                    // excluye bloqueados efectivos
                    $q3->whereNot(function ($q4) {
                        $q4->where('is_blocked', true)
                           ->orWhere('max_capacity', 0);
                    });
                });
            });
        }

        $totalCandidates = (clone $q)->count();

        $this->logInfo('purge:start', "Starting purge of old overrides", [
            'cutoff'       => $cutoff,
            'daysAgo'      => $this->daysAgo,
            'onlyInactive' => $this->onlyInactive,
            'keepBlocked'  => $this->keepBlocked,
            'limit'        => $this->limit,
            'chunk'        => $this->chunk,
            'dryRun'       => $this->dryRun,
            'candidates'   => $totalCandidates,
        ]);

        if ($totalCandidates === 0) {
            $this->logInfo('purge:noop', 'No matching overrides to purge.', ['cutoff' => $cutoff]);
            return;
        }

        if ($this->dryRun) {
            $wouldDelete = min($totalCandidates, $this->limit);
            $this->logInfo('purge:dry-run', "Dry-run: would delete {$wouldDelete} overrides.", [
                'candidates' => $totalCandidates,
                'limit'      => $this->limit,
            ]);
            return;
        }

        $deleted = 0;

        $idSub = (clone $q)
            ->select('availability_id')
            ->orderBy('availability_id')
            ->limit($this->limit);

        // Procesa en chunks por id
        DB::table('tour_availability')
            ->whereIn('availability_id', $idSub)
            ->orderBy('availability_id')
            ->chunkById($this->chunk, function ($rows) use (&$deleted) {
                $ids = collect($rows)->pluck('availability_id')->all();
                if ($ids) {
                    $deleted += TourAvailability::whereIn('availability_id', $ids)->delete();
                }
            }, 'availability_id', 'availability_id');

        $this->logInfo('purge:done', "Purge finished. Deleted {$deleted} overrides.", [
            'cutoff'    => $cutoff,
            'deleted'   => $deleted,
            'limit'     => $this->limit,
            'processed' => min($totalCandidates, $this->limit),
        ]);
    }

    private function logInfo(string $action, string $message, array $context = []): void
    {
        if (!class_exists(LoggerHelper::class)) {
            logger()->info("[PurgeOldAvailabilityOverrides][$action] {$message}", $context);
            return;
        }

        try {
            LoggerHelper::info('PurgeOldAvailabilityOverrides', $action, $message, $context);
        } catch (\Throwable $e) {
            logger()->info("[PurgeOldAvailabilityOverrides][$action] {$message}", $context + [
                '_logger_error' => $e->getMessage(),
            ]);
        }
    }
}
