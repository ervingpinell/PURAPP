{{-- resources/views/admin/bookings/partials/form-edit.blade.php --}}

@php
  use App\Models\Tour;
  use App\Models\TourLanguage;
  use App\Models\HotelList;
  use App\Models\MeetingPoint;

  /** @var \App\Models\Booking $booking */
  /** @var \App\Models\BookingDetail|null $detail */

  // Asegura que la redención esté disponible aunque no venga eager loaded
  $booking->loadMissing(['redemption.promoCode']);

  // ================== Precarga básica ==================
  $tourId          = old('tour_id',          $booking->tour_id);
  $scheduleId      = old('schedule_id',      $detail->schedule_id ?? null);
  $tourDate        = old('tour_date',        optional($detail->tour_date ?? null)->format('Y-m-d'));
  $langId          = old('tour_language_id', $booking->tour_language_id);
  $adults          = (int) old('adults_quantity',  $detail->adults_quantity ?? 1);
  $kids            = (int) old('kids_quantity',    $detail->kids_quantity   ?? 0);

  $hotelId         = old('hotel_id',         $detail->hotel_id ?? null);
  $isOtherHotel    = (int) old('is_other_hotel', (int)($detail->is_other_hotel ?? 0));
  $otherHotelName  = old('other_hotel_name', $detail->other_hotel_name ?? null);
  $meetingPointId  = old('meeting_point_id', $detail->meeting_point_id ?? null);

  $pickupDefault   = old('pickup_mode', $meetingPointId ? 'point' : 'hotel');

  // Catálogos
  $tours         = Tour::with('schedules')->orderBy('name')->get();
  $languages     = TourLanguage::orderBy('name')->get();
  $hotels        = isset($hotels) ? $hotels : HotelList::where('is_active',1)->orderBy('name')->get();
  $meetingPoints = isset($meetingPoints) ? $meetingPoints : MeetingPoint::where('is_active',1)->orderBy('name')->get();

  // Tour actual (para precargar horarios)
  $currentTour   = $tours->firstWhere('tour_id', (int) $tourId);

  // ================== Snapshots de precios (detalle) ==================
  $snapAdultPrice    = (float) ($detail->adult_price ?? $booking->tour->adult_price ?? 0);
  $snapKidPrice      = (float) ($detail->kid_price   ?? $booking->tour->kid_price   ?? 0);
  $snapshotSubtotal  = isset($detail->total)
                        ? (float) $detail->total
                        : round($snapAdultPrice * $adults + $snapKidPrice * $kids, 2);

  // ================== PROMO: SIEMPRE desde el pivot actual ==================
  $redemption   = $booking->redemption; // pivot con snapshots
  $promoModel   = optional($redemption)->promoCode ?: $booking->promoCode; // accessor (compat)
  $promoCode    = old('promo_code', $promoModel->code ?? '');

  // Operación/importe EXACTOS tal como se aplicaron
  $initOperation = ($redemption && ($redemption->operation_snapshot === 'add')) ? 'add' : 'subtract';

  // Primero intenta el snapshot del pivot; si no viene, calcula desde el modelo
  $initDiscount = (float) ($redemption->applied_amount ?? 0.0);
  if (!$initDiscount && $promoModel) {
      if ($promoModel->discount_percent) {
          $initDiscount = round($snapshotSubtotal * ($promoModel->discount_percent/100), 2);
      } elseif ($promoModel->discount_amount) {
          $initDiscount = (float) $promoModel->discount_amount;
      }
  }

  // Total guardado en cabecera (fuente de verdad del total)
  $savedTotal = (float) ($booking->total ?? 0);

  // ===== i18n fallbacks (por si faltan traducciones) =====
  $tSelectLanguage = __('m_bookings.bookings.placeholders.select_language');
  if ($tSelectLanguage === 'm_bookings.bookings.placeholders.select_language') $tSelectLanguage = 'Selecciona un idioma';
  $tSelectTour = __('m_bookings.bookings.placeholders.select_tour');
  if ($tSelectTour === 'm_bookings.bookings.placeholders.select_tour') $tSelectTour = 'Selecciona un tour';
  $tSelectSchedule = __('m_bookings.bookings.placeholders.select_schedule');
  if ($tSelectSchedule === 'm_bookings.bookings.placeholders.select_schedule') $tSelectSchedule = 'Selecciona un horario';
  $tSelectHotel = __('m_bookings.bookings.placeholders.select_hotel');
  if ($tSelectHotel === 'm_bookings.bookings.placeholders.select_hotel') $tSelectHotel = 'Selecciona un hotel';
  $tEnterHotel = __('m_bookings.bookings.placeholders.enter_hotel_name');
  if ($tEnterHotel === 'm_bookings.bookings.placeholders.enter_hotel_name') $tEnterHotel = 'Escribe el nombre del hotel';
  $tPromoPlaceholder = __('m_bookings.bookings.placeholders.promo_code');
  if ($tPromoPlaceholder === 'm_bookings.bookings.placeholders.promo_code') $tPromoPlaceholder = 'Código promocional';
  $tApply = __('m_bookings.bookings.buttons.apply');
  if ($tApply === 'm_bookings.bookings.buttons.apply') $tApply = 'Aplicar';
  $tRemove = __('m_bookings.bookings.buttons.remove');
  if ($tRemove === 'm_bookings.bookings.buttons.remove') $tRemove = 'Quitar';
  $tPromoLabel = __('m_bookings.bookings.fields.promo_label');
  if ($tPromoLabel === 'm_bookings.bookings.fields.promo_label') $tPromoLabel = 'Descuento';
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
        <option value="">{{ $tSelectLanguage }}</option>
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
    <i class="fas fa-suitcase-rolling me-1"></i> {{ __('m_bookings.details.tour_info') }}
  </div>

  <div class="card-body">
    <div class="row g-3">
      {{-- Tour --}}
      <div class="col-md-12">
        <label class="form-label">
          {{ __('m_bookings.bookings.fields.tour') }} <span class="text-danger">*</span>
        </label>
        <select name="tour_id" id="selectTour" class="form-select @error('tour_id') is-invalid @enderror" required>
          <option value="">{{ $tSelectTour }}</option>
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
          <option value="">{{ $tSelectSchedule }}</option>
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
          <option value="">{{ $tSelectHotel }}</option>
          @foreach($hotels as $h)
            <option value="{{ $h->hotel_id ?? $h->id }}"
              @selected($isOtherHotel ? false : (string)$hotelId === (string)($h->hotel_id ?? $h->id))>
              {{ $h->name }}
            </option>
          @endforeach
          <option value="other" @selected($isOtherHotel === 1)>Otro…</option>
        </select>
        @error('hotel_id') <div class="invalid-feedback">{{ $message }}</div> @enderror

        {{-- Otro hotel --}}
        <div data-role="other-hotel-wrapper" class="mt-2 {{ $isOtherHotel ? '' : 'd-none' }}">
          <input type="text" name="other_hotel_name"
                 class="form-control @error('other_hotel_name') is-invalid @enderror"
                 value="{{ $otherHotelName }}"
                 placeholder="{{ $tEnterHotel }}">
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
        <input type="text" class="form-control" id="adultPriceDisplay" readonly value="${{ number_format($snapAdultPrice,2) }}">
      </div>
      <div class="col-6">
        <label class="form-label small mb-1">{{ __('m_bookings.bookings.fields.child_price') }}</label>
        <input type="text" class="form-control" id="kidPriceDisplay" readonly value="${{ number_format($snapKidPrice,2) }}">
      </div>
    </div>

    <div class="mt-3">
      <label class="form-label small mb-1">Subtotal</label>
      <input type="text" class="form-control" id="subtotalDisplay" readonly value="${{ number_format($snapshotSubtotal,2) }}">
    </div>

    {{-- ===== Código Promocional (Aplicar / Quitar con un botón) ===== --}}
    <div class="mt-3">
      <label class="form-label">{{ __('m_bookings.bookings.fields.promo_code') }}</label>
      <div class="row g-2 align-items-end">
        <div class="col-md-6">
          <input type="text" name="promo_code" id="promoCodeInput" class="form-control"
                 value="{{ $promoCode }}"
                 placeholder="{{ $tPromoPlaceholder }}">
        </div>
        <div class="col-md-3">
          <button type="button" class="btn w-100" id="btnTogglePromo">
            @if($promoCode)
              <i class="fas fa-times"></i> {{ $tRemove }}
            @else
              <i class="fas fa-check"></i> {{ $tApply }}
            @endif
          </button>
        </div>
      </div>

      <div class="mt-2" id="promoFeedback" style="display: {{ $promoCode ? 'block' : 'none' }};">
        @if($promoCode)
          <div class="alert alert-success mb-0">
            {{ __('m_bookings.receipt.promo') }} ({{ $promoCode }}):
            {{ $initOperation === 'add' ? '+' : '-' }}${{ number_format($initDiscount,2) }}
          </div>
        @endif
      </div>
    </div>

    <div class="row mt-3">
      <div class="col-md-6">
        <label class="form-label">{{ $tPromoLabel }}</label>
        <input type="text" class="form-control" id="promoDisplay" readonly
               value="{{ $promoCode ? (($initOperation === 'add' ? '+' : '-') . '$' . number_format($initDiscount,2)) : '$0.00' }}">
      </div>
      <div class="col-md-6">
        <label class="form-label mb-1"><strong>{{ __('m_bookings.bookings.fields.total_to_pay') }}</strong></label>
        <input type="text" class="form-control fw-bold text-success fs-5" id="totalDisplay" readonly
               value="${{ number_format(
                 $savedTotal ?: max(0, ($initOperation === 'add' ? $snapshotSubtotal + $initDiscount
                                                                 : $snapshotSubtotal - $initDiscount)), 2) }}">
      </div>
    </div>

    {{-- Hidden para back y snapshots --}}
    <input type="hidden" name="adult_price" id="hiddenAdultPrice" value="{{ $snapAdultPrice }}">
    <input type="hidden" name="kid_price"   id="hiddenKidPrice"   value="{{ $snapKidPrice }}">
    <input type="hidden" name="subtotal"    id="hiddenSubtotal"   value="{{ $snapshotSubtotal }}">
    <input type="hidden" name="total"       id="hiddenTotal"      value="{{ $savedTotal ?: max(0, ($initOperation === 'add' ? $snapshotSubtotal + $initDiscount : $snapshotSubtotal - $initDiscount)) }}">
  </div>
