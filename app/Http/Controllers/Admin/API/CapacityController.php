<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\{Tour, Schedule};
use App\Services\Bookings\BookingCapacityService;
use Illuminate\Http\Request;

class CapacityController extends Controller
{
    public function snapshot(Request $request)
    {
        $data = $request->validate([
            'tour_id'      => ['required','exists:tours,tour_id'],
            'schedule_id'  => ['required','exists:schedules,schedule_id'],
            'date'         => ['required','date'],
            'exclude_booking_id' => ['nullable','integer'],
            'count_holds'  => ['sometimes','boolean'],
            'exclude_cart_id' => ['nullable','integer'],
        ]);

        $tour     = Tour::findOrFail($data['tour_id']);
        $schedule = Schedule::findOrFail($data['schedule_id']);

        $svc   = app(BookingCapacityService::class);
        $snap  = $svc->capacitySnapshot(
            $tour, $schedule, $data['date'],
            $data['exclude_booking_id'] ?? null,
            $request->boolean('count_holds', true),
            $data['exclude_cart_id'] ?? null
        );

        // opcional: nivel
        $snap['level'] = $svc->capacityLevel($tour, $schedule, $data['date']);

        return response()->json($snap);
    }

    /**
     * Precheck para intentar reservar X pax.
     * Body: { tour_id, schedule_id, date, quantity:int }
     */
    public function precheck(Request $request)
    {
        $data = $request->validate([
            'tour_id'     => ['required','exists:tours,tour_id'],
            'schedule_id' => ['required','exists:schedules,schedule_id'],
            'date'        => ['required','date'],
            'quantity'    => ['required','integer','min:1','max:9999'],
        ]);

        $tour     = Tour::findOrFail($data['tour_id']);
        $schedule = Schedule::findOrFail($data['schedule_id']);
        $svc      = app(BookingCapacityService::class);

        $snap = $svc->capacitySnapshot($tour, $schedule, $data['date']);

        $ok  = !$snap['blocked'] && $snap['available'] >= (int)$data['quantity'];

        return response()->json([
            'ok'         => $ok,
            'reason'     => $snap['blocked'] ? 'blocked' : ($ok ? 'ok' : 'insufficient'),
            'available'  => $snap['available'],
            'max'        => $snap['max'],
            'confirmed'  => $snap['confirmed'],
            'held'       => $snap['held'],
        ], $ok ? 200 : 409);
    }
}
