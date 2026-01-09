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
    }

    protected string $controller = 'TourTypeController';

    public function index()
    {
        // Cargar TODAS las traducciones para mostrar badges de idiomas disponibles
        $tourTypes = TourType::with('translations')
            ->orderByDesc('created_at')
            ->get();

        $currentLocale = app()->getLocale();

        return view('admin.tourtypes.index', compact('tourTypes', 'currentLocale'));
    }

    public function store(StoreTourTypeRequest $request, \App\Services\Contracts\TranslatorInterface $translator): RedirectResponse
    {
        try {
            $data = $request->validated();

            $tourType = DB::transaction(function () use ($data, $translator) {
                // 1. Crear TourType solo con campos no traducibles
                $tourType = TourType::create([
                    'is_active' => true,
                ]);

                // 2. Crear traducción en español (locale por defecto)
                $tourType->translations()->create([
                    'locale'      => 'es',
                    'name'        => $data['name'],
                    'description' => $data['description'] ?? null,
                    'duration'    => $data['duration'] ?? null,
                ]);

                // 3. Generar traducciones automáticas para otros idiomas
                $locales = ['en', 'fr', 'pt', 'de'];

                // Traducir campos si existen
                $trNames        = $translator->translateAll($data['name']);
                $trDescriptions = !empty($data['description']) ? $translator->translateAll($data['description']) : [];
                $trDurations    = !empty($data['duration'])    ? $translator->translateAll($data['duration'])    : [];

                foreach ($locales as $locale) {
                    $tourType->translations()->create([
                        'locale'      => $locale,
                        'name'        => $trNames[$locale] ?? $data['name'],
                        'description' => $trDescriptions[$locale] ?? ($data['description'] ?? null),
                        'duration'    => $trDurations[$locale] ?? ($data['duration'] ?? null),
                    ]);
                }

                return $tourType;
            });

            LoggerHelper::mutated($this->controller, 'store', 'tour_type', $tourType->getKey(), [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return redirect()
                ->route('admin.tourtypes.index')
                ->with('success', 'm_config.tourtypes.created_success');
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'store', 'tour_type', null, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()
                ->with('error', 'm_config.tourtypes.unexpected_error')
                ->withInput();
        }
    }

    public function update(UpdateTourTypeRequest $request, TourType $tourType): RedirectResponse
    {
        try {
            $data = $request->validated();

            DB::transaction(function () use ($tourType, $data) {
                // Actualizar o crear traducción en español
                $tourType->translations()->updateOrCreate(
                    ['locale' => 'es'],
                    [
                        'name'        => $data['name'],
                        'description' => $data['description'] ?? null,
                        'duration'    => $data['duration'] ?? null,
                    ]
                );
            });

            LoggerHelper::mutated($this->controller, 'update', 'tour_type', $tourType->getKey(), [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return redirect()
                ->route('admin.tourtypes.index')
                ->with('success', 'm_config.tourtypes.updated_success');
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'update', 'tour_type', $tourType->getKey(), $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()
                ->with('error', 'm_config.tourtypes.unexpected_error')
                ->withInput()
                ->with('edit_modal', $tourType->getKey());
        }
    }

    public function toggle(ToggleTourTypeRequest $request, TourType $tourType): RedirectResponse
    {
        try {
            $tourType->is_active = ! $tourType->is_active;
            $tourType->save();

            LoggerHelper::mutated($this->controller, 'toggle', 'tour_type', $tourType->getKey(), [
                'is_active' => $tourType->is_active,
                'user_id'   => optional($request->user())->getAuthIdentifier(),
            ]);

            $key = $tourType->is_active
                ? 'm_config.tourtypes.activated_success'
                : 'm_config.tourtypes.deactivated_success';

            return redirect()
                ->route('admin.tourtypes.index')
                ->with('success', $key);
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'toggle', 'tour_type', $tourType->getKey(), $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', 'm_config.tourtypes.unexpected_error');
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        $tourType = TourType::findOrFail($id);

        try {
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
