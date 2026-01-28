{{-- resources/views/public/products/subcategory.blade.php --}}
@extends('layouts.app')

@section('meta_title'){{ $metaTitle }}@endsection
@section('meta_description'){{ $metaDescription }}@endsection
@section('title', $subcategoryConfig['label'] ?? 'Products')

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
      <li class="breadcrumb-item">
        <a href="{{ url('/' . $categoryConfig['url_prefix']) }}">
          {{ $categoryConfig['plural'] }}
        </a>
      </li>
      <li class="breadcrumb-item active" aria-current="page">
        {{ $subcategoryConfig['label'] }}
      </li>
    </ol>
  </nav>

  @php
  use App\Helpers\SchemaHelper;
  $breadcrumbItems = [
    ['name' => __('adminlte::adminlte.home'), 'url' => url('/')],
    ['name' => $categoryConfig['plural'], 'url' => url('/' . $categoryConfig['url_prefix'])],
    ['name' => $subcategoryConfig['label']],
  ];
  $breadcrumbSchema = SchemaHelper::generateBreadcrumbSchema($breadcrumbItems);
  @endphp
  <script type="application/ld+json">
    {!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
  </script>

  {{-- HEADER --}}
  <div class="tours-index-header mb-2">
    <div>
      <h1 class="mt-5 mb-0">
        <i class="{{ $subcategoryConfig['icon'] ?? 'fas fa-tag' }}"></i>
        {{ $subcategoryConfig['label'] }}
      </h1>
      <p class="text-muted mb-0">
        {{ $subcategoryConfig['description'] }}
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

  {{-- LAYOUT DE TOURS (Grid mode only for subcategories) --}}
  <div
    id="tours-layout"
    class="tours-layout mode-grid"
    data-default-layout="grid"
    data-total-tours="{{ $totalTours }}">

    <div class="tours-track">
      @forelse($tours as $tour)
      @php
      $cover = optional($tour->coverImage)->url
        ?? $coverFromFolder($tour->product_id ?? $tour->id ?? null);

      // Nombre sin paréntesis
      $rawName = $tour->translated_name ?? $tour->name;
      $displayName = preg_replace('/\s*\(.*?\)\s*/', '', (string) $rawName) ?: $rawName;

      // Obtener precios activos para HOY
      $activeCategories = $tour->activePricesForDate(now())
        ->sortBy('category_id')
        ->values();

      // Tags de itinerario
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

      $tourUrl = \App\Helpers\ProductCategoryHelper::productUrl($tour);
      @endphp

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

            @if(!empty($tour->length))
            <div class="tour-duration-badge">
              <span class="duration-number">{{ $tour->length }}</span>
              <span class="duration-unit">hrs</span>
            </div>
            @endif
          </div>

          <div class="card-body tours-card-body">
            {{-- HEADER: título --}}
            <div class="tour-card-header">
              <div class="tour-title-pill">
                <div class="tour-title-text">
                  {{ $displayName }}
                </div>
              </div>
            </div>

            {{-- MAIN: tags de itinerario --}}
            <div class="tour-card-main">
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

              <a
                href="{{ $tourUrl }}"
                class="btn btn-tour-cta w-100 mt-2">
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
    if (!layoutRoot) return;

    // Igualar secciones de las cards
    function equalizeTourSections() {
      const wrappers = Array.from(document.querySelectorAll('.tours-index-card-wrapper'));
      if (!wrappers.length) return;

      const selectors = [
        '.tour-card-header',
        '.tour-tags-container',
        '.tour-prices-container'
      ];

      // Reset heights
      selectors.forEach(selector => {
        wrappers.forEach(w => {
          const elem = w.querySelector(selector);
          if (elem) elem.style.minHeight = '';
        });
      });

      void layoutRoot.offsetHeight;

      // Group by rows
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

      // Equalize each section per row
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

    // Card click handlers
    function initCardClickHandlers() {
      const wrappers = Array.from(document.querySelectorAll('.tours-index-card-wrapper[data-url]'));
      if (!wrappers.length) return;

      const DRAG_THRESHOLD = 10;
      let startX = 0;
      let startY = 0;

      wrappers.forEach(wrapper => {
        const url = wrapper.dataset.url;
        if (!url) return;

        wrapper.addEventListener('click', function(e) {
          const tag = (e.target.tagName || '').toLowerCase();

          if (['a', 'button', 'input', 'select', 'textarea', 'label'].includes(tag)) {
            return;
          }

          if (wrapper.dataset.suppressClick === 'true') {
            wrapper.dataset.suppressClick = 'false';
            return;
          }

          window.location.href = url;
        });

        wrapper.addEventListener('touchstart', function(e) {
          if (!e.touches || !e.touches.length) return;
          const t = e.touches[0];
          startX = t.clientX;
          startY = t.clientY;
          wrapper.dataset.suppressClick = 'false';
        }, { passive: true });

        wrapper.addEventListener('touchmove', function(e) {
          if (!e.touches || !e.touches.length) return;
          const t = e.touches[0];
          const dx = Math.abs(t.clientX - startX);
          const dy = Math.abs(t.clientY - startY);

          if (dx > DRAG_THRESHOLD || dy > DRAG_THRESHOLD) {
            wrapper.dataset.suppressClick = 'true';
          }
        }, { passive: true });

        wrapper.addEventListener('touchend', function() {
          setTimeout(() => {
            wrapper.dataset.suppressClick = 'false';
          }, 0);
        }, { passive: true });

        wrapper.addEventListener('keydown', function(e) {
          const key = e.key || e.code;
          if (key === 'Enter' || key === ' ' || key === 'Spacebar') {
            e.preventDefault();
            window.location.href = url;
          }
        });
      });
    }

    window.addEventListener('load', function() {
      setTimeout(equalizeTourSections, 100);
      initCardClickHandlers();
    });

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
      initCardClickHandlers();
    }
  })();
</script>
@endpush
