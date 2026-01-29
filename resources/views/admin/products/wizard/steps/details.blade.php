{{-- resources/views/admin/tours/wizard/steps/details.blade.php --}}

@extends('adminlte::page')

@section('title', __('m_tours.tour.wizard.steps.details'))

@push('css')
<style>
    body.sidebar-mini .content-wrapper {
        overflow-y: auto !important;
    }

    /* Header del wizard, integrado con AdminLTE */
    .details-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
        padding: 1.5rem 2rem;
        border-radius: .5rem;
        margin-bottom: 1.5rem;
    }

    .details-header h1 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 600;
    }

    .details-header p {
        margin: .25rem 0 0;
        opacity: .9;
    }

    /* Card principal (dejamos la card de AdminLTE y sólo ajustamos detalles) */
    .details-card .card-header {
        border-bottom: 0;
        background: #111827;
        color: #e5e7eb;
    }

    .details-card .card-body {
        background: #0b1120;
        color: #e5e7eb;
    }

    .details-card .card-footer {
        background: #020617;
        border-top: 1px solid #1f2937;
    }

    /* Inputs / selects en dark */
    .details-card .form-control,
    .details-card textarea.form-control {
        background: #020617;
        border: 1px solid #374151;
        color: #e5e7eb;
    }

    .details-card .form-control:focus,
    .details-card textarea.form-control:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 0.15rem rgba(99, 102, 241, .25);
        background: #020617;
        color: #e5e7eb;
    }

    label {
        font-weight: 600;
    }

    .form-text.text-muted,
    small.text-muted {
        color: #9ca3af !important;
    }

    .invalid-feedback {
        display: block;
        font-size: .85rem;
    }

    /* Contenedor de idiomas */
    #languages-container {
        max-height: 280px;
        overflow-y: auto;
        background: #020617;
        border-color: #374151 !important;
    }

    #languages-container::-webkit-scrollbar {
        width: 8px;
    }

    #languages-container::-webkit-scrollbar-thumb {
        background: #4b5563;
        border-radius: 4px;
    }

    #languages-container.border-danger {
        border-color: #dc2626 !important;
    }

    /* Contador de caracteres */
    .char-counter {
        font-size: .75rem;
        color: #9ca3af;
        margin-top: .25rem;
        display: block;
    }

    .char-counter.near-limit {
        color: #fbbf24;
    }

    .char-counter.at-limit {
        color: #f87171;
    }

    /* Alert info borrador */
    .alert-draft-info {
        background: #0f172a;
        border-color: #1d4ed8;
        color: #e5e7eb;
    }

    /* Modal drafts (colores a juego con dark) */
    #draftsModal .modal-content {
        background: #020617;
        color: #e5e7eb;
        border: 1px solid #1f2937;
    }

    #draftsModal .modal-header {
        background: #facc15;
        color: #111827;
        border-bottom: 1px solid #f59e0b;
    }

    #draftsModal .modal-body {
        background: #020617;
    }

    #draftsModal .modal-footer {
        background: #020617;
        border-top: 1px solid #1f2937;
    }

    #draftsModal table thead {
        background: #111827;
    }

    #draftsModal table tbody tr:nth-child(even) {
        background: #020617;
    }

    @media (max-width: 768px) {
        .details-header {
            padding: 1.2rem 1rem;
        }
    }
</style>
@endpush

@section('content')
@php
$isEditing = isset($product) && $product && $product->exists;
$currentStep = $step ?? 1;
@endphp

