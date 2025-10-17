@php
  use Carbon\Carbon;
  use App\Models\AppSetting;

  $tz     = config('app.timezone', 'America/Costa_Rica');
  $today  = Carbon::today($tz)->toDateString();

  // ======= Global (AppSetting -> config fallback)
  $gCutoff = (string) AppSetting::get('booking.cutoff_hour', config('booking.cutoff_hour', '18:00'));
  $gLead   = (int)    AppSetting::get('booking.lead_days', (int) config('booking.lead_days', 1));

  // Helper: calcula min reservable considerando si ya pasó el cutoff
  $calc = function (string $cutoff, int $lead) use ($tz) {
      $now = Carbon::now($tz);
      [$hh,$mm] = array_pad(explode(':', $cutoff, 2), 2, '00');
      $cutoffToday = Carbon::create($now->year, $now->month, $now->day, (int)$hh, (int)$mm, 0, $tz);

      $passed = $now->gte($cutoffToday);
      $days   = max(0, (int)$lead) + ($passed ? 1 : 0);
      return [
        'cutoff'       => sprintf('%02d:%02d', (int)$hh, (int)$mm),
        'lead_days'    => (int)$lead,
        'after_cutoff' => $passed,
        'min'          => $now->copy()->addDays($days)->toDateString(),
      ];
  };

  // ======= Tour (override -> global)
  $tCutoff = $tour->cutoff_hour ?: $gCutoff;
  $tLead   = is_null($tour->lead_days) ? $gLead : (int) $tour->lead_days;
  $tourRule = $calc($tCutoff, $tLead);

  // ======= Horarios (pivot -> tour)
  $scheduleRules = [];
  foreach ($tour->schedules->sortBy('start_time') as $s) {
      $pCut = optional($s->pivot)->cutoff_hour;
      $pLd  = optional($s->pivot)->lead_days;
      $sCut = $pCut ?: $tCutoff;
      $sLd  = is_null($pLd) ? $tLead : (int)$pLd;
      $scheduleRules[$s->schedule_id] = $calc($sCut, $sLd);
  }

  // Para minDate inicial si aún no hay horario seleccionado:
  $mins = array_map(fn($r) => $r['min'], $scheduleRules);
  $mins[] = $tourRule['min'];
  $initialMin = min($mins);

  $rulesPayload = [
    'tz'        => $tz,
    'tour'      => $tourRule,
    'schedules' => $scheduleRules,
    'initialMin'=> $initialMin,
  ];
@endphp

