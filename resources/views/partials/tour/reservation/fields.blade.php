{{-- ====== Date & Time + Pickup (Simplificado) ====== --}}
@push('css')
<style>
  .gv-label-icon {
    display: flex;
    align-items: center;
    gap: .5rem;
    font-weight: 700
  }

  .gv-label-icon i {
    color: #30363c;
    line-height: 1
  }

  .gv-label-icon span {
    white-space: nowrap
  }

  .reservation-box .choices,
  .reservation-box .choices__inner,
  .reservation-box .choices__list--dropdown {
    width: 100%
  }

  .pickup-section {
    transition: opacity .3s ease
  }

  .pickup-section.disabled {
    opacity: .5;
    pointer-events: none
  }

  .field-hint {
    font-size: .85rem
  }

  .error-message {
    display: none;
    font-size: .875rem;
    margin-top: .25rem
  }
</style>
@endpush

@php
$tr = function(string $key, string $fallback) {
    $t = __($key);
    return ($t === $key) ? $fallback : $t;
};

$oldDate = old('tour_date');
$oldSchedule = old('schedule_id');
$oldLanguage = old('tour_language_id');
$oldHotel = old('hotel_id');
$oldOtherHotel = old('other_hotel_name');
$oldIsOther = old('is_other_hotel', 0);
$oldMeeting = old('meeting_point_id');
$isSelected = fn($v,$o) => (string)$v===(string)$o ? 'selected' : '';

$maxFutureDays = (int) setting('booking.max_future_days', config('booking.max_days_advance', 730));
@endphp

