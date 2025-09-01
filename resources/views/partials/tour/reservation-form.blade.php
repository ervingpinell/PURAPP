@php
  $tz    = config('app.timezone', 'America/Costa_Rica');
  $today = \Carbon\Carbon::today($tz)->toDateString();

  // 🚦 Fecha mínima reservable según cutoff/lead-days
  $minBookable = \App\Support\BookingRules::earliestBookableDate()->toDateString();

  // 🔧 Lee ajustes desde AppSetting (con fallback a config)
  $cutoffRaw = \App\Models\AppSetting::get('booking.cutoff_hour', config('booking.cutoff_hour', '18:00')); // "HH:MM"
  $leadDays  = (int) \App\Models\AppSetting::get('booking.lead_days', (int) config('booking.lead_days', 1));

  // ⏱️ Normaliza hora a HH:MM (24h) en TZ de la app
  try {
      $cutoffHM = \Carbon\Carbon::createFromFormat('H:i', (string)$cutoffRaw, $tz)->format('H:i');
  } catch (\Throwable $e) {
      $cutoffHM = '18:00';
  }
@endphp

<form action="{{ route('carrito.agregar', $tour->tour_id) }}" method="POST"
  class="reservation-box gv-ui p-3 shadow-sm rounded bg-white mb-4 border"
  data-adult-price="{{ $tour->adult_price }}"
  data-kid-price="{{ $tour->kid_price }}"
  data-cutoff="{{ $cutoffHM }}"
  data-lead-days="{{ $leadDays }}">
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

    <h3 class="fw-bold fs-5 mb-2">{{ __('adminlte::adminlte.price') }}</h3>
    <div class="price-breakdown mb-3">
      <span class="fw-bold">{{ __('adminlte::adminlte.adult') }}:</span>
      <span class="price-adult fw-bold text-danger">${{ number_format($tour->adult_price, 2) }}</span> |
      <span class="fw-bold">{{ __('adminlte::adminlte.kid') }}:</span>
      <span class="price-kid fw-bold text-danger">${{ number_format($tour->kid_price, 2) }}</span>
    </div>

    <p class="fw-bold mb-3 gv-total">
      {{ __('adminlte::adminlte.total') }}:
      <span id="reservation-total-price" class="text-danger">$0.00</span>
    </p>
  </div>

  {{-- ===== BODY ===== --}}
  <div class="form-body position-relative">
    <fieldset @guest disabled aria-disabled="true" @endguest>

      {{-- ===== Travelers ===== --}}
      <div class="mb-2">
        <button type="button"
          class="btn traveler-button w-100 d-flex align-items-center justify-content-between"
          data-bs-toggle="modal" data-bs-target="#travelerModal">
          <span><i class="fas fa-user me-2"></i> <span id="traveler-summary">2</span></span>
          <i class="fas fa-chevron-down"></i>
        </button>
      </div>

      {{-- ===== Date ===== --}}
      <label class="form-label">{{ __('adminlte::adminlte.select_date') }}</label>
      <input id="tourDateInput" type="text" name="tour_date" class="form-control mb-1"
             placeholder="dd/mm/yyyy" required>

      <div class="form-text" id="cutoffHint">
        @if ($leadDays === 0)
          {{ __('Reservas para hoy cierran a las :time', ['time' => $cutoffHM]) }}
        @elseif ($leadDays === 1)
          {{ __('Reservas para mañana cierran hoy a las :time', ['time' => $cutoffHM]) }}
        @else
          {{ __('Reservas para fechas a :days día(s) cierran el día anterior a las :time', ['days' => $leadDays, 'time' => $cutoffHM]) }}
        @endif
      </div>

      {{-- ===== Schedule ===== --}}
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

      {{-- ===== Language ===== --}}
      <label class="form-label">{{ __('adminlte::adminlte.select_language') }}</label>
      <select name="tour_language_id" class="form-select mb-3" required>
        <option value="">-- {{ __('adminlte::adminlte.select_option') }} --</option>
        @foreach($tour->languages as $lang)
          <option value="{{ $lang->tour_language_id }}">{{ $lang->name }}</option>
        @endforeach
      </select>

      {{-- ===== Hotel ===== --}}
      <label class="form-label">{{ __('adminlte::adminlte.select_hotel') }}</label>
      <select class="form-select mb-3" id="hotelSelect" name="hotel_id">
        <option value="">-- {{ __('adminlte::adminlte.select_option') }} --</option>
        @foreach($hotels as $hotel)
          <option value="{{ $hotel->hotel_id }}">{{ $hotel->name }}</option>
        @endforeach
        <option value="other">{{ __('adminlte::adminlte.hotel_other') }}</option>
      </select>

      <div class="mb-3 d-none" id="otherHotelWrapper">
        <label for="otherHotelInput" class="form-label">{{ __('adminlte::adminlte.hotel_name') }}</label>
        <input type="text" class="form-control" name="other_hotel_name" id="otherHotelInput"
               placeholder="{{ __('adminlte::adminlte.hotel_name') }}">
        <div class="form-text text-danger mt-1" id="outsideAreaMessage" style="display:none;">
          {{ __('adminlte::adminlte.outside_area')
              ?: 'Has ingresado un hotel personalizado. Contáctanos para confirmar si podemos ofrecer transporte desde ese lugar.' }}
        </div>
      </div>

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

