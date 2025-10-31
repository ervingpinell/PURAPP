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

{{-- ===== Pickup (Hotel) ===== --}}
<div class="section-title mt-3 d-flex align-items-center gap-2">
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

{{-- Campo “otro hotel” --}}
<div class="mb-2 d-none" id="otherHotelWrapper">
  <label for="otherHotelInput" class="form-label">{{ __('adminlte::adminlte.hotel_name') }}</label>
  <input type="text" class="form-control" name="other_hotel_name" id="otherHotelInput"
         placeholder="{{ __('adminlte::adminlte.hotel_name') }}">
  <div class="form-text text-danger mt-1" id="outsideAreaMessage" style="display:none;">
    {{ __('adminlte::adminlte.outside_area')
        ?: 'Has ingresado un hotel personalizado. Contáctanos para confirmar si podemos ofrecer transporte desde ese lugar.' }}
  </div>
</div>

{{-- ===== Meeting Point (opcional / alternativo) ===== --}}
<div class="section-title mt-3 d-flex align-items-center gap-2">
  <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
  <span>{{ __('adminlte::adminlte.meetingPoint') ?? 'Punto de encuentro' }}</span>
</div>
<label for="meetingPointSelect" class="visually-hidden">{{ __('adminlte::adminlte.meetingPoint') ?? 'Punto de encuentro' }}</label>
<select class="form-select w-100" name="selected_meeting_point" id="meetingPointSelect">
  <option value="">-- {{ __('adminlte::adminlte.select_option') }} --</option>
  @foreach($meetingPoints as $mp)
    @php
      $mpName = $mp->getTranslated('name');
      $mpDesc = $mp->getTranslated('description');
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
  <div id="mpDesc" class="mp-desc mb-1"></div>
  <div id="mpTime" class="mp-time"></div>
  <a id="mpLink" class="btn btn-sm btn-outline-success d-none mt-2" href="#" target="_blank" rel="noopener">
    <i class="fas fa-map me-1"></i> {{ __('adminlte::adminlte.open_map') ?: 'View location' }}
  </a>
</div>

{{-- ====== SCRIPT: Calendar dinámico ====== --}}
@push('scripts')
<script>
(function(){
  const blockedBySchedule = @json($blockedBySchedule ?? []);
  const fullByCapacity = @json($capacityDisabled ?? []);
  const generalBlocks = @json($blockedGeneral ?? []);
  const fullyBlocked = @json($fullyBlockedDates ?? []);

  const dateInput = document.getElementById('tourDateInput');
  const scheduleSelect = document.getElementById('scheduleSelect');
  const help = document.getElementById('noSlotsHelp');

  if (!window.flatpickr || !dateInput) return;

  const fp = flatpickr(dateInput, {
    dateFormat: 'd/m/Y',
    minDate: 'today',
    locale: '{{ app()->getLocale() }}',
    disable: [],
  });

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
    fp.set('disable', combined);
    help.style.display = specific.length > 0 ? 'block' : 'none';
    help.textContent = specific.length > 0
      ? '{{ __("m_bookings.bookings.messages.limited_seats_available") }}'
      : '';
  }

  scheduleSelect.addEventListener('change', () => {
    updateDisabled();
    fp.clear();
  });

  updateDisabled();
})();
</script>
@endpush
