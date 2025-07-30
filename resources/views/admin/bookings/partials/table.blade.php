<table class="table table-bordered table-striped text-nowrap">
  <thead class="bg-primary text-white">
    <tr>
      <th>ID Reserva</th>
      <th>Estado</th>
      <th>Fecha Reserva</th>
      <th>Cliente</th>
      <th>Correo</th>
      <th>Teléfono</th>
      <th>Tour</th>
      <th>Fecha Tour</th>
      <th>Hotel</th>
      <th>Horario</th>
      <th>Tipo</th>
      <th>Adultos</th>
      <th>Niños</th>
      <th>Referencia</th>
      <th>Total</th>
      <th>Acciones</th>
    </tr>
  </thead>
  <tbody>
@foreach ($bookings as $booking)
  @php $detail = $booking->detail; @endphp
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
    <td>{{ $booking->user->full_name ?? '-' }}</td>
    <td>{{ $booking->user->email ?? '-' }}</td>
    <td>{{ $booking->user->phone ?? '-' }}</td>
    <td>{{ $detail->tour->name ?? '-' }}</td>
    <td>{{ optional($detail)->tour_date?->format('Y-m-d') ?? '-' }}</td>
    <td>{{ $detail->hotel->name ?? $detail->other_hotel_name ?? '-' }}</td>
    <td>{{ $detail->schedule->start_time ?? '' }} - {{ $detail->schedule->end_time ?? '' }}</td>
    <td>{{ optional($detail->tour->tourType ?? null)->name ?? '—' }}</td>
    <td>{{ $detail->adults_quantity }}</td>
    <td>{{ $detail->kids_quantity }}</td>
    <td>{{ $booking->booking_reference }}</td>
    <td>${{ number_format($booking->total, 2) }}</td>
    <td>
      {{-- Acciones --}}
    </td>
  </tr>
@endforeach

  </tbody>
</table>

<div class="mt-3">
  {{ $bookings->links() }}
</div>
