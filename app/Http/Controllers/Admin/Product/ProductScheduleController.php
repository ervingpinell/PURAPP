<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use Exception;
use App\Models\Product;
use App\Models\Schedule;
use App\Services\LoggerHelper;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Product\Schedule\StoreScheduleRequest;
use App\Http\Requests\Product\Schedule\UpdateScheduleRequest;
use App\Http\Requests\Product\Schedule\AttachScheduleToProductRequest;
use App\Http\Requests\Product\Schedule\ToggleScheduleRequest;
use App\Http\Requests\Product\Schedule\ToggleScheduleAssignmentRequest;

/**
 * ProductScheduleController
 *
 * Handles productschedule operations.
 */
class ProductScheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['can:view-product-schedules'])->only(['index']);
        $this->middleware(['can:create-product-schedules'])->only(['store']);
        $this->middleware(['can:edit-product-schedules'])->only(['edit', 'update', 'attach', 'updatePivotCapacity', 'detach']);
        $this->middleware(['can:publish-product-schedules'])->only(['toggle']);
        $this->middleware(['can:publish-product-schedule-assignments'])->only(['toggleAssignment']);
        $this->middleware(['can:delete-product-schedules'])->only(['destroy']);
        $this->middleware(['can:restore-schedules'])->only(['restore']);
        $this->middleware(['can:force-delete-schedules'])->only(['forceDelete']);
    }

    protected string $controller = 'ProductScheduleController';

    public function index()
    {
        $trashedCount = Schedule::onlyTrashed()->count();
        $generalSchedules = Schedule::orderBy('start_time')->get();

        $products = Product::with([
            'schedules' => function ($q) {
                $q->orderBy('schedules.start_time');
            }
        ])->orderByRaw("name->>'" . app()->getLocale() . "' ASC")->get();

        return view('admin.products.schedule.index', compact('generalSchedules', 'products', 'trashedCount'));
    }

    /**
     * Listar horarios eliminados
     */
    public function trash()
    {
        $schedules = Schedule::onlyTrashed()
            ->with(['deletedBy'])
            ->orderByDesc('deleted_at')
            ->get();

        return view('admin.products.schedule.trash', compact('schedules'));
    }

    /**
     * Restaurar horario eliminado
     */
    public function restore($id): RedirectResponse
    {
        try {
            $schedule = Schedule::onlyTrashed()->findOrFail($id);
            $schedule->restore();

            LoggerHelper::mutated($this->controller, 'restore', 'schedule', $id, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return redirect()->route('admin.products.schedule.trash')
                ->with('success', __('m_products.schedule.success.restored'));
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'restore', 'schedule', $id, $e, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);
            return back()->with('error', __('m_products.schedule.error.restore'));
        }
    }

    /**
     * Eliminar permanentemente
     */
    public function forceDelete($id): RedirectResponse
    {
        try {
            $schedule = Schedule::onlyTrashed()->findOrFail($id);
            $schedule->forceDelete();

            LoggerHelper::mutated($this->controller, 'forceDelete', 'schedule', $id, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return redirect()->route('admin.products.schedule.trash')
                ->with('success', __('m_products.schedule.success.force_deleted'));
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'forceDelete', 'schedule', $id, $e, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);
            return back()->with('error', __('m_products.schedule.error.force_delete'));
        }
    }

    /**
     * Crear horario (general o para un product)
     * - Si viene `product_id`, se adjunta al product con pivote is_active = true y opcional base_capacity
     * - Si NO, queda como horario general (sin capacidad en la tabla schedules)
     */
    public function store(StoreScheduleRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();

            // Crear schedule SIN max_capacity (ya no existe en la tabla)
            $schedule = Schedule::create([
                'start_time' => $data['start_time'],
                'end_time'   => $data['end_time'],
                'label'      => $data['label'] ?? null,
                'is_active'  => $request->has('is_active') ? $request->boolean('is_active') : true,
            ]);

            // Si se adjunta a un product, crear pivote con base_capacity opcional
            if (!empty($data['product_id'])) {
                $pivotData = ['is_active' => true];

                if (!empty($data['base_capacity'])) {
                    $pivotData['base_capacity'] = (int) $data['base_capacity'];
                }

                $schedule->products()->syncWithoutDetaching([
                    $data['product_id'] => $pivotData
                ]);
            }

            LoggerHelper::mutated($this->controller, 'store', 'schedule', $schedule->getKey(), [
                'product_id_attached' => $data['product_id'] ?? null,
                'base_capacity'    => $data['base_capacity'] ?? null,
                'user_id'          => optional($request->user())->getAuthIdentifier(),
            ]);

            return redirect()->route('admin.products.schedule.index')
                ->with('success', __('m_products.schedule.success.created'));
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'store', 'schedule', null, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);
            return back()
                ->with('error', __('m_products.schedule.error.create'))
                ->withInput();
        }
    }

    public function edit(Schedule $schedule)
    {
        return view('admin.products.schedule.edit', compact('schedule'));
    }

    /**
     * Actualizar horario general (SIN capacidad)
     */
    public function update(UpdateScheduleRequest $request, Schedule $schedule): RedirectResponse
    {
        try {
            $data = $request->validated();

            // Actualizar schedule SIN max_capacity
            $schedule->update([
                'start_time' => $data['start_time'],
                'end_time'   => $data['end_time'],
                'label'      => $data['label'] ?? null,
                'is_active'  => $request->boolean('is_active'),
            ]);

            LoggerHelper::mutated($this->controller, 'update', 'schedule', $schedule->getKey(), [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return redirect()->route('admin.products.schedule.index')
                ->with('success', __('m_products.schedule.success.updated'));
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'update', 'schedule', $schedule->getKey(), $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);
            return back()
                ->with('error', __('m_products.schedule.error.update'))
                ->withInput();
        }
    }

    /**
     * Toggle estado activo del schedule (global)
     */
    public function toggle(ToggleScheduleRequest $request, Schedule $schedule): RedirectResponse
    {
        try {
            $schedule->is_active = !$schedule->is_active;
            $schedule->save();

            LoggerHelper::mutated($this->controller, 'toggle', 'schedule', $schedule->getKey(), [
                'is_active' => $schedule->is_active,
                'user_id'   => optional($request->user())->getAuthIdentifier(),
            ]);

            $msg = $schedule->is_active
                ? __('m_products.schedule.success.activated_global')
                : __('m_products.schedule.success.deactivated_global');

            return back()->with('success', $msg);
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'toggle', 'schedule', $schedule->getKey(), $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);
            return back()->with('error', __('m_products.schedule.error.toggle'));
        }
    }

    /**
     * Toggle asignación de schedule a product (pivote)
     */
    public function toggleAssignment(ToggleScheduleAssignmentRequest $request, Product $product, Schedule $schedule): RedirectResponse
    {
        try {
            $rel = $product->schedules()->where('schedules.schedule_id', $schedule->getKey())->first();

            if (!$rel) {
                return back()->with('error', __('m_products.schedule.error.not_assigned_to_product'));
            }

            $current = (bool) ($rel->pivot->is_active ?? true);
            $product->schedules()->updateExistingPivot($schedule->getKey(), ['is_active' => !$current]);

            LoggerHelper::mutated($this->controller, 'toggleAssignment', 'product_schedule_pivot', $schedule->getKey(), [
                'product_id'         => $product->getKey(),
                'schedule_id'     => $schedule->getKey(),
                'pivot_is_active' => !$current,
                'user_id'         => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('success', !$current
                ? __('m_products.schedule.success.assignment_activated')
                : __('m_products.schedule.success.assignment_deactivated'));
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'toggleAssignment', 'product_schedule_pivot', $schedule->getKey(), $e, [
                'product_id' => $product->getKey(),
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);
            return back()->with('error', __('m_products.schedule.error.assignment_toggle'));
        }
    }

    /**
     * Asignar schedule existente a un product (con capacidad opcional)
     */
    public function attach(AttachScheduleToProductRequest $request, Product $product): RedirectResponse
    {
        try {
            $data = $request->validated();

            $pivotData = ['is_active' => true];

            if (!empty($data['base_capacity'])) {
                $pivotData['base_capacity'] = (int) $data['base_capacity'];
            }

            $product->schedules()->syncWithoutDetaching([
                $data['schedule_id'] => $pivotData
            ]);

            LoggerHelper::mutated($this->controller, 'attach', 'product_schedule_pivot', $data['schedule_id'], [
                'product_id'       => $product->getKey(),
                'base_capacity' => $data['base_capacity'] ?? null,
                'user_id'       => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('success', __('m_products.schedule.success.attached'));
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'attach', 'product_schedule_pivot', null, $e, [
                'product_id' => $product->getKey(),
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);
            return back()->with('error', __('m_products.schedule.error.attach'));
        }
    }

    /**
     * Actualizar capacidad del pivote (base_capacity)
     */
    public function updatePivotCapacity(Request $request, Product $product, Schedule $schedule): RedirectResponse
    {
        $request->validate([
            'base_capacity' => 'nullable|integer|min:1|max:999',
        ]);

        try {
            $product->schedules()->updateExistingPivot($schedule->getKey(), [
                'base_capacity' => $request->base_capacity
            ]);

            LoggerHelper::mutated($this->controller, 'updatePivotCapacity', 'product_schedule_pivot', $schedule->getKey(), [
                'product_id'       => $product->getKey(),
                'base_capacity' => $request->base_capacity,
                'user_id'       => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('success', 'Capacidad del horario actualizada correctamente.');
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'updatePivotCapacity', 'product_schedule_pivot', $schedule->getKey(), $e, [
                'product_id' => $product->getKey(),
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);
            return back()->with('error', 'Error al actualizar capacidad.');
        }
    }

    /**
     * Desasignar schedule de un product
     */
    public function detach(Product $product, Schedule $schedule): RedirectResponse
    {
        try {
            $product->schedules()->detach($schedule->getKey());

            LoggerHelper::mutated($this->controller, 'detach', 'product_schedule_pivot', $schedule->getKey(), [
                'product_id' => $product->getKey(),
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return back()->with('success', __('m_products.schedule.success.detached'));
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'detach', 'product_schedule_pivot', $schedule->getKey(), $e, [
                'product_id' => $product->getKey(),
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);
            return back()->with('error', __('m_products.schedule.error.detach'));
        }
    }

    /**
     * Eliminar schedule general usando SoftDeletes
     */
    public function destroy(Schedule $schedule): RedirectResponse
    {
        try {
            $id = $schedule->getKey();

            // Set deleted_by before deleting
            $schedule->update(['deleted_by' => auth()->id()]);
            $schedule->delete();

            LoggerHelper::mutated($this->controller, 'destroy', 'schedule', $id, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return redirect()->route('admin.products.schedule.index')
                ->with('success', __('m_products.schedule.success.deleted'));
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'destroy', 'schedule', $schedule->getKey(), $e, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);
            return back()->with('error', __('m_products.schedule.error.delete'));
        }
    }
}
