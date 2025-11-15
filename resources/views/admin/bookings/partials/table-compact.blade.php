{{-- resources/views/admin/bookings/partials/table-compact.blade.php --}}
@php
  use App\Models\CustomerCategory;

  $locale   = app()->getLocale();
  $currency = config('app.currency_symbol', '$');

  // Mapa de nombres traducidos por category_id y por slug para resolver rápido
  $allCats = CustomerCategory::active()
    ->with('translations') // para que getTranslatedName use la relación ya cargada
    ->get();

  $catNameById = $allCats->mapWithKeys(function($c) use ($locale) {
      return [$c->category_id => ($c->getTranslatedName($locale) ?: $c->slug ?: '')];
  })->all();

  $catNameBySlug = $allCats->filter(fn($c) => $c->slug)
    ->mapWithKeys(function($c) use ($locale) {
      $label = $c->getTranslatedName($locale);
      // fallback a archivo de idioma por slug
      if (!$label && $c->slug) {
        $try = __('customer_categories.labels.' . $c->slug);
        if ($try !== 'customer_categories.labels.' . $c->slug) $label = $try;
        if (!$label) {
          $try2 = __('m_tours.customer_categories.labels.' . $c->slug);
          if ($try2 !== 'm_tours.customer_categories.labels.' . $c->slug) $label = $try2;
        }
      }
      return [$c->slug => ($label ?: $c->slug)];
    })->all();
@endphp

