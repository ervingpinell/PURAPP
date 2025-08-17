@php
  $tz = config('app.timezone', 'America/Costa_Rica');
  $today = \Carbon\Carbon::today($tz)->toDateString();
@endphp

<form action="{{ route('carrito.agregar', $tour->tour_id) }}" method="POST"
  class="reservation-box gv-ui p-3 shadow-sm rounded bg-white mb-4 border"
  data-adult-price="{{ $tour->adult_price }}"
  data-kid-price="{{ $tour->kid_price }}">

  @csrf
  <input type="hidden" name="tour_id" value="{{ $tour->tour_id }}">

  {{-- Título y precio --}}
  <h3 class="fw-bold fs-5 mb-2">{{ __('adminlte::adminlte.price') }}</h3>
  <div class="price-breakdown mb-3">
    <span class="fw-bold">{{ __('adminlte::adminlte.adult') }}:</span>
    <span class="price-adult fw-bold text-danger">${{ number_format($tour->adult_price, 2) }}</span> |
    <span class="fw-bold">{{ __('adminlte::adminlte.kid') }}:</span>
    <span class="price-kid fw-bold text-danger">${{ number_format($tour->kid_price, 2) }}</span>
  </div>

  {{-- Viajeros --}}
  <div class="mb-2">
    <button type="button"
      class="btn traveler-button w-100 d-flex align-items-center justify-content-between"
      data-bs-toggle="modal" data-bs-target="#travelerModal">
      <span><i class="fas fa-user me-2"></i> <span id="traveler-summary">2</span></span>
      <i class="fas fa-chevron-down"></i>
    </button>
  </div>

  {{-- Total dinámico --}}
  <p class="fw-bold mb-3 gv-total">
    {{ __('adminlte::adminlte.total') }}:
    <span id="reservation-total-price" class="text-danger">$0.00</span>
  </p>

  {{-- Fecha --}}
  <label class="form-label">{{ __('adminlte::adminlte.select_date') }}</label>
  <input
    id="tourDateInput"
    type="text"
    name="tour_date"
    class="form-control mb-1"
    placeholder="dd/mm/yyyy"
    required
  >

  {{-- Horario --}}
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

  {{-- Idioma --}}
  <label class="form-label">{{ __('adminlte::adminlte.select_language') }}</label>
  <select name="tour_language_id" class="form-select mb-3" required>
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

  <div class="mb-3 d-none" id="otherHotelWrapper">
    <label for="otherHotelInput" class="form-label">{{ __('adminlte::adminlte.hotel_name') }}</label>
    <input type="text" class="form-control" name="other_hotel_name" id="otherHotelInput"
           placeholder="{{ __('adminlte::adminlte.hotel_name') }}">
    <div class="form-text text-danger mt-1" id="outsideAreaMessage" style="display:none;">
      {{ __('adminlte::adminlte.outside_area') }}
    </div>
  </div>

  {{-- Hidden fields --}}
  <input type="hidden" name="is_other_hotel" id="isOtherHotel" value="0">
  <input type="hidden" name="adults_quantity" id="adults_quantity" value="2" required>
  <input type="hidden" name="kids_quantity" id="kids_quantity" value="0">
  <input type="hidden" name="selected_pickup_point" id="selectedPickupPoint">
  <input type="hidden" name="selected_meeting_point" id="selectedMeetingPoint">

  {{-- CTA --}}
  @auth
    <button type="submit" class="btn btn-success gv-cta w-100">
      <i class="fas fa-cart-plus me-2"></i> {{ __('adminlte::adminlte.add_to_cart') }}
    </button>
  @else
    <a href="{{ route('login') }}" class="btn btn-success gv-cta w-100"
       onclick="return askLoginWithSwal(event, this.href);">
      <i class="fas fa-cart-plus me-2"></i> {{ __('adminlte::adminlte.add_to_cart') }}
    </a>
  @endauth
</form>

{{-- ======= Estilos unificados + Choices dropdown ======= --}}
<style>
.gv-ui .form-control,
.gv-ui .form-select,
.gv-ui .choices__inner {
  background-color: #fff !important;
}
</style>

{{-- ======= Librerías (una sola vez) ======= --}}
@once
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
  <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
@endonce

