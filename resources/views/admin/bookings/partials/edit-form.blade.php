{{-- resources/views/admin/bookings/partials/edit-form.blade.php --}}
@php
  $tz      = config('app.timezone', 'America/Costa_Rica');
  $today   = \Carbon\Carbon::today($tz)->toDateString();
  $detailDt = optional($booking->detail)->tour_date;
  $calcPast = $detailDt ? \Carbon\Carbon::parse($detailDt, $tz)->lt(\Carbon\Carbon::parse($today, $tz)) : false;
  $isPast   = isset($isPast) ? (bool)$isPast : $calcPast;

  // Mostrar errores solo si pertenecen a este modal
  $showMyErrors = (session('showEditModal') == $booking->booking_id) || (old('_modal') === 'edit:'.$booking->booking_id);

  $bookingDateVal = old('booking_date', \Carbon\Carbon::parse($booking->booking_date)->format('Y-m-d'));
  $bookingDateHuman = \Carbon\Carbon::parse($bookingDateVal)->locale('es')->translatedFormat('d \\de F \\de Y');
@endphp

@if($isPast)
  <div class="alert alert-warning mb-3">
    Esta reserva corresponde a una fecha pasada y no puede editarse.
  </div>
@endif

{{-- 🔒 Enviar siempre booking_date aunque el fieldset esté disabled --}}
<input type="hidden" name="booking_date" value="{{ $bookingDateVal }}">

