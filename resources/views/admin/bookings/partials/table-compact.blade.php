{{-- resources/views/admin/bookings/partials/table-compact.blade.php --}}

<table class="table table-bordered table-striped table-hover table-compact">
  <thead class="bg-primary text-white">
    <tr>
      <th>{{ __('m_bookings.bookings.fields.reference') }}</th>
      <th>{{ __('m_bookings.bookings.fields.status') }}</th>
      <th>{{ __('m_bookings.bookings.fields.customer') }}</th>
      <th>{{ __('m_bookings.bookings.fields.tour') }}</th>
      <th>{{ __('m_bookings.bookings.fields.tour_date') }}</th>
      <th>{{ __('m_bookings.bookings.fields.schedule') }}</th>
      <th>Pickup Place</th>
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
        $liveName = optional($detail->tour)->name;
        $snapName = $detail->tour_name_snapshot ?: ($booking->tour_name_snapshot ?? null);
        $tourCellText = $liveName ?? ($snapName ? __('m_bookings.bookings.messages.deleted_tour_snapshot', ['name' => $snapName]) : __('m_bookings.bookings.messages.deleted_tour'));
        $tourDisplay = mb_strlen($tourCellText) > 30 ? (mb_substr($tourCellText, 0, 30) . '…') : $tourCellText;

        // ===== Horario =====
        $scheduleLabel = $detail->schedule
            ? \Carbon\Carbon::parse($detail->schedule->start_time)->format('g:i A')
            : '—';

        // ===== Pickup Place (Hotel o Meeting Point) =====
        $pickupLabel = null;
        $pickupIcon  = null;
        $pickupSnap  = $detail->pickup_place_snapshot ?? $booking->pickup_place_snapshot ?? null;
        $hotelName   = optional($detail->hotel)->name
                       ?? ($detail->hotel_name_snapshot ?? $booking->hotel_name_snapshot ?? null);
        $mpName      = optional($detail->meetingPoint)->name
                       ?? ($detail->meeting_point_name_snapshot ?? $booking->meeting_point_name_snapshot ?? null);

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

        if ($detail->categories && is_string($detail->categories)) {
            try { $categoriesData = json_decode($detail->categories, true); } catch (\Exception $e) { \Log::warning('Error parsing categories in table', ['booking_id' => $booking->booking_id]); }
        } elseif (is_array($detail->categories)) {
            $categoriesData = $detail->categories;
        }

        $categories = [];
        if (!empty($categoriesData)) {
            if (isset($categoriesData[0]) && is_array($categoriesData[0])) {
                foreach ($categoriesData as $cat) {
                    $qty  = (int)($cat['quantity'] ?? 0);
                    $name = $cat['name'] ?? $cat['category_name'] ?? 'N/A';
                    if ($qty > 0) {
                        $categories[] = ['name' => $name, 'quantity' => $qty];
                        $totalPersons += $qty;
                    }
                }
            } else {
                foreach ($categoriesData as $catId => $cat) {
                    $qty  = (int)($cat['quantity'] ?? 0);
                    $name = $cat['name'] ?? $cat['category_name'] ?? "Cat #{$catId}";
                    if ($qty > 0) {
                        $categories[] = ['name' => $name, 'quantity' => $qty];
                        $totalPersons += $qty;
                    }
                }
            }
        }

        // Fallback legacy
        if (empty($categories)) {
            $adults = (int)($detail->adults_quantity ?? 0);
            $kids   = (int)($detail->kids_quantity ?? 0);
            if ($adults > 0) { $categories[] = ['name' => 'Adults', 'quantity' => $adults]; }
            if ($kids > 0)   { $categories[] = ['name' => 'Kids',   'quantity' => $kids]; }
            $totalPersons = $adults + $kids;
        }

        // Tooltip con todo el desglose
        $catsTitle = '';
        if (!empty($categories)) {
            $titleParts = [];
            foreach ($categories as $c) {
                $titleParts[] = ($c['name'] ?? 'N/A') . ' ×' . (int)($c['quantity'] ?? 0);
            }
            $catsTitle = implode(' · ', $titleParts);
        }
    @endphp

    <tr>
      <td><strong>{{ $booking->booking_reference }}</strong></td>

      <td>
        <span class="badge badge-compact badge-interactive
          {{ $booking->status === 'pending' ? 'bg-warning text-dark' : '' }}
          {{ $booking->status === 'confirmed' ? 'bg-success text-white' : '' }}
          {{ $booking->status === 'cancelled' ? 'bg-danger text-white' : '' }}"
          data-bs-toggle="modal"
          data-bs-target="#modalDetails{{ $booking->booking_id }}"
          style="cursor: pointer;"
          title="{{ __('m_bookings.bookings.ui.click_to_view') }}">
          <i class="fas fa-eye me-1"></i>
          {{ __('m_bookings.bookings.statuses.' . $booking->status) }}
        </span>
      </td>

      <td>{{ $booking->user->full_name ?? $booking->user->name ?? '-' }}</td>

      <td title="{{ $tourCellText }}">{{ $tourDisplay }}</td>

      <td>{{ optional($detail)->tour_date?->format('d-M-Y') ?? '-' }}</td>

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
            @foreach($categories as $c)
              <span class="cat-chip" title="{{ $c['name'] }} ×{{ (int)($c['quantity'] ?? 0) }}">
                {{ $c['name'] }} ×{{ (int)($c['quantity'] ?? 0) }}
              </span>
            @endforeach

            <span class="cat-total ms-auto" title="Total pax">
              <i class="fas fa-users me-1"></i>{{ $totalPersons }}
            </span>
          </div>
        @else
          <span class="badge bg-secondary">0</span>
        @endif
      </td>

      <td><strong>${{ number_format($booking->total, 2) }}</strong></td>

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

@if ($bookings->lastPage() > 1)
  <nav class="mt-4 d-flex justify-content-center">
    <ul class="pagination">
      @if ($bookings->onFirstPage())
        <li class="page-item disabled"><span class="page-link">«</span></li>
      @else
        <li class="page-item"><a class="page-link" href="{{ $bookings->previousPageUrl() }}">«</a></li>
      @endif

      @for ($i = 1; $i <= $bookings->lastPage(); $i++)
        <li class="page-item {{ $i == $bookings->currentPage() ? 'active' : '' }}">
          <a class="page-link" href="{{ $bookings->url($i) }}">{{ $i }}</a>
        </li>
      @endfor

      @if ($bookings->hasMorePages())
        <li class="page-item"><a class="page-link" href="{{ $bookings->nextPageUrl() }}">»</a></li>
      @else
        <li class="page-item disabled"><span class="page-link">»</span></li>
      @endif
    </ul>
  </nav>
@endif

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
    background: #3f6791; /* color solicitado */
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
    background: #60a862; /* color solicitado */
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
