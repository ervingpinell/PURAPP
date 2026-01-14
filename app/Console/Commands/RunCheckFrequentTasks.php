<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cart;

class RunCheckFrequentTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-frequent';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run frequent checks (every 5-10 mins) like expiring carts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 1. Expirar carritos activos vencidos (cada 5 min)
        try {
            Cart::query()
                ->where('is_active', true)
                ->whereNotNull('expires_at')
                ->where('expires_at', '<=', now())
                ->orderBy('cart_id')
                ->chunkById(500, function ($carts) {
                    foreach ($carts as $cart) {
                        $cart->forceExpire();
                    }
                });
            // Silent unless error, as it runs frequently
        } catch (\Exception $e) {
            $this->error('x Error expiring carts: ' . $e->getMessage());
        }

        return 0;
    }
}
