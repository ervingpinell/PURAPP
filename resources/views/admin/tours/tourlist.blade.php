{{-- resources/views/admin/tours/tourlist.blade.php --}}

@push('css')
<style>
  :root {
    --tbl-font-size: 1rem;
    --btn-cell-mult: 2.2;
    --btn-cell-size: calc(var(--tbl-font-size) * var(--btn-cell-mult));
  }

  .table-responsive-custom {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }

  .table-sm td,
  .table-sm th {
    padding: .5rem;
    font-size: var(--tbl-font-size);
    vertical-align: middle;
  }

  .font-toolbar {
    display: flex;
    gap: .5rem;
    align-items: center;
    margin: .5rem 0 1rem;
    flex-wrap: wrap;
  }

  .font-toolbar .btn {
    line-height: 1;
    padding: .25rem .5rem;
  }

  .font-toolbar .size-indicator {
    min-width: 3.5rem;
    text-align: center;
    font-variant-numeric: tabular-nums;
    font-weight: 500;
  }

  .actions-cell {
    min-width: 280px;
  }

  .actions-cell .d-flex {
    gap: .375rem;
  }

  .actions-cell .btn-sm {
    width: var(--btn-cell-size);
    height: var(--btn-cell-size);
    padding: 0 !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
    border-radius: .375rem;
    font-size: var(--tbl-font-size);
  }

  .actions-cell .btn-sm i {
    font-size: 1em;
  }

  .schedule-badge {
    display: inline-block;
    margin: .15rem;
    white-space: nowrap;
  }

  .price-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: .25rem 0;
    border-bottom: 1px solid #e9ecef;
  }

  .price-item:last-child {
    border-bottom: none;
  }

  .price-label {
    font-weight: 500;
    color: var(--bs-body-color);
  }

  .price-value {
    font-weight: 600;
    color: #28a745;
  }

  .price-range {
    font-size: .75rem;
    color: var(--bs-secondary-color);
    margin-left: .5rem;
  }

  .table-striped tbody tr:hover {
    background-color: rgba(0, 0, 0, .03);
  }

  @media (max-width: 992px) {
    :root { --btn-cell-mult: 2.0; }
  }

  @media (max-width: 768px) {
    :root { --btn-cell-mult: 1.8; }
    .actions-cell { min-width: 240px; }
  }

  .table-responsive-custom::-webkit-scrollbar {
    height: 8px;
  }

  .table-responsive-custom::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
  }

  .table-responsive-custom::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
  }

  .table-responsive-custom::-webkit-scrollbar-thumb:hover {
    background: #555;
  }

  .scroll-hint {
    display: none;
    text-align: center;
    padding: .5rem;
    background: #e3f2fd;
    border-radius: .25rem;
    margin-bottom: .5rem;
    font-size: .875rem;
    color: #1976d2;
  }

  @media (max-width: 992px) {
    .scroll-hint { display: block; }
  }

  /* ======== MOBILE / TABLET CARDS (tipo app) ======== */

  .desktop-table-wrapper {
    display: block;
  }

  .tour-mobile-list {
    display: none;
  }

  /* Hasta tablets: usamos cards; desktop real: tabla */
  @media (max-width: 991.98px) {
    .desktop-table-wrapper {
      display: none;
    }

    .tour-mobile-list {
      display: flex;
      flex-direction: column;
      gap: .75rem;
    }

    /* Ocultamos barra de fuente y hint en mobile/tablet */
    .font-toolbar,
    .scroll-hint {
      display: none !important;
    }
  }

  .tour-mobile-card {
    background: #111827;
    border-radius: 16px;
    box-shadow: 0 6px 18px rgba(15, 23, 42, 0.7);
    overflow: hidden;
    border: 1px solid #1f2937;
  }

  .tour-mobile-main {
    display: flex;
    align-items: center;
    padding: .75rem .9rem;
    gap: .75rem;
    cursor: pointer;
  }

  .tour-mobile-main:active {
    background: #020617;
  }

  .tour-mobile-thumb {
    width: 56px;
    height: 56px;
    border-radius: 14px;
    overflow: hidden;
    flex-shrink: 0;
    background: linear-gradient(135deg, #4f46e5, #06b6d4);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #e5e7eb;
    font-weight: 600;
    font-size: .9rem;
    text-transform: uppercase;
  }

  .tour-mobile-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .tour-mobile-info {
    flex: 1;
    min-width: 0;
  }

  .tour-mobile-title {
    font-weight: 600;
    font-size: .98rem;
    color: #f9fafb;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .tour-mobile-type {
    font-size: .75rem;
    color: #9ca3af;
  }

  .tour-mobile-slug {
    font-size: .75rem;
    color: #6b7280;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .tour-mobile-meta {
    display: flex;
    align-items: center;
    margin-top: .25rem;
    gap: .35rem;
    flex-wrap: wrap;
  }

  .tour-mobile-price {
    font-weight: 600;
    font-size: .88rem;
    color: #f97316;
  }

  .tour-mobile-status-badge {
    font-size: .7rem;
    padding: .15rem .45rem;
    border-radius: 999px;
  }

  .tour-mobile-chevron {
    flex-shrink: 0;
    color: #9ca3af;
    transition: transform .2s ease;
  }

  .tour-mobile-chevron.rotated {
    transform: rotate(180deg);
  }

  .tour-mobile-body {
    padding: .65rem .9rem .85rem;
    border-top: 1px solid #1f2937;
    background: #020617;
    color: #e5e7eb;
  }

  .tour-mobile-body .section-label {
    font-size: .75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: #9ca3af;
    margin-bottom: .25rem;
  }

  .tour-mobile-body .badge {
    font-size: .75rem;
  }

  .tour-mobile-meta-row {
    display: flex;
    gap: .35rem;
    flex-wrap: wrap;
    margin-bottom: .35rem;
  }

  .tour-mobile-meta-chip {
    font-size: .75rem;
    padding: .1rem .45rem;
    border-radius: 999px;
    background: #1f2937;
    color: #e5e7eb;
  }

  .tour-mobile-actions {
    display: flex;
    flex-wrap: wrap;
    gap: .4rem;
    margin-top: .5rem;
  }

  .tour-mobile-actions .btn-sm {
    border-radius: 999px;
    padding: .35rem .6rem;
    display: inline-flex;
    align-items: center;
    gap: .25rem;
    font-size: .78rem;
  }

  .tour-mobile-actions .btn-sm i {
    font-size: .9em;
  }
</style>
@endpush

@include('admin.carts.cartmodal')

@php
use Illuminate\Support\Facades\Storage;

/**
 * Portada del tour (igual que índice público)
 */
$coverFromFolder = function (?int $tourId): string {
    if (!$tourId) {
        return asset('images/volcano.png');
    }

    $folder = "tours/{$tourId}/gallery";

    if (!Storage::disk('public')->exists($folder)) {
        return asset('images/volcano.png');
    }

    $allowed = ['jpg', 'jpeg', 'png', 'webp'];

    $first = collect(Storage::disk('public')->files($folder))
        ->filter(fn ($p) => in_array(strtolower(pathinfo($p, PATHINFO_EXTENSION)), $allowed, true))
        ->sort(fn ($a, $b) => strnatcasecmp($a, $b))
        ->first();

    return $first ? asset('storage/' . $first) : asset('images/volcano.png');
};
@endphp

{{-- Toolbar de tamaño de fuente (solo desktop) --}}
@once
<div class="font-toolbar">
  <button class="btn btn-outline-secondary btn-sm" id="fontSmaller" type="button"
          title="{{ __('m_tours.common.font_decrease') }}"
          aria-label="{{ __('m_tours.common.font_decrease') }}">
    A−
  </button>
  <div class="size-indicator" id="fontIndicator" aria-live="polite">100%</div>
  <button class="btn btn-outline-secondary btn-sm" id="fontBigger" type="button"
          title="{{ __('m_tours.common.font_increase') }}"
          aria-label="{{ __('m_tours.common.font_increase') }}">
    A+
  </button>
</div>
@endonce

{{-- Indicador de scroll (solo desktop) --}}
<div class="scroll-hint">
  <i class="fas fa-arrows-alt-h me-1"></i>
  {{ __('m_tours.tour.ui.scroll_hint') }}
</div>

{{-- ================= DESKTOP TABLE ================= --}}
<div class="desktop-table-wrapper">
  <div class="table-responsive-custom">
    <table class="table table-sm table-bordered table-striped table-hover" id="toursTable">
      <thead class="bg-primary text-white">
        <tr>
          <th style="min-width:60px;">{{ __('m_tours.tour.table.id') }}</th>
          <th style="min-width:180px;">{{ __('m_tours.tour.table.name') }}</th>
          <th style="min-width:150px;">{{ __('m_tours.tour.table.schedules') }}</th>
          <th style="min-width:180px;">{{ __('m_tours.tour.table.prices') }}</th>
          <th style="min-width:100px;">{{ __('m_tours.tour.table.capacity') }}</th>
          <th style="min-width:100px;">{{ __('m_tours.tour.table.group_size') }}</th>
          <th style="min-width:90px;">{{ __('m_tours.tour.table.status') }}</th>
          <th style="min-width:280px;">{{ __('m_tours.tour.table.actions') }}</th>
        </tr>
      </thead>
      <tbody id="toursTbody">
        @foreach($tours as $tour)
          @php
            $isArchived  = !is_null($tour->deleted_at ?? null);
            $hasBookings = (int) ($tour->bookings_count ?? 0);
            $currency    = config('app.currency_symbol', '$');
            $locale      = app()->getLocale();

            $activePrices = $tour->prices
              ->filter(fn($p) => $p->is_active && $p->category && $p->category->is_active)
              ->sortBy('category.order');

            $mainPrice      = $activePrices->first();
            $mainPriceLabel = null;
            $mainPriceValue = null;

            if ($mainPrice) {
                $cat = $mainPrice->category;

                $mainPriceLabel = method_exists($cat, 'getTranslatedName')
                    ? ($cat->getTranslatedName($locale) ?: null)
                    : null;

                if (!$mainPriceLabel && !empty($cat->slug)) {
                    foreach ([
                        'customer_categories.labels.' . $cat->slug,
                        'm_tours.customer_categories.labels.' . $cat->slug,
                    ] as $k) {
                        $tr = __($k);
                        if ($tr !== $k) { $mainPriceLabel = $tr; break; }
                    }
                }

                if (!$mainPriceLabel) {
                    $mainPriceLabel = $cat->name ?? $cat->slug ?? '';
                }

                $mainPriceValue = $currency . number_format($mainPrice->price, 2);
            }

            $slug = $tour->slug ?? $tour->tour_slug ?? null;

            $thumb = optional($tour->coverImage)->url
              ?? $coverFromFolder($tour->tour_id ?? $tour->id ?? null);
          @endphp

          <tr>
            <td>{{ $tour->tour_id }}</td>

            <td>
              <strong>
                {{ method_exists($tour, 'getTranslatedName') ? $tour->getTranslatedName($locale) : $tour->name }}
              </strong>
              @if($tour->tourType)
                <br>
                <small class="text-muted">{{ $tour->tourType->name }}</small>
              @endif
            </td>

            <td>
              @forelse($tour->schedules->sortBy('start_time') as $schedule)
                <span class="badge bg-success schedule-badge">
                  {{ date('g:i A', strtotime($schedule->start_time)) }}
                </span>
              @empty
                <span class="text-muted">{{ __('m_tours.tour.ui.no_schedules') }}</span>
              @endforelse
            </td>

            <td>
              @if($activePrices->isNotEmpty())
                @foreach($activePrices as $price)
                  @php
                    $cat = $price->category;

                    $catLabel = method_exists($cat, 'getTranslatedName')
                        ? ($cat->getTranslatedName($locale) ?: null)
                        : null;

                    if (!$catLabel && !empty($cat->slug)) {
                        foreach ([
                          'customer_categories.labels.' . $cat->slug,
                          'm_tours.customer_categories.labels.' . $cat->slug,
                        ] as $k) {
                          $tr = __($k);
                          if ($tr !== $k) { $catLabel = $tr; break; }
                        }
                    }

                    if (!$catLabel) { $catLabel = $cat->name ?? $cat->slug ?? ''; }
                  @endphp

                  <div class="price-item">
                    <span class="price-label">
                      {{ $catLabel }}
                      <span class="price-range">({{ $price->min_quantity }}-{{ $price->max_quantity }})</span>
                    </span>
                    <span class="price-value">{{ $currency }}{{ number_format($price->price, 2) }}</span>
                  </div>
                @endforeach
              @else
                <span class="text-muted">{{ __('m_tours.tour.ui.no_prices') }}</span>
              @endif
            </td>

            <td class="text-center">
              <span class="badge bg-info">
                {{ $tour->max_capacity }} {{ __('m_tours.common.people') }}
              </span>
            </td>

            <td class="text-center">
              <span class="badge text-bg-light">
                {{ $tour->group_size ? $tour->group_size.' '. __('m_tours.common.people') : __('m_tours.common.na') }}
              </span>
            </td>

            <td class="text-center">
              <span class="badge {{ $tour->is_active ? 'bg-success' : 'bg-secondary' }}">
                {{ $tour->is_active ? __('m_tours.common.active') : __('m_tours.common.inactive') }}
              </span>
            </td>

            <td class="actions-cell">
              <div class="d-flex flex-wrap">
                <button type="button"
                  class="btn btn-primary btn-sm"
                  data-toggle="modal"
                  data-target="#modalCart{{ $tour->tour_id }}"
                  title="{{ __('m_tours.tour.ui.add_to_cart') }}"
                  aria-label="{{ __('m_tours.tour.ui.add_to_cart') }}">
                  <i class="fas fa-cart-plus"></i>
                </button>

                <a href="{{ route('admin.tours.edit', $tour) }}"
                   class="btn btn-warning btn-sm"
                   title="{{ __('m_tours.tour.ui.edit') }}"
                   aria-label="{{ __('m_tours.tour.ui.edit') }}">
                  <i class="fas fa-edit"></i>
                </a>

                @unless($isArchived)
                  <form action="{{ route('admin.tours.toggle', $tour) }}"
                        method="POST"
                        class="d-inline js-toggle-form"
                        data-question="{{ $tour->is_active ? __('m_tours.tour.alerts.toggle_question_active') : __('m_tours.tour.alerts.toggle_question_inactive') }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                            class="btn btn-sm btn-{{ $tour->is_active ? 'success' : 'secondary' }}"
                            title="{{ $tour->is_active ? __('m_tours.tour.ui.deactivate') : __('m_tours.tour.ui.activate') }}"
                            aria-label="{{ $tour->is_active ? __('m_tours.tour.ui.deactivate') : __('m_tours.tour.ui.activate') }}">
                      <i class="fas fa-toggle-{{ $tour->is_active ? 'on' : 'off' }}"></i>
                    </button>
                  </form>
                @endunless

                <a href="{{ route('admin.tours.prices.index', $tour) }}"
                   class="btn btn-info btn-sm"
                   title="{{ __('m_tours.tour.ui.manage_prices') }}"
                   aria-label="{{ __('m_tours.tour.ui.manage_prices') }}">
                  <i class="fas fa-dollar-sign"></i>
                </a>

                <a href="{{ route('admin.tours.images.index', $tour) }}"
                   class="btn btn-secondary btn-sm"
                   title="{{ __('m_tours.tour.ui.manage_images') }}"
                   aria-label="{{ __('m_tours.tour.ui.manage_images') }}">
                  <i class="fas fa-images"></i>
                </a>

                @unless($isArchived)
                  <form id="delete-form-{{ $tour->tour_id }}"
                        action="{{ route('admin.tours.destroy', $tour) }}"
                        method="POST"
                        class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="button"
                            class="btn btn-danger btn-sm"
                            title="{{ __('m_tours.tour.ui.delete') }}"
                            aria-label="{{ __('m_tours.tour.ui.delete') }}"
                            onclick="confirmDelete({{ $tour->tour_id }})">
                      <i class="fas fa-trash-alt"></i>
                    </button>
                  </form>
                @endunless

                @if($isArchived)
                  <form action="{{ route('admin.tours.restore', $tour->tour_id) }}"
                        method="POST"
                        class="d-inline">
                    @csrf
                    <button type="submit"
                            class="btn btn-success btn-sm"
                            title="{{ __('m_tours.tour.ui.restore') }}"
                            aria-label="{{ __('m_tours.tour.ui.restore') }}">
                      <i class="fas fa-undo"></i>
                    </button>
                  </form>

                  <form id="purge-form-{{ $tour->tour_id }}"
                        action="{{ route('admin.tours.purge', $tour->tour_id) }}"
                        method="POST"
                        class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="button"
                            class="btn btn-outline-danger btn-sm"
                            title="{{ __('m_tours.tour.ui.purge') }}"
                            aria-label="{{ __('m_tours.tour.ui.purge') }}"
                            onclick="confirmPurge({{ $tour->tour_id }}, {{ $hasBookings }})">
                      <i class="fas fa-times"></i>
                    </button>
                  </form>
                @endif
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  @if($tours instanceof \Illuminate\Contracts\Pagination\Paginator)
    <div class="mt-3" id="paginationLinks">
      {{ $tours->withQueryString()->links() }}
    </div>
  @endif
</div>

{{-- ================= MOBILE/TABLET LIST (CARDS) ================= --}}
<div class="tour-mobile-list">
  @foreach($tours as $tour)
    @php
      $isArchived  = !is_null($tour->deleted_at ?? null);
      $hasBookings = (int) ($tour->bookings_count ?? 0);
      $currency    = config('app.currency_symbol', '$');
      $locale      = app()->getLocale();

      $activePrices = $tour->prices
        ->filter(fn($p) => $p->is_active && $p->category && $p->category->is_active)
        ->sortBy('category.order');

      // ==== NUEVO: resumen con TODAS las categorías ====
      $pricesSummary = null;
      if ($activePrices->isNotEmpty()) {
          $chunks = [];
          foreach ($activePrices as $p) {
              $cat = $p->category;

              $label = method_exists($cat, 'getTranslatedName')
                  ? ($cat->getTranslatedName($locale) ?: null)
                  : null;

              if (!$label && !empty($cat->slug)) {
                  foreach ([
                      'customer_categories.labels.' . $cat->slug,
                      'm_tours.customer_categories.labels.' . $cat->slug,
                  ] as $k) {
                      $tr = __($k);
                      if ($tr !== $k) { $label = $tr; break; }
                  }
              }

              if (!$label) { $label = $cat->name ?? $cat->slug ?? ''; }

              $amount = $currency . number_format($p->price, 2);
              $chunks[] = "{$label}: {$amount}";
          }
          $pricesSummary = implode(' · ', $chunks);
      }

      $slug = $tour->slug ?? $tour->tour_slug ?? null;

      $thumb = optional($tour->coverImage)->url
        ?? $coverFromFolder($tour->tour_id ?? $tour->id ?? null);

      $title = method_exists($tour, 'getTranslatedName')
        ? ($tour->getTranslatedName($locale) ?: $tour->name)
        : ($tour->name ?? '');

      $initials = mb_substr($title ?: 'T', 0, 2);
    @endphp

    <div class="tour-mobile-card">
      {{-- HEADER / MAIN --}}
      <div class="tour-mobile-main"
           data-toggle="collapse"
           data-target="#tourMobileDetails{{ $tour->tour_id }}"
           aria-expanded="false"
           aria-controls="tourMobileDetails{{ $tour->tour_id }}">
        <div class="tour-mobile-thumb">
          @if($thumb)
            <img src="{{ $thumb }}" alt="{{ $title }}">
          @else
            {{ $initials }}
          @endif
        </div>

        <div class="tour-mobile-info">
          <div class="tour-mobile-title">{{ $title }}</div>
          @if($tour->tourType)
            <div class="tour-mobile-type">{{ $tour->tourType->name }}</div>
          @endif
          @if($slug)
            <div class="tour-mobile-slug">{{ $slug }}</div>
          @endif
          <div class="tour-mobile-meta">
            <span class="tour-mobile-status-badge badge
              {{ $tour->is_active ? 'bg-success' : 'bg-secondary' }}">
              {{ $tour->is_active ? __('m_tours.common.active') : __('m_tours.common.inactive') }}
            </span>

            @if($pricesSummary)
              <span class="tour-mobile-price">
                {{ $pricesSummary }}
              </span>
            @else
              <span class="text-muted" style="font-size:.7rem;">
                {{ __('m_tours.tour.ui.no_prices') }}
              </span>
            @endif
          </div>
        </div>

        <div class="tour-mobile-chevron" id="chevron-{{ $tour->tour_id }}">
          <i class="fas fa-chevron-down"></i>
        </div>
      </div>

      {{-- BODY / DETAILS --}}
      <div id="tourMobileDetails{{ $tour->tour_id }}" class="collapse">
        <div class="tour-mobile-body">
          {{-- Horarios --}}
          <div class="mb-2">
            <div class="section-label">{{ __('m_tours.tour.table.schedules') }}</div>
            @forelse($tour->schedules->sortBy('start_time') as $schedule)
              <span class="badge bg-success me-1 mb-1">
                {{ date('g:i A', strtotime($schedule->start_time)) }}
              </span>
            @empty
              <span class="text-muted">{{ __('m_tours.tour.ui.no_schedules') }}</span>
            @endforelse
          </div>

          {{-- Precios detallados --}}
          <div class="mb-2">
            <div class="section-label">{{ __('m_tours.tour.table.prices') }}</div>
            @if($activePrices->isNotEmpty())
              @foreach($activePrices as $p)
                @php
                  $cat = $p->category;

                  $label = method_exists($cat, 'getTranslatedName')
                      ? ($cat->getTranslatedName($locale) ?: null)
                      : null;

                  if (!$label && !empty($cat->slug)) {
                      foreach ([
                          'customer_categories.labels.' . $cat->slug,
                          'm_tours.customer_categories.labels.' . $cat->slug,
                      ] as $k) {
                          $tr = __($k);
                          if ($tr !== $k) { $label = $tr; break; }
                      }
                  }

                  if (!$label) { $label = $cat->name ?? $cat->slug ?? ''; }

                  $amount = $currency . number_format($p->price, 2);
                @endphp
                <span class="tour-mobile-meta-chip mb-1">
                  {{ $label }}: {{ $amount }}
                  ({{ $p->min_quantity }}-{{ $p->max_quantity }})
                </span>
              @endforeach
            @else
              <span class="text-muted">{{ __('m_tours.tour.ui.no_prices') }}</span>
            @endif
          </div>

          {{-- Capacidades --}}
          <div class="tour-mobile-meta-row">
            <span class="tour-mobile-meta-chip">
              {{ __('m_tours.tour.table.capacity') }}:
              {{ $tour->max_capacity }} {{ __('m_tours.common.people') }}
            </span>
            <span class="tour-mobile-meta-chip">
              {{ __('m_tours.tour.table.group_size') }}:
              {{ $tour->group_size ? $tour->group_size.' '. __('m_tours.common.people') : __('m_tours.common.na') }}
            </span>
          </div>

          {{-- Acciones --}}
          <div class="tour-mobile-actions">
            <button type="button"
                    class="btn btn-primary btn-sm"
                    data-toggle="modal"
                    data-target="#modalCart{{ $tour->tour_id }}">
              <i class="fas fa-cart-plus"></i>
              {{ __('m_tours.tour.ui.add_to_cart') }}
            </button>

            <a href="{{ route('admin.tours.edit', $tour) }}"
               class="btn btn-warning btn-sm">
              <i class="fas fa-edit"></i>
              {{ __('m_tours.tour.ui.edit') }}
            </a>

            @unless($isArchived)
              <form action="{{ route('admin.tours.toggle', $tour) }}"
                    method="POST"
                    class="d-inline js-toggle-form"
                    data-question="{{ $tour->is_active ? __('m_tours.tour.alerts.toggle_question_active') : __('m_tours.tour.alerts.toggle_question_inactive') }}">
                @csrf
                @method('PATCH')
                <button type="submit"
                        class="btn btn-sm btn-{{ $tour->is_active ? 'success' : 'secondary' }}">
                  <i class="fas fa-toggle-{{ $tour->is_active ? 'on' : 'off' }}"></i>
                  {{ $tour->is_active ? __('m_tours.tour.ui.deactivate') : __('m_tours.tour.ui.activate') }}
                </button>
              </form>
            @endunless

            <a href="{{ route('admin.tours.prices.index', $tour) }}"
               class="btn btn-info btn-sm">
              <i class="fas fa-dollar-sign"></i>
              {{ __('m_tours.tour.ui.manage_prices') }}
            </a>

            <a href="{{ route('admin.tours.images.index', $tour) }}"
               class="btn btn-secondary btn-sm">
              <i class="fas fa-images"></i>
              {{ __('m_tours.tour.ui.manage_images') }}
            </a>

            @unless($isArchived)
              <form id="delete-form-mobile-{{ $tour->tour_id }}"
                    action="{{ route('admin.tours.destroy', $tour) }}"
                    method="POST"
                    class="d-inline">
                @csrf
                @method('DELETE')
                <button type="button"
                        class="btn btn-danger btn-sm"
                        onclick="confirmDelete({{ $tour->tour_id }})">
                  <i class="fas fa-trash-alt"></i>
                  {{ __('m_tours.tour.ui.delete') }}
                </button>
              </form>
            @endunless

            @if($isArchived)
              <form action="{{ route('admin.tours.restore', $tour->tour_id) }}"
                    method="POST"
                    class="d-inline">
                @csrf
                <button type="submit"
                        class="btn btn-success btn-sm">
                  <i class="fas fa-undo"></i>
                  {{ __('m_tours.tour.ui.restore') }}
                </button>
              </form>

              <form id="purge-form-mobile-{{ $tour->tour_id }}"
                    action="{{ route('admin.tours.purge', $tour->tour_id) }}"
                    method="POST"
                    class="d-inline">
                @csrf
                @method('DELETE')
                <button type="button"
                        class="btn btn-outline-danger btn-sm"
                        onclick="confirmPurge({{ $tour->tour_id }}, {{ $hasBookings }})">
                  <i class="fas fa-times"></i>
                  {{ __('m_tours.tour.ui.purge') }}
                </button>
              </form>
            @endif
          </div>
        </div>
      </div>
    </div>
  @endforeach

  @if($tours instanceof \Illuminate\Contracts\Pagination\Paginator)
    <div class="mt-3" id="paginationLinksMobile">
      {{ $tours->withQueryString()->links() }}
    </div>
  @endif
</div>

@push('js')
<script>
  function confirmDelete(id) {
    const form =
      document.getElementById('delete-form-' + id) ||
      document.getElementById('delete-form-mobile-' + id);

    if (!form) return;

    const run = () => {
      Swal.fire({
        title: @json(__('m_tours.tour.alerts.delete_title')),
        text:  @json(__('m_tours.tour.alerts.delete_text')),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: @json(__('m_tours.common.confirm_delete')),
        cancelButtonText:  @json(__('m_tours.common.cancel')),
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d'
      }).then((result) => {
        if (result.isConfirmed) {
          form.submit();
        }
      });
    };
    if (window.Swal) run(); else setTimeout(run, 300);
  }

  function confirmPurge(id, hasBookings) {
    const form =
      document.getElementById('purge-form-' + id) ||
      document.getElementById('purge-form-mobile-' + id);

    if (!form) return;

    const extra = hasBookings > 0
      ? `<div class="mt-2 text-start">
           <strong>{{ __('m_tours.common.warning') }}:</strong>
           {{ __('m_tours.tour.alerts.purge_text_with_bookings', ['count' => '___COUNT___']) }}
         </div>`.replace('___COUNT___', hasBookings)
      : '';

    const run = () => {
      Swal.fire({
        title: @json(__('m_tours.tour.alerts.purge_title')),
        html:  @json(__('m_tours.tour.alerts.purge_text')) + extra,
        icon: 'error',
        showCancelButton: true,
        confirmButtonText: @json(__('m_tours.common.confirm_delete')),
        cancelButtonText:  @json(__('m_tours.common.cancel')),
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d'
      }).then((result) => {
        if (result.isConfirmed) {
          form.submit();
        }
      });
    };
    if (window.Swal) run(); else setTimeout(run, 300);
  }

  document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('submit', function(ev) {
      const form = ev.target;
      if (!form.matches('.js-toggle-form')) return;
      ev.preventDefault();
      const question = form.dataset.question || @json(__('m_tours.common.confirm_action'));
      const run = () => {
        Swal.fire({
          icon: 'question',
          title: @json(__('m_tours.common.confirm')),
          text: question,
          showCancelButton: true,
          confirmButtonText: @json(__('m_tours.common.yes')),
          cancelButtonText:  @json(__('m_tours.common.cancel')),
          confirmButtonColor: '#0d6efd',
          cancelButtonColor: '#6c757d'
        }).then(res => {
          if (res.isConfirmed) form.submit();
        });
      };
      if (window.Swal) run(); else setTimeout(run, 300);
    });

    const root = document.documentElement;
    const indicator = document.getElementById('fontIndicator');
    const LS_KEY = 'toursTableFontPct';

    function setPct(pct) {
      pct = Math.max(70, Math.min(150, pct));
      const rem = (pct / 100).toFixed(3) + 'rem';
      root.style.setProperty('--tbl-font-size', rem);
      let mult = 2.2;
      if (pct <= 95) mult = 2.1;
      if (pct <= 90) mult = 2.0;
      if (pct <= 85) mult = 1.9;
      if (pct <= 80) mult = 1.8;
      root.style.setProperty('--btn-cell-mult', String(mult));
      if (indicator) indicator.textContent = pct + '%';
      localStorage.setItem(LS_KEY, String(pct));
    }

    const saved = parseInt(localStorage.getItem(LS_KEY) || '100', 10);
    setPct(saved);

    document.getElementById('fontSmaller')?.addEventListener('click', () => {
      const current = parseInt(localStorage.getItem(LS_KEY) || '100', 10);
      setPct(current - 5);
    });
    document.getElementById('fontBigger')?.addEventListener('click', () => {
      const current = parseInt(localStorage.getItem(LS_KEY) || '100', 10);
      setPct(current + 5);
    });

    // Rotar chevron de los acordeones mobile (Bootstrap 4: data-target)
    document.querySelectorAll('.tour-mobile-main').forEach(el => {
      const targetSel = el.getAttribute('data-target') || el.getAttribute('data-bs-target');
      const tgt = targetSel ? document.querySelector(targetSel) : null;
      const chevron = el.querySelector('.tour-mobile-chevron');

      if (!tgt || !chevron) return;

      tgt.addEventListener('show.bs.collapse', () => {
        chevron.classList.add('rotated');
      });

      tgt.addEventListener('hide.bs.collapse', () => {
        chevron.classList.remove('rotated');
      });
    });
  });
</script>
@endpush
