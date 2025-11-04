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
      <th>{{ __('m_bookings.bookings.fields.categories') }}</th>
      <th>{{ __('m_bookings.bookings.fields.total') }}</th>
      <th>{{ __('m_bookings.bookings.ui.actions') }}</th>
    </tr>
  </thead>
  <tbody>
  @foreach ($bookings as $booking)
    @php
        $detail = $booking->detail;
        $liveName = optional($detail->tour)->name;
        $snapName = $detail->tour_name_snapshot ?: ($booking->tour_name_snapshot ?? null);
        $tourCellText = $liveName ?? ($snapName ? __('m_bookings.bookings.messages.deleted_tour_snapshot', ['name' => $snapName]) : __('m_bookings.bookings.messages.deleted_tour'));

        // Truncate tour name if too long
        $tourDisplay = strlen($tourCellText) > 30 ? substr($tourCellText, 0, 30) . '...' : $tourCellText;

        // ========== PARSEAR CATEGORÍAS ==========
        $categoriesData = [];
        $totalPersons = 0;

        if ($detail->categories && is_string($detail->categories)) {
          try {
            $categoriesData = json_decode($detail->categories, true);
          } catch (\Exception $e) {
            \Log::warning('Error parsing categories in table', ['booking_id' => $booking->booking_id]);
          }
        } elseif (is_array($detail->categories)) {
          $categoriesData = $detail->categories;
        }

        $categories = [];
        if (!empty($categoriesData)) {
          // Array de objetos
          if (isset($categoriesData[0]) && is_array($categoriesData[0])) {
            foreach ($categoriesData as $cat) {
              $qty = (int)($cat['quantity'] ?? 0);
              $name = $cat['name'] ?? $cat['category_name'] ?? 'N/A';
              $categories[] = ['name' => $name, 'quantity' => $qty];
              $totalPersons += $qty;
            }
          }
          // Array asociativo
          else {
            foreach ($categoriesData as $catId => $cat) {
              $qty = (int)($cat['quantity'] ?? 0);
              $name = $cat['name'] ?? $cat['category_name'] ?? "Cat #{$catId}";
              $categories[] = ['name' => $name, 'quantity' => $qty];
              $totalPersons += $qty;
            }
          }
        }

        // Fallback a legacy
        if (empty($categories)) {
          $adults = (int)($detail->adults_quantity ?? 0);
          $kids = (int)($detail->kids_quantity ?? 0);
          if ($adults > 0) $categories[] = ['name' => 'Adults', 'quantity' => $adults];
          if ($kids > 0) $categories[] = ['name' => 'Kids', 'quantity' => $kids];
          $totalPersons = $adults + $kids;
        }
    @endphp

    <tr>
      <td>
        <strong>{{ $booking->booking_reference }}</strong>
      </td>
      <td>
        {{-- Badge interactivo que abre el modal de detalles --}}
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
      <td>
        @if($detail->schedule)
          {{ \Carbon\Carbon::parse($detail->schedule->start_time)->format('g:i A') }}
        @else
          —
        @endif
      </td>
      <td>
        {{-- Desglose de categorías --}}
        @if(!empty($categories))
          <div class="d-flex flex-column gap-1">
            @foreach($categories as $cat)
              <div class="d-flex align-items-center justify-content-between">
                <small class="text-muted">{{ $cat['name'] }}:</small>
                <span class="badge bg-secondary">{{ $cat['quantity'] }}</span>
              </div>
            @endforeach
            <div class="border-top pt-1 mt-1">
              <strong class="text-primary">Total: {{ $totalPersons }}</strong>
            </div>
          </div>
        @else
          <span class="badge bg-info">{{ $totalPersons }}</span>
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
  .table-compact .gap-1 {
    gap: 0.25rem;
  }
</style>
