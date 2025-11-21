<?php

namespace App\Http\Controllers\Admin\Tours;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Models\Amenity;
use App\Models\AmenityTranslation;
use App\Services\Contracts\TranslatorInterface;
use App\Services\LoggerHelper;
use App\Http\Requests\Tour\Amenity\StoreAmenityRequest;
use App\Http\Requests\Tour\Amenity\UpdateAmenityRequest;

class AmenityController extends Controller
{
    protected string $controller = 'AmenityController';

    public function index()
    {
        $amenities = Amenity::with('translations')->get()->sortBy('name');
        return view('admin.tours.amenities.index', compact('amenities'));
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
                    AmenityTranslation::create([
                        'amenity_id' => $amenity->amenity_id,
                        'locale'     => $locale,
                        'name'       => $translated[$locale] ?? $name,
                    ]);
                }

                return $amenity;
            });

            // Guard por seguridad
            if (! $amenity) {
                LoggerHelper::error($this->controller, 'store', 'Amenity instance is null after transaction', [
                    'entity'  => 'amenity',
                    'user_id' => optional($request->user())->getAuthIdentifier(),
                ]);

                return back()->with('error', __('m_tours.amenity.error.create'));
            }

            // Log post-commit
            LoggerHelper::mutated($this->controller, 'store', 'amenity', $amenity->amenity_id, [
                'locales_saved' => count($locales),
                'user_id'       => optional($request->user())->getAuthIdentifier(),
            ]);

            return redirect()
                ->route('admin.tours.amenities.index')
                ->with('success', __('m_tours.amenity.success.created'));
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'store', 'amenity', null, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', __('m_tours.amenity.error.create'));
        }
    }

    public function update(UpdateAmenityRequest $request, Amenity $amenity)
    {
        try {
            // Update translations for each locale
            foreach ($request->input('translations', []) as $locale => $name) {
                $amenity->translations()->updateOrCreate(
                    ['locale' => $locale],
                    ['name' => trim($name)]
                );
            }

            LoggerHelper::mutated($this->controller, 'update', 'amenity', $amenity->amenity_id, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return redirect()
                ->route('admin.tours.amenities.index')
                ->with('success', __('m_tours.amenity.success.updated'));
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'update', 'amenity', $amenity->amenity_id, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', __('m_tours.amenity.error.update'));
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
                ? __('m_tours.amenity.success.activated')
                : __('m_tours.amenity.success.deactivated');

            return redirect()
                ->route('admin.tours.amenities.index')
                ->with('success', $msg);
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'toggle', 'amenity', $amenity->amenity_id, $e, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', __('m_tours.amenity.error.toggle'));
        }
    }

    /** Eliminación definitiva */
    public function destroy(Amenity $amenity)
    {
        try {
            $id = $amenity->amenity_id;
            $amenity->delete();

            LoggerHelper::mutated($this->controller, 'destroy', 'amenity', $id, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return redirect()
                ->route('admin.tours.amenities.index')
                ->with('success', __('m_tours.amenity.success.deleted'));
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'destroy', 'amenity', $amenity->amenity_id ?? null, $e, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', __('m_tours.amenity.error.delete'));
        }
    }
}
