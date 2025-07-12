{{-- ✅ resources/views/admin/bookings/partials/edit-form.blade.php --}}
{{-- ✅ Partial LIMPIO, sin <form>, sin @csrf, sin @method --}}

{{-- Cliente --}}
<div class="mb-3">
  <label class="form-label">Cliente</label>
  <select name="user_id" class="form-control" required>
    @foreach(\App\Models\User::all() as $u)
      <option value="{{ $u->user_id }}" {{ $booking->user_id == $u->user_id ? 'selected' : '' }}>
        {{ $u->full_name }}
      </option>
    @endforeach
  </select>
</div>

{{-- Correo --}}
<div class="mb-3">
  <label class="form-label">Correo</label>
  <input type="email" name="email" class="form-control"
    value="{{ $booking->user->email ?? '' }}" readonly>
</div>

{{-- Teléfono --}}
<div class="mb-3">
  <label class="form-label">Teléfono</label>
  <input type="text" name="phone" class="form-control"
    value="{{ $booking->user->phone ?? '' }}" readonly>
</div>

{{-- Tour --}}
<div class="mb-3">
  <label class="form-label">Tour</label>
  <select name="tour_id" id="edit_tour" class="form-control" required>
    @foreach(\App\Models\Tour::with('schedules')->get() as $tour)
      <option value="{{ $tour->tour_id }}"
        data-schedules='@json($tour->schedules->map(fn($s)=>[
          "schedule_id"=>$s->schedule_id,
          "start_time"=>\Carbon\Carbon::parse($s->start_time)->format("g:i A"),
          "end_time"=>\Carbon\Carbon::parse($s->end_time)->format("g:i A")
        ]))'
        {{ $booking->tour_id == $tour->tour_id ? 'selected' : '' }}>
        {{ $tour->name }}
      </option>
    @endforeach
  </select>
</div>

{{-- Horario dinámico --}}
<div class="mb-3">
  <label class="form-label">Horario</label>
  <select name="schedule_id" id="edit_schedule" class="form-control" required>
    <option value="">Seleccione horario</option>
    @foreach($booking->tour->schedules as $s)
      <option value="{{ $s->schedule_id }}"
        {{ $booking->detail->schedule_id == $s->schedule_id ? 'selected' : '' }}>
        {{ \Carbon\Carbon::parse($s->start_time)->format('g:i A') }} –
        {{ \Carbon\Carbon::parse($s->end_time)->format('g:i A') }}
      </option>
    @endforeach
  </select>
</div>

{{-- Idioma --}}
<div class="mb-3">
  <label class="form-label">Idioma</label>
  <select name="tour_language_id" class="form-control" required>
    @foreach(\App\Models\TourLanguage::all() as $lang)
      <option value="{{ $lang->tour_language_id }}"
        {{ $booking->tour_language_id == $lang->tour_language_id ? 'selected' : '' }}>
        {{ $lang->name }}
      </option>
    @endforeach
  </select>
</div>

{{-- Fecha reserva --}}
<div class="mb-3">
  <label class="form-label">Fecha Reserva</label>
  <input type="date" name="booking_date" class="form-control"
    value="{{ \Carbon\Carbon::parse($booking->booking_date)->format('Y-m-d') }}" required>
</div>

{{-- Fecha tour --}}
<div class="mb-3">
  <label class="form-label">Fecha del Tour</label>
  <input type="date" name="tour_date" class="form-control"
    value="{{ \Carbon\Carbon::parse($booking->detail->tour_date)->format('Y-m-d') }}" required>
</div>

{{-- Hotel --}}
<div class="mb-3">
  <label class="form-label">Hotel</label>
  <select name="hotel_id"
          id="edit_hotel_select"
          class="form-control">
    <option value="">Seleccione hotel</option>
    @foreach(\App\Models\HotelList::where('is_active', true)->orderBy('name')->get() as $h)
      <option value="{{ $h->hotel_id }}"
        {{ !$booking->detail->is_other_hotel && $booking->detail->hotel_id == $h->hotel_id ? 'selected':'' }}>
        {{ $h->name }}
      </option>
    @endforeach
    <option value="other" {{ $booking->detail->is_other_hotel ? 'selected':'' }}>Otro…</option>
  </select>
</div>

{{-- Otro hotel --}}
<div class="mb-3 {{ $booking->detail->is_other_hotel ? '' : 'd-none' }}"
     id="edit_other_hotel_wrapper">
  <label class="form-label">Nombre de otro hotel</label>
  <input type="text" name="other_hotel_name" class="form-control"
    value="{{ $booking->detail->other_hotel_name }}">
</div>
<input type="hidden" name="is_other_hotel"
       id="edit_is_other_hotel"
       value="{{ $booking->detail->is_other_hotel ? 1 : 0 }}">

{{-- Adultos --}}
<div class="mb-3">
  <label class="form-label">Cantidad Adultos</label>
  <input type="number" name="adults_quantity" class="form-control"
    value="{{ $booking->detail->adults_quantity }}" min="1" required>
</div>

{{-- Niños --}}
<div class="mb-3">
  <label class="form-label">Cantidad Niños</label>
  <input type="number" name="kids_quantity" class="form-control"
    value="{{ $booking->detail->kids_quantity }}" min="0" max="2" required>
</div>

{{-- Notas --}}
<div class="mb-3">
  <label class="form-label">Notas</label>
  <textarea name="notes" class="form-control" rows="2">{{ old('notes', $booking->notes) }}</textarea>
</div>

{{-- Estado --}}
<div class="mb-3">
  <label class="form-label">Estado</label>
  <select name="status" class="form-control" required>
    @foreach($statuses as $val => $label)
      <option value="{{ $val }}" {{ $booking->status === $val ? 'selected':'' }}>
        {{ $label }}
      </option>
    @endforeach
  </select>
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

    // Tour y horarios dinámicos
    const tourSel = document.getElementById('edit_tour');
    const schSel  = document.getElementById('edit_schedule');

    tourSel?.addEventListener('change', () => {
      const opt = tourSel.options[tourSel.selectedIndex];
      const schedules = JSON.parse(opt.dataset.schedules || '[]');
      schSel.innerHTML = '<option value="">Seleccione horario</option>';
      schedules.forEach(s => {
        const o = document.createElement('option');
        o.value = s.schedule_id;
        o.text = `${s.start_time} – ${s.end_time}`;
        schSel.appendChild(o);
      });
    });
  });
</script>
@endpush
