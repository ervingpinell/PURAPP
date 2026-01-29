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

  /* NUEVO: badge compacto por regla de precio (desktop) */
  .price-rule-badge {
    font-size: .75rem;
    padding: .2rem .55rem;
    border-radius: 999px;
    margin: .15rem .25rem .15rem 0;
    cursor: default;
    background-color: #3f6791;
    color: #f9fafb;
    border: none;
  }

  .price-rule-badge i {
    font-size: .8em;
    margin-right: .25rem;
    color: #bfdbfe;
  }

  .table-striped tbody tr:hover {
    background-color: rgba(0, 0, 0, .03);
  }

  @media (max-width: 992px) {
    :root {
      --btn-cell-mult: 2.0;
    }
  }

  @media (max-width: 768px) {
    :root {
      --btn-cell-mult: 1.8;
    }

    .actions-cell {
      min-width: 240px;
    }
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
    .scroll-hint {
      display: block;
    }
  }

  /* ======== MOBILE / TABLET CARDS (tipo app) ======== */

  .desktop-table-wrapper {
    display: block;
  }

  .product-mobile-list {
    display: none;
  }

  /* Hasta tablets: usamos cards; desktop real: tabla */
  @media (max-width: 991.98px) {
    .desktop-table-wrapper {
      display: none;
    }

    .product-mobile-list {
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

  .product-mobile-card {
    background: #111827;
    border-radius: 16px;
    box-shadow: 0 6px 18px rgba(15, 23, 42, 0.7);
    overflow: hidden;
    border: 1px solid #1f2937;
  }

  .product-mobile-main {
    display: flex;
    align-items: center;
    padding: .75rem .9rem;
    gap: .75rem;
    cursor: pointer;
  }

  .product-mobile-main:active {
    background: #020617;
  }

  .product-mobile-thumb {
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

  .product-mobile-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .product-mobile-info {
    flex: 1;
    min-width: 0;
  }

  .product-mobile-title {
    font-weight: 600;
    font-size: .98rem;
    color: #f9fafb;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .product-mobile-type {
    font-size: .75rem;
    color: #9ca3af;
  }

  .product-mobile-slug {
    font-size: .75rem;
    color: #6b7280;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .product-mobile-meta {
    display: flex;
    align-items: center;
    margin-top: .25rem;
    gap: .35rem;
    flex-wrap: wrap;
  }

  .product-mobile-status-badge {
    font-size: .7rem;
    padding: .15rem .45rem;
    border-radius: 999px;
  }

  .product-mobile-chevron {
    flex-shrink: 0;
    color: #9ca3af;
    transition: transform .2s ease;
  }

  .product-mobile-chevron.rotated {
    transform: rotate(180deg);
  }

  .product-mobile-body {
    padding: .65rem .9rem .85rem;
    border-top: 1px solid #1f2937;
    background: #020617;
    color: #e5e7eb;
  }

  .product-mobile-body .section-label {
    font-size: .75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: #9ca3af;
    margin-bottom: .25rem;
  }

  .product-mobile-body .badge {
    font-size: .75rem;
  }

  .product-mobile-meta-row {
    display: flex;
    gap: .35rem;
    flex-wrap: wrap;
    margin-bottom: .35rem;
  }

  .product-mobile-meta-chip {
    font-size: .75rem;
    padding: .1rem .45rem;
    border-radius: 999px;
    background: #1f2937;
    color: #e5e7eb;
  }

  .product-mobile-actions {
    display: flex;
    flex-wrap: wrap;
    gap: .4rem;
    margin-top: .5rem;
  }

  .product-mobile-actions .btn-sm {
    border-radius: 999px;
    padding: .35rem .6rem;
    display: inline-flex;
    align-items: center;
    gap: .25rem;
    font-size: .78rem;
  }

  .product-mobile-actions .btn-sm i {
    font-size: .9em;
  }
</style>
@endpush

@include('admin.carts.cartmodal')

@php
use Illuminate\Support\Facades\Storage;

/**
* Portada del product (igual que índice público)
*/
$coverFromFolder = function (?int $productId): string {
if (!$productId) {
return asset('images/volcano.png');
}

$folder = "tours/{$productId}/gallery";

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
  {{ __('m_tours.product.ui.scroll_hint') }}
</div>

{{-- ================= DESKTOP TABLE ================= --}}
<div class="desktop-table-wrapper">
  <div class="table-responsive-custom">
    <table class="table table-sm table-bordered table-striped table-hover" id="toursTable">
      <thead class="bg-primary text-white">
        <tr>
          <th style="min-width:60px;">{{ __('m_tours.product.table.id') }}</th>
          <th style="min-width:180px;">{{ __('m_tours.product.table.name') }}</th>
          <th style="min-width:150px;">{{ __('m_tours.product.table.schedules') }}</th>
          <th style="min-width:180px;">{{ __('m_tours.product.table.prices') }}</th>
          <th style="min-width:100px;">{{ __('m_tours.product.table.capacity') }}</th>
          <th style="min-width:100px;">{{ __('m_tours.product.table.group_size') }}</th>
          <th style="min-width:90px;">{{ __('m_tours.product.table.status') }}</th>
          <th style="min-width:280px;">{{ __('m_tours.product.table.actions') }}</th>
        </tr>
      </thead>
      <tbody id="toursTbody">
        @foreach($products as $product)
        @php
        $isArchived = !is_null($product->deleted_at ?? null);
        $hasBookings = (int) ($product->bookings_count ?? 0);
        $currency = config('app.currency_symbol', '$');
        $locale = app()->getLocale();

        $activePrices = $product->prices
        ->filter(fn($p) => $p->is_active && $p->category && $p->category->is_active)
        ->sortBy('category.order');

        // ====== Agrupar precios por regla (default / rango de fechas) ======
        $groupedPriceRules = $activePrices
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
        // que el default quede al final
        return '9999-12-31';
        }
        $from = $first->valid_from ?? null;
        return $from instanceof \Carbon\Carbon ? $from->format('d-M-Y') : ($from ?: '0000-01-01');
        });

        $slug = $product->slug ?? $product->tour_slug ?? null;

        $thumb = optional($product->coverImage)->url
        ?? $coverFromFolder($product->product_id ?? $product->id ?? null);
        @endphp

        <tr>
          <td>{{ $product->product_id }}</td>

          <td>
            <strong>
              {{ method_exists($product, 'getTranslatedName') ? $product->getTranslatedName($locale) : $product->name }}
            </strong>
            @if($product->productType)
            <br>
            <small class="text-muted">{{ $product->productType->name }}</small>
            @endif
          </td>

          <td>
            @forelse($product->schedules->sortBy('start_time') as $schedule)
            <span class="badge bg-success schedule-badge">
              {{ date('g:i A', strtotime($schedule->start_time)) }}
            </span>
            @empty
            <span class="text-muted">{{ __('m_tours.product.ui.no_schedules') }}</span>
            @endforelse
          </td>

          {{-- ====== PRECIOS DESKTOP: badges por regla con tooltip ====== --}}
          <td>
            @if($groupedPriceRules->isNotEmpty())
            @foreach($groupedPriceRules as $key => $pricesGroup)
            @php
            /** @var \App\Models\TourPrice $first */
            $first = $pricesGroup->first();
            $from = $first->valid_from ?? null;
            $until = $first->valid_until ?? null;

            $fromStr = $from instanceof \Carbon\Carbon ? $from->format('d-M-Y') : $from;
            $untilStr = $until instanceof \Carbon\Carbon ? $until->format('d-M-Y') : $until;

            // Calcular rango de fechas para tooltip o fallback
            $dateRangeStr = '';
            if ($fromStr && $untilStr) {
            $dateRangeStr = "{$fromStr} → {$untilStr}";
            } elseif ($fromStr) {
            $dateRangeStr = "Desde {$fromStr}";
            } elseif ($untilStr) {
            $dateRangeStr = "Hasta {$untilStr}";
            }

            // Determinar etiqueta a mostrar
            if (!empty($first->label)) {
            $label = $first->label;
            } elseif ($first->is_default) {
            $label = __('m_tours.product.summary.default_price_rule') !== 'm_tours.product.summary.default_price_rule'
            ? __('m_tours.product.summary.default_price_rule')
            : 'Precio por defecto';
            } else {
            $label = $dateRangeStr ?: 'Rango sin fechas';
            }

            // resumen para tooltip
            $summaryLines = [];

            // Agregar rango de fechas al inicio del tooltip si existe
            if ($dateRangeStr) {
            $summaryLines[] = "<strong>{$dateRangeStr}</strong>";
            $summaryLines[] = ""; // Línea vacía para separar
            }

            foreach ($pricesGroup as $p) {
            $cat = $p->category;

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

            $amount = $currency . number_format($p->price, 2);

            $summaryLines[] = e($catLabel) . ': ' . $amount .
            ' (' . $p->min_quantity . '-' . $p->max_quantity . ')';
            }

            $tooltipHtml = implode('<br>', $summaryLines);
            @endphp

            <span class="badge badge-primary price-rule-badge"
              data-toggle="tooltip"
              data-html="true"
              data-placement="top"
              title="{!! $tooltipHtml !!}">
              <i class="fas fa-calendar-alt mr-1 text-primary"></i>
              {{ $label }}
            </span>
            @endforeach
            @else
            <span class="text-muted">{{ __('m_tours.product.ui.no_prices') }}</span>
            @endif
          </td>

          <td class="text-center">
            <span class="badge bg-info">
              {{ $product->max_capacity }} {{ __('m_tours.common.people') }}
            </span>
          </td>

          <td class="text-center">
            <span class="badge text-bg-light">
              {{ $product->group_size ? $product->group_size.' '. __('m_tours.common.people') : __('m_tours.common.na') }}
            </span>
          </td>

          <td class="text-center">
            <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-secondary' }}">
              {{ $product->is_active ? __('m_tours.common.active') : __('m_tours.common.inactive') }}
            </span>
          </td>

          <td class="actions-cell">
            <div class="d-flex flex-wrap">


              @can('edit-tours')
              <a href="{{ route('admin.products.edit', $product) }}"
                class="btn btn-edit btn-sm"
                title="{{ __('m_tours.product.ui.edit') }}"
                aria-label="{{ __('m_tours.product.ui.edit') }}">
                <i class="fas fa-edit"></i>
              </a>

              <button type="button"
                class="btn btn-primary btn-sm"
                data-toggle="modal"
                data-target="#translateModal{{ $product->product_id }}"
                title="Traducir producto"
                aria-label="Traducir producto">
                <i class="fas fa-language"></i>
              </button>
              @endcan

              @unless($isArchived)
              @can('publish-tours')
              <form action="{{ route('admin.products.toggle', $product) }}"
                method="POST"
                class="d-inline js-toggle-form"
                data-question="{{ $product->is_active ? __('m_tours.product.alerts.toggle_question_active') : __('m_tours.product.alerts.toggle_question_inactive') }}">
                @csrf
                @method('PATCH')
                <button type="submit"
                  class="btn btn-sm btn-{{ $product->is_active ? 'toggle' : 'secondary' }}"
                  title="{{ $product->is_active ? __('m_tours.product.ui.deactivate') : __('m_tours.product.ui.activate') }}"
                  aria-label="{{ $product->is_active ? __('m_tours.product.ui.deactivate') : __('m_tours.product.ui.activate') }}">
                  <i class="fas fa-toggle-{{ $product->is_active ? 'on' : 'off' }}"></i>
                </button>
              </form>
              @endcan
              @endunless

              @can('view-product-prices')
              <a href="{{ route('admin.products.prices.index', $product) }}"
                class="btn btn-info btn-sm"
                title="{{ __('m_tours.product.ui.manage_prices') }}"
                aria-label="{{ __('m_tours.product.ui.manage_prices') }}">
                <i class="fas fa-dollar-sign"></i>
              </a>
              @endcan

              @can('edit-tours')
              <a href="{{ route('admin.products.images.index', $product) }}"
                class="btn btn-secondary btn-sm"
                title="{{ __('m_tours.product.ui.manage_images') }}"
                aria-label="{{ __('m_tours.product.ui.manage_images') }}">
                <i class="fas fa-images"></i>
              </a>
              @endcan

              @unless($isArchived)
              @can('delete-tours')
              @if(auth()->user() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('super-admin')))
              <form id="delete-form-{{ $product->product_id }}"
                action="{{ route('admin.products.destroy', $product) }}"
                method="POST"
                class="d-inline">
                @csrf
                @method('DELETE')
                <button type="button"
                  class="btn btn-danger btn-sm"
                  title="{{ __('m_tours.product.ui.delete') }}"
                  aria-label="{{ __('m_tours.product.ui.delete') }}"
                  onclick="confirmDelete({{ $product->product_id }})">
                  <i class="fas fa-trash-alt"></i>
                </button>
              </form>
              @endif
              @endcan
              @endunless

              @if($isArchived)
              @can('delete-tours')
              <form action="{{ route('admin.products.restore', $product->product_id) }}"
                method="POST"
                class="d-inline">
                @csrf
                <button type="submit"
                  class="btn btn-success btn-sm"
                  title="{{ __('m_tours.product.ui.restore') }}"
                  aria-label="{{ __('m_tours.product.ui.restore') }}">
                  <i class="fas fa-undo"></i>
                </button>
              </form>

              <form id="purge-form-{{ $product->product_id }}"
                action="{{ route('admin.products.purge', $product->product_id) }}"
                method="POST"
                class="d-inline">
                @csrf
                @method('DELETE')
                <button type="button"
                  class="btn btn-outline-danger btn-sm"
                  title="{{ __('m_tours.product.ui.purge') }}"
                  aria-label="{{ __('m_tours.product.ui.purge') }}"
                  onclick="confirmPurge({{ $product->product_id }}, {{ $hasBookings }})">
                  <i class="fas fa-times"></i>
                </button>
              </form>
              @endcan
              @endif
            </div>
          </td>
        </tr>

        {{-- Translation Modal --}}
        <div class="modal fade" id="translateModal{{ $product->product_id }}" tabindex="-1" role="dialog">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <form action="{{ route('admin.products.update', $product) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="product_type_id" value="{{ $product->product_type_id }}">
                
                <div class="modal-header">
                  <h5 class="modal-title">Traducir: {{ $product->name }}</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                
                <div class="modal-body">
                  {{-- Language Tabs --}}
                  <ul class="nav nav-tabs" role="tablist">
                    @foreach(['es' => 'ES', 'en' => 'EN', 'fr' => 'FR', 'pt' => 'PT', 'de' => 'DE'] as $loc => $label)
                    <li class="nav-item">
                      <a class="nav-link {{ $loop->first ? 'active' : '' }}" 
                         data-toggle="tab" 
                         href="#lang{{ $loc }}{{ $product->product_id }}" 
                         role="tab">
                        {{ $label }}
                      </a>
                    </li>
                    @endforeach
                  </ul>

                  {{-- Tab Content --}}
                  <div class="tab-content mt-3">
                    @foreach(['es', 'en', 'fr', 'pt', 'de'] as $loc)
                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" 
                         id="lang{{ $loc }}{{ $product->product_id }}" 
                         role="tabpanel">
                      
                      <div class="mb-3">
                        <label>Nombre ({{ strtoupper($loc) }})</label>
                        <input type="text" 
                               name="translations[{{ $loc }}][name]" 
                               class="form-control"
                               value="{{ $product->getTranslation('name', $loc, false) }}">
                      </div>

                      <div class="mb-3">
                        <label>Resumen / Overview ({{ strtoupper($loc) }})</label>
                        <textarea name="translations[{{ $loc }}][overview]" 
                                  class="form-control" 
                                  rows="5">{{ $product->getTranslation('overview', $loc, false) }}</textarea>
                      </div>

                      <div class="mb-3">
                        <label>Recomendaciones ({{ strtoupper($loc) }})</label>
                        <textarea name="translations[{{ $loc }}][recommendations]" 
                                  class="form-control" 
                                  rows="3">{{ $product->getTranslation('recommendations', $loc, false) }}</textarea>
                      </div>

                    </div>
                    @endforeach
                  </div>
                </div>
                
                <div class="modal-footer">
                  <button type="submit" class="btn btn-success">Guardar Traducciones</button>
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        @endforeach
      </tbody>
    </table>
  </div>

  @if($products instanceof \Illuminate\Contracts\Pagination\Paginator)
  <div class="mt-3" id="paginationLinks">
    {{ $products->withQueryString()->links() }}
  </div>
  @endif
