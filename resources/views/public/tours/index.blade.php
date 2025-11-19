{{-- resources/views/public/tours/index.blade.php --}}

@extends('layouts.app')

@section('title', 'Tours')

@push('styles')
  @vite([
    'resources/css/app.css',
    'resources/css/home.css',
    'resources/css/tour.css',
    'resources/css/tours-index.css',
  ])
@endpush

@php
use Illuminate\Support\Facades\Storage;

/**
 * Imagen de portada fallback (mismo patrón que en home)
 */
$coverFromFolder = function (?int $tourId): string {
    if (!$tourId) return asset('images/volcano.png');
    $folder = "tours/{$tourId}/gallery";
    if (!Storage::disk('public')->exists($folder)) {
        return asset('images/volcano.png');
    }

    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    $first = collect(Storage::disk('public')->files($folder))
        ->filter(fn($p) => in_array(strtolower(pathinfo($p, PATHINFO_EXTENSION)), $allowed, true))
        ->sort(fn ($a, $b) => strnatcasecmp($a, $b))
        ->first();

    return $first ? asset('storage/' . $first) : asset('images/volcano.png');
};

/**
 * Helper: nombre traducido de categoría
 */
$catName = function ($cat) {
    if (!$cat) return 'N/A';

    if (method_exists($cat, 'getTranslatedName')) {
        return $cat->getTranslatedName(app()->getLocale());
    }

    $loc = app()->getLocale();
    $fb  = config('app.fallback_locale', 'es');
    $t   = optional($cat->translations);

    return $t->firstWhere('locale', $loc)->name
        ?? $t->firstWhere('locale', $fb)->name
        ?? ($cat->display_name ?? $cat->name ?? 'N/A');
};

/**
 * Helper: rango de edad legible
 */
$ageRangeText = function ($cat) {
    if (!$cat) return null;
    $from = $cat->age_from;
    $to   = $cat->age_to;

    if (is_null($from) && is_null($to)) return null;
    if (!is_null($from) && is_null($to)) return "{$from}+";
    if (is_null($from) && !is_null($to)) return "0–{$to}";
    return "{$from}–{$to}";
};
@endphp

