<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Models\Amenity;
// use App\Models\AmenityTranslation;
use App\Services\Contracts\TranslatorInterface;
use App\Services\LoggerHelper;
use App\Http\Requests\Product\Amenity\StoreAmenityRequest;
use App\Http\Requests\Product\Amenity\UpdateAmenityRequest;

/**
 * AmenityController
 *
 * Handles amenity operations.
 */
class AmenityController extends Controller
{
    public function __construct()
    {
        $this->middleware(['can:view-amenities'])->only(['index']);
        $this->middleware(['can:create-amenities'])->only(['store']);
        $this->middleware(['can:edit-amenities'])->only(['update']);
        $this->middleware(['can:publish-amenities'])->only(['toggle']);
        $this->middleware(['can:delete-amenities'])->only(['destroy']);
        $this->middleware(['can:restore-amenities'])->only(['trash', 'restore']);
        $this->middleware(['can:force-delete-amenities'])->only(['forceDelete']);
    }

    protected string $controller = 'AmenityController';

    public function index()
    {
        $amenities = Amenity::get()->sortBy('name');
        $trashedCount = Amenity::onlyTrashed()->count();
        return view('admin.products.amenities.index', compact('amenities', 'trashedCount'));
    }

    public function store(StoreAmenityRequest $request, TranslatorInterface $translator)
    {
        try {
            $locales = supported_locales();

            $amenity = DB::transaction(function () use ($request, $translator, $locales) {
                $name = $request->string('name')->trim();

                // Create amenity without name field
                $amenity = Amenity::create([
                    'is_active' => true,
                ]);

                // Translate to all locales
                $translated = $translator->translateAll($name);

                // Create translations for all locales
                foreach ($locales as $locale) {
                    $amenity->setTranslation('name', $locale, $translated[$locale] ?? $name);
                }

                // CRITICAL: Save translations to database
                $amenity->save();

                return $amenity;
            });

            // Guard por seguridad
            if (! $amenity) {
                LoggerHelper::error($this->controller, 'store', 'Amenity instance is null after transaction', [
                    'entity'  => 'amenity',
                    'user_id' => optional($request->user())->getAuthIdentifier(),
                ]);

                return back()->with('error', __('m_products.amenity.error.create'));
            }

            // Log post-commit
            LoggerHelper::mutated($this->controller, 'store', 'amenity', $amenity->amenity_id, [
                'locales_saved' => count($locales),
                'user_id'       => optional($request->user())->getAuthIdentifier(),
            ]);

            return redirect()
                ->route('admin.products.amenities.index')
                ->with('success', __('m_products.amenity.success.created'));
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'store', 'amenity', null, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', __('m_products.amenity.error.create'));
        }
    }

    public function update(UpdateAmenityRequest $request, Amenity $amenity)
    {
        try {
            // Update translations for each locale
            foreach ($request->input('translations', []) as $locale => $name) {
                $amenity->setTranslation('name', $locale, trim($name));
            }
            $amenity->save();

            LoggerHelper::mutated($this->controller, 'update', 'amenity', $amenity->amenity_id, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return redirect()
                ->route('admin.products.amenities.index')
                ->with('success', __('m_products.amenity.success.updated'));
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'update', 'amenity', $amenity->amenity_id, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', __('m_products.amenity.error.update'));
        }
    }

    /** Toggle activar/desactivar (race-safe) */
    public function toggle(Amenity $amenity)
    {
        try {
            Amenity::whereKey($amenity->getKey())->update(['is_active' => DB::raw('NOT is_active')]);
            $amenity->refresh();

            LoggerHelper::mutated($this->controller, 'toggle', 'amenity', $amenity->amenity_id, [
                'is_active' => $amenity->is_active,
                'user_id'   => optional(request()->user())->getAuthIdentifier(),
            ]);

            $msg = $amenity->is_active
                ? __('m_products.amenity.success.activated')
                : __('m_products.amenity.success.deactivated');

            return redirect()
                ->route('admin.products.amenities.index')
                ->with('success', $msg);
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'toggle', 'amenity', $amenity->amenity_id, $e, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', __('m_products.amenity.error.toggle'));
        }
    }

    /** Soft delete */
    public function destroy(Amenity $amenity)
    {
        try {
            $amenity->deleted_by = auth()->id();
            $amenity->save();
            $amenity->delete();

            LoggerHelper::mutated($this->controller, 'destroy', 'amenity', $amenity->amenity_id, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return redirect()
                ->route('admin.products.amenities.index')
                ->with('success', __('m_products.amenity.success.deleted'));
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'destroy', 'amenity', $amenity->amenity_id ?? null, $e, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', __('m_products.amenity.error.delete'));
        }
    }

    public function trash()
    {
        $amenities = Amenity::onlyTrashed()
            ->with(['deletedBy'])
            ->orderByDesc('deleted_at')
            ->get();

        return view('admin.products.amenities.trash', compact('amenities'));
    }

    public function restore($id)
    {
        $amenity = Amenity::onlyTrashed()->findOrFail($id);
        $amenity->deleted_by = null;
        $amenity->save();
        $amenity->restore();

        LoggerHelper::mutated($this->controller, 'restore', 'amenity', $id);

        return redirect()
            ->route('admin.products.amenities.trash')
            ->with('success', 'Amenidad restaurada correctamente.');
    }

    public function forceDelete($id)
    {
        $amenity = Amenity::onlyTrashed()->findOrFail($id);

        // Verificar si tiene productos relacionados antes de borrar permanentemente
        // Nota: Amenity usa belongsToMany, así que verificamos la tabla pivote
        if ($amenity->products()->exists()) {
            return redirect()
                ->route('admin.products.amenities.trash')
                ->with('error', 'No se puede eliminar permanentemente porque tiene productos asociados.');
        }

        $amenity->forceDelete();

        LoggerHelper::mutated($this->controller, 'forceDelete', 'amenity', $id);

        return redirect()
            ->route('admin.products.amenities.trash')
            ->with('success', 'Amenidad eliminada permanentemente.');
    }
}
