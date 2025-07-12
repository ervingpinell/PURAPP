{{-- ✅ Partial limpio: SIN <form>, SIN @csrf, SIN @method --}}

<div class="mb-3">
  <label class="form-label">Estado</label>
  <select name="status" class="form-control">
    @foreach($statuses as $val => $label)
      <option value="{{ $val }}" {{ $booking->status === $val ? 'selected':'' }}>
        {{ $label }}
      </option>
    @endforeach
  </select>
</div>

<div class="mb-3">
  <label class="form-label">Adultos</label>
  <input type="number"
         name="adults_quantity"
         class="form-control"
         value="{{ $booking->detail->adults_quantity }}"
         min="1" required>
</div>

<div class="mb-3">
  <label class="form-label">Niños</label>
  <input type="number"
         name="kids_quantity"
         class="form-control"
         value="{{ $booking->detail->kids_quantity }}"
         min="0" max="2" required>
</div>

<div class="mb-3">
  <label class="form-label">Hotel</label>
  <select name="hotel_id"
          id="edit_hotel_select"
          class="form-control">
    <option value="">— Seleccione —</option>
    @foreach(\App\Models\HotelList::where('is_active', true)->orderBy('name')->get() as $h)
      <option value="{{ $h->hotel_id }}"
        {{ !$booking->detail->is_other_hotel && $booking->detail->hotel_id == $h->hotel_id ? 'selected':'' }}>
        {{ $h->name }}
      </option>
    @endforeach
    <option value="other" {{ $booking->detail->is_other_hotel ? 'selected':'' }}>
      Otro…
    </option>
  </select>
</div>

<div class="mb-3 {{ $booking->detail->is_other_hotel ? '' : 'd-none' }}"
     id="edit_other_hotel_wrapper">
  <label class="form-label">Nombre de otro hotel</label>
  <input type="text"
         name="other_hotel_name"
         class="form-control"
         value="{{ $booking->detail->other_hotel_name }}">
</div>

<input type="hidden"
       name="is_other_hotel"
       id="edit_is_other_hotel"
       value="{{ $booking->detail->is_other_hotel ? 1 : 0 }}">

<div class="mb-3">
  <label class="form-label">Horario</label>
  <select name="schedule_id" class="form-control" required>
    <option value="">Sin horario</option>
    @foreach($booking->tour->schedules as $s)
      <option value="{{ $s->schedule_id }}"
        {{ $booking->detail->schedule_id == $s->schedule_id ? 'selected' : '' }}>
        {{ \Carbon\Carbon::parse($s->start_time)->format('g:i A') }} –
        {{ \Carbon\Carbon::parse($s->end_time)->format('g:i A') }}
      </option>
    @endforeach
  </select>
</div>

<div class="mb-3">
  <label class="form-label">Notas</label>
  <textarea name="notes" class="form-control" rows="2">{{ old('notes', $booking->notes) }}</textarea>
</div>

<button type="submit" class="btn btn-primary">Guardar cambios</button>

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const sel    = document.getElementById('edit_hotel_select');
    const wrap   = document.getElementById('edit_other_hotel_wrapper');
    const hidden = document.getElementById('edit_is_other_hotel');

    sel?.addEventListener('change', () => {
      if (sel.value === 'other') {
        wrap.classList.remove('d-none');
        hidden.value = 1;
      } else {
        wrap.classList.add('d-none');
        wrap.querySelector('input').value = '';
        hidden.value = 0;
      }
    });
  });
</script>
@endpush