</div>

{{-- ================= MOBILE/TABLET LIST (CARDS) ================= --}}
<div class="product-mobile-list">
  @foreach($products as $product)
  @php
  $isArchived = !is_null($product->deleted_at ?? null);
  $hasBookings = (int) ($product->bookings_count ?? 0);
  $currency = config('app.currency_symbol', '$');
  $locale = app()->getLocale();

  $activePrices = $product->prices
  ->filter(fn($p) => $p->is_active && $p->category && $p->category->is_active)
  ->sortBy('category.order');

  // Agrupamos igual que en desktop, para mostrar por regla con fechas
  $groupedPriceRulesMobile = $activePrices
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
  $first = $prices->first();
  if ($first->is_default) {
  return '9999-12-31';
  }
  $from = $first->valid_from ?? null;
  return $from instanceof \Carbon\Carbon ? $from->format('d-M-Y') : ($from ?: '0000-01-01');
  });

  $slug = $product->slug ?? $product->tour_slug ?? null;

  $thumb = optional($product->coverImage)->url
  ?? $coverFromFolder($product->product_id ?? $product->id ?? null);

  $title = method_exists($product, 'getTranslatedName')
  ? ($product->getTranslatedName($locale) ?: $product->name)
  : ($product->name ?? '');

  $initials = mb_substr($title ?: 'T', 0, 2);
  @endphp

  <div class="product-mobile-card">
    {{-- HEADER / MAIN --}}
    <div class="product-mobile-main"
      data-toggle="collapse"
      data-target="#tourMobileDetails{{ $product->product_id }}"
      aria-expanded="false"
      aria-controls="tourMobileDetails{{ $product->product_id }}">
      <div class="product-mobile-thumb">
        @if($thumb)
        <img src="{{ $thumb }}" alt="{{ $title }}">
        @else
        {{ $initials }}
        @endif
      </div>

      <div class="product-mobile-info">
        <div class="product-mobile-title">{{ $title }}</div>
        @if($product->productType)
        <div class="product-mobile-type">{{ $product->productType->name }}</div>
        @endif
        @if($slug)
        <div class="product-mobile-slug">{{ $slug }}</div>
        @endif
        <div class="product-mobile-meta">
          <span class="product-mobile-status-badge badge
                {{ $product->is_active ? 'bg-success' : 'bg-secondary' }}">
            {{ $product->is_active ? __('m_tours.common.active') : __('m_tours.common.inactive') }}
          </span>

          {{-- Ya no mostramos el resumen naranja aquí.
                 Si no hay precios, mostramos sólo el mensaje --}}
          @if($groupedPriceRulesMobile->isEmpty())
          <span class="text-muted" style="font-size:.7rem;">
            {{ __('m_tours.product.ui.no_prices') }}
          </span>
          @endif
        </div>
      </div>

      <div class="product-mobile-chevron" id="chevron-{{ $product->product_id }}">
        <i class="fas fa-chevron-down"></i>
      </div>
    </div>

    {{-- BODY / DETAILS --}}
    <div id="tourMobileDetails{{ $product->product_id }}" class="collapse">
      <div class="product-mobile-body">
        {{-- Horarios --}}
        <div class="mb-2">
          <div class="section-label">{{ __('m_tours.product.table.schedules') }}</div>
          @forelse($product->schedules->sortBy('start_time') as $schedule)
          <span class="badge bg-success me-1 mb-1">
            {{ date('g:i A', strtotime($schedule->start_time)) }}
          </span>
          @empty
          <span class="text-muted">{{ __('m_tours.product.ui.no_schedules') }}</span>
          @endforelse
        </div>

        {{-- Precios detallados agrupados por regla/fechas (como desktop) --}}
        <div class="mb-2">
          <div class="section-label">{{ __('m_tours.product.table.prices') }}</div>
          @if($groupedPriceRulesMobile->isNotEmpty())
          @foreach($groupedPriceRulesMobile as $key => $pricesGroup)
          @php
          $first = $pricesGroup->first();
          $from = $first->valid_from ?? null;
          $until = $first->valid_until ?? null;

          $fromStr = $from instanceof \Carbon\Carbon ? $from->format('d-M-Y') : $from;
          $untilStr = $until instanceof \Carbon\Carbon ? $until->format('d-M-Y') : $until;

          // Calcular rango de fechas
          $dateRangeStr = '';
          if ($fromStr && $untilStr) {
          $dateRangeStr = "{$fromStr} → {$untilStr}";
          } elseif ($fromStr) {
          $dateRangeStr = "Desde {$fromStr}";
          } elseif ($untilStr) {
          $dateRangeStr = "Hasta {$untilStr}";
          }

          if (!empty($first->label)) {
          $ruleLabel = $first->label;
          if ($dateRangeStr) {
          $ruleLabel .= " ({$dateRangeStr})";
          }
          } elseif ($first->is_default) {
          $ruleLabel = __('m_tours.product.summary.default_price_rule') !== 'm_tours.product.summary.default_price_rule'
          ? __('m_tours.product.summary.default_price_rule')
          : 'Precio por defecto';
          } else {
          $ruleLabel = $dateRangeStr ?: 'Rango sin fechas';
          }
          @endphp
          <div class="mb-2">
            <span class="badge badge-primary mb-1">
              <i class="fas fa-calendar-alt mr-1"></i>{{ $ruleLabel }}
            </span>
            <div class="product-mobile-meta-row">
              @foreach($pricesGroup as $p)
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
              <span class="product-mobile-meta-chip mb-1">
                {{ $label }}: {{ $amount }}
                ({{ $p->min_quantity }}-{{ $p->max_quantity }})
              </span>
              @endforeach
            </div>
          </div>
          @endforeach
          @else
          <span class="text-muted">{{ __('m_tours.product.ui.no_prices') }}</span>
          @endif
        </div>

        {{-- Capacidades --}}
        <div class="product-mobile-meta-row">
          <span class="product-mobile-meta-chip bg-success">
            {{ __('m_tours.product.table.capacity') }}:
            {{ $product->max_capacity }} {{ __('m_tours.common.people') }}
          </span>
          <span class="product-mobile-meta-chip bg-success">
            {{ __('m_tours.product.table.group_size') }}:
            {{ $product->group_size ? $product->group_size.' '. __('m_tours.common.people') : __('m_tours.common.na') }}
          </span>
        </div>

        {{-- Acciones --}}
        <div class="product-mobile-actions border-top pt-2 pb-2">


          @can('edit-tours')
          <a href="{{ route('admin.products.edit', $product) }}"
            class="btn btn-warning btn-sm">
            <i class="fas fa-edit"></i>
            {{ __('m_tours.product.ui.edit') }}
          </a>
          @endcan

          @unless($isArchived)
          @can('publish-tours')
          <form action="{{ route('admin.products.toggle', $product) }}"
            method="POST"
            class="d-inline js-toggle-form"
            data-question="{{ $product->is_active ? __('m_tours.product.alerts.toggle_question_active') : __('m_tours.product.alerts.toggle_question_inactive') }}">
            @csrf
            @method('PATCH')
            <button type="submit"
              class="btn btn-sm btn-{{ $product->is_active ? 'success' : 'secondary' }}">
              <i class="fas fa-toggle-{{ $product->is_active ? 'on' : 'off' }}"></i>
              {{ __('m_tours.product.ui.deactivate') }}
            </button>
          </form>
          @endcan
          @endunless

          @can('view-product-prices')
          <a href="{{ route('admin.products.prices.index', $product) }}"
            class="btn btn-info btn-sm">
            <i class="fas fa-dollar-sign"></i>
            {{ __('m_tours.product.ui.manage_prices') }}
          </a>
          @endcan

          @can('manage-product-images')
          <a href="{{ route('admin.products.images.index', $product) }}"
            class="btn btn-warning btn-sm">
            <i class="fas fa-images"></i>
            {{ __('m_tours.product.ui.gallery') }}
          </a>
          @endcan

          @unless($isArchived)
          @can('delete-tours')
          @if(auth()->user() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('super-admin')))
          <form id="delete-form-mobile-{{ $product->product_id }}"
            action="{{ route('admin.products.destroy', $product) }}"
            method="POST"
            class="d-inline">
            @csrf
            @method('DELETE')
            <button type="button"
              class="btn btn-danger btn-sm"
              onclick="confirmDelete({{ $product->product_id }})">
              <i class="fas fa-trash-alt"></i>
              {{ __('m_tours.product.ui.delete') }}
            </button>
          </form>
          @endif
          @endcan
          @endunless

          @if($isArchived)
          @can('delete-tours')
          <form action="{{ route('admin.products.restore', $product->product_id) }}"
            method="POST"
            class="d-inline">
            @csrf
            <button type="submit"
              class="btn btn-success btn-sm">
              <i class="fas fa-undo"></i>
              {{ __('m_tours.product.ui.restore') }}
            </button>
          </form>

          <form id="purge-form-mobile-{{ $product->product_id }}"
            action="{{ route('admin.products.purge', $product->product_id) }}"
            method="POST"
            class="d-inline">
            @csrf
            @method('DELETE')
            <button type="button"
              class="btn btn-outline-danger btn-sm"
              onclick="confirmPurge({{ $product->product_id }}, {{ $hasBookings }})">
              <i class="fas fa-times"></i>
              {{ __('m_tours.product.ui.purge') }}
            </button>
          </form>
          @endcan
          @endif
        </div>
      </div>
    </div>
  </div>
  @endforeach

  @if($products instanceof \Illuminate\Contracts\Pagination\Paginator)
  <div class="mt-3" id="paginationLinksMobile">
    {{ $products->withQueryString()->links() }}
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
        title: @json(__('m_tours.product.alerts.delete_title')),
        text: @json(__('m_tours.product.alerts.delete_text')),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: @json(__('m_tours.common.confirm_delete')),
        cancelButtonText: @json(__('m_tours.common.cancel')),
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d'
      }).then((result) => {
        if (result.isConfirmed) {
          form.submit();
        }
      });
    };
    if (window.Swal) run();
    else setTimeout(run, 300);
  }

  function confirmPurge(id, hasBookings) {
    const form =
      document.getElementById('purge-form-' + id) ||
      document.getElementById('purge-form-mobile-' + id);

    if (!form) return;

    const extra = hasBookings > 0 ?
      `<div class="mt-2 text-start">
           <strong>{{ __('m_tours.common.warning') }}:</strong>
           {{ __('m_tours.product.alerts.purge_text_with_bookings', ['count' => '___COUNT___']) }}
         </div>`.replace('___COUNT___', hasBookings) :
      '';

    const run = () => {
      Swal.fire({
        title: @json(__('m_tours.product.alerts.purge_title')),
        html: @json(__('m_tours.product.alerts.purge_text')) + extra,
        icon: 'error',
        showCancelButton: true,
        confirmButtonText: @json(__('m_tours.common.confirm_delete')),
        cancelButtonText: @json(__('m_tours.common.cancel')),
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d'
      }).then((result) => {
        if (result.isConfirmed) {
          form.submit();
        }
      });
    };
    if (window.Swal) run();
    else setTimeout(run, 300);
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
          cancelButtonText: @json(__('m_tours.common.cancel')),
          confirmButtonColor: '#0d6efd',
          cancelButtonColor: '#6c757d'
        }).then(res => {
          if (res.isConfirmed) form.submit();
        });
      };
      if (window.Swal) run();
      else setTimeout(run, 300);
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
    document.querySelectorAll('.product-mobile-main').forEach(el => {
      const targetSel = el.getAttribute('data-target') || el.getAttribute('data-target');
      const tgt = targetSel ? document.querySelector(targetSel) : null;
      const chevron = el.querySelector('.product-mobile-chevron');

      if (!tgt || !chevron) return;

      tgt.addEventListener('show.bs.collapse', () => {
        chevron.classList.add('rotated');
      });

      tgt.addEventListener('hide.bs.collapse', () => {
        chevron.classList.remove('rotated');
      });
    });

    // Tooltips Bootstrap 4
    if (window.jQuery && $.fn.tooltip) {
      $('[data-toggle="tooltip"]').tooltip({
        html: true
      });
    }
  });
</script>
@endpush