{{-- resources/views/public/tours/index.blade.php --}}
@extends('layouts.app')

@section('title', __('adminlte::adminlte.tours_index_title'))

@push('styles')
@vite(entrypoints: [
'resources/css/home.css',
'resources/css/tours-index.css',
'resources/css/breadcrumbs.css',
])
@endpush

@section('content')
<div class="container tours-index-page" id="tours-page">
  {{-- Breadcrumbs --}}
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
      <li class="breadcrumb-item">
        <a href="{{ url('/') }}">{{ __('adminlte::adminlte.home') }}</a>
      </li>
      <li class="breadcrumb-item active" aria-current="page">
        {{ __('adminlte::adminlte.tours') }}
      </li>
    </ol>
  </nav>
  @php
  use App\Helpers\SchemaHelper;
  $breadcrumbItems = [
  ['name' => __('adminlte::adminlte.home'), 'url' => url('/')],
  ['name' => __('adminlte::adminlte.tours')],
  ];
  $breadcrumbSchema = SchemaHelper::generateBreadcrumbSchema($breadcrumbItems);
  @endphp
  <script type="application/ld+json">
    {
      !!json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!
    }
  </script>
  {{-- HEADER --}}
  <div class="tours-index-header mb-2">
    <div>
      <h1 class="mt-5 mb-0">
        {{ __('adminlte::adminlte.tours_index_title') }}
      </h1>
      <p class="text-muted mb-0">
        {{ __('adminlte::adminlte.tours_index_subtitle') }}
      </p>
    </div>

    <div class="tours-index-counter">
      <span class="counter-badge">
        <i class="fas fa-leaf"></i>
        <span>
          {{ trans_choice('adminlte::adminlte.tours_count', $tours->total(), ['count' => $tours->total()]) }}
        </span>
      </span>
    </div>
  </div>

  {{-- CONTROLES SUPERIORES (filtro + layout) --}}
  <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
    {{-- Botón para desplegar filtros (collapse) --}}
    <button
      class="btn btn-outline-success btn-sm filters-toggle d-inline-flex align-items-center gap-1"
      type="button"
      data-bs-toggle="collapse"
      data-bs-target="#tours-filters"
      aria-expanded="false"
      aria-controls="tours-filters">
      <i class="fas fa-filter"></i>
      <span>{{ __('adminlte::adminlte.filters_btn') }}</span>
    </button>

    {{-- Layout selector: scroll (default) + grid --}}
    <div
      class="layout-toggle btn-group btn-group-sm d-none" {{-- se mostrará por JS si aplica --}}
      role="group"
      aria-label="Layout selector"
      id="layout-toggle-group">
      <button
        type="button"
        class="btn btn-light layout-btn active"
        data-layout="scroll"
        title="{{ __('adminlte::adminlte.layout_scroll') }}">
        <i class="fas fa-arrows-alt-h"></i>
      </button>
      <button
        type="button"
        class="btn btn-light layout-btn"
        data-layout="grid"
        title="{{ __('adminlte::adminlte.layout_grid') }}">
        <i class="fas fa-th-large"></i>
      </button>
    </div>
  </div>

  {{-- FILTROS (COLLAPSE) --}}
  <div id="tours-filters" class="collapse mb-2">
    <div class="card tours-index-filters">
      <div class="card-body">
        <div class="tours-index-filters-header mb-2">
          <div class="filters-title-block">
            <div class="filters-icon-circle">
              <i class="fas fa-filter"></i>
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

          @if(request('q') || request('category'))
          <div class="filters-active-chip">
            <i class="fas fa-check-circle me-1"></i>
            {{ __('adminlte::adminlte.filters_active') }}
          </div>
          @endif
        </div>

        <form
          method="GET"
          action="{{ localized_route('tours.index') }}"
          class="tours-index-filters-form row g-2 align-items-end">
          {{-- Buscar --}}
          <div class="col-12 col-md-6">
            <label class="filters-label mb-1" for="q">
              {{ __('adminlte::adminlte.search_tours_placeholder') }}
            </label>
            <div class="input-group filters-input-group">
              <span class="input-group-text">
                <i class="fas fa-search"></i>
              </span>
              <input
                type="text"
                name="q"
                id="q"
                class="form-control filters-control"
                placeholder="{{ __('adminlte::adminlte.search_tours_placeholder') }}"
                value="{{ request('q') }}">
            </div>
          </div>

          {{-- Categoría de TOUR (TourType) --}}
          <div class="col-12 col-md-4">
            <label class="filters-label mb-1" for="category">
              {{ __('adminlte::adminlte.category_label') }}
            </label>
            <div class="filters-select-wrapper">
              <span class="filters-select-icon">
                <i class="fas fa-tags"></i>
              </span>
              <select
                name="category"
                id="category"
                class="form-select filters-control filters-select">
                <option value="">
                  {{ __('adminlte::adminlte.all_categories') }}
                </option>
                @foreach($categories as $cat)
                <option value="{{ $cat->tour_type_id }}" @selected(request('category')==$cat->tour_type_id)>
                  {{ $cat->translated_name ?? $cat->name }}
                </option>
                @endforeach
              </select>
            </div>
          </div>

          {{-- Botón filtrar / limpiar --}}
          <div class="col-12 col-md-2 d-flex gap-2 justify-content-md-end mt-2 mt-md-0">
            <button type="submit" class="btn btn-success w-100">
              <i class="fas fa-filter me-1"></i>
              {{ __('adminlte::adminlte.filters_btn') }}
            </button>

            @if(request('q') || request('category'))
            <a
              href="{{ localized_route('tours.index') }}"
              class="btn btn-outline-light filters-clear-btn">
              <i class="fas fa-times-circle me-1"></i>
              {{ __('adminlte::adminlte.clear_filters') }}
            </a>
            @endif
          </div>
        </form>
      </div>
    </div>
  </div>

  @php
  $loc = app()->getLocale();
  $fb = config('app.fallback_locale', 'es');

  $coverFromFolder = function (?int $tourId): string {
  if (!$tourId) return asset('images/volcano.png');
  $folder = "tours/{$tourId}/gallery";
  if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($folder)) {
  return asset('images/volcano.png');
  }
  $allowed = ['jpg','jpeg','png','webp'];
  $first = collect(\Illuminate\Support\Facades\Storage::disk('public')->files($folder))
  ->filter(fn($p) => in_array(strtolower(pathinfo($p, PATHINFO_EXTENSION)), $allowed, true))
  ->sort(fn($a, $b) => strnatcasecmp($a, $b))
  ->first();

  return $first ? asset('storage/'.$first) : asset('images/volcano.png');
  };

  $catName = function ($cat) use ($loc, $fb) {
  if (!$cat) return 'N/A';
  if (method_exists($cat, 'getTranslatedName')) {
  return $cat->getTranslatedName($loc);
  }
  $t = optional($cat->translations);

  return $t->firstWhere('locale', $loc)->name
  ?? $t->firstWhere('locale', $fb)->name
  ?? ($cat->display_name ?? $cat->name ?? 'N/A');
  };

  $ageRangeText = function ($cat) {
  if (!$cat) return null;
  $from = $cat->age_from;
  $to = $cat->age_to;

  if (is_null($from) && is_null($to)) return null;
  if (!is_null($from) && is_null($to)) return "{$from}+";
  if (is_null($from) && !is_null($to)) return "0–{$to}";

  return "{$from}–{$to}";
  };

  $totalTours = $tours->total();
  @endphp

  {{-- LAYOUT DE TOURS --}}
  <div
    id="tours-layout"
    class="tours-layout mode-scroll"
    data-default-layout="scroll"
    data-total-tours="{{ $totalTours }}">
    {{-- hint de scroll (solo mobile) --}}
    <div class="scroll-hint d-md-none">
      <span class="scroll-hint-icon">
        <i class="fas fa-arrows-alt-h"></i>
      </span>
    </div>

    <div class="tours-track">
      @forelse($tours as $tour)
      @php
      $cover = optional($tour->coverImage)->url
      ?? $coverFromFolder($tour->tour_id ?? $tour->id ?? null);

      // Nombre sin paréntesis
      $rawName = $tour->translated_name ?? $tour->name;
      $displayName = preg_replace('/\s*\(.*?\)\s*/', '', (string) $rawName) ?: $rawName;

      // NUEVO: Obtener solo UN PRECIO por categoría (válido para HOY)
      $activeCategories = $tour->activePricesForDate(now())
      ->sortBy('category_id')
      ->values();

      // Tags de itinerario (todos los items activos)
      $itineraryTags = collect();
      if ($tour->itinerary) {
      $items = $tour->itinerary->items ?? collect();
      $itineraryTags = $items
      ->map(function ($item) use ($loc, $fb) {
      $tr = $item->translate($loc) ?? $item->translate($fb);
      return $tr->title ?? $item->title;
      })
      ->filter()
      ->unique()
      ->values();
      }

      $tourUrl = localized_route('tours.show', $tour);
      @endphp

      {{-- *** AQUI HACEMOS LA CARD CLICKEABLE *** --}}
      <article
        class="tours-index-card-wrapper tour-card-clickable"
        data-url="{{ $tourUrl }}"
        tabindex="0"
        aria-label="{{ $displayName }}">
        <div class="card tours-index-card h-100">
          {{-- Imagen con badge de duración --}}
          <div class="tour-image-container">
            <img
              src="{{ $cover }}"
              class="card-img-top"
              alt="{{ $displayName }}"
              loading="lazy">

            {{-- Badge de duración sobre la imagen --}}
            @if(!empty($tour->length))
            <div class="tour-duration-badge">
              <span class="duration-number">{{ $tour->length }}</span>
              <span class="duration-unit">hrs</span>
            </div>
            @endif
          </div>

          {{-- BODY estructurado: header / main / footer --}}
          <div class="card-body tours-card-body">
            {{-- HEADER: título --}}
            <div class="tour-card-header">
              <div class="tour-title-pill">
                <div class="tour-title-text">
                  {{ $displayName }}
                </div>
              </div>
            </div>

            {{-- MAIN: solo tags de itinerario --}}
            <div class="tour-card-main">
              {{-- TAGS DE ITINERARIO --}}
              <div class="tour-tags-container">
                @if($itineraryTags->isNotEmpty())
                <div class="tour-tags">
                  @foreach($itineraryTags as $tag)
                  <div class="tour-tag">{{ $tag }}</div>
                  @endforeach
                </div>
                @endif
              </div>
            </div>

            {{-- FOOTER: precios + botón --}}
            <div class="tour-card-footer">
              {{-- Precios por categoría --}}
              <div class="tour-prices-container">
                @if($activeCategories->isNotEmpty())
                <div class="tours-index-prices mb-2">
                  @foreach($activeCategories as $priceRecord)
                  @php
                  $category = $priceRecord->category;
                  $label = $catName($category);
                  $ageText = $ageRangeText($category);
                  $amount = (float) $priceRecord->price_with_tax;
                  $isSeasonal = $priceRecord->valid_from || $priceRecord->valid_until;
                  @endphp
                  <div class="d-flex justify-content-between tours-index-price-row">
                    <div class="tours-index-price-label">
                      <strong>{{ $label }}</strong>
                      @if($ageText)
                      <small> ({{ $ageText }})</small>
                      @endif
                      @if($isSeasonal)
                      <span class="badge badge-info badge-sm ml-1" style="font-size: 0.65rem;" title="Precio de temporada">
                        <i class="fas fa-calendar-alt"></i>
                      </span>
                      @endif
                      <div class="text-muted" style="font-size: 0.7rem;">
                        Min: {{ $priceRecord->min_quantity ?? 0 }} - Máx: {{ $priceRecord->max_quantity ?? 12 }}
                      </div>
                    </div>
                    <div class="tours-index-price-amount">
                      ${{ number_format($amount, 2) }}
                    </div>
                  </div>
                  @endforeach
                </div>
                @else
                <p class="text-muted tiny mb-2">
                  {{ __('adminlte::adminlte.no_prices_available') }}
                </p>
                @endif
              </div>

              {{-- Botón ver tour --}}
              <a
                href="{{ $tourUrl }}"
                class="btn btn-success w-100 mt-2">
                {{ __('adminlte::adminlte.see_tour') }}
              </a>
            </div>
          </div>
        </div>
      </article>
      @empty
      <p class="mt-4 text-center text-muted">
        {{ __('adminlte::adminlte.no_tours_found') }}
      </p>
      @endforelse
    </div>
  </div>

  {{-- PAGINACIÓN --}}
  <div class="mt-3">
    {{ $tours->links() }}
  </div>
