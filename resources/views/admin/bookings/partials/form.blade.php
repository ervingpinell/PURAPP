{{-- Cliente --}}
<div class="mb-3">
  <label class="form-label">Cliente</label>
  <select name="user_id" class="form-control @error('user_id') is-invalid @enderror" required>
    <option value="">Seleccione cliente</option>
    @foreach(\App\Models\User::select('user_id','full_name')->orderBy('full_name')->get() as $u)
      <option value="{{ $u->user_id }}" {{ old('user_id') == $u->user_id ? 'selected' : '' }}>
        {{ $u->full_name }}
      </option>
    @endforeach
  </select>
  @error('user_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

{{-- Tour --}}
<div class="mb-3">
  <label class="form-label">Tour</label>
  <select name="tour_id" id="selectTour" class="form-control @error('tour_id') is-invalid @enderror" required>
    <option value="">Seleccione un tour</option>
    @foreach(\App\Models\Tour::with('schedules')->orderBy('name')->get() as $tour)
      <option value="{{ $tour->tour_id }}"
        data-precio-adulto="{{ $tour->adult_price }}"
        data-precio-nino="{{ $tour->kid_price }}"
        data-schedules='@json($tour->schedules->map(fn($s)=>[
          "schedule_id"=>$s->schedule_id,
          "start_time"=>\Carbon\Carbon::parse($s->start_time)->format("g:i A"),
          "end_time"=>\Carbon\Carbon::parse($s->end_time)->format("g:i A")
        ]))'
        {{ old('tour_id') == $tour->tour_id ? 'selected' : '' }}>
        {{ $tour->name }}
      </option>
    @endforeach
  </select>
  @error('tour_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

{{-- Horario --}}
<div class="mb-3">
  <label class="form-label">Horario</label>
  <select name="schedule_id" id="selectSchedule" class="form-control @error('schedule_id') is-invalid @enderror" required>
    <option value="">Seleccione un horario</option>
    {{-- Se llena por JS según el tour elegido --}}
  </select>
  @error('schedule_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

{{-- Idioma --}}
<div class="mb-3">
  <label class="form-label">Idioma</label>
  <select name="tour_language_id" class="form-control @error('tour_language_id') is-invalid @enderror" required>
    <option value="">Seleccione idioma</option>
    @foreach(\App\Models\TourLanguage::orderBy('name')->get() as $lang)
      <option value="{{ $lang->tour_language_id }}" {{ old('tour_language_id') == $lang->tour_language_id ? 'selected' : '' }}>
        {{ $lang->name }}
      </option>
    @endforeach
  </select>
  @error('tour_language_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

{{-- Fecha del Tour --}}
<div class="mb-3">
  <label class="form-label">Fecha del Tour</label>
  <input
    type="date"
    name="tour_date"
    class="form-control @error('tour_date') is-invalid @enderror"
    required
    value="{{ old('tour_date') }}"
  >
  @error('tour_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

{{-- Hotel --}}
<div class="mb-3">
  <label class="form-label">Hotel</label>
  <select name="hotel_id" id="hotel_id" class="form-control @error('hotel_id') is-invalid @enderror">
    <option value="">Seleccione hotel</option>
    @foreach($hotels as $h)
      <option value="{{ $h->hotel_id }}" {{ old('hotel_id') == $h->hotel_id ? 'selected' : '' }}>
        {{ $h->name }}
      </option>
    @endforeach
    <option value="other" {{ old('is_other_hotel') ? 'selected' : '' }}>Otro…</option>
  </select>
  @error('hotel_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

{{-- Otro hotel --}}
<div class="mb-3 {{ old('is_other_hotel') ? '' : 'd-none' }}" id="other_hotel_wrapper">
  <label class="form-label">Nombre de otro hotel</label>
  <input type="text" name="other_hotel_name" id="other_hotel_name"
         class="form-control @error('other_hotel_name') is-invalid @enderror"
         value="{{ old('other_hotel_name') }}"
         placeholder="Escriba el nombre del hotel">
  @error('other_hotel_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
<input type="hidden" name="is_other_hotel" id="is_other_hotel" value="{{ old('is_other_hotel', 0) }}">

{{-- Adultos --}}
<div class="mb-3">
  <label class="form-label">Adultos</label>
  <input type="number" name="adults_quantity"
         class="form-control @error('adults_quantity') is-invalid @enderror"
         min="1" required
         value="{{ old('adults_quantity', 1) }}">
  @error('adults_quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

{{-- Niños --}}
<div class="mb-3">
  <label class="form-label">Niños</label>
  <input type="number" name="kids_quantity"
         class="form-control @error('kids_quantity') is-invalid @enderror"
         min="0" max="2" required
         value="{{ old('kids_quantity', 0) }}">
  @error('kids_quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

{{-- Estado --}}
<div class="mb-3">
  <label class="form-label">Estado</label>
  <select name="status" class="form-control @error('status') is-invalid @enderror" required>
    <option value="">Seleccione estado</option>
    <option value="pending"   {{ old('status') === 'pending'   ? 'selected':'' }}>Pending</option>
    <option value="confirmed" {{ old('status') === 'confirmed' ? 'selected':'' }}>Confirmed</option>
    <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected':'' }}>Cancelled</option>
  </select>
  @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

{{-- Totales solo lectura (si usas cálculo en JS) --}}
<div class="mb-3">
  <label class="form-label">Precio Adulto</label>
  <input type="text" class="form-control precio-adulto" readonly>
</div>
<div class="mb-3">
  <label class="form-label">Precio Niño</label>
  <input type="text" class="form-control precio-nino" readonly>
</div>
<div class="mb-3">
  <label class="form-label">Total a Pagar</label>
  <input type="text" name="total" class="form-control total-pago" readonly>
</div>
