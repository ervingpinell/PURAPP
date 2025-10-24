<?php

namespace App\Jobs;

use App\Models\Cart;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ExpireCartsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public bool $deleteWhenMissingModels = true;

    /**
     * Optional: whether to purge old inactive carts in this run.
     */
    public function __construct(public bool $purge = false) {}

    /**
     * Ensure only one job runs at a time (per queue) to avoid races.
     */
    public function middleware(): array
    {
        // key can be any string; keep short and unique
        return [(new WithoutOverlapping('expire-carts-job'))->dontRelease()];
    }

    public function handle(): void
    {
        // 1) Expire active carts past expiration (or with null expires_at)
        Cart::query()
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '<=', now());
            })
            ->with('items')
            ->chunkById(200, function (Collection $carts) {
                foreach ($carts as $cart) {
                    DB::transaction(function () use ($cart) {
                        $cart->items()->delete();
                        $cart->forceFill([
                            'is_active'  => false,
                            'expires_at' => now(),
                        ])->save();
                    });
                }
            });

        // 2) Optional purge of old inactive carts
        if ($this->purge) {
            $days = (int) config('cart.purge_after_days', 30);

            Cart::query()
                ->where('is_active', false)
                ->where('updated_at', '<', now()->subDays($days))
                ->chunkById(200, function (Collection $carts) {
                    foreach ($carts as $cart) {
                        DB::transaction(function () use ($cart) {
                            $cart->items()->delete();
                            $cart->delete();
                        });
                    }
                });
        }
    }
}
