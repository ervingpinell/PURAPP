<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Models\{
    Product,     // Was Tour
    ProductType, // Was TourType
    ProductLanguage,
    ProductAuditLog, // Was TourAuditLog
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
// use App\Services\Contracts\TranslatorInterface;
// use App\Models\TourTypeTranslation; // Removed
// use App\Models\ItineraryItemTranslation; // Removed
// use App\Models\AmenityTranslation; // Removed
// use App\Models\CustomerCategoryTranslation; // Removed
use Carbon\Carbon;

/**
 * ProductWizardController
 *
 * Handles productwizard operations.
 */
class ProductWizardController extends Controller
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

        return view('admin.products.product-wizard.steps.details', [
            'tour'           => null,
            'tourTypes'      => ProductType::active()->get(),
            'languages'      => ProductLanguage::where('is_active', true)->get(),
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
    public function edit(Product $product)
    {
        $userId = optional(auth()->user())->user_id ?? auth()->id();

        // Si el tour ya está publicado, asegurar que current_step sea 6
        if (!$product->is_draft && $product->current_step < 6) {
            $product->update([
                'current_step' => 6,
                'updated_by'   => $userId,
            ]);
        }

        // Si es draft, ir al paso donde se quedó
        if ($product->is_draft) {
            $step = $product->current_step ?? 1;

            LoggerHelper::info($this->controller ?? 'ProductWizardController', 'edit', 'Redirecting to wizard step', [
                'product_id' => $product->product_id,
                'step'    => $step,
                'user_id' => $userId
            ]);

            return redirect()->route('admin.products.product-wizard.step', [
                'product' => $product,
                'step' => $step
            ]);
        }

        // Si ya está publicado, ir al resumen (paso 6)
        LoggerHelper::info($this->controller ?? 'ProductWizardController', 'edit', 'Redirecting to wizard summary', [
            'product_id' => $product->product_id,
            'user_id' => $userId
        ]);

        return redirect()->route('admin.products.product-wizard.step', [
            'product' => $product,
            'step' => 6
        ]);
    }


    /**
     * ============================================================
     * CONTINUAR CON DRAFT EXISTENTE
     * ============================================================
     */
    public function continueDraft(Product $product)
    {
        $userId = optional(auth()->user())->user_id ?? auth()->id();

        // Verificar que sea draft
        if (!$product->is_draft) {
            return redirect()
                ->route('admin.products.index')
                ->with('error', __('m_tours.tour.wizard.not_a_draft'));
        }

        // 🆕 VERIFICAR PROPIEDAD (seguridad)
        if ($product->created_by && $product->created_by !== $userId) {
            abort(403, 'No autorizado para editar este borrador');
        }

        // 🆕 LOG DE AUDITORÍA
        ProductAuditLog::logAction(
            action: 'draft_continued',
            productId: $product->product_id, // Argument name likely changed? Check ProductAuditLog later. Assuming productId or standard naming.
            description: "Usuario continuó editando el borrador '{$product->name}'",
            context: 'wizard',
            wizardStep: $product->current_step ?? 1,
            tags: ['draft', 'wizard', 'continued']
        );

        LoggerHelper::info(
            'ProductWizardController',
            'continueDraft',
            'Continuar con draft existente',
            [
                'product_id'      => $product->product_id,
                'user_id'      => $userId,
                'current_step' => $product->current_step,
            ]
        );

        // Redirigir al paso donde se quedó
        $nextStep = $product->current_step ?? 1;

        return redirect()
            ->route('admin.products.product-wizard.step', ['product' => $product, 'step' => $nextStep])
            ->with('success', __('m_tours.tour.wizard.continuing_draft'));
    }

    /**
     * ============================================================
     * ELIMINAR DRAFT ESPECÍFICO
     * ============================================================
     */
    public function deleteDraft(Product $product)
    {
        $userId = optional(auth()->user())->user_id ?? auth()->id();

        // 🔍 LOG DE ENTRADA
        LoggerHelper::info(
            'ProductWizardController',
            'deleteDraft',
            'Entrada a deleteDraft desde UI',
            [
                'tour_route_param_id' => $product->product_id ?? $product->getKey(),
                'tour_slug'           => $product->slug ?? null,
                'is_draft'            => $product->is_draft,
                'created_by'          => $product->created_by,
                'user_id'             => $userId,
                'http_method'         => request()->method(),
                'route_name'          => optional(request()->route())->getName(),
                'full_url'            => request()->fullUrl(),
                'referer'             => request()->headers->get('referer'),
            ]
        );

        // Verificar que sea draft
        if (!$product->is_draft) {
            LoggerHelper::info(
                'ProductWizardController',
                'deleteDraft',
                'Intento de borrar tour que no es borrador',
                [
                    'product_id' => $product->product_id ?? $product->getKey(),
                    'user_id' => $userId,
                ]
            );

            return redirect()
                ->route('admin.products.index')
                ->with('error', __('m_tours.tour.wizard.not_a_draft'));
        }

        // Verificar propiedad (seguridad)
        if ($product->created_by && $product->created_by !== $userId) {
            LoggerHelper::info(
                'ProductWizardController',
                'deleteDraft',
                'Usuario no autorizado para eliminar este borrador',
                [
                    'product_id'      => $product->product_id ?? $product->getKey(),
                    'created_by'   => $product->created_by,
                    'current_user' => $userId,
                ]
            );

            abort(403, 'No autorizado para eliminar este borrador');
        }

        DB::beginTransaction();
        try {
            $productId   = $product->product_id;
            $productName = $product->name;

            LoggerHelper::info(
                'ProductWizardController',
                'deleteDraft',
                'Comenzando eliminación de borrador y relaciones',
                [
                    'product_id'       => $productId,
                    'user_id'       => $userId,
                    'has_itinerary' => (bool) $product->itinerary_id,
                ]
            );

            // Eliminar relaciones
            $product->languages()->detach();
            $product->amenities()->detach();
            $product->excludedAmenities()->detach();
            $product->schedules()->detach();
            $product->prices()->delete();

            if ($product->itinerary_id && $product->itinerary) {
                LoggerHelper::info(
                    'ProductWizardController',
                    'deleteDraft',
                    'Eliminando itinerario asociado al borrador',
                    [
                        'product_id'      => $productId,
                        'itinerary_id' => $product->itinerary_id,
                    ]
                );

                $product->itinerary->delete();
            }

            LoggerHelper::info(
                'ProductWizardController',
                'deleteDraft',
                'Ejecutando forceDelete del tour borrador SIN eventos',
                [
                    'product_id' => $productId,
                    'user_id' => $userId,
                ]
            );

            // 🔧 CLAVE: evitar que el trait Auditable dispare auditDeleted
            // y trate de insertar en tour_audit_logs después de borrar el tour.
            \App\Models\Product::withoutEvents(function () use ($product) {
                $product->forceDelete();
            });

            DB::commit();

            LoggerHelper::mutated(
                'ProductWizardController',
                'deleteDraft',
                'tour',
                $productId,
                [
                    'user_id' => $userId,
                    'action'  => 'draft_deleted',
                    'note'    => 'Borrador eliminado con forceDelete sin eventos Eloquent',
                ]
            );

            return redirect()
                ->route('admin.products.product-wizard.create')
                ->with('success', __('m_tours.tour.wizard.draft_deleted'));
        } catch (\Throwable $e) {
            DB::rollBack();

            LoggerHelper::exception(
                'ProductWizardController',
                'deleteDraft',
                'tour',
                $product->product_id ?? $product->getKey(),
                $e,
                [
                    'user_id'    => $userId,
                    'route_name' => optional(request()->route())->getName(),
                    'full_url'   => request()->fullUrl(),
                ]
            );

            report($e);

            return redirect()
                ->route('admin.products.product-wizard.create')
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
            $drafts = Product::where('is_draft', true)
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
                'ProductWizardController',
                'deleteAllDrafts',
                'tour',
                null,
                [
                    'user_id'       => $userId,
                    'deleted_count' => $deletedCount,
                ]
            );

            return redirect()
                ->route('admin.products.product-wizard.create')
                ->with('success', __('m_tours.tour.wizard.all_drafts_deleted', ['count' => $deletedCount]));
        } catch (\Throwable $e) {
            DB::rollBack();
            LoggerHelper::exception('ProductWizardController', 'deleteAllDrafts', 'tour', null, $e, ['user_id' => $userId]);
            report($e);
            return redirect()
                ->route('admin.products.product-wizard.create')
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
            'product_type_id' => 'required|exists:product_types,product_type_id', // Ahora requerido
            'is_active'    => 'boolean',
            'languages'    => 'required|array|min:1', // Ahora requerido con mínimo 1
            'languages.*'  => 'exists:tour_languages,tour_language_id',
            'recommendations' => 'nullable|string', // 🆕 Nuevo campo
        ]);

        $product = null;

        DB::beginTransaction();
        try {
            if (empty($data['slug'])) {
                $data['slug'] = Product::generateUniqueSlug($data['name']);
            }

            // 🆕 CREAR CON created_by y updated_by
            $product = Product::create([
                'name'         => $data['name'],
                'slug'         => $data['slug'],
                'overview'     => $data['overview'] ?? null,
                'length'       => $data['length'] ?? null,
                'max_capacity' => $data['max_capacity'],
                'group_size'   => $data['group_size'] ?? null,
                'recommendations' => $data['recommendations'] ?? null, // 🆕 Nuevo campo
                'color'        => $data['color'] ?? '#3490dc',
                'product_type_id' => $data['product_type_id'] ?? null,
                'is_active'    => $data['is_active'] ?? false,
                'is_draft'     => true,
                'current_step' => 1,
                'created_by'   => $userId,  // 🆕
                'updated_by'   => $userId,  // 🆕
            ]);

            if (!empty($data['languages'])) {
                $product->languages()->sync($data['languages']);
            }

            DB::commit();

            // El trait Auditable ya registra el log automáticamente

            LoggerHelper::mutated(
                'ProductWizardController',
                'storeDetails',
                'tour',
                $product->product_id,
                [
                    'user_id'      => $userId,
                    'current_step' => 1,
                    'is_draft'     => true,
                ]
            );

            return redirect()
                ->route('admin.products.product-wizard.step', ['product' => $product, 'step' => 2])
                ->with('success', __('m_tours.tour.wizard.details_saved'));
        } catch (\Throwable $e) {
            DB::rollBack();
            LoggerHelper::exception('ProductWizardController', 'storeDetails', 'tour', $product?->product_id, $e, ['user_id' => $userId]);
            report($e);
            return back()->withInput()->with('error', __('m_tours.common.error_saving'));
        }
    }

    /**
     * Actualizar detalles de un tour en borrador (volver del paso 2, 3, etc.)
     */
    public function updateDetails(Request $request, Product $product)
    {
        LoggerHelper::info(
            'ProductWizardController',
            'updateDetails',
            'Entrada a updateDetails',
            [
                'product_id'   => $product->product_id ?? null,
                'slug'      => $product->slug ?? null,
                'is_draft'  => $product->is_draft,
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
                    ->ignore($product->getKey(), $product->getKeyName())
                    ->whereNull('deleted_at'),
            ],
            'overview'     => ['required', 'string', 'max:1000'],
            'length'       => ['required', 'numeric', 'min:0.5', 'max:240'],
            'max_capacity' => ['required', 'integer', 'min:1', 'max:500'],
            'group_size'   => ['required', 'integer', 'min:1', 'max:500'],
            'product_type_id' => ['required', 'integer', 'exists:product_types,product_type_id'],
            'color'        => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'languages'    => ['required', 'array', 'min:1'],
            'languages.*'  => ['integer', 'exists:tour_languages,tour_language_id'],
            'recommendations' => ['nullable', 'string'], // 🆕 Nuevo campo
        ]);

        $data['overview'] = trim($data['overview'] ?? '');

        DB::transaction(function () use ($product, $data, $userId) {
            $previousStep = (int) ($product->current_step ?? 1);

            // Actualizar campos básicos (NO tocamos is_draft aquí)
            $product->fill([
                'name'         => $data['name'],
                'slug'         => $data['slug'],
                'overview'     => $data['overview'],
                'length'       => $data['length'],
                'max_capacity' => $data['max_capacity'],
                'group_size'   => $data['group_size'],
                'product_type_id' => $data['product_type_id'],
                'recommendations' => $data['recommendations'] ?? null, // 🆕 Nuevo campo
                'color'        => $data['color'] ?? '#3490dc',
            ]);

            // Si sigue siendo draft, respetamos el current_step del wizard
            if ($product->is_draft) {
                $product->current_step = max($previousStep, 1);
            }

            $product->updated_by = $userId;
            $product->save();

            // Sincronizar idiomas
            if (!empty($data['languages'])) {
                $product->languages()->sync($data['languages']);
            }
        });

        // Si es draft, seguimos el flujo normal del wizard (paso 2).
        // Si ya no es draft, lo más lógico es mandarlo al resumen (paso 6).
        $nextStep = $product->is_draft ? 2 : 6;

        return redirect()
            ->route('admin.products.product-wizard.step', [
                'product' => $product,
                'step' => $nextStep,
            ])
            ->with('success', __('m_tours.tour.wizard.details_saved'));
    }



    public function showStep(Product $product, int $step)
    {
        // Validar paso válido
        if (!isset(self::STEPS[$step])) {
            return redirect()->route('admin.products.product-wizard.step', [
                'product' => $product,
                'step' => 1,
            ]);
        }

        $stepName = self::STEPS[$step];
        $userId   = optional(auth()->user())->user_id ?? auth()->id();

        // ==========================
        // Manejo de current_step
        // ==========================
        if ($product->is_draft) {
            // Para borradores: seguimos usando current_step para “desbloquear” pasos
            $product->update([
                'current_step' => max($product->current_step ?? 1, $step),
                'updated_by'   => $userId,
            ]);
        } else {
            // Para tours publicados: aseguramos que tenga al menos 6
            // para que el stepper muestre todos los pasos desbloqueados
            $newCurrent = max($product->current_step ?? 6, 6);

            if ($product->current_step !== $newCurrent) {
                $product->update([
                    'current_step' => $newCurrent,
                    'updated_by'   => $userId,
                ]);
            } else {
                // Igual actualizamos updated_by para trazabilidad
                $product->update([
                    'updated_by' => $userId,
                ]);
            }
        }

        // Datos comunes
        $data = [
            'product'  => $product->load([
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
                $data['productTypes'] = ProductType::active()->get();
                $data['languages'] = ProductLanguage::where('is_active', true)->get();
                break;

            case 'itinerary':
                $data['itineraries'] = Itinerary::withTrashed()
                    ->with(['items']) // translations removed
                    ->get();
                break;

            case 'schedules':
                $data['schedules'] = Schedule::active()->orderBy('start_time')->get();
                $product->load(['schedules' => function ($query) {
                    $query->withPivot(['is_active', 'cutoff_hour', 'lead_days', 'base_capacity']);
                }]);
                break;

            case 'amenities':
                // Cargar todas para que el admin pueda gestionar las inactivas si ya estaban asignadas
                $data['amenities'] = Amenity::withTrashed()->get();
                break;

            case 'prices':
                $data['categories'] = CustomerCategory::active()
                    ->ordered()
                    ->ordered()
                    ->get();
                $data['taxes'] = Tax::where('is_active', true)->orderBy('sort_order')->get();

                // Group existing prices by periods
                $product->load(['prices.category']);
                $data['pricingPeriods'] = \App\Models\ProductPrice::groupByPeriods($product->prices);

                break;
        }

        LoggerHelper::info(
            'ProductWizardController',
            'showStep',
            'Mostrar paso del wizard',
            [
                'product_id' => $product->product_id ?? $product->getKey(),
                'step'    => $step,
                'name'    => $stepName,
                'user_id' => $userId,
            ]
        );

        return view("admin.products.wizard.steps.{$stepName}", $data);
    }


    /**
     * ============================================================
     * GUARDAR ITINERARIO (PASO 2)
     * 🔄 RENOMBRADO: saveItinerary → storeItinerary
     * ============================================================
     */
    public function storeItinerary(Request $request, Product $product)
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
            $itineraryId = $product->itinerary_id;

            // CASO 1: Asignar itinerario existente
            if (!empty($data['itinerary_id'])) {
                $product->update([
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

                // Create Spanish translation using Spatie
                $itinerary->setTranslation('name', 'es', $data['new_itinerary_name']);
                if (!empty($data['new_itinerary_description'])) {
                    $itinerary->setTranslation('description', 'es', $data['new_itinerary_description']);
                }
                $itinerary->save();

                foreach ($itemsData as $index => $itemData) {
                    // Check if item exists (Spatie JSON query for 'es' locale)
                    // Using Laravel's JSON syntax which works for Postgres/MySQL
                    $item = ItineraryItem::where("title->es", $itemData['title'])->first();

                    if (!$item) {
                        $item = new ItineraryItem();
                        $item->is_active = true;
                        $item->setTranslation('title', 'es', $itemData['title']);
                        if (!empty($itemData['description'])) {
                            $item->setTranslation('description', 'es', $itemData['description']);
                        }
                        $item->save();
                    } else {
                        // Update existing item description if provided and currently empty
                        $currentDesc = $item->getTranslation('description', 'es', false);
                        if (!empty($itemData['description']) && empty($currentDesc)) {
                            $item->setTranslation('description', 'es', $itemData['description']);
                            $item->save();
                        }
                    }

                    // Attach to itinerary
                    // Pivot table: itinerary_item_itinerary
                    // We check if already attached to avoid duplicates if re-running (though this is new itinerary so empty)
                    // But standard attach is fine here.
                    $itinerary->items()->attach($item->item_id, [
                        'item_order' => $index + 1,
                        'is_active'  => true,
                        // 'created_at' => now(), // managed by withTimestamps()
                        // 'updated_at' => now(),
                    ]);
                }

                $product->update([
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
                $product->product_id ?? $product->getKey(),
                [
                    'user_id'      => $userId,
                    'itinerary_id' => $itineraryId,
                    'current_step' => $product->current_step,
                ]
            );

            return redirect()
                ->route('admin.products.product-wizard.step', ['product' => $product, 'step' => 3])
                ->with('success', __('m_tours.tour.wizard.itinerary_saved'));
        } catch (\Throwable $e) {
            DB::rollBack();

            LoggerHelper::exception(
                'TourWizardController',
                'storeItinerary',
                'tour',
                $product->product_id ?? $product->getKey(),
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
    public function storeSchedules(Request $request, Product $product)
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
            $product->schedules()->sync($pivotPayload);

            // Actualizar updated_by
            $product->update(['updated_by' => $userId]);

            DB::commit();

            LoggerHelper::mutated(
                'TourWizardController',
                'storeSchedules',
                'tour',
                $product->product_id ?? $product->getKey(),
                [
                    'user_id'        => $userId,
                    'schedule_ids'   => array_values($selectedIds),
                    'pivot_payload'  => $pivotPayload,
                ]
            );

            return redirect()
                ->route('admin.products.product-wizard.step', ['product' => $product, 'step' => 4])
                ->with('success', __('m_tours.tour.wizard.schedules_saved'));
        } catch (\Throwable $e) {
            DB::rollBack();

            LoggerHelper::exception(
                'TourWizardController',
                'storeSchedules',
                'tour',
                $product->product_id ?? $product->getKey(),
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
    public function storeAmenities(Request $request, Product $product)
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

            $product->amenities()->sync($included);
            $product->excludedAmenities()->sync($excluded);

            // 🆕 Actualizar updated_by
            $product->update(['updated_by' => $userId]);

            DB::commit();

            LoggerHelper::mutated(
                'TourWizardController',
                'storeAmenities',
                'tour',
                $product->product_id ?? $product->getKey(),
                [
                    'user_id'            => $userId,
                    'included_amenities' => $included,
                    'excluded_amenities' => $excluded,
                    'step'               => 4,
                ]
            );

            return redirect()
                ->route('admin.products.product-wizard.step', ['product' => $product, 'step' => 5])
                ->with('success', __('m_tours.tour.wizard.amenities_saved'));
        } catch (\Throwable $e) {
            DB::rollBack();

            LoggerHelper::exception(
                'TourWizardController',
                'storeAmenities',
                'tour',
                $product->product_id ?? $product->getKey(),
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
    public function storePrices(Request $request, Product $product)
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
            $product->prices()->delete();

            $totalPricesCreated = 0;

            // Crear precios por periodo
            foreach ($data['periods'] as $period) {
                $validFrom = !empty($period['valid_from']) ? $period['valid_from'] : null;
                $validUntil = !empty($period['valid_until']) ? $period['valid_until'] : null;
                $label = !empty($period['label']) ? $period['label'] : null;

                foreach ($period['categories'] as $categoryData) {
                    $product->prices()->create([
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
                $product->taxes()->sync($data['taxes']);
            } else {
                $product->taxes()->detach();
            }

            // Actualizar updated_by
            $product->update(['updated_by' => $userId]);

            DB::commit();

            LoggerHelper::mutated(
                'TourWizardController',
                'storePrices',
                'tour',
                $product->product_id ?? $product->getKey(),
                [
                    'user_id'        => $userId,
                    'step'           => 5,
                    'periods_count'  => count($data['periods']),
                    'prices_count'   => $totalPricesCreated,
                ]
            );

            return redirect()
                ->route('admin.products.product-wizard.step', ['product' => $product, 'step' => 6])
                ->with('success', __('m_tours.tour.wizard.prices_saved'));
        } catch (\Throwable $e) {
            DB::rollBack();

            LoggerHelper::exception(
                'TourWizardController',
                'storePrices',
                'tour',
                $product->product_id ?? $product->getKey(),
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
    public function publish(Product $product)
    {
        if (!$product->is_draft) {
            return redirect()
                ->route('admin.products.index')
                ->with('info', __('m_tours.tour.wizard.already_published'));
        }

        $userId = optional(auth()->user())->user_id ?? auth()->id();

        // 🆕 Actualizar con updated_by
        $product->update([
            'is_draft'     => false,
            'is_active'    => true,
            'current_step' => max($product->current_step, 6),
            'updated_by'   => $userId,  // 🆕
        ]);

        LoggerHelper::mutated(
            'TourWizardController',
            'publish',
            'tour',
            $product->product_id ?? $product->getKey(),
            [
                'user_id'   => $userId,
                'is_draft'  => $product->is_draft,
                'is_active' => $product->is_active,
            ]
        );

        return redirect()
            ->route('admin.products.index')
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
                $type->setTranslation('name', $locale, $nameTr[$locale] ?? $data['name']);
                $type->setTranslation('description', $locale, $descTr[$locale] ?? ($data['description'] ?? null));
                $type->setTranslation('duration', $locale, $durTr[$locale] ?? ($data['duration'] ?? null));
            }
            $type->save();

            return $type;
        });

        LoggerHelper::mutated(
            'TourWizardController',
            'quickStoreTourType',
            'tour_type',
            $type->product_type_id ?? $type->getKey(),
            ['user_id' => $userId]
        );

        return response()->json([
            'id'   => $type->product_type_id,
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

        $language = ProductLanguage::create([
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
                $item->setTranslation('title', $locale, $titleTr[$locale] ?? $data['title']);
                $item->setTranslation('description', $locale, $descTr[$locale] ?? ($data['description'] ?? null));
            }
            $item->save();

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
    public function quickStoreSchedule(Request $request, Product $product)
    {
        if (!$product->is_draft) {
            return redirect()
                ->route('admin.products.edit', $product)
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

            $product->schedules()->syncWithoutDetaching([
                $schedule->schedule_id => $pivotData,
            ]);

            // 🆕 Actualizar updated_by
            $product->update(['updated_by' => $userId]);

            DB::commit();

            LoggerHelper::mutated(
                'TourWizardController',
                'quickStoreSchedule',
                'schedule',
                $schedule->schedule_id ?? $schedule->getKey(),
                [
                    'product_id'       => $product->product_id ?? $product->getKey(),
                    'base_capacity' => $data['base_capacity'] ?? null,
                    'user_id'       => $userId,
                ]
            );

            return redirect()
                ->route('admin.products.product-wizard.step', ['product' => $product, 'step' => 3])
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
                    'product_id' => $product->product_id ?? $product->getKey(),
                    'user_id' => $userId,
                ]
            );

            report($e);

            return redirect()
                ->route('admin.products.product-wizard.step', ['product' => $product, 'step' => 3])
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
                    $amenity->setTranslation('name', $locale, $nameTr[$locale] ?? $data['name']);
                }
                $amenity->save();

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
        LoggerHelper::info('ProductWizardController', 'quickStoreCategory', 'Start', $request->all());

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
            LoggerHelper::info('ProductWizardController', 'quickStoreCategory', 'Validation passed', $data);
        } catch (\Illuminate\Validation\ValidationException $e) {
            LoggerHelper::warning('ProductWizardController', 'quickStoreCategory', 'Validation failed', [
                'errors' => $e->errors()
            ]);
            throw $e;
        }

        $userId = optional($request->user())->user_id ?? $request->user()?->getAuthIdentifier();

        // Slug is now provided by the user
        $slug = $data['slug'];
        LoggerHelper::info('ProductWizardController', 'quickStoreCategory', 'Using provided slug', ['slug' => $slug]);

        try {
            $category = DB::transaction(function () use ($data, $slug, $translator) {
                // LoggerHelper::info('TourWizardController', 'quickStoreCategory', 'Creating category base');
                $category = \App\Models\CustomerCategory::create([
                    'slug'      => $slug,
                    'age_from'  => $data['age_from'],
                    'age_to'    => $data['age_to'], // Can be null
                    'is_active' => $data['is_active'] ?? true,
                ]);

                // LoggerHelper::info('TourWizardController', 'quickStoreCategory', 'Translating name');
                $nameTr = $translator->translateAll($data['name']);

                foreach (supported_locales() as $locale) {
                    $category->setTranslation('name', $locale, $nameTr[$locale] ?? $data['name']);
                }
                $category->save();

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

            LoggerHelper::info('ProductWizardController', 'quickStoreCategory', 'Success', ['id' => $category->category_id]);

            return response()->json([
                'id'        => $category->category_id,
                'name'      => $category->getTranslatedName(), // Use helper method
                'age_range' => $ageLabel,
                'slug'      => $category->slug,
                'message'   => __('m_tours.prices.quick_category.created_ok'),
            ], 201);
        } catch (\Exception $e) {
            LoggerHelper::exception('ProductWizardController', 'quickStoreCategory', 'customer_category', null, $e, [
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
