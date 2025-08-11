{{-- Modal Editar --}}
<div class="modal fade" id="modalEditar{{ $reserva->booking_id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form
      id="editForm-{{ $reserva->booking_id }}"
      class="js-edit-booking-form"
      action="{{ route('admin.reservas.update', $reserva->booking_id) }}"
      method="POST"
      novalidate
      data-booking-id="{{ $reserva->booking_id }}">
      @csrf
      @method('PUT')

      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            Reserva #{{ $reserva->booking_id }} – {{ $reserva->user->full_name ?? 'Cliente' }}
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <div class="modal-body">
          @include('admin.bookings.partials.edit-form', [
            'booking'  => $reserva,
            'statuses' => [
              'pending'   => 'Pending',
              'confirmed' => 'Confirmed',
              'cancelled' => 'Cancelled',
            ],
          ])
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-warning">Actualizar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </form>
  </div>
</div>

{{-- Reabrir este modal si el controlador devolvió errores para esta reserva --}}
@if (session('showEditModal') == $reserva->booking_id)
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const m = document.getElementById('modalEditar{{ $reserva->booking_id }}');
      if (m) new bootstrap.Modal(m).show();
    });
  </script>
@endif

<script>
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('editForm-{{ $reserva->booking_id }}');
  if (!form) return;

  const btnSubmit = form.querySelector('button[type="submit"]');
  const btnCancel = form.querySelector('[data-bs-dismiss="modal"]');

  // 1) Evitar doble submit + spinner
  form.addEventListener('submit', (e) => {
    if (form.dataset.submitted === 'true') {
      e.preventDefault();
      return;
    }
    form.dataset.submitted = 'true';

    if (btnSubmit) {
      btnSubmit.disabled = true;
      btnSubmit.dataset.originalText = btnSubmit.innerHTML;
      btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Actualizando...';
    }
    // Si también quieres bloquear "Cancelar" mientras envía, descomenta:
    // if (btnCancel) btnCancel.disabled = true;
  });

  // 2) Toggle "Otro hotel" (funciona dentro de ESTE modal)
  const hotelSel      = form.querySelector('select[name="hotel_id"]');
  const otherWrap     = form.querySelector('[data-role="other-hotel-wrapper"]');
  const isOtherHidden = form.querySelector('input[name="is_other_hotel"]');

  const toggleOtherHotel = () => {
    if (!hotelSel || !otherWrap || !isOtherHidden) return;
    const isOther = hotelSel.value === 'other';
    otherWrap.classList.toggle('d-none', !isOther);
    isOtherHidden.value = isOther ? 1 : 0;
    if (!isOther) {
      const otherInput = form.querySelector('input[name="other_hotel_name"]');
      if (otherInput) otherInput.value = '';
    }
  };
  toggleOtherHotel();
  hotelSel?.addEventListener('change', toggleOtherHotel);

  // 3) Actualizar horarios cuando cambie el tour (usa data-schedules del <option>)
  const tourSel  = form.querySelector('select[name="tour_id"]');
  const schedSel = form.querySelector('select[name="schedule_id"]');

  tourSel?.addEventListener('change', () => {
    const opt  = tourSel.selectedOptions[0];
    const json = opt ? opt.getAttribute('data-schedules') : '[]';
    let list = [];
    try { list = JSON.parse(json || '[]'); } catch(e) { console.error(e); }

    // reconstruir opciones
    if (schedSel) {
      schedSel.innerHTML = '<option value="">Seleccione horario</option>';
      list.forEach(s => {
        const o = document.createElement('option');
        o.value = s.schedule_id;
        o.textContent = `${s.start_time} – ${s.end_time}`;
        schedSel.appendChild(o);
      });
    }
  });
});
</script>
