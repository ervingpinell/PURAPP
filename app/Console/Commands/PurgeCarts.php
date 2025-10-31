<?php

namespace App\Console\Commands;

use App\Models\Cart;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PurgeCarts extends Command
{
    protected $signature = 'carts:purge {--dry-run : No borra, solo muestra conteos}';
    protected $description = 'Purga carritos expirados, inactivos antiguos y carritos vacíos según config/cart.php';

    public function handle(): int
    {
        $expiredAfterHours = (int) config('cart.purge_expired_after_hours', 24);
        $inactiveAfterDays = (int) config('cart.purge_inactive_after_days', 30);
        $emptyAfterDays    = (int) config('cart.purge_empty_after_days', 7);

        $now       = now();
        $expiredAt = $now->clone()->subHours($expiredAfterHours);
        $inactiveAt= $now->clone()->subDays($inactiveAfterDays);
        $emptyAt   = $now->clone()->subDays($emptyAfterDays);

        $dry = $this->option('dry-run');

        $expiredQuery = Cart::query()
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', $expiredAt);

        $inactiveQuery = Cart::query()
            ->where('is_active', false)
            ->where('created_at', '<=', $inactiveAt);

        $emptyQuery = Cart::query()
            ->whereDoesntHave('items')
            ->where('created_at', '<=', $emptyAt);

        $expiredIds  = $expiredQuery->pluck('cart_id')->all();
        $inactiveIds = $inactiveQuery->pluck('cart_id')->all();
        $emptyIds    = $emptyQuery->pluck('cart_id')->all();

        $ids = collect($expiredIds)
            ->merge($inactiveIds)
            ->merge($emptyIds)
            ->unique()
            ->values();

        $this->info("Candidatos a purgar: " . $ids->count());
        $this->line("- Expirados (<= {$expiredAfterHours}h): " . count($expiredIds));
        $this->line("- Inactivos (>{$inactiveAfterDays}d): " . count($inactiveIds));
        $this->line("- Vacíos (>{$emptyAfterDays}d): " . count($emptyIds));

        if ($dry || $ids->isEmpty()) {
            if ($dry) $this->comment('Dry-run: no se borró nada.');
            return self::SUCCESS;
        }

        $ids->chunk(500)->each(function ($chunk) {
            DB::transaction(function () use ($chunk) {
                DB::table('cart_items')->whereIn('cart_id', $chunk)->delete();
                DB::table('carts')->whereIn('cart_id', $chunk)->delete();
            });
        });

        $this->info('Purgados: '.$ids->count().' carritos (y sus items).');

        return self::SUCCESS;
    }
}
