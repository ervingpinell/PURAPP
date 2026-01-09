<?php

namespace App\Http\Controllers\Admin\Tours;

use App\Http\Controllers\Controller;
use App\Models\{
    Tour,
    TourType,
    TourLanguage,
    TourAuditLog,
    Amenity,
    Schedule,
    Itinerary,
    ItineraryItem,
    CustomerCategory,
    Tax
};
use App\Services\{LoggerHelper, DraftLimitService};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use App\Services\Contracts\TranslatorInterface;
use App\Models\TourTypeTranslation;
use App\Models\ItineraryItemTranslation;
use App\Models\AmenityTranslation;
use App\Models\CustomerCategoryTranslation;
use Carbon\Carbon;

/**
 * TourWizardController
 *
 * Handles tourwizard operations.
 */
class TourWizardController extends Controller
{
    protected DraftLimitService $draftLimit;

    public function __construct(DraftLimitService $draftLimitService)
    {
        $this->draftLimit = $draftLimitService;
    }

    // Definición de pasos del wizard
    private const STEPS = [
        1 => 'details',
        2 => 'itinerary',
        3 => 'schedules',
        4 => 'amenities',
        5 => 'prices',
        6 => 'summary',
    ];

    /**
     * ============================================================
     * INICIAR CREACIÓN DE TOUR - CON DETECCIÓN DE DRAFTS
     * ============================================================
     */
    public function create()
    {
        $userId = optional(auth()->user())->user_id ?? auth()->id();

        // 🆕 VERIFICAR LÍMITE DE DRAFTS
        $limitCheck = $this->draftLimit->canCreateDraft($userId);

        if (is_array($limitCheck)) {
            // Usuario alcanzó el límite -> igual mostramos la pantalla, pero marcamos flag
            $existingDrafts = $limitCheck['all_drafts'];
            $limitReached   = true;
            $limitInfo      = $limitCheck;
        } else {
            $existingDrafts = $this->draftLimit->getUserDrafts($userId);
            $limitReached   = false;
            $limitInfo      = null;
        }

        $warning = $this->draftLimit->getWarningMessage($userId);

        return view('admin.tours.wizard.steps.details', [
            'tour'           => null,
            'tourTypes'      => TourType::active()->withTranslation()->get(),
            'languages'      => TourLanguage::where('is_active', true)->get(),
            'step'           => 1,
            'steps'          => self::STEPS,
            'existingDrafts' => $existingDrafts,
            'limitReached'   => $limitReached,
            'limitWarning'   => $warning,
            'limitInfo'      => $limitInfo,
            'draftsStats'    => $this->draftLimit->getUserStats($userId),
        ]);
    }

    /**
     * ============================================================
     * EDITAR TOUR - Redirigir al wizard
     * ============================================================
     */
    public function edit(Tour $tour)
    {
        $userId = optional(auth()->user())->user_id ?? auth()->id();

        // Si el tour ya está publicado, asegurar que current_step sea 6
        if (!$tour->is_draft && $tour->current_step < 6) {
            $tour->update([
                'current_step' => 6,
                'updated_by'   => $userId,
            ]);
        }

        // Si es draft, ir al paso donde se quedó
        if ($tour->is_draft) {
            $step = $tour->current_step ?? 1;

            LoggerHelper::info(...);

            return redirect()->route('admin.tours.wizard.step', [
                'tour' => $tour,
                'step' => $step
            ]);
        }

        // Si ya está publicado, ir al resumen (paso 6)
        LoggerHelper::info(...);

        return redirect()->route('admin.tours.wizard.step', [
            'tour' => $tour,
            'step' => 6
        ]);
    }


    /**
     * ============================================================
     * CONTINUAR CON DRAFT EXISTENTE
     * ============================================================
     */
    public function continueDraft(Tour $tour)
    {
        $userId = optional(auth()->user())->user_id ?? auth()->id();

        // Verificar que sea draft
        if (!$tour->is_draft) {
            return redirect()
                ->route('admin.tours.index')
                ->with('error', __('m_tours.tour.wizard.not_a_draft'));
        }

        // 🆕 VERIFICAR PROPIEDAD (seguridad)
        if ($tour->created_by && $tour->created_by !== $userId) {
            abort(403, 'No autorizado para editar este borrador');
        }

        // 🆕 LOG DE AUDITORÍA
        TourAuditLog::logAction(
            action: 'draft_continued',
            tourId: $tour->tour_id,
            description: "Usuario continuó editando el borrador '{$tour->name}'",
            context: 'wizard',
            wizardStep: $tour->current_step ?? 1,
            tags: ['draft', 'wizard', 'continued']
        );

        LoggerHelper::info(
            'TourWizardController',
            'continueDraft',
            'Continuar con draft existente',
            [
                'tour_id'      => $tour->tour_id,
                'user_id'      => $userId,
                'current_step' => $tour->current_step,
            ]
        );

        // Redirigir al paso donde se quedó
        $nextStep = $tour->current_step ?? 1;

        return redirect()
            ->route('admin.tours.wizard.step', ['tour' => $tour, 'step' => $nextStep])
            ->with('success', __('m_tours.tour.wizard.continuing_draft'));
    }

