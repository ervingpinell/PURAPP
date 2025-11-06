{{-- ====== Date & Time + Pickup ====== --}}
@push('css')
<style>
  .gv-label-icon{display:flex;align-items:center;gap:.5rem;font-weight:700}
  .gv-label-icon i{color:#30363c;line-height:1}
  .gv-label-icon span{white-space:nowrap}
  .reservation-box .choices,
  .reservation-box .choices__inner,
  .reservation-box .choices__list--dropdown{width:100%}
  .pickup-section{transition:opacity .3s ease}
  .pickup-section.disabled{opacity:.5;pointer-events:none}
</style>
@endpush

<div class="row g-2">
  {{-- Fecha --}}
  <div class="col-12 col-sm-6">
    <label class="form-label gv-label-icon">
      <i class="fas fa-calendar-alt" aria-hidden="true"></i>
      <span>{{ __('adminlte::adminlte.select_date') }}</span>
    </label>
    <input id="tourDateInput" type="text" name="tour_date" class="form-control w-100" placeholder="dd/mm/yyyy" required>
  </div>

  {{-- Horario --}}
  <div class="col-12 col-sm-6">
    <label class="form-label gv-label-icon">
      <i class="fas fa-clock" aria-hidden="true"></i>
      <span>{{ __('adminlte::adminlte.select_time') }}</span>
    </label>
    <select name="schedule_id" class="form-select w-100" id="scheduleSelect" required>
      <option value="">-- {{ __('adminlte::adminlte.select_option') }} --</option>
      @foreach($tour->schedules->sortBy('start_time') as $schedule)
        <option value="{{ $schedule->schedule_id }}">
          {{ date('g:i A', strtotime($schedule->start_time)) }} - {{ date('g:i A', strtotime($schedule->end_time)) }}
        </option>
      @endforeach
    </select>
  </div>
</div>

<div id="noSlotsHelp" class="form-text text-danger mb-2" style="display:none;"></div>

{{-- Idioma --}}
<div class="section-title mt-3 d-flex align-items-center gap-2">
  <i class="fas fa-language" aria-hidden="true"></i>
  <span>{{ __('adminlte::adminlte.select_language') }}</span>
</div>
<label for="languageSelect" class="visually-hidden">{{ __('adminlte::adminlte.select_language') }}</label>
<select name="tour_language_id" class="form-select mb-2 w-100" id="languageSelect" required>
  <option value="">-- {{ __('adminlte::adminlte.select_option') }} --</option>
  @foreach($tour->languages as $lang)
    <option value="{{ $lang->tour_language_id }}">{{ $lang->name }}</option>
  @endforeach
</select>

{{-- Pickup: Hotel O Punto de encuentro (excluyentes) --}}
<div class="pickup-options mt-3">
  {{-- Hotel --}}
  <div class="pickup-section" id="hotelSection">
    <div class="section-title d-flex align-items-center gap-2">
      <i class="fas fa-hotel" aria-hidden="true"></i>
      <span>{{ __('adminlte::adminlte.select_hotel') ?? 'Hotel o punto de recogida' }}</span>
    </div>
    <label for="hotelSelect" class="visually-hidden">{{ __('adminlte::adminlte.select_hotel') }}</label>
    <select class="form-select mb-2 w-100" id="hotelSelect" name="hotel_id">
      <option value="">-- {{ __('adminlte::adminlte.select_option') }} --</option>
      @foreach($hotels as $hotel)
        <option value="{{ $hotel->hotel_id }}">{{ $hotel->name }}</option>
      @endforeach
      <option value="other">{{ __('adminlte::adminlte.hotel_other') }}</option>
    </select>

    {{-- Otro hotel --}}
    <div class="mb-2 d-none" id="otherHotelWrapper">
      <label for="otherHotelInput" class="form-label">{{ __('adminlte::adminlte.hotel_name') }}</label>
      <input type="text" class="form-control" name="other_hotel_name" id="otherHotelInput"
             placeholder="{{ __('adminlte::adminlte.hotel_name') }}">
      <div class="form-text text-danger mt-1" id="outsideAreaMessage" style="display:none;">
        {{ __('adminlte::adminlte.outside_area')
            ?: 'Has ingresado un hotel personalizado. Contáctanos para confirmar si podemos ofrecer transporte desde ese lugar.' }}
      </div>
    </div>
  </div>

  <div class="text-center my-2">
    <span class="badge bg-secondary">{{ __('adminlte::adminlte.or') ?? 'O' }}</span>
  </div>

  {{-- Meeting point --}}
  <div class="pickup-section" id="meetingPointSection">
    <div class="section-title d-flex align-items-center gap-2">
      <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
      <span>{{ __('adminlte::adminlte.meetingPoint') ?? 'Punto de encuentro' }}</span>
    </div>
    <label for="meetingPointSelect" class="visually-hidden">{{ __('adminlte::adminlte.meetingPoint') }}</label>
    <select class="form-select w-100" name="selected_meeting_point" id="meetingPointSelect">
      <option value="">-- {{ __('adminlte::adminlte.select_option') }} --</option>
      @foreach($meetingPoints as $mp)
        @php
          $mpName = method_exists($mp, 'getTranslated') ? $mp->getTranslated('name') : ($mp->name ?? '');
          $mpDesc = method_exists($mp, 'getTranslated') ? $mp->getTranslated('description') : ($mp->description ?? '');
        @endphp
        <option
          value="{{ $mp->id }}"
          data-desc="{{ e($mpDesc ?? '') }}"
          data-time="{{ $mp->pickup_time ?? '' }}"
          data-url="{{ $mp->map_url ?? $mp->url ?? '' }}"
        >
          {{ $mpName }}{{ $mp->pickup_time ? ' — '.$mp->pickup_time : '' }}
        </option>
      @endforeach
    </select>

    {{-- Info dinámica --}}
    <div id="meetingPointInfo" class="meeting-info card card-body bg-light border rounded small d-none mt-2">
      <div id="mpDesc" class="mp-desc mb-2"></div>
      <div id="mpTime" class="mp-time text-muted mb-2"></div>
      <a id="mpLink" class="btn btn-sm btn-outline-success d-none" href="#" target="_blank" rel="noopener">
        <i class="fas fa-map me-1"></i> {{ __('adminlte::adminlte.open_map') ?: 'Ver ubicación' }}
      </a>
    </div>
  </div>
</div>

<input type="hidden" name="is_other_hotel" id="isOtherHotel" value="0">

@once
  {{-- Flatpickr --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

  {{-- Choices.js --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
  <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
@endonce

@push('scripts')
<script>
(function(){
  if (window.__gvDateTimeInit) return;
  window.__gvDateTimeInit = true;

  /* ========= Datos del backend ========= */
  const RULES  = @json($rulesPayload ?? ['tour'=>['min'=>null],'schedules'=>[],'initialMin'=>null]);
  const blockedBySchedule = @json($blockedBySchedule ?? (object)[]); // <- FIX
  const fullByCapacity    = @json($capacityDisabled  ?? (object)[]); // <- FIX
  const blockedGeneral    = @json($blockedGeneral    ?? []);
  const fullyBlockedDates = @json($fullyBlockedDates ?? []);

  const T = {
    noSlots: @json(__('adminlte::adminlte.no_slots_for_date') ?: 'No hay horarios disponibles para esa fecha.')
  };

  /* ========= Elementos ========= */
  const dateInput      = document.getElementById('tourDateInput');
  const scheduleSelect = document.getElementById('scheduleSelect');
  const help           = document.getElementById('noSlotsHelp');

  const hotelSelect = document.getElementById('hotelSelect');
  const otherHotelWrapper = document.getElementById('otherHotelWrapper');
  const otherHotelInput   = document.getElementById('otherHotelInput');
  const isOtherHotelInput = document.getElementById('isOtherHotel');
  const outsideMessage    = document.getElementById('outsideAreaMessage');

  const meetingPointSelect  = document.getElementById('meetingPointSelect');
  const meetingPointSection = document.getElementById('meetingPointSection');
  const hotelSection        = document.getElementById('hotelSection');
  const meetingPointInfo    = document.getElementById('meetingPointInfo');
  const mpDesc = document.getElementById('mpDesc');
  const mpTime = document.getElementById('mpTime');
  const mpLink = document.getElementById('mpLink');

  if (!dateInput || !scheduleSelect) return;

  /* ========= Helpers ========= */
  const isoFromDate = (d) => `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;

  // Opciones base ANTES de instanciar Choices
  const BASE_CHOICES = Array.from(scheduleSelect.querySelectorAll('option'))
    .filter(o => o.value !== '')
    .map(o => ({ value:String(o.value), label:o.label }));

  const ruleForSchedule = (sid) => (RULES.schedules && RULES.schedules[String(sid)]) ? RULES.schedules[String(sid)] : (RULES.tour || {min:null});
  const globalMin = RULES.initialMin || (RULES.tour?.min) || 'today';
  const SCHEDULE_IDS = BASE_CHOICES.map(o => String(o.value));

  const canUseScheduleOnDate = (iso, sid) => {
    if (!iso) return false;
    if (fullyBlockedDates.includes(iso)) return false;
    if (blockedGeneral.includes(iso))    return false;
    if ((blockedBySchedule[sid] || []).includes(iso)) return false;
    if ((fullByCapacity[sid]    || []).includes(iso)) return false;
    const r = ruleForSchedule(sid);
    return !r.min || iso >= r.min;
  };

  const anyScheduleAvailable = (iso) => SCHEDULE_IDS.some(id => canUseScheduleOnDate(iso, id));
  const isDayFullyBlocked = (iso) => {
    if (!iso) return true;
    if (fullyBlockedDates.includes(iso)) return true;
    if (blockedGeneral.includes(iso))    return true;
    return !anyScheduleAvailable(iso);
  };

  /* ========= Choices ========= */
  const scheduleChoices = new Choices(scheduleSelect, { searchEnabled:false, shouldSort:false, itemSelectText:'', placeholder:false });
  const hotelChoices    = new Choices(hotelSelect,    { searchEnabled:true,  shouldSort:false, itemSelectText:'' });
  const meetingChoices  = new Choices(meetingPointSelect, { searchEnabled:true, shouldSort:false, itemSelectText:'', placeholder:true, placeholderValue:'-- {{ __('adminlte::adminlte.select_option') }} --' });
  window.meetingChoices = meetingChoices;

  function rebuildScheduleChoices(iso){
    const prevValue = scheduleChoices.getValue(true) || '';
    scheduleChoices.removeActiveItems();
    scheduleChoices.clearStore();
    scheduleChoices.clearChoices();

    if (!iso || isDayFullyBlocked(iso)) {
      scheduleChoices.setChoices([], 'value', 'label', true);
      scheduleChoices.disable();
      help.textContent = iso ? T.noSlots : '';
      help.style.display = iso ? '' : 'none';
      return;
    }

    const allowed = BASE_CHOICES.map(o => ({
      ...o,
      disabled: !canUseScheduleOnDate(iso, o.value)
    }));

    scheduleChoices.setChoices(allowed, 'value', 'label', true);

    const enabled = allowed.filter(c => !c.disabled);
    if (enabled.length){
      scheduleChoices.enable();
      help.style.display = 'none';
      if (prevValue && enabled.some(c => String(c.value)===String(prevValue))){
        scheduleChoices.setChoiceByValue(String(prevValue));
      } else if (enabled.length===1){
        scheduleChoices.setChoiceByValue(String(enabled[0].value));
        scheduleSelect.dispatchEvent(new Event('change',{bubbles:true}));
      }
    } else {
      scheduleChoices.disable();
      help.textContent = T.noSlots;
      help.style.display = '';
    }
  }

  /* ========= Flatpickr ========= */
  let fp;
  if (window.flatpickr){
    fp = flatpickr(dateInput, {
      altInput: true,
      altFormat: 'd/m/Y',
      dateFormat: 'Y-m-d',
      minDate: globalMin,
      disable: [ (date) => isDayFullyBlocked(isoFromDate(date)) ],
      onChange: (_sel, iso) => rebuildScheduleChoices(iso),
      onReady: (_sel, iso, instance) => {
        const start = iso || (globalMin === 'today' ? instance.formatDate(new Date(),'Y-m-d') : globalMin);
        instance.setDate(start, false);
        rebuildScheduleChoices(start);
      }
    });
  } else {
    dateInput.type = 'date';
    dateInput.min  = (globalMin==='today') ? new Date().toISOString().slice(0,10) : globalMin;
    dateInput.addEventListener('change', e=> rebuildScheduleChoices(e.target.value));
    scheduleChoices.disable();
  }

  // Cambio de horario
  scheduleSelect.addEventListener('change', ()=>{
    const sid  = scheduleSelect.value;
    const rule = sid ? ruleForSchedule(sid) : (RULES.tour || {min:null});
    if (fp){
      fp.set('minDate', sid ? (rule.min || globalMin) : globalMin);
      const currentIso = dateInput.value;
      if (currentIso && sid && !canUseScheduleOnDate(currentIso, sid)){
        fp.clear();
        help.textContent = T.noSlots;
        help.style.display = '';
      } else {
        help.style.display = 'none';
      }
    }
  });

  /* ========= Excluyente: Hotel vs Meeting ========= */
  function toggleOther(){
    const isOther = (hotelChoices.getValue(true) === 'other');
    otherHotelWrapper.classList.toggle('d-none', !isOther);
    if (isOtherHotelInput) isOtherHotelInput.value = isOther ? '1' : '0';
    outsideMessage && (outsideMessage.style.display = isOther ? 'block' : 'none');
    if (isOther) { otherHotelInput?.focus(); }
  }
  hotelSelect.addEventListener('change', toggleOther); toggleOther();

  function refreshMeetingInfo(){
    const val = meetingChoices.getValue(true);
    if (!val){ meetingPointInfo.classList.add('d-none'); return; }
    const opt = Array.from(meetingPointSelect.options).find(o => String(o.value)===String(val));
    if (!opt){ meetingPointInfo.classList.add('d-none'); return; }
    const desc = opt.dataset.desc || '';
    const time = opt.dataset.time || '';
    const url  = opt.dataset.url  || '';
    mpDesc.textContent = desc;  mpDesc.classList.toggle('d-none', !desc);
    mpTime.textContent = time ? `⏰ ${time}` : ''; mpTime.classList.toggle('d-none', !time);
    if (url){ mpLink.href=url; mpLink.classList.remove('d-none'); } else { mpLink.removeAttribute('href'); mpLink.classList.add('d-none'); }
    meetingPointInfo.classList.remove('d-none');
  }
  meetingPointSelect.addEventListener('change', refreshMeetingInfo); refreshMeetingInfo();

  // Mutua exclusión
  function validateHotelMeetingPoint(){
    const hotelValue = hotelChoices.getValue(true);
    const meetingVal = meetingChoices.getValue(true);

    if (hotelValue){
      meetingChoices.disable(); meetingPointSelect.value=''; meetingChoices.removeActiveItems(); refreshMeetingInfo();
      meetingPointSection.classList.add('disabled');
    } else {
      meetingChoices.enable(); meetingPointSection.classList.remove('disabled');
    }

    if (meetingVal){
      hotelChoices.disable(); hotelSelect.value=''; hotelChoices.removeActiveItems(); toggleOther(); if (isOtherHotelInput) isOtherHotelInput.value='0';
      hotelSection.classList.add('disabled');
    } else {
      hotelChoices.enable(); hotelSection.classList.remove('disabled');
    }
  }
  hotelSelect.addEventListener('change', validateHotelMeetingPoint);
  meetingPointSelect.addEventListener('change', validateHotelMeetingPoint);
  validateHotelMeetingPoint();
})();
</script>
@endpush
