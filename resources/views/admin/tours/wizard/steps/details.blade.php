{{-- resources/views/admin/tours/wizard/steps/details.blade.php --}}

@extends('adminlte::page')

@section('title', __('m_tours.tour.wizard.steps.details'))
@push('css')
<style>
    /* Permitir scroll vertical */
    body.sidebar-mini .content-wrapper {
        overflow-y: auto !important;
    }

    /* Header mejorado */
    .details-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 0.5rem;
        margin-bottom: 2rem;
    }

    .details-header h1 {
        margin: 0;
        font-size: 1.75rem;
        font-weight: 600;
    }

    .details-header p {
        margin: 0.5rem 0 0 0;
        opacity: 0.9;
    }

    .details-header .btn-secondary {
        border-color: rgba(255,255,255,0.5);
        background: rgba(74, 85, 104, 0.9);
        color: white;
        font-weight: 600;
    }

    .details-header .btn-secondary:hover {
        background: rgba(90, 103, 120, 1);
        border-color: white;
    }

    /* Tarjetas principales */
    .details-card {
        border: none;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        border-radius: 0.5rem;
        overflow: hidden;
        background: #2d3748;
    }

    .details-card .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 1rem;
    }

    .details-card .card-header h3 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 600;
    }

    .details-card .card-body {
        background: #2d3748;
        color: #cbd5e0;
        padding: 1.5rem;
    }

    /* Tarjeta de categorías y sidebar */
    .sidebar-card {
        background: #2d3748;
        border: 1px solid #4a5568;
        border-radius: 0.5rem;
        overflow: hidden;
    }

    .sidebar-card .card-header {
        background: #434d5f;
        border-bottom: 1px solid #4a5568;
        padding: 0.75rem 1rem;
        color: #e2e8f0;
    }

    .sidebar-card .card-header h3 {
        margin: 0;
        font-size: 0.95rem;
        font-weight: 600;
    }

    .sidebar-card .card-body {
        background: #2d3748;
        padding: 1rem;
    }

    /* Custom controls */
    .custom-control-label {
        color: #cbd5e0;
        cursor: pointer;
    }

    .custom-control-label::before {
        background-color: #3a4556;
        border-color: #4a5568;
    }

    .custom-control-input:checked ~ .custom-control-label::before {
        background-color: #667eea;
        border-color: #667eea;
    }

    .custom-control-input:focus ~ .custom-control-label::before {
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    /* Selects e inputs */
    select.form-control,
    input.form-control,
    textarea.form-control {
        background: #3a4556;
        border: 1px solid #4a5568;
        color: #e2e8f0;
    }

    select.form-control:focus,
    input.form-control:focus,
    textarea.form-control:focus {
        background: #3a4556;
        border-color: #667eea;
        color: #e2e8f0;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    select.form-control option {
        background: #2d3748;
        color: #e2e8f0;
    }

    input.form-control:disabled,
    select.form-control:disabled,
    textarea.form-control:disabled {
        background: #2a3545;
        color: #718096;
        cursor: not-allowed;
    }

    label {
        color: #e2e8f0;
        font-weight: 600;
    }

    .form-text.text-muted,
    small.text-muted {
        color: #a0aec0 !important;
    }

    /* Alerts */
    .alert-success {
        background: rgba(72, 187, 120, 0.15);
        border: 1px solid rgba(72, 187, 120, 0.3);
        color: #9ae6b4;
    }

    .alert-success .close {
        color: #9ae6b4;
        opacity: 0.8;
    }

    .alert-danger {
        background: rgba(245, 101, 101, 0.15);
        border: 1px solid rgba(245, 101, 101, 0.3);
        color: #fc8181;
    }

    .alert-danger .close {
        color: #fc8181;
        opacity: 0.8;
    }

    .invalid-feedback {
        color: #fc8181;
    }

    .is-invalid {
        border-color: #f56565 !important;
    }

    .is-invalid:focus {
        box-shadow: 0 0 0 0.2rem rgba(245, 101, 101, 0.25) !important;
    }

    /* Empty state */
    .empty-state {
        background: #3a4556;
        border: 2px dashed #4a5568;
        border-radius: 0.375rem;
        padding: 2rem;
        text-align: center;
        color: #a0aec0;
    }

    .empty-state i {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        color: #718096;
    }

    /* Contenedor de idiomas y similares */
    .border.rounded {
        background: #3a4556 !important;
        border-color: #4a5568 !important;
    }

    #languages-container {
        max-height: 300px;
        overflow-y: auto;
    }

    #languages-container::-webkit-scrollbar {
        width: 8px;
    }

    #languages-container::-webkit-scrollbar-track {
        background: #2d3748;
        border-radius: 4px;
    }

    #languages-container::-webkit-scrollbar-thumb {
        background: #4a5568;
        border-radius: 4px;
    }

    #languages-container::-webkit-scrollbar-thumb:hover {
        background: #667eea;
    }

    /* Alert info mejorado */
    .alert-info {
        background: rgba(102, 126, 234, 0.15);
        border: 1px solid rgba(102, 126, 234, 0.3);
        color: #b794f6;
    }

    .alert-info strong {
        color: #c3dafe;
    }

    .alert-info i {
        color: #9f7aea;
    }

    /* Botones */
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        font-weight: 600;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #5a67d8 0%, #6b42a0 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.2);
    }

    .btn-primary:focus {
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.5);
    }

    .btn-success {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        border: none;
        color: white;
        font-weight: 600;
    }

    .btn-success:hover {
        background: linear-gradient(135deg, #38a169 0%, #2f855a 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.2);
    }

    .btn-info {
        background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
        border: none;
        color: white;
        font-weight: 600;
    }

    .btn-info:hover {
        background: linear-gradient(135deg, #3182ce 0%, #2c5282 100%);
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

    .btn-outline-secondary {
        border-color: #4a5568;
        color: #cbd5e0;
        background: transparent;
    }

    .btn-outline-secondary:hover {
        background: #3a4556;
        border-color: #667eea;
        color: #e2e8f0;
    }

    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
    }

    /* Card footer */
    .card-footer {
        background: #2a3545;
        border-top: 1px solid #4a5568;
        padding: 1rem 1.5rem;
    }

    /* Custom Switch */
    .custom-switch .custom-control-label::before {
        background-color: #4a5568;
        border: none;
    }

    .custom-switch .custom-control-input:checked ~ .custom-control-label::before {
        background-color: #667eea;
        border: none;
    }

    .custom-switch .custom-control-input:focus ~ .custom-control-label::before {
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    /* Modal overrides (genérico) */
    .modal-content {
        background: #2d3748;
        color: #e2e8f0;
        border: 1px solid #4a5568;
    }

    .modal-header {
        background: #434d5f;
        border-bottom: 1px solid #4a5568;
        color: #e2e8f0;
    }

    .modal-title {
        color: #e2e8f0;
    }

    .modal-body {
        background: #2d3748;
    }

    .modal-footer {
        background: #2d3748;
        border-top: 1px solid #4a5568;
    }

    .close {
        color: #cbd5e0;
        opacity: 0.8;
        text-shadow: none;
    }

    .close:hover {
        color: #e2e8f0;
        opacity: 1;
    }

    /* Text colors */
    .text-danger {
        color: #fc8181 !important;
    }

    .text-success {
        color: #9ae6b4 !important;
    }

    .text-muted {
        color: #a0aec0 !important;
    }

    /* Scrollbars */
    ::-webkit-scrollbar {
        width: 10px;
        height: 10px;
    }

    ::-webkit-scrollbar-track {
        background: #1a202c;
        border-radius: 5px;
    }

    ::-webkit-scrollbar-thumb {
        background: #4a5568;
        border-radius: 5px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #667eea;
    }

    /* ============================
       Drafts modal overrides
       ============================ */

    /* Header amarillo + texto oscuro */
    #draftsModal .modal-header {
        background: #fbbf24;
        color: #111827;
        border-bottom: 1px solid #f59e0b;
    }

    #draftsModal .modal-header .modal-title,
    #draftsModal .modal-header .modal-title i {
        color: #111827;
    }

    /* Cuerpo y footer en dark */
    #draftsModal .modal-body {
        background: #0f172a;
        color: #e5e7eb;
    }

    #draftsModal .modal-footer {
        background: #0b1220;
        border-top: 1px solid #374151;
    }

    /* Tabla en dark mode */
    #draftsModal .table {
        color: #e5e7eb;
        margin-bottom: 0;
    }

    #draftsModal .table thead th {
        background: #1f2937;
        color: #e5e7eb;
        border-bottom: 1px solid #4b5563;
        font-weight: 600;
    }

    #draftsModal .table tbody tr {
        background-color: #020617;
    }

    #draftsModal .table tbody tr:nth-child(even) {
        background-color: #111827;
    }

    #draftsModal .table tbody tr:hover {
        background-color: #374151;
    }

    /* Badges dentro del modal */
    #draftsModal .badge.bg-primary,
    #draftsModal .badge.bg-info {
        border-radius: 999px;
        padding: 0.25rem 0.6rem;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .details-header {
            padding: 1.5rem;
        }

        .details-header h1 {
            font-size: 1.5rem;
        }

        .details-card .card-body {
            padding: 1rem;
        }
    }
