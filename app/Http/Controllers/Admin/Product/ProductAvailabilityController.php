<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use Exception;
use App\Models\{Product, Schedule, ProductAvailability, ProductExcludedDate};
use App\Services\LoggerHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Http\Requests\Product\ProductAvailability\StoreProductAvailabilityRequest;
use App\Http\Requests\Product\ProductAvailability\UpdateProductAvailabilityRequest;
use Carbon\Carbon;

/**
 * ProductAvailabilityController
 *
 * Handles touravailability operations.
 */
class ProductAvailabilityController extends Controller
{
    public function __construct()
    {
        // View permission
        $this->middleware(['can:view-tour-availability'])->only(['index']);

        // Edit permissions (capacity updates, overrides)
        $this->middleware(['can:edit-tour-availability'])->only([
            'updateTourCapacity',
            'updateScheduleBaseCapacity',
            'upsertDayScheduleOverride',
            'store', // legacy
            'update', // legacy
            'destroy' // legacy
        ]);

        // Publish permission for blocking/unblocking
        $this->middleware(['can:publish-tour-availability'])->only(['toggleBlockDaySchedule']);
    }

    protected string $controller = 'ProductAvailabilityController';

    /**
     * Vista principal con tabs: Global, Por Tour+Horario, Día+Horario
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'global');

        // Capacidad global y schedules por tour
        $products = Product::with(['tourType', 'schedules' => function ($q) {
            $q->orderBy('start_time');
        }])->orderByRaw('name->>\'' . app()->getLocale() . '\' ASC')->get();

        // Schedules para referencia
        $schedules = Schedule::orderBy('start_time')->get();

        // Overrides por día+horario
        $dayScheduleOverrides = ProductAvailability::whereNotNull('schedule_id')
            ->with(['tour', 'schedule'])
            ->orderBy('date', 'desc')
            ->orderBy('product_id')
            ->orderBy('schedule_id')
            ->paginate(20, ['*'], 'day_schedule_page');

        return view('admin.products.capacity.index', compact(
            'tab',
            'products',
            'schedules',
            'dayScheduleOverrides'
        ));
    }

    /**
     * Capacidad GLOBAL de un tour (Tour.max_capacity).
     */
    public function updateTourCapacity(Request $request, Product $product)
    {
        $request->validate([
            'max_capacity' => 'required|integer|min:0|max:9999',
        ]);

        try {
            $product->update([
                'max_capacity' => $request->max_capacity,
            ]);

            LoggerHelper::mutated($this->controller, 'updateTourCapacity', 'tour', $product->product_id, [
                'max_capacity' => $request->max_capacity,
                'user_id'      => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('success', 'Capacidad global del tour actualizada correctamente.');
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'updateTourCapacity', 'tour', $product->product_id, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);
            return back()->with('error', 'Error al actualizar capacidad global.');
        }
    }

    /**
     * Capacidad BASE por HORARIO (pivot schedule_tour.base_capacity).
     * Body: { schedule_id:int, base_capacity:int|null }
     */
    public function updateScheduleBaseCapacity(Request $request, Product $product)
    {
        $data = $request->validate([
            'schedule_id'   => ['required', 'exists:schedules,schedule_id'],
            'base_capacity' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);

        try {
            $payload = [
                'base_capacity' => $data['base_capacity'],
                'updated_at'    => now(),
            ];

            if (Schema::hasColumn('schedule_tour', 'created_at')) {
                $payload['created_at'] = DB::raw("COALESCE(created_at, '" . now() . "')");
            }

            DB::table('schedule_tour')->updateOrInsert(
                ['product_id' => $product->product_id, 'schedule_id' => $data['schedule_id']],
                $payload
            );

            LoggerHelper::mutated($this->controller, 'updateScheduleBaseCapacity', 'schedule_tour', null, [
                'product_id'       => $product->product_id,
                'schedule_id'   => $data['schedule_id'],
                'base_capacity' => $data['base_capacity'],
                'user_id'       => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('success', 'Capacidad por horario actualizada.');
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'updateScheduleBaseCapacity', 'schedule_tour', null, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);
            return back()->with('error', 'Error al actualizar capacidad por horario.');
        }
    }

    /**
     * Override puntual por DÍA+HORARIO (upsert).
     * Body: { schedule_id:int, date:YYYY-MM-DD, max_capacity:int|null }
     * Nota: max_capacity = null => deja pasar a pivot/tour.
     */
    public function upsertDayScheduleOverride(Request $request, Product $product)
    {
        $data = $request->validate([
            'schedule_id'  => ['required', 'exists:schedules,schedule_id'],
            'date'         => ['required', 'date'],
            'max_capacity' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);

        try {
            $availability = ProductAvailability::updateOrCreate(
                [
                    'product_id'     => $product->product_id,
                    'schedule_id' => $data['schedule_id'],
                    'date'        => $data['date'],
                ],
                [
                    'is_blocked'   => false,
                    'max_capacity' => $data['max_capacity'], // null => sin override, usa jerarquía inferior
                    'is_active'    => true,
                ]
            );

            LoggerHelper::mutated($this->controller, 'upsertDayScheduleOverride', 'tour_availability', $availability->getKey(), [
                'was_recently_created' => $availability->wasRecentlyCreated,
                'product_id'     => $product->product_id,
                'schedule_id' => $data['schedule_id'],
                'date'        => $data['date'],
                'user_id'     => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('success', $availability->wasRecentlyCreated ? 'Override creado.' : 'Override actualizado.');
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'upsertDayScheduleOverride', 'tour_availability', null, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);
            return back()->with('error', 'Error al guardar override por día+horario.');
        }
    }

    /**
     * Bloqueo puntual por DÍA+HORARIO (y bitácora en ProductExcludedDate).
     * Body: { schedule_id:int|null, date:YYYY-MM-DD, block:boolean, reason?:string }
     *
     * Con la UNIQUE (product_id, schedule_id, date):
     * - Usamos updateOrCreate($conditions, $attrs) para no duplicar filas.
     * - Al DESBLOQUEAR no enviamos 'max_capacity' para conservar el valor previo.
     */
    public function toggleBlockDaySchedule(Request $request, Product $product)
    {
        $data = $request->validate([
            'schedule_id' => ['nullable', 'exists:schedules,schedule_id'],
            'date'        => ['required', 'date'],
            'block'       => ['required', 'boolean'],
            'reason'      => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $conditions = [
                'product_id'     => $product->product_id,
                // IMPORTANTE: puede ser NULL si usas override general del día
                'schedule_id' => $data['schedule_id'] ?? null,
                'date'        => \Carbon\Carbon::parse($data['date'])->toDateString(),
            ];

            // Atributos a actualizar (mínimos, para no tocar max_capacity al desbloquear)
            $attrs = [
                'is_active'  => true,
                'is_blocked' => (bool) $data['block'],
            ];

            if ($data['block']) {
                // Bloqueo real: forzamos max_capacity = null
                $attrs['max_capacity'] = null;
            }
            // Si desbloquea, NO incluimos 'max_capacity' => se conserva el valor previo

            $availability = \App\Models\ProductAvailability::updateOrCreate($conditions, $attrs);

            // Bitácora humana (un día) en ProductExcludedDate
            if ($data['block']) {
                \App\Models\ProductExcludedDate::firstOrCreate(
                    [
                        'product_id'     => $product->product_id,
                        'schedule_id' => $conditions['schedule_id'],
                        'start_date'  => $conditions['date'],
                        'end_date'    => $conditions['date'],
                    ],
                    ['reason' => $data['reason'] ?? 'Bloqueo puntual']
                );
            } else {
                ProductExcludedDate::where([
                    'product_id'     => $product->product_id,
                    'schedule_id' => $conditions['schedule_id'],
                ])->whereDate('start_date', $conditions['date'])->delete();
            }

            LoggerHelper::mutated(
                'ProductAvailabilityController',
                'toggleBlockDaySchedule',
                'tour_availability',
                $availability->getKey(),
                [
                    'product_id'     => $product->product_id,
                    'schedule_id' => $conditions['schedule_id'],
                    'date'        => $conditions['date'],
                    'is_blocked'  => (bool) $data['block'],
                    'user_id'     => optional($request->user())->getAuthIdentifier(),
                ]
            );

            return back()->with('success', $data['block'] ? 'Fecha bloqueada.' : 'Fecha desbloqueada.');
        } catch (\Throwable $e) {
            LoggerHelper::exception(
                'ProductAvailabilityController',
                'toggleBlockDaySchedule',
                'tour_availability',
                null,
                $e,
                ['user_id' => optional($request->user())->getAuthIdentifier()]
            );

            return back()->with('error', 'Error al alternar bloqueo.');
        }
    }


    /**
     * ===== Métodos legacy que ya usabas =====
     * Crear o actualizar override (día+horario) vía FormRequest.
     */
    public function store(StoreProductAvailabilityRequest $request)
    {
        try {
            $data = $request->validated();

            $conditions = [
                'product_id'     => $data['product_id'],
                'schedule_id' => $data['schedule_id'],
                'date'        => $data['date'],
            ];

            $attributes = ['is_active' => true];

            if (!empty($data['is_blocked'])) {
                $attributes['max_capacity'] = null;
                $attributes['is_blocked']   = true;
            } elseif (isset($data['max_capacity']) && $data['max_capacity'] !== '') {
                $attributes['max_capacity'] = (int) $data['max_capacity'];
                $attributes['is_blocked']   = false;
            } else {
                $attributes['max_capacity'] = null;
                $attributes['is_blocked']   = false;
            }

            $availability = \App\Models\ProductAvailability::updateOrCreate($conditions, $attributes);

            LoggerHelper::mutated($this->controller, 'store', 'tour_availability', $availability->getKey(), [
                'was_recently_created' => $availability->wasRecentlyCreated,
                'product_id'              => $data['product_id'],
                'schedule_id'          => $data['schedule_id'],
                'date'                 => $data['date'],
                'user_id'              => optional($request->user())->getAuthIdentifier(),
            ]);

            $message = $availability->wasRecentlyCreated
                ? 'Override de capacidad creado correctamente.'
                : 'Override de capacidad actualizado correctamente.';

            return back()->with('success', $message);
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'store', 'tour_availability', null, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);
            return back()->with('error', 'Error al guardar el override de capacidad.');
        }
    }

    /**
     * Actualizar override existente (legacy).
     * Si viene is_blocked=true, se borra max_capacity.
     */
    public function update(UpdateProductAvailabilityRequest $request, ProductAvailability $availability)
    {
        try {
            $data = $request->validated();

            $updateData = ['is_active' => true];

            if (isset($data['is_blocked'])) {
                $updateData['is_blocked'] = (bool) $data['is_blocked'];
                if ($updateData['is_blocked']) {
                    $updateData['max_capacity'] = null;
                }
            }

            if (array_key_exists('max_capacity', $data) && !($updateData['is_blocked'] ?? false)) {
                $updateData['max_capacity'] = $data['max_capacity'];
            }

            if (!empty($updateData)) {
                $availability->update($updateData);
            }

            LoggerHelper::mutated($this->controller, 'update', 'tour_availability', $availability->getKey(), [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('success', 'Override de capacidad actualizado.');
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'update', 'tour_availability', $availability->getKey(), $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', 'Error al actualizar override.');
        }
    }

    /**
     * Eliminar override (legacy).
     */
    public function destroy(ProductAvailability $availability)
    {
        try {
            $id = $availability->getKey();
            $availability->delete();

            LoggerHelper::mutated($this->controller, 'destroy', 'tour_availability', $id, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return back()->with('success', 'Override eliminado correctamente.');
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'destroy', 'tour_availability', $availability->getKey(), $e, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', 'Error al eliminar override.');
        }
    }
}
