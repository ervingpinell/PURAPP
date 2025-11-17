{{-- resources/views/admin/tours/wizard/steps/prices.blade.php --}}

@extends('adminlte::page')

@section('title', __('m_tours.tour.wizard.steps.prices'))

@push('css')
<style>
    /* Header mejorado */
    .prices-header {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        color: white;
        padding: 2rem;
        border-radius: 0.5rem;
        margin-bottom: 2rem;
    }

    .prices-header h1 {
        margin: 0;
        font-size: 1.75rem;
        font-weight: 600;
    }

    .prices-header p {
        margin: 0.5rem 0 0 0;
        opacity: 0.9;
    }

    /* Tarjetas con tema oscuro */
    .prices-card {
        border: none;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        margin-bottom: 1.25rem;
        border-radius: 0.5rem;
        overflow: hidden;
        background: #2d3748;
    }

    .prices-card .card-header {
        padding: 0.75rem 1rem;
        font-weight: 600;
        font-size: 0.95rem;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        background: #3a4556;
        color: #e2e8f0;
    }

    .prices-card .card-body {
        padding: 1rem;
        background: #2d3748;
        color: #cbd5e0;
    }

    /* Alert info mejorado */
    .alert-info {
        background: rgba(66, 153, 225, 0.15);
        border: 1px solid rgba(66, 153, 225, 0.3);
        color: #90cdf4;
        border-radius: 0.375rem;
    }

    .alert-info i {
        color: #63b3ed;
    }

    .alert-info strong {
        color: #bee3f8;
    }

    /* Alert warning */
    .alert-warning {
        background: rgba(237, 137, 54, 0.15);
        border: 1px solid rgba(237, 137, 54, 0.3);
        color: #fbd38d;
        border-radius: 0.375rem;
    }

    /* Selector de categorías */
    .category-selector-wrapper {
        background: #3a4556;
        padding: 1rem;
        border-radius: 0.375rem;
        margin-bottom: 1.5rem;
    }

    .category-selector-wrapper label {
        color: #e2e8f0;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .category-selector-wrapper select,
    .category-selector-wrapper input {
        background: #2d3748;
        border: 1px solid #4a5568;
        color: #e2e8f0;
    }

    .category-selector-wrapper select:focus,
    .category-selector-wrapper input:focus {
        background: #2d3748;
        border-color: #667eea;
        color: #e2e8f0;
    }

    .category-selector-wrapper select option {
        background: #2d3748;
        color: #e2e8f0;
    }

    /* Price cards individuales */
    .price-card {
        background: #3a4556;
        border: 1px solid #4a5568;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .price-card .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-bottom: none;
        padding: 0.75rem 1rem;
    }

    .price-card .card-title {
        color: white;
        margin: 0;
    }

    .price-card .card-title small {
        opacity: 0.8;
    }

    .price-card .card-body {
        background: #2d3748;
        padding: 1rem;
    }

    .price-card label {
        color: #a0aec0;
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .price-card input,
    .price-card .input-group-text {
        background: #3a4556;
        border: 1px solid #4a5568;
        color: #e2e8f0;
    }

    .price-card input:focus {
        background: #3a4556;
        border-color: #667eea;
        color: #e2e8f0;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .price-card .input-group-text {
        color: #48bb78;
        font-weight: 600;
    }

    /* Botones header */
    .header-actions {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    /* Footer de navegación */
    .navigation-footer {
        background: #2d3748;
        border: none;
        box-shadow: 0 -2px 8px rgba(0,0,0,0.15);
        padding: 1.25rem;
        border-radius: 0.5rem;
        margin-top: 2rem;
    }

    /* Modal mejorado */
    .modal-content {
        background: #2d3748;
        border: 1px solid #4a5568;
    }

    .modal-header {
        background: #3a4556;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        color: #e2e8f0;
    }

    .modal-header .close {
        color: #e2e8f0;
        opacity: 0.8;
    }

    .modal-header .close:hover {
        opacity: 1;
    }

    .modal-body {
        background: #2d3748;
        color: #cbd5e0;
    }

    .modal-body label {
        color: #e2e8f0;
        font-weight: 600;
    }

    .modal-body input {
        background: #3a4556;
        border: 1px solid #4a5568;
        color: #e2e8f0;
    }

    .modal-body input:focus {
        background: #3a4556;
        border-color: #667eea;
        color: #e2e8f0;
    }

    .modal-footer {
        background: #3a4556;
        border-top: 1px solid rgba(255,255,255,0.1);
    }

    /* Switch mejorado */
    .form-check-input:checked {
        background-color: #48bb78;
        border-color: #48bb78;
    }

    .form-check-input:focus {
        box-shadow: 0 0 0 0.2rem rgba(72, 187, 120, 0.25);
    }

    /* Estilos de botones */
    .btn-primary {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        border: none;
        color: white;
        font-weight: 600;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #38a169 0%, #2f855a 100%);
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.2);
    }

    .btn-success {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        border: none;
        color: white;
        font-weight: 600;
    }

    .btn-success:hover {
        background: linear-gradient(135deg, #38a169 0%, #2f855a 100%);
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.2);
    }

    .btn-secondary {
        background: #4a5568;
        border: none;
        color: #e2e8f0;
        font-weight: 600;
    }

    .btn-secondary:hover {
        background: #5a6778;
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.2);
    }

    .btn-danger {
        background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
        border: none;
        color: white;
        font-weight: 600;
    }

    .btn-danger:hover {
        background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.2);
    }

    /* Responsive */
    @media (max-width: 991.98px) {
        .prices-header {
            padding: 1.5rem;
        }

        .prices-header h1 {
            font-size: 1.5rem;
        }

        .header-actions {
            width: 100%;
            margin-top: 0.5rem;
        }

        .header-actions .btn {
            flex: 1;
        }
    }

    @media (max-width: 767.98px) {
        .category-selector-wrapper {
            padding: 0.75rem;
        }

        .price-card .row > div {
            margin-bottom: 1rem;
        }

        .navigation-footer .d-flex {
            flex-direction: column;
            gap: 0.75rem;
        }

        .navigation-footer .btn {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    @include('admin.tours.wizard.partials.stepper')

    {{-- Header mejorado --}}
    <div class="prices-header">
        <h1>
            <i class="fas fa-dollar-sign"></i>
            {{ __('m_tours.tour.wizard.steps.prices') }}
        </h1>
        <p>{{ $tour->name }}</p>
    </div>

    <form method="POST" action="{{ route('admin.tours.wizard.store.prices', $tour) }}">
        @csrf

        <div class="card prices-card">
            <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <h3 class="card-title mb-2 mb-md-0">
                    <i class="fas fa-list-ul"></i>
                    {{ __('m_tours.tour.pricing.configured_categories') }}
                </h3>

                <div class="header-actions">
                    <a href="{{ route('admin.customer_categories.index') }}"
                       class="btn btn-primary btn-sm"
                       title="{{ __('m_tours.prices.quick_category.go_to_index_title') }}">
                        <i class="fas fa-list"></i>
                        <span class="d-none d-md-inline">
                            {{ __('m_tours.prices.quick_category.go_to_index') }}
                        </span>
                    </a>

                    <button type="button"
                            class="btn btn-success btn-sm"
                            data-toggle="modal"
                            data-target="#modalQuickCategory">
                        <i class="fas fa-plus"></i>
                        <span class="d-none d-md-inline">
                            {{ __('m_tours.prices.quick_category.button') }}
                        </span>
                    </button>
                </div>
            </div>

            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>{{ __('m_tours.tour.pricing.note_title') }}</strong>
                    {{ __('m_tours.tour.pricing.note_text') }}
                </div>

                @php
                    $existingPrices = $tour->prices->keyBy('category_id');
                    $currency = config('app.currency_symbol', '$');
                @endphp

                {{-- Selector para agregar categorías --}}
                <div class="category-selector-wrapper">
                    <label for="category-selector" class="form-label">
                        <i class="fas fa-plus-circle"></i>
                        {{ __('m_tours.tour.pricing.add_existing_category') }}
                    </label>
                    <div class="d-flex flex-column flex-sm-row gap-2">
                        <select id="category-selector" class="form-control mb-2 mb-sm-0 mr-sm-2 flex-grow-1">
                            <option value="">{{ __('m_tours.tour.pricing.choose_category_placeholder') }}</option>
                            @foreach($categories ?? [] as $category)
                                @php
                                    $catLabel = $category->getTranslatedName() ?: $category->name;
                                    $ageLabel = $category->age_range ?? ($category->age_from . '-' . $category->age_to);
                                @endphp
                                <option
                                    value="{{ $category->category_id }}"
                                    data-name="{{ $catLabel }}"
                                    data-age-range="{{ $ageLabel }}"
                                    data-slug="{{ $category->slug }}">
                                    {{ $catLabel }} ({{ $ageLabel }})
                                </option>
                            @endforeach
                        </select>

                        <button type="button"
                                class="btn btn-primary"
                                id="btn-add-category">
                            <i class="fas fa-plus-circle"></i>
                            {{ __('m_tours.tour.pricing.add_button') }}
                        </button>
                    </div>
                </div>

                {{-- Contenedor de precios --}}
                <div id="prices-container">
                    @forelse($tour->prices as $price)
                        @if($price->category)
                            @php
                                $category = $price->category;
                                $catLabel = $category->getTranslatedName() ?: $category->name;
                                $ageLabel = $category->age_range ?? ($category->age_from . '-' . $category->age_to);
                            @endphp

                            <div class="card mb-3 price-card" data-category-id="{{ $category->category_id }}">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="card-title">
                                            {{ $catLabel }}
                                            <small>({{ $ageLabel }})</small>
                                        </h5>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-danger remove-price-card">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>

                                <div class="card-body">
                                    <div class="row">
                                        {{-- Precio --}}
                                        <div class="col-md-4">
                                            <label class="form-label">{{ __('m_tours.tour.pricing.price_usd') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text">{{ $currency }}</span>
                                                <input
                                                    type="number"
                                                    name="prices[{{ $category->category_id }}][price]"
                                                    class="form-control"
                                                    value="{{ old('prices.'.$category->category_id.'.price', number_format($price->price, 2, '.', '')) }}"
                                                    step="0.01"
                                                    min="0"
                                                    required>
                                            </div>
                                        </div>

                                        {{-- Cantidad Mínima --}}
                                        <div class="col-md-3 col-6">
                                            <label class="form-label">{{ __('m_tours.tour.pricing.min_quantity') }}</label>
                                            <input
                                                type="number"
                                                name="prices[{{ $category->category_id }}][min_quantity]"
                                                class="form-control"
                                                value="{{ old('prices.'.$category->category_id.'.min_quantity', $price->min_quantity) }}"
                                                min="0"
                                                max="255">
                                        </div>

                                        {{-- Cantidad Máxima --}}
                                        <div class="col-md-3 col-6">
                                            <label class="form-label">{{ __('m_tours.tour.pricing.max_quantity') }}</label>
                                            <input
                                                type="number"
                                                name="prices[{{ $category->category_id }}][max_quantity]"
                                                class="form-control"
                                                value="{{ old('prices.'.$category->category_id.'.max_quantity', $price->max_quantity) }}"
                                                min="0"
                                                max="255">
                                        </div>

                                        {{-- Estado Activo --}}
                                        <div class="col-md-2 col-12">
                                            <label class="form-label d-block">{{ __('m_tours.tour.pricing.status') }}</label>
                                            <div class="form-check form-switch">
                                                <input type="hidden" name="prices[{{ $category->category_id }}][is_active]" value="0">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    name="prices[{{ $category->category_id }}][is_active]"
                                                    value="1"
                                                    {{ old('prices.'.$category->category_id.'.is_active', $price->is_active) ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                    </div>

                                    <input type="hidden" name="prices[{{ $category->category_id }}][category_id]" value="{{ $category->category_id }}">
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="alert alert-warning" id="no-prices-alert">
                            <i class="fas fa-exclamation-triangle"></i>
                            {{ __('m_tours.tour.pricing.no_categories') }}
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Footer de navegación --}}
        <div class="card">
            <div class="card-footer navigation-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('admin.tours.wizard.step', ['tour' => $tour, 'step' => 4]) }}"
                       class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        {{ __('m_tours.common.previous') }}
                    </a>

<div class="d-flex gap-2">
    <a href="{{ route('admin.tours.wizard.cancel', $tour) }}"
       class="btn btn-danger"
       onclick="return confirm('{{ __('m_tours.tour.wizard.confirm_cancel') }}')">
        <i class="fas fa-trash"></i>
        <span class="d-none d-md-inline">{{ __('m_tours.common.cancel') }}</span>
    </a>

    <button type="submit" class="btn btn-primary ml-2">
        {{ __('m_tours.tour.wizard.save_and_continue') }}
        <i class="fas fa-arrow-right"></i>
    </button>
</div>

                </div>
            </div>
        </div>
    </form>
</div>

{{-- Modal Quick Create Category --}}
<div class="modal fade" id="modalQuickCategory" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="quickCategoryForm"
              action="{{ route('admin.tours.wizard.quick.category') }}"
              method="POST"
              class="modal-content"
              autocomplete="off">
            @csrf

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus-circle"></i>
                    {{ __('m_tours.prices.quick_category.title') }}
                </h5>
                <button type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="{{ __('m_tours.common.close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <label for="quick_category_name">
                        {{ __('m_tours.prices.quick_category.name_label') }}
                    </label>
                    <input type="text"
                           id="quick_category_name"
                           name="name"
                           class="form-control"
                           maxlength="255"
                           required>
                </div>

                <div class="form-row">
                    <div class="form-group col-6">
                        <label for="quick_category_age_from">
                            {{ __('m_tours.prices.quick_category.age_from') }}
                        </label>
                        <input type="number"
                               id="quick_category_age_from"
                               name="age_from"
                               class="form-control"
                               min="0"
                               max="120"
                               required>
                    </div>
                    <div class="form-group col-6">
                        <label for="quick_category_age_to">
                            {{ __('m_tours.prices.quick_category.age_to') }}
                        </label>
                        <input type="number"
                               id="quick_category_age_to"
                               name="age_to"
                               class="form-control"
                               min="0"
                               max="120"
                               required>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i>
                    {{ __('m_tours.prices.quick_category.save') }}
                </button>
                <button type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal">
                    {{ __('m_tours.prices.quick_category.cancel') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const categorySelector = document.getElementById('category-selector');
    const addCategoryBtn   = document.getElementById('btn-add-category');
    const pricesContainer  = document.getElementById('prices-container');
    const noAlert          = document.getElementById('no-prices-alert');
    const quickForm        = document.getElementById('quickCategoryForm');
    const currency         = @json(config('app.currency_symbol', '$'));

    const i18n = {
        alreadyAdded:  @json(__('m_tours.tour.pricing.already_added')),
        saving:        @json(__('m_tours.prices.quick_category.saving')),
        successTitle:  @json(__('m_tours.prices.quick_category.success_title')),
        successText:   @json(__('m_tours.prices.quick_category.success_text')),
        errorTitle:    @json(__('m_tours.prices.quick_category.error_title')),
        errorGeneric:  @json(__('m_tours.prices.quick_category.error_generic')),
        priceLabel:    @json(__('m_tours.tour.pricing.price_usd')),
        minLabel:      @json(__('m_tours.tour.pricing.min_quantity')),
        maxLabel:      @json(__('m_tours.tour.pricing.max_quantity')),
        statusLabel:   @json(__('m_tours.tour.pricing.status')),
    };

    function showError(message) {
        const text = message || i18n.errorGeneric;
        if (window.Swal) {
            Swal.fire({
                icon: 'error',
                title: i18n.errorTitle,
                text: text,
            });
        } else {
            alert(text);
        }
    }

    function showSuccess(message) {
        const text = message || i18n.successText;
        if (window.Swal) {
            Swal.fire({
                icon: 'success',
                title: i18n.successTitle,
                text: text,
                timer: 1600,
                showConfirmButton: false
            });
        }
    }

    function createPriceCard(categoryId, categoryName, ageRange) {
        if (document.querySelector(`[data-category-id="${categoryId}"]`)) {
            showError(i18n.alreadyAdded);
            return;
        }

        if (noAlert) noAlert.style.display = 'none';

        const cardHTML = `
            <div class="card mb-3 price-card" data-category-id="${categoryId}">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">
                            ${categoryName}
                            <small>(${ageRange})</small>
                        </h5>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-light remove-price-card">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label">${i18n.priceLabel}</label>
                            <div class="input-group">
                                <span class="input-group-text">${currency}</span>
                                <input type="number"
                                       name="prices[${categoryId}][price]"
                                       class="form-control"
                                       step="0.01"
                                       min="0"
                                       value="0.00"
                                       required>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <label class="form-label">${i18n.minLabel}</label>
                            <input type="number"
                                   name="prices[${categoryId}][min_quantity]"
                                   class="form-control"
                                   value="0"
                                   min="0"
                                   max="255">
                        </div>
                        <div class="col-md-3 col-6">
                            <label class="form-label">${i18n.maxLabel}</label>
                            <input type="number"
                                   name="prices[${categoryId}][max_quantity]"
                                   class="form-control"
                                   value="12"
                                   min="0"
                                   max="255">
                        </div>
                        <div class="col-md-2 col-12">
                            <label class="form-label d-block">${i18n.statusLabel}</label>
                            <div class="form-check form-switch">
                                <input type="hidden" name="prices[${categoryId}][is_active]" value="0">
                                <input class="form-check-input"
                                       type="checkbox"
                                       name="prices[${categoryId}][is_active]"
                                       value="1"
                                       checked>
                            </div>
                        </div>
                    </div>
                    <input type="hidden"
                           name="prices[${categoryId}][category_id]"
                           value="${categoryId}">
                </div>
            </div>
        `;

        pricesContainer.insertAdjacentHTML('beforeend', cardHTML);
    }

    if (addCategoryBtn) {
        addCategoryBtn.addEventListener('click', function () {
            const selectedOption = categorySelector.options[categorySelector.selectedIndex];
            if (!selectedOption || !selectedOption.value) {
                return;
            }

            const categoryId   = selectedOption.value;
            const categoryName = selectedOption.dataset.name;
            const ageRange     = selectedOption.dataset.ageRange;

            createPriceCard(categoryId, categoryName, ageRange);
            categorySelector.value = '';
        });
    }

    if (pricesContainer) {
        pricesContainer.addEventListener('click', function (e) {
            if (e.target.closest('.remove-price-card')) {
                const card = e.target.closest('.price-card');
                if (card) {
                    card.remove();
                }

                if (pricesContainer.querySelectorAll('.price-card').length === 0) {
                    if (noAlert) noAlert.style.display = 'block';
                }
            }
        });
    }

    if (quickForm) {
        quickForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const submitBtn    = quickForm.querySelector('button[type="submit"]');
            const originalHtml = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>' + i18n.saving;

            try {
                const formData = new FormData(quickForm);
                const token    = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                const res  = await fetch(quickForm.action, {
                    method: 'POST',
                    headers: token ? {'X-CSRF-TOKEN': token} : {},
                    body: formData
                });

                const raw = await res.text();
                let payload = null;
                try { payload = raw ? JSON.parse(raw) : null; } catch (e) {}

                if (!res.ok) {
                    if (res.status === 422 && payload && payload.errors) {
                        const firstFieldErrors = Object.values(payload.errors)[0] || [];
                        const firstMessage = firstFieldErrors[0] || i18n.errorGeneric;
                        showError(firstMessage);
                    } else if (payload && payload.message) {
                        showError(payload.message);
                    } else {
                        showError();
                    }
                    return;
                }

                const id        = payload.id;
                const name      = payload.name;
                const ageRange  = payload.age_range;
                const message   = payload.message || null;

                const opt = document.createElement('option');
                opt.value        = id;
                opt.dataset.name = name;
                opt.dataset.ageRange = ageRange;
                opt.textContent  = `${name} (${ageRange})`;
                categorySelector.appendChild(opt);

                createPriceCard(id, name, ageRange);
                quickForm.reset();

                if (window.$ && typeof window.$.fn.modal === 'function') {
                    window.$('#modalQuickCategory').modal('hide');
                } else if (window.jQuery && typeof window.jQuery.fn.modal === 'function') {
                    window.jQuery('#modalQuickCategory').modal('hide');
                } else {
                    const modalEl = document.getElementById('modalQuickCategory');
                    if (modalEl) {
                        modalEl.classList.remove('show');
                        modalEl.style.display = 'none';
                    }
                    document.body.classList.remove('modal-open');
                    document.querySelectorAll('.modal-backdrop').forEach(function (bd) { bd.remove(); });
                }

                showSuccess(message);

            } catch (error) {
                console.error(error);
                showError();
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHtml;
            }
        });
    }
});
</script>
@endpush
