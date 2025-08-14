{{-- ✅ Partial de edición (sin <form>, sin @csrf, sin @method) --}}
@php
  $tz      = config('app.timezone', 'America/Costa_Rica');
  $today   = \Carbon\Carbon::today($tz)->toDateString();
  // Usa $isPast del controlador si existe; si no, calcúlalo
  $detailDt = optional($booking->detail)->tour_date;
  $calcPast = $detailDt ? \Carbon\Carbon::parse($detailDt, $tz)->lt(\Carbon\Carbon::parse($today, $tz)) : false;
  $isPast   = isset($isPast) ? (bool)$isPast : $calcPast;
@endphp

@if($isPast)
  <div class="alert alert-warning mb-3">
    Esta reserva corresponde a una fecha pasada y no puede editarse.
  </div>
@endif

{{-- Deshabilita todo si la reserva es pasada --}}
<fieldset @if($isPast) disabled @endif>

  {{-- Cliente --}}
  <div class="mb-3">
    <label class="form-label">Cliente</label>
    <select name="user_id" class="form-control" required>
      @foreach(\App\Models\User::select('user_id','full_name')->get() as $u)
        <option value="{{ $u->user_id }}" {{ $booking->user_id == $u->user_id ? 'selected' : '' }}>
          {{ $u->full_name }}
        </option>
      @endforeach
    </select>
  </div>

  {{-- Correo --}}
  <div class="mb-3">
    <label class="form-label">Correo</label>
    <input type="email" class="form-control" value="{{ $booking->user->email ?? '' }}" readonly>
  </div>

  {{-- Teléfono --}}
  <div class="mb-3">
    <label class="form-label">Teléfono</label>
    <input type="text" class="form-control" value="{{ $booking->user->phone ?? '' }}" readonly>
  </div>

  {{-- Tour --}}
  <div class="mb-3">
    <label class="form-label">Tour</label>
    <select name="tour_id" class="form-control" required>
      @foreach(\App\Models\Tour::with('schedules')->orderBy('name')->get() as $tour)
        @php
          $sched = $tour->schedules->map(function ($s) {
            return [
              'schedule_id'  => $s->schedule_id,
              'start_time'   => \Carbon\Carbon::parse($s->start_time)->format('g:i A'),
              'end_time'     => \Carbon\Carbon::parse($s->end_time)->format('g:i A'),
              'max_capacity' => $s->max_capacity,
            ];
          })->values();
        @endphp

        <option value="{{ $tour->tour_id }}"
          data-schedules='@json($sched, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT)'
          {{ $booking->tour_id == $tour->tour_id ? 'selected' : '' }}>
          {{ $tour->name }}
        </option>
      @endforeach
    </select>
  </div>

  {{-- Horario --}}
  <div class="mb-3">
    <label class="form-label">Horario</label>
    <select name="schedule_id" class="form-control" required>
      <option value="">Seleccione horario</option>
      @foreach($booking->tour->schedules as $s)
        <option value="{{ $s->schedule_id }}"
          {{ $booking->detail->schedule_id == $s->schedule_id ? 'selected' : '' }}>
          {{ \Carbon\Carbon::parse($s->start_time)->format('g:i A') }} – {{ \Carbon\Carbon::parse($s->end_time)->format('g:i A') }}
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

  {{-- Fecha reserva (se deja editable si quieres; no se fuerza min porque es histórica) --}}
  <div class="mb-3">
    <label class="form-label">Fecha Reserva</label>
    <input type="date" name="booking_date" class="form-control"
      value="{{ \Carbon\Carbon::parse($booking->booking_date)->format('Y-m-d') }}" required>
  </div>

  {{-- Fecha tour (🔒 con min = hoy) --}}
  <div class="mb-3">
    <label class="form-label">Fecha del Tour</label>
    <input
      type="date"
      name="tour_date"
      class="form-control"
      value="{{ \Carbon\Carbon::parse($booking->detail->tour_date)->format('Y-m-d') }}"
      min="{{ $today }}"
      required
    >
  </div>

  {{-- Hotel --}}
  <div class="mb-3">
    <label class="form-label">Hotel</label>
    <select name="hotel_id" class="form-control" data-role="hotel-select">
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
  <div class="mb-3 {{ $booking->detail->is_other_hotel ? '' : 'd-none' }}" data-role="other-hotel-wrapper">
    <label class="form-label">Nombre de otro hotel</label>
    <input type="text" name="other_hotel_name" class="form-control"
      value="{{ $booking->detail->other_hotel_name }}">
  </div>
  <input type="hidden" name="is_other_hotel" value="{{ $booking->detail->is_other_hotel ? 1 : 0 }}" data-role="is-other-hotel">

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

  {{-- Promo Code (editable y opción de quitar) --}}
  <div class="mb-3">
    <label class="form-label d-flex justify-content-between align-items-center">
      <span>Promo code</span>
    </label>

    <input
      type="text"
      name="promo_code"
      class="form-control"
      value="{{ old('promo_code', optional($booking->promoCode)->code) }}"
      placeholder="PROMO2025">

    <div class="form-check mt-2">
      <input class="form-check-input" type="checkbox" name="remove_promo" value="1" id="removePromo{{ $booking->booking_id }}">
      <label class="form-check-label" for="removePromo{{ $booking->booking_id }}">
        Quitar promo code de esta reserva
      </label>
    </div>
  </div>

</fieldset>

@once
<script>
document.addEventListener('DOMContentLoaded', () => {
  // Toggle "Otro hotel"
  const hotelSel = document.querySelector('[data-role="hotel-select"]');
  const wrap     = document.querySelector('[data-role="other-hotel-wrapper"]');
  const hidden   = document.querySelector('[data-role="is-other-hotel"]');

  const toggleOther = () => {
    const isOther = hotelSel?.value === 'other';
    wrap?.classList.toggle('d-none', !isOther);
    if (hidden) hidden.value = isOther ? 1 : 0;
  };
  hotelSel?.addEventListener('change', toggleOther);
  toggleOther();

  // Enforce min date (tour_date)
  const dateInput = document.querySelector('input[name="tour_date"]');
  const todayStr  = @json($today);
  if (dateInput) {
    dateInput.setAttribute('min', todayStr);
    dateInput.addEventListener('change', () => {
      const val = dateInput.value;
      if (!val) return;
      const picked = new Date(val + 'T00:00:00');
      const today  = new Date(todayStr + 'T00:00:00');
      picked.setHours(0,0,0,0);
      today.setHours(0,0,0,0);
      if (picked < today) dateInput.value = todayStr;
    });
  }
});
</script>
@endonce