<fieldset @if($isPast) disabled @endif>

      {{-- 📌 Fecha de origen de la reserva (solo texto, NO editable) --}}
  <div class="mb-3">
    <label class="form-label">Fecha de Reserva (origen)</label>
    <div class="form-control-text">{{ $bookingDateHuman }}</div>
    @if($showMyErrors) @error('booking_date') <div class="text-danger small mt-1">{{ $message }}</div> @enderror @endif
  </div>

  {{-- Cliente --}}
  <div class="mb-3">
    <label class="form-label">Cliente</label>
    <select name="user_id" class="form-control {{ $showMyErrors && $errors->has('user_id') ? 'is-invalid':'' }}" required>
      @foreach(\App\Models\User::select('user_id','full_name')->orderBy('full_name')->get() as $u)
        <option value="{{ $u->user_id }}" {{ old('user_id', $booking->user_id) == $u->user_id ? 'selected' : '' }}>
          {{ $u->full_name }}
        </option>
      @endforeach
    </select>
    @if($showMyErrors) @error('user_id') <div class="invalid-feedback">{{ $message }}</div> @enderror @endif
  </div>

  {{-- Correo (solo lectura) --}}
  <div class="mb-3">
    <label class="form-label">Correo</label>
    <input type="email" class="form-control" value="{{ $booking->user->email ?? '' }}" readonly>
  </div>

  {{-- Tour --}}
  <div class="mb-3">
    <label class="form-label">Tour</label>
    <select name="tour_id" class="form-control {{ $showMyErrors && $errors->has('tour_id') ? 'is-invalid':'' }}" required>
      @foreach(\App\Models\Tour::with('schedules')->orderBy('name')->get() as $tour)
        @php
          $sched = $tour->schedules->map(fn($s)=>[
            'schedule_id'=>$s->schedule_id,
            'start_time'=>\Carbon\Carbon::parse($s->start_time)->format('g:i A'),
            'end_time'=>\Carbon\Carbon::parse($s->end_time)->format('g:i A'),
            'max_capacity'=>$s->max_capacity,
          ])->values();
        @endphp
        <option value="{{ $tour->tour_id }}"
                data-schedules='@json($sched, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT)'
                {{ old('tour_id', $booking->tour_id) == $tour->tour_id ? 'selected' : '' }}>
          {{ $tour->name }}
        </option>
      @endforeach
    </select>
    @if($showMyErrors) @error('tour_id') <div class="invalid-feedback">{{ $message }}</div> @enderror @endif
  </div>

  {{-- Horario --}}
  <div class="mb-3">
    <label class="form-label">Horario</label>
    <select name="schedule_id" class="form-control {{ $showMyErrors && $errors->has('schedule_id') ? 'is-invalid':'' }}" required>
      <option value="">Seleccione horario</option>
      @foreach($booking->tour->schedules as $s)
        <option value="{{ $s->schedule_id }}" {{ old('schedule_id', $booking->detail->schedule_id) == $s->schedule_id ? 'selected' : '' }}>
          {{ \Carbon\Carbon::parse($s->start_time)->format('g:i A') }} – {{ \Carbon\Carbon::parse($s->end_time)->format('g:i A') }}
        </option>
      @endforeach
    </select>
    @if($showMyErrors) @error('schedule_id') <div class="invalid-feedback">{{ $message }}</div> @enderror @endif
  </div>

  {{-- Idioma --}}
  <div class="mb-3">
    <label class="form-label">Idioma</label>
    <select name="tour_language_id" class="form-control {{ $showMyErrors && $errors->has('tour_language_id') ? 'is-invalid':'' }}" required>
      @foreach(\App\Models\TourLanguage::orderBy('name')->get() as $lang)
        <option value="{{ $lang->tour_language_id }}" {{ old('tour_language_id', $booking->tour_language_id) == $lang->tour_language_id ? 'selected' : '' }}>
          {{ $lang->name }}
        </option>
      @endforeach
    </select>
    @if($showMyErrors) @error('tour_language_id') <div class="invalid-feedback">{{ $message }}</div> @enderror @endif
  </div>



  {{-- Fecha del Tour (min = hoy) --}}
  <div class="mb-3">
    <label class="form-label">Fecha del Tour</label>
    <input
      type="date"
      name="tour_date"
      min="{{ $today }}"
      class="form-control {{ $showMyErrors && $errors->has('tour_date') ? 'is-invalid':'' }}"
      value="{{ old('tour_date', \Carbon\Carbon::parse($booking->detail->tour_date)->format('Y-m-d')) }}"
      required
    >
    @if($showMyErrors) @error('tour_date') <div class="invalid-feedback">{{ $message }}</div> @enderror @endif
  </div>

  {{-- Hotel --}}
  @php
    $hotelOld = old('hotel_id', (!$booking->detail->is_other_hotel ? $booking->detail->hotel_id : 'other'));
  @endphp
  <div class="mb-3">
    <label class="form-label">Hotel</label>
    <select name="hotel_id" class="form-control {{ $showMyErrors && $errors->has('hotel_id') ? 'is-invalid':'' }}" data-role="hotel-select">
      <option value="">Seleccione hotel</option>
      @foreach(\App\Models\HotelList::where('is_active', true)->orderBy('name')->get() as $h)
        <option value="{{ $h->hotel_id }}" {{ $hotelOld == $h->hotel_id ? 'selected':'' }}>
          {{ $h->name }}
        </option>
      @endforeach
      <option value="other" {{ $hotelOld === 'other' ? 'selected':'' }}>Otro…</option>
    </select>
    @if($showMyErrors) @error('hotel_id') <div class="invalid-feedback">{{ $message }}</div> @enderror @endif
  </div>

  {{-- Otro hotel --}}
  @php
    $isOtherOld = old('is_other_hotel', $booking->detail->is_other_hotel ? 1 : 0);
  @endphp
  <div class="mb-3 {{ $isOtherOld ? '' : 'd-none' }}" data-role="other-hotel-wrapper">
    <label class="form-label">Nombre de otro hotel</label>
    <input type="text" name="other_hotel_name"
           class="form-control {{ $showMyErrors && $errors->has('other_hotel_name') ? 'is-invalid':'' }}"
           value="{{ old('other_hotel_name', $booking->detail->other_hotel_name) }}">
    @if($showMyErrors) @error('other_hotel_name') <div class="invalid-feedback">{{ $message }}</div> @enderror @endif
  </div>
  <input type="hidden" name="is_other_hotel" value="{{ $isOtherOld }}" data-role="is-other-hotel">

  {{-- Adultos --}}
  <div class="mb-3">
    <label class="form-label">Cantidad Adultos</label>
    <input type="number" name="adults_quantity" min="1" required
           class="form-control {{ $showMyErrors && $errors->has('adults_quantity') ? 'is-invalid':'' }}"
           value="{{ old('adults_quantity', $booking->detail->adults_quantity) }}">
    @if($showMyErrors) @error('adults_quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror @endif
  </div>

  {{-- Niños --}}
  <div class="mb-3">
    <label class="form-label">Cantidad Niños</label>
    <input type="number" name="kids_quantity" min="0" max="2" required
           class="form-control {{ $showMyErrors && $errors->has('kids_quantity') ? 'is-invalid':'' }}"
           value="{{ old('kids_quantity', $booking->detail->kids_quantity) }}">
    @if($showMyErrors) @error('kids_quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror @endif
  </div>

  {{-- Notas --}}
  <div class="mb-3">
    <label class="form-label">Notas</label>
    <textarea name="notes" class="form-control {{ $showMyErrors && $errors->has('notes') ? 'is-invalid':'' }}" rows="2">{{ old('notes', $booking->notes) }}</textarea>
    @if($showMyErrors) @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror @endif
  </div>

  {{-- Estado --}}
  <div class="mb-3">
    <label class="form-label">Estado</label>
    <select name="status" class="form-control {{ $showMyErrors && $errors->has('status') ? 'is-invalid':'' }}" required>
      @foreach($statuses as $val => $label)
        <option value="{{ $val }}" {{ old('status', $booking->status) === $val ? 'selected':'' }}>
          {{ $label }}
        </option>
      @endforeach
    </select>
    @if($showMyErrors) @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror @endif
  </div>

</fieldset>