</div>

{{-- Forzar estado por defecto en edición --}}
<input type="hidden" name="status" value="pending">

{{-- ===================== Scripts: precios, promo y pickup ===================== --}}
<script>
(function(){
  const form       = document.currentScript.closest('form');
  if (!form) return;

  const tourSel    = form.querySelector('#selectTour');
  const schedSel   = form.querySelector('#selectSchedule');
  const adultsInp  = form.querySelector('#adultsQuantity');
  const kidsInp    = form.querySelector('#kidsQuantity');

  const adultDisp  = form.querySelector('#adultPriceDisplay');
  const kidDisp    = form.querySelector('#kidPriceDisplay');
  const subDisp    = form.querySelector('#subtotalDisplay');
  const promoDisp  = form.querySelector('#promoDisplay');
  const totalDisp  = form.querySelector('#totalDisplay');

  const hAdult     = form.querySelector('#hiddenAdultPrice');
  const hKid       = form.querySelector('#hiddenKidPrice');
  const hSub       = form.querySelector('#hiddenSubtotal');
  const hTotal     = form.querySelector('#hiddenTotal');

  const promoInput = form.querySelector('#promoCodeInput');
  const btnToggle  = form.querySelector('#btnTogglePromo');
  const feedback   = form.querySelector('#promoFeedback');

  // Pickup
  const radioHotel   = form.querySelector('#locationHotel');
  const radioPoint   = form.querySelector('#locationMeeting');
  const hotelSection = form.querySelector('#hotelSection');
  const mpSection    = form.querySelector('#meetingPointSection');
  const hotelSelect  = form.querySelector('#selectHotel');
  const otherWrap    = form.querySelector('[data-role="other-hotel-wrapper"]');
  const mpSelect     = form.querySelector('#meetingPointSelect');
  const mpHelp       = form.querySelector('#meetingPointHelp');

  let adultPrice = parseFloat(hAdult?.value || 0);
  let kidPrice   = parseFloat(hKid?.value   || 0);

  // Etiquetas (inyectadas desde Blade)
  const LABEL_APPLY  = @json($tApply);
  const LABEL_REMOVE = @json($tRemove);

  // Estado inicial del cupón (desde snapshot del pivot)
  let promo = {
    code:      (promoInput?.value || '').trim(),
    discount:  Number(@json($initDiscount)),
    operation: (@json($initOperation) === 'add') ? 'add' : 'subtract'
  };

  function setToggleLabel(){
    if (!btnToggle) return;
    if (promo && promo.code) {
      btnToggle.innerHTML = `<i class="fas fa-times"></i> ${LABEL_REMOVE}`;
      btnToggle.classList.remove('btn-primary');
      btnToggle.classList.add('btn-danger');
    } else {
      btnToggle.innerHTML = `<i class="fas fa-check"></i> ${LABEL_APPLY}`;
      btnToggle.classList.remove('btn-danger');
      btnToggle.classList.add('btn-primary');
    }
  }

  function money(v){ return '$' + (Number(v||0).toFixed(2)); }

  function readTourPrices(){
    const opt = tourSel?.selectedOptions?.[0];
    const ap  = parseFloat(opt?.getAttribute('data-adult-price') || NaN);
    const kp  = parseFloat(opt?.getAttribute('data-kid-price')   || NaN);
    if (!Number.isNaN(ap)) adultPrice = ap;
    if (!Number.isNaN(kp)) kidPrice   = kp;
    if (adultDisp) adultDisp.value = money(adultPrice);
    if (kidDisp)   kidDisp.value   = money(kidPrice);
    if (hAdult) hAdult.value = adultPrice;
    if (hKid)   hKid.value   = kidPrice;
  }

  function rebuildSchedulesOnTourChange(){
    if (!tourSel || !schedSel) return;
    const opt        = tourSel.selectedOptions[0];
    const schedules  = JSON.parse(opt?.getAttribute('data-schedules') || '[]');

    const placeholder = document.createElement('option');
    placeholder.value = '';
    placeholder.textContent = @json($tSelectSchedule);

    const prev = schedSel.value;
    schedSel.innerHTML = '';
    schedSel.appendChild(placeholder);

    schedules.forEach(s => {
      const o = document.createElement('option');
      o.value = s.schedule_id;
      o.textContent = `${s.start_time} – ${s.end_time}`;
      schedSel.appendChild(o);
    });

    const stillExists = Array.from(schedSel.options).some(o => o.value === prev);
    if (stillExists) schedSel.value = prev;
  }

  function recalcTotals(){
    const a = parseInt(adultsInp?.value || 0, 10);
    const k = parseInt(kidsInp?.value   || 0, 10);

    const subtotal = Math.max(0, +(a * adultPrice + k * kidPrice).toFixed(2));
    let total      = subtotal;
    let promoText  = '$0.00';

    if (promo && promo.code) {
      const delta = Number(promo.discount || 0);
      if (promo.operation === 'add') {
        total = subtotal + delta;
        promoText = '+' + money(delta).replace('$','$');
      } else {
        total = Math.max(0, subtotal - delta);
        promoText = '-' + money(delta).replace('$','$');
      }
    }

    if (subDisp)   subDisp.value   = money(subtotal);
    if (promoDisp) promoDisp.value = promoText;
    if (totalDisp) totalDisp.value = money(total);

    if (hSub)   hSub.value   = subtotal.toFixed(2);
    if (hTotal) hTotal.value = total.toFixed(2);
  }

  async function applyPromo(){
    const code = (promoInput?.value || '').trim();
    if (!code) return;

    const a = parseInt(adultsInp?.value || 0, 10);
    const k = parseInt(kidsInp?.value   || 0, 10);
    const subtotal = Math.max(0, +(a * adultPrice + k * kidPrice).toFixed(2));

    try {
      const base = @json(route('admin.bookings.verifyPromoCode'));
      const url  = new URL(base, window.location.origin);
      url.searchParams.set('code', code);
      url.searchParams.set('subtotal', subtotal);

      const res  = await fetch(url.toString(), { headers: { 'Accept': 'application/json' }});
      const data = await res.json();

      if (!data || data.valid === false) {
        promo = { code: '', discount: 0, operation: 'subtract' };
        if (feedback) {
          feedback.style.display = 'block';
          feedback.className = 'alert alert-danger mt-2';
          feedback.textContent = data?.message || 'Código inválido';
        }
      } else {
        promo = {
          code: code,
          discount: Number(data.discount_amount || 0),
          operation: (data.operation === 'add') ? 'add' : 'subtract'
        };
        if (feedback) {
          feedback.style.display = 'block';
          feedback.className = 'alert alert-success mt-2';
          feedback.textContent = data?.message || 'Código aplicado';
        }
      }
      setToggleLabel();
      recalcTotals();
    } catch (e) {
      console.error(e);
    }
  }

  function removePromo(){
    promo = { code: '', discount: 0, operation: 'subtract' };
    if (promoInput) promoInput.value = '';
    if (feedback)   feedback.style.display = 'none';
    setToggleLabel();
    recalcTotals();
  }

  // Alterna aplicar/quitar con un solo botón
  btnToggle?.addEventListener('click', () => {
    if (promo && promo.code) {
      removePromo();
    } else {
      applyPromo();
    }
  });

  // Pickup
  function togglePickup(){
    const useHotel = !!(radioHotel && radioHotel.checked);
    if (hotelSection) hotelSection.classList.toggle('d-none', !useHotel);
    if (mpSection)    mpSection.classList.toggle('d-none', useHotel);
  }
  function handleHotelSelect(){
    if (!hotelSelect || !otherWrap) return;
    const isOther = (hotelSelect.value === 'other');
    otherWrap.classList.toggle('d-none', !isOther);
    const hidden = form.querySelector('input[name="is_other_hotel"]');
    if (hidden) hidden.value = isOther ? 1 : 0;
  }
  function updateMeetingPointHelp(){
    if (!mpSelect || !mpHelp) return;
    const opt = mpSelect.selectedOptions[0];
    if (!opt) { mpHelp.innerHTML = ''; return; }
    const t = opt.getAttribute('data-time') || '';
    const d = opt.getAttribute('data-description') || '';
    const m = opt.getAttribute('data-map') || '';
    let html = '';
    if (t) html += `<div><i class="far fa-clock me-1"></i><strong>Hora:</strong> ${t}</div>`;
    if (d) html += `<div><i class="fas fa-map-pin me-1"></i>${d}</div>`;
    if (m) html += `<div><a href="${m}" target="_blank" rel="noopener"><i class="fas fa-external-link-alt me-1"></i>Ver mapa</a></div>`;
    mpHelp.innerHTML = html;
  }

  // Eventos varios
  tourSel?.addEventListener('change', () => { readTourPrices(); rebuildSchedulesOnTourChange(); recalcTotals(); });
  adultsInp?.addEventListener('input', recalcTotals);
  kidsInp?.addEventListener('input', recalcTotals);
  mpSelect?.addEventListener('change', updateMeetingPointHelp);
  radioHotel?.addEventListener('change', togglePickup);
  radioPoint?.addEventListener('change', togglePickup);
  hotelSelect?.addEventListener('change', handleHotelSelect);

  // Init
  setToggleLabel();
  recalcTotals();
  togglePickup();
  handleHotelSelect();
  updateMeetingPointHelp();
})();
</script>
