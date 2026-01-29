<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Schedule, Product, ProductAvailability, ProductExcludedDate};
use App\Services\Bookings\BookingCapacityService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\LoggerHelper;

/**
 * CapacityController
 *
 * Handles capacity operations.
 */
class CapacityController extends Controller
{
    protected BookingCapacityService $capacityService;

    public function __construct(BookingCapacityService $capacityService)
    {
        $this->capacityService = $capacityService;
    }

    /**
     * AUMENTAR capacidad (incremento relativo)
     * Body: { amount:int, date:"Y-m-d", product_id:int }
     */
    public function increase(Schedule $schedule, Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:tours,product_id'],
            'amount'  => ['required', 'integer', 'min:-999', 'max:999'], // Permite negativos
            'date'    => ['required', 'date'],
        ]);

        $product = Product::findOrFail($data['product_id']);
        $date = Carbon::parse($data['date'])->toDateString();

        try {
            DB::beginTransaction();

            $override = ProductAvailability::where('product_id', $product->product_id)
                ->where('schedule_id', $schedule->schedule_id)
                ->where('date', $date)
                ->first();

            $wasBlocked = $override && $override->is_blocked;
            $snapshot = $this->capacityService->capacitySnapshot($product, $schedule, $date);
            $confirmed = (int)$snapshot['confirmed'];
            $currentMax = (int)$snapshot['max'];

            // Desbloquear o incrementar/decrementar
            if ($wasBlocked || $currentMax === 0) {
                $newMax = max($confirmed, $confirmed + (int)$data['amount']); // Mínimo = confirmados

                ProductExcludedDate::where('product_id', $product->product_id)
                    ->where('schedule_id', $schedule->schedule_id)
                    ->whereDate('start_date', $date)
                    ->delete();

                ProductAvailability::updateOrCreate(
                    [
                        'product_id'     => $product->product_id,
                        'schedule_id' => $schedule->schedule_id,
                        'date'        => $date,
                    ],
                    [
                        'is_active'    => true,
                        'is_blocked'   => false,
                        'max_capacity' => $newMax,
                    ]
                );
            } else {
                $newMax = max($confirmed, $currentMax + (int)$data['amount']); // Permite sumar/restar

                ProductAvailability::updateOrCreate(
                    [
                        'product_id'     => $product->product_id,
                        'schedule_id' => $schedule->schedule_id,
                        'date'        => $date,
                    ],
                    [
                        'is_active'    => true,
                        'is_blocked'   => false,
                        'max_capacity' => $newMax,
                    ]
                );
            }

            DB::commit();

            $updated = $this->capacityService->capacitySnapshot($product, $schedule, $date);
            $pct = $updated['max'] > 0 ? (int)floor(($updated['confirmed'] * 100) / $updated['max']) : 0;

            return response()->json([
                'ok'           => true,
                'used'         => $updated['confirmed'],
                'max_capacity' => $updated['max'],
                'remaining'    => $updated['available'],
                'pct'          => $pct,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            LoggerHelper::exception('CapacityController', 'increase', 'ProductAvailability', $product->product_id, $e);

            return response()->json([
                'ok'      => false,
                'message' => 'No se pudo aumentar la capacidad',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * VER DETALLES (próximos 30 días o fecha específica)
     * Query: ?product_id=X&date=Y (opcional)
     */
    public function show(Schedule $schedule, Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:tours,product_id'],
            'date'    => ['nullable', 'date'],
        ]);

        $product = Product::findOrFail($data['product_id']);
        $tz   = config('app.timezone', 'UTC');

        if (!empty($data['date'])) {
            $start = Carbon::parse($data['date'], $tz)->toDateString();
            $end   = $start;
        } else {
            $start = Carbon::now($tz)->toDateString();
            $end   = Carbon::now($tz)->addDays(30)->toDateString();
        }

        try {
            $dates = collect(\Carbon\CarbonPeriod::create($start, $end))->map->toDateString();
            $rows  = [];

            foreach ($dates as $date) {
                $snap = $this->capacityService->capacitySnapshot($product, $schedule, $date);
                $pct  = $snap['max'] > 0 ? (int)floor(($snap['confirmed'] * 100) / $snap['max']) : 0;

                $rows[] = [
                    'date'      => $date,
                    'product'   => $product->name,
                    'used'      => $snap['confirmed'],
                    'max'       => $snap['max'],
                    'remaining' => $snap['available'],
                    'pct'       => $pct,
                ];
            }

            return response()->json(['ok' => true, 'data' => $rows]);
        } catch (\Throwable $e) {
            LoggerHelper::exception('CapacityController', 'show', 'Product', $product->product_id, $e);

            return response()->json([
                'ok'      => false,
                'message' => 'No se pudieron cargar los detalles',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * BLOQUEAR fecha completa
     * Body: { date:"Y-m-d", product_id:int, reason?:string }
     */
    public function block(Schedule $schedule, Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:tours,product_id'],
            'date'    => ['required', 'date'],
            'reason'  => ['nullable', 'string', 'max:255'],
        ]);

        $product = Product::findOrFail($data['product_id']);
        $date = Carbon::parse($data['date'])->toDateString();

        try {
            DB::beginTransaction();

            // Verificar si ya está bloqueado
            $existing = ProductAvailability::where('product_id', $product->product_id)
                ->where('schedule_id', $schedule->schedule_id)
                ->where('date', $date)
                ->first();

            if ($existing && $existing->is_blocked) {
                DB::rollBack();
                return response()->json([
                    'ok'      => false,
                    'message' => 'Esta fecha ya está bloqueada',
                ], 422);
            }

            // Crear override de bloqueo
            ProductAvailability::updateOrCreate(
                [
                    'product_id'     => $product->product_id,
                    'schedule_id' => $schedule->schedule_id,
                    'date'        => $date,
                ],
                [
                    'is_active'    => true,
                    'is_blocked'   => true,
                    'max_capacity' => null,
                ]
            );

            // Bitácora en excluded_dates
            ProductExcludedDate::firstOrCreate(
                [
                    'product_id'     => $product->product_id,
                    'schedule_id' => $schedule->schedule_id,
                    'start_date'  => $date,
                    'end_date'    => $date,
                ],
                ['reason' => $data['reason'] ?? 'Bloqueo manual']
            );

            DB::commit();

            $snap = $this->capacityService->capacitySnapshot($product, $schedule, $date);

            LoggerHelper::mutated('CapacityController', 'block', 'ProductExcludedDate', $product->product_id, [
                'schedule_id' => $schedule->schedule_id,
                'date'        => $date,
            ]);

            return response()->json([
                'ok'           => true,
                'used'         => $snap['confirmed'],
                'max_capacity' => 0,
                'remaining'    => 0,
                'pct'          => 100,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            LoggerHelper::exception('CapacityController', 'block', 'ProductExcludedDate', null, $e);

            return response()->json([
                'ok'      => false,
                'message' => 'No se pudo bloquear',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