<div class="container-fluid">

    {{-- Header del paso --}}
    <div class="details-header">
        <h1>
            <i class="fas fa-info-circle"></i>
            @if($isEditing)
            {{ __('m_tours.tour.wizard.edit_tour') }}
            @else
            {{ __('m_tours.tour.wizard.create_new_tour') }}
            @endif
        </h1>
        <p>{{ __('m_tours.tour.wizard.steps.details') }}</p>
    </div>

    {{-- Stepper superior --}}
    @include('admin.products.wizard.partials.stepper', [
    'currentStep' => $currentStep,
    'steps' => $steps ?? [],
    'product' => $product ?? null,
    ])

    {{-- Errores / mensajes --}}
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
        <strong>{{ __('m_tours.common.form_errors_title') }}</strong>
        <ul class="mb-0 mt-1">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button"
            class="close"
            data-dismiss="alert"
            aria-label="{{ __('m_tours.common.close') }}"></button>
    </div>
    @endif

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        {{ session('success') }}
        <button type="button"
            class="close"
            data-dismiss="alert"
            aria-label="{{ __('m_tours.common.close') }}"></button>
    </div>
    @endif

    @if(isset($limitWarning) && $limitWarning)
    <div class="alert alert-info alert-dismissible fade show mt-3" role="alert">
        {!! $limitWarning !!}
        <button type="button"
            class="close"
            data-dismiss="alert"
            aria-label="{{ __('m_tours.common.close') }}"></button>
    </div>
    @endif

    {{-- FORM DETALLES --}}
    <form
        id="tour-details-form"
        method="POST"
        action="{{ $isEditing
            ? route('admin.products.wizard.update.details', $product)
            : route('admin.products.wizard.store.details') }}"
        novalidate>
        @csrf

        <div class="card details-card mt-3">
            <div class="card-header">
                <h3 class="card-title mb-0">
                    <i class="fas fa-info-circle"></i>
                    {{ __('m_tours.tour.wizard.basic_info') }}
                </h3>
            </div>

            <div class="card-body">
                <div class="row">
                    {{-- Columna principal --}}
                    <div class="col-md-8">
                        {{-- Nombre --}}
                        <div class="form-group">
                            <label for="name">
                                {{ __('m_tours.tour.fields.name') }}
                                <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                name="name"
                                id="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $product->name ?? '') }}"
                                required
                                autofocus
                                maxlength="255"
                                data-validate="required|min:3|max:255">
                            <small class="char-counter" id="name-counter">0 / 255</small>
                            <div class="invalid-feedback" id="name-error"></div>
                            @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Slug --}}
                        <div class="form-group">
                            <label for="slug">
                                Slug
                                <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                name="slug"
                                id="slug"
                                class="form-control @error('slug') is-invalid @enderror"
                                value="{{ old('slug', $product->slug ?? '') }}"
                                placeholder="{{ __('m_tours.tour.ui.generate_auto') }}"
                                maxlength="255"
                                pattern="[a-z0-9-]+"
                                required
                                data-validate="required|slug|max:255">
                            <small class="form-text text-muted">
                                {{ __('m_tours.tour.ui.slug_help') }}
                            </small>
                            <div class="invalid-feedback" id="slug-error"></div>
                            @error('slug')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Overview --}}
                        <div class="form-group">
                            <label for="overview">
                                {{ __('m_tours.tour.fields.overview') }}
                                <span class="text-danger">*</span>
                            </label>
                            <textarea
                                name="overview"
                                id="overview"
                                class="form-control @error('overview') is-invalid @enderror"
                                rows="5"
                                maxlength="1000"
                                required
                                data-validate="required|max:1000">{{ old('overview', $product->overview ?? '') }}</textarea>
                            <small class="char-counter" id="overview-counter">0 / 1000</small>
                            <div class="invalid-feedback" id="overview-error"></div>
                            @error('overview')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Recommendations --}}
                        <div class="form-group">
                            <label for="recommendations">
                                {{ __('m_tours.tour.fields.recommendations') ?? 'Recomendaciones' }}
                            </label>
                            <textarea
                                name="recommendations"
                                id="recommendations"
                                class="form-control @error('recommendations') is-invalid @enderror"
                                rows="3"
                                maxlength="1000"
                                placeholder="Ej. Traer protector solar, zapatos cómodos...">{{ old('recommendations', $product->recommendations ?? '') }}</textarea>
                            <small class="char-counter" id="recommendations-counter">0 / 1000</small>
                            <div class="invalid-feedback" id="recommendations-error"></div>
                            @error('recommendations')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            {{-- Duración (horas) --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="length">
                                        {{ __('m_tours.tour.fields.length_hours') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        name="length"
                                        id="length"
                                        class="form-control @error('length') is-invalid @enderror"
                                        value="{{ old('length', $product->length ?? '') }}"
                                        step="0.5"
                                        min="0.5"
                                        max="240"
                                        required
                                        data-validate="required|number|min:0.5|max:240">
                                    <small class="form-text text-muted">
                                        {{ __('m_tours.tour.validation.length_in_hours') }}
                                    </small>
                                    <div class="invalid-feedback" id="length-error"></div>
                                    @error('length')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Capacidad máxima --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="max_capacity">
                                        {{ __('m_tours.tour.fields.max_capacity') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        name="max_capacity"
                                        id="max_capacity"
                                        class="form-control @error('max_capacity') is-invalid @enderror"
                                        value="{{ old('max_capacity', $product->max_capacity ?? 12) }}"
                                        min="1"
                                        max="500"
                                        required
                                        data-validate="required|number|min:1|max:500">
                                    <small class="form-text text-muted">
                                        {{ __('m_tours.tour.validation.max_capacity_help') }}
                                    </small>
                                    <div class="invalid-feedback" id="max_capacity-error"></div>
                                    @error('max_capacity')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Tamaño de grupo --}}
                        <div class="form-group">
                            <label for="group_size">
                                {{ __('m_tours.tour.fields.group_size') }}
                                <span class="text-danger">*</span>
                            </label>
                            <input
                                type="number"
                                name="group_size"
                                id="group_size"
                                class="form-control @error('group_size') is-invalid @enderror"
                                value="{{ old('group_size', $product->group_size ?? '') }}"
                                min="1"
                                max="500"
                                required
                                data-validate="required|number|min:1|max:500">
                            <small class="form-text text-muted">
                                {{ __('m_tours.tour.hints.group_size') }}
                            </small>
                            <div class="invalid-feedback" id="group_size-error"></div>
                            @error('group_size')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Idiomas --}}
                        <div class="form-group">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="mb-0">
                                    {{ __('m_tours.tour.ui.available_languages') }}
                                    <span class="text-danger">*</span>
                                </label>

                                <div class="btn-group btn-group-sm" role="group">
                                    <button
                                        type="button"
                                        class="btn btn-success"
                                        data-toggle="modal"
                                        data-target="#modalCreateLanguage">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                    <a href="{{ route('admin.languages.index') }}"
                                        target="_blank"
                                        class="btn btn-info"
                                        title="{{ __('m_tours.tour.ui.manage_languages') }}">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="border rounded p-3" id="languages-container">
                                @php
                                $oldLanguages = old('languages', $isEditing
                                ? $product->languages->pluck('tour_language_id')->toArray()
                                : []);
                                @endphp

                                @forelse($languages ?? [] as $language)
                                <div class="custom-control custom-checkbox mb-2">
                                    <input
                                        type="checkbox"
                                        class="custom-control-input"
                                        id="language_{{ $language->tour_language_id }}"
                                        name="languages[]"
                                        value="{{ $language->tour_language_id }}"
                                        {{ in_array($language->tour_language_id, $oldLanguages, true) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="language_{{ $language->tour_language_id }}">
                                        <i class="fas fa-language"></i> {{ $language->name }}
                                    </label>
                                </div>
                                @empty
                                <p class="text-muted mb-0" id="languages-empty-state">
                                    {{ __('m_tours.tour.ui.no_languages_defined') }}
                                </p>
                                @endforelse
                            </div>

                            <small class="form-text text-muted">
                                {{ __('m_tours.tour.validation.languages_hint') ?? 'Selecciona al menos un idioma disponible para este tour' }}
                            </small>
                            <div class="invalid-feedback" id="languages-error" style="display: none;"></div>
                            @error('languages')
                            <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                            @error('languages.*')
                            <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Columna lateral --}}
                    <div class="col-md-4">
                        {{-- Color --}}
                        <div class="form-group">
                            <label for="color">
                                {{ __('m_tours.tour.ui.color') }}
                                <span class="text-danger">*</span>
                            </label>
                            <input
                                type="color"
                                name="color"
                                id="color"
                                class="form-control form-control-color @error('color') is-invalid @enderror"
                                value="{{ old('color', $product->color ?? '#3490dc') }}"
                                required
                                data-validate="required|color">
                            <div class="invalid-feedback" id="color-error"></div>
                            @error('color')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Tipo de tour --}}
                        <div class="form-group">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="product_type_id" class="mb-0">
                                    {{ __('Categoría de Producto') }}
                                    <span class="text-danger">*</span>
                                </label>

                                <div class="btn-group btn-group-sm" role="group">
                                    <button
                                        type="button"
                                        class="btn btn-success"
                                        data-toggle="modal"
                                        data-target="#modalCreateTourType">
                                        <i class="fas fa-plus"></i>
                                    </button>

                                    <a href="{{ route('admin.product-types.index') }}"
                                        target="_blank"
                                        class="btn btn-info"
                                        title="Gestionar Categorías">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </div>
                            </div>

                            <select
                                name="product_type_id"
                                id="product_type_id"
                                class="form-control @error('product_type_id') is-invalid @enderror"
                                required
                                data-validate="required|select">
                                <option value="">{{ '-- Seleccionar Categoría --' }}</option>
                                @foreach($productTypes ?? [] as $type)
                                <option value="{{ $type->product_type_id }}"
                                    {{ old('product_type_id', $product->product_type_id ?? '') == $type->product_type_id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="product_type_id-error"></div>
                            @error('product_type_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Subtipo de Producto (dinámico) --}}
                        <div class="form-group" id="subtype-container" style="display: none;">
                            <label for="product_subtype_id" class="mb-0">
                                <i class="fas fa-layer-group text-info"></i>
                                Subtipo
                            </label>
                            <small class="form-text text-muted d-block mb-2">
                                Clasificación específica del producto (opcional)
                            </small>

                            <select
                                name="product_subtype_id"
                                id="product_subtype_id"
                                class="form-control">
                                <option value="">Seleccionar subtipo...</option>
                            </select>
                        </div>

                        {{-- Estado (oculto, siempre borrador en este paso) --}}
                        <input type="hidden" name="is_active" value="0">

                        {{-- Info modo borrador --}}
                        <div class="alert alert-draft-info mt-3">
                            <i class="fas fa-info-circle"></i>
                            <strong>{{ __('m_tours.tour.wizard.draft_mode') }}</strong>
                            <p class="mb-0 small">{{ __('m_tours.tour.wizard.draft_explanation') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <div></div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-danger">
                            <i class="fas fa-times"></i> {{ __('m_tours.common.cancel') }}
                        </a>

                        <button type="submit" class="btn btn-primary ml-2" id="submit-btn">
                            {{ __('m_tours.tour.wizard.save_and_continue') }}
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- Modal: quick create tipo de tour --}}
<div class="modal fade" id="modalCreateTourType" tabindex="-1" aria-labelledby="modalCreateTourTypeLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST"
            action="{{ route('admin.products.wizard.quick.tour-type') }}"
            class="modal-content"
            id="formCreateTourType">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="modalCreateTourTypeLabel">
                    <i class="fas fa-tags"></i> Agregar Categoría de Producto
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('m_tours.common.close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="tourTypeModalErrors" class="alert alert-danger d-none"></div>

                <div class="form-group">
                    <label for="new_tour_type_name">
                        {{ __('m_tours.tour_type.fields.name') ?? 'Nombre' }}
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="name" id="new_tour_type_name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="new_tour_type_description">
                        {{ __('m_tours.tour_type.fields.description') ?? 'Descripción' }}
                    </label>
                    <textarea name="description" id="new_tour_type_description" class="form-control" rows="3"></textarea>
                </div>

                {{-- Duración sugerida (simple, sin $locale / $translation) --}}
                <div class="form-group">
                    <label for="new_tour_type_duration">
                        {{ __('m_tours.tour_type.fields.duration') ?? 'Duración' }}
                    </label>
                    <input
                        type="text"
                        class="form-control"
                        id="new_tour_type_duration"
                        name="duration"
                        list="durationOptions"
                        value="{{ old('duration') }}"
                        placeholder="{{ __('m_tours.tour_type.fields.duration_placeholder') ?? 'Ej: 4 horas, 6 horas, etc.' }}">
                    <datalist id="durationOptions">
                        <option value="4 horas"></option>
                        <option value="6 horas"></option>
                        <option value="8 horas"></option>
                        <option value="10 horas"></option>
                    </datalist>
                    <small class="form-text text-muted">
                        {{ __('m_tours.tour_type.fields.duration_hint') ?? 'Duración sugerida del tour (opcional)' }}
                    </small>
                </div>

                <div class="custom-control custom-switch">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox"
                        class="custom-control-input"
                        id="new_tour_type_is_active"
                        name="is_active"
                        value="1"
                        checked>
                    <label class="custom-control-label" for="new_tour_type_is_active">
                        {{ __('m_tours.tour_type.fields.status') ?? 'Activo' }}
                    </label>
                </div>

                <small class="form-text text-muted mt-2">
                    Creación rápida. Para configuraciones avanzadas usa el módulo de categorías de producto.
                </small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    {{ __('m_tours.common.cancel') }}
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> {{ __('m_tours.common.save') }}
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: quick create idioma --}}
<div class="modal fade" id="modalCreateLanguage" tabindex="-1" aria-labelledby="modalCreateLanguageLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST"
            action="{{ route('admin.products.wizard.quick.language') }}"
            class="modal-content"
            id="formCreateLanguage">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="modalCreateLanguageLabel">
                    <i class="fas fa-language"></i> {{ __('m_tours.tour.ui.add_language') }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('m_tours.common.close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="languageModalErrors" class="alert alert-danger d-none"></div>

                <div class="form-group">
                    <label for="new_language_name">
                        {{ __('m_tours.language.fields.name') ?? 'Nombre' }}
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="name" id="new_language_name" class="form-control" required>
                </div>

                <small class="form-text text-muted mt-2">
                    {{ __('m_tours.tour.ui.quick_create_language_hint') ?? 'Creación rápida. Para configuraciones avanzadas usa el módulo de idiomas.' }}
                </small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    {{ __('m_tours.common.cancel') }}
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> {{ __('m_tours.common.save') }}
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal de borradores existentes --}}
@if(isset($existingDrafts) && $existingDrafts->count() > 0)
@php
$mainDraft = $existingDrafts->sortByDesc('updated_at')->first();
@endphp

<div class="modal fade" id="draftsModal" tabindex="-1" aria-labelledby="draftsModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="draftsModalLabel">
                    <i class="fas fa-exclamation-triangle"></i>
                    {{ __('m_tours.tour.wizard.existing_drafts_title') }}
                </h5>
            </div>

            <div class="modal-body">
                <p class="lead mb-3">
                    {{ __('m_tours.tour.wizard.existing_drafts_message', ['count' => $existingDrafts->count()]) }}
                </p>

                <div class="table-responsive">
                    <table class="table table-hover table-sm mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('m_tours.tour.fields.name') }}</th>
                                <th>{{ __('m_tours.tour.fields.type') }}</th>
                                <th class="text-center">{{ __('m_tours.tour.wizard.current_step') }}</th>
                                <th>{{ __('m_tours.common.updated_at') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($existingDrafts as $draft)
                            <tr>
                                <td>
                                    <strong>{{ $draft->name ?: __('m_tours.tour.wizard.unnamed_draft') }}</strong><br>
                                    <small class="text-muted">{{ $draft->slug }}</small>
                                </td>
                                <td>
                                    @if($draft->tourType)
                                    <span class="badge bg-info">{{ $draft->tourType->name }}</span>
                                    @else
                                    <span class="text-muted">{{ __('m_tours.common.not_set') }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary">
                                        {{ __('m_tours.tour.wizard.step') }} {{ $draft->current_step ?? 1 }}/6
                                    </span>
                                </td>
                                <td>
                                    <small>{{ $draft->updated_at->diffForHumans() }}</small><br>
                                    <small class="text-muted">{{ $draft->updated_at->format('d/m/Y H:i') }}</small>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="alert alert-info mt-3 mb-0">
                    <i class="fas fa-info-circle"></i>
                    {{ __('m_tours.tour.wizard.drafts_info') }}
                </div>
            </div>

            <div class="modal-footer d-flex justify-content-around">
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary flex-fill mx-2">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('m_tours.tour.ui.back') }}
                </a>

                @if($mainDraft)
                <button type="button"
                    class="btn btn-danger flex-fill mx-2"
                    id="deleteMainDraft"
                    data-delete-url="{{ route('admin.products.wizard.delete-draft', $mainDraft) }}"
                    data-draft-name="{{ $mainDraft->name }}">
                    <i class="fas fa-trash-alt"></i>
                    {{ __('m_tours.tour.wizard.delete_draft') }}
                </button>

                <a href="{{ route('admin.products.wizard.continue', $mainDraft) }}"
                    class="btn btn-success flex-fill mx-2">
                    <i class="fas fa-play"></i>
                    {{ __('m_tours.tour.wizard.continue_draft') }}
                </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const IS_EDITING = @json($isEditing);

        const validationMessages = {
            required: @json(__('m_tours.tour.validation.required')),
            min: @json(__('m_tours.tour.validation.min')),
            max: @json(__('m_tours.tour.validation.max')),
            number: @json(__('m_tours.tour.validation.number')),
            slug: @json(__('m_tours.tour.validation.slug')),
            color: @json(__('m_tours.tour.validation.color')),
            select: @json(__('m_tours.tour.validation.select')),
        };

        const LANGUAGES_REQUIRED_MESSAGE = @json(__('m_tours.tour.validation.languages_required'));

        // ============================================================
        // VALIDACIÓN DEL FORMULARIO PRINCIPAL
        // ============================================================
        function validateField(field) {
            const rules = field.dataset.validate;
            if (!rules) return true;

            const value = field.value.trim();
            const fieldName = field.name;
            const errorDiv = document.getElementById(fieldName + '-error');

            let isValid = true;
            let errorMessage = '';
            const ruleList = rules.split('|');

            for (let rule of ruleList) {
                const [ruleName, ruleValue] = rule.split(':');

                switch (ruleName) {
                    case 'required':
                        if (!value) {
                            isValid = false;
                            errorMessage = validationMessages.required;
                        }
                        break;
                    case 'min': {
                        const minLength = parseFloat(ruleValue);
                        if (field.type === 'number') {
                            if (value && parseFloat(value) < minLength) {
                                isValid = false;
                                errorMessage = validationMessages.min.replace(':min', minLength);
                            }
                        } else if (value && value.length < minLength) {
                            isValid = false;
                            errorMessage = validationMessages.min.replace(':min', minLength);
                        }
                        break;
                    }
                    case 'max': {
                        const maxLength = parseInt(ruleValue);
                        if (field.type === 'number') {
                            if (value && parseFloat(value) > maxLength) {
                                isValid = false;
                                errorMessage = validationMessages.max.replace(':max', maxLength);
                            }
                        } else if (value && value.length > maxLength) {
                            isValid = false;
                            errorMessage = validationMessages.max.replace(':max', maxLength);
                        }
                        break;
                    }
                    case 'number':
                        if (value && isNaN(value)) {
                            isValid = false;
                            errorMessage = validationMessages.number;
                        }
                        break;
                    case 'slug':
                        if (value && !/^[a-z0-9-]*$/.test(value)) {
                            isValid = false;
                            errorMessage = validationMessages.slug;
                        }
                        break;
                    case 'color':
                        if (value && !/^#[0-9A-F]{6}$/i.test(value)) {
                            isValid = false;
                            errorMessage = validationMessages.color;
                        }
                        break;
                    case 'select':
                        if (!value) {
                            isValid = false;
                            errorMessage = validationMessages.select;
                        }
                        break;
                }
                if (!isValid) break;
            }

            if (isValid) {
                field.classList.remove('is-invalid');
                field.classList.add('is-valid');
                if (errorDiv) {
                    errorDiv.textContent = '';
                    errorDiv.style.display = 'none';
                }
            } else {
                field.classList.remove('is-valid');
                field.classList.add('is-invalid');
                if (errorDiv) {
                    errorDiv.textContent = errorMessage;
                    errorDiv.style.display = 'block';
                }
            }

            return isValid;
        }

        function updateCharCounter(field, counter, max) {
            const current = field.value.length;
            counter.textContent = `${current} / ${max}`;
            counter.classList.remove('near-limit', 'at-limit');
            if (current >= max) {
                counter.classList.add('at-limit');
            } else if (current >= max * 0.9) {
                counter.classList.add('near-limit');
            }
        }

        // Validación de idiomas (al menos uno debe estar seleccionado)
        function validateLanguages() {
            const languagesContainer = document.getElementById('languages-container');
            const languagesError = document.getElementById('languages-error');
            const checkedLanguages = languagesContainer.querySelectorAll('input[type="checkbox"]:checked');

            if (checkedLanguages.length === 0) {
                if (languagesError) {
                    languagesError.textContent = LANGUAGES_REQUIRED_MESSAGE;
                    languagesError.style.display = 'block';
                }
                languagesContainer.classList.add('border-danger');
                return false;
            }

            if (languagesError) {
                languagesError.textContent = '';
                languagesError.style.display = 'none';
            }
            languagesContainer.classList.remove('border-danger');
            return true;
        }

        const nameField = document.getElementById('name');
        const slugField = document.getElementById('slug');
        const nameCounter = document.getElementById('name-counter');
        const overviewField = document.getElementById('overview');
        const overviewCounter = document.getElementById('overview-counter');

        // Generación automática de slug
        if (nameField && slugField && !IS_EDITING) {
            nameField.addEventListener('input', function(e) {
                if (!slugField.value || slugField.dataset.autogenerated === 'true') {
                    const slug = e.target.value
                        .toLowerCase()
                        .normalize('NFD')
                        .replace(/[\u0300-\u036f]/g, '')
                        .replace(/[^a-z0-9]+/g, '-')
                        .replace(/^-+|-+$/g, '');
                    slugField.value = slug;
                    slugField.dataset.autogenerated = 'true';
                }
            });

            slugField.addEventListener('input', function() {
                if (this.value) {
                    this.dataset.autogenerated = 'false';
                }
            });
        }

        // Contadores de caracteres
        if (nameField && nameCounter) {
            updateCharCounter(nameField, nameCounter, 255);
            nameField.addEventListener('input', () => updateCharCounter(nameField, nameCounter, 255));
        }
        if (overviewField && overviewCounter) {
            updateCharCounter(overviewField, overviewCounter, 1000);
            overviewField.addEventListener('input', () => updateCharCounter(overviewField, overviewCounter, 1000));
        }

        // Validación en tiempo real
        const formFields = document.querySelectorAll('[data-validate]');
        formFields.forEach(field => {
            field.addEventListener('blur', () => validateField(field));
            let timeout;
            field.addEventListener('input', () => {
                clearTimeout(timeout);
                timeout = setTimeout(() => validateField(field), 400);
            });
        });

        // Agregar listeners a los checkboxes de idiomas
        const languageCheckboxes = document.querySelectorAll('#languages-container input[type="checkbox"]');
        languageCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', validateLanguages);
        });

        // Submit del formulario principal
        const form = document.getElementById('tour-details-form');
        const submitBtn = document.getElementById('submit-btn');

        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                let isFormValid = true;
                formFields.forEach(field => {
                    if (!validateField(field)) isFormValid = false;
                });

                // Validar idiomas
                if (!validateLanguages()) {
                    isFormValid = false;
                }

                if (!isFormValid) {
                    const firstInvalid = form.querySelector('.is-invalid, .border-danger');
                    if (firstInvalid) {
                        firstInvalid.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                        if (firstInvalid.tagName === 'INPUT' || firstInvalid.tagName === 'SELECT' || firstInvalid.tagName === 'TEXTAREA') {
                            firstInvalid.focus();
                        }
                    }

                    Swal.fire({
                        icon: 'error',
                        title: @json(__('m_tours.tour.validation.form_error_title')),
                        text: @json(__('m_tours.tour.validation.form_error_message')),
                        confirmButtonColor: '#6366f1',
                    });

                    return false;
                }

                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ' + @json(__('m_tours.tour.validation.saving'));
                }

                form.submit();
            });
        }

        // ============================================================
        // UTILIDADES PARA MODALES QUICK-CREATE
        // ============================================================
        function closeModalAndCleanup(modalId) {
            const modalEl = document.getElementById(modalId);
            if (modalEl && window.bootstrap && bootstrap.Modal) {
                const instance = bootstrap.Modal.getInstance(modalEl) || bootstrap.Modal.getOrCreateInstance(modalEl);
                instance.hide();

                // Force cleanup of backdrop if it sticks
                setTimeout(() => {
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    if (backdrops.length > 0) {
                        backdrops.forEach(bd => bd.remove());
                    }
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';
                }, 350); // Wait for animation
            }
        }

        function showModalErrors(errorDiv, errors) {
            if (!errorDiv) return;

            errorDiv.classList.remove('d-none');

            if (Array.isArray(errors)) {
                errorDiv.innerHTML = errors.map(err => `<div>${err}</div>`).join('');
            } else if (typeof errors === 'string') {
                errorDiv.innerHTML = `<div>${errors}</div>`;
            } else if (errors && typeof errors === 'object') {
                const messages = Object.values(errors).flat();
                errorDiv.innerHTML = messages.map(msg => `<div>${msg}</div>`).join('');
            }
        }

        function clearModalErrors(errorDiv) {
            if (!errorDiv) return;
            errorDiv.classList.add('d-none');
            errorDiv.innerHTML = '';
        }

        function validateRequired(field, errorDiv, fieldName) {
            const value = field.value.trim();

            if (!value) {
                field.classList.add('is-invalid');
                field.classList.remove('is-valid');
                if (errorDiv) {
                    errorDiv.textContent = validationMessages.required.replace(':attribute', fieldName);
                    errorDiv.style.display = 'block';
                }
                return false;
            }

            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
            if (errorDiv) {
                errorDiv.textContent = '';
                errorDiv.style.display = 'none';
            }
            return true;
        }

        // ============================================================
        // DYNAMIC SUBTYPE LOADING
        // ============================================================
        const productTypeSelect = document.getElementById('product_type_id');
        const subtypeContainer = document.getElementById('subtype-container');
        const subtypeSelect = document.getElementById('product_subtype_id');

        if (productTypeSelect && subtypeContainer && subtypeSelect) {
            // Load subtypes when product type changes
            productTypeSelect.addEventListener('change', function() {
                const productTypeId = this.value;
                
                if (!productTypeId) {
                    subtypeContainer.style.display = 'none';
                    subtypeSelect.innerHTML = '<option value="">Seleccionar subtipo...</option>';
                    return;
                }

                // Fetch subtypes from database
                fetch(`/admin/product-types/${productTypeId}/subtypes-data`)
                    .then(response => response.json())
                    .then(data => {
                        subtypeSelect.innerHTML = '<option value="">Seleccionar subtipo...</option>';
                        
                        if (data.length > 0) {
                            data.forEach(subtype => {
                                const option = document.createElement('option');
                                option.value = subtype.subtype_id;
                                option.textContent = subtype.name;
                                subtypeSelect.appendChild(option);
                            });
                            subtypeContainer.style.display = 'block';
                        } else {
                            subtypeContainer.style.display = 'none';
                        }
                    })
                    .catch(error => {
                        console.error('Error loading subtypes:', error);
                        subtypeContainer.style.display = 'none';
                    });
            });

            // Trigger on page load if editing
            if (productTypeSelect.value) {
                productTypeSelect.dispatchEvent(new Event('change'));
            }
        }

        // ============================================================
        // MODAL TIPO DE TOUR - CON VALIDACIONES
        // ============================================================
        const tourTypeForm = document.getElementById('formCreateTourType');
        const tourTypeSelect = document.getElementById('product_type_id');
        const tourTypeErrors = document.getElementById('tourTypeModalErrors');
        const tourTypeNameField = document.getElementById('new_tour_type_name');

        if (tourTypeNameField) {
            let tourTypeNameError = tourTypeNameField.nextElementSibling;
            if (!tourTypeNameError || !tourTypeNameError.classList.contains('invalid-feedback')) {
                tourTypeNameError = document.createElement('div');
                tourTypeNameError.className = 'invalid-feedback';
                tourTypeNameField.parentNode.insertBefore(tourTypeNameError, tourTypeNameField.nextSibling);
            }

            tourTypeNameField.addEventListener('input', function() {
                validateRequired(this, tourTypeNameError, @json(__('m_tours.tour_type.fields.name') ?? 'Nombre'));
            });

            tourTypeNameField.addEventListener('blur', function() {
                validateRequired(this, tourTypeNameError, @json(__('m_tours.tour_type.fields.name') ?? 'Nombre'));
            });
        }

        if (tourTypeForm) {
            tourTypeForm.addEventListener('submit', function(e) {
                e.preventDefault();

                if (tourTypeErrors) {
                    clearModalErrors(tourTypeErrors);
                }

                const isNameValid = validateRequired(
                    tourTypeNameField,
                    tourTypeNameField.nextElementSibling,
                    @json(__('m_tours.tour_type.fields.name') ?? 'Nombre')
                );

                if (!isNameValid) {
                    tourTypeNameField.focus();
                    return false;
                }

                const submitBtn = tourTypeForm.querySelector('button[type="submit"]');
                const originalBtnHtml = submitBtn ? submitBtn.innerHTML : '';
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ' + @json(__('m_tours.tour.validation.saving') ?? 'Guardando...');
                }

                const url = tourTypeForm.action;
                const formData = new FormData(tourTypeForm);

                fetch(url, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: formData
                    })
                    .then(async response => {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalBtnHtml;
                        }

                        if (response.ok) {
                            return response.json();
                        }

                        if (response.status === 422) {
                            const data = await response.json();
                            showModalErrors(tourTypeErrors, data.errors || data.message);
                            return null;
                        }

                        const text = await response.text();
                        showModalErrors(tourTypeErrors, text || 'Error al guardar');
                        return null;
                    })
                    .then(data => {
                        if (!data) return;

                        if (tourTypeSelect && data.id && data.name) {
                            const option = document.createElement('option');
                            option.value = data.id;
                            option.textContent = data.name;
                            option.selected = true;
                            tourTypeSelect.appendChild(option);
                        }

                        tourTypeForm.reset();
                        tourTypeNameField.classList.remove('is-valid', 'is-invalid');
                        closeModalAndCleanup('modalCreateTourType');

                        Swal.fire({
                            icon: 'success',
                            title: @json(__('m_tours.tour.validation.success')),
                            text: @json(__('m_tours.tour.validation.tour_type_created')),
                            timer: 2000,
                            showConfirmButton: false,
                        });
                    })
                    .catch(err => {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalBtnHtml;
                        }

                        showModalErrors(tourTypeErrors, err.message || 'Error de red');
                    });
            });
        }

        // ============================================================
        // MODAL IDIOMA - CON VALIDACIONES
        // ============================================================
        const languageForm = document.getElementById('formCreateLanguage');
        const languagesContainer = document.getElementById('languages-container');
        const languagesEmptyState = document.getElementById('languages-empty-state');
        const languageErrors = document.getElementById('languageModalErrors');
        const languageNameField = document.getElementById('new_language_name');

        if (languageNameField) {
            let languageNameError = languageNameField.nextElementSibling;
            if (!languageNameError || !languageNameError.classList.contains('invalid-feedback')) {
                languageNameError = document.createElement('div');
                languageNameError.className = 'invalid-feedback';
                languageNameField.parentNode.insertBefore(languageNameError, languageNameField.nextSibling);
            }

            languageNameField.addEventListener('input', function() {
                validateRequired(this, languageNameError, @json(__('m_tours.language.fields.name') ?? 'Nombre'));
            });

            languageNameField.addEventListener('blur', function() {
                validateRequired(this, languageNameError, @json(__('m_tours.language.fields.name') ?? 'Nombre'));
            });
        }

        if (languageForm) {
            languageForm.addEventListener('submit', function(e) {
                e.preventDefault();

                if (languageErrors) {
                    clearModalErrors(languageErrors);
                }

                const isNameValid = validateRequired(
                    languageNameField,
                    languageNameField.nextElementSibling,
                    @json(__('m_tours.language.fields.name') ?? 'Nombre')
                );

                if (!isNameValid) {
                    languageNameField.focus();
                    return false;
                }

                const submitBtn = languageForm.querySelector('button[type="submit"]');
                const originalBtnHtml = submitBtn ? submitBtn.innerHTML : '';
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ' + @json(__('m_tours.tour.validation.saving') ?? 'Guardando...');
                }

                const url = languageForm.action;
                const formData = new FormData(languageForm);

                fetch(url, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: formData
                    })
                    .then(async response => {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalBtnHtml;
                        }

                        if (response.ok) {
                            return response.json();
                        }

                        if (response.status === 422) {
                            const data = await response.json();
                            showModalErrors(languageErrors, data.errors || data.message);
                            return null;
                        }

                        const text = await response.text();
                        showModalErrors(languageErrors, text || 'Error al guardar');
                        return null;
                    })
                    .then(data => {
                        if (!data) return;

                        if (languagesEmptyState) languagesEmptyState.remove();

                        if (languagesContainer && data.id && data.name) {
                            const wrapper = document.createElement('div');
                            wrapper.className = 'custom-control custom-checkbox mb-2';
                            const checkboxId = 'language_' + data.id;

                            wrapper.innerHTML = `
                                <input type="checkbox"
                                       class="custom-control-input"
                                       id="${checkboxId}"
                                       name="languages[]"
                                       value="${data.id}"
                                       checked>
                                <label class="custom-control-label" for="${checkboxId}">
                                    <i class="fas fa-language"></i> ${data.name}
                                </label>
                            `;
                            languagesContainer.appendChild(wrapper);

                            // Agregar listener al nuevo checkbox
                            const newCheckbox = wrapper.querySelector('input[type="checkbox"]');
                            if (newCheckbox) {
                                newCheckbox.addEventListener('change', validateLanguages);
                            }
                        }

                        languageForm.reset();
                        languageNameField.classList.remove('is-valid', 'is-invalid');
                        closeModalAndCleanup('modalCreateLanguage');

                        Swal.fire({
                            icon: 'success',
                            title: @json(__('m_tours.tour.validation.success')),
                            text: @json(__('m_tours.tour.validation.language_created')),
                            timer: 2000,
                            showConfirmButton: false,
                        });
                    })
                    .catch(err => {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalBtnHtml;
                        }

                        showModalErrors(languageErrors, err.message || 'Error de red');
                    });
            });
        }

        // ============================================================
        // LIMPIAR VALIDACIONES AL ABRIR MODALES
        // ============================================================
        const modalCreateTourType = document.getElementById('modalCreateTourType');
        if (modalCreateTourType) {
            modalCreateTourType.addEventListener('show.bs.modal', function() {
                if (tourTypeErrors) clearModalErrors(tourTypeErrors);
                if (tourTypeNameField) {
                    tourTypeNameField.classList.remove('is-valid', 'is-invalid');
                    const errorDiv = tourTypeNameField.nextElementSibling;
                    if (errorDiv && errorDiv.classList.contains('invalid-feedback')) {
                        errorDiv.textContent = '';
                        errorDiv.style.display = 'none';
                    }
                }
            });
        }

        const modalCreateLanguage = document.getElementById('modalCreateLanguage');
        if (modalCreateLanguage) {
            modalCreateLanguage.addEventListener('show.bs.modal', function() {
                if (languageErrors) clearModalErrors(languageErrors);
                if (languageNameField) {
                    languageNameField.classList.remove('is-valid', 'is-invalid');
                    const errorDiv = languageNameField.nextElementSibling;
                    if (errorDiv && errorDiv.classList.contains('invalid-feedback')) {
                        errorDiv.textContent = '';
                        errorDiv.style.display = 'none';
                    }
                }
            });
        }

        // ============================================================
        // MODAL DE BORRADORES EXISTENTES
        // ============================================================
        @if(isset($existingDrafts) && $existingDrafts-> count() > 0)
        const draftsModalEl = document.getElementById('draftsModal');
        if (draftsModalEl && window.bootstrap && bootstrap.Modal) {
            const draftsModal = new bootstrap.Modal(draftsModalEl);
            draftsModal.show();
        }

        const deleteMainDraftBtn = document.getElementById('deleteMainDraft');
        if (deleteMainDraftBtn) {
            deleteMainDraftBtn.addEventListener('click', function() {
                const deleteUrl = this.dataset.deleteUrl;
                const draftName = this.dataset.draftName || '';

                Swal.fire({
                    title: @json(__('m_tours.tour.wizard.confirm_delete_title')),
                    html: '<p>{{ __("m_tours.tour.wizard.confirm_delete_message") }}</p>' +
                        (draftName ? '<p class="font-weight-bold">' + draftName + '</p>' : ''),
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#4b5563',
                    confirmButtonText: @json(__('m_tours.common.delete')),
                    cancelButtonText: @json(__('m_tours.common.cancel')),
                }).then((result) => {
                    if (result.isConfirmed && deleteUrl) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = deleteUrl;

                        const tokenInput = document.createElement('input');
                        tokenInput.type = 'hidden';
                        tokenInput.name = '_token';
                        tokenInput.value = @json(csrf_token());

                        const methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        methodInput.value = 'DELETE';

                        form.appendChild(tokenInput);
                        form.appendChild(methodInput);
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        }
        @endif
    });
</script>
@endpush
