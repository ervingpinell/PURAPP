{{-- resources/views/admin/bookings/partials/form-edit.blade.php --}}

@php
  use App\Models\Tour;
  use App\Models\TourLanguage;
  use App\Models\HotelList;
  use App\Models\MeetingPoint;

  /** @var \App\Models\Booking $booking */
  /** @var \App\Models\BookingDetail|null $detail */

  // Datos base para precarga
  $tourId          = old('tour_id',          $booking->tour_id);
  $scheduleId      = old('schedule_id',      $detail->schedule_id ?? null);
  $tourDate        = old('tour_date',        optional($detail->tour_date ?? null)->format('Y-m-d'));
  $langId          = old('tour_language_id', $booking->tour_language_id);
  $adults          = old('adults_quantity',  $detail->adults_quantity ?? 1);
  $kids            = old('kids_quantity',    $detail->kids_quantity   ?? 0);

  $hotelId         = old('hotel_id',         $detail->hotel_id ?? null);
  $isOtherHotel    = (int) old('is_other_hotel', (int)($detail->is_other_hotel ?? 0));
  $otherHotelName  = old('other_hotel_name', $detail->other_hotel_name ?? null);

  $meetingPointId  = old('meeting_point_id', $detail->meeting_point_id ?? null);

  // Pickup mode deducido por datos guardados
  $pickupDefault   = old('pickup_mode', $meetingPointId ? 'point' : 'hotel');

  // Catálogos
  $tours         = Tour::with('schedules')->orderBy('name')->get();
  $languages     = TourLanguage::orderBy('name')->get();
  $hotels        = isset($hotels) ? $hotels : HotelList::where('is_active',1)->orderBy('name')->get();
  $meetingPoints = isset($meetingPoints) ? $meetingPoints : MeetingPoint::where('is_active',1)->orderBy('name')->get();

  // Para precargar schedules del tour actual
  $currentTour   = $tours->firstWhere('tour_id', (int) $tourId);
@endphp

