<table class="table table-bordered table-striped text-nowrap">
  <thead class="bg-primary text-white">
    <tr>
      <th>ID Reserva</th>
      <th>Estado</th>
      <th>Fecha Reserva</th>
      <th>Referencia</th>
      <th>Cliente</th>
      <th>Correo</th>
      <th>Teléfono</th>
      <th>Tour</th>
      <th>Idioma</th>
      <th>Fecha Tour</th>
      <th>Hotel</th>
      <th>Meeting Point</th>
      <th>Horario</th>
      <th>Tipo</th>
      <th>Adultos</th>
      <th>Niños</th>
      <th>Promo Code</th>
      <th>Total</th>
      <th>Acciones</th>
    </tr>
  </thead>
  <tbody>
@foreach ($bookings as $booking)
  @php
      $detail = $booking->detail;

      // Nombre del tour (soporta tours soft-deleted)
      $tourLiveName = optional($detail->tour)->name; // si la relación existe y no está borrado
      $tourTrashedName = null;

      if (!$tourLiveName && $detail?->tour_id) {
          // intenta recuperar el nombre incluyendo los eliminados (soft delete)
          $tourTrashedName = \App\Models\Tour::withTrashed()
              ->where('tour_id', $detail->tour_id)
              ->value('name');
      }

      // Texto final para la celda
      $tourCellText = $tourLiveName
          ? $tourLiveName
          : ($tourTrashedName ? "Tour eliminado ({$tourTrashedName})" : "Tour eliminado");
  @endphp
  <tr>
    <td>{{ $booking->booking_id }}</td>
    <td>
      <span class="badge
        {{ $booking->status === 'pending' ? 'bg-warning' : '' }}
        {{ $booking->status === 'confirmed' ? 'bg-success' : '' }}
        {{ $booking->status === 'cancelled' ? 'bg-danger' : '' }}">
        {{ ucfirst($booking->status) }}
      </span>
    </td>
    <td>{{ $booking->booking_date }}</td>
    <td>{{ $booking->booking_reference }}</td>
    <td>{{ $booking->user->full_name ?? '-' }}</td>
    <td>{{ $booking->user->email ?? '-' }}</td>
    <td>{{ $booking->user->phone ?? '-' }}</td>

    {{-- 🟠 Tour: soporta "eliminado (nombre)" --}}
    <td>{{ $tourCellText }}</td>

    <td>{{ optional($detail->tourLanguage)->name ?? '-' }}</td>
    <td>{{ optional($detail)->tour_date?->format('Y-m-d') ?? '-' }}</td>
    <td>{{ $detail->hotel->name ?? $detail->other_hotel_name ?? '-' }}</td>

    {{-- SOLO nombre del punto de encuentro --}}
    <td>{{ optional($detail->meetingPoint ?? null)->name ?? '—' }}</td>

    <td>{{ $detail->schedule->start_time ?? '' }} - {{ $detail->schedule->end_time ?? '' }}</td>
    <td>{{ optional($detail->tour->tourType ?? null)->name ?? '—' }}</td>
    <td>{{ $detail->adults_quantity }}</td>
    <td>{{ $detail->kids_quantity }}</td>
    <td>{{ $booking->promoCode->code ?? '—' }}</td>
    <td>${{ number_format($booking->total, 2) }}</td>
    <td class="text-nowrap">
      {{-- Descargar comprobante --}}
      <a href="{{ route('admin.reservas.comprobante', $booking->booking_id) }}"
         class="btn btn-primary btn-sm" title="Descargar comprobante">
        <i class="fas fa-file-download"></i>
      </a>

      {{-- Editar --}}
      <button class="btn btn-sm btn-edit"
              data-bs-toggle="modal"
              data-bs-target="#modalEditar{{ $booking->booking_id }}"
              title="Editar">
        <i class="fas fa-edit"></i>
      </button>

      {{-- Eliminar --}}
      <form action="{{ route('admin.reservas.destroy', $booking->booking_id) }}"
            method="POST" class="d-inline">
        @csrf
        @method('DELETE')
        <button class="btn btn-sm btn-delete"
                onclick="return confirm('¿Estás seguro de eliminar esta reserva?')"
                title="Eliminar">
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
      @for ($i = 1; $i <= $bookings->lastPage(); $i++)
        <li class="page-item {{ $i == $bookings->currentPage() ? 'active' : '' }}">
          <a class="page-link" href="{{ $bookings->url($i) }}">{{ $i }}</a>
        </li>
      @endfor
    </ul>
  </nav>
@endif
