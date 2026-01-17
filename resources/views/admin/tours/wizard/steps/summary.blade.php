{{-- resources/views/admin/tours/wizard/steps/summary.blade.php --}}

@extends('adminlte::page')

@section('title', __('m_tours.tour.wizard.steps.summary'))

@push('css')
<style>
    /* Mejoras de diseño general */
    .summary-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 0.5rem;
        margin-bottom: 2rem;
    }

    .preserve-newlines {
        white-space: pre-line;
    }

    .summary-header h1 {
        margin: 0;
        font-size: 1.75rem;
        font-weight: 600;
    }

    .summary-header p {
        margin: 0.5rem 0 0 0;
        opacity: 0.9;
    }

    /* Tarjetas más compactas con fondos oscuros */
    .summary-card {
        border: none;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        margin-bottom: 1.25rem;
        border-radius: 0.5rem;
        overflow: hidden;
        background: #2d3748;
    }

    .summary-card .card-header {
        padding: 0.75rem 1rem;
        font-weight: 600;
        font-size: 0.95rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        background: #3a4556;
        color: #e2e8f0;
    }

    .summary-card .card-body {
        padding: 1rem;
        background: #2d3748;
        color: #cbd5e0;
    }

    /* Grid de información */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
    }

    .info-item {
        padding: 0.75rem;
        background: #3a4556;
        border-radius: 0.375rem;
        border-left: 3px solid #667eea;
    }

    .info-item .label {
        font-size: 0.8rem;
        color: #a0aec0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
    }

    .info-item .value {
        font-size: 1rem;
        font-weight: 600;
        color: #e2e8f0;
    }

    .info-item .value code {
        background: #1a202c;
        color: #68d391;
        padding: 0.2rem 0.4rem;
        border-radius: 0.25rem;
    }

    /* Lista de items con iconos */
    .icon-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .icon-list li {
        padding: 0.5rem 0;
        display: flex;
        align-items: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        color: #cbd5e0;
    }

    .icon-list li:last-child {
        border-bottom: none;
    }

    .icon-list i {
        margin-right: 0.75rem;
        width: 20px;
        text-align: center;
    }

    /* Lista de items de itinerario (con bullets marcados) */
    .itinerary-items-list {
        list-style: none;
        padding-left: 0;
        margin-bottom: 0;
    }

    .itinerary-items-list li {
        position: relative;
        padding-left: 1.5rem;
        margin-bottom: 0.75rem;
    }

    .itinerary-items-list li::before {
        content: '•';
        position: absolute;
        left: 0;
        top: 0.15rem;
        font-size: 1.1rem;
        color: #667eea;
    }

    /* Tabla de precios mejorada */
    .pricing-table {
        margin: 0;
    }

    .pricing-table thead th {
        background: #3a4556;
        border: none;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 0.75rem;
        font-weight: 600;
        color: #e2e8f0;
    }

    .pricing-table tbody td {
        padding: 0.75rem;
        vertical-align: middle;
        background: #2d3748;
        color: #cbd5e0;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    /* Panel de acciones mejorado */
    .action-panel {
        position: sticky;
        top: 20px;
    }

    .publish-card {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        border: none;
        color: white;
        border-radius: 0.5rem;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(17, 153, 142, 0.3);
    }

    .publish-card .card-header {
        background: rgba(0, 0, 0, 0.15);
        border-bottom: 1px solid rgba(255, 255, 255, 0.15);
    }

    .publish-card .card-body {
        padding: 1.25rem;
    }

    .publish-card .alert-info {
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: white;
    }

    .publish-card hr {
        border-color: rgba(255, 255, 255, 0.2);
    }

    /* Checklist */
    .checklist-item {
        padding: 0.5rem 0;
        display: flex;
        align-items: center;
        color: #cbd5e0;
    }

    .checklist-item i {
        margin-right: 0.75rem;
        font-size: 1.1rem;
    }

    /* Badges personalizados */
    .custom-badge {
        padding: 0.35rem 0.75rem;
        border-radius: 1rem;
        font-size: 0.8rem;
        font-weight: 600;
    }

    /* Tabs para organizar contenido */
    .summary-tabs .nav-tabs {
        border-bottom: 2px solid rgba(255, 255, 255, 0.1);
        background: #3a4556;
    }

    .summary-tabs .nav-tabs .nav-link {
        border: none;
        color: #a0aec0;
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        border-bottom: 3px solid transparent;
        transition: all 0.3s;
    }

    .summary-tabs .nav-tabs .nav-link:hover {
        border-color: transparent;
        color: #667eea;
        background: rgba(102, 126, 234, 0.1);
    }

    .summary-tabs .nav-tabs .nav-link.active {
        color: #667eea;
        border-bottom-color: #667eea;
        background: #2d3748;
    }

    .summary-tabs .tab-content h5,
    .summary-tabs .tab-content h6 {
        color: #e2e8f0;
    }

    .summary-tabs .tab-content p {
        color: #a0aec0;
    }

    .summary-tabs .tab-content ol {
        color: #cbd5e0;
    }

    /* Override para textos en general */
    .text-muted {
        color: #a0aec0 !important;
    }

    .border-top {
        border-top-color: rgba(255, 255, 255, 0.1) !important;
    }

    /* Responsive */
    @media (max-width: 991.98px) {
        .action-panel {
            position: static;
            margin-top: 1.5rem;
        }

        .summary-header {
            padding: 1.5rem;
        }

        .summary-header h1 {
            font-size: 1.5rem;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 767.98px) {
        .pricing-table {
            font-size: 0.85rem;
        }

        .pricing-table thead th,
        .pricing-table tbody td {
            padding: 0.5rem;
        }

        .summary-tabs .nav-tabs .nav-link {
            padding: 0.5rem 0.75rem;
            font-size: 0.85rem;
        }
    }
</style>
@endpush

@section('content')
@php
$currentLocale = app()->getLocale();

// Traducciones del tour
$tourTr = $tour->translations->firstWhere('locale', $currentLocale)
?? $tour->translations->first();

$tourName = $tourTr->name ?? $tour->name ?? '';
$tourOverview = $tourTr->overview ?? $tour->overview ?? null;

// Traducción del tipo de tour
$tourTypeName = null;
if ($tour->tourType) {
$ttTranslations = $tour->tourType->translations ?? collect();
$ttTr = $ttTranslations->firstWhere('locale', $currentLocale)
?? $ttTranslations->first();
$tourTypeName = $ttTr->name ?? $tour->tourType->name ?? null;
}

// Traducción de itinerario (si existe)
$itineraryName = null;
$itineraryDesc = null;
if ($tour->itinerary) {
$itTranslations = $tour->itinerary->translations ?? collect();
$itTr = $itTranslations->firstWhere('locale', $currentLocale)
?? $itTranslations->first();
$itineraryName = $itTr->name ?? null;
$itineraryDesc = $itTr->description ?? null;
}

// Colección robusta de items de itinerario
$itineraryItems = collect();
if ($tour->itinerary && $tour->itinerary->relationLoaded('items')) {
$itineraryItems = $tour->itinerary->items;
} elseif ($tour->itinerary && method_exists($tour->itinerary, 'items')) {
$itineraryItems = $tour->itinerary->items;
} elseif ($tour->relationLoaded('itineraryItems')) {
$itineraryItems = $tour->itineraryItems;
}
@endphp

<div class="container-fluid">
    @include('admin.tours.wizard.partials.stepper')

    {{-- Header mejorado --}}
    <div class="summary-header">
        <h1>
            <i class="fas fa-eye"></i>
            {{ __('m_tours.tour.wizard.steps.summary') }}
        </h1>
        <p>{{ $tourName }}</p>
    </div>

    <div class="row">
        {{-- Columna principal --}}
        <div class="col-lg-8">
            {{-- Información básica en grid --}}
            <div class="card summary-card">
                <div class="card-header">
                    <i class="fas fa-info-circle text-primary"></i>
                    {{ __('m_tours.tour.summary.basic_details_title') }}
                </div>
                <div class="card-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="label">{{ __('m_tours.tour.fields.name') }}</div>
                            <div class="value">{{ $tourName }}</div>
                        </div>

                        <div class="info-item">
                            <div class="label">Slug</div>
                            <div class="value"><code>{{ $tour->slug }}</code></div>
                        </div>

                        @if($tour->tourType && $tourTypeName)
                        <div class="info-item">
                            <div class="label">{{ __('m_tours.tour.fields.type') }}</div>
                            <div class="value">{{ $tourTypeName }}</div>
                        </div>
                        @endif

                        <div class="info-item">
                            <div class="label">{{ __('m_tours.tour.fields.length_hours') }}</div>
                            <div class="value">
                                {{ $tour->length ?? 'N/A' }} {{ __('m_tours.common.hours') }}
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="label">{{ __('m_tours.tour.fields.max_capacity') }}</div>
                            <div class="value">
                                {{ $tour->max_capacity }} {{ __('m_tours.common.people') }}
                            </div>
                        </div>

                        @if($tour->group_size)
                        <div class="info-item">
                            <div class="label">{{ __('m_tours.tour.fields.group_size') }}</div>
                            <div class="value">
                                {{ $tour->group_size }} {{ __('m_tours.common.people') }}
                            </div>
                        </div>
                        @endif
                    </div>

                    @if($tourOverview)
                    <div class="mt-3 pt-3 border-top">
                        <div class="label mb-2" style="color: #a0aec0; font-size: 0.8rem; text-transform: uppercase;">
                            {{ __('m_tours.tour.fields.overview') }}
                        </div>
                        <p class="mb-0 text-muted">
                            {!! nl2br(e($tourOverview)) !!}
                        </p>
                    </div>
                    @endif

                    @if(isset($tour->recommendations) && $tour->recommendations)
                    <div class="mt-3 pt-3 border-top">
                        <div class="label mb-2" style="color: #a0aec0; font-size: 0.8rem; text-transform: uppercase;">
                            {{ __('m_tours.tour.fields.recommendations') ?? 'Recomendaciones' }}
                        </div>
                        <p class="mb-0 text-muted">
                            {!! nl2br(e($tour->recommendations)) !!}
                        </p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Tabs para organizar el resto del contenido --}}
            <div class="card summary-card summary-tabs">
                <div class="card-header p-0">
                    <ul class="nav nav-tabs" role="tablist">
                        @if($tour->itinerary && $itineraryItems->isNotEmpty())
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#itinerary-tab">
                                <i class="fas fa-route"></i> {{ __('m_tours.tour.summary.itinerary_title') }}
                            </a>
                        </li>
                        @endif

                        @if($tour->allSchedules->isNotEmpty())
                        <li class="nav-item">
                            <a class="nav-link {{ (!$tour->itinerary || $itineraryItems->isEmpty()) ? 'active' : '' }}"
                                data-toggle="tab" href="#schedules-tab">
                                <i class="fas fa-clock"></i> {{ __('m_tours.tour.summary.schedules_title') }}
                            </a>
                        </li>
                        @endif

                        @if($tour->allAmenities->isNotEmpty() || $tour->allExcludedAmenities->isNotEmpty())
                        <li class="nav-item">
                            <a class="nav-link {{ (!$tour->itinerary || $itineraryItems->isEmpty()) && $tour->allSchedules->isEmpty() ? 'active' : '' }}"
                                data-toggle="tab" href="#amenities-tab">
                                <i class="fas fa-star"></i> {{ __('m_tours.tour.summary.amenities_title') ?? 'Amenidades' }}
                            </a>
                        </li>
                        @endif

                        @if($tour->allLanguages->isNotEmpty())
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#languages-tab">
                                <i class="fas fa-language"></i> {{ __('m_tours.tour.summary.languages_title') }}
                            </a>
                        </li>
                        @endif

                        @if($tour->allPrices->isNotEmpty())
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#prices-tab">
                                <i class="fas fa-dollar-sign"></i> {{ __('m_tours.tour.summary.prices_title') }}
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        {{-- Tab de Itinerario --}}
                        @if($tour->itinerary && $itineraryItems->isNotEmpty())
                        <div class="tab-pane fade show active" id="itinerary-tab">
                            <h5 class="mb-3">{{ $itineraryName ?? __('m_tours.itinerary.fields.name') }}</h5>
                            @if($itineraryDesc)
                            <p class="text-muted">{{ $itineraryDesc }}</p>
                            @endif

                            {{-- Label "Items:" --}}
                            <div class="label mb-2" style="color: #a0aec0; font-size: 0.8rem; text-transform: uppercase;">
                                Items
                            </div>

                            <ol class="itinerary-items-list">
                                @foreach($itineraryItems as $item)
                                @php
                                $itemTitle = $item->title;
                                $itemDesc = $item->description;
                                @endphp
                                <li>
                                    <strong style="color: #e2e8f0;">{{ $itemTitle }}</strong>
                                    @if($itemDesc)
                                    <p class="text-muted small mb-0 mt-1">{{ $itemDesc }}</p>
                                    @endif
                                </li>
                                @endforeach
                            </ol>
                        </div>
                        @endif

                        {{-- Tab de Horarios --}}
                        @if($tour->allSchedules->isNotEmpty())
                        <div class="tab-pane fade {{ (!$tour->itinerary || $itineraryItems->isEmpty()) ? 'show active' : '' }}"
                            id="schedules-tab">
                            <ul class="icon-list">
                                @foreach($tour->allSchedules as $schedule)
                                <li>
                                    <i class="fas fa-clock text-primary"></i>
                                    <div class="flex-grow-1">
                                        <strong style="color: #e2e8f0;">
                                            {{ date('g:i A', strtotime($schedule->start_time)) }}
                                            -
                                            {{ date('g:i A', strtotime($schedule->end_time)) }}
                                        </strong>
                                        @if($schedule->label)
                                        <span class="badge badge-info custom-badge ml-2">
                                            {{ $schedule->label }}
                                        </span>
                                        @endif
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        {{-- Tab de Amenidades --}}
                        @if($tour->allAmenities->isNotEmpty() || $tour->allExcludedAmenities->isNotEmpty())
                        <div class="tab-pane fade {{ (!$tour->itinerary || $itineraryItems->isEmpty()) && $tour->allSchedules->isEmpty() ? 'show active' : '' }}"
                            id="amenities-tab">
                            <div class="row">
                                @if($tour->allAmenities->isNotEmpty())
                                <div class="col-md-6">
                                    <h6 class="text-success mb-3">
                                        <i class="fas fa-check-circle"></i>
                                        {{ __('m_tours.tour.ui.amenities_included') }}
                                    </h6>
                                    <ul class="icon-list">
                                        @foreach($tour->allAmenities as $amenity)
                                        @php
                                        $amTranslations = $amenity->translations ?? collect();
                                        $amTr = $amTranslations->firstWhere('locale', $currentLocale)
                                        ?? $amTranslations->first();
                                        $amenName = $amTr->name ?? $amenity->name ?? '';
                                        @endphp
                                        <li>
                                            @if($amenity->icon)
                                            <i class="{{ $amenity->icon }} text-success"></i>
                                            @else
                                            <i class="fas fa-check text-success"></i>
                                            @endif
                                            <span>{{ $amenName }}</span>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif

                                @if($tour->allExcludedAmenities->isNotEmpty())
                                <div class="col-md-6">
                                    <h6 class="text-danger mb-3">
                                        <i class="fas fa-times-circle"></i>
                                        {{ __('m_tours.tour.ui.amenities_excluded') }}
                                    </h6>
                                    <ul class="icon-list">
                                        @foreach($tour->allExcludedAmenities as $amenity)
                                        @php
                                        $amTranslations = $amenity->translations ?? collect();
                                        $amTr = $amTranslations->firstWhere('locale', $currentLocale)
                                        ?? $amTranslations->first();
                                        $amenName = $amTr->name ?? $amenity->name ?? '';
                                        @endphp
                                        <li>
                                            @if($amenity->icon)
                                            <i class="{{ $amenity->icon }} text-danger"></i>
                                            @else
                                            <i class="fas fa-times text-danger"></i>
                                            @endif
                                            <span>{{ $amenName }}</span>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        {{-- Tab de Idiomas --}}
                        @if($tour->allLanguages->isNotEmpty())
                        <div class="tab-pane fade" id="languages-tab">
                            <ul class="icon-list">
                                @foreach($tour->allLanguages as $language)
                                @php
                                $langTranslations = $language->translations ?? collect();
                                $langTr = $langTranslations->firstWhere('locale', $currentLocale)
                                ?? $langTranslations->first();
                                $langName = $langTr->name ?? $language->name ?? '';
                                @endphp
                                <li>
                                    <i class="fas fa-language text-primary"></i>
                                    <span>{{ $langName }}</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        {{-- Tab de Precios por reglas / fechas --}}
                        @if($tour->allPrices->isNotEmpty())
                        <div class="tab-pane fade" id="prices-tab">
                            @php
                            // Agrupar por rango (o default) usando campos en la propia tabla de precios
                            $groupedPrices = $tour->allPrices
                            ->filter(fn($p) => $p->category) // sólo con categoría
                            ->groupBy(function ($p) {
                            $from = $p->valid_from ?? null;
                            $until = $p->valid_until ?? null;

                            if ($p->is_default) {
                            return 'default';
                            }

                            $fromStr = $from instanceof \Carbon\Carbon ? $from->format('d-M-Y') : $from;
                            $untilStr = $until instanceof \Carbon\Carbon ? $until->format('d-M-Y') : $until;

                            return ($fromStr ?: 'null') . '_' . ($untilStr ?: 'null');
                            })
                            ->sortBy(function ($prices, $key) {
                            /** @var \Illuminate\Support\Collection $prices */
                            $first = $prices->first();
                            if ($first->is_default) {
                            return '9999-12-31';
                            }
                            $from = $first->valid_from ?? null;
                            return $from instanceof \Carbon\Carbon ? $from->format('d-M-Y') : ($from ?: '0000-01-01');
                            });

                            $noPriceMsg = __('m_tours.tour.summary.no_price_rules');
                            if ($noPriceMsg === 'm_tours.tour.summary.no_price_rules') {
                            $noPriceMsg = 'No hay reglas de precio configuradas.';
                            }
                            @endphp

                            @if($groupedPrices->isEmpty())
                            <p class="text-muted mb-0">{{ $noPriceMsg }}</p>
                            @else
                            <h5 class="mb-3">{{ __('m_tours.tour.summary.prices_title') ?? 'Precios por categoría' }}</h5>

                            <div id="priceRulesAccordion" class="accordion">
                                @foreach($groupedPrices as $key => $pricesGroup)
                                @php
                                /** @var \App\Models\TourPrice $first */
                                $first = $pricesGroup->first();
                                $from = $first->valid_from ?? null;
                                $until = $first->valid_until ?? null;

                                $fromStr = $from instanceof \Carbon\Carbon ? $from->format('d-M-Y') : $from;
                                $untilStr = $until instanceof \Carbon\Carbon ? $until->format('d-M-Y') : $until;

                                $accordionId = 'priceRule_' . md5($key);
                                @endphp

                                <div class="card mb-2" style="background:#1a202c;border:none;">
                                    <div class="card-header p-2" id="heading-{{ $accordionId }}">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <button
                                                class="btn btn-link text-left p-0"
                                                type="button"
                                                data-toggle="collapse"
                                                data-target="#collapse-{{ $accordionId }}"
                                                aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                                                aria-controls="collapse-{{ $accordionId }}"
                                                style="color:#e2e8f0;text-decoration:none;">
                                                <i class="fas fa-calendar-alt mr-2"></i>

                                                @if($first->is_default)
                                                {{ __('m_tours.tour.summary.default_price_rule') ?? 'Precio por defecto' }}
                                                @else
                                                {{ __('m_tours.tour.summary.date_range') ?? 'Rango de fechas' }}

                                                @if($fromStr)
                                                <span class="badge badge-light custom-badge ml-2">
                                                    {{ $fromStr }}
                                                </span>
                                                @endif

                                                @if($untilStr)
                                                <span class="badge badge-light custom-badge ml-1">
                                                    {{ $untilStr }}
                                                </span>
                                                @endif
                                                @endif
                                            </button>

                                            <span class="badge custom-badge badge-{{ $first->is_default ? 'info' : 'primary' }}">
                                                {{ $first->is_default ? __('m_tours.common.default') : __('m_tours.common.custom') }}
                                            </span>
                                        </div>
                                    </div>

                                    <div
                                        id="collapse-{{ $accordionId }}"
                                        class="collapse {{ $loop->first ? 'show' : '' }}"
                                        aria-labelledby="heading-{{ $accordionId }}"
                                        data-parent="#priceRulesAccordion">
                                        <div class="card-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-sm pricing-table mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>{{ __('m_tours.tour.summary.table.category') }}</th>
                                                            <th class="text-right">{{ __('m_tours.tour.summary.table.price') }}</th>
                                                            <th class="text-center">{{ __('m_tours.tour.summary.table.min_max') }}</th>
                                                            <th class="text-center">{{ __('m_tours.tour.summary.table.status') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($pricesGroup as $price)
                                                        <tr>
                                                            <td>{{ $price->category->getTranslatedName() }}</td>
                                                            <td class="text-right">
                                                                <span class="font-weight-bold" style="color:#f56565;">
                                                                    {{ config('app.currency_symbol', '$') }}{{ number_format($price->price, 2) }}
                                                                </span>
                                                            </td>
                                                            <td class="text-center">
                                                                {{ $price->min_quantity }} - {{ $price->max_quantity }}
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="badge custom-badge badge-{{ $price->is_active ? 'success' : 'secondary' }}">
                                                                    {{ $price->is_active ? __('m_tours.common.active') : __('m_tours.common.inactive') }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endif

                            {{-- Tax Breakdown debajo del acordeón --}}
                            @if($tour->taxes->isNotEmpty())
                            <div class="border-top mt-3 pt-3">
                                <h6 class="mb-3" style="color: #e2e8f0;">
                                    <i class="fas fa-percentage text-info"></i> {{ __('taxes.breakdown.title') }}
                                </h6>
                                <div class="row">
                                    @foreach($tour->taxes as $tax)
                                    <div class="col-md-6 mb-2">
                                        <div class="d-flex justify-content-between align-items-center p-2 rounded" style="background: #3a4556;">
                                            <div>
                                                <div class="font-weight-bold" style="color: #e2e8f0;">
                                                    {{ $tax->name }}
                                                </div>
                                                <small class="text-muted">
                                                    <code style="background: #1a202c; color: #68d391; padding: 0.2rem 0.4rem; border-radius: 0.25rem;">
                                                        {{ $tax->code }}
                                                    </code>
                                                    -
                                                    {{ $tax->type == 'percentage'
                                                                    ? number_format($tax->rate, 2) . '%'
                                                                    : config('app.currency_symbol', '$') . number_format($tax->rate, 2) }}
                                                </small>
                                            </div>
                                            <span class="badge {{ $tax->is_inclusive ? 'badge-success' : 'badge-warning' }}">
                                                {{ $tax->is_inclusive ? __('taxes.included') : __('taxes.not_included') }}
                                            </span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Panel de acciones --}}
        <div class="col-lg-4">
            <div class="action-panel">
                {{-- Tarjeta de publicación --}}
                <div class="card publish-card">
                    <div class="card-header">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-rocket"></i>
                            {{ __('m_tours.tour.wizard.ready_to_publish') }}
                        </h3>
                    </div>
                    <div class="card-body">
                        <p>{{ __('m_tours.tour.wizard.publish_explanation') }}</p>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <small>{{ __('m_tours.tour.wizard.can_edit_later') }}</small>
                        </div>

                        <form method="POST" action="{{ route('admin.tours.wizard.publish', $tour) }}">
                            @csrf
                            <button type="submit" class="btn btn-light btn-block btn-lg font-weight-bold">
                                <i class="fas fa-check-circle"></i>
                                {{ __('m_tours.tour.wizard.publish_tour') }}
                            </button>
                        </form>

                        <hr>

                        <a href="{{ route('admin.tours.wizard.step', ['tour' => $tour, 'step' => 5]) }}"
                            class="btn btn-warning btn-block">
                            <i class="fas fa-arrow-left"></i>
                            {{ __('m_tours.common.previous') }}
                        </a>

                        @if($tour->is_draft)
                        <button type="button"
                            class="btn btn-danger btn-block mt-2"
                            onclick="return confirm('{{ __('m_tours.tour.wizard.confirm_cancel') }}') && document.getElementById('delete-draft-form').submit();">
                            <i class="fas fa-trash"></i>
                            {{ __('m_tours.tour.wizard.delete_draft') }}
                        </button>
                        @endif
                    </div>
                </div>

                {{-- Checklist --}}
                <div class="card summary-card mt-3">
                    <div class="card-header">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-tasks"></i>
                            {{ __('m_tours.tour.wizard.checklist') }}
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="checklist-item">
                            <i class="fas fa-check-circle text-success"></i>
                            <span>{{ __('m_tours.tour.wizard.checklist_details') }}</span>
                        </div>
                        <div class="checklist-item">
                            <i class="fas fa-{{ $tour->itinerary && $itineraryItems->isNotEmpty() ? 'check-circle text-success' : 'circle text-muted' }}"></i>
                            <span>{{ __('m_tours.tour.wizard.checklist_itinerary') }}</span>
                        </div>
                        <div class="checklist-item">
                            <i class="fas fa-{{ $tour->schedules->isNotEmpty() ? 'check-circle text-success' : 'circle text-muted' }}"></i>
                            <span>{{ __('m_tours.tour.wizard.checklist_schedules') }}</span>
                        </div>
                        <div class="checklist-item">
                            <i class="fas fa-{{ $tour->amenities->isNotEmpty() ? 'check-circle text-success' : 'circle text-muted' }}"></i>
                            <span>{{ __('m_tours.tour.wizard.checklist_amenities') }}</span>
                        </div>
                        <div class="checklist-item">
                            <i class="fas fa-{{ $tour->prices->isNotEmpty() ? 'check-circle text-success' : 'circle text-muted' }}"></i>
                            <span>{{ __('m_tours.tour.wizard.checklist_prices') }}</span>
                        </div>

                        @if(!$tour->itinerary || $itineraryItems->isEmpty() || $tour->schedules->isEmpty() || $tour->prices->isEmpty())
                        <div class="alert alert-warning mt-3 mb-0"
                            style="background: rgba(255, 193, 7, 0.2); border-color: #ffc107; color: #ffc107;">
                            <i class="fas fa-exclamation-triangle"></i>
                            <small>{{ __('m_tours.tour.wizard.incomplete_warning') }}</small>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

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

@push('js')
<script>
    $(document).ready(function() {
        // Prevenir scroll al cambiar tabs
        $('.summary-tabs .nav-link[data-toggle="tab"]').on('click', function() {
            const currentScroll = window.pageYOffset || document.documentElement.scrollTop;
            setTimeout(function() {
                window.scrollTo(0, currentScroll);
            }, 0);
        });

        $('.summary-tabs .nav-link[data-toggle="tab"]').on('shown.bs.tab', function() {
            const currentScroll = window.pageYOffset || document.documentElement.scrollTop;
            window.scrollTo(0, currentScroll);
        });
    });
</script>
@endpush
@endsection