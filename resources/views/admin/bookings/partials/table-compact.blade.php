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
      <th>{{ __('m_bookings.bookings.fields.adults') }}</th>
      <th>{{ __('m_bookings.bookings.fields.children') }}</th>
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
      <td>{{ $booking->user->full_name ?? '-' }}</td>
      <td title="{{ $tourCellText }}">{{ $tourDisplay }}</td>
      <td>{{ optional($detail)->tour_date?->format('d-M-Y') ?? '-' }}</td>
      <td>
        @if($detail->schedule)
          {{ \Carbon\Carbon::parse($detail->schedule->start_time)->format('g:i A') }}
        @else
          —
        @endif
      </td>
      <td class="text-center">{{ $detail->adults_quantity }}</td>
      <td class="text-center">{{ $detail->kids_quantity }}</td>
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
        <button class="btn btn-sm btn-warning"
                data-bs-toggle="modal"
                data-bs-target="#modalEdit{{ $booking->booking_id }}"
                title="{{ __('m_bookings.bookings.buttons.edit') }}">
          <i class="fas fa-edit"></i>
        </button>

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
      {{-- Previous Page Link --}}
      @if ($bookings->onFirstPage())
        <li class="page-item disabled"><span class="page-link">«</span></li>
      @else
        <li class="page-item"><a class="page-link" href="{{ $bookings->previousPageUrl() }}">«</a></li>
      @endif

      {{-- Pagination Elements --}}
      @for ($i = 1; $i <= $bookings->lastPage(); $i++)
        <li class="page-item {{ $i == $bookings->currentPage() ? 'active' : '' }}">
          <a class="page-link" href="{{ $bookings->url($i) }}">{{ $i }}</a>
        </li>
      @endfor

      {{-- Next Page Link --}}
      @if ($bookings->hasMorePages())
        <li class="page-item"><a class="page-link" href="{{ $bookings->nextPageUrl() }}">»</a></li>
      @else
        <li class="page-item disabled"><span class="page-link">»</span></li>
      @endif
    </ul>
  </nav>
@endif
