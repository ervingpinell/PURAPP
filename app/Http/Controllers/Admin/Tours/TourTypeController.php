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

    // ... (store, update, toggle maintained as is) ...

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
