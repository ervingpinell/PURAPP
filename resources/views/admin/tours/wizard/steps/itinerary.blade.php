{{-- resources/views/admin/tours/wizard/steps/itinerary.blade.php --}}

@extends('adminlte::page')

@section('title', __('m_tours.tour.wizard.steps.itinerary'))

@push('css')
<style>
    /* Botones de acción superior */
    .action-buttons {
        background: #2d3748;
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    /* Permitir scroll vertical */
    body.sidebar-mini .content-wrapper {
        overflow-y: auto !important;
    }

    /* Header mejorado */
    .itinerary-header {
        background: linear-gradient(135deg, #38b2ac 0%, #319795 100%);
        color: white;
        padding: 2rem;
        border-radius: 0.5rem;
        margin-bottom: 2rem;
    }

    .itinerary-header h1 {
        margin: 0;
        font-size: 1.75rem;
        font-weight: 600;
    }

    .itinerary-header p {
        margin: 0.5rem 0 0 0;
        opacity: 0.9;
    }

    .itinerary-header .btn-secondary {
        border-color: rgba(255, 255, 255, 0.5);
        background: rgba(74, 85, 104, 0.9);
        color: white;
        font-weight: 600;
    }

    .itinerary-header .btn-secondary:hover {
        background: rgba(90, 103, 120, 1);
        border-color: white;
        color: white;
    }

    /* Tarjetas principales */
    .itinerary-card {
        border: none;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        border-radius: 0.5rem;
        overflow: hidden;
        background: #2d3748;
    }

    .itinerary-card .card-header {
        background: linear-gradient(135deg, #38b2ac 0%, #319795 100%);
        color: white;
        border: none;
        padding: 1rem;
    }

    .itinerary-card .card-header h3 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 600;
    }

    .itinerary-card .card-body {
        background: #2d3748;
        color: #cbd5e0;
        padding: 1.25rem;
    }

    /* Tarjeta interna principal */
    .inner-card {
        background: #3a4556;
        border: 1px solid #4a5568;
        border-radius: 0.375rem;
        overflow: hidden;
    }

    .inner-card .card-header {
        background: #434d5f;
        border-bottom: 1px solid #4a5568;
        padding: 0.75rem 1rem;
    }

    .inner-card .card-header h3 {
        color: #e2e8f0;
        margin: 0;
        font-size: 1rem;
    }

    .inner-card .card-body {
        background: #3a4556;
        padding: 1.25rem;
    }

    /* Card de creación nuevo */
    .card-success {
        background: #2d3748;
        border: 1px solid #48bb78;
        box-shadow: 0 0 0 1px rgba(72, 187, 120, 0.1);
    }

    .card-success .card-header {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        color: white;
        border: none;
    }

    .card-success .card-body {
        background: #2d3748;
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
        border-color: #38b2ac;
        color: #e2e8f0;
        box-shadow: 0 0 0 0.2rem rgba(56, 178, 172, 0.25);
    }

    select.form-control option {
        background: #2d3748;
        color: #e2e8f0;
    }

    label {
        color: #e2e8f0;
        font-weight: 600;
    }

    .form-text.text-muted,
    small.text-muted {
        color: #a0aec0 !important;
    }

    /* Alert info para preview */
    .alert-info {
        background: rgba(56, 178, 172, 0.15);
        border: 1px solid rgba(56, 178, 172, 0.3);
        color: #81e6d9;
    }

    .alert-info strong {
        color: #b2f5ea;
    }

    /* Alert de success y danger */
    .alert-success {
        background: rgba(72, 187, 120, 0.15);
        border: 1px solid rgba(72, 187, 120, 0.3);
        color: #9ae6b4;
    }

    .alert-danger {
        background: rgba(245, 101, 101, 0.15);
        border: 1px solid rgba(245, 101, 101, 0.3);
        color: #fc8181;
    }

    /* Lista de preview */
    .list-group-item {
        background: #3a4556;
        border: 1px solid #4a5568;
        color: #cbd5e0;
    }

    .list-group-item strong {
        color: #e2e8f0;
    }

    /* Pool de items existentes */
    .pool-container {
        background: #2d3748;
        border: 1px solid #4a5568;
        border-radius: 0.375rem;
        padding: 1rem;
        max-height: 260px;
        overflow-y: auto;
    }

    .pool-container::-webkit-scrollbar {
        width: 8px;
    }

    .pool-container::-webkit-scrollbar-track {
        background: #1a202c;
        border-radius: 4px;
    }

    .pool-container::-webkit-scrollbar-thumb {
        background: #4a5568;
        border-radius: 4px;
    }

    .pool-container::-webkit-scrollbar-thumb:hover {
        background: #38b2ac;
    }

    .form-check {
        margin-bottom: 0.75rem;
    }

    .form-check-input {
        background-color: #3a4556;
        border-color: #4a5568;
    }

    .form-check-input:checked {
        background-color: #38b2ac;
        border-color: #38b2ac;
    }

    .form-check-input:focus {
        box-shadow: 0 0 0 0.2rem rgba(56, 178, 172, 0.25);
    }

    .form-check-label {
        color: #cbd5e0;
        cursor: pointer;
    }

    .form-check-label strong {
        color: #e2e8f0;
    }

    /* Items del itinerario (cards drag & drop) */
    #itinerary-items-container {
        background: #2d3748;
        border: 2px dashed #4a5568;
        border-radius: 0.375rem;
        padding: 0.75rem;
        min-height: 60px;
    }

    .itinerary-item {
        background: linear-gradient(135deg, #434d5f 0%, #3a4556 100%);
        border: 1px solid #4a5568;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        border-radius: 0.375rem;
        margin-bottom: 0.75rem;
        transition: all 0.2s;
    }

    .itinerary-item:hover {
        border-color: #38b2ac;
        box-shadow: 0 4px 8px rgba(56, 178, 172, 0.2);
    }

    .itinerary-item .card-body {
        background: transparent;
        padding: 0.75rem;
    }

    .itinerary-item strong {
        color: #81e6d9;
    }

    .itinerary-item .item-title-display {
        color: #e2e8f0;
        margin-bottom: 0.5rem;
    }

    .itinerary-item .item-description-display {
        color: #a0aec0;
        font-size: 0.85rem;
    }

    .drag-handle {
        color: #718096;
        cursor: grab;
    }

    .drag-handle:active {
        cursor: grabbing;
    }

    .itinerary-item .btn-outline-secondary {
        border-color: #4a5568;
        color: #a0aec0;
    }

    .itinerary-item .btn-outline-secondary:hover {
        background: #38b2ac;
        border-color: #38b2ac;
        color: white;
    }

    .itinerary-item .btn-outline-danger {
        border-color: #4a5568;
        color: #fc8181;
    }

    .itinerary-item .btn-outline-danger:hover {
        background: #f56565;
        border-color: #f56565;
        color: white;
    }

    /* Cards laterales de info */
    .card-info {
        border: none;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        border-radius: 0.5rem;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .card-info .card-header {
        background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
        color: white;
        border: none;
        padding: 1rem;
    }

    .card-info .card-header h3 {
        margin: 0;
        font-size: 1rem;
        font-weight: 600;
    }

    .card-info .card-body {
        background: #2d3748;
        color: #cbd5e0;
        padding: 1.25rem;
    }

    .card-info h5,
    .card-info h6 {
        color: #e2e8f0;
    }

    .card-info hr {
        border-color: rgba(255, 255, 255, 0.1);
    }

    .card-info ul {
        color: #cbd5e0;
    }

    /* Card secundario */
    .card-secondary {
        background: #2d3748;
        border: 1px solid #4a5568;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
    }

    .card-secondary .card-header {
        background: #3a4556;
        border-bottom: 1px solid #4a5568;
        color: #e2e8f0;
    }

    .card-secondary .card-body {
        background: #2d3748;
        color: #cbd5e0;
    }

    /* Footer de navegación */
    .navigation-footer {
        background: #2d3748;
        border: none;
        box-shadow: 0 -2px 8px rgba(0, 0, 0, 0.15);
        padding: 1.25rem;
        border-radius: 0.5rem;
    }

    /* Estilos de botones */
    .btn-primary {
        background: linear-gradient(135deg, #38b2ac 0%, #319795 100%);
        border: none;
        color: white;
        font-weight: 600;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #319795 0%, #2c7a7b 100%);
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
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
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
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
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
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
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
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
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
    }

    /* Modales */
    .modal-content {
        background: #2d3748;
        border: 1px solid #4a5568;
    }

    .modal-header {
        background: #3a4556;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
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

    .modal-footer {
        background: #3a4556;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    /* Validación */
    .is-invalid {
        border-color: #f56565 !important;
    }

    .invalid-feedback {
        color: #fc8181;
        display: block;
    }

    /* Responsive */
    @media (max-width: 991.98px) {
        .itinerary-header {
            padding: 1.5rem;
        }

        .itinerary-header h1 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .itinerary-header .d-flex {
            flex-direction: column;
            align-items: flex-start !important;
        }

        .itinerary-header .btn {
            margin-top: 0.75rem;
        }

        .card-info {
            margin-top: 1.5rem;
        }
    }

    @media (max-width: 767.98px) {
        .pool-container {
            max-height: 200px;
        }

        #itinerary-items-container {
            min-height: 80px;
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
@php
    $currentLocale = app()->getLocale();

    // Construimos el JSON solo desde translations
    $itineraryJson = collect($itineraries ?? [])
        ->keyBy('itinerary_id')
        ->map(function ($it) use ($currentLocale) {
            $tr = $it->translations->firstWhere('locale', $currentLocale)
                ?? $it->translations->first();

            return [
                'id'          => $it->itinerary_id,
                'name'        => $tr->name ?? null,
                'description' => $tr->description ?? null,
                'items'       => $it->items->map(function ($item) use ($currentLocale) {
                    $itr = $item->translations->firstWhere('locale', $currentLocale)
                        ?? $item->translations->first();

                    return [
                        'id'          => $item->item_id,
                        'title'       => $itr->title ?? null,
                        'description' => $itr->description ?? null,
                    ];
                })->values()->toArray(),
            ];
        })->toArray();

    $oldItems = old('items', []);
@endphp

<div class="container-fluid">
    @include('admin.tours.wizard.partials.stepper')

    {{-- Header mejorado --}}
    <div class="itinerary-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1>
                    <i class="fas fa-route"></i>
                    {{ __('m_tours.tour.wizard.steps.itinerary') }}
                </h1>
                <p class="mb-0">{{ $tour->name }}</p>
            </div>
        </div>
    </div>

    <form id="tour-itinerary-form" method="POST" action="{{ route('admin.tours.wizard.store.itinerary', $tour) }}">
        @csrf

        {{-- Botones de acción --}}
        <div class="action-buttons">
            <a href="{{ route('admin.tours.schedule.index') }}"
               class="btn btn-primary btn-sm"
               title="{{ __('m_tours.common.crud_go_to_index', ['element' => __('m_tours.itinerary.plural')]) }}">
                <i class="fas fa-list"></i>
                <span class="d-none d-md-inline">
                    {{ __('m_tours.common.crud_go_to_index', ['element' => __('m_tours.itinerary.plural')]) }}
                </span>
            </a>
        </div>

        <div class="card itinerary-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-route"></i> {{ __('m_tours.tour.fields.itinerary') }}
                </h3>
            </div>

            <div class="card-body">
                <div class="row">
                    {{-- COLUMNA PRINCIPAL --}}
                    <div class="col-md-8">
                        <div class="card inner-card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-route"></i> {{ __('m_tours.tour.fields.itinerary') }}
                                </h3>
                            </div>

                            <div class="card-body">
                                {{-- SELECT ITINERARIO EXISTENTE / NUEVO --}}
                                <div class="form-group">
                                    <label for="select-itinerary">{{ __('m_tours.tour.fields.itinerary') }}</label>
                                    <select name="itinerary_id"
                                            id="select-itinerary"
                                            class="form-control @error('itinerary_id') is-invalid @enderror">
                                        <option value="">
                                            {{ __('m_tours.itinerary.ui.new_itinerary') }}
                                        </option>
                                        @foreach($itineraries ?? [] as $itinerary)
                                            @php
                                                $itTr   = $itinerary->translations->firstWhere('locale', $currentLocale)
                                                            ?? $itinerary->translations->first();
                                                $itName = $itTr->name
                                                    ?? __('m_tours.itinerary.fields.name') . ' #' . $itinerary->itinerary_id;
                                            @endphp
                                            <option value="{{ $itinerary->itinerary_id }}"
                                                {{ old('itinerary_id', $tour->itinerary_id ?? '') == $itinerary->itinerary_id ? 'selected' : '' }}>
                                                {{ $itName }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('itinerary_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">
                                        {{ __('m_tours.itinerary.ui.select_or_create_hint') }}
                                    </small>
                                </div>

                                {{-- PREVIEW ITINERARIO EXISTENTE --}}
                                <div id="view-itinerary-items-create" style="display:none;" class="mt-3">
                                    <div class="alert alert-info">
                                        <strong>{{ __('m_tours.itinerary.fields.description') }}:</strong>
                                        <div id="selected-itinerary-description" class="mt-2"></div>
                                    </div>
                                    <h6 class="mb-2">{{ __('m_tours.itinerary_item.ui.list_title') }}</h6>
                                    <ul class="list-group"></ul>
                                </div>

                                {{-- SECCIÓN CREAR NUEVO ITINERARIO --}}
                                <div id="new-itinerary-section" style="display:none;" class="mt-4">
                                    <div class="card card-success">
                                        <div class="card-header">
                                            <h4 class="card-title mb-0">
                                                <i class="fas fa-plus"></i> {{ __('m_tours.itinerary.ui.create_title') }}
                                            </h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="new_itinerary_name">
                                                    {{ __('m_tours.itinerary.fields.name') }} *
                                                </label>
                                                <input type="text"
                                                       name="new_itinerary_name"
                                                       id="new_itinerary_name"
                                                       class="form-control @error('new_itinerary_name') is-invalid @enderror"
                                                       value="{{ old('new_itinerary_name') }}"
                                                       placeholder="{{ __('m_tours.itinerary.ui.create_title') }}">
                                                @error('new_itinerary_name')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                                <small class="form-text text-muted">
                                                    {{ __('m_tours.itinerary.fields.name') }}
                                                </small>
                                            </div>

                                            <div class="form-group">
                                                <label for="new_itinerary_description">
                                                    {{ __('m_tours.itinerary.fields.description') }}
                                                </label>
                                                <textarea name="new_itinerary_description"
                                                          id="new_itinerary_description"
                                                          class="form-control @error('new_itinerary_description') is-invalid @enderror"
                                                          rows="3"
                                                          placeholder="{{ __('m_tours.itinerary.fields.description_optional') }}">{{ old('new_itinerary_description') }}</textarea>
                                                @error('new_itinerary_description')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <hr>

                                            <div class="row">
                                                {{-- Pool de items existentes --}}
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>
                                                            <i class="fas fa-list"></i> {{ __('m_tours.itinerary_item.ui.list_title') }}
                                                        </label>
                                                        <p class="text-muted small mb-2">
                                                            {{ __('m_tours.itinerary_item.ui.pool_hint') }}
                                                        </p>

                                                        <div class="pool-container">
                                                            @php
                                                                $availableItems = \App\Models\ItineraryItem::where('is_active', true)
                                                                    ->with('translations')
                                                                    ->get()
                                                                    ->sortBy(function ($item) use ($currentLocale) {
                                                                        $tr = $item->translations->firstWhere('locale', $currentLocale)
                                                                            ?? $item->translations->first();
                                                                        return $tr->title ?? '';
                                                                    });
                                                            @endphp

                                                            @forelse($availableItems as $item)
                                                                @php
                                                                    $tr   = $item->translations->firstWhere('locale', $currentLocale)
                                                                                ?? $item->translations->first();
                                                                    $title = $tr->title ?? '';
                                                                    $desc  = $tr->description ?? '';
                                                                @endphp
                                                                <div class="form-check">
                                                                    <input type="checkbox"
                                                                           class="form-check-input existing-item-checkbox"
                                                                           id="existing_item_{{ $item->item_id }}"
                                                                           value="{{ $item->item_id }}"
                                                                           data-title="{{ $title }}"
                                                                           data-description="{{ $desc }}">
                                                                    <label class="form-check-label" for="existing_item_{{ $item->item_id }}">
                                                                        <strong>{{ $title }}</strong>
                                                                        @if($desc)
                                                                            <br>
                                                                            <small class="text-muted">
                                                                                {{ \Illuminate\Support\Str::limit($desc, 80) }}
                                                                            </small>
                                                                        @endif
                                                                    </label>
                                                                </div>
                                                            @empty
                                                                <p class="text-muted mb-0">
                                                                    {{ __('m_tours.tour.ui.none.itinerary_items') }}
                                                                </p>
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Items del nuevo itinerario --}}
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <div>
                                                                <label class="mb-0">
                                                                    <i class="fas fa-stream"></i> {{ __('m_tours.itinerary.items') }}
                                                                </label>
                                                                <p class="text-muted small mb-0">
                                                                    {{ __('m_tours.itinerary_item.ui.drag_to_order') }}
                                                                </p>
                                                            </div>

                                                            <button type="button"
                                                                    class="btn btn-success btn-sm"
                                                                    id="btn-add-new-itinerary-item">
                                                                <i class="fas fa-plus"></i>
                                                                <span class="d-none d-md-inline">{{ __('m_tours.itinerary.ui.add_item') ?? 'Agregar' }}</span>
                                                            </button>
                                                        </div>

                                                        <div id="itinerary-items-container">
                                                            @foreach($oldItems as $index => $item)
                                                                @php
                                                                    $title = $item['title'] ?? '';
                                                                    $desc  = $item['description'] ?? '';
                                                                @endphp
                                                                <div class="card itinerary-item">
                                                                    <div class="card-body">
                                                                        <div class="d-flex align-items-start mb-2">
                                                                            <span class="text-muted mr-2 drag-handle">
                                                                                <i class="fas fa-grip-vertical"></i>
                                                                            </span>
                                                                            <strong>
                                                                                {{ __('m_tours.itinerary.item') }}
                                                                                #<span class="item-number">{{ $loop->iteration }}</span>
                                                                            </strong>

                                                                            <button type="button"
                                                                                    class="btn btn-sm btn-outline-secondary ml-2 edit-item"
                                                                                    aria-label="edit item">
                                                                                <i class="fas fa-edit"></i>
                                                                            </button>

                                                                            <button type="button"
                                                                                    class="btn btn-sm btn-outline-danger ml-auto remove-item"
                                                                                    aria-label="remove item">
                                                                                <i class="fas fa-times"></i>
                                                                            </button>
                                                                        </div>

                                                                        <h6 class="item-title-display">{{ $title }}</h6>
                                                                        @if($desc)
                                                                            <p class="mb-0 item-description-display">{{ $desc }}</p>
                                                                        @else
                                                                            <p class="mb-0 item-description-display d-none"></p>
                                                                        @endif

                                                                        <input type="hidden"
                                                                               name="items[{{ $index }}][title]"
                                                                               class="item-title-input"
                                                                               value="{{ $title }}">
                                                                        <input type="hidden"
                                                                               name="items[{{ $index }}][description]"
                                                                               class="item-description-input"
                                                                               value="{{ $desc }}">
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>

                                                    @error('items')
                                                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                                                    @enderror
                                                    @error('items.*.title')
                                                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> {{-- #new-itinerary-section --}}
                            </div>
                        </div>
                    </div>

                    {{-- COLUMNA LATERAL --}}
                    <div class="col-md-4">
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-info-circle"></i> {{ __('m_tours.tour.ui.confirm_title') }}
                                </h3>
                            </div>
                            <div class="card-body">
                                <h5>{{ __('m_tours.itinerary.ui.assign') }}</h5>
                                <ul>
                                    <li>
                                        <strong>{{ __('m_tours.itinerary.ui.assign') }}:</strong>
                                        {{ __('m_tours.itinerary.ui.drag_hint') }}
                                    </li>
                                    <li>
                                        <strong>{{ __('m_tours.itinerary.ui.create_button') }}</strong>:
                                        {{ __('m_tours.itinerary.ui.create_title') }}
                                    </li>
                                </ul>
                                <hr>
                                <h5>{{ __('m_tours.itinerary.ui.create_title') }}</h5>
                                <ul class="small mb-0">
                                    <li>
                                        <strong>{{ __('m_tours.itinerary_item.ui.list_title') }}</strong>:
                                        {{ __('m_tours.itinerary.ui.drag_hint') }}
                                    </li>
                                    <li>
                                        <strong>{{ __('m_tours.itinerary_item.ui.register_item') }}</strong>:
                                        {{ __('m_tours.itinerary.ui.create_title') }}
                                    </li>
                                    <li>
                                        <strong>{{ __('m_tours.common.success_title') }}</strong>:
                                        {{ __('m_tours.itinerary.ui.save_changes') }}
                                    </li>
                                </ul>
                            </div>
                        </div>

                        @if($tour ?? false)
                            <div class="card card-secondary">
                                <div class="card-header">
                                    <h3 class="card-title">{{ __('m_tours.tour.fields.itinerary') }}</h3>
                                </div>
                                <div class="card-body">
                                    @if($tour->itinerary)
                                        @php
                                            $it   = $tour->itinerary;
                                            $itTr = $it->translations->firstWhere('locale', $currentLocale)
                                                        ?? $it->translations->first();
                                            $itName = $itTr->name ?? __('m_tours.itinerary.fields.name');
                                            $itDesc = $itTr->description ?? null;
                                        @endphp

                                        <h6>{{ $itName }}</h6>

                                        @if($itDesc)
                                            <p class="text-muted small">{{ $itDesc }}</p>
                                        @endif

                                        @if($it->items->isNotEmpty())
                                            <hr>
                                            <ol class="mb-0">
                                                @foreach($it->items as $item)
                                                    @php
                                                        $itr   = $item->translations->firstWhere('locale', $currentLocale)
                                                                    ?? $item->translations->first();
                                                        $title = $itr->title ?? '';
                                                    @endphp
                                                    <li>{{ $title }}</li>
                                                @endforeach
                                            </ol>
                                        @endif
                                    @else
                                        <p class="text-muted mb-0">
                                            {{ __('m_tours.tour.ui.none.itinerary') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div> {{-- col-md-4 --}}
                </div>
            </div>

            {{-- FOOTER NAVEGACIÓN + CANCELAR DRAFT --}}
            <div class="card-footer navigation-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('admin.tours.wizard.step', ['tour' => $tour, 'step' => 1]) }}"
                       class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        {{ __('m_tours.common.previous') }}
                    </a>

                    <div class="d-flex">
                        @if($tour->is_draft)
                            <button type="button"
                                    class="btn btn-danger"
                                    onclick="if (confirm('{{ __('m_tours.tour.wizard.confirm_cancel') }}')) { document.getElementById('delete-draft-form').submit(); }">
                                <i class="fas fa-trash"></i>
                                <span class="d-none d-md-inline">{{ __('m_tours.common.cancel') }}</span>
                            </button>
                        @endif

                        <button type="submit" class="btn btn-primary ml-2">
                            {{ __('m_tours.tour.wizard.save_and_continue') }}
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>

        </div> {{-- .card --}}
    </form>

    {{-- FORM OCULTO PARA ELIMINAR DRAFT --}}
    @if($tour->is_draft)
        <form id="delete-draft-form"
              action="{{ route('admin.tours.wizard.delete-draft', $tour) }}"
              method="POST"
              style="display:none;">
            @csrf
            @method('DELETE')
        </form>
    @endif
</div>

{{-- Modal: Crear nuevo item --}}
<div class="modal fade" id="modalCreateItineraryItem" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content"
              id="formCreateItineraryItem"
              method="POST"
              action="{{ route('admin.tours.wizard.quick.itinerary-item') }}">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus-circle"></i>
                    {{ __('m_tours.itinerary.ui.quick_create_item') ?? 'Nuevo item de itinerario' }}
                </h5>
                <button type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="{{ __('m_tours.common.close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="createItemModalErrors" class="alert alert-danger d-none"></div>

                <div class="form-group">
                    <label for="quick_item_title">
                        {{ __('m_tours.itinerary.fields.item_title') }}
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="quick_item_title" name="title" class="form-control" required>
                </div>

                <div class="form-group mb-0">
                    <label for="quick_item_description">
                        {{ __('m_tours.itinerary.fields.item_description') }}
                    </label>
                    <textarea id="quick_item_description" name="description" class="form-control" rows="3"></textarea>
                </div>

                <small class="form-text text-muted mt-2">
                    {{ __('m_tours.itinerary.ui.quick_create_item_help') ?? 'El item se creará en el módulo global y se agregará a este itinerario.' }}
                </small>
            </div>
            <div class="modal-footer">
                <button type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal">
                    {{ __('m_tours.common.cancel') }}
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    {{ __('m_tours.common.add') ?? 'Agregar' }}
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Editar item --}}
<div class="modal fade" id="modalEditItineraryItem" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit"></i>
                    {{ __('m_tours.itinerary_item.ui.edit_item') ?? 'Editar item de itinerario' }}
                </h5>
                <button type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="{{ __('m_tours.common.close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="editItemModalErrors" class="alert alert-danger d-none"></div>

                <div class="form-group">
                    <label for="edit_item_title">
                        {{ __('m_tours.itinerary.fields.item_title') }}
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="edit_item_title" class="form-control" required>
                </div>

                <div class="form-group mb-0">
                    <label for="edit_item_description">
                        {{ __('m_tours.itinerary.fields.item_description') }}
                    </label>
                    <textarea id="edit_item_description" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal">
                    {{ __('m_tours.common.cancel') }}
                </button>
                <button type="button"
                        class="btn btn-primary"
                        id="btn-save-edit-item">
                    <i class="fas fa-save"></i>
                    {{ __('m_tours.common.save') }}
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ITINERARY_DATA = @json($itineraryJson ?? []);

    const translations = {
        cannotDelete:        @json(__('m_tours.itinerary.ui.cannot_delete_item') ?? 'No se puede eliminar'),
        minOneItem:         @json(__('m_tours.itinerary.ui.min_one_item') ?? 'Debe existir al menos un item.'),
        confirmDeleteTitle: @json(__('m_tours.common.confirm_delete_title') ?? '¿Eliminar este item?'),
        confirmDeleteText:  @json(__('m_tours.common.confirm_delete_text') ?? 'Esta acción no se puede deshacer'),
        confirmDeleteButton:@json(__('m_tours.common.delete') ?? 'Sí, eliminar'),
        cancelButton:       @json(__('m_tours.common.cancel') ?? 'Cancelar'),
        titleRequired:      @json(__('validation.required', ['attribute' => __('m_tours.itinerary.fields.item_title')]) ?? 'El título es obligatorio'),
        saving:             @json(__('m_tours.common.saving') ?? 'Guardando...'),
        itemAdded:          @json(__('m_tours.itinerary.ui.item_added') ?? 'Item agregado'),
        itemAddedSuccess:   @json(__('m_tours.itinerary.ui.item_added_success') ?? 'El item se agregó correctamente al itinerario'),
        itineraryNameRequired: @json(__('m_tours.itinerary.validation.name_required') ?? 'El nombre del itinerario es obligatorio'),
        mustAddItems:          @json(__('m_tours.itinerary.validation.must_add_items') ?? 'Debes agregar al menos un item al nuevo itinerario'),
        validationTitle:       @json(__('m_tours.itinerary.validation.title') ?? 'Validación de Itinerario'),
        errorCreatingItem:     @json(__('m_tours.itinerary.ui.error_creating_item') ?? 'Error de validación al crear el item.'),
        networkError:          @json(__('m_tours.common.network_error') ?? 'Error de red'),
    };

    function openModal(modalId) {
        const modalEl = document.getElementById(modalId);
        if (!modalEl) return;
        if (window.bootstrap && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        } else if (window.jQuery && typeof jQuery(modalEl).modal === 'function') {
            jQuery(modalEl).modal('show');
        } else {
            modalEl.classList.add('show');
            modalEl.style.display = 'block';
        }
    }

    function closeModalAndCleanup(modalId) {
        const modalEl = document.getElementById(modalId);
        if (modalEl) {
            if (window.bootstrap && bootstrap.Modal) {
                (bootstrap.Modal.getInstance(modalEl) || bootstrap.Modal.getOrCreateInstance(modalEl)).hide();
            } else if (window.jQuery && typeof jQuery(modalEl).modal === 'function') {
                jQuery(modalEl).modal('hide');
            } else {
                modalEl.classList.remove('show');
                modalEl.style.display = 'none';
            }
        }
        document.body.classList.remove('modal-open');
        document.querySelectorAll('.modal-backdrop').forEach(bd => bd.parentNode && bd.parentNode.removeChild(bd));
    }

    const itinerarySelect = document.getElementById('select-itinerary');
    const newSection      = document.getElementById('new-itinerary-section');
    const viewSection     = document.getElementById('view-itinerary-items-create');

    function renderItineraryPreview(itineraryId) {
        if (!viewSection) return;
        const descEl = document.getElementById('selected-itinerary-description');
        const listEl = viewSection.querySelector('ul');
        const data   = ITINERARY_DATA[itineraryId] || null;

        if (!data || (!data.description && (!data.items || !data.items.length))) {
            viewSection.style.display = 'none';
            if (descEl) descEl.textContent = '';
            if (listEl) listEl.innerHTML = '';
            return;
        }

        viewSection.style.display = 'block';
        if (descEl) {
            descEl.textContent = data.description || @json(__('m_tours.itinerary.fields.description_optional'));
        }
        if (listEl) {
            if (Array.isArray(data.items) && data.items.length) {
                listEl.innerHTML = data.items.map((item, idx) => `
                    <li class="list-group-item">
                        <strong>#${idx + 1} - ${item.title || @json(__('m_tours.itinerary_item.fields.title'))}</strong>
                        ${item.description ? `<br><small class="text-muted">${item.description}</small>` : ''}
                    </li>
                `).join('');
            } else {
                listEl.innerHTML = '<li class="list-group-item text-muted">{{ __('m_tours.tour.ui.none.itinerary_items') }}</li>';
            }
        }
    }

    if (itinerarySelect) {
        itinerarySelect.addEventListener('change', function () {
            const value = this.value;
            if (!value) {
                if (newSection) newSection.style.display = 'block';
                renderItineraryPreview(null);
            } else {
                if (newSection) newSection.style.display = 'none';
                renderItineraryPreview(value);
            }
        });

        // Disparar al cargar para respetar old('itinerary_id')
        itinerarySelect.dispatchEvent(new Event('change'));
    }

    const container = document.getElementById('itinerary-items-container');
    const addNewBtn = document.getElementById('btn-add-new-itinerary-item');

    function updateItemNumbersAndNames() {
        if (!container) return;
        const items = container.querySelectorAll('.itinerary-item');
        items.forEach((item, index) => {
            const numSpan = item.querySelector('.item-number');
            if (numSpan) numSpan.textContent = index + 1;
            const titleInput = item.querySelector('.item-title-input');
            const descInput  = item.querySelector('.item-description-input');
            if (titleInput) titleInput.name = `items[${index}][title]`;
            if (descInput)  descInput.name  = `items[${index}][description]`;
        });
    }

    function createItemCard(title = '', description = '', sourceKey = null) {
        if (!container) return;
        const total   = container.querySelectorAll('.itinerary-item').length;
        const wrapper = document.createElement('div');
        wrapper.className = 'card itinerary-item';
        if (sourceKey) wrapper.dataset.sourceKey = sourceKey;
        const safeTitle       = title ? String(title) : '';
        const safeDescription = description ? String(description) : '';

        wrapper.innerHTML = `
            <div class="card-body">
                <div class="d-flex align-items-start mb-2">
                    <span class="text-muted mr-2 drag-handle">
                        <i class="fas fa-grip-vertical"></i>
                    </span>
                    <strong>{{ __('m_tours.itinerary.item') }} #<span class="item-number">${total + 1}</span></strong>
                    <button type="button" class="btn btn-sm btn-outline-secondary ml-2 edit-item" aria-label="edit item">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger ml-auto remove-item" aria-label="remove item">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <h6 class="item-title-display">${safeTitle}</h6>
                <p class="mb-0 item-description-display ${safeDescription ? '' : 'd-none'}">${safeDescription}</p>
                <input type="hidden" name="items[${total}][title]" class="item-title-input" value="${safeTitle}">
                <input type="hidden" name="items[${total}][description]" class="item-description-input" value="${safeDescription}">
            </div>
        `;
        container.appendChild(wrapper);
        updateItemNumbersAndNames();
    }

    if (window.Sortable && container) {
        Sortable.create(container, {
            handle: '.drag-handle',
            animation: 150,
            onSort: updateItemNumbersAndNames
        });
    }

    if (addNewBtn) {
        addNewBtn.addEventListener('click', function () {
            const quickTitle       = document.getElementById('quick_item_title');
            const quickDescription = document.getElementById('quick_item_description');
            const createErrors     = document.getElementById('createItemModalErrors');

            if (createErrors) {
                createErrors.classList.add('d-none');
                createErrors.innerHTML = '';
            }
            if (quickTitle) quickTitle.value = '';
            if (quickDescription) quickDescription.value = '';

            openModal('modalCreateItineraryItem');
        });
    }

    if (container) {
        container.addEventListener('click', function (e) {
            const removeBtn = e.target.closest('.remove-item');
            const editBtn   = e.target.closest('.edit-item');

            if (removeBtn) {
                const items = container.querySelectorAll('.itinerary-item');
                if (items.length <= 1) {
                    Swal.fire({
                        icon: 'warning',
                        title: translations.cannotDelete,
                        text: translations.minOneItem,
                        confirmButtonColor: '#38b2ac',
                    });
                    return;
                }
                const card = removeBtn.closest('.itinerary-item');
                if (!card) return;

                Swal.fire({
                    title: translations.confirmDeleteTitle,
                    text: translations.confirmDeleteText,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#f56565',
                    cancelButtonColor: '#4a5568',
                    confirmButtonText: translations.confirmDeleteButton,
                    cancelButtonText: translations.cancelButton
                }).then((result) => {
                    if (result.isConfirmed) {
                        card.remove();
                        updateItemNumbersAndNames();
                    }
                });
                return;
            }

            if (editBtn) {
                const card = editBtn.closest('.itinerary-item');
                if (!card) return;
                startEditItem(card);
            }
        });
    }

    updateItemNumbersAndNames();

    document.querySelectorAll('.existing-item-checkbox').forEach(cb => {
        cb.addEventListener('change', function () {
            if (!container) return;
            const itemId    = this.value;
            const title     = this.dataset.title || '';
            const desc      = this.dataset.description || '';
            const sourceKey = 'existing_item_' + itemId;
            const existingCard = container.querySelector(`.itinerary-item[data-source-key="${sourceKey}"]`);

            if (this.checked) {
                if (!existingCard) createItemCard(title, desc, sourceKey);
            } else {
                if (existingCard) {
                    existingCard.remove();
                    updateItemNumbersAndNames();
                }
            }
        });
    });

    const formCreateItineraryItem = document.getElementById('formCreateItineraryItem');
    const quickTitle              = document.getElementById('quick_item_title');
    const quickDescription        = document.getElementById('quick_item_description');
    const createItemModalErrors   = document.getElementById('createItemModalErrors');

    if (formCreateItineraryItem && quickTitle && quickDescription) {
        formCreateItineraryItem.addEventListener('submit', function (e) {
            e.preventDefault();

            if (!quickTitle.value.trim()) {
                if (createItemModalErrors) {
                    createItemModalErrors.classList.remove('d-none');
                    createItemModalErrors.innerHTML = '<div>' + translations.titleRequired + '</div>';
                }
                quickTitle.focus();
                return;
            }

            const url      = formCreateItineraryItem.action;
            const formData = new FormData(formCreateItineraryItem);

            if (createItemModalErrors) {
                createItemModalErrors.classList.add('d-none');
                createItemModalErrors.innerHTML = '';
            }

            const submitBtn    = formCreateItineraryItem.querySelector('button[type="submit"]');
            const originalHtml = submitBtn ? submitBtn.innerHTML : '';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ' + translations.saving;
            }

            fetch(url, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(async (response) => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHtml;
                }

                if (response.ok) return response.json();
                if (response.status === 422) {
                    const data = await response.json();
                    if (createItemModalErrors) {
                        createItemModalErrors.classList.remove('d-none');
                        if (data.errors) {
                            const msgs = Object.values(data.errors).flat();
                            createItemModalErrors.innerHTML = msgs.map(msg => '<div>' + msg + '</div>').join('');
                        } else {
                            createItemModalErrors.innerHTML = '<div>' + translations.errorCreatingItem + '</div>';
                        }
                    }
                    return null;
                }
                const text = await response.text();
                if (createItemModalErrors) {
                    createItemModalErrors.classList.remove('d-none');
                    createItemModalErrors.innerHTML = '<div>' + text + '</div>';
                }
                return null;
            })
            .then((data) => {
                if (!data || !data.id || !data.title) return;
                createItemCard(data.title, data.description || '', null);
                quickTitle.value       = '';
                quickDescription.value = '';
                closeModalAndCleanup('modalCreateItineraryItem');

                Swal.fire({
                    icon: 'success',
                    title: translations.itemAdded,
                    text: translations.itemAddedSuccess,
                    timer: 2000,
                    showConfirmButton: false,
                });
            })
            .catch((err) => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHtml;
                }

                if (createItemModalErrors) {
                    createItemModalErrors.classList.remove('d-none');
                    createItemModalErrors.innerHTML =
                        '<div>' + (err.message || translations.networkError) + '</div>';
                }
            });
        });
    }

    let currentEditingCard         = null;
    const editTitleInput           = document.getElementById('edit_item_title');
    const editDescInput            = document.getElementById('edit_item_description');
    const editErrors               = document.getElementById('editItemModalErrors');
    const btnSaveEdit              = document.getElementById('btn-save-edit-item');

    function startEditItem(card) {
        currentEditingCard = card;
        const titleInput   = card.querySelector('.item-title-input');
        const descInput    = card.querySelector('.item-description-input');

        if (editErrors) {
            editErrors.classList.add('d-none');
            editErrors.innerHTML = '';
        }
        if (editTitleInput) editTitleInput.value = titleInput ? titleInput.value : '';
        if (editDescInput)  editDescInput.value  = descInput ? descInput.value : '';

        openModal('modalEditItineraryItem');
    }

    if (btnSaveEdit && editTitleInput && editDescInput) {
        btnSaveEdit.addEventListener('click', function () {
            if (!currentEditingCard) return;
            const newTitle = editTitleInput.value.trim();
            const newDesc  = editDescInput.value.trim();

            if (!newTitle) {
                if (editErrors) {
                    editErrors.classList.remove('d-none');
                    editErrors.innerHTML = '<div>' + translations.titleRequired + '</div>';
                }
                editTitleInput.focus();
                return;
            }

            const titleDisplay = currentEditingCard.querySelector('.item-title-display');
            const descDisplay  = currentEditingCard.querySelector('.item-description-display');
            const titleInput   = currentEditingCard.querySelector('.item-title-input');
            const descInput    = currentEditingCard.querySelector('.item-description-input');

            if (titleDisplay) titleDisplay.textContent = newTitle;
            if (titleInput)   titleInput.value         = newTitle;

            if (descDisplay) {
                if (newDesc) {
                    descDisplay.textContent = newDesc;
                    descDisplay.classList.remove('d-none');
                } else {
                    descDisplay.textContent = '';
                    descDisplay.classList.add('d-none');
                }
            }
            if (descInput) descInput.value = newDesc;

            closeModalAndCleanup('modalEditItineraryItem');
            currentEditingCard = null;
        });
    }

    // VALIDACIÓN DEL FORMULARIO PRINCIPAL
    const mainForm              = document.getElementById('tour-itinerary-form');
    const newItineraryNameInput = document.getElementById('new_itinerary_name');

    if (mainForm) {
        mainForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const selectedItineraryId = itinerarySelect ? itinerarySelect.value : '';
            const isCreatingNew       = !selectedItineraryId;

            let errors = [];

            if (isCreatingNew) {
                const newItineraryName = newItineraryNameInput
                    ? newItineraryNameInput.value.trim()
                    : '';

                if (!newItineraryName) {
                    errors.push(translations.itineraryNameRequired);
                    if (newItineraryNameInput) {
                        newItineraryNameInput.classList.add('is-invalid');
                        newItineraryNameInput.focus();
                    }
                } else if (newItineraryNameInput) {
                    newItineraryNameInput.classList.remove('is-invalid');
                }

                const itemsCount = container
                    ? container.querySelectorAll('.itinerary-item').length
                    : 0;

                if (itemsCount === 0) {
                    errors.push(translations.mustAddItems);
                    if (container) {
                        container.style.border = '2px solid #f56565';
                        setTimeout(() => {
                            container.style.border = '2px dashed #4a5568';
                        }, 3000);
                    }
                }
            }

            if (errors.length > 0) {
                Swal.fire({
                    icon: 'error',
                    title: translations.validationTitle,
                    html: '<ul style="text-align: left;">' +
                        errors.map(err => '<li>' + err + '</li>').join('') +
                        '</ul>',
                    confirmButtonColor: '#38b2ac',
                });
                return false;
            }

            const submitBtn = mainForm.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled  = true;
                submitBtn.innerHTML =
                    '<i class="fas fa-spinner fa-spin"></i> ' + translations.saving;
            }

            mainForm.submit();
        });
    }

    if (newItineraryNameInput) {
        newItineraryNameInput.addEventListener('input', function () {
            this.classList.remove('is-invalid');
        });
    }
});
</script>
@endpush