@section('content')
<div class="container page-first">
  <div class="tours-index-page mx-auto">

    {{-- HEADER SUPERIOR --}}
    <div class="tours-index-header mb-3">
      <div>
        <h1 class="mb-1">
          {{ __('adminlte::adminlte.all_tours_title') ?? 'Tours & Experiencias' }}
        </h1>
        <p class="text-muted mb-0">
          {{ __('adminlte::adminlte.all_tours_subtitle') ?? 'Explora todas nuestras actividades y filtra por texto o categoría.' }}
        </p>
      </div>

      @if($tours->total() > 0)
        <div class="tours-index-counter">
          <span class="counter-badge">
            <i class="fas fa-map-signs"></i>
            {{ $tours->total() }} {{ Str::plural('tour', $tours->total()) }}
          </span>
        </div>
      @endif
    </div>

    {{-- CARD DE FILTROS --}}
    <div class="card tours-index-filters mb-3">
      <div class="card-body">
        {{-- Header de filtros --}}
        <div class="tours-index-filters-header mb-3">
          <div class="filters-title-block">
            <div class="filters-icon-circle">
              <i class="fas fa-sliders-h"></i>
            </div>
            <div>
              <div class="filters-title">
                {{ __('adminlte::adminlte.filters_title') }}
              </div>
              <div class="filters-subtitle">
                {{ __('adminlte::adminlte.filters_subtitle') }}
              </div>
            </div>
          </div>

          @if($search || $activeCategory)
            <div class="filters-active-chip">
              <i class="fas fa-filter"></i>
              <span>{{ __('adminlte::adminlte.filters_active') }}</span>
            </div>
          @endif
        </div>

        {{-- Formulario de filtros --}}
        <form method="GET"
              action="{{ localized_route('tours.index') }}"
              class="tours-index-filters-form">
          <div class="row g-2 align-items-end">

            {{-- Búsqueda --}}
            <div class="col-12 col-md-6 col-lg-6">
              <label class="filters-label d-md-none mb-1" for="filters-q">
                {{ __('adminlte::adminlte.search_tours_placeholder') }}
              </label>
              <div class="input-group input-group-sm filters-input-group">
                <span class="input-group-text">
                  <i class="fas fa-search"></i>
                </span>
                <input
                  type="text"
                  id="filters-q"
                  name="q"
                  value="{{ $search }}"
                  class="form-control filters-control"
                  placeholder="{{ __('adminlte::adminlte.search_tours_placeholder') }}"
                >
              </div>
            </div>

            {{-- Categoría --}}
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
              <label class="filters-label d-md-none mb-1" for="filters-category">
                {{ __('adminlte::adminlte.category_label') }}
              </label>
              <div class="filters-select-wrapper">
                <span class="filters-select-icon">
                  <i class="fas fa-tags"></i>
                </span>
                <select
                  id="filters-category"
                  name="category"
                  class="form-control filters-control filters-select"
                >
                  <option value="">
                    {{ __('adminlte::adminlte.all_categories') }}
                  </option>
                  @foreach($categories as $cat)
                    <option
                      value="{{ $cat->id }}"
                      {{ (string)$activeCategory === (string)$cat->id ? 'selected' : '' }}
                    >
                      {{ $cat->name }}
                    </option>
                  @endforeach
                </select>
              </div>
            </div>

            {{-- Botones --}}
            <div class="col-12 col-sm-6 col-md-2 col-lg-3">
              <div class="d-flex gap-2 justify-content-sm-end mt-2 mt-md-0 filters-actions">
                <button type="submit" class="btn btn-sm filters-submit-btn">
                  <i class="fas fa-filter me-1"></i>
                  {{ __('adminlte::adminlte.filters_btn') }}
                </button>

                @if($search || $activeCategory)
                  <a href="{{ localized_route('tours.index') }}"
                     class="btn btn-sm filters-clear-btn">
                    <i class="fas fa-rotate-left me-1"></i>
                    {{ __('adminlte::adminlte.clear_filters') }}
                  </a>
                @endif
              </div>
            </div>
          </div>

          {{-- Chips bajo filtros --}}
          @if($search || $activeCategory)
            <div class="filters-chips">
              @if($search)
                <div class="filters-chip">
                  <span class="chip-label">
                    <i class="fas fa-search me-1"></i> {{ __('adminlte::adminlte.search_tours_placeholder') }}:
                  </span>
                  <span class="chip-value">“{{ $search }}”</span>
                </div>
              @endif

              @if($activeCategory)
                @php
                  $catNameChip = optional(
                    $categories->firstWhere('id', $activeCategory)
                  )->name;
                @endphp
                @if($catNameChip)
                  <div class="filters-chip">
                    <span class="chip-label">
                      <i class="fas fa-tag me-1"></i> {{ __('adminlte::adminlte.category_label') }}:
                    </span>
                    <span class="chip-value">{{ $catNameChip }}</span>
                  </div>
                @endif
              @endif
            </div>
          @endif
        </form>
      </div>
    </div>

    {{-- GRID DE CARDS --}}
    @if($tours->count())
      <div class="row tours-index-grid">
        @foreach($tours as $tour)
          @php
            $cover = optional($tour->coverImage)->url
              ?? $coverFromFolder($tour->tour_id ?? $tour->id ?? null);

            // Colección de precios activos (ya prefiltrados en el controlador)
            $activeCategories = collect($tour->price_categories ?? []);
            $maxToShow        = 2;
          @endphp

          <div class="col-12 col-sm-6 col-lg-4">
            <article class="card tours-index-card h-100">
              <img
                src="{{ $cover }}"
                class="card-img-top"
                alt="{{ $tour->translated_name ?? $tour->name }}"
              >

              <div class="card-body d-flex flex-column">
                {{-- TÍTULO EN PILL ROJA (altura normalizada a 2 filas mín.) --}}
                <div class="tour-title-pill">
                  <div class="tour-title-text">
                    {{ $tour->translated_name ?? $tour->name }}
                  </div>
                </div>

                {{-- DURACIÓN --}}
                @if(!empty($tour->duration_label))
                  <p class="tours-index-duration mb-2">
                    <strong>{{ __('adminlte::adminlte.duration') }}:</strong>
                    {{ $tour->duration_label }}
                  </p>
                @elseif(!empty($tour->length))
                  <p class="tours-index-duration mb-2">
                    <strong>{{ __('adminlte::adminlte.duration') }}:</strong>
                    {{ $tour->length }} {{ __('adminlte::adminlte.horas') }}
                  </p>
                @endif

                {{-- PRECIOS POR CATEGORÍA (similar al modal de home) --}}
                @if($activeCategories->isNotEmpty())
                  <div class="tours-index-price-row mb-3">
                    @foreach($activeCategories->take($maxToShow) as $priceRecord)
                      @php
                        $category = $priceRecord->category;
                        $nameTr   = $catName($category);
                        $price    = (float) $priceRecord->price;
                        $ageText  = $ageRangeText($category);
                      @endphp
                      <div class="d-flex justify-content-between">
                        <div class="tours-index-price-label">
                          <strong>{{ $nameTr }}</strong>
                          @if($ageText)
                            <small> ({{ $ageText }})</small>
                          @endif
                        </div>
                        <div class="tours-index-price-amount">
                          ${{ number_format($price, 2) }}
                        </div>
                      </div>
                    @endforeach

                    @if($activeCategories->count() > $maxToShow)
                      <div class="mt-1 small text-muted">
                        + {{ $activeCategories->count() - $maxToShow }}
                        {{ __('adminlte::adminlte.more_categories') ?? 'categorías adicionales' }}
                      </div>
                    @endif
                  </div>
                @else
                  <p class="mb-3 small text-muted">
                    {{ __('adminlte::adminlte.no_prices_available') ?? 'Precios no disponibles' }}
                  </p>
                @endif

                {{-- BOTÓN CTA --}}
                <div class="mt-auto">
                  <a
                    href="{{ localized_route('tours.show', $tour->slug) }}"
                    class="btn btn-success w-100"
                  >
                    {{ __('adminlte::adminlte.see_tour') ?? 'Ver detalles' }}
                  </a>
                </div>
              </div>
            </article>
          </div>
        @endforeach
      </div>

      {{-- Paginación --}}
      <div class="tours-pagination mt-3">
        {{ $tours->links() }}
      </div>

    @else
      {{-- SIN RESULTADOS --}}
      <div class="tours-empty text-center py-5">
        <div class="tours-empty-icon mb-2">
          <i class="fas fa-compass"></i>
        </div>
        <h5 class="mb-2">
          {{ __('adminlte::adminlte.no_tours_found') ?? 'No se encontraron tours' }}
        </h5>
        <p class="mb-3 text-muted">
          {{ __('adminlte::adminlte.no_tours_found_help') ?? 'Prueba cambiando el texto de búsqueda o selecciona otra categoría.' }}
        </p>
        <a href="{{ localized_route('tours.index') }}" class="btn btn-outline-secondary btn-sm">
          {{ __('adminlte::adminlte.clear_filters') ?? 'Limpiar filtros' }}
        </a>
      </div>
    @endif

  </div>
</div>
@endsection