</style>
@endpush


@section('content')
@php
    $isEditing = isset($tour) && $tour && $tour->exists;
    $currentStep = $step ?? 1;
@endphp

<div class="container-fluid">
    {{-- Header mejorado --}}
    <div class="details-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1>
                    <i class="fas fa-info-circle"></i>
                    @if($isEditing)
                        {{ __('m_tours.tour.wizard.edit_tour') }}
                    @else
                        {{ __('m_tours.tour.wizard.create_new_tour') }}
                    @endif
                </h1>
                <p class="mb-0">{{ __('m_tours.tour.wizard.steps.details') }}</p>
            </div>
        </div>
    </div>

    {{-- Stepper superior --}}
    @include('admin.tours.wizard.partials.stepper', [
        'currentStep' => $currentStep,
        'steps'       => $steps ?? [],
        'tour'        => $tour ?? null,
    ])

    {{-- Mensajes de error / éxito --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            <strong>{{ __('m_tours.common.form_errors_title') }}</strong>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="{{ __('m_tours.common.close') }}"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            {{ session('success') }}
            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="{{ __('m_tours.common.close') }}"></button>
        </div>
    @endif

    @if(isset($limitWarning) && $limitWarning)
        <div class="alert alert-info alert-dismissible fade show mt-3" role="alert">
            {!! $limitWarning !!}
            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="{{ __('m_tours.common.close') }}"></button>
        </div>
    @endif

    {{-- Formulario de detalles --}}
    <form
        id="tour-details-form"
        method="POST"
        action="{{ $isEditing
            ? route('admin.tours.wizard.update.details', $tour)
            : route('admin.tours.wizard.store.details') }}"
    >
        @csrf

        <div class="card details-card mt-3">
            <div class="card-header">
                <h3 class="card-title mb-0">
                    <i class="fas fa-info-circle"></i>
                    {{ __('m_tours.tour.wizard.steps.details') }}
                </h3>
            </div>

            <div class="card-body">
                <div class="row">
                    {{-- Columna Principal --}}
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
                                value="{{ old('name', $tour->name ?? '') }}"
                                required
                                autofocus>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Slug --}}
                        <div class="form-group">
                            <label for="slug">Slug</label>
                            <input
                                type="text"
                                name="slug"
                                id="slug"
                                class="form-control @error('slug') is-invalid @enderror"
                                value="{{ old('slug', $tour->slug ?? '') }}"
                                placeholder="{{ __('m_tours.tour.ui.generate_auto') }}">
                            <small class="form-text text-muted">
                                {{ __('m_tours.tour.ui.slug_help') }}
                            </small>
                            @error('slug')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Overview --}}
                        <div class="form-group">
                            <label for="overview">{{ __('m_tours.tour.fields.overview') }}</label>
                            <textarea
                                name="overview"
                                id="overview"
                                class="form-control @error('overview') is-invalid @enderror"
                                rows="5">{{ old('overview', $tour->overview ?? '') }}</textarea>
                            @error('overview')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            {{-- Duración --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="length">{{ __('m_tours.tour.fields.length_hours') }}</label>
                                    <input
                                        type="number"
                                        name="length"
                                        id="length"
                                        class="form-control @error('length') is-invalid @enderror"
                                        value="{{ old('length', $tour->length ?? '') }}"
                                        step="0.5"
                                        min="0">
                                    @error('length')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Capacidad Máxima --}}
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
                                        value="{{ old('max_capacity', $tour->max_capacity ?? 12) }}"
                                        min="1"
                                        required>
                                    @error('max_capacity')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Tamaño grupo --}}
                        <div class="form-group">
                            <label for="group_size">{{ __('m_tours.tour.fields.group_size') }}</label>
                            <input
                                type="number"
                                name="group_size"
                                id="group_size"
                                class="form-control @error('group_size') is-invalid @enderror"
                                value="{{ old('group_size', $tour->group_size ?? '') }}"
                                min="1">
                            <small class="form-text text-muted">
                                {{ __('m_tours.tour.hints.group_size') }}
                            </small>
                            @error('group_size')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Idiomas --}}
                        <div class="form-group">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="mb-0">{{ __('m_tours.tour.ui.available_languages') }}</label>

                                <div class="btn-group btn-group-sm" role="group">
                                    <button
                                        type="button"
                                        class="btn btn-success"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalCreateLanguage">
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
                                        ? $tour->languages->pluck('tour_language_id')->toArray()
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

                            @error('languages')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                            @error('languages.*')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Columna Lateral --}}
                    <div class="col-md-4">
                        {{-- Color --}}
                        <div class="form-group">
                            <label for="color">{{ __('m_tours.tour.ui.color') }}</label>
                            <input
                                type="color"
                                name="color"
                                id="color"
                                class="form-control form-control-color @error('color') is-invalid @enderror"
                                value="{{ old('color', $tour->color ?? '#3490dc') }}">
                            @error('color')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Tipo de Tour --}}
                        <div class="form-group">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="tour_type_id" class="mb-0">
                                    {{ __('m_tours.tour.fields.type') }}
                                </label>

                                <div class="btn-group btn-group-sm" role="group">
                                    <button
                                        type="button"
                                        class="btn btn-success"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalCreateTourType">
                                        <i class="fas fa-plus"></i>
                                    </button>

                                    <a href="{{ route('admin.tourtypes.index') }}"
                                       target="_blank"
                                       class="btn btn-info"
                                       title="{{ __('m_tours.tour.ui.manage_tour_types') }}">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </div>
                            </div>

                            <select
                                name="tour_type_id"
                                id="tour_type_id"
                                class="form-control @error('tour_type_id') is-invalid @enderror">
                                <option value="">{{ '-- ' . __('m_tours.tour.ui.select_type') . ' --' }}</option>
                                @foreach($tourTypes ?? [] as $type)
                                    <option value="{{ $type->tour_type_id }}"
                                            {{ old('tour_type_id', $tour->tour_type_id ?? '') == $type->tour_type_id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tour_type_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Estado --}}
                        <div class="form-group mt-3">
                            <div class="custom-control custom-switch">
                                <input type="hidden" name="is_active" value="0">
                                <input
                                    type="checkbox"
                                    class="custom-control-input"
                                    id="is_active"
                                    name="is_active"
                                    value="1"
                                    {{ old('is_active', $tour->is_active ?? false) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">
                                    {{ __('m_tours.tour.fields.status') }}
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                {{ __('m_tours.tour.wizard.hints.status') }}
                            </small>
                        </div>

                        {{-- Info borrador --}}
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle"></i>
                            <strong>{{ __('m_tours.tour.wizard.draft_mode') }}</strong>
                            <p class="mb-0 small">{{ __('m_tours.tour.wizard.draft_explanation') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <div>
                        {{-- Paso anterior vacío en step 1 --}}
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.tours.index') }}" class="btn btn-danger">
                            <i class="fas fa-times"></i> {{ __('m_tours.common.cancel') }}
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

{{-- ========================= --}}
{{-- Modales rápidos           --}}
{{-- ========================= --}}

{{-- Modal: Crear Tipo de Tour --}}
<div class="modal fade" id="modalCreateTourType" tabindex="-1" aria-labelledby="modalCreateTourTypeLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST"
              action="{{ route('admin.tours.wizard.quick.tour-type') }}"
              class="modal-content"
              id="formCreateTourType">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="modalCreateTourTypeLabel">
                    <i class="fas fa-tags"></i> {{ __('m_tours.tour.ui.add_tour_type') }}
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="{{ __('m_tours.common.close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="tourTypeModalErrors" class="alert alert-danger d-none"></div>

                <div class="form-group">
                    <label for="new_tour_type_name">{{ __('m_tours.tour_type.fields.name') ?? 'Nombre' }}</label>
                    <input type="text" name="name" id="new_tour_type_name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="new_tour_type_description">{{ __('m_tours.tour_type.fields.description') ?? 'Descripción' }}</label>
                    <textarea name="description" id="new_tour_type_description" class="form-control" rows="3"></textarea>
                </div>

                <div class="custom-control custom-switch">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" class="custom-control-input" id="new_tour_type_is_active" name="is_active" value="1" checked>
                    <label class="custom-control-label" for="new_tour_type_is_active">
                        {{ __('m_tours.tour_type.fields.status') ?? 'Activo' }}
                    </label>
                </div>

                <small class="form-text text-muted mt-2">
                    {{ __('m_tours.tour.ui.quick_create_type_hint') ?? 'Creación rápida. Para configuraciones avanzadas usa el módulo de tipos de tour.' }}
                </small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal">
                    {{ __('m_tours.common.cancel') }}
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> {{ __('m_tours.common.save') }}
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Crear Idioma --}}
<div class="modal fade" id="modalCreateLanguage" tabindex="-1" aria-labelledby="modalCreateLanguageLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST"
              action="{{ route('admin.tours.wizard.quick.language') }}"
              class="modal-content"
              id="formCreateLanguage">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="modalCreateLanguageLabel">
                    <i class="fas fa-language"></i> {{ __('m_tours.tour.ui.add_language') }}
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="{{ __('m_tours.common.close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="languageModalErrors" class="alert alert-danger d-none"></div>

                <div class="form-group">
                    <label for="new_language_name">{{ __('m_tours.language.fields.name') ?? 'Nombre' }}</label>
                    <input type="text" name="name" id="new_language_name" class="form-control" required>
                </div>

                <small class="form-text text-muted mt-2">
                    {{ __('m_tours.tour.ui.quick_create_language_hint') ?? 'Creación rápida. Para configuraciones avanzadas usa el módulo de idiomas.' }}
                </small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal">
                    {{ __('m_tours.common.cancel') }}
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> {{ __('m_tours.common.save') }}
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ========================= --}}
{{-- Modal de drafts existentes --}}
{{-- ========================= --}}
@if(isset($existingDrafts) && $existingDrafts->count() > 0)
    @php
        $mainDraft = $existingDrafts->sortByDesc('updated_at')->first();
    @endphp

    <div class="modal fade" id="draftsModal" tabindex="-1" aria-labelledby="draftsModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header drafts-modal-header">
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
              <table class="table table-hover table-sm">
                <thead class="drafts-table-head">
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
                        <strong>{{ $draft->name }}</strong>
                        <br>
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
            {{-- Botón Eliminar Borrador --}}
            @if($mainDraft)
              <button type="button"
                      class="btn btn-danger flex-fill mx-2"
                      id="deleteMainDraft"
                      data-draft-id="{{ $mainDraft->tour_id }}"
                      data-draft-name="{{ $mainDraft->name }}">
                <i class="fas fa-trash-alt"></i>
                {{ __('m_tours.tour.wizard.delete_draft') }}
              </button>
            @endif

            {{-- Botón Continuar Draft --}}
            @if($mainDraft)
              <a href="{{ route('admin.tours.wizard.continue', $mainDraft) }}"
                 class="btn btn-success flex-fill mx-2">
                <i class="fas fa-play"></i>
                {{ __('m_tours.tour.wizard.continue_draft') }}
              </a>
            @endif
          </div>
        </div>
      </div>
    </div>

    {{-- Formularios ocultos --}}
    @foreach($existingDrafts as $draft)
      <form id="deleteDraftForm{{ $draft->tour_id }}"
            action="{{ route('admin.tours.wizard.delete-draft', $draft) }}"
            method="POST"
            style="display: none;">
        @csrf
        @method('DELETE')
      </form>
    @endforeach

    {{-- Se mantiene por si lo usas en otro lugar, pero sin botón visible --}}
    <form id="deleteAllDraftsForm"
          action="{{ route('admin.tours.wizard.delete-all-drafts') }}"
          method="POST"
          style="display: none;">
      @csrf
      @method('DELETE')
    </form>
