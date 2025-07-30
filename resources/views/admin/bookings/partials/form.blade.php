{{-- Cliente --}}
<div class="mb-3">
  <label class="form-label">Cliente</label>
  <select name="user_id" class="form-control" required>
    @foreach(\App\Models\User::all() as $u)
      <option value="{{ $u->user_id }}"
        @if(($modo === 'editar' && $reserva->user_id == $u->user_id) || old('user_id') == $u->user_id) selected @endif>
        {{ $u->full_name }}
      </option>
    @endforeach
  </select>
</div>

{{-- TOUR --}}
<div class="mb-3">
  <label class="form-label">Tour</label>
  <select name="tour_id" id="{{ $modo === 'editar' ? 'edit_tour_' . $reserva->booking_id : 'selectTour' }}" class="form-control" required>
    <option value="">Seleccione un tour</option>
    @foreach(\App\Models\Tour::with('schedules')->get() as $tour)
      <option value="{{ $tour->tour_id }}"
        data-precio-adulto="{{ $tour->adult_price }}"
        data-precio-nino="{{ $tour->kid_price }}"
        data-schedules='@json($tour->schedules->map(function($s) {
          return [
            "schedule_id" => $s->schedule_id,
            "start_time" => \Carbon\Carbon::parse($s->start_time)->format("g:i A"),
            "end_time" => \Carbon\Carbon::parse($s->end_time)->format("g:i A")
          ];
        }))'
        @if(($modo === 'editar' && $reserva->tour_id == $tour->tour_id) || old('tour_id') == $tour->tour_id) selected @endif
      >
        {{ $tour->name }}
      </option>
    @endforeach
  </select>
</div>

{{-- HORARIO --}}
<div class="mb-3">
  <label class="form-label">Horario</label>
  <select name="schedule_id" id="{{ $modo === 'editar' ? 'edit_schedule_' . $reserva->booking_id : 'selectSchedule' }}" class="form-control" required>
    <option value="">Seleccione un horario</option>
    @if($modo === 'editar')
      @foreach($reserva->detail->tour->schedules as $s)
        <option value="{{ $s->schedule_id }}"
          {{ $reserva->detail->schedule_id == $s->schedule_id ? 'selected' : '' }}>
          {{ \Carbon\Carbon::parse($s->start_time)->format('g:i A') }} – {{ \Carbon\Carbon::parse($s->end_time)->format('g:i A') }}
        </option>
      @endforeach
    @endif
  </select>
</div>

{{-- Idioma --}}
<div class="mb-3">
  <label class="form-label">Idioma</label>
  <select name="tour_language_id" class="form-control" required>
    @foreach(\App\Models\TourLanguage::all() as $lang)
      <option value="{{ $lang->tour_language_id }}"
        @if(($modo === 'editar' && $reserva->tour_language_id == $lang->tour_language_id) || old('tour_language_id') == $lang->tour_language_id) selected @endif>
        {{ $lang->name }}
      </option>
    @endforeach
  </select>
</div>


{{-- Fecha del Tour --}}
<div class="mb-3">
  <label class="form-label">Fecha del Tour</label>
  <input type="date" name="tour_date" class="form-control" required
    value="{{ $modo === 'editar' ? \Carbon\Carbon::parse($reserva->detail->tour_date)->format('Y-m-d') : old('tour_date') }}">
</div>

{{-- Hotel --}}
<div class="mb-3">
  <label class="form-label">Hotel</label>
  <select name="hotel_id"
    id="{{ $modo === 'editar' ? 'edit_hotel_' . $reserva->booking_id : 'selectHotel' }}"
    class="form-control">
    <option value="">Seleccione hotel</option>
    @foreach($hotels as $h)
      <option value="{{ $h->hotel_id }}"
        @if($modo === 'editar' && !$reserva->detail->is_other_hotel && $reserva->detail->hotel_id == $h->hotel_id) selected @endif>
        {{ $h->name }}
      </option>
    @endforeach
    <option value="other" @if($modo === 'editar' && $reserva->detail->is_other_hotel) selected @endif>Otro…</option>
  </select>
</div>

{{-- Otro hotel --}}
<div class="mb-3 {{ $modo === 'editar' && $reserva->detail->is_other_hotel ? '' : 'd-none' }}"
  id="{{ $modo === 'editar' ? 'edit_other_hotel_container_' . $reserva->booking_id : 'otherHotelRegistrarWrapper' }}">
  <label class="form-label">Nombre de otro hotel</label>
  <input type="text" name="other_hotel_name" class="form-control"
    value="{{ $modo === 'editar' ? $reserva->detail->other_hotel_name : old('other_hotel_name') }}"
    placeholder="Escriba el nombre del hotel">
</div>
<input type="hidden"
  name="is_other_hotel"
  id="{{ $modo === 'editar' ? 'edit_is_other_hotel_' . $reserva->booking_id : 'isOtherHotelRegistrar' }}"
  value="{{ $modo === 'editar' ? ($reserva->detail->is_other_hotel ? 1 : 0) : 0 }}">

{{-- Adultos --}}
<div class="mb-3">
  <label class="form-label">Adultos</label>
  <input type="number" name="adults_quantity" class="form-control cantidad-adultos" min="1" required
    value="{{ $modo === 'editar' ? $reserva->detail->adults_quantity : old('adults_quantity') }}">
</div>

{{-- Niños --}}
<div class="mb-3">
  <label class="form-label">Niños</label>
  <input type="number" name="kids_quantity" class="form-control cantidad-ninos" min="0" max="2" required
    value="{{ $modo === 'editar' ? $reserva->detail->kids_quantity : old('kids_quantity') }}">
</div>

{{-- Precio Adulto --}}
<div class="mb-3">
  <label class="form-label">Precio Adulto</label>
  <input type="text" class="form-control precio-adulto" readonly>
</div>

{{-- Precio Niño --}}
<div class="mb-3">
  <label class="form-label">Precio Niño</label>
  <input type="text" class="form-control precio-nino" readonly>
</div>

{{-- Total --}}
<div class="mb-3">
  <label class="form-label">Total a Pagar</label>
  <input type="text" name="total" class="form-control total-pago" readonly>
</div>

{{-- Notas (solo edición) --}}
@if($modo === 'editar')
  <div class="mb-3">
    <label class="form-label">Notas</label>
    <textarea name="notes" class="form-control" rows="2">{{ $reserva->notes }}</textarea>
  </div>
@endif

{{-- Estado --}}
<div class="mb-3">
  <label class="form-label">Estado</label>
  <select name="status" class="form-control" required>
    <option value="pending"   {{ $modo === 'editar' && $reserva->status === 'pending'   ? 'selected' : '' }}>Pending</option>
    <option value="confirmed" {{ $modo === 'editar' && $reserva->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
    <option value="cancelled" {{ $modo === 'editar' && $reserva->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
  </select>
</div>
