<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cart;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanupExpiredCartReservations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cart:cleanup-reservations
                            {--minutes=30 : Minutes after which a reservation expires}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup expired cart reservations and unreserve items';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expirationMinutes = (int) $this->option('minutes');
        $cutoffTime = now()->subMinutes($expirationMinutes);

        $this->info("Cleaning up cart reservations older than {$expirationMinutes} minutes...");

        $cleaned = 0;
        $bookingsDeleted = 0;

        // Find carts with expired reservations
        $carts = Cart::where('is_active', true)
            ->whereHas('items', function ($q) use ($cutoffTime) {
                $q->where('is_reserved', true)
                    ->where('reserved_at', '<', $cutoffTime);
            })
            ->get();

        foreach ($carts as $cart) {
            DB::transaction(function () use ($cart, $cutoffTime, &$cleaned, &$bookingsDeleted) {
                // Get expired reservation tokens
                $expiredTokens = $cart->items()
                    ->where('is_reserved', true)
                    ->where('reserved_at', '<', $cutoffTime)
                    ->pluck('reservation_token')
                    ->unique()
                    ->filter();

                foreach ($expiredTokens as $token) {
                    // Find pending bookings created with this reservation
                    $pendingBookings = Booking::where('status', 'pending')
                        ->whereDoesntHave('payments', function ($q) {
                            $q->where('status', 'completed');
                        })
                        ->where('created_at', '>=', $cutoffTime->subMinutes(5)) // Safety margin
                        ->where('user_id', $cart->user_id)
                        ->get();

                    // Delete unpaid pending bookings
                    foreach ($pendingBookings as $booking) {
                        $booking->delete(); // Soft delete
                        $bookingsDeleted++;
                    }

                    // Unreserve cart items
                    $cart->items()
                        ->where('reservation_token', $token)
                        ->update([
                            'is_reserved' => false,
                            'reserved_at' => null,
                            'reservation_token' => null,
                        ]);

                    $cleaned++;
                }
            });
        }

        $this->info("✅ Cleaned {$cleaned} expired reservations");
        $this->info("✅ Deleted {$bookingsDeleted} unpaid pending bookings");

        Log::info('Cart reservations cleanup completed', [
            'reservations_cleaned' => $cleaned,
            'bookings_deleted' => $bookingsDeleted,
            'expiration_minutes' => $expirationMinutes,
        ]);

        return Command::SUCCESS;
    }
}