@endif
@endsection

@push('js')
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
        const IS_EDITING = @json($isEditing);

        const nameField = document.getElementById('name');
        const slugField = document.getElementById('slug');

        if (nameField && slugField && !IS_EDITING) {
            nameField.addEventListener('input', function (e) {
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

            slugField.addEventListener('input', function () {
                if (this.value) {
                    this.dataset.autogenerated = 'false';
                }
            });
        }

        function closeModalAndCleanup(modalId) {
            const modalEl = document.getElementById(modalId);

            if (modalEl) {
                if (window.bootstrap && bootstrap.Modal) {
                    const instance = bootstrap.Modal.getInstance(modalEl) || bootstrap.Modal.getOrCreateInstance(modalEl);
                    instance.hide();
                }
                else if (window.jQuery && typeof jQuery(modalEl).modal === 'function') {
                    jQuery(modalEl).modal('hide');
                } else {
                    modalEl.classList.remove('show');
                    modalEl.style.display = 'none';
                }
            }

            document.body.classList.remove('modal-open');

            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(function (bd) {
                if (bd.parentNode) {
                    bd.parentNode.removeChild(bd);
                }
            });
        }

        function ajaxForm(formEl, onSuccess, onError) {
            if (!formEl) return;
            formEl.addEventListener('submit', function (e) {
                e.preventDefault();

                const url = formEl.action;
                const formData = new FormData(formEl);

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: formData
                })
                    .then(async response => {
                        if (response.ok) {
                            return response.json();
                        }
                        if (response.status === 422) {
                            const data = await response.json();
                            if (onError) onError(data);
                            return null;
                        }
                        const text = await response.text();
                        if (onError) onError({ message: text });
                        return null;
                    })
                    .then(data => {
                        if (data && onSuccess) {
                            onSuccess(data);
                        }
                    })
                    .catch(err => {
                        if (onError) onError({ message: err.message || 'Error de red' });
                    });
            });
        }

        // Crear Tipo de Tour
        const tourTypeForm = document.getElementById('formCreateTourType');
        const tourTypeSelect = document.getElementById('tour_type_id');
        const tourTypeErrors = document.getElementById('tourTypeModalErrors');

        ajaxForm(tourTypeForm, function (data) {
            if (tourTypeErrors) {
                tourTypeErrors.classList.add('d-none');
                tourTypeErrors.innerHTML = '';
            }

            if (!data || !data.id || !data.name) return;

            if (tourTypeSelect) {
                const option = document.createElement('option');
                option.value = data.id;
                option.textContent = data.name;
                option.selected = true;
                tourTypeSelect.appendChild(option);
            }

            tourTypeForm.reset();
            closeModalAndCleanup('modalCreateTourType');
        }, function (errorData) {
            if (!tourTypeErrors) return;
            tourTypeErrors.classList.remove('d-none');

            if (errorData && errorData.errors) {
                const msgs = Object.values(errorData.errors).flat();
                tourTypeErrors.innerHTML = msgs.map(m => `<div>${m}</div>`).join('');
            } else if (errorData && errorData.message) {
                tourTypeErrors.innerHTML = `<div>${errorData.message}</div>`;
            } else {
                tourTypeErrors.innerHTML = `<div>Error al crear el tipo de tour.</div>`;
            }
        });

        // Crear Idioma
        const languageForm = document.getElementById('formCreateLanguage');
        const languagesContainer = document.getElementById('languages-container');
        const languagesEmptyState = document.getElementById('languages-empty-state');
        const languageErrors = document.getElementById('languageModalErrors');

        ajaxForm(languageForm, function (data) {
            if (languageErrors) {
                languageErrors.classList.add('d-none');
                languageErrors.innerHTML = '';
            }

            if (!data || !data.id || !data.name) return;

            if (languagesEmptyState) {
                languagesEmptyState.remove();
            }

            if (languagesContainer) {
                const wrapper = document.createElement('div');
                wrapper.className = 'custom-control custom-checkbox mb-2';

                const checkboxId = 'language_' + data.id;

                wrapper.innerHTML = `
                    <input
                        type="checkbox"
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
            }

            languageForm.reset();
            closeModalAndCleanup('modalCreateLanguage');
        }, function (errorData) {
            if (!languageErrors) return;
            languageErrors.classList.remove('d-none');

            if (errorData && errorData.errors) {
                const msgs = Object.values(errorData.errors).flat();
                languageErrors.innerHTML = msgs.map(m => `<div>${m}</div>`).join('');
            } else if (errorData && errorData.message) {
                languageErrors.innerHTML = `<div>${errorData.message}</div>`;
            } else {
                languageErrors.innerHTML = `<div>Error al crear el idioma.</div>`;
            }
        });

        // Drafts modal
        @if(isset($existingDrafts) && $existingDrafts->count() > 0)
            const draftsModalEl = document.getElementById('draftsModal');
            if (draftsModalEl) {
                if (window.bootstrap && bootstrap.Modal) {
                    const draftsModal = new bootstrap.Modal(draftsModalEl);
                    draftsModal.show();
                } else if (window.jQuery && typeof jQuery(draftsModalEl).modal === 'function') {
                    jQuery(draftsModalEl).modal('show');
                }
            }

            // Eliminar borrador principal (botón footer)
            const deleteMainDraftBtn = document.getElementById('deleteMainDraft');
            if (deleteMainDraftBtn) {
                deleteMainDraftBtn.addEventListener('click', function () {
                    const draftId = this.dataset.draftId;
                    const draftName = this.dataset.draftName;

                    Swal.fire({
                      title: @json(__('m_tours.tour.wizard.confirm_delete_title')),
                      html: '<p>{{ __("m_tours.tour.wizard.confirm_delete_message") }}</p><p class="font-weight-bold">' + draftName + '</p>',
                      icon: 'warning',
                      showCancelButton: true,
                      confirmButtonColor: '#d33',
                      cancelButtonColor: '#3085d6',
                      confirmButtonText: @json(__('m_tours.common.delete')),
                      cancelButtonText: @json(__('m_tours.common.cancel')),
                    }).then((result) => {
                      if (result.isConfirmed) {
                        const form = document.getElementById('deleteDraftForm' + draftId);
                        if (form) form.submit();
                      }
                    });
                });
            }
        @endif
    });
  </script>
@endpush
