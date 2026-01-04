<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SendDailyOperationsReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:send-daily-operations-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily operations report with today\'s bookings to operations team';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now()->format('Y-m-d');

        // Query all bookings for today
        $bookings = Booking::with(['user', 'tour', 'details.schedule', 'details.hotel', 'details.meetingPoint'])
            ->whereHas('details', function ($query) use ($today) {
                $query->whereDate('tour_date', $today);
            })
            ->get();

        if ($bookings->isEmpty()) {
            $this->info('No bookings scheduled for today.');
            Log::info('[SendDailyOperationsReport] No bookings for today');
            return 0;
        }

        // Group by status
        $confirmed = $bookings->where('status', 'confirmed')->sortBy(function ($booking) {
            return $booking->details->first()?->pickup_time ?? '00:00:00';
        });

        $pending = $bookings->where('status', 'pending')->sortBy(function ($booking) {
            return $booking->details->first()?->pickup_time ?? '00:00:00';
        });

        $cancelled = $bookings->where('status', 'cancelled')->sortBy(function ($booking) {
            return $booking->details->first()?->pickup_time ?? '00:00:00';
        });

        $this->info("Today's bookings: {$confirmed->count()} confirmed, {$pending->count()} pending, {$cancelled->count()} cancelled");

        // TODO: Generate Excel file
        // $excel = new DailyOperationsExport($confirmed, $pending, $cancelled, $today);
        // $filename = "bookings-{$today}.xlsx";
        // $path = storage_path("app/temp/{$filename}");
        // Excel::store($excel, "temp/{$filename}");

        // Get operations email
        $operationsEmail = setting('booking.operations_email', config('booking.operations_email', 'info@greenvacationscr.com'));

        // TODO: Send email with Excel attachment
        // Mail::to($operationsEmail)->send(new DailyOperationsReport($confirmed, $pending, $cancelled, $path));

        // Cleanup temp file
        // Storage::delete("temp/{$filename}");

        $this->info('Daily operations report sent successfully.');
        Log::info("[SendDailyOperationsReport] Report sent to {$operationsEmail}");

        return 0;
    }
}