{{-- ===================== Cliente (solo lectura) ===================== --}}
<div class="card mb-3 border-0 shadow-sm">
  <div class="card-header bg-success text-white fw-semibold">
    <i class="fas fa-user me-1"></i> {{ __('m_bookings.details.customer_info') }}
  </div>

  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label">{{ __('m_bookings.bookings.fields.customer') }}</label>
        <input type="text" class="form-control" value="{{ $booking->user->full_name ?? 'N/A' }}" disabled>
      </div>
      <div class="col-md-4">
        <label class="form-label">{{ __('m_bookings.bookings.fields.email') }}</label>
        <input type="text" class="form-control" value="{{ $booking->user->email ?? 'N/A' }}" disabled>
      </div>
      <div class="col-md-4">
        <label class="form-label">{{ __('m_bookings.bookings.fields.phone') }}</label>
        <input type="text" class="form-control" value="{{ $booking->user->full_phone ?? 'N/A' }}" disabled>
      </div>
    </div>
    <div class="form-text mt-1">
      {{ __('m_bookings.bookings.messages.customer_locked') }}
    </div>

    {{-- Idioma (editable) --}}
    <div class="mt-3">
      <label class="form-label">
        {{ __('m_bookings.bookings.fields.language') }} <span class="text-danger">*</span>
      </label>
      <select name="tour_language_id" id="languageSelect" class="form-select @error('tour_language_id') is-invalid @enderror" required>
        <option value="">{{ __('m_bookings.bookings.placeholders.select_language') }}</option>
        @foreach($languages as $lang)
          <option value="{{ $lang->tour_language_id }}" @selected((string)$langId === (string)$lang->tour_language_id)>
            {{ $lang->name }}
          </option>
        @endforeach
      </select>
      @error('tour_language_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
  </div>
</div>

{{-- ===================== Tour / Horario / Fecha / Cantidades ===================== --}}
<div class="card mb-3 border-0 shadow-sm">
  <div class="card-header bg-primary text-white fw-semibold">
    <i class="fas a-suitcase-rolling me-1"></i> {{ __('m_bookings.details.tour_info') }}
  </div>

  <div class="card-body">
    <div class="row g-3">
      {{-- Tour --}}
      <div class="col-md-12">
        <label class="form-label">
          {{ __('m_bookings.bookings.fields.tour') }} <span class="text-danger">*</span>
        </label>
        <select name="tour_id" id="selectTour" class="form-select @error('tour_id') is-invalid @enderror" required>
          <option value="">{{ __('m_bookings.bookings.placeholders.select_tour') }}</option>
          @foreach($tours as $tour)
            <option value="{{ $tour->tour_id }}"
                    data-adult-price="{{ $tour->adult_price }}"
                    data-kid-price="{{ $tour->kid_price }}"
                    data-schedules='@json(
                      $tour->schedules->map(fn($s)=>[
                        "schedule_id"=>$s->schedule_id,
                        "start_time"=>\Carbon\Carbon::parse($s->start_time)->format("g:i A"),
                        "end_time"=>\Carbon\Carbon::parse($s->end_time)->format("g:i A")
                      ])
                    )'
                    @selected((string)$tourId === (string)$tour->tour_id)>
              {{ $tour->name }}
            </option>
          @endforeach
        </select>
        @error('tour_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>

      {{-- Horario --}}
      <div class="col-md-12">
        <label class="form-label">
          {{ __('m_bookings.bookings.fields.schedule') }} <span class="text-danger">*</span>
        </label>
        <select name="schedule_id" id="selectSchedule" class="form-select @error('schedule_id') is-invalid @enderror" required>
          <option value="">{{ __('m_bookings.bookings.placeholders.select_schedule') }}</option>
          @if($currentTour && $currentTour->schedules->count())
            @foreach($currentTour->schedules as $s)
              @php
                $start = \Carbon\Carbon::parse($s->start_time)->format('g:i A');
                $end   = \Carbon\Carbon::parse($s->end_time)->format('g:i A');
              @endphp
              <option value="{{ $s->schedule_id }}" @selected((string)$scheduleId === (string)$s->schedule_id)>
                {{ $start }} – {{ $end }}
              </option>
            @endforeach
          @endif
        </select>
        @error('schedule_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>

      {{-- Fecha del Tour --}}
      <div class="col-md-12">
        <label class="form-label">
          {{ __('m_bookings.bookings.fields.tour_date') }} <span class="text-danger">*</span>
        </label>
        <input type="date" name="tour_date" id="tourDate"
               class="form-control @error('tour_date') is-invalid @enderror"
               value="{{ $tourDate }}" required onfocus="this.showPicker()">
        @error('tour_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>

      {{-- Adultos --}}
      <div class="col-md-6">
        <label class="form-label">
          {{ __('m_bookings.bookings.fields.adults') }} <span class="text-danger">*</span>
        </label>
        <input type="number" name="adults_quantity" id="adultsQuantity"
               class="form-control adults-quantity @error('adults_quantity') is-invalid @enderror"
               min="1" value="{{ (int)$adults }}" required>
        @error('adults_quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>

      {{-- Niños --}}
      <div class="col-md-6">
        <label class="form-label">{{ __('m_bookings.bookings.fields.children') }}</label>
        <input type="number" name="kids_quantity" id="kidsQuantity"
               class="form-control kids-quantity @error('kids_quantity') is-invalid @enderror"
               min="0" value="{{ (int)$kids }}">
        @error('kids_quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>
    </div>
  </div>
</div>

{{-- ===================== Ubicación de Recogida ===================== --}}
<div class="card mb-3 border-0 shadow-sm">
  <div class="card-header bg-info text-white fw-semibold">
    <i class="fas fa-map-marker-alt me-2"></i>{{ __('m_bookings.bookings.fields.pickup_location') }}
  </div>
  <div class="card-body">
    {{-- Radios --}}
    <div class="mb-3">
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="pickup_mode" id="locationHotel" value="hotel"
          {{ $pickupDefault === 'hotel' ? 'checked' : '' }}>
        <label class="form-check-label" for="locationHotel">
          <i class="fas fa-hotel me-1"></i>{{ __('m_bookings.bookings.fields.hotel') }}
        </label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="pickup_mode" id="locationMeeting" value="point"
          {{ $pickupDefault === 'point' ? 'checked' : '' }}>
        <label class="form-check-label" for="locationMeeting">
          <i class="fas fa-map-pin me-1"></i>{{ __('m_bookings.bookings.fields.meeting_point') }}
        </label>
      </div>
    </div>

    {{-- Contenedor dinámico --}}
    <div id="pickupContainer">
      {{-- Hotel --}}
      <div id="hotelSection" class="{{ $pickupDefault === 'hotel' ? '' : 'd-none' }}">
        <label class="form-label">{{ __('m_bookings.bookings.fields.hotel') }}</label>
        <select name="hotel_id" id="selectHotel"
                class="form-select @error('hotel_id') is-invalid @enderror">
          <option value="">{{ __('m_bookings.bookings.placeholders.select_hotel') }}</option>
          @foreach($hotels as $h)
            <option value="{{ $h->hotel_id ?? $h->id }}"
              @selected($isOtherHotel ? false : (string)$hotelId === (string)($h->hotel_id ?? $h->id))>
              {{ $h->name }}
            </option>
          @endforeach
          <option value="other" @selected($isOtherHotel === 1)>Otro…</option>
        </select>
        @error('hotel_id') <div class="invalid-feedback">{{ $message }}</div> @enderror

        {{-- Otro hotel (nota: data-role para scripts del modal de edición) --}}
        <div data-role="other-hotel-wrapper" class="mt-2 {{ $isOtherHotel ? '' : 'd-none' }}">
          <input type="text" name="other_hotel_name"
                 class="form-control @error('other_hotel_name') is-invalid @enderror"
                 value="{{ $otherHotelName }}"
                 placeholder="{{ __('m_bookings.bookings.placeholders.enter_hotel_name') }}">
          @error('other_hotel_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <input type="hidden" name="is_other_hotel" value="{{ $isOtherHotel }}">
      </div>

      {{-- Meeting Point --}}
      <div id="meetingPointSection" class="{{ $pickupDefault === 'point' ? '' : 'd-none' }}">
        <label class="form-label">{{ __('m_bookings.bookings.fields.meeting_point') }}</label>
        <select name="meeting_point_id" id="meetingPointSelect"
                class="form-select @error('meeting_point_id') is-invalid @enderror">
          <option value="">{{ __('m_bookings.bookings.placeholders.select_point') }}</option>
          @foreach($meetingPoints as $mp)
            <option value="{{ $mp->id }}"
                    data-time="{{ $mp->pickup_time }}"
                    data-description="{{ $mp->description }}"
                    data-map="{{ $mp->map_url }}"
                    @selected((string)$meetingPointId === (string)$mp->id)>
              {{ $mp->name }}{{ $mp->pickup_time ? ' — '.$mp->pickup_time : '' }}
            </option>
          @endforeach
        </select>
        @error('meeting_point_id') <div class="invalid-feedback">{{ $message }}</div> @enderror

        <div id="meetingPointHelp" class="form-text mt-2 small">
          @php
            $mpSel = $meetingPoints->firstWhere('id', (int)$meetingPointId);
          @endphp
          @if($mpSel)
            @if($mpSel->pickup_time)
              <div><i class="far fa-clock me-1"></i><strong>Hora:</strong> {{ $mpSel->pickup_time }}</div>
            @endif
            @if($mpSel->description)
              <div><i class="fas fa-map-pin me-1"></i>{{ $mpSel->description }}</div>
            @endif
            @if($mpSel->map_url)
              <div><a href="{{ $mpSel->map_url }}" target="_blank" rel="noopener">
                <i class="fas fa-external-link-alt me-1"></i>Ver mapa</a></div>
            @endif
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ===================== Resumen de Precios (edición) ===================== --}}
<div class="card mb-3 border-0 shadow-sm">
  <div class="card-header bg-danger text-white fw-semibold">
    <i class="fas fa-calculator me-2"></i>{{ __('m_bookings.bookings.pricing.title') }}
  </div>
  <div class="card-body">
    <div class="row g-2">
      <div class="col-6">
        <label class="form-label small mb-1">{{ __('m_bookings.bookings.fields.adult_price') }}</label>
        <input type="text" class="form-control" id="adultPriceDisplay" readonly value="$0.00">
      </div>
      <div class="col-6">
        <label class="form-label small mb-1">{{ __('m_bookings.bookings.fields.child_price') }}</label>
        <input type="text" class="form-control" id="kidPriceDisplay" readonly value="$0.00">
      </div>
    </div>

    <div class="mt-3">
      <label class="form-label small mb-1">Subtotal</label>
      <input type="text" class="form-control" id="subtotalDisplay" readonly value="$0.00">
    </div>

    <div class="mt-3">
      <label class="form-label mb-1"><strong>{{ __('m_bookings.bookings.fields.total_to_pay') }}</strong></label>
      <input type="text" class="form-control fw-bold text-success fs-5" id="totalDisplay" readonly value="$0.00">
    </div>

    {{-- Campos ocultos para enviar al backend (por si los usas) --}}
    <input type="hidden" name="adult_price" id="hiddenAdultPrice" value="{{ old('adult_price', $detail->adult_price ?? 0) }}">
    <input type="hidden" name="kid_price" id="hiddenKidPrice" value="{{ old('kid_price',   $detail->kid_price   ?? 0) }}">
    <input type="hidden" name="subtotal" id="hiddenSubtotal" value="{{ old('subtotal', ($detail->adults_quantity ?? 0) * ($detail->adult_price ?? 0) + ($detail->kids_quantity ?? 0) * ($detail->kid_price ?? 0)) }}">
    <input type="hidden" name="total" id="hiddenTotal" value="{{ old('total', $booking->total ?? 0) }}">
  </div>
</div>

{{-- Forzar estado por defecto en edición --}}
<input type="hidden" name="status" value="pending">

{{-- ====== Script pequeño para recalcular precios en el modal de edición ====== --}}
<script>
(function(){
  const form = document.currentScript.closest('form');
  if (!form) return;

  const tourSel   = form.querySelector('#selectTour');
  const schedSel  = form.querySelector('#selectSchedule');
  const adultsInp = form.querySelector('#adultsQuantity');
  const kidsInp   = form.querySelector('#kidsQuantity');

  const adultDisp = form.querySelector('#adultPriceDisplay');
  const kidDisp   = form.querySelector('#kidPriceDisplay');
  const subDisp   = form.querySelector('#subtotalDisplay');
  const totalDisp = form.querySelector('#totalDisplay');

  const hAdult    = form.querySelector('#hiddenAdultPrice');
  const hKid      = form.querySelector('#hiddenKidPrice');
  const hSub      = form.querySelector('#hiddenSubtotal');
  const hTotal    = form.querySelector('#hiddenTotal');

  let adultPrice = 0, kidPrice = 0;

  function format(v){ return '$' + (Number(v||0).toFixed(2)); }

  function readTourPrices(){
    const opt = tourSel?.selectedOptions?.[0];
    adultPrice = parseFloat(opt?.getAttribute('data-adult-price') || 0);
    kidPrice   = parseFloat(opt?.getAttribute('data-kid-price')   || 0);

    if (adultDisp) adultDisp.value = format(adultPrice);
    if (kidDisp)   kidDisp.value   = format(kidPrice);

    if (hAdult) hAdult.value = adultPrice;
    if (hKid)   hKid.value   = kidPrice;
  }

  function recalc(){
    const a = parseInt(adultsInp?.value || 0, 10);
    const k = parseInt(kidsInp?.value   || 0, 10);

    const subtotal = (a * adultPrice) + (k * kidPrice);
    const total    = subtotal; // en edición no aplicamos promo aquí

    if (subDisp)  subDisp.value  = format(subtotal);
    if (totalDisp) totalDisp.value = format(total);

    if (hSub)   hSub.value   = subtotal.toFixed(2);
    if (hTotal) hTotal.value = total.toFixed(2);
  }

  function rebuildSchedulesOnTourChange(){
    if (!tourSel || !schedSel) return;
    const opt = tourSel.selectedOptions[0];
    const schedules = JSON.parse(opt?.getAttribute('data-schedules') || '[]');

    // Reconstruir opciones
    const placeholder = document.createElement('option');
    placeholder.value = '';
    placeholder.textContent = @json(__('m_bookings.bookings.placeholders.select_schedule'));

    schedSel.innerHTML = '';
    schedSel.appendChild(placeholder);

    schedules.forEach(s => {
      const o = document.createElement('option');
      o.value = s.schedule_id;
      o.textContent = `${s.start_time} – ${s.end_time}`;
      schedSel.appendChild(o);
    });

    // No forzamos selección para no perder la que ya existe si coincide
  }

  // Eventos
  tourSel?.addEventListener('change', () => {
    readTourPrices();
    rebuildSchedulesOnTourChange();
    recalc();
  });
  adultsInp?.addEventListener('input', recalc);
  kidsInp?.addEventListener('input', recalc);

  // Init al cargar el modal
  readTourPrices();
  recalc();
})();
</script>