<form action="{{ route('carrito.agregar', $tour->tour_id) }}" method="POST"
  class="reservation-box gv-ui p-3 shadow-sm rounded bg-white mb-4 border"
  data-adult-price="{{ $tour->adult_price }}"
  data-kid-price="{{ $tour->kid_price }}">
  @csrf
  <input type="hidden" name="tour_id" value="{{ $tour->tour_id }}">

  {{-- ===== HEADER ===== --}}
  <div class="form-header">
    @guest
      <div class="alert alert-warning d-flex align-items-center gap-2 mb-3">
        <i class="fas fa-lock me-2"></i>
        <div>
          <strong>{{ __('adminlte::adminlte.auth_required_title') ?? 'Debes iniciar sesión para reservar' }}</strong>
          <div class="small">
            {{ __('adminlte::adminlte.auth_required_body') ?? 'Inicia sesión o regístrate para completar tu compra. Los campos se desbloquean al iniciar sesión.' }}
          </div>
        </div>
        <a href="{{ route('login', ['redirect' => url()->current()]) }}" class="btn btn-success gv-cta w-100 mt-3">
          <i class="fas fa-cart-plus me-2"></i> {{ __('adminlte::adminlte.login_now') }}
        </a>
      </div>
    @endguest

    {{-- Título menos pesado (se estiliza en tour.css) --}}
    <h4 class="mb-2">{{ __('adminlte::adminlte.price') }}</h4>

    <div class="price-breakdown mb-3">
      <span class="fw-bold">{{ __('adminlte::adminlte.adult') }}:</span>
      <span class="price-adult fw-bold text-danger">${{ number_format($tour->adult_price, 2) }}</span> |
      <span class="fw-bold">{{ __('adminlte::adminlte.kid') }}:</span>
      <span class="price-kid fw-bold text-danger">${{ number_format($tour->kid_price, 2) }}</span>
    </div>

    {{-- (Se quitó el total del header; ahora vive debajo del selector de personas) --}}
  </div>

  {{-- ===== BODY ===== --}}
  <div class="form-body position-relative">
    <fieldset @guest disabled aria-disabled="true" @endguest>

      {{-- Travelers --}}
      <div class="mb-2">
        <button type="button"
          class="btn traveler-button w-100 d-flex align-items-center justify-content-between"
          data-bs-toggle="modal" data-bs-target="#travelerModal">
          <span class="d-flex align-items-center gap-2">
            <i class="fas fa-user me-1"></i>

            {{-- Contador legacy (oculto) --}}
            <span id="traveler-summary" class="d-none">2</span>

            {{-- Píldoras separadas Adultos / Niños --}}
            <span class="trav-pill">
              <i class="fas fa-male me-1"></i>
              <span id="adult-count">2</span>
            </span>
            <span class="trav-sep">|</span>
            <span class="trav-pill kids d-none">
              <i class="fas fa-child me-1"></i>
              <span id="kid-count">0</span>
            </span>
          </span>
          <i class="fas fa-chevron-down"></i>
        </button>

        {{-- Total + desglose debajo del selector de personas --}}
        <div class="gv-total-inline small mt-2">
          <div class="d-flex justify-content-between">
            <span>{{ __('adminlte::adminlte.total') }}:</span>
            <strong id="reservation-total-price-inline">$0.00</strong>
          </div>
          <div class="text-muted" id="traveler-breakdown">2× {{ __('adminlte::adminlte.adult') }}</div>
        </div>
      </div>

      {{-- Date --}}
      <label class="form-label">{{ __('adminlte::adminlte.select_date') }}</label>
      <input id="tourDateInput" type="text" name="tour_date" class="form-control mb-1"
             placeholder="dd/mm/yyyy" required>
      <div class="form-text" id="cutoffHint"></div>

      {{-- Schedule --}}
      <label class="form-label mt-2">{{ __('adminlte::adminlte.select_time') }}</label>
      <select name="schedule_id" class="form-select mb-1" id="scheduleSelect" required>
        <option value="">-- {{ __('adminlte::adminlte.select_option') }} --</option>
        @foreach($tour->schedules->sortBy('start_time') as $schedule)
          <option value="{{ $schedule->schedule_id }}">
            {{ date('g:i A', strtotime($schedule->start_time)) }} - {{ date('g:i A', strtotime($schedule->end_time)) }}
          </option>
        @endforeach
      </select>
      <div id="noSlotsHelp" class="form-text text-danger mb-3" style="display:none;"></div>

      {{-- Language --}}
      <label class="form-label">{{ __('adminlte::adminlte.select_language') }}</label>
      <select name="tour_language_id" class="form-select mb-3" id="languageSelect" required>
        <option value="">-- {{ __('adminlte::adminlte.select_option') }} --</option>
        @foreach($tour->languages as $lang)
          <option value="{{ $lang->tour_language_id }}">{{ $lang->name }}</option>
        @endforeach
      </select>

      {{-- Hotel --}}
      <label class="form-label">{{ __('adminlte::adminlte.select_hotel') }}</label>
      <select class="form-select mb-3" id="hotelSelect" name="hotel_id">
        <option value="">-- {{ __('adminlte::adminlte.select_option') }} --</option>
        @foreach($hotels as $hotel)
          <option value="{{ $hotel->hotel_id }}">{{ $hotel->name }}</option>
        @endforeach
        <option value="other">{{ __('adminlte::adminlte.hotel_other') }}</option>
      </select>

      {{-- Campo “otro hotel” --}}
      <div class="mb-3 d-none" id="otherHotelWrapper">
        <label for="otherHotelInput" class="form-label">{{ __('adminlte::adminlte.hotel_name') }}</label>
        <input type="text" class="form-control" name="other_hotel_name" id="otherHotelInput"
               placeholder="{{ __('adminlte::adminlte.hotel_name') }}">
        <div class="form-text text-danger mt-1" id="outsideAreaMessage" style="display:none;">
          {{ __('adminlte::adminlte.outside_area')
              ?: 'Has ingresado un hotel personalizado. Contáctanos para confirmar si podemos ofrecer transporte desde ese lugar.' }}
        </div>
      </div>

      {{-- Meeting Point --}}
      <label class="form-label d-flex align-items-center gap-2">
        <i class="fas fa-map-marker-alt"></i> <span>{{ __('adminlte::adminlte.meetingPoint') ?? 'Meeting Point' }}</span>
      </label>
      <select class="form-select mb-3" name="selected_meeting_point" id="meetingPointSelect">
        <option value="">-- {{ __('adminlte::adminlte.select_option') }} --</option>
        @foreach($meetingPoints as $mp)
          <option value="{{ $mp->id }}">
            {{ $mp->name }}{{ $mp->pickup_time ? ' — '.$mp->pickup_time : '' }}
          </option>
        @endforeach
      </select>

      {{-- Hidden fields --}}
      <input type="hidden" name="is_other_hotel" id="isOtherHotel" value="0">
      <input type="hidden" name="adults_quantity" id="adults_quantity" value="2" required>
      <input type="hidden" name="kids_quantity" id="kids_quantity" value="0">
      <input type="hidden" name="selected_pickup_point" id="selectedPickupPoint">
      <input type="hidden" name="selected_meeting_point" id="selectedMeetingPoint">
    </fieldset>
  </div>

  {{-- CTA --}}
  @auth
    <button id="addToCartBtn" type="button" class="btn btn-success gv-cta w-100 mt-3">
      <i class="fas fa-cart-plus me-2"></i> {{ __('adminlte::adminlte.add_to_cart') }}
    </button>
  @else
    <a href="{{ route('login') }}" class="btn btn-success gv-cta w-100 mt-3"
       onclick="return askLoginWithSwal(event, this.href);">
      <i class="fas fa-cart-plus me-2"></i> {{ __('adminlte::adminlte.add_to_cart') }}
    </a>
  @endauth
