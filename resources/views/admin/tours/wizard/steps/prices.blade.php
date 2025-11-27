{{-- resources/views/admin/tours/wizard/steps/prices.blade.php --}}
{{-- NUEVA VERSIÓN COMPACTA: Precios agrupados por periodos de fechas --}}

@extends('adminlte::page')

@section('title', __('m_tours.tour.wizard.steps.prices'))

@push('css')
<style>
    /* Dark theme compatible styles */
    .pricing-period-card {
        margin-bottom: 1.5rem;
        border: 1px solid #454d55;
        border-radius: 0.5rem;
        overflow: hidden;
        background: #343a40;
    }

    .period-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1rem 1.25rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .period-header.default {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
    }

    /* Listado de periodos en el SweetAlert de solapamiento */
    .price-conflict-list {
        list-style-type: disc;
        list-style-position: inside; /* Clave para que el texto quede cerca del punto */
        padding-left: 0;
        margin: .75rem 0 0;
        text-align: left;
    }

    .price-conflict-list li {
        margin: .15rem 0;
    }

    .period-dates {
        display: flex;
        gap: 1rem;
        align-items: flex-start;
        flex-wrap: wrap;
    }

    .period-dates input[type="date"] {
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
        padding: 0.375rem 0.75rem;
        border-radius: 0.25rem;
        font-size: 0.875rem;
    }

    .period-dates input[type="date"]::-webkit-calendar-picker-indicator {
        filter: invert(1);
    }

    .period-dates input[type="date"].is-invalid {
        border-color: #f56565;
        background: rgba(245, 101, 101, 0.2);
    }

    .categories-table {
        margin: 0;
        background: #343a40;
    }

    .categories-table thead {
        background: #454d55;
    }

    .categories-table th {
        font-size: 0.8125rem;
        font-weight: 600;
        padding: 0.5rem;
        border-bottom: 2px solid #454d55;
        color: #c2c7d0;
    }

    .categories-table td {
        padding: 0.5rem;
        vertical-align: middle;
        border-top: 1px solid #454d55;
        color: #c2c7d0;
    }

    .categories-table tbody tr:hover {
        background: #3a4047;
    }

    .categories-table input[type="number"],
    .categories-table input[type="text"] {
        font-size: 0.875rem;
        padding: 0.375rem 0.5rem;
        background: #3d444b;
        border: 1px solid #6c757d;
        color: #fff;
    }

    .categories-table input[type="number"].is-invalid {
        border-color: #f56565;
    }

    .categories-table input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    .add-category-section {
        padding: 1rem 1.25rem;
        background: #3d444b;
        border-top: 1px solid #454d55;
        display: flex;
        gap: 0.5rem;
        align-items: center;
        flex-wrap: wrap;
    }

    .add-category-section select {
        background: #343a40;
        border: 1px solid #6c757d;
        color: #c2c7d0;
        flex: 1;
        min-width: 200px;
    }

    .btn-add-period {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white !important;
        border: none;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        border-radius: 0.5rem;
        transition: all 0.3s;
    }

    .btn-add-period:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        color: white !important;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: #6c757d;
        background: #343a40;
        border: 1px solid #454d55;
        border-radius: 0.5rem;
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .input-group-text {
        background: #3d444b;
        border: 1px solid #6c757d;
        color: #c2c7d0;
    }

    /* Responsive improvements */
    @media (max-width: 768px) {
        .period-dates {
            flex-direction: column;
            align-items: flex-start;
            width: 100%;
        }

        .period-dates > div {
            display: flex;
            flex-direction: column;
            min-width: 180px;
        }

        .period-dates label {
            margin-bottom: 0.25rem;
            font-size: 0.75rem;
            opacity: 0.9;
            white-space: nowrap;
        }

        .period-dates input[type="date"] {
            width: 100%;
        }

        .add-category-section {
            flex-direction: column;
        }

        .add-category-section select,
        .add-category-section button {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    {{-- Wizard Stepper --}}
    @include('admin.tours.wizard.partials.stepper', ['currentStep' => $step])

    {{-- Header --}}
    <div class="card mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;">
        <div class="card-body">
            <h1 class="mb-2" style="font-size: 1.75rem; font-weight: 600;">
                <i class="fas fa-dollar-sign mr-2"></i>
                {{ __('m_tours.tour.wizard.steps.prices') }}
            </h1>
            <p class="mb-0" style="opacity: 0.9;">
                {{ __('m_tours.tour.pricing.wizard_description') ?? 'Define los precios por temporada y categoría de cliente' }}
            </p>
        </div>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <h5><i class="icon fas fa-ban"></i> {{ __('m_tours.common.validation_errors') }}</h5>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.tours.wizard.store.prices', $tour) }}" method="POST" id="prices-form">
        @csrf

        {{-- Add Period Button --}}
        <div class="mb-3">
            <button type="button" class="btn btn-add-period" id="add-period-btn">
                <i class="fas fa-plus-circle mr-2"></i>
                {{ __('m_tours.tour.pricing.add_period') ?? 'Agregar Periodo de Precios' }}
            </button>
        </div>

        {{-- Pricing Periods Container --}}
        <div id="pricing-periods-container">
            @forelse($pricingPeriods ?? [] as $periodIndex => $period)
                <div class="card pricing-period-card" data-period-index="{{ $periodIndex }}">
                    <div class="period-header {{ ($period['is_default'] ?? false) ? 'default' : '' }}">
                        <div class="period-dates">
                            @if(!($period['is_default'] ?? false))
                                <div>
                                    <label class="mb-1" style="font-size: 0.75rem; opacity: 0.9;">
                                        {{ __('m_tours.tour.pricing.valid_from') }}
                                    </label>
                                    <input type="date"
                                           name="periods[{{ $periodIndex }}][valid_from]"
                                           value="{{ $period['valid_from'] ?? '' }}"
                                           class="form-control form-control-sm period-date-input"
                                           data-period-index="{{ $periodIndex }}">
                                </div>
                                <div>
                                    <label class="mb-1" style="font-size: 0.75rem; opacity: 0.9;">
                                        {{ __('m_tours.tour.pricing.valid_until') }}
                                    </label>
                                    <input type="date"
                                           name="periods[{{ $periodIndex }}][valid_until]"
                                           value="{{ $period['valid_until'] ?? '' }}"
                                           class="form-control form-control-sm period-date-input"
                                           data-period-index="{{ $periodIndex }}">
                                    <small class="text-muted" style="font-size: 0.7rem;">
                                        {{ __('m_tours.tour.pricing.leave_empty_no_limit') }}
                                    </small>
                                </div>
                            @else
                                <h5 class="mb-0">
                                    <i class="fas fa-infinity mr-2"></i>
                                    {{ $period['label'] ?? 'Precio Base' }}
                                </h5>
                                <input type="hidden" name="periods[{{ $periodIndex }}][valid_from]" value="">
                                <input type="hidden" name="periods[{{ $periodIndex }}][valid_until]" value="">
                            @endif
                        </div>
                        <button type="button" class="btn btn-sm btn-danger remove-period-btn">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>

                    <div class="card-body p-0">
                        <table class="table table-sm table-hover categories-table mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 25%;">{{ __('m_tours.tour.pricing.category') }}</th>
                                    <th style="width: 15%;">{{ __('m_tours.tour.pricing.age_range') }}</th>
                                    <th style="width: 15%;">{{ __('m_tours.tour.pricing.price_usd') }}</th>
                                    <th style="width: 12%;">{{ __('m_tours.tour.pricing.min_quantity') }}</th>
                                    <th style="width: 12%;">{{ __('m_tours.tour.pricing.max_quantity') }}</th>
                                    <th style="width: 10%;" class="text-center">{{ __('m_tours.tour.pricing.active') }}</th>
                                    <th style="width: 11%;"></th>
                                </tr>
                            </thead>
                            <tbody class="categories-tbody">
                                @foreach(($period['categories'] ?? []) as $catIndex => $cat)
                                    <tr data-category-id="{{ $cat['id'] ?? '' }}">
                                        <td>
                                            <strong>{{ $cat['name'] ?? '' }}</strong>
                                            <input type="hidden"
                                                   name="periods[{{ $periodIndex }}][categories][{{ $catIndex }}][category_id]"
                                                   value="{{ $cat['id'] ?? '' }}">
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $cat['age_range'] ?? '' }}</small>
                                        </td>
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">$</span>
                                                </div>
                                                <input type="number"
                                                       name="periods[{{ $periodIndex }}][categories][{{ $catIndex }}][price]"
                                                       value="{{ $cat['price'] ?? '0.00' }}"
                                                       class="form-control price-input"
                                                       step="0.01"
                                                       min="0"
                                                       required>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="number"
                                                   name="periods[{{ $periodIndex }}][categories][{{ $catIndex }}][min_quantity]"
                                                   value="{{ $cat['min_quantity'] ?? '0' }}"
                                                   class="form-control form-control-sm"
                                                   min="0">
                                        </td>
                                        <td>
                                            <input type="number"
                                                   name="periods[{{ $periodIndex }}][categories][{{ $catIndex }}][max_quantity]"
                                                   value="{{ $cat['max_quantity'] ?? '12' }}"
                                                   class="form-control form-control-sm"
                                                   min="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="checkbox"
                                                   name="periods[{{ $periodIndex }}][categories][{{ $catIndex }}][is_active]"
                                                   value="1"
                                                   {{ ($cat['is_active'] ?? true) ? 'checked' : '' }}>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-danger remove-category-btn">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="add-category-section">
                        <select class="form-control form-control-sm add-category-select">
                            <option value="">{{ __('m_tours.tour.pricing.choose_category_placeholder') }}</option>
                            @foreach($categories ?? [] as $category)
                                <option value="{{ $category->category_id }}"
                                        data-name="{{ $category->getTranslatedName() ?? $category->name }}"
                                        data-age-range="{{ $category->age_range ?? ($category->age_from . '-' . $category->age_to) }}">
                                    {{ $category->getTranslatedName() ?? $category->name }}
                                    ({{ $category->age_range ?? ($category->age_from . '-' . $category->age_to) }})
                                </option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-sm btn-primary add-category-btn">
                            <i class="fas fa-plus mr-1"></i> {{ __('m_tours.tour.pricing.add_button') }}
                        </button>
                    </div>
                </div>
            @empty
                <div class="card empty-state" id="empty-state">
                    <div class="card-body">
                        <i class="fas fa-calendar-times"></i>
                        <h5>{{ __('m_tours.tour.pricing.no_periods') ?? 'No hay periodos de precios definidos' }}</h5>
                        <p class="text-muted">
                            {{ __('m_tours.tour.pricing.click_add_period') ?? 'Haz clic en "Agregar Periodo de Precios" para comenzar' }}
                        </p>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Taxes Section --}}
        <div class="card mt-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-percentage mr-2"></i>
                    {{ __('m_tours.tour.pricing.taxes') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @forelse($taxes ?? [] as $tax)
                        <div class="col-md-4 col-sm-6">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox"
                                       class="custom-control-input"
                                       id="tax-{{ $tax->tax_id }}"
                                       name="taxes[]
                                       " value="{{ $tax->tax_id }}"
                                       {{ $tour->taxes->contains($tax->tax_id) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="tax-{{ $tax->tax_id }}">
                                    {{ $tax->name }} ({{ $tax->rate }}%)
                                </label>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <p class="text-muted mb-0">
                                {{ __('m_tours.tour.pricing.no_taxes') ?? 'No hay impuestos disponibles' }}
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Navigation Buttons --}}
        <div class="d-flex justify-content-between mt-4 mb-5">
            <a href="{{ route('admin.tours.wizard.step', ['tour' => $tour, 'step' => 4]) }}"
               class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>
                {{ __('m_tours.tour.wizard.previous') }}
            </a>
            <button type="submit" class="btn btn-success btn-lg" id="submit-btn">
                {{ __('m_tours.tour.wizard.save_and_continue') }}
                <i class="fas fa-arrow-right ml-2"></i>
            </button>
        </div>
    </form>
</div>

{{-- Modal de Confirmación de Solapamiento (si lo quieres seguir usando para categorías) --}}
<div class="modal fade" id="overlapModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content bg-dark">
            <div class="modal-header bg-warning">
                <h5 class="modal-title text-dark">
                    <i class="fas fa-exclamation-triangle"></i>
                    Solapamiento de Fechas Detectado
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="overlap-message"></p>
                <div id="overlap-suggestions"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning" id="confirm-overlap">
                    Continuar de Todas Formas
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {
        console.log('Pricing periods script loaded');

        // contador inicial de periodos existentes
        let periodCounter = {{ count($pricingPeriods ?? []) }};
        console.log('Initial period counter:', periodCounter);

        // Translations
        const translations = {
            defaultPrice: @json(__('m_tours.tour.pricing.default_price') ?? 'Precio Base'),
            allYear: @json(__('m_tours.tour.pricing.all_year') ?? 'Todo el año'),
            validFrom: @json(__('m_tours.tour.pricing.valid_from') ?? 'Válido desde'),
            validUntil: @json(__('m_tours.tour.pricing.valid_until') ?? 'Válido hasta'),
            category: @json(__('m_tours.tour.pricing.category') ?? 'Categoría'),
            ageRange: @json(__('m_tours.tour.pricing.age_range') ?? 'Rango de edad'),
            priceUsd: @json(__('m_tours.tour.pricing.price_usd') ?? 'Precio (USD)'),
            minQuantity: @json(__('m_tours.tour.pricing.min_quantity') ?? 'Cantidad Mínima'),
            maxQuantity: @json(__('m_tours.tour.pricing.max_quantity') ?? 'Cantidad Máxima'),
            active: @json(__('m_tours.tour.pricing.active') ?? 'Activo'),
            addButton: @json(__('m_tours.tour.pricing.add_button') ?? 'Agregar'),
            chooseCategoryPlaceholder: @json(__('m_tours.tour.pricing.choose_category_placeholder') ?? 'Selecciona una categoría'),
            confirmRemovePeriod: @json(__('m_tours.tour.pricing.confirm_remove_period') ?? '¿Estás seguro de eliminar este periodo?'),
            noPeriods: @json(__('m_tours.tour.pricing.no_periods') ?? 'No hay periodos de precios definidos'),
            clickAddPeriod: @json(__('m_tours.tour.pricing.click_add_period') ?? 'Haz clic en "Agregar Periodo de Precios" para comenzar'),
            selectCategoryFirst: @json(__('m_tours.tour.pricing.select_category_first') ?? 'Por favor selecciona una categoría primero'),
            categoryAlreadyAdded: @json(__('m_tours.tour.pricing.category_already_in_period') ?? 'Esta categoría ya está agregada en este periodo'),
            addAtLeastOnePeriod: @json(__('m_tours.tour.pricing.add_at_least_one_period') ?? 'Debes agregar al menos un periodo de precios'),
            addAtLeastOneCategory: @json(__('m_tours.tour.pricing.add_at_least_one_category') ?? 'Debes agregar al menos una categoría a cada periodo'),
            noPriceGreaterZero: @json(__('m_tours.prices.validation.no_price_greater_zero') ?? 'Debe haber al menos una categoría con precio mayor a $0.00'),
            invalidDateRange: @json(__('m_tours.tour.pricing.invalid_date_range') ?? 'La fecha de fin debe ser posterior a la fecha de inicio'),
            categoryAddedSuccess: @json(__('m_tours.tour.pricing.category_added_success') ?? 'Categoría agregada exitosamente'),
            periodRemovedSuccess: @json(__('m_tours.tour.pricing.period_removed_success') ?? 'Periodo eliminado exitosamente'),
            categoryRemovedSuccess: @json(__('m_tours.tour.pricing.category_removed_success') ?? 'Categoría eliminada exitosamente'),
            duplicateCategoryTitle: @json(__('m_tours.tour.pricing.duplicate_category_title') ?? 'Categoría duplicada'),
            invalidDateRangeTitle: @json(__('m_tours.tour.pricing.invalid_date_range_title') ?? 'Rango de fechas inválido'),
            removeCategoryConfirmText: @json(__('m_tours.tour.pricing.remove_category_confirm_text') ?? 'Se eliminará esta categoría del periodo'),
            validationFailed: @json(__('m_tours.tour.pricing.validation_failed') ?? 'Validación fallida'),
            areYouSure: @json(__('m_tours.tour.pricing.are_you_sure') ?? '¿Estás seguro?'),
            yesDelete: @json(__('m_tours.tour.pricing.yes_delete') ?? 'Sí, eliminar'),
            cancel: @json(__('m_tours.tour.pricing.cancel') ?? 'Cancelar'),
            attention: @json(__('m_tours.tour.pricing.attention') ?? 'Atención'),
            periodAddedSuccess: @json(__('m_tours.tour.pricing.period_added_success') ?? 'Periodo agregado correctamente'),
            overlapNotAllowedTitle: @json(__('m_tours.tour.pricing.overlap_not_allowed_title') ?? 'Rango de fechas no permitido'),
            overlapNotAllowedText: @json(__('m_tours.tour.pricing.overlap_not_allowed_text') ?? 'Las fechas seleccionadas se solapan con otro periodo de precios. Ajusta el rango para que no se crucen.'),
            overlapConflictWith: @json(__('m_tours.tour.pricing.overlap_conflict_with') ?? 'Conflicto con los siguientes periodos:')
        };

        // Categories data
        const categoriesData = [
            @foreach($categories ?? [] as $category)
                {
                    id: '{{ $category->category_id }}',
                    name: @json($category->getTranslatedName() ?? $category->name),
                    ageRange: @json($category->age_range ?? ($category->age_from . '-' . $category->age_to)),
                },
            @endforeach
        ];

        // === Helpers de fechas / solapamiento ===
        function parseDate(value) {
            if (!value) return null;
            const d = new Date(value + 'T00:00:00');
            return isNaN(d.getTime()) ? null : d;
        }

        // Considera null como -∞ / +∞ para rangos abiertos
        function rangesOverlap(aFrom, aUntil, bFrom, bUntil) {
            const startA = aFrom ? aFrom.getTime() : -Infinity;
            const endA = aUntil ? aUntil.getTime() : +Infinity;
            const startB = bFrom ? bFrom.getTime() : -Infinity;
            const endB = bUntil ? bUntil.getTime() : +Infinity;

            // Inclusivo: si comparten un día ya se consideran solapados
            return startA <= endB && startB <= endA;
        }

        // Devuelve lista de otros periodos que se solapan con el actual
        function findOverlapsForPeriod($period) {
            const thisFrom = parseDate($period.find('input[name*="[valid_from]"]').val());
            const thisUntil = parseDate($period.find('input[name*="[valid_until]"]').val());

            // Si no hay ninguna fecha en este periodo, no hay nada que validar
            if (!thisFrom && !thisUntil) {
                return [];
            }

            const conflicts = [];

            $('.pricing-period-card').each(function () {
                const $other = $(this);
                if ($other.is($period)) return; // saltar el mismo periodo

                const otherFrom = parseDate($other.find('input[name*="[valid_from]"]').val());
                const otherUntil = parseDate($other.find('input[name*="[valid_until]"]').val());

                // Si el otro periodo tampoco tiene fechas, no lo consideramos (periodo base)
                if (!otherFrom && !otherUntil) {
                    return;
                }

                if (rangesOverlap(thisFrom, thisUntil, otherFrom, otherUntil)) {
                    conflicts.push({
                        from: otherFrom,
                        until: otherUntil
                    });
                }
            });

            return conflicts;
        }

        // Template for new period
        function getPeriodTemplate(index, isDefault = false) {
            const headerClass = isDefault ? 'default' : '';
            let headerContent = '';

            if (isDefault) {
                headerContent = `
                    <h5 class="mb-0">
                        <i class="fas fa-infinity mr-2"></i>
                        ${translations.defaultPrice} (${translations.allYear})
                    </h5>
                    <input type="hidden" name="periods[${index}][valid_from]" value="">
                    <input type="hidden" name="periods[${index}][valid_until]" value="">
                `;
            } else {
                headerContent = `
                    <div>
                        <label class="mb-1" style="font-size: 0.75rem; opacity: 0.9;">${translations.validFrom}</label>
                        <input type="date"
                               name="periods[${index}][valid_from]"
                               class="form-control form-control-sm period-date-input"
                               data-period-index="${index}">
                    </div>
                    <div>
                        <label class="mb-1" style="font-size: 0.75rem; opacity: 0.9;">${translations.validUntil}</label>
                        <input type="date"
                               name="periods[${index}][valid_until]"
                               class="form-control form-control-sm period-date-input"
                               data-period-index="${index}">
                        <small class="text-muted" style="font-size: 0.7rem;">
                            {{ __('m_tours.tour.pricing.leave_empty_no_limit') }}
                        </small>
                    </div>
                `;
            }

            let categoriesOptions = `<option value="">${translations.chooseCategoryPlaceholder}</option>`;
            categoriesData.forEach(cat => {
                categoriesOptions += `<option value="${cat.id}" data-name="${cat.name}" data-age-range="${cat.ageRange}">
                    ${cat.name} (${cat.ageRange})
                </option>`;
            });

            return `
                <div class="card pricing-period-card" data-period-index="${index}">
                    <div class="period-header ${headerClass}">
                        <div class="period-dates">
                            ${headerContent}
                        </div>
                        <button type="button" class="btn btn-sm btn-danger remove-period-btn">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm table-hover categories-table mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 25%;">${translations.category}</th>
                                    <th style="width: 15%;">${translations.ageRange}</th>
                                    <th style="width: 15%;">${translations.priceUsd}</th>
                                    <th style="width: 12%;">${translations.minQuantity}</th>
                                    <th style="width: 12%;">${translations.maxQuantity}</th>
                                    <th style="width: 10%;" class="text-center">${translations.active}</th>
                                    <th style="width: 11%;"></th>
                                </tr>
                            </thead>
                            <tbody class="categories-tbody"></tbody>
                        </table>
                    </div>
                    <div class="add-category-section">
                        <select class="form-control form-control-sm add-category-select">
                            ${categoriesOptions}
                        </select>
                        <button type="button" class="btn btn-sm btn-primary add-category-btn">
                            <i class="fas fa-plus mr-1"></i> ${translations.addButton}
                        </button>
                    </div>
                </div>
            `;
        }

        // Add new period
        $('#add-period-btn').on('click', function () {
            const newPeriod = getPeriodTemplate(periodCounter, false);
            const $container = $('#pricing-periods-container');

            if ($('#empty-state').length) {
                $('#empty-state').remove();
            }

            $container.prepend(newPeriod);
            periodCounter++;

            Swal.fire({
                icon: 'success',
                title: translations.periodAddedSuccess,
                showConfirmButton: false,
                timer: 1500
            });
        });

        // Remove period
        $(document).on('click', '.remove-period-btn', function () {
            const $card = $(this).closest('.pricing-period-card');

            Swal.fire({
                title: translations.areYouSure,
                text: translations.confirmRemovePeriod,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#48bb78',
                cancelButtonColor: '#6c757d',
                confirmButtonText: translations.yesDelete,
                cancelButtonText: translations.cancel
            }).then((result) => {
                if (result.isConfirmed) {
                    $card.remove();

                    if ($('.pricing-period-card').length === 0) {
                        $('#pricing-periods-container').html(`
                            <div class="card empty-state" id="empty-state">
                                <div class="card-body">
                                    <i class="fas fa-calendar-times"></i>
                                    <h5>${translations.noPeriods}</h5>
                                    <p class="text-muted">${translations.clickAddPeriod}</p>
                                </div>
                            </div>
                        `);
                    }

                    Swal.fire({
                        icon: 'success',
                        title: translations.periodRemovedSuccess,
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            });
        });

        // Guardar valor previo para poder revertir si hay solapamiento
        $(document).on('focus', '.period-date-input', function () {
            $(this).data('prev-value', $(this).val());
        });

        // Validación de fechas en cada cambio (rango correcto + sin solapamientos)
        $(document).on('change', '.period-date-input', function () {
            const $input = $(this);
            const prevValue = $input.data('prev-value') || '';
            const $period = $input.closest('.pricing-period-card');
            const $fromInput = $period.find('input[name*="[valid_from]"]');
            const $untilInput = $period.find('input[name*="[valid_until]"]');

            const validFrom = $fromInput.val();
            const validUntil = $untilInput.val();

            // Limpiar estados previos
            $fromInput.removeClass('is-invalid');
            $untilInput.removeClass('is-invalid');
            $input.removeClass('is-invalid');

            // 1) Validar que el rango propio tenga sentido (from <= until)
            if (validFrom && validUntil && validFrom > validUntil) {
                $fromInput.addClass('is-invalid');
                $untilInput.addClass('is-invalid');

                Swal.fire({
                    icon: 'error',
                    title: translations.invalidDateRangeTitle,
                    text: translations.invalidDateRange,
                    confirmButtonColor: '#48bb78'
                });

                $input.val(prevValue);
                return;
            }

            // 2) Comprobar solapamiento con otros periodos
            const conflicts = findOverlapsForPeriod($period);

            if (conflicts.length > 0) {
                $input.addClass('is-invalid');

                const listHtml = conflicts.map(c => {
                    const from = c.from ? c.from.toISOString().slice(0, 10) : '∞';
                    const until = c.until ? c.until.toISOString().slice(0, 10) : '∞';
                    return `<li>${from} — ${until}</li>`;
                }).join('');

                Swal.fire({
                    icon: 'warning',
                    title: translations.overlapNotAllowedTitle,
                    html: `
                        <p>${translations.overlapNotAllowedText}</p>
                        <p>${translations.overlapConflictWith}</p>
                        <ul class="price-conflict-list">
                            ${listHtml}
                        </ul>
                    `,
                    confirmButtonColor: '#48bb78'
                }).then(() => {
                    $input.removeClass('is-invalid');
                });

                $input.val(prevValue);
                return;
            }

            // Si todo está bien, actualizar valor previo
            $input.data('prev-value', $input.val());
        });

        // Add category to period
        $(document).on('click', '.add-category-btn', function () {
            const $period = $(this).closest('.pricing-period-card');
            const periodIndex = $period.data('period-index');
            const $select = $period.find('.add-category-select');
            const categoryId = $select.val();

            if (!categoryId) {
                Swal.fire({
                    icon: 'warning',
                    title: translations.attention,
                    text: translations.selectCategoryFirst,
                    confirmButtonColor: '#48bb78'
                });
                return;
            }

            if ($period.find(`tr[data-category-id="${categoryId}"]`).length > 0) {
                Swal.fire({
                    icon: 'warning',
                    title: translations.duplicateCategoryTitle,
                    text: translations.categoryAlreadyAdded,
                    confirmButtonColor: '#48bb78'
                });
                return;
            }

            const validFrom = $period.find('input[name*="[valid_from]"]').val();
            const validUntil = $period.find('input[name*="[valid_until]"]').val();

            if (validFrom && validUntil && validFrom > validUntil) {
                Swal.fire({
                    icon: 'error',
                    title: translations.invalidDateRangeTitle,
                    text: translations.invalidDateRange,
                    confirmButtonColor: '#48bb78'
                });

                $period.find('input[name*="[valid_from]"]').addClass('is-invalid');
                $period.find('input[name*="[valid_until]"]').addClass('is-invalid');
                return;
            }

            $period.find('.period-date-input').removeClass('is-invalid');

            addCategoryRow($period, $select, categoryId, periodIndex);
        });

        function addCategoryRow($period, $select, categoryId, periodIndex) {
            const $option = $select.find('option:selected');
            const categoryName = $option.data('name');
            const ageRange = $option.data('age-range');
            const catIndex = $period.find('.categories-tbody tr').length;

            const newRow = `
                <tr data-category-id="${categoryId}">
                    <td>
                        <strong>${categoryName}</strong>
                        <input type="hidden"
                               name="periods[${periodIndex}][categories][${catIndex}][category_id]"
                               value="${categoryId}">
                    </td>
                    <td><small class="text-muted">${ageRange}</small></td>
                    <td>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input type="number"
                                   name="periods[${periodIndex}][categories][${catIndex}][price]"
                                   value="0.00"
                                   class="form-control price-input"
                                   step="0.01"
                                   min="0"
                                   required>
                        </div>
                    </td>
                    <td>
                        <input type="number"
                               name="periods[${periodIndex}][categories][${catIndex}][min_quantity]"
                               value="0"
                               class="form-control form-control-sm"
                               min="0">
                    </td>
                    <td>
                        <input type="number"
                               name="periods[${periodIndex}][categories][${catIndex}][max_quantity]"
                               value="12"
                               class="form-control form-control-sm"
                               min="0">
                    </td>
                    <td class="text-center">
                        <input type="checkbox"
                               name="periods[${periodIndex}][categories][${catIndex}][is_active]"
                               value="1"
                               checked>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger remove-category-btn">
                            <i class="fas fa-times"></i>
                        </button>
                    </td>
                </tr>
            `;

            $period.find('.categories-tbody').append(newRow);
            $select.val('');

            Swal.fire({
                icon: 'success',
                title: translations.categoryAddedSuccess,
                showConfirmButton: false,
                timer: 1500
            });
        }

        // Remove category from period
        $(document).on('click', '.remove-category-btn', function () {
            const $row = $(this).closest('tr');

            Swal.fire({
                title: translations.areYouSure,
                text: translations.removeCategoryConfirmText,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#48bb78',
                cancelButtonColor: '#6c757d',
                confirmButtonText: translations.yesDelete,
                cancelButtonText: translations.cancel
            }).then((result) => {
                if (result.isConfirmed) {
                    $row.remove();

                    Swal.fire({
                        icon: 'success',
                        title: translations.categoryRemovedSuccess,
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            });
        });

        // Form validation
        $('#prices-form').submit(function (e) {
            e.preventDefault();

            const periods = $('.pricing-period-card').length;

            if (periods === 0) {
                Swal.fire({
                    icon: 'error',
                    title: translations.validationFailed,
                    text: translations.addAtLeastOnePeriod,
                    confirmButtonColor: '#48bb78'
                });
                return false;
            }

            let hasCategories = false;
            let hasInvalidDates = false;
            let hasPriceGreaterThanZero = false;

            $('.pricing-period-card').each(function () {
                const $period = $(this);

                if ($period.find('.categories-tbody tr').length > 0) {
                    hasCategories = true;
                }

                const validFrom = $period.find('input[name*="[valid_from]"]').val();
                const validUntil = $period.find('input[name*="[valid_until]"]').val();

                if (validFrom && validUntil && validFrom > validUntil) {
                    hasInvalidDates = true;
                    $period.find('input[name*="[valid_from]"]').addClass('is-invalid');
                    $period.find('input[name*="[valid_until]"]').addClass('is-invalid');
                }

                $period.find('.price-input').each(function () {
                    const price = parseFloat($(this).val());
                    if (!isNaN(price) && price > 0) {
                        hasPriceGreaterThanZero = true;
                    }
                });

                // doble check de solapamientos al enviar
                const conflicts = findOverlapsForPeriod($period);
                if (conflicts.length > 0) {
                    hasInvalidDates = true;
                }
            });

            if (hasInvalidDates) {
                Swal.fire({
                    icon: 'error',
                    title: translations.overlapNotAllowedTitle,
                    text: translations.overlapNotAllowedText,
                    confirmButtonColor: '#48bb78'
                });
                return false;
            }

            if (!hasCategories) {
                Swal.fire({
                    icon: 'error',
                    title: translations.validationFailed,
                    text: translations.addAtLeastOneCategory,
                    confirmButtonColor: '#48bb78'
                });
                return false;
            }

            if (!hasPriceGreaterThanZero) {
                $('.price-input').addClass('is-invalid');

                Swal.fire({
                    icon: 'error',
                    title: translations.validationFailed,
                    text: translations.noPriceGreaterZero,
                    confirmButtonColor: '#48bb78'
                }).then(() => {
                    setTimeout(() => {
                        $('.price-input').removeClass('is-invalid');
                    }, 3000);
                });
                return false;
            }

            const $submitBtn = $('#submit-btn');
            $submitBtn.prop('disabled', true)
                .html('<i class="fas fa-spinner fa-spin mr-2"></i>Guardando...');

            this.submit();
        });

        $(document).on('input', '.price-input', function () {
            $(this).removeClass('is-invalid');
        });
    });
</script>
@endpush
