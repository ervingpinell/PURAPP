{{-- Fallback: si no viene $meetingPoints desde el controlador, lo cargamos aquí --}}
@php
  /** @var \Illuminate\Support\Collection<int,\App\Models\MeetingPoint> $meetingPoints */
  $meetingPoints = $meetingPoints
    ?? \App\Models\MeetingPoint::orderByRaw('sort_order IS NULL, sort_order ASC')
        ->orderBy('name','asc')
        ->get();
@endphp

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
      <input type="hidden" name="_modal" value="edit:{{ $reserva->booking_id }}"><!-- 👈 Para reabrir este modal -->

      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            Reserva #{{ $reserva->booking_id }} – {{ $reserva->user->full_name ?? 'Cliente' }}
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <div class="modal-body">
          @php
            $showMyErrors = (session('showEditModal') == $reserva->booking_id)
                            || (old('_modal') === 'edit:'.$reserva->booking_id);
            $detail = $reserva->detail;
          @endphp

          @if ($showMyErrors && $errors->any())
            <div class="alert alert-danger">
              <ul class="mb-0">
                @foreach ($errors->all() as $err)
                  <li>{{ $err }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          {{-- ========== FORM PRINCIPAL EXISTENTE ========== --}}
          @include('admin.bookings.partials.edit-form', [
            'booking'  => $reserva,
            'statuses' => [
              'pending'   => 'Pending',
              'confirmed' => 'Confirmed',
              'cancelled' => 'Cancelled',
            ],
          ])

          {{-- ========== MEETING POINT (simple) ========== --}}
          <hr class="my-3">
          <div class="mb-2">
            <label class="form-label"><i class="fas fa-map-marker-alt me-1"></i> Meeting Point</label>
            <select
              name="meeting_point_id"
              class="form-select @error('meeting_point_id') is-invalid @enderror">
              <option value="">— Selecciona un punto de encuentro —</option>
              @foreach ($meetingPoints as $mp)
                <option
                  value="{{ $mp->id }}"
                  @selected(old('meeting_point_id', $detail->meeting_point_id ?? null) == $mp->id)>
                  {{ $mp->name }}
                </option>
              @endforeach
            </select>
            @error('meeting_point_id')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">
              Solo se muestra el <strong>nombre</strong> del punto en el listado.
            </div>
          </div>
          {{-- /MEETING POINT --}}
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-warning">Actualizar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </form>
  </div>
</div>

{{-- Reabrir este modal si corresponde --}}
@if (session('showEditModal') == $reserva->booking_id || (old('_modal') === 'edit:'.$reserva->booking_id && $errors->any()))
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

  // Evitar doble submit + spinner
  form.addEventListener('submit', (e) => {
    if (form.dataset.submitted === 'true') { e.preventDefault(); return; }
    form.dataset.submitted = 'true';
    if (btnSubmit) {
      btnSubmit.disabled = true;
      btnSubmit.dataset.originalText = btnSubmit.innerHTML;
      btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Actualizando...';
    }
  });

  // Toggle "Otro hotel" (scoped al modal)
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

  // Actualizar horarios cuando cambie el tour
  const tourSel  = form.querySelector('select[name="tour_id"]');
  const schedSel = form.querySelector('select[name="schedule_id"]');

  tourSel?.addEventListener('change', () => {
    const opt  = tourSel.selectedOptions[0];
    const json = opt ? opt.getAttribute('data-schedules') : '[]';
    let list = [];
    try { list = JSON.parse(json || '[]'); } catch(e) { console.error(e); }

    schedSel.innerHTML = '<option value="">Seleccione horario</option>';
    list.forEach(s => {
      const o = document.createElement('option');
      o.value = s.schedule_id;
      o.textContent = `${s.start_time} – ${s.end_time}`;
      schedSel.appendChild(o);
    });
    schedSel.value = '';
  });
});
</script>