<style>
  .gv-ui .form-control,
  .gv-ui .form-select,
  .gv-ui .choices__inner { background-color: #fff !important; }
  .form-body { position: relative; }
</style>

@once
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
  <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
@endonce

@push('scripts')
<script>
(function(){
  const formEl = document.querySelector('form.reservation-box');
  if (!formEl) return;

  if (formEl.dataset.bound === '1') return;
  formEl.dataset.bound = '1';

  formEl.addEventListener('submit', (e) => e.preventDefault());

  window.isAuthenticated = @json(Auth::check());
  window.CART_COUNT_URL  = @json(route('cart.count.public'));
  const todayIso    = @json($today);
  const minBookable = @json($minBookable);

  const fullyBlockedDates = Array.isArray(window.fullyBlockedDates) ? window.fullyBlockedDates : [];
  const blockedGeneral    = Array.isArray(window.blockedGeneral) ? window.blockedGeneral : [];
  const blockedBySchedule = (window.blockedBySchedule && typeof window.blockedBySchedule === 'object') ? window.blockedBySchedule : {};

  const dateInput   = document.getElementById('tourDateInput');
  const scheduleSel = document.getElementById('scheduleSelect');
  const helpMsg     = document.getElementById('noSlotsHelp');
  const langSelect  = document.querySelector('select[name="tour_language_id"]');
  const hotelSelect = document.getElementById('hotelSelect');

  if (!window.isAuthenticated) {
    if (dateInput) { dateInput.setAttribute('disabled', 'disabled'); dateInput.setAttribute('readonly', 'readonly'); }
    scheduleSel && scheduleSel.setAttribute('disabled', 'disabled');
    langSelect  && langSelect.setAttribute('disabled', 'disabled');
    hotelSelect && hotelSelect.setAttribute('disabled', 'disabled');
    return;
  }

  const scheduleChoices = new Choices(scheduleSel, { searchEnabled:false, shouldSort:false, itemSelectText:'', placeholder:true, placeholderValue:'-- {{ __('adminlte::adminlte.select_option') }} --' });
  const langChoices  = new Choices(langSelect,  { searchEnabled:false, shouldSort:false, itemSelectText:'' });
  const hotelChoices = new Choices(hotelSelect, { searchEnabled:true,  shouldSort:false, itemSelectText:'' });

  const BASE_CHOICES = scheduleChoices._store.choices.filter(c => c.value !== '').map(c => ({ value:String(c.value), label:c.label }));
  const SCHEDULE_IDS = BASE_CHOICES.map(o => o.value);

  const isoFromDate = (d) => `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
  const isDayFullyBlocked = (iso) => iso && (fullyBlockedDates.includes(iso) || blockedGeneral.includes(iso) || (SCHEDULE_IDS.length && SCHEDULE_IDS.every(id => (blockedBySchedule[id] || []).includes(iso))));
  const isBlockedForSchedule = (iso, sid) => blockedGeneral.includes(iso) || (blockedBySchedule[sid] || []).includes(iso);

  function rebuildScheduleChoices(iso){
    const ph = [{ value:'', label:'-- {{ __('adminlte::adminlte.select_option') }} --', disabled:true, selected:true }];
    if (!iso || isDayFullyBlocked(iso)) {
      scheduleChoices.clearStore();
      scheduleChoices.setChoices(ph, 'value', 'label', true);
      scheduleChoices.disable();
      helpMsg.textContent = iso ? @json(__('adminlte::adminlte.no_times_for_day') ?? 'No hay horarios disponibles para esa fecha.') : '';
      helpMsg.style.display = iso ? '' : 'none';
      return;
    }
    const allowed = BASE_CHOICES.filter(o => !isBlockedForSchedule(iso, o.value));
    scheduleChoices.clearStore();
    scheduleChoices.setChoices(ph.concat(allowed), 'value', 'label', true);
    if (allowed.length === 0) {
      scheduleChoices.disable();
      helpMsg.textContent = @json(__('adminlte::adminlte.no_times_for_day') ?? 'No hay horarios disponibles para esa fecha.');
      helpMsg.style.display = '';
    } else {
      scheduleChoices.enable();
      helpMsg.style.display = 'none';
    }
  }

  // ✅ Flatpickr con minDate ya calculado por reglas (cutoff/lead-days)
  if (window.flatpickr && dateInput) {
    flatpickr(dateInput, {
      dateFormat: 'Y-m-d',
      altInput: true,
      altFormat: 'd/m/Y',
      minDate: minBookable,
      disable: [ (date) => isDayFullyBlocked(isoFromDate(date)) ],
      onChange: (_sel, iso) => rebuildScheduleChoices(iso),
      onReady: (_sel, iso) => { if (!iso) { scheduleChoices.disable(); helpMsg.style.display = 'none'; } else { rebuildScheduleChoices(iso); } }
    });
  } else if (dateInput) {
    // Fallback nativo
    dateInput.type = 'date';
    dateInput.min  = minBookable;
    dateInput.addEventListener('change', e => rebuildScheduleChoices(e.target.value));
    scheduleChoices.disable();
  }

  // Other hotel
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

  // ======= AJAX Add to cart =======
  const addBtn = document.getElementById('addToCartBtn');
  if (!addBtn) return;

  let submitting = false;
  addBtn.addEventListener('click', async () => {
    if (submitting) return;
    submitting = true;

    const prevHTML = addBtn.innerHTML;
    addBtn.disabled = true;
    addBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>{{ __('adminlte::adminlte.add_to_cart') }}';

    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const fd = new FormData(formEl);

    try {
      const res = await fetch(formEl.action, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrf,
        },
        body: fd
      });

      let data = {};
      try { data = await res.json(); } catch (_) {}

      if (!res.ok) {
        const msg = (data && data.message) ? data.message : 'No se pudo agregar el tour. Intenta de nuevo.';
        await Swal.fire({ icon: 'error', title: 'Error', text: msg });
        return;
      }

      const okMsg = (data && data.message) ? data.message : 'Tour añadido al carrito.';
      await Swal.fire({ icon: 'success', title: 'Success', text: okMsg, confirmButtonColor: '#198754' });

      if (typeof data.count !== 'undefined' && window.setCartCount) {
        window.setCartCount(data.count);
      } else if (window.CART_COUNT_URL && window.setCartCount) {
        try {
          const cRes = await fetch(window.CART_COUNT_URL, { headers: { 'Accept': 'application/json' }});
          const cData = await cRes.json();
          window.setCartCount(cData.count || 0);
        } catch (_) {}
      }

      window.location.reload();

    } catch (err) {
      await Swal.fire({ icon: 'error', title: 'Error', text: 'Error de red. Intenta nuevamente.' });
    } finally {
      addBtn.disabled = false;
      addBtn.innerHTML = prevHTML;
      submitting = false;
    }
  });
})();
</script>
@endpush