<table class="table table-bordered table-striped table-hover table-compact">
  <thead class="bg-primary text-white">
    <tr>
      <th>{{ __('m_bookings.bookings.fields.reference') }}</th>
      <th>{{ __('m_bookings.bookings.fields.status') }}</th>
      <th>{{ __('m_bookings.bookings.fields.customer') }}</th>
      <th>{{ __('m_bookings.bookings.fields.tour') }}</th>
      <th>{{ __('m_bookings.bookings.fields.tour_date') }}</th>
      <th>{{ __('m_bookings.bookings.fields.schedule') }}</th>
      <th>{{ __('m_bookings.bookings.fields.pickup_place') }}</th>
      <th>{{ __('m_bookings.bookings.fields.travelers') }}</th>
      <th>{{ __('m_bookings.bookings.fields.total') }}</th>
      <th>{{ __('m_bookings.bookings.ui.actions') }}</th>
    </tr>
  </thead>
  <tbody>
  @foreach ($bookings as $booking)
    @php
        $detail = $booking->detail;

        // ===== Tour name con fallback =====
        $liveName = optional($detail?->tour)->name;
        $snapName = $detail->tour_name_snapshot ?: ($booking->tour_name_snapshot ?? null);
        $tourCellText = $liveName
            ?? ($snapName
                ? __('m_bookings.bookings.messages.deleted_tour_snapshot', ['name' => $snapName])
                : __('m_bookings.bookings.messages.deleted_tour'));
        $tourDisplay = mb_strlen($tourCellText) > 30 ? (mb_substr($tourCellText, 0, 30) . '…') : $tourCellText;

        // ===== Horario =====
        $scheduleLabel = $detail?->schedule
            ? \Carbon\Carbon::parse($detail->schedule->start_time)->format('g:i A')
            : '—';

        // ===== Pickup Place (Hotel, Other Hotel o Meeting Point) =====
        // prioridad: other_hotel_name → hotel → snapshot → meeting point → snapshot
        $hotelName = $detail?->other_hotel_name
            ?: optional($detail?->hotel)->name
            ?: ($detail->hotel_name_snapshot ?? $booking->hotel_name_snapshot ?? null);

        $mpName = optional($detail?->meetingPoint)->name
            ?: ($detail->meeting_point_name_snapshot ?? $booking->meeting_point_name_snapshot ?? null);

        $pickupSnap = $detail->pickup_place_snapshot ?? $booking->pickup_place_snapshot ?? null;

        $pickupLabel = null;
        $pickupIcon  = null;

        if ($hotelName) {
            $pickupLabel = $hotelName;
            $pickupIcon  = 'fa-hotel';
        } elseif ($mpName) {
            $pickupLabel = $mpName;
            $pickupIcon  = 'fa-map-marker-alt';
        } elseif ($pickupSnap) {
            $pickupLabel = $pickupSnap;
            $pickupIcon  = 'fa-map-marker-alt';
        } else {
            $pickupLabel = '—';
        }

        $pickupDisplay = $pickupLabel && $pickupLabel !== '—'
            ? (mb_strlen($pickupLabel) > 34 ? (mb_substr($pickupLabel, 0, 34) . '…') : $pickupLabel)
            : '—';

        // ===== Categorías =====
        $categoriesData = [];
        $totalPersons   = 0;

        if ($detail?->categories && is_string($detail->categories)) {
            try { $categoriesData = json_decode($detail->categories, true) ?: []; }
            catch (\Exception $e) { \Log::warning('Error parsing categories in table', ['booking_id' => $booking->booking_id]); }
        } elseif (is_array($detail?->categories)) {
            $categoriesData = $detail->categories;
        }

        // Función helper para resolver nombre traducido de categoría desde array del detalle
        $resolveCatName = function(array $cat) use ($catNameById, $catNameBySlug) {
            // 1) por id
            $id = $cat['category_id'] ?? $cat['id'] ?? null;
            if ($id && isset($catNameById[$id]) && $catNameById[$id]) {
                return $catNameById[$id];
            }
            // 2) por slug
            $slug = $cat['slug'] ?? null;
            if ($slug && isset($catNameBySlug[$slug]) && $catNameBySlug[$slug]) {
                return $catNameBySlug[$slug];
            }
            // 3) por archivos de idioma (si vino el slug pero no está en catNameBySlug)
            if ($slug) {
                $tr = __('customer_categories.labels.' . $slug);
                if ($tr !== 'customer_categories.labels.' . $slug) return $tr;
                $tr2 = __('m_tours.customer_categories.labels.' . $slug);
                if ($tr2 !== 'm_tours.customer_categories.labels.' . $slug) return $tr2;
            }
            // 4) fallback al nombre en el snapshot
            return $cat['name'] ?? $cat['category_name'] ?? 'N/A';
        };

        $categoriesRendered = [];
        if (!empty($categoriesData)) {
            // Soportar dos formatos (lista y mapa)
            if (isset($categoriesData[0]) && is_array($categoriesData[0])) {
                foreach ($categoriesData as $cat) {
                    $qty  = (int)($cat['quantity'] ?? 0);
                    if ($qty > 0) {
                        $name = $resolveCatName($cat);
                        $categoriesRendered[] = ['name' => $name, 'quantity' => $qty];
                        $totalPersons += $qty;
                    }
                }
            } else {
                foreach ($categoriesData as $catId => $cat) {
                    $qty  = (int)($cat['quantity'] ?? 0);
                    if ($qty > 0) {
                        // inyectar id si la clave lo es
                        if (!isset($cat['category_id']) && is_numeric($catId)) {
                          $cat['category_id'] = (int)$catId;
                        }
                        $name = $resolveCatName($cat);
                        $categoriesRendered[] = ['name' => $name, 'quantity' => $qty];
                        $totalPersons += $qty;
                    }
                }
            }
        }

        // Fallback legacy (adults/kids)
        if (empty($categoriesRendered)) {
            $adults = (int)($detail->adults_quantity ?? 0);
            $kids   = (int)($detail->kids_quantity ?? 0);
            if ($adults > 0) { $categoriesRendered[] = ['name' => __('customer_categories.labels.adult') !== 'customer_categories.labels.adult' ? __('customer_categories.labels.adult') : 'Adults', 'quantity' => $adults]; }
            if ($kids > 0)   { $categoriesRendered[] = ['name' => __('customer_categories.labels.child') !== 'customer_categories.labels.child' ? __('customer_categories.labels.child') : 'Kids',   'quantity' => $kids]; }
            $totalPersons = $adults + $kids;
        }

        // Tooltip con todo el desglose
        $catsTitle = '';
        if (!empty($categoriesRendered)) {
            $titleParts = [];
            foreach ($categoriesRendered as $c) {
                $titleParts[] = ($c['name'] ?? 'N/A') . ' ×' . (int)($c['quantity'] ?? 0);
            }
            $catsTitle = implode(' · ', $titleParts);
        }
    @endphp

    <tr>
      <td><strong>{{ $booking->booking_reference }}</strong></td>

      <td>
        <span class="badge badge-compact badge-interactive
          {{ $booking->status === 'pending'   ? 'bg-warning text-dark' : '' }}
          {{ $booking->status === 'confirmed' ? 'bg-success text-white' : '' }}
          {{ $booking->status === 'cancelled' ? 'bg-danger text-white'  : '' }}"
          data-bs-toggle="modal"
          data-bs-target="#modalDetails{{ $booking->booking_id }}"
          style="cursor: pointer;"
          title="{{ __('m_bookings.bookings.ui.click_to_view') }}">
          <i class="fas fa-eye me-1"></i>
          {{ __('m_bookings.bookings.statuses.' . $booking->status) }}
        </span>
      </td>

      <td>{{ $booking->user->full_name ?? $booking->user->name ?? '—' }}</td>

      <td title="{{ $tourCellText }}">{{ $tourDisplay }}</td>

      <td>{{ optional($detail?->tour_date)->format('d-M-Y') ?? '—' }}</td>

      <td>{{ $scheduleLabel }}</td>

      {{-- Pickup Place --}}
      <td title="{{ $pickupLabel }}">
        @if($pickupLabel !== '—')
          <span class="chip chip-muted">
            @if($pickupIcon)<i class="fas {{ $pickupIcon }} me-1"></i>@endif
            {{ $pickupDisplay }}
          </span>
        @else
          —
        @endif
      </td>

      {{-- Categorías (mostrar todas en chips, sin agrupar) --}}
      <td>
        @if($totalPersons > 0)
          <div class="cats-inline" title="{{ $catsTitle }}">
            @foreach($categoriesRendered as $c)
              <span class="cat-chip" title="{{ $c['name'] }} ×{{ (int)($c['quantity'] ?? 0) }}">
                {{ $c['name'] }} ×{{ (int)($c['quantity'] ?? 0) }}
              </span>
            @endforeach

            <span class="cat-total ms-auto" title="{{ __('m_bookings.bookings.fields.total_travelers') }}">
              <i class="fas fa-users me-1"></i>{{ $totalPersons }}
            </span>
          </div>
        @else
          <span class="badge bg-secondary">0</span>
        @endif
      </td>

      <td><strong>{{ $currency }}{{ number_format((float)$booking->total, 2) }}</strong></td>

      <td class="text-nowrap">
        {{-- View Details --}}
        <button class="btn btn-sm btn-info btn-details"
                data-bs-toggle="modal"
                data-bs-target="#modalDetails{{ $booking->booking_id }}"
                title="{{ __('m_bookings.bookings.ui.view_details') }}">
          <i class="fas fa-eye"></i>
        </button>

        {{-- Download Receipt --}}
        <a href="{{ route('admin.bookings.receipt', $booking->booking_id) }}"
           class="btn btn-primary btn-sm"
           title="{{ __('m_bookings.bookings.ui.download_receipt') }}">
          <i class="fas fa-file-download"></i>
        </a>

        {{-- Edit --}}
        <a href="{{ route('admin.bookings.edit', $booking) }}"
           class="btn btn-sm btn-warning"
           title="{{ __('m_bookings.bookings.buttons.edit') }}">
          <i class="fas fa-edit"></i>
        </a>

        {{-- Delete --}}
        <form action="{{ route('admin.bookings.destroy', $booking->booking_id) }}"
              method="POST" class="d-inline">
          @csrf
          @method('DELETE')
          <button class="btn btn-sm btn-danger"
                  onclick="return confirm('{{ __('m_bookings.bookings.confirm.delete') }}')"
                  title="{{ __('m_bookings.bookings.buttons.delete') }}">
            <i class="fas fa-trash-alt"></i>
          </button>
        </form>
      </td>
    </tr>
  @endforeach
  </tbody>
</table>

<style>
  .table-compact .gap-1 { gap: 0.25rem; }

  /* Chips en línea para categorías */
  .cats-inline {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: .25rem .5rem;
  }
  .cat-chip {
    display: inline-flex;
    align-items: center;
    font-size: .75rem;
    line-height: 1;
    padding: .125rem .4rem;
    border-radius: .6rem;
    background: #3f6791;
    border: 1px solid var(--bs-border-color, #dee2e6);
    color: #fff;
    white-space: nowrap;
    max-width: 100%;
  }
  .cat-total {
    display: inline-flex;
    align-items: center;
    font-weight: 700;
    font-size: .78rem;
    line-height: 1;
    padding: .125rem .45rem;
    border-radius: .6rem;
    background: #60a862;
    color: #fff;
    white-space: nowrap;
  }

  /* Pickup chip */
  .chip {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    font-size: .8rem;
    line-height: 1.1;
    padding: .2rem .45rem;
    border-radius: .5rem;
    border: 1px solid var(--bs-border-color, #dee2e6);
    max-width: 260px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .chip-muted {
    background: var(--bs-secondary-bg, #f1f3f5);
    color: var(--bs-body-color, #212529);
  }
</style>