</form>

@once
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
  <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
@endonce

@push('styles')
<style>
  .trav-pill{
    display:inline-flex; align-items:center; gap:.25rem;
    padding:.125rem .5rem; border:1px solid #e5e7eb; border-radius:999px;
    font-weight:600; line-height:1;
  }
  .trav-pill i{ opacity:.9; }
  .trav-sep{ opacity:.5; }

  .gv-total-inline{
    background:#f8fafb; border:1px dashed #e2e8f0; border-radius:.5rem; padding:.5rem .75rem;
  }
  .gv-total-inline strong{ font-weight:700; }
</style>
@endpush

@push('scripts')
<script>
(function(){
  const RULES = @json($rulesPayload);

  const T = {
    selectFromList: @json(__('adminlte::adminlte.selectFromList')),
    fillThisField:  @json(__('adminlte::adminlte.fillThisField')),
    pickupRequiredTitle: @json(__('adminlte::adminlte.pickupRequiredTitle')),
    pickupRequiredBody:  @json(__('adminlte::adminlte.pickupRequiredBody')),
    ok: @json(__('adminlte::adminlte.ok')),
    selectOption: @json(__('adminlte::adminlte.select_option')),
  };

  const formEl = document.querySelector('form.reservation-box');
  if (!formEl || formEl.dataset.bound === '1') return;
  formEl.dataset.bound = '1';

  /* ========= PRECIOS, CANTIDADES Y TOTAL (se ejecuta SIEMPRE, incluso guest) ========= */
  const adultPrice = Number(formEl?.dataset?.adultPrice || 0);
  const kidPrice   = Number(formEl?.dataset?.kidPrice   || 0);

  const inAdults = document.getElementById('adults_quantity');
  const inKids   = document.getElementById('kids_quantity');

  const totalInlineEl = document.getElementById('reservation-total-price-inline');
  const brkEl         = document.getElementById('traveler-breakdown');
  const adtCntEl      = document.getElementById('adult-count');
  const kidCntEl      = document.getElementById('kid-count');
  const kidsPill      = document.querySelector('.trav-pill.kids');
  const sepEl         = document.querySelector('.trav-sep');

  const fmt = (n) => {
    try { return (new Intl.NumberFormat(undefined, { style: 'currency', currency: 'USD' })).format(n); }
    catch { return `$${Number(n).toFixed(2)}`; }
  };

  function updateTravelerPills(a, k){
    if (adtCntEl) adtCntEl.textContent = a;
    if (kidCntEl) kidCntEl.textContent = k;
    const showKids = Number(k) > 0;
    if (kidsPill)  kidsPill.classList.toggle('d-none', !showKids);
    if (sepEl)     sepEl.classList.toggle('d-none', !showKids);
  }

  function updateBreakdown(a, k){
    if (!brkEl) return;
    const parts = [];
    if (Number(a) > 0) parts.push(`${a}× {{ __('adminlte::adminlte.adult') }}`);
    if (Number(k) > 0) parts.push(`${k}× {{ __('adminlte::adminlte.kid') }}`);
    brkEl.textContent = parts.join(' | ') || `0× {{ __('adminlte::adminlte.adult') }}`;
  }

  function updateTotals(){
    const a = Number(inAdults?.value || 0);
    const k = Number(inKids?.value   || 0);
    const total = (a * adultPrice) + (k * kidPrice);
    if (totalInlineEl) totalInlineEl.textContent = fmt(total);
    updateTravelerPills(a, k);
    updateBreakdown(a, k);
  }

  // Inicializa una vez
  updateTotals();

  // Escucha cambios de los hidden (los actualiza el modal de viajeros)
  ['change','input'].forEach(evt => {
    inAdults?.addEventListener(evt, updateTotals);
    inKids?.addEventListener(evt, updateTotals);
  });

  // Si el modal lanza este evento personalizado:
  window.addEventListener('traveler:updated', updateTotals);

  // Observa el legacy #traveler-summary, por si otro script lo toca
  const legacySum = document.getElementById('traveler-summary');
  if (legacySum) {
    new MutationObserver(() => updateTotals()).observe(legacySum, { childList:true, subtree:true, characterData:true });
  }

  /* ========= HASTA AQUÍ: total y desglose disponibles también para invitados ========= */

  // Bloqueo de envío normal
  formEl.addEventListener('submit', (e) => e.preventDefault());

  window.isAuthenticated = @json(Auth::check());
  window.CART_COUNT_URL  = @json(route('cart.count.public'));

  const fullyBlockedDates = Array.isArray(window.fullyBlockedDates) ? window.fullyBlockedDates : [];
  const blockedGeneral    = Array.isArray(window.blockedGeneral) ? window.blockedGeneral : [];
  const blockedBySchedule = (window.blockedBySchedule && typeof window.blockedBySchedule === 'object') ? window.blockedBySchedule : {};

  const dateInput   = document.getElementById('tourDateInput');
  const scheduleSel = document.getElementById('scheduleSelect');
  const helpMsg     = document.getElementById('noSlotsHelp');
  const hintEl      = document.getElementById('cutoffHint');
  const langSelect  = document.getElementById('languageSelect');
  const hotelSelect = document.getElementById('hotelSelect');
  const meetingSel  = document.getElementById('meetingPointSelect');

  // Si NO está autenticado: deshabilita campos pero conserva el total mostrado
  if (!window.isAuthenticated) {
    if (dateInput) { dateInput.setAttribute('disabled','disabled'); dateInput.setAttribute('readonly','readonly'); }
    scheduleSel && scheduleSel.setAttribute('disabled','disabled');
    langSelect  && langSelect.setAttribute('disabled','disabled');
    hotelSelect && hotelSelect.setAttribute('disabled','disabled');
    meetingSel  && meetingSel.setAttribute('disabled','disabled');
    return; // No seguimos con flatpickr/choices/etc. para invitados
  }

  /* ============= Helpers de reglas/fechas ============= */
  const isoFromDate = (d) => `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
  const ruleForSchedule = (sid) => RULES.schedules[String(sid)] || RULES.tour;
  const minAcrossAll = () => [RULES.tour.min, ...Object.values(RULES.schedules).map(r => r.min)].sort()[0];

  function setHint(rule){
    if (!hintEl || !rule) return;
    const lead = Number(rule.lead_days || 0);
    const co   = rule.cutoff || '--:--';
    const after= rule.after_cutoff ? ' (aplica día siguiente tras cutoff)' : '';
    hintEl.textContent = `Anticipación mínima: ${lead} día(s). Hora de corte: ${co}${after}. Fecha mínima: ${rule.min}.`;
  }

  const canUseScheduleOnDate = (iso, sid) => {
    if (!iso) return false;
    if (fullyBlockedDates.includes(iso)) return false;
    if (blockedGeneral.includes(iso))    return false;
    if ((blockedBySchedule[sid] || []).includes(iso)) return false;
    const r = ruleForSchedule(sid);
    return !(iso < r.min);
  };

  const anyScheduleAvailable = (iso) => BASE_IDS.some((sid) => canUseScheduleOnDate(iso, sid));
  const isDayFullyBlocked = (iso) => {
    if (!iso) return true;
    if (fullyBlockedDates.includes(iso)) return true;
    if (blockedGeneral.includes(iso))    return true;
    return !anyScheduleAvailable(iso);
  };

  /* ============= Choices ============= */
  const scheduleChoices = new Choices(scheduleSel, { searchEnabled:false, shouldSort:false, itemSelectText:'', placeholder:true, placeholderValue:'-- ' + T.selectOption + ' --' });
  const langChoices     = new Choices(langSelect,  { searchEnabled:false, shouldSort:false, itemSelectText:'' });
  const hotelChoices    = new Choices(hotelSelect, { searchEnabled:true,  shouldSort:false, itemSelectText:'' });
  const meetingChoices  = new Choices(meetingSel,  { searchEnabled:true,  shouldSort:false, itemSelectText:'', placeholder:true, placeholderValue:'-- ' + T.selectOption + ' --' });

  const BASE_CHOICES = scheduleChoices._store.choices.filter(c => c.value !== '').map(c => ({ value:String(c.value), label:c.label }));
  const BASE_IDS = BASE_CHOICES.map(o => o.value);

  function rebuildScheduleChoices(iso){
    const ph = [{ value:'', label:'-- ' + T.selectOption + ' --', disabled:true, selected:true }];
    scheduleChoices.clearStore();

    if (!iso || isDayFullyBlocked(iso)) {
      scheduleChoices.setChoices(ph, 'value', 'label', true);
      scheduleChoices.disable();
      helpMsg.textContent = iso ? 'No hay horarios disponibles para esa fecha.' : '';
      helpMsg.style.display = iso ? '' : 'none';
      return;
    }

    const allowed = BASE_CHOICES.map(o => ({ ...o, disabled: !canUseScheduleOnDate(iso, o.value) }));
    scheduleChoices.setChoices(ph.concat(allowed), 'value', 'label', true);

    const hasEnabled = allowed.some(c => !c.disabled);
    if (hasEnabled) { scheduleChoices.enable(); helpMsg.style.display = 'none'; }
    else { scheduleChoices.disable(); helpMsg.textContent = 'No hay horarios disponibles para esa fecha.'; helpMsg.style.display = ''; }
  }

  /* ============= Flatpickr ============= */
  let fp;
  const setupFlatpickr = () => {
    const initialMin = RULES.initialMin || minAcrossAll();

    if (window.flatpickr && dateInput) {
      fp = flatpickr(dateInput, {
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'd/m/Y',
        minDate: initialMin,
        disable: [ (date) => isDayFullyBlocked(isoFromDate(date)) ],
        onChange: (_sel, iso) => rebuildScheduleChoices(iso),
        onReady: (_sel, iso) => {
          setHint(RULES.tour);
          if (!iso) {
            scheduleChoices.disable();
            helpMsg.style.display = 'none';
            fp.setDate(initialMin, false);
            rebuildScheduleChoices(initialMin);
          } else {
            rebuildScheduleChoices(iso);
          }
        }
      });
    } else if (dateInput) {
      dateInput.type = 'date';
      dateInput.min  = initialMin;
      dateInput.addEventListener('change', e => rebuildScheduleChoices(e.target.value));
      scheduleChoices.disable();
    }
  };
  setupFlatpickr();

  /* Cambio de horario -> minDate/hint */
  scheduleSel.addEventListener('change', () => {
    const sid = scheduleSel.value;
    const rule = sid ? ruleForSchedule(sid) : RULES.tour;
    if (fp) fp.set('minDate', sid ? rule.min : minAcrossAll());
    setHint(rule);
    const current = dateInput.value;
    if (current && sid && current < rule.min && fp) fp.setDate(rule.min, true);
    const iso = dateInput.value || (fp ? fp.input.value : null);
    rebuildScheduleChoices(iso);
  });

  /* Hotel "otro" */
  const otherWrap = document.getElementById('otherHotelWrapper');
  const isOtherH  = document.getElementById('isOtherHotel');
  const otherInp  = document.getElementById('otherHotelInput');
  const warnMsg   = document.getElementById('outsideAreaMessage');

  const toggleOther = () => {
    const isOther = (hotelChoices.getValue(true) === 'other');
    otherWrap.classList.toggle('d-none', !isOther);
    if (isOtherH) isOtherH.value = isOther ? 1 : 0;
    if (isOther) { warnMsg && (warnMsg.style.display = ''); otherInp && otherInp.setAttribute('required','required'); }
    else { warnMsg && (warnMsg.style.display = 'none'); if (otherInp) { otherInp.removeAttribute('required'); otherInp.value=''; } }
  };
  hotelSelect.addEventListener('change', toggleOther);
  toggleOther();

  /* Meeting point -> hidden */
  const hiddenMP = document.getElementById('selectedMeetingPoint');
  meetingSel.addEventListener('change', () => { if (hiddenMP) hiddenMP.value = meetingChoices.getValue(true) || ''; });

  /* Mutua exclusión hotel/meeting */
  function validateHotelMeetingPoint() {
    const hotelValue = hotelChoices.getValue(true);
    const meetingValue = meetingChoices.getValue(true);

    if (hotelValue && hotelValue !== '') {
      meetingChoices.disable(); meetingSel.value = ''; if (hiddenMP) hiddenMP.value = '';
    } else { meetingChoices.enable(); }

    if (meetingValue && meetingValue !== '') {
      hotelChoices.disable(); hotelSelect.value = ''; toggleOther(); if (isOtherH) isOtherH.value = 0;
    } else { hotelChoices.enable(); }
  }
  hotelSelect.addEventListener('change', validateHotelMeetingPoint);
  meetingSel.addEventListener('change', validateHotelMeetingPoint);
  validateHotelMeetingPoint();

  /* Mensajes nativos traducidos */
  const setSelectValidity = (el) => el.setCustomValidity(T.selectFromList || '');
  const setInputValidity  = (el) => el.setCustomValidity(T.fillThisField || '');
  [scheduleSel, langSelect, meetingSel, hotelSelect].forEach(el => {
    if (!el) return;
    el.addEventListener('invalid', () => setSelectValidity(el));
    el.addEventListener('change',  () => el.setCustomValidity(''));
    el.addEventListener('input',   () => el.setCustomValidity(''));
  });
  if (dateInput) {
    dateInput.addEventListener('invalid', () => setInputValidity(dateInput));
    dateInput.addEventListener('input',   () => dateInput.setCustomValidity(''));
    dateInput.addEventListener('change',  () => dateInput.setCustomValidity(''));
  }

  /* AJAX Add to cart */
  const addBtn = document.getElementById('addToCartBtn');
  if (!addBtn) return;

  let submitting = false;
  addBtn.addEventListener('click', async () => {
    if (submitting) return;

    if (!formEl.checkValidity()) { formEl.reportValidity(); return; }

    const hotelChoicesObj = hotelChoices;
    const meetingChoicesObj = meetingChoices;

    const isOtherH = document.getElementById('isOtherHotel');
    const otherInp = document.getElementById('otherHotelInput');

    const hotelValue = hotelChoicesObj.getValue(true);
    const isOtherHotel = isOtherH && isOtherH.value === '1';
    const otherHotelName = otherInp && otherInp.value.trim();
    const meetingValue = meetingChoicesObj.getValue(true);

    const hasHotel = (hotelValue && hotelValue !== '' && hotelValue !== 'other') || (isOtherHotel && otherHotelName);
    const hasMeeting = (meetingValue && meetingValue !== '');

    if (!hasHotel && !hasMeeting) {
      await Swal.fire({ icon: 'warning', title: T.pickupRequiredTitle, text: T.pickupRequiredBody, confirmButtonColor: '#198754', confirmButtonText: T.ok });
      return;
    }

    submitting = true;
    const prevHTML = addBtn.innerHTML;
    addBtn.disabled = true;
    addBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>{{ __('adminlte::adminlte.add_to_cart') }}';

    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const fd = new FormData(formEl);

    try {
      const res = await fetch(formEl.action, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: fd
      });

      let data = {}; try { data = await res.json(); } catch(_) {}

      if (!res.ok) {
        const msg = (data && data.message) ? data.message : 'No se pudo agregar el tour. Intenta de nuevo.';
        await Swal.fire({ icon:'error', title:'Error', text:msg }); return;
      }

      const okMsg = (data && data.message) ? data.message : 'Tour añadido al carrito.';
      await Swal.fire({ icon:'success', title:'Success', text:okMsg, confirmButtonColor:'#198754', confirmButtonText:T.ok });

      if (typeof data.count !== 'undefined' && window.setCartCount) {
        window.setCartCount(data.count);
      } else if (window.CART_COUNT_URL && window.setCartCount) {
        try {
          const cRes = await fetch(window.CART_COUNT_URL, { headers: { 'Accept': 'application/json' }});
          const cData = await cRes.json();
          window.setCartCount(cData.count || 0);
        } catch(_) {}
      }

      window.location.reload();

    } catch (err) {
      await Swal.fire({ icon:'error', title:'Error', text:'Error de red. Intenta nuevamente.', confirmButtonText:T.ok });
    } finally {
      addBtn.disabled = false;
      addBtn.innerHTML = prevHTML;
      submitting = false;
    }
  });
})();
</script>
@endpush