    /**
     * ============================================================
     * ELIMINAR DRAFT ESPECÍFICO
     * ============================================================
     */
    public function deleteDraft(Tour $tour)
    {
        $userId = optional(auth()->user())->user_id ?? auth()->id();

        // 🔍 LOG DE ENTRADA
        LoggerHelper::info(
            'TourWizardController',
            'deleteDraft',
            'Entrada a deleteDraft desde UI',
            [
                'tour_route_param_id' => $tour->tour_id ?? $tour->getKey(),
                'tour_slug'           => $tour->slug ?? null,
                'is_draft'            => $tour->is_draft,
                'created_by'          => $tour->created_by,
                'user_id'             => $userId,
                'http_method'         => request()->method(),
                'route_name'          => optional(request()->route())->getName(),
                'full_url'            => request()->fullUrl(),
                'referer'             => request()->headers->get('referer'),
            ]
        );

        // Verificar que sea draft
        if (!$tour->is_draft) {
            LoggerHelper::info(
                'TourWizardController',
                'deleteDraft',
                'Intento de borrar tour que no es borrador',
                [
                    'tour_id' => $tour->tour_id ?? $tour->getKey(),
                    'user_id' => $userId,
                ]
            );

            return redirect()
                ->route('admin.tours.index')
                ->with('error', __('m_tours.tour.wizard.not_a_draft'));
        }

        // Verificar propiedad (seguridad)
        if ($tour->created_by && $tour->created_by !== $userId) {
            LoggerHelper::info(
                'TourWizardController',
                'deleteDraft',
                'Usuario no autorizado para eliminar este borrador',
                [
                    'tour_id'      => $tour->tour_id ?? $tour->getKey(),
                    'created_by'   => $tour->created_by,
                    'current_user' => $userId,
                ]
            );

            abort(403, 'No autorizado para eliminar este borrador');
        }

        DB::beginTransaction();
        try {
            $tourId   = $tour->tour_id;
            $tourName = $tour->name;

            LoggerHelper::info(
                'TourWizardController',
                'deleteDraft',
                'Comenzando eliminación de borrador y relaciones',
                [
                    'tour_id'       => $tourId,
                    'user_id'       => $userId,
                    'has_itinerary' => (bool) $tour->itinerary_id,
                ]
            );

            // Eliminar relaciones
            $tour->languages()->detach();
            $tour->amenities()->detach();
            $tour->excludedAmenities()->detach();
            $tour->schedules()->detach();
            $tour->prices()->delete();

            if ($tour->itinerary_id && $tour->itinerary) {
                LoggerHelper::info(
                    'TourWizardController',
                    'deleteDraft',
                    'Eliminando itinerario asociado al borrador',
                    [
                        'tour_id'      => $tourId,
                        'itinerary_id' => $tour->itinerary_id,
                    ]
                );

                $tour->itinerary->delete();
            }

            LoggerHelper::info(
                'TourWizardController',
                'deleteDraft',
                'Ejecutando forceDelete del tour borrador SIN eventos',
                [
                    'tour_id' => $tourId,
                    'user_id' => $userId,
                ]
            );

            // 🔧 CLAVE: evitar que el trait Auditable dispare auditDeleted
            // y trate de insertar en tour_audit_logs después de borrar el tour.
            \App\Models\Tour::withoutEvents(function () use ($tour) {
                $tour->forceDelete();
            });

            DB::commit();

            LoggerHelper::mutated(
                'TourWizardController',
                'deleteDraft',
                'tour',
                $tourId,
                [
                    'user_id' => $userId,
                    'action'  => 'draft_deleted',
                    'note'    => 'Borrador eliminado con forceDelete sin eventos Eloquent',
                ]
            );

            return redirect()
                ->route('admin.tours.wizard.create')
                ->with('success', __('m_tours.tour.wizard.draft_deleted'));
        } catch (\Throwable $e) {
            DB::rollBack();

            LoggerHelper::exception(
                'TourWizardController',
                'deleteDraft',
                'tour',
                $tour->tour_id ?? $tour->getKey(),
                $e,
                [
                    'user_id'    => $userId,
                    'route_name' => optional(request()->route())->getName(),
                    'full_url'   => request()->fullUrl(),
                ]
            );

            report($e);

            return redirect()
                ->route('admin.tours.wizard.create')
                ->with('error', __('m_tours.common.error_deleting'));
        }
    }


    /**
     * ============================================================
     * ELIMINAR TODOS LOS DRAFTS DEL USUARIO
     * ============================================================
     */
    public function deleteAllDrafts()
    {
        $userId = optional(auth()->user())->user_id ?? auth()->id();

        DB::beginTransaction();
        try {
            // 🆕 SOLO DRAFTS DEL USUARIO ACTUAL (seguridad)
            $drafts = Tour::where('is_draft', true)
                ->where('created_by', $userId)
                ->get();

            $deletedCount = 0;

            foreach ($drafts as $draft) {
                $draft->languages()->detach();
                $draft->amenities()->detach();
                $draft->excludedAmenities()->detach();
                $draft->schedules()->detach();
                $draft->prices()->delete();

                if ($draft->itinerary_id && $draft->itinerary) {
                    $draft->itinerary->delete();
                }

                $draft->forceDelete();
                $deletedCount++;
            }

            // 🆕 LOG DE AUDITORÍA
            TourAuditLog::logAction(
                action: 'bulk_action',
                userId: $userId,
                description: "Usuario eliminó {$deletedCount} borrador(es) en acción masiva",
                context: 'wizard',
                tags: ['draft', 'bulk-delete', 'user-action']
            );

            DB::commit();

            LoggerHelper::mutated(
                'TourWizardController',
                'deleteAllDrafts',
                'tour',
                null,
                [
                    'user_id'       => $userId,
                    'deleted_count' => $deletedCount,
                ]
            );

            return redirect()
                ->route('admin.tours.wizard.create')
                ->with('success', __('m_tours.tour.wizard.all_drafts_deleted', ['count' => $deletedCount]));
        } catch (\Throwable $e) {
            DB::rollBack();
            LoggerHelper::exception('TourWizardController', 'deleteAllDrafts', 'tour', null, $e, ['user_id' => $userId]);
            report($e);
            return redirect()
                ->route('admin.tours.wizard.create')
                ->with('error', __('m_tours.common.error_deleting'));
        }
    }

