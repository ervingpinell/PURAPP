<?php

namespace App\Http\Controllers\Admin\Tours;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use App\Models\TourType;
use App\Services\LoggerHelper;
use App\Http\Requests\Tour\TourType\StoreTourTypeRequest;
use App\Http\Requests\Tour\TourType\UpdateTourTypeRequest;
use App\Http\Requests\Tour\TourType\UpdateTourTypeTranslationRequest;
use App\Http\Requests\Tour\TourType\ToggleTourTypeRequest;

/**
 * TourTypeController
 *
 * Handles tourtype operations.
 */
class TourTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['can:view-tour-types'])->only(['index']);
        $this->middleware(['can:create-tour-types'])->only(['store']);
        $this->middleware(['can:edit-tour-types'])->only(['update', 'editTranslations', 'updateTranslation']);
        $this->middleware(['can:publish-tour-types'])->only(['toggle']);
        $this->middleware(['can:delete-tour-types'])->only(['destroy']);
        $this->middleware(['can:restore-tour-types'])->only(['trash', 'restore']);
        $this->middleware(['can:force-delete-tour-types'])->only(['forceDelete']);
    }

    protected string $controller = 'TourTypeController';

    public function index()
    {
        // Cargar TODAS las traducciones y contar eliminados
        $tourTypes = TourType::with('translations')
            ->orderByDesc('created_at')
            ->get();

        $trashedCount = TourType::onlyTrashed()->count();

        $currentLocale = app()->getLocale();

        return view('admin.tourtypes.index', compact('tourTypes', 'currentLocale', 'trashedCount'));
    }

    public function store(StoreTourTypeRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->validated();
            
            $tourType = TourType::create([
                'is_active' => true,
                // Add explicit fields if needed, but fillable only has is_active, cover_path, deleted_by
            ]);

            // Create initial translation (ES implied by request validation rules usually focusing on single language create)
            // Or use app locale. Assuming 'es' or current.
            $tourType->translations()->create([
                'locale' => app()->getLocale(), 
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'duration' => $data['duration'] ?? null,
            ]);

            DB::commit();

            LoggerHelper::mutated($this->controller, 'store', 'tour_type', $tourType->getKey(), [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return redirect()->route('admin.tourtypes.index')
                ->with('success', 'm_config.tourtypes.created_success');

        } catch (Exception $e) {
            DB::rollBack();
            LoggerHelper::exception($this->controller, 'store', 'tour_type', null, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);
            return back()->with('error', 'Error al crear el tipo de tour.')->withInput();
        }
    }

    public function update(UpdateTourTypeRequest $request, TourType $tourType)
    {
        try {
            DB::beginTransaction();

            $translations = $request->input('translations', []);

            if (!empty($translations)) {
                // Update via Translations Array (New Tabs Approach)
                foreach ($translations as $locale => $transData) {
                    $tourType->translations()->updateOrCreate(
                        ['locale' => $locale],
                        [
                            'name' => $transData['name'],
                            'description' => $transData['description'] ?? null,
                            'duration' => $transData['duration'] ?? null,
                        ]
                    );
                }
            } else {
                // Legacy / Single Field Update
                // Assume 'es' or matches validation logic
                $data = $request->validated(); // This might fail if we change Validation rules to expect translations
                // But if request has 'name', we update current/fallback
                $tourType->translations()->updateOrCreate(
                    ['locale' => app()->getLocale()], // or 'es'
                    [
                        'name' => $data['name'] ?? $request->input('name'), 
                        'description' => $data['description'] ?? $request->input('description'),
                        'duration' => $data['duration'] ?? $request->input('duration'),
                    ]
                );
            }

            DB::commit();

            LoggerHelper::mutated($this->controller, 'update', 'tour_type', $tourType->getKey(), [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return redirect()->route('admin.tourtypes.index')
                ->with('success', 'm_config.tourtypes.updated_success');

        } catch (Exception $e) {
            DB::rollBack();
            LoggerHelper::exception($this->controller, 'update', 'tour_type', $tourType->getKey(), $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);
            return back()->with('error', 'Error al actualizar el tipo de tour.')->withInput();
        }
    }

    public function toggle(TourType $tourType)
    {
        try {
            $tourType->update(['is_active' => !$tourType->is_active]);

            LoggerHelper::mutated($this->controller, 'toggle', 'tour_type', $tourType->getKey(), [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
                'new_status' => $tourType->is_active
            ]);

            return back()->with('success', $tourType->is_active ? 'Activado correctamente' : 'Desactivado correctamente');
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'toggle', 'tour_type', $tourType->getKey(), $e);
            return back()->with('error', 'Error al cambiar el estado.');
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        $tourType = TourType::findOrFail($id);

        try {
            $tourType->deleted_by = auth()->id();
            $tourType->save();
            $tourType->delete();

            LoggerHelper::mutated($this->controller, 'destroy', 'tour_type', $id, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return back()->with('success', 'm_config.tourtypes.deleted_success');
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'destroy', 'tour_type', $id, $e, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', 'm_config.tourtypes.in_use_error');
        }
    }

    public function trash()
    {
        $tourTypes = TourType::onlyTrashed()
            ->with(['translations', 'deletedBy'])
            ->orderByDesc('deleted_at')
            ->get();

        return view('admin.tourtypes.trash', compact('tourTypes'));
    }

    public function restore($id)
    {
        $tourType = TourType::onlyTrashed()->findOrFail($id);
        $tourType->deleted_by = null;
        $tourType->save();
        $tourType->restore();

        LoggerHelper::mutated($this->controller, 'restore', 'tour_type', $id);

        return redirect()
            ->route('admin.tourtypes.trash')
            ->with('success', 'Tipo de tour restaurado correctamente.');
    }

    public function forceDelete($id)
    {
        $tourType = TourType::onlyTrashed()->findOrFail($id);

        // Verificar si tiene tours relacionados antes de borrar permanentemente
        if ($tourType->tours()->exists()) {
            return redirect()
                ->route('admin.tourtypes.trash')
                ->with('error', 'No se puede eliminar permanentemente porque tiene tours asociados.');
        }

        $tourType->forceDelete();

        LoggerHelper::mutated($this->controller, 'forceDelete', 'tour_type', $id);

        return redirect()
            ->route('admin.tourtypes.trash')
            ->with('success', 'Tipo de tour eliminado permanentemente.');
    }

    /**
     * Mostrar vista de edición de traducciones con pestañas por locale
     */
    public function editTranslations(TourType $tourType)
    {
        // Cargar todas las traducciones existentes
        $tourType->load('translations');

        // Definir locales soportados
        $supportedLocales = [
            'es' => 'Español',
            'en' => 'English',
            'fr' => 'Français',
            'pt' => 'Português',
            'de' => 'Deutsch',
        ];

        // Crear mapa de traducciones existentes por locale
        $translationsByLocale = $tourType->translations->keyBy('locale');

        return view('admin.tourtypes.edit-translations', compact(
            'tourType',
            'supportedLocales',
            'translationsByLocale'
        ));
    }

    /**
     * Actualizar o crear traducción para un locale específico
     */
    public function updateTranslation(
        UpdateTourTypeTranslationRequest $request,
        TourType $tourType,
        string $locale
    ): RedirectResponse {
        try {
            $data = $request->validated();

            // Validar que el locale sea soportado
            $supportedLocales = ['es', 'en', 'fr', 'pt', 'de'];
            if (!in_array($locale, $supportedLocales)) {
                return back()
                    ->with('error', 'Locale no soportado.')
                    ->withInput();
            }

            // Actualizar o crear traducción
            $tourType->translations()->updateOrCreate(
                ['locale' => $locale],
                [
                    'name' => $data['name'],
                    'description' => $data['description'] ?? null,
                    'duration' => $data['duration'] ?? null,
                ]
            );

            LoggerHelper::mutated($this->controller, 'updateTranslation', 'tour_type', $tourType->getKey(), [
                'locale' => $locale,
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return redirect()
                ->route('admin.tourtypes.translations.edit', $tourType)
                ->with('success', "Traducción en {$locale} guardada correctamente.")
                ->with('active_locale', $locale); // Para mantener la pestaña activa
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'updateTranslation', 'tour_type', $tourType->getKey(), $e, [
                'locale' => $locale,
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()
                ->with('error', 'Error al guardar la traducción.')
                ->withInput();
        }
    }
}
