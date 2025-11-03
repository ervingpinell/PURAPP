{{-- ===== Date & Time ===== --}}
@push('css')
<style>
  .gv-label-icon {
    display: flex;
    align-items: center;
    gap: .5rem;
    font-weight: 700;
  }
  .gv-label-icon i { color: #30363c; line-height: 1; }
  .gv-label-icon span { white-space: nowrap; }

  .reservation-box .choices,
  .reservation-box .choices__inner,
  .reservation-box .choices__list--dropdown {
    width: 100%;
  }

  .pickup-section {
    transition: opacity 0.3s ease;
  }

  .pickup-section.disabled {
    opacity: 0.5;
    pointer-events: none;
  }
</style>
@endpush

<div class="row g-2">
  {{-- Date --}}
  <div class="col-12 col-sm-6">
    <label class="form-label gv-label-icon">
      <i class="fas fa-calendar-alt" aria-hidden="true"></i>
      <span>{{ __('adminlte::adminlte.select_date') }}</span>
    </label>
    <input
      id="tourDateInput"
      type="text"
      name="tour_date"
      class="form-control w-100"
      placeholder="dd/mm/yyyy"
      required
    >
  </div>

  {{-- Schedule --}}
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

{{-- ===== Language ===== --}}
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

{{-- ===== Pickup: Hotel O Meeting Point (excluyentes) ===== --}}
<div class="pickup-options mt-3">

  {{-- Hotel Section --}}
  <div class="pickup-section" id="hotelSection">
    <div class="section-title d-flex align-items-center gap-2">
      <i class="fas fa-hotel" aria-hidden="true"></i>
      <span>{{ __('adminlte::adminlte.select_hotel') ?? 'Hotel o punto de recogida' }}</span>
    </div>
    <label for="hotelSelect" class="visually-hidden">
      {{ __('adminlte::adminlte.select_hotel') ?? 'Hotel o punto de recogida' }}
    </label>
    <select class="form-select mb-2 w-100" id="hotelSelect" name="hotel_id">
      <option value="">-- {{ __('adminlte::adminlte.select_option') }} --</option>
      @foreach($hotels as $hotel)
        <option value="{{ $hotel->hotel_id }}">{{ $hotel->name }}</option>
      @endforeach
      <option value="other">{{ __('adminlte::adminlte.hotel_other') }}</option>
    </select>

    {{-- Campo "otro hotel" --}}
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

  {{-- Meeting Point Section --}}
  <div class="pickup-section" id="meetingPointSection">
    <div class="section-title d-flex align-items-center gap-2">
      <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
      <span>{{ __('adminlte::adminlte.meetingPoint') ?? 'Punto de encuentro' }}</span>
    </div>
    <label for="meetingPointSelect" class="visually-hidden">{{ __('adminlte::adminlte.meetingPoint') ?? 'Punto de encuentro' }}</label>
    <select class="form-select w-100" name="selected_meeting_point" id="meetingPointSelect">
      <option value="">-- {{ __('adminlte::adminlte.select_option') }} --</option>
      @foreach($meetingPoints as $mp)
        @php
          $mpName = method_exists($mp, 'getTranslated')
              ? $mp->getTranslated('name')
              : ($mp->name ?? '');
          $mpDesc = method_exists($mp, 'getTranslated')
              ? $mp->getTranslated('description')
              : ($mp->description ?? '');
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

    {{-- Info dinámica del meeting point --}}
    <div id="meetingPointInfo" class="meeting-info card card-body bg-light border rounded small d-none mt-2">
      <div id="mpDesc" class="mp-desc mb-2"></div>
      <div id="mpTime" class="mp-time text-muted mb-2"></div>
      <a id="mpLink" class="btn btn-sm btn-outline-success d-none" href="#" target="_blank" rel="noopener">
        <i class="fas fa-map me-1"></i> {{ __('adminlte::adminlte.open_map') ?: 'Ver ubicación' }}
      </a>
    </div>
  </div>
</div>

{{-- Hidden fields --}}
<input type="hidden" name="is_other_hotel" id="isOtherHotel" value="0">

{{-- ====== SCRIPT: Calendar dinámico + guards + exclusión mutua ====== --}}
@push('scripts')
<script>
(function(){
  if (window.__gvDateTimeInit) return;
  window.__gvDateTimeInit = true;

  // Remover duplicados
  const dups = document.querySelectorAll('#scheduleSelect');
  if (dups.length > 1) {
    dups.forEach((el, i) => { if (i > 0) el.closest('.col-12, .col-sm-6')?.remove(); });
  }

  const blockedBySchedule = @json($blockedBySchedule ?? []);
  const fullByCapacity    = @json($capacityDisabled ?? []);
  const generalBlocks     = @json($blockedGeneral ?? []);
  const fullyBlocked      = @json($fullyBlockedDates ?? []);

  const dateInput      = document.getElementById('tourDateInput');
  const scheduleSelect = document.getElementById('scheduleSelect');
  const help           = document.getElementById('noSlotsHelp');

  if (!dateInput || !scheduleSelect) return;

  // Choices.js
  try {
    if (window.__gvScheduleChoices?.destroy) {
      window.__gvScheduleChoices.destroy();
    }
    if (window.Choices) {
      window.__gvScheduleChoices = new Choices(scheduleSelect, {
        searchEnabled: false,
        shouldSort: false,
        itemSelectText: '',
        placeholder: false
      });
    }
  } catch (_) {}

  // Flatpickr
  let fp;
  if (window.flatpickr) {
    fp = flatpickr(dateInput, {
      dateFormat: 'd/m/Y',
      minDate: 'today',
      disable: [],
    });
  } else {
    dateInput.type = 'date';
  }

  const NO_SLOTS_TEXT = @json( __('adminlte::adminlte.no_slots_for_date') ?: 'No hay horarios disponibles para esa fecha.' );

  function updateDisabled() {
    const sid = scheduleSelect.value;
    const specific = [
      ...(blockedBySchedule[sid] || []),
      ...(fullByCapacity[sid] || []),
    ];
    const combined = [
      ...generalBlocks,
      ...fullyBlocked,
      ...specific,
    ];

    if (fp) {
      fp.set('disable', combined);
      fp.clear();
    }

    const isBlocked = specific.length > 0 || combined.length > 0;
    help.style.display = isBlocked ? 'block' : 'none';
    help.textContent   = isBlocked ? NO_SLOTS_TEXT : '';
  }

  scheduleSelect.addEventListener('change', updateDisabled);
  updateDisabled();

  // ======== EXCLUSIÓN MUTUA: Hotel vs Meeting Point ========
  const hotelSelect = document.getElementById('hotelSelect');
  const meetingPointSelect = document.getElementById('meetingPointSelect');
  const hotelSection = document.getElementById('hotelSection');
  const meetingPointSection = document.getElementById('meetingPointSection');
  const otherHotelWrapper = document.getElementById('otherHotelWrapper');
  const otherHotelInput = document.getElementById('otherHotelInput');
  const isOtherHotelInput = document.getElementById('isOtherHotel');
  const outsideMessage = document.getElementById('outsideAreaMessage');
  const meetingPointInfo = document.getElementById('meetingPointInfo');

  if (hotelSelect && meetingPointSelect) {
    // Al seleccionar hotel, deshabilitar meeting point
    hotelSelect.addEventListener('change', function() {
      if (this.value) {
        meetingPointSelect.value = '';
        meetingPointSection.classList.add('disabled');
        meetingPointInfo.classList.add('d-none');

        // Manejar "otro hotel"
        const isOther = this.value === 'other';
        otherHotelWrapper.classList.toggle('d-none', !isOther);
        if (isOtherHotelInput) isOtherHotelInput.value = isOther ? '1' : '0';
        if (outsideMessage) outsideMessage.style.display = isOther ? 'block' : 'none';
        if (isOther && otherHotelInput) otherHotelInput.focus();
      } else {
        meetingPointSection.classList.remove('disabled');
        otherHotelWrapper.classList.add('d-none');
        if (isOtherHotelInput) isOtherHotelInput.value = '0';
        if (outsideMessage) outsideMessage.style.display = 'none';
      }
    });

    // Al seleccionar meeting point, deshabilitar hotel
    meetingPointSelect.addEventListener('change', function() {
      if (this.value) {
        hotelSelect.value = '';
        hotelSection.classList.add('disabled');
        otherHotelWrapper.classList.add('d-none');
        if (isOtherHotelInput) isOtherHotelInput.value = '0';

        // Mostrar info del meeting point
        const option = this.options[this.selectedIndex];
        const desc = option.getAttribute('data-desc') || '';
        const time = option.getAttribute('data-time') || '';
        const url = option.getAttribute('data-url') || '';

        meetingPointInfo.classList.remove('d-none');
        document.getElementById('mpDesc').textContent = desc;
        document.getElementById('mpTime').textContent = time ? `⏰ ${time}` : '';

        const link = document.getElementById('mpLink');
        if (url) {
          link.href = url;
          link.classList.remove('d-none');
        } else {
          link.classList.add('d-none');
        }
      } else {
        hotelSection.classList.remove('disabled');
        meetingPointInfo.classList.add('d-none');
      }
    });
  }
})();
</script>
@endpush