    /**
     * ============================================================
     * GUARDAR DETALLES Y CREAR DRAFT (PASO 1)
     * ============================================================
     */
    public function storeDetails(Request $request)
    {
        $userId = optional($request->user())->user_id ?? $request->user()?->getAuthIdentifier();

        // 🆕 VERIFICAR LÍMITE ANTES DE CREAR
        $limitCheck = $this->draftLimit->canCreateDraft($userId);

        if (is_array($limitCheck)) {
            return back()
                ->withInput()
                ->with('error', $limitCheck['message'])
                ->with('showDraftsModal', true);
        }

        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'slug'         => [
                'required', // Ahora es requerido
                'string',
                'max:255',
                Rule::unique('tours', 'slug')->whereNull('deleted_at'),
            ],
            'overview'     => 'required|string|max:1000', // Ahora requerido
            'length'       => 'required|numeric|min:0.5|max:240', // Ahora requerido
            'max_capacity' => 'required|integer|min:1|max:500',
            'group_size'   => 'required|integer|min:1|max:500', // Ahora requerido
            'color'        => 'required|string|max:7', // Ahora requerido
            'tour_type_id' => 'required|exists:tour_types,tour_type_id', // Ahora requerido
            'is_active'    => 'boolean',
            'languages'    => 'required|array|min:1', // Ahora requerido con mínimo 1
            'languages.*'  => 'exists:tour_languages,tour_language_id',
            'recommendations' => 'nullable|string', // 🆕 Nuevo campo
        ]);

        $tour = null;

        DB::beginTransaction();
        try {
            if (empty($data['slug'])) {
                $data['slug'] = Tour::generateUniqueSlug($data['name']);
            }

            // 🆕 CREAR CON created_by y updated_by
            $tour = Tour::create([
                'name'         => $data['name'],
                'slug'         => $data['slug'],
                'overview'     => $data['overview'] ?? null,
                'length'       => $data['length'] ?? null,
                'max_capacity' => $data['max_capacity'],
                'group_size'   => $data['group_size'] ?? null,
                'recommendations' => $data['recommendations'] ?? null, // 🆕 Nuevo campo
                'color'        => $data['color'] ?? '#3490dc',
                'tour_type_id' => $data['tour_type_id'] ?? null,
                'is_active'    => $data['is_active'] ?? false,
                'is_draft'     => true,
                'current_step' => 1,
                'created_by'   => $userId,  // 🆕
                'updated_by'   => $userId,  // 🆕
            ]);

            if (!empty($data['languages'])) {
                $tour->languages()->sync($data['languages']);
            }

            DB::commit();

            // El trait Auditable ya registra el log automáticamente

            LoggerHelper::mutated(
                'TourWizardController',
                'storeDetails',
                'tour',
                $tour->tour_id,
                [
                    'user_id'      => $userId,
                    'current_step' => 1,
                    'is_draft'     => true,
                ]
            );

            return redirect()
                ->route('admin.tours.wizard.step', ['tour' => $tour, 'step' => 2])
                ->with('success', __('m_tours.tour.wizard.details_saved'));
        } catch (\Throwable $e) {
            DB::rollBack();
            LoggerHelper::exception('TourWizardController', 'storeDetails', 'tour', $tour?->tour_id, $e, ['user_id' => $userId]);
            report($e);
            return back()->withInput()->with('error', __('m_tours.common.error_saving'));
        }
    }

    /**
     * Actualizar detalles de un tour en borrador (volver del paso 2, 3, etc.)
     */
    public function updateDetails(Request $request, Tour $tour)
    {
        LoggerHelper::info(
            'TourWizardController',
            'updateDetails',
            'Entrada a updateDetails',
            [
                'tour_id'   => $tour->tour_id ?? null,
                'slug'      => $tour->slug ?? null,
                'is_draft'  => $tour->is_draft,
                'step'      => 1,
            ]
        );

        $userId = optional(auth()->user())->user_id ?? auth()->id();

        // ==========================
        // Validación Paso 1
        // ==========================
        $data = $request->validate([
            'name'         => ['required', 'string', 'min:3', 'max:255'],
            'slug'         => [
                'required',
                'string',
                'min:3',
                'max:255',
                'regex:/^[a-z0-9-]+$/',
                Rule::unique('tours', 'slug')
                    ->ignore($tour->getKey(), $tour->getKeyName())
                    ->whereNull('deleted_at'),
            ],
            'overview'     => ['required', 'string', 'max:1000'],
            'length'       => ['required', 'numeric', 'min:0.5', 'max:240'],
            'max_capacity' => ['required', 'integer', 'min:1', 'max:500'],
            'group_size'   => ['required', 'integer', 'min:1', 'max:500'],
            'tour_type_id' => ['required', 'integer', 'exists:tour_types,tour_type_id'],
            'color'        => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'languages'    => ['required', 'array', 'min:1'],
            'languages.*'  => ['integer', 'exists:tour_languages,tour_language_id'],
            'recommendations' => ['nullable', 'string'], // 🆕 Nuevo campo
        ]);

        $data['overview'] = trim($data['overview'] ?? '');

        DB::transaction(function () use ($tour, $data, $userId) {
            $previousStep = (int) ($tour->current_step ?? 1);

            // Actualizar campos básicos (NO tocamos is_draft aquí)
            $tour->fill([
                'name'         => $data['name'],
                'slug'         => $data['slug'],
                'overview'     => $data['overview'],
                'length'       => $data['length'],
                'max_capacity' => $data['max_capacity'],
                'group_size'   => $data['group_size'],
                'tour_type_id' => $data['tour_type_id'],
                'recommendations' => $data['recommendations'] ?? null, // 🆕 Nuevo campo
                'color'        => $data['color'] ?? '#3490dc',
            ]);

            // Si sigue siendo draft, respetamos el current_step del wizard
            if ($tour->is_draft) {
                $tour->current_step = max($previousStep, 1);
            }

            $tour->updated_by = $userId;
            $tour->save();

            // Sincronizar idiomas
            if (!empty($data['languages'])) {
                $tour->languages()->sync($data['languages']);
            }
        });

        // Si es draft, seguimos el flujo normal del wizard (paso 2).
        // Si ya no es draft, lo más lógico es mandarlo al resumen (paso 6).
        $nextStep = $tour->is_draft ? 2 : 6;

        return redirect()
            ->route('admin.tours.wizard.step', [
                'tour' => $tour,
                'step' => $nextStep,
            ])
            ->with('success', __('m_tours.tour.wizard.details_saved'));
    }



    public function showStep(Tour $tour, int $step)
    {
        // Validar paso válido
        if (!isset(self::STEPS[$step])) {
            return redirect()->route('admin.tours.wizard.step', [
                'tour' => $tour,
                'step' => 1,
            ]);
        }

        $stepName = self::STEPS[$step];
        $userId   = optional(auth()->user())->user_id ?? auth()->id();

        // ==========================
        // Manejo de current_step
        // ==========================
        if ($tour->is_draft) {
            // Para borradores: seguimos usando current_step para “desbloquear” pasos
            $tour->update([
                'current_step' => max($tour->current_step ?? 1, $step),
                'updated_by'   => $userId,
            ]);
        } else {
            // Para tours publicados: aseguramos que tenga al menos 6
            // para que el stepper muestre todos los pasos desbloqueados
            $newCurrent = max($tour->current_step ?? 6, 6);

            if ($tour->current_step !== $newCurrent) {
                $tour->update([
                    'current_step' => $newCurrent,
                    'updated_by'   => $userId,
                ]);
            } else {
                // Igual actualizamos updated_by para trazabilidad
                $tour->update([
                    'updated_by' => $userId,
                ]);
            }
        }

        // Datos comunes
        $data = [
            'tour'  => $tour->load([
                'languages',
                'amenities',
                'excludedAmenities',
                'schedules',
                'prices.category',
                'itinerary.items',
            ]),
            'step'  => $step,
            'steps' => self::STEPS,
        ];

        // Datos específicos por paso
        switch ($stepName) {
            case 'details':
                $data['tourTypes'] = TourType::active()->withTranslation()->get();
                $data['languages'] = TourLanguage::where('is_active', true)->get();
                break;

            case 'itinerary':
                $data['itineraries'] = Itinerary::active()->with(['allItems.translations', 'translations'])->get();
                break;

            case 'schedules':
                $data['schedules'] = Schedule::active()->orderBy('start_time')->get();
                break;

            case 'amenities':
                $data['amenities'] = Amenity::active()->get();
                break;

            case 'prices':
                $data['categories'] = CustomerCategory::active()
                    ->ordered()
                    ->with('translations')
                    ->get();
                $data['taxes'] = Tax::where('is_active', true)->orderBy('sort_order')->get();

                // Group existing prices by periods
                $tour->load(['prices.category']);
                $data['pricingPeriods'] = \App\Models\TourPrice::groupByPeriods($tour->prices);

                break;
        }

        LoggerHelper::info(
            'TourWizardController',
            'showStep',
            'Mostrar paso del wizard',
            [
                'tour_id' => $tour->tour_id ?? $tour->getKey(),
                'step'    => $step,
                'name'    => $stepName,
                'user_id' => $userId,
            ]
        );

        return view("admin.tours.wizard.steps.{$stepName}", $data);
    }


    /**
     * ============================================================
     * GUARDAR ITINERARIO (PASO 2)
     * 🔄 RENOMBRADO: saveItinerary → storeItinerary
     * ============================================================
     */
    public function storeItinerary(Request $request, Tour $tour)
    {
        $userId = optional($request->user())->user_id ?? $request->user()?->getAuthIdentifier();

        // Validación mejorada
        $data = $request->validate([
            'itinerary_id'              => 'nullable|exists:itineraries,itinerary_id',
            'new_itinerary_name'        => 'required_without:itinerary_id|nullable|string|max:255',
            'new_itinerary_description' => 'nullable|string',
            'items'                     => 'required_without:itinerary_id|nullable|array|min:1',
            'items.*.title'             => 'required|string|max:255',
            'items.*.description'       => 'nullable|string',
        ], [
            'new_itinerary_name.required_without' => 'El nombre del itinerario es obligatorio cuando creas uno nuevo.',
            'items.required_without' => 'Debes agregar al menos un item al nuevo itinerario.',
            'items.min' => 'Debes agregar al menos un item al itinerario.',
            'items.*.title.required' => 'Cada item debe tener un título.',
        ]);

        DB::beginTransaction();

        try {
            $itineraryId = $tour->itinerary_id;

            // CASO 1: Asignar itinerario existente
            if (!empty($data['itinerary_id'])) {
                $tour->update([
                    'itinerary_id' => $data['itinerary_id'],
                    'updated_by' => $userId,
                ]);
                $itineraryId = $data['itinerary_id'];

                // CASO 2: Crear itinerario nuevo
            } elseif (!empty($data['new_itinerary_name'])) {

                $itemsData = collect($data['items'] ?? [])
                    ->filter(fn($item) => !empty($item['title']))
                    ->values()
                    ->all();

                if (empty($itemsData)) {
                    return back()
                        ->withInput()
                        ->withErrors([
                            'items' => __('m_tours.itinerary.ui.min_one_item') ?? 'Debes agregar al menos un ítem al itinerario.',
                        ]);
                }

                $itinerary = Itinerary::create([
                    'is_active'   => true,
                ]);

                // Create Spanish translation
                $itinerary->translations()->create([
                    'locale'      => 'es',
                    'name'        => $data['new_itinerary_name'],
                    'description' => $data['new_itinerary_description'] ?? null,
                ]);

                foreach ($itemsData as $index => $itemData) {
                    // Check if item exists by looking for a Spanish translation with this title
                    $existingTranslation = \App\Models\ItineraryItemTranslation::where('locale', 'es')
                        ->where('title', $itemData['title'])
                        ->first();

                    if ($existingTranslation) {
                        $item = ItineraryItem::find($existingTranslation->item_id);
                    } else {
                        $item = null;
                    }

                    if (!$item) {
                        $item = ItineraryItem::create([
                            'is_active'   => true,
                        ]);

                        // Create Spanish translation
                        $item->translations()->create([
                            'locale'      => 'es',
                            'title'       => $itemData['title'],
                            'description' => $itemData['description'] ?? null,
                        ]);
                    } else {
                        // Update existing translation if description is provided and current is empty
                        $translation = $item->translations()->where('locale', 'es')->first();
                        if ($translation && ($itemData['description'] ?? null) && !$translation->description) {
                            $translation->update(['description' => $itemData['description']]);
                        }
                    }

                    DB::table('itinerary_item_itinerary')->updateOrInsert(
                        [
                            'itinerary_id'      => $itinerary->itinerary_id,
                            'itinerary_item_id' => $item->item_id,
                        ],
                        [
                            'item_order' => $index + 1,
                            'is_active'  => true,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }

                $tour->update([
                    'itinerary_id' => $itinerary->itinerary_id,
                    'updated_by' => $userId,
                ]);
                $itineraryId = $itinerary->itinerary_id;
            } else {
                // Si no hay itinerario seleccionado ni datos para crear uno nuevo
                return back()
                    ->withInput()
                    ->withErrors([
                        'itinerary_id' => 'Debes seleccionar un itinerario existente o crear uno nuevo.',
                    ]);
            }

            DB::commit();

            LoggerHelper::mutated(
                'TourWizardController',
                'storeItinerary',
                'tour',
                $tour->tour_id ?? $tour->getKey(),
                [
                    'user_id'      => $userId,
                    'itinerary_id' => $itineraryId,
                    'current_step' => $tour->current_step,
                ]
            );

            return redirect()
                ->route('admin.tours.wizard.step', ['tour' => $tour, 'step' => 3])
                ->with('success', __('m_tours.tour.wizard.itinerary_saved'));
        } catch (\Throwable $e) {
            DB::rollBack();

            LoggerHelper::exception(
                'TourWizardController',
                'storeItinerary',
                'tour',
                $tour->tour_id ?? $tour->getKey(),
                $e,
                ['user_id' => $userId]
            );

            report($e);

            return back()
                ->withInput()
                ->with('error', __('m_tours.common.error_saving'));
        }
    }
    /**
     * ============================================================
     * GUARDAR HORARIOS (PASO 3)
     * 🔄 RENOMBRADO: saveSchedules → storeSchedules
     * ============================================================
     */
    public function storeSchedules(Request $request, Tour $tour)
    {
        $userId = optional($request->user())->getAuthIdentifier();

        $data = $request->validate([
            'schedules'                   => 'required|array|min:1',
            'schedules.*'                 => 'exists:schedules,schedule_id',
            'base_capacity'               => 'nullable|array',
            'base_capacity.*'             => 'nullable|integer|min:1|max:999',
            'new_schedule.create'         => 'nullable|boolean',
            'new_schedule.start_time'     => 'required_if:new_schedule.create,1|date_format:H:i',
            'new_schedule.end_time'       => 'required_if:new_schedule.create,1|date_format:H:i|after:new_schedule.start_time',
            'new_schedule.label'          => 'nullable|string|max:100',
            'new_schedule.base_capacity'  => 'nullable|integer|min:1|max:999',
        ], [
            'schedules.required' => __('m_tours.schedule.validation.no_schedule_selected') ?? 'Debes seleccionar al menos un horario.',
            'schedules.min' => __('m_tours.schedule.validation.no_schedule_selected') ?? 'Debes seleccionar al menos un horario.',
            'new_schedule.end_time.after' => __('m_tours.schedule.validation.end_after_start') ?? 'La hora de fin debe ser posterior a la hora de inicio.',
        ]);

        DB::beginTransaction();

        try {
            $selectedIds   = $data['schedules'] ?? [];
            $baseCapacity  = $data['base_capacity'] ?? [];

            // Crear nuevo horario si se solicitó
            if (!empty($data['new_schedule']['create'])) {
                $newSchedule = Schedule::create([
                    'start_time' => $data['new_schedule']['start_time'],
                    'end_time'   => $data['new_schedule']['end_time'],
                    'label'      => $data['new_schedule']['label'] ?? null,
                    'is_active'  => true,
                ]);

                $selectedIds[] = $newSchedule->schedule_id;

                if (!empty($data['new_schedule']['base_capacity'])) {
                    $baseCapacity[$newSchedule->schedule_id] = (int) $data['new_schedule']['base_capacity'];
                }
            }

            // Armar payload de sync con datos de pivote
            $pivotPayload = [];

            foreach ($selectedIds as $scheduleId) {
                $capacity = $baseCapacity[$scheduleId] ?? null;

                $pivotPayload[$scheduleId] = [
                    'is_active'     => true,
                    'base_capacity' => $capacity !== null && $capacity !== '' ? (int) $capacity : null,
                ];
            }

            // Sincronizar horarios
            $tour->schedules()->sync($pivotPayload);

            // Actualizar updated_by
            $tour->update(['updated_by' => $userId]);

            DB::commit();

            LoggerHelper::mutated(
                'TourWizardController',
                'storeSchedules',
                'tour',
                $tour->tour_id ?? $tour->getKey(),
                [
                    'user_id'        => $userId,
                    'schedule_ids'   => array_values($selectedIds),
                    'pivot_payload'  => $pivotPayload,
                ]
            );

            return redirect()
                ->route('admin.tours.wizard.step', ['tour' => $tour, 'step' => 4])
                ->with('success', __('m_tours.tour.wizard.schedules_saved'));
        } catch (\Throwable $e) {
            DB::rollBack();

            LoggerHelper::exception(
                'TourWizardController',
                'storeSchedules',
                'tour',
                $tour->tour_id ?? $tour->getKey(),
                $e,
                ['user_id' => $userId]
            );

            report($e);

            return back()
                ->withInput()
                ->with('error', __('m_tours.common.error_saving'));
        }
    }
    /**
     * ============================================================
     * GUARDAR AMENIDADES (PASO 4)
     * 🔄 RENOMBRADO: saveAmenities → storeAmenities
     * ============================================================
     */
    public function storeAmenities(Request $request, Tour $tour)
    {
        $data = $request->validate([
            'included_amenities'   => 'nullable|array',
            'included_amenities.*' => 'exists:amenities,amenity_id',
            'excluded_amenities'   => 'nullable|array',
            'excluded_amenities.*' => 'exists:amenities,amenity_id',
        ]);

        $userId = optional($request->user())->user_id ?? $request->user()?->getAuthIdentifier();

        DB::beginTransaction();
        try {
            $included = $data['included_amenities'] ?? [];
            $excluded = $data['excluded_amenities'] ?? [];

            // Quitar de excluidas las que están incluidas
            $excluded = array_values(array_diff($excluded, $included));

            $tour->amenities()->sync($included);
            $tour->excludedAmenities()->sync($excluded);

            // 🆕 Actualizar updated_by
            $tour->update(['updated_by' => $userId]);

            DB::commit();

            LoggerHelper::mutated(
                'TourWizardController',
                'storeAmenities',
                'tour',
                $tour->tour_id ?? $tour->getKey(),
                [
                    'user_id'            => $userId,
                    'included_amenities' => $included,
                    'excluded_amenities' => $excluded,
                    'step'               => 4,
                ]
            );

            return redirect()
                ->route('admin.tours.wizard.step', ['tour' => $tour, 'step' => 5])
                ->with('success', __('m_tours.tour.wizard.amenities_saved'));
        } catch (\Throwable $e) {
            DB::rollBack();

            LoggerHelper::exception(
                'TourWizardController',
                'storeAmenities',
                'tour',
                $tour->tour_id ?? $tour->getKey(),
                $e,
                ['user_id' => $userId]
            );

            report($e);
            return back()->withInput()->with('error', __('m_tours.common.error_saving'));
        }
    }

    /**
     * ============================================================
     * GUARDAR PRECIOS (PASO 5) - NUEVA ESTRUCTURA POR PERIODOS
     * ============================================================
     */
    public function storePrices(Request $request, Tour $tour)
    {
        $data = $request->validate([
            'periods'                            => 'required|array|min:1',
            'periods.*.valid_from'               => 'nullable|date',
            'periods.*.valid_until'              => 'nullable|date|after_or_equal:periods.*.valid_from',
            'periods.*.label'                    => 'nullable|string|max:255',
            'periods.*.categories'               => 'required|array|min:1',
            'periods.*.categories.*.category_id' => 'required|exists:customer_categories,category_id',
            'periods.*.categories.*.price'       => 'required|numeric|min:0',
            'periods.*.categories.*.min_quantity' => 'nullable|integer|min:0',
            'periods.*.categories.*.max_quantity' => 'nullable|integer|min:0',
            'periods.*.categories.*.is_active'   => 'nullable|boolean',
            'taxes'                              => 'nullable|array',
            'taxes.*'                            => 'exists:taxes,tax_id',
        ], [
            'periods.required' => __('m_tours.tour.pricing.add_at_least_one_period') ?? 'Debes agregar al menos un periodo de precios.',
            'periods.min' => __('m_tours.tour.pricing.add_at_least_one_period') ?? 'Debes agregar al menos un periodo de precios.',
            'periods.*.categories.required' => __('m_tours.tour.pricing.add_at_least_one_category') ?? 'Debes agregar al menos una categoría a cada periodo.',
            'periods.*.categories.min' => __('m_tours.tour.pricing.add_at_least_one_category') ?? 'Debes agregar al menos una categoría a cada periodo.',
            'periods.*.categories.*.price.required' => __('m_tours.prices.validation.price_required') ?? 'El precio es obligatorio.',
            'periods.*.categories.*.price.min' => __('m_tours.prices.validation.price_min') ?? 'El precio debe ser mayor o igual a 0.',
            'periods.*.valid_until.after_or_equal' => __('m_tours.tour.pricing.invalid_date_range') ?? 'La fecha de fin debe ser posterior o igual a la fecha de inicio.',
        ]);

        $userId = optional($request->user())->user_id ?? $request->user()?->getAuthIdentifier();

        // Validar que al menos un precio sea mayor a 0
        $hasPriceGreaterThanZero = false;
        foreach ($data['periods'] as $period) {
            foreach ($period['categories'] as $category) {
                if (isset($category['price']) && floatval($category['price']) > 0) {
                    $hasPriceGreaterThanZero = true;
                    break 2;
                }
            }
        }

        if (!$hasPriceGreaterThanZero) {
            return back()
                ->withInput()
                ->withErrors([
                    'periods' => __('m_tours.prices.validation.no_price_greater_zero') ?? 'Debe haber al menos una categoría con precio mayor a $0.00',
                ]);
        }

        DB::beginTransaction();
        try {
            // Eliminar precios existentes
            $tour->prices()->delete();

            $totalPricesCreated = 0;

            // Crear precios por periodo
            foreach ($data['periods'] as $period) {
                $validFrom = !empty($period['valid_from']) ? $period['valid_from'] : null;
                $validUntil = !empty($period['valid_until']) ? $period['valid_until'] : null;
                $label = !empty($period['label']) ? $period['label'] : null;

                foreach ($period['categories'] as $categoryData) {
                    $tour->prices()->create([
                        'category_id'  => $categoryData['category_id'],
                        'price'        => $categoryData['price'],
                        'min_quantity' => $categoryData['min_quantity'] ?? 0,
                        'max_quantity' => $categoryData['max_quantity'] ?? 12,
                        'is_active'    => isset($categoryData['is_active']) ? (bool)$categoryData['is_active'] : true,
                        'valid_from'   => $validFrom,
                        'valid_until'  => $validUntil,
                        'label'        => $label,
                    ]);
                    $totalPricesCreated++;
                }
            }

            // Sincronizar impuestos
            if (isset($data['taxes'])) {
                $tour->taxes()->sync($data['taxes']);
            } else {
                $tour->taxes()->detach();
            }

            // Actualizar updated_by
            $tour->update(['updated_by' => $userId]);

            DB::commit();

            LoggerHelper::mutated(
                'TourWizardController',
                'storePrices',
                'tour',
                $tour->tour_id ?? $tour->getKey(),
                [
                    'user_id'        => $userId,
                    'step'           => 5,
                    'periods_count'  => count($data['periods']),
                    'prices_count'   => $totalPricesCreated,
                ]
            );

            return redirect()
                ->route('admin.tours.wizard.step', ['tour' => $tour, 'step' => 6])
                ->with('success', __('m_tours.tour.wizard.prices_saved'));
        } catch (\Throwable $e) {
            DB::rollBack();

            LoggerHelper::exception(
                'TourWizardController',
                'storePrices',
                'tour',
                $tour->tour_id ?? $tour->getKey(),
                $e,
                ['user_id' => $userId]
            );

            report($e);
            return back()->withInput()->with('error', __('m_tours.common.error_saving'));
        }
    }

    /**
     * Publicar tour (finalizar wizard)
     */
    public function publish(Tour $tour)
    {
        if (!$tour->is_draft) {
            return redirect()
                ->route('admin.tours.index')
                ->with('info', __('m_tours.tour.wizard.already_published'));
        }

        $userId = optional(auth()->user())->user_id ?? auth()->id();

        // 🆕 Actualizar con updated_by
        $tour->update([
            'is_draft'     => false,
            'is_active'    => true,
            'current_step' => max($tour->current_step, 6),
            'updated_by'   => $userId,  // 🆕
        ]);

        LoggerHelper::mutated(
            'TourWizardController',
            'publish',
            'tour',
            $tour->tour_id ?? $tour->getKey(),
            [
                'user_id'   => $userId,
                'is_draft'  => $tour->is_draft,
                'is_active' => $tour->is_active,
            ]
        );

        return redirect()
            ->route('admin.tours.index')
            ->with('success', __('m_tours.tour.wizard.published_successfully'));
    }

    /**
     * QUICK CREATE: Tipo de Tour (AJAX)
     */
    public function quickStoreTourType(Request $request, TranslatorInterface $translator)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration'    => 'nullable|string|max:255',
            'is_active'   => 'boolean',
        ]);

        $userId = optional($request->user())->user_id ?? $request->user()?->getAuthIdentifier();

        $type = DB::transaction(function () use ($data, $translator) {
            // 1. Crear TourType solo con campos no traducibles
            $type = TourType::create([
                'is_active' => $data['is_active'] ?? true,
            ]);

            // 2. Traducir campos
            $nameTr = $translator->translateAll($data['name']);
            $descTr = isset($data['description']) ? $translator->translateAll($data['description']) : [];
            $durTr  = isset($data['duration']) ? $translator->translateAll($data['duration']) : [];

            // 3. Crear traducciones para todos los locales
            foreach (supported_locales() as $locale) {
                $type->translations()->create([
                    'locale'      => $locale,
                    'name'        => $nameTr[$locale] ?? $data['name'],
                    'description' => $descTr[$locale] ?? ($data['description'] ?? null),
                    'duration'    => $durTr[$locale] ?? ($data['duration'] ?? null),
                ]);
            }

            return $type;
        });

        LoggerHelper::mutated(
            'TourWizardController',
            'quickStoreTourType',
            'tour_type',
            $type->tour_type_id ?? $type->getKey(),
            ['user_id' => $userId]
        );

        return response()->json([
            'id'   => $type->tour_type_id,
            'name' => $type->name, // Usa el accessor mágico
        ], 201);
    }

    /**
     * QUICK CREATE: Idioma (AJAX)
     */
    public function quickStoreLanguage(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $userId = optional($request->user())->user_id ?? $request->user()?->getAuthIdentifier();

        $language = TourLanguage::create([
            'name'      => $data['name'],
            'is_active' => true,
        ]);

        LoggerHelper::mutated(
            'TourWizardController',
            'quickStoreLanguage',
            'tour_language',
            $language->tour_language_id ?? $language->getKey(),
            ['user_id' => $userId]
        );

        return response()->json([
            'id'   => $language->tour_language_id,
            'name' => $language->name,
        ], 201);
    }

    /**
     * QUICK CREATE: Itinerary Item suelto (AJAX)
     */
    public function quickCreateItineraryItem(Request $request, TranslatorInterface $translator)
    {
        if (!$request->ajax() && !$request->wantsJson()) {
            abort(404);
        }

        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $userId = optional($request->user())->user_id ?? $request->user()?->getAuthIdentifier();

        $item = DB::transaction(function () use ($data, $translator) {
            $item = ItineraryItem::create([
                'is_active'   => true,
            ]);

            $titleTr = $translator->translateAll($data['title']);
            $descTr  = isset($data['description']) ? $translator->translateAll($data['description']) : [];

            foreach (supported_locales() as $locale) {
                ItineraryItemTranslation::create([
                    'item_id'     => $item->item_id,
                    'locale'      => $locale,
                    'title'       => $titleTr[$locale] ?? $data['title'],
                    'description' => $descTr[$locale] ?? ($data['description'] ?? null),
                ]);
            }

            return $item;
        });

        LoggerHelper::mutated(
            'TourWizardController',
            'quickCreateItineraryItem',
            'itinerary_item',
            $item->item_id ?? $item->getKey(),
            ['user_id' => $userId]
        );

        return response()->json([
            'id'          => $item->item_id,
            'title'       => $item->title, // Magic accessor
            'description' => $item->description, // Magic accessor
        ], 201);
    }

    /**
     * QUICK CREATE: Horario desde el wizard (Paso 3)
     */
    public function quickStoreSchedule(Request $request, Tour $tour)
    {
        if (!$tour->is_draft) {
            return redirect()
                ->route('admin.tours.edit', $tour)
                ->with('info', __('m_tours.tour.wizard.already_published'));
        }

        $userId = optional($request->user())->user_id ?? $request->user()?->getAuthIdentifier();

        $data = $request->validate([
            'start_time'    => ['required', 'date_format:H:i'],
            'end_time'      => ['required', 'date_format:H:i', 'after:start_time'],
            'label'         => ['nullable', 'string', 'max:100'],
            'base_capacity' => ['nullable', 'integer', 'min:1', 'max:999'],
            'is_active'     => ['nullable', 'boolean'],
        ]);

        DB::beginTransaction();

        try {
            $schedule = Schedule::create([
                'start_time' => $data['start_time'],
                'end_time'   => $data['end_time'],
                'label'      => $data['label'] ?? null,
                'is_active'  => $data['is_active'] ?? true,
            ]);

            $pivotData = ['is_active' => true];

            if (!empty($data['base_capacity'])) {
                $pivotData['base_capacity'] = (int) $data['base_capacity'];
            }

            $tour->schedules()->syncWithoutDetaching([
                $schedule->schedule_id => $pivotData,
            ]);

            // 🆕 Actualizar updated_by
            $tour->update(['updated_by' => $userId]);

            DB::commit();

            LoggerHelper::mutated(
                'TourWizardController',
                'quickStoreSchedule',
                'schedule',
                $schedule->schedule_id ?? $schedule->getKey(),
                [
                    'tour_id'       => $tour->tour_id ?? $tour->getKey(),
                    'base_capacity' => $data['base_capacity'] ?? null,
                    'user_id'       => $userId,
                ]
            );

            return redirect()
                ->route('admin.tours.wizard.step', ['tour' => $tour, 'step' => 3])
                ->with('success', __('m_tours.schedule.success.created_and_attached'));
        } catch (\Throwable $e) {
            DB::rollBack();

            LoggerHelper::exception(
                'TourWizardController',
                'quickStoreSchedule',
                'schedule',
                null,
                $e,
                [
                    'tour_id' => $tour->tour_id ?? $tour->getKey(),
                    'user_id' => $userId,
                ]
            );

            report($e);

            return redirect()
                ->route('admin.tours.wizard.step', ['tour' => $tour, 'step' => 3])
                ->withInput()
                ->with('error', __('m_tours.schedule.error.create_from_wizard'));
        }
    }

    /**
     * QUICK CREATE: Amenidad (AJAX)
     */
    public function quickStoreAmenity(Request $request, TranslatorInterface $translator)
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('amenity_translations', 'name')->where('locale', 'es'),
            ],
        ]);

        $userId = optional($request->user())->user_id ?? $request->user()?->getAuthIdentifier();

        try {
            $amenity = DB::transaction(function () use ($data, $translator) {
                // Create amenity without name field
                $amenity = Amenity::create([
                    'is_active' => true,
                ]);

                // Translate name
                $nameTr = $translator->translateAll($data['name']);

                // Create translations for all locales
                foreach (supported_locales() as $locale) {
                    AmenityTranslation::create([
                        'amenity_id' => $amenity->amenity_id,
                        'locale'     => $locale,
                        'name'       => $nameTr[$locale] ?? $data['name'],
                    ]);
                }

                return $amenity;
            });

            LoggerHelper::mutated(
                'TourWizardController',
                'quickStoreAmenity',
                'amenity',
                $amenity->amenity_id ?? $amenity->getKey(),
                ['user_id' => $userId]
            );

            return response()->json([
                'id'      => $amenity->amenity_id,
                'name'    => $amenity->name, // Magic accessor will get from translation
                'message' => __('m_tours.amenity.quick_create.success_text'),
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            LoggerHelper::exception(
                'TourWizardController',
                'quickStoreAmenity',
                'amenity',
                null,
                $e,
                ['user_id' => $userId]
            );
            return response()->json(['message' => 'Error creating amenity'], 500);
        }
    }

    /**
     * QUICK CREATE: Customer Category (AJAX)
     */
    public function quickStoreCategory(Request $request, TranslatorInterface $translator)
    {
        Log::info('quickStoreCategory: Start', $request->all());

        // if (!$request->ajax() && !$request->wantsJson()) {
        //     Log::warning('quickStoreCategory: Not AJAX/JSON request');
        //     abort(404);
        // }

        try {
            $data = $request->validate([
                'name'      => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('customer_category_translations', 'name')->where('locale', 'es'),
                ],
                'slug'      => [
                    'required',
                    'string',
                    'max:50',
                    'unique:customer_categories,slug',
                ],
                'age_from'  => ['required', 'integer', 'min:0', 'max:120'],
                'age_to'    => [
                    'nullable',
                    'integer',
                    'min:0',
                    'max:120',
                    function ($attribute, $value, $fail) use ($request) {
                        if (!is_null($value) && $value < $request->input('age_from')) {
                            $fail(__('m_tours.prices.validation.age_to_greater_equal'));
                        }
                    },
                ],
                'is_active' => ['nullable', 'boolean'],
            ]);
            Log::info('quickStoreCategory: Validation passed', $data);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('quickStoreCategory: Validation failed', $e->errors());
            throw $e;
        }

        $userId = optional($request->user())->user_id ?? $request->user()?->getAuthIdentifier();

        // Slug is now provided by the user
        $slug = $data['slug'];
        Log::info('quickStoreCategory: Using provided slug', ['slug' => $slug]);

        try {
            $category = DB::transaction(function () use ($data, $slug, $translator) {
                Log::info('quickStoreCategory: Creating category base');
                $category = \App\Models\CustomerCategory::create([
                    'slug'      => $slug,
                    'age_from'  => $data['age_from'],
                    'age_to'    => $data['age_to'], // Can be null
                    'is_active' => $data['is_active'] ?? true,
                ]);
                Log::info('quickStoreCategory: Category base created', ['id' => $category->category_id]);

                Log::info('quickStoreCategory: Translating name');
                $nameTr = $translator->translateAll($data['name']);
                Log::info('quickStoreCategory: Translations received', $nameTr);

                foreach (supported_locales() as $locale) {
                    CustomerCategoryTranslation::create([
                        'category_id' => $category->category_id,
                        'locale'      => $locale,
                        'name'        => $nameTr[$locale] ?? $data['name'],
                    ]);
                }
                Log::info('quickStoreCategory: Translations saved');

                return $category;
            });

            LoggerHelper::mutated(
                'TourWizardController',
                'quickStoreCategory',
                'customer_category',
                $category->category_id ?? $category->getKey(),
                [
                    'user_id'   => $userId,
                    'age_from'  => $category->age_from,
                    'age_to'    => $category->age_to,
                    'is_active' => $category->is_active,
                ]
            );

            $ageLabel = $category->age_from . ' - ' . $category->age_to;

            Log::info('quickStoreCategory: Success', ['id' => $category->category_id]);

            return response()->json([
                'id'        => $category->category_id,
                'name'      => $category->getTranslatedName(), // Use helper method
                'age_range' => $ageLabel,
                'slug'      => $category->slug,
                'message'   => __('m_tours.prices.quick_category.created_ok'),
            ], 201);
        } catch (\Exception $e) {
            Log::error('quickStoreCategory: Exception caught', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            LoggerHelper::exception(
                'TourWizardController',
                'quickStoreCategory',
                'customer_category',
                null,
                $e,
                ['user_id' => $userId]
            );
            return response()->json(['message' => 'Error creating category: ' . $e->getMessage()], 500);
        }
    }
}