</div>
@endsection

@push('scripts')
<script>
  (function() {
    "use strict";

    const layoutRoot = document.getElementById('tours-layout');
    const layoutToggle = document.getElementById('layout-toggle-group');
    if (!layoutRoot || !layoutToggle) return;

    const buttons = Array.from(document.querySelectorAll('.layout-btn'));
    const SCROLL = 'scroll';
    const GRID = 'grid';
    const STORAGE_KEY = 'gv_tours_layout';
    const totalTours = parseInt(layoutRoot.dataset.totalTours || '0', 10);

    // --- Igualar solo 3 secciones: títulos, tags y precios ---
    function equalizeTourSections() {
      const wrappers = Array.from(document.querySelectorAll('.tours-index-card-wrapper'));
      if (!wrappers.length) return;

      const selectors = [
        '.tour-card-header',
        '.tour-tags-container',
        '.tour-prices-container'
      ];

      // 1. Resetear todas las alturas
      selectors.forEach(selector => {
        wrappers.forEach(w => {
          const elem = w.querySelector(selector);
          if (elem) elem.style.minHeight = '';
        });
      });

      // 2. Forzar reflow
      void layoutRoot.offsetHeight;

      // 3. Agrupar wrappers por filas
      const rows = new Map();
      const tolerance = 5;

      wrappers.forEach(w => {
        const rect = w.getBoundingClientRect();
        const top = Math.round(rect.top);

        let rowKey = null;
        for (const [key, _] of rows) {
          if (Math.abs(key - top) <= tolerance) {
            rowKey = key;
            break;
          }
        }

        if (rowKey === null) {
          rowKey = top;
          rows.set(rowKey, []);
        }

        rows.get(rowKey).push(w);
      });

      // 4. Para cada fila, igualar cada sección
      rows.forEach((rowItems) => {
        if (rowItems.length === 0) return;

        selectors.forEach(selector => {
          let maxHeight = 0;

          rowItems.forEach(w => {
            const elem = w.querySelector(selector);
            if (!elem) return;
            const height = elem.offsetHeight;
            if (height > maxHeight) maxHeight = height;
          });

          if (maxHeight > 0) {
            rowItems.forEach(w => {
              const elem = w.querySelector(selector);
              if (elem) elem.style.minHeight = maxHeight + 'px';
            });
          }
        });
      });
    }

    // *** NUEVO: Manejo de click/tap seguro en cards ***
    function initCardClickHandlers() {
      const wrappers = Array.from(document.querySelectorAll('.tours-index-card-wrapper[data-url]'));
      if (!wrappers.length) return;

      const DRAG_THRESHOLD = 10; // px
      let startX = 0;
      let startY = 0;

      wrappers.forEach(wrapper => {
        const url = wrapper.dataset.url;
        if (!url) return;

        // Click general (desktop + tap que no fue swipe)
        wrapper.addEventListener('click', function(e) {
          const tag = (e.target.tagName || '').toLowerCase();

          // Deja que los links/botones internos funcionen normal
          if (['a', 'button', 'input', 'select', 'textarea', 'label'].includes(tag)) {
            return;
          }

          if (wrapper.dataset.suppressClick === 'true') {
            // click generado después de un swipe → se ignora
            wrapper.dataset.suppressClick = 'false';
            return;
          }

          window.location.href = url;
        });

        // Touchstart: registramos posición inicial
        wrapper.addEventListener('touchstart', function(e) {
          if (!e.touches || !e.touches.length) return;
          const t = e.touches[0];
          startX = t.clientX;
          startY = t.clientY;
          wrapper.dataset.suppressClick = 'false';
        }, {
          passive: true
        });

        // Touchmove: si se movió más de cierto umbral, marcamos como swipe
        wrapper.addEventListener('touchmove', function(e) {
          if (!e.touches || !e.touches.length) return;
          const t = e.touches[0];
          const dx = Math.abs(t.clientX - startX);
          const dy = Math.abs(t.clientY - startY);

          if (dx > DRAG_THRESHOLD || dy > DRAG_THRESHOLD) {
            wrapper.dataset.suppressClick = 'true';
          }
        }, {
          passive: true
        });

        // Touchend: el click sintético llegará después si no fue swipe
        wrapper.addEventListener('touchend', function() {
          // leve delay por cualquier click sintético pendiente
          setTimeout(() => {
            wrapper.dataset.suppressClick = 'false';
          }, 0);
        }, {
          passive: true
        });

        // Accesibilidad teclado (Enter/Space)
        wrapper.addEventListener('keydown', function(e) {
          const key = e.key || e.code;
          if (key === 'Enter' || key === ' ' || key === 'Spacebar') {
            e.preventDefault();
            window.location.href = url;
          }
        });
      });
    }

    // --- reglas de layout scroll/grid ---
    function shouldAllowScrollOnDesktop() {
      const isDesktop = window.matchMedia('(min-width: 992px)').matches;
      if (!isDesktop) return true;
      return totalTours > 8;
    }

    function refreshLayoutToggleVisibility() {
      const isDesktop = window.matchMedia('(min-width: 992px)').matches;
      const allowScrollDesktop = shouldAllowScrollOnDesktop();

      if (isDesktop && !allowScrollDesktop) {
        layoutToggle.classList.add('d-none');
        applyLayout(GRID, {
          force: true,
          persist: false
        });
      } else {
        layoutToggle.classList.remove('d-none');
      }
    }

    function applyLayout(mode, opts) {
      opts = opts || {};
      const force = !!opts.force;
      const persist = opts.persist !== false;

      if (![SCROLL, GRID].includes(mode)) mode = SCROLL;

      if (!force && !shouldAllowScrollOnDesktop() && mode === SCROLL) {
        mode = GRID;
      }

      layoutRoot.classList.remove('mode-scroll', 'mode-grid');
      layoutRoot.classList.add('mode-' + mode);

      buttons.forEach(btn => {
        const bMode = btn.dataset.layout;
        const active = (bMode === mode);
        btn.classList.toggle('active', active);
        btn.setAttribute('aria-pressed', active ? 'true' : 'false');
      });

      const hint = layoutRoot.querySelector('.scroll-hint');
      if (hint) {
        hint.style.display = (mode === SCROLL ? '' : 'none');
      }

      if (persist) {
        try {
          localStorage.setItem(STORAGE_KEY, mode);
        } catch (e) {}
      }

      requestAnimationFrame(() => {
        requestAnimationFrame(equalizeTourSections);
      });
    }

    // Layout inicial
    let initial = null;
    try {
      initial = localStorage.getItem(STORAGE_KEY) || null;
    } catch (e) {
      initial = null;
    }
    initial = initial || (layoutRoot.dataset.defaultLayout || SCROLL);

    refreshLayoutToggleVisibility();
    applyLayout(initial, {
      force: false,
      persist: true
    });

    buttons.forEach(btn => {
      btn.addEventListener('click', function() {
        const target = this.dataset.layout || SCROLL;
        applyLayout(target, {
          force: false,
          persist: true
        });
      });
    });

    // Debounce para resize
    let resizeTimer;
    window.addEventListener('resize', function() {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(() => {
        refreshLayoutToggleVisibility();
        equalizeTourSections();
      }, 150);
    });

    // Igualar cuando termina de cargar
    window.addEventListener('load', function() {
      setTimeout(equalizeTourSections, 100);
      initCardClickHandlers(); // *** inicializamos aquí ***
    });

    // También después de que las imágenes se carguen
    const images = Array.from(document.querySelectorAll('.tours-index-card img.card-img-top'));
    let imagesLoaded = 0;
    const totalImages = images.length;

    if (totalImages > 0) {
      images.forEach(img => {
        if (img.complete) {
          imagesLoaded++;
          if (imagesLoaded === totalImages) {
            requestAnimationFrame(equalizeTourSections);
          }
        } else {
          img.addEventListener('load', function() {
            imagesLoaded++;
            if (imagesLoaded === totalImages) {
              requestAnimationFrame(equalizeTourSections);
            }
          });
        }
      });
    } else {
      // Por si no hay imágenes igualmente inicializamos handlers
      initCardClickHandlers();
    }
  })();
</script>
@endpush