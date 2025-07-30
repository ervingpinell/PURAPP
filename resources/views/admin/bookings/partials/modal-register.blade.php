{{-- resources/views/admin/reservas/partials/modal-registrar.blade.php --}}
<div class="modal fade" id="modalRegistrar" tabindex="-1">
  <div class="modal-dialog">
    <form action="{{ route('admin.reservas.store') }}" method="POST">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Registrar Reserva</h5>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          {{-- Incluye el formulario sin campo de fecha de reserva --}}
          @include('admin.bookings.partials.form', ['modo' => 'crear'])
        </div>
        <div class="modal-footer">
          <button class="btn btn-primary">Guardar</button>
          <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </form>
  </div>
</div>