<div class="row g-2">
  {{-- Fecha --}}
  <div class="col-12 col-sm-6">
    <label class="form-label gv-label-icon">
      <i class="fas fa-calendar-alt" aria-hidden="true"></i>
      <span>{{ $tr('adminlte::adminlte.select_date','Selecciona una fecha') }}</span>
    </label>
    <input
      id="tourDateInput"
      type="text"
      name="tour_date"
      class="form-control w-100 @error('tour_date') is-invalid @enderror"
      placeholder="dd/mm/yyyy"
      value="{{ $oldDate }}"
      required>
    @error('tour_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  {{-- Horario --}}
  <div class="col-12 col-sm-6">
    <label class="form-label gv-label-icon">
      <i class="fas fa-clock" aria-hidden="true"></i>
      <span>{{ $tr('adminlte::adminlte.select_time','Selecciona una hora') }}</span>
    </label>
    <select
      name="schedule_id"
      class="form-select w-100 @error('schedule_id') is-invalid @enderror"
      id="scheduleSelect"
      required>
      <option value="">-- {{ $tr('adminlte::adminlte.select_option','Selecciona una opción') }} --</option>
      @foreach($tour->schedules->sortBy('start_time') as $schedule)
      <option value="{{ $schedule->schedule_id }}" {{ $isSelected($schedule->schedule_id,$oldSchedule) }}>
        {{ date('g:i A', strtotime($schedule->start_time)) }} - {{ date('g:i A', strtotime($schedule->end_time)) }}
      </option>
      @endforeach
    </select>
    @error('schedule_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
    <div id="noSlotsHelp" class="form-text text-danger field-hint" style="display:none;"></div>
  </div>
</div>

{{-- Idioma --}}
<div class="section-title mt-3 d-flex align-items-center gap-2">
  <i class="fas fa-language" aria-hidden="true"></i>
  <span>{{ $tr('adminlte::adminlte.select_language','Selecciona un idioma') }}</span>
</div>
<label for="languageSelect" class="visually-hidden">{{ $tr('adminlte::adminlte.select_language','Selecciona un idioma') }}</label>
<select
  name="tour_language_id"
  class="form-select mb-1 w-100 @error('tour_language_id') is-invalid @enderror"
  id="languageSelect"
  required>
  <option value="">-- {{ $tr('adminlte::adminlte.select_option','Selecciona una opción') }} --</option>
  @foreach($tour->languages as $lang)
  <option value="{{ $lang->tour_language_id }}" {{ $isSelected($lang->tour_language_id,$oldLanguage) }}>
    {{ $lang->name }}
  </option>
  @endforeach
</select>
<div id="langHelp" class="error-message text-danger"></div>
@error('tour_language_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

{{-- Pickup: Hotel O Punto de encuentro (excluyentes) --}}
<div class="pickup-options mt-3">
  {{-- Hotel --}}
  <div class="pickup-section" id="hotelSection">
    <div class="section-title d-flex align-items-center gap-2">
      <i class="fas fa-hotel" aria-hidden="true"></i>
      <span>{{ $tr('adminlte::adminlte.select_hotel','Hotel') }}</span>
    </div>
    <label for="hotelSelect" class="visually-hidden">{{ $tr('adminlte::adminlte.select_hotel','Hotel') }}</label>
    <select
      class="form-select mb-1 w-100 @error('hotel_id') is-invalid @enderror"
      id="hotelSelect"
      name="hotel_id">
      <option value="">-- {{ $tr('adminlte::adminlte.select_option','Selecciona una opción') }} --</option>
      @foreach($hotels as $hotel)
      <option value="{{ $hotel->hotel_id }}" {{ $isSelected($hotel->hotel_id,$oldHotel) }}>
        {{ $hotel->name }}
      </option>
      @endforeach
      <option value="other" {{ $oldIsOther ? 'selected' : '' }}>{{ $tr('adminlte::adminlte.hotel_other','Otro hotel') }}</option>
    </select>
    <div id="hotelHelp" class="error-message text-danger"></div>

    {{-- Otro hotel --}}
    <div class="mb-2 {{ $oldIsOther ? '' : 'd-none' }}" id="otherHotelWrapper">
      <label for="otherHotelInput" class="form-label">{{ $tr('adminlte::adminlte.hotel_name','Nombre del hotel') }}</label>
      <input
        type="text"
        class="form-control @error('other_hotel_name') is-invalid @enderror"
        name="other_hotel_name"
        id="otherHotelInput"
        value="{{ $oldOtherHotel }}"
        placeholder="{{ $tr('adminlte::adminlte.hotel_name','Nombre del hotel') }}">
      @error('other_hotel_name') <div class="invalid-feedback">{{ $message }}</div> @enderror

      <div class="form-text text-danger mt-1 field-hint" id="outsideAreaMessage" style="display: {{ $oldIsOther ? 'block' : 'none' }};">
        {{ $tr('adminlte::adminlte.outside_area','Has ingresado un hotel personalizado. Contáctanos para confirmar si podemos ofrecer transporte desde ese lugar.') }}
      </div>
    </div>
  </div>

  <div class="text-center my-2">
    <span class="badge bg-secondary">{{ $tr('adminlte::adminlte.or','O') }}</span>
  </div>

  {{-- Meeting point --}}
  <div class="pickup-section" id="meetingPointSection">
    <div class="section-title d-flex align-items-center gap-2">
      <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
      <span>{{ $tr('adminlte::adminlte.meetingPoint','Punto de encuentro') }}</span>
    </div>
    <label for="meetingPointSelect" class="visually-hidden">{{ $tr('adminlte::adminlte.meetingPoint','Punto de encuentro') }}</label>
    <select
      class="form-select w-100 @error('meeting_point_id') is-invalid @enderror"
      name="meeting_point_id"
      id="meetingPointSelect">
      <option value="">-- {{ $tr('adminlte::adminlte.select_option','Selecciona una opción') }} --</option>
      @foreach($meetingPoints as $mp)
      @php
      $mpName = method_exists($mp,'getTranslated') ? $mp->getTranslated('name') : ($mp->name ?? '');
      $mpDesc = method_exists($mp,'getTranslated') ? $mp->getTranslated('description') : ($mp->description ?? '');
      @endphp
      <option
        value="{{ $mp->id }}"
        data-desc="{{ e($mpDesc ?? '') }}"
        data-time="{{ $mp->pickup_time ?? '' }}"
        data-url="{{ $mp->map_url ?? $mp->url ?? '' }}"
        {{ $isSelected($mp->id,$oldMeeting) }}>
        {{ $mpName }}{{ $mp->pickup_time ? ' — '.$mp->pickup_time : '' }}
      </option>
      @endforeach
    </select>
    <div id="mpHelp" class="error-message text-danger"></div>
    @error('meeting_point_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

    {{-- Info dinámica --}}
    <div id="meetingPointInfo" class="meeting-info card card-body bg-light border rounded small d-none mt-2">
      <div id="mpDesc" class="mp-desc mb-2"></div>
      <div id="mpTime" class="mp-time text-muted mb-2"></div>
      <a id="mpLink" class="btn btn-sm btn-outline-success d-none" href="#" target="_blank" rel="noopener">
        <i class="fas fa-map me-1"></i> {{ $tr('adminlte::adminlte.open_map','Ver ubicación') }}
      </a>
    </div>
  </div>
</div>

<input type="hidden" name="is_other_hotel" id="isOtherHotel" value="{{ $oldIsOther ? '1' : '0' }}">

@once
{{-- Flatpickr --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/airbnb.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

{{-- Choices.js --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endonce

@push('scripts')
<script>
(function() {
    if (window.__gvDateTimeInit) return;
    window.__gvDateTimeInit = true;

    /* ========= Datos del backend ========= */
    const RULES = @json($rulesPayload ?? ['tour' => ['min' => null], 'schedules' => [], 'initialMin' => null]);
    const blockedBySchedule = @json($blockedBySchedule ?? (object)[]);
    const fullByCapacity = @json($capacityDisabled ?? (object)[]);
    const blockedGeneral = @json($blockedGeneral ?? []);
    const fullyBlockedDates = @json($fullyBlockedDates ?? []);
    const maxFutureDays = {{ $maxFutureDays }};

    // ===== FECHAS CON PRECIOS =====
    const categoriesData = @json($categoriesData ?? []);

    // Construir set de fechas que tienen AL MENOS una categoría con precio
    const datesWithPrices = new Set();
    categoriesData.forEach(cat => {
        if (!cat.rules || !Array.isArray(cat.rules)) return;

        cat.rules.forEach(rule => {
            if (rule.is_default) {
                // Default = todas las fechas futuras
                const today = new Date();
                const maxDate = new Date();
                maxDate.setDate(maxDate.getDate() + maxFutureDays);

                for (let d = new Date(today); d <= maxDate; d.setDate(d.getDate() + 1)) {
                    const iso = d.toISOString().split('T')[0];
                    datesWithPrices.add(iso);
                }
            } else if (rule.valid_from && rule.valid_until) {
                // Rango específico
                const start = new Date(rule.valid_from);
                const end = new Date(rule.valid_until);

                for (let d = new Date(start); d <= end; d.setDate(d.getDate() + 1)) {
                    const iso = d.toISOString().split('T')[0];
                    datesWithPrices.add(iso);
                }
            } else if (rule.valid_from) {
                // Solo from (hasta maxFutureDays)
                const start = new Date(rule.valid_from);
                const maxDate = new Date();
                maxDate.setDate(maxDate.getDate() + maxFutureDays);

                for (let d = new Date(start); d <= maxDate; d.setDate(d.getDate() + 1)) {
                    const iso = d.toISOString().split('T')[0];
                    datesWithPrices.add(iso);
                }
            } else if (rule.valid_until) {
                // Solo until (desde hoy)
                const today = new Date();
                const end = new Date(rule.valid_until);

                for (let d = new Date(today); d <= end; d.setDate(d.getDate() + 1)) {
                    const iso = d.toISOString().split('T')[0];
                    datesWithPrices.add(iso);
                }
            }
        });
    });

    // Calcular fecha máxima
    const maxDateObj = new Date();
    maxDateObj.setDate(maxDateObj.getDate() + maxFutureDays);

    const OLD = {
        date: @json($oldDate),
        schedule: @json($oldSchedule),
        language: @json($oldLanguage),
        hotel: @json($oldHotel),
        otherHotel: @json($oldOtherHotel),
        isOther: @json((bool) $oldIsOther),
        meeting: @json($oldMeeting),
    };

    const T = {
        noSlotsTitle: @json($tr('adminlte::adminlte.no_slots_title', 'Sin horarios')),
        noSlots: @json($tr('adminlte::adminlte.no_slots_for_date', 'No hay horarios disponibles para esa fecha.')),
        needScheduleTitle: @json($tr('adminlte::adminlte.need_schedule_title', 'Horario obligatorio')),
        needScheduleText: @json($tr('adminlte::adminlte.need_schedule', 'Por favor, selecciona una hora.')),
        needLangTitle: @json($tr('adminlte::adminlte.need_language_title', 'Idioma obligatorio')),
        needLangText: @json($tr('adminlte::adminlte.need_language', 'Por favor, selecciona un idioma.')),
        needPickTitle: @json($tr('adminlte::adminlte.need_pickup_title', 'Recogida obligatoria')),
        needPickText: @json($tr('adminlte::adminlte.need_pickup', 'Debes seleccionar un hotel o un punto de encuentro para continuar.')),
        placeholder: @json('-- '.$tr('adminlte::adminlte.select_option', 'Selecciona una opción').' --'),
        ok: @json($tr('adminlte::adminlte.ok', 'OK')),
    };

    /* ========= Elementos ========= */
    const dateInput = document.getElementById('tourDateInput');
    const scheduleSelect = document.getElementById('scheduleSelect');
    const help = document.getElementById('noSlotsHelp');

    const hotelSelect = document.getElementById('hotelSelect');
    const otherHotelWrapper = document.getElementById('otherHotelWrapper');
    const otherHotelInput = document.getElementById('otherHotelInput');
    const isOtherHotelInput = document.getElementById('isOtherHotel');
    const languageSelect = document.getElementById('languageSelect');
    const meetingPointSelect = document.getElementById('meetingPointSelect');

    const meetingPointSection = document.getElementById('meetingPointSection');
    const hotelSection = document.getElementById('hotelSection');
    const meetingPointInfo = document.getElementById('meetingPointInfo');
    const mpDesc = document.getElementById('mpDesc');
    const mpTime = document.getElementById('mpTime');
    const mpLink = document.getElementById('mpLink');

    if (!dateInput || !scheduleSelect) return;

    // Forzar input readonly
    dateInput.setAttribute('readonly', 'readonly');
    dateInput.style.backgroundColor = '#fff';
    dateInput.style.cursor = 'pointer';

    /* ========= Helpers ========= */
    const isoFromDate = (d) => `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
    const textOf = (opt) => (opt.textContent || '').trim();
    const warn = (title, text, focusEl) => Swal.fire({
        icon: 'warning',
        title,
        text,
        confirmButtonText: T.ok
    }).then(() => {
        if (focusEl) {
            focusEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
            setTimeout(() => focusEl.focus(), 120);
        }
    });

    const BASE_CHOICES = Array.from(scheduleSelect.querySelectorAll('option'))
        .filter(o => o.value !== '')
        .map(o => ({
            value: String(o.value),
            label: textOf(o)
        }));

    const ruleForSchedule = (sid) => (RULES.schedules && RULES.schedules[String(sid)]) ? RULES.schedules[String(sid)] : (RULES.tour || { min: null });
    const globalMin = RULES.initialMin || (RULES.tour?.min) || 'today';
    const SCHEDULE_IDS = BASE_CHOICES.map(o => String(o.value));

    const canUseScheduleOnDate = (iso, sid) => {
        if (!iso) return false;
        if (fullyBlockedDates.includes(iso)) return false;
        if (blockedGeneral.includes(iso)) return false;
        if ((blockedBySchedule[sid] || []).includes(iso)) return false;
        if ((fullByCapacity[sid] || []).includes(iso)) return false;
        const r = ruleForSchedule(sid);
        return !r.min || iso >= r.min;
    };

    const anyScheduleAvailable = (iso) => SCHEDULE_IDS.some(id => canUseScheduleOnDate(iso, id));

    const isDayFullyBlocked = (iso) => {
        if (!iso) return true;
        if (fullyBlockedDates.includes(iso)) return true;
        if (blockedGeneral.includes(iso)) return true;
        if (!anyScheduleAvailable(iso)) return true;

        // ===== NUEVA LÓGICA: Bloquear si no hay precios =====
        if (!datesWithPrices.has(iso)) return true;

        return false;
    };

    /* ========= Choices ========= */
    const scheduleChoices = new Choices(scheduleSelect, {
        searchEnabled: false,
        shouldSort: false,
        itemSelectText: '',
        placeholder: true,
        placeholderValue: T.placeholder
    });
    const hotelChoices = new Choices(hotelSelect, {
        searchEnabled: true,
        shouldSort: false,
        itemSelectText: '',
        placeholder: true,
        placeholderValue: T.placeholder
    });
    const meetingChoices = new Choices(meetingPointSelect, {
        searchEnabled: true,
        shouldSort: false,
        itemSelectText: '',
        placeholder: true,
        placeholderValue: T.placeholder
    });
    const languageChoices = new Choices(languageSelect, {
        searchEnabled: true,
        shouldSort: false,
        itemSelectText: '',
        placeholder: true,
        placeholderValue: T.placeholder
    });

    function forceSchedulePlaceholder() {
        scheduleChoices.removeActiveItems();
        scheduleSelect.value = '';
        scheduleChoices.setChoiceByValue('');
    }

    function rebuildScheduleChoices(iso) {
        scheduleChoices.removeActiveItems();
        scheduleChoices.clearStore();
        scheduleChoices.clearChoices();

        if (!iso || isDayFullyBlocked(iso)) {
            scheduleChoices.setChoices([], 'value', 'label', true);
            scheduleChoices.disable();
            help.textContent = iso ? T.noSlots : '';
            help.style.display = iso ? '' : 'none';
            if (iso) warn(T.noSlotsTitle, T.noSlots, dateInput);
            return;
        }

        const allowed = BASE_CHOICES.map(o => ({
            ...o,
            disabled: !canUseScheduleOnDate(iso, o.value)
        }));

        scheduleChoices.setChoices(allowed, 'value', 'label', true);

        const enabled = allowed.filter(c => !c.disabled);
        if (enabled.length) {
            scheduleChoices.enable();
            help.style.display = 'none';

            if (OLD.schedule && enabled.some(c => String(c.value) === String(OLD.schedule))) {
                scheduleChoices.setChoiceByValue(String(OLD.schedule));
                scheduleSelect.dispatchEvent(new Event('change', { bubbles: true }));
            } else {
                forceSchedulePlaceholder();
            }
        } else {
            scheduleChoices.disable();
            help.textContent = T.noSlots;
            help.style.display = '';
            warn(T.noSlotsTitle, T.noSlots, dateInput);
        }
    }

    /* ========= Flatpickr ========= */
    let fp;
    if (typeof flatpickr !== 'undefined') {
        fp = flatpickr(dateInput, {
            altInput: true,
            altFormat: 'd/m/Y',
            dateFormat: 'Y-m-d',
            defaultDate: OLD.date || null,
            minDate: globalMin,
            maxDate: maxDateObj,
            disable: [(date) => isDayFullyBlocked(isoFromDate(date))],
            locale: {
                firstDayOfWeek: 1
            },
            onChange: (_sel, iso) => {
                rebuildScheduleChoices(iso);
                forceSchedulePlaceholder();
            },
            onReady: (_sel, iso, instance) => {
                if (OLD.date) {
                    instance.setDate(OLD.date, false);
                    rebuildScheduleChoices(OLD.date);
                    if (OLD.schedule) {
                        scheduleChoices.setChoiceByValue(String(OLD.schedule));
                    }
                } else {
                    rebuildScheduleChoices(null);
                }
            }
        });
    } else {
        console.error('Flatpickr not loaded');
        dateInput.removeAttribute('readonly');
        dateInput.type = 'date';
        dateInput.min = (globalMin === 'today') ? new Date().toISOString().slice(0, 10) : globalMin;
        dateInput.max = maxDateObj.toISOString().slice(0, 10);
        if (OLD.date) dateInput.value = OLD.date;
        dateInput.addEventListener('change', e => {
            rebuildScheduleChoices(e.target.value);
            forceSchedulePlaceholder();
        });
        scheduleChoices.disable();
    }

    scheduleSelect.addEventListener('change', () => {
        const sid = scheduleSelect.value;
        const rule = sid ? ruleForSchedule(sid) : (RULES.tour || { min: null });
        if (fp) {
            fp.set('minDate', sid ? (rule.min || globalMin) : globalMin);
            const currentIso = dateInput.value;
            if (currentIso && sid && !canUseScheduleOnDate(currentIso, sid)) {
                fp.clear();
                help.textContent = T.noSlots;
                help.style.display = '';
                forceSchedulePlaceholder();
                warn(T.noSlotsTitle, T.noSlots, dateInput);
            } else {
                help.style.display = 'none';
            }
        }
    });

    /* ========= Excluyente: Hotel vs Meeting ========= */
    function toggleOther() {
        const isOther = (hotelChoices.getValue(true) === 'other') || !!OLD.isOther;
        otherHotelWrapper.classList.toggle('d-none', !isOther);
        if (isOtherHotelInput) isOtherHotelInput.value = isOther ? '1' : '0';
        if (isOther && !otherHotelInput.value && OLD.otherHotel) {
            otherHotelInput.value = OLD.otherHotel;
        }
        if (isOther) otherHotelInput?.focus();
    }

    function refreshMeetingInfo() {
        const val = meetingChoices.getValue(true);
        if (!val) {
            meetingPointInfo.classList.add('d-none');
            return;
        }
        const opt = Array.from(meetingPointSelect.options).find(o => String(o.value) === String(val));
        if (!opt) {
            meetingPointInfo.classList.add('d-none');
            return;
        }
        const desc = opt.dataset.desc || '';
        const theTime = opt.dataset.time || '';
        const url = opt.dataset.url || '';
        mpDesc.textContent = desc;
        mpDesc.classList.toggle('d-none', !desc);
        mpTime.textContent = theTime ? `⏰ ${theTime}` : '';
        mpTime.classList.toggle('d-none', !theTime);
        if (url) {
            mpLink.href = url;
            mpLink.classList.remove('d-none');
        } else {
            mpLink.removeAttribute('href');
            mpLink.classList.add('d-none');
        }
        meetingPointInfo.classList.remove('d-none');
    }

    function validatePickupUI() {
        const hotelValue = hotelChoices.getValue(true);
        const meetingVal = meetingChoices.getValue(true);

        if (hotelValue) {
            meetingChoices.disable();
            meetingPointSelect.value = '';
            meetingChoices.removeActiveItems();
            refreshMeetingInfo();
            meetingPointSection.classList.add('disabled');
        } else {
            meetingChoices.enable();
            meetingPointSection.classList.remove('disabled');
        }

        if (meetingVal) {
            hotelChoices.disable();
            hotelSelect.value = '';
            hotelChoices.removeActiveItems();
            toggleOther();
            if (isOtherHotelInput) isOtherHotelInput.value = '0';
            hotelSection.classList.add('disabled');
        } else {
            hotelChoices.enable();
            hotelSection.classList.remove('disabled');
        }
    }

    // Restaurar selecciones previas
    if (OLD.language) languageChoices.setChoiceByValue(String(OLD.language));
    if (OLD.hotel) hotelChoices.setChoiceByValue(String(OLD.hotel));
    else if (OLD.isOther) hotelChoices.setChoiceByValue('other');
    toggleOther();
    if (OLD.meeting) meetingChoices.setChoiceByValue(String(OLD.meeting));
    refreshMeetingInfo();
    validatePickupUI();

    hotelSelect.addEventListener('change', () => {
        toggleOther();
        validatePickupUI();
    });
    meetingPointSelect.addEventListener('change', () => {
        refreshMeetingInfo();
        validatePickupUI();
    });

    /* ========= Validación con SweetAlert2 ========= */
    async function validateForm(e) {
        if (!scheduleSelect.value) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            await warn(T.needScheduleTitle, T.needScheduleText, scheduleSelect);
            return false;
        }

        if (!languageSelect.value) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            await warn(T.needLangTitle, T.needLangText, languageSelect);
            return false;
        }

        const hotelVal = hotelSelect.value;
        const meetingVal = meetingPointSelect.value;
        const isOther = (hotelVal === 'other');
        const otherTxt = (otherHotelInput?.value || '').trim();
        const hasHotel = (hotelVal && hotelVal !== '' && hotelVal !== 'other') || (isOther && otherTxt !== '');
        const hasMeeting = !!meetingVal;

        if (!hasHotel && !hasMeeting) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            await warn(T.needPickTitle, T.needPickText, hotelSelect);
            return false;
        }
        return true;
    }

    document.addEventListener('click', function(e) {
        const target = e.target.closest('[data-role="add-to-cart"], [name="add_to_cart"], #add-to-cart-btn, button[type="submit"]');
        if (!target) return;
        const form = target.closest('form');
        if (!form || !form.contains(scheduleSelect)) return;
        validateForm(e);
    }, { capture: true });

    const form = document.getElementById('languageSelect')?.closest('form');
    if (form) {
        form.addEventListener('submit', (e) => {
            if (!validateForm(e)) return false;
        }, { capture: true });
    }
})();
</script>
@endpush