{{-- ======= Comportamiento: bloqueos + dropdowns grandes ======= --}}
@push('scripts')
<script>
(function(){
  // ==== Datos del padre (ya definidos en la vista principal) ====
  const fullyBlockedDates = Array.isArray(window.fullyBlockedDates) ? window.fullyBlockedDates : [];
  const blockedGeneral    = Array.isArray(window.blockedGeneral) ? window.blockedGeneral : [];
  const blockedBySchedule = (window.blockedBySchedule && typeof window.blockedBySchedule === 'object') ? window.blockedBySchedule : {};

  const dateInput   = document.getElementById('tourDateInput');
  const scheduleSel = document.getElementById('scheduleSelect');
  const helpMsg     = document.getElementById('noSlotsHelp');
  const todayIso    = @json($today);

  // ===== Choices instances =====
  const scheduleChoices = new Choices(scheduleSel, {
    searchEnabled: false,
    shouldSort: false,
    itemSelectText: '',
    placeholder: true,
    placeholderValue: '-- {{ __('adminlte::adminlte.select_option') }} --'
  });

  const langSelect   = document.querySelector('select[name="tour_language_id"]');
  const hotelSelect  = document.getElementById('hotelSelect');

  const langChoices  = new Choices(langSelect,  { searchEnabled:false, shouldSort:false, itemSelectText:'' });
  const hotelChoices = new Choices(hotelSelect, { searchEnabled:true,  shouldSort:false, itemSelectText:'' });

  // Lista base de horarios para reconstrucciones
  const BASE_CHOICES = scheduleChoices._store.choices
    .filter(c => c.value !== '')
    .map(c => ({ value: String(c.value), label: c.label }));
  const SCHEDULE_IDS = BASE_CHOICES.map(o => o.value);

  // Helpers
  const isoFromDate = (d) => {
    const yyyy = d.getFullYear();
    const mm = String(d.getMonth()+1).padStart(2,'0');
    const dd = String(d.getDate()).padStart(2,'0');
    return `${yyyy}-${mm}-${dd}`;
  };
  const isDayFullyBlocked = (iso) => {
    if (!iso) return false;
    if (fullyBlockedDates.includes(iso)) return true;
    if (blockedGeneral.includes(iso)) return true;
    if (!SCHEDULE_IDS.length) return false;
    return SCHEDULE_IDS.every(id => (blockedBySchedule[id] || []).includes(iso));
  };
  const isBlockedForSchedule = (iso, sid) =>
    blockedGeneral.includes(iso) || (blockedBySchedule[sid] || []).includes(iso);

  function rebuildScheduleChoices(iso){
    const ph = [{ value:'', label:'-- {{ __('adminlte::adminlte.select_option') }} --', disabled:true, selected:true }];

    if (!iso || isDayFullyBlocked(iso)) {
      scheduleChoices.clearStore();
      scheduleChoices.setChoices(ph, 'value', 'label', true);
      scheduleChoices.disable();
      helpMsg.textContent = iso
        ? @json(__('adminlte::adminlte.no_times_for_day') ?? 'No hay horarios disponibles para esa fecha.')
        : '';
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

  // ==== Flatpickr: bloquea fechas sin horarios ====
  if (window.flatpickr && dateInput) {
    flatpickr(dateInput, {
      dateFormat: 'Y-m-d',  // valor para backend
      altInput: true,
      altFormat: 'd/m/Y',   // visual para usuario
      minDate: todayIso,
      disable: [ (date) => isDayFullyBlocked(isoFromDate(date)) ],
      onChange: (_sel, iso) => rebuildScheduleChoices(iso),
      onReady: (_sel, iso) => {
        if (!iso) {
          scheduleChoices.disable();
          helpMsg.style.display = 'none';
        } else {
          rebuildScheduleChoices(iso);
        }
      }
    });
  } else {
    // Fallback nativo si no carga flatpickr
    dateInput.type = 'date';
    dateInput.min  = todayIso;
    dateInput.addEventListener('change', e => rebuildScheduleChoices(e.target.value));
    scheduleChoices.disable();
  }

  // “Otro hotel” con Choices
  const otherWrap = document.getElementById('otherHotelWrapper');
  const isOtherH  = document.getElementById('isOtherHotel');
  const otherInp  = document.getElementById('otherHotelInput');
  const toggleOther = () => {
    const isOther = (hotelChoices.getValue(true) === 'other');
    otherWrap.classList.toggle('d-none', !isOther);
    if (isOtherH) isOtherH.value = isOther ? 1 : 0;
    if (!isOther && otherInp) otherInp.value = '';
  };
  hotelSelect.addEventListener('change', toggleOther);
  toggleOther();
})();
</script>
@endpush
