{{-- Modal Editar --}}
<div class="modal fade" id="modalEditar{{ $reserva->booking_id }}" tabindex="-1">
  <div class="modal-dialog">
    <form action="{{ route('admin.reservas.update', $reserva->booking_id) }}" method="POST">
      @csrf @method('PUT')
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            Reserva #{{ $reserva->booking_id }} – {{ $reserva->user->full_name ?? 'Cliente' }}
          </h5>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          @include('admin.bookings.partials.form', ['modo' => 'editar', 'reserva' => $reserva])
        </div>
        <div class="modal-footer">
          <button class="btn btn-warning">Actualizar</button>
          <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </form>
  </div>
</div>
