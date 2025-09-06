<div class="accordion-item border-0 border-bottom">
  <h2 class="accordion-header" id="headingHotels">
    <button
      class="accordion-button bg-white px-0 shadow-none collapsed"
      type="button"
      data-bs-toggle="collapse"
      data-bs-target="#collapseHotels"
      aria-expanded="false"
      aria-controls="collapseHotels"
    >
      <span class="me-2 d-inline-flex align-items-center" aria-hidden="true">
        <i class="fas fa-plus icon-plus"></i>
        <i class="fas fa-minus icon-minus"></i>
      </span>
      {{ __('adminlte::adminlte.hotels_meeting_points') }}
    </button>
  </h2>

  <div id="collapseHotels" class="accordion-collapse collapse"
       data-bs-parent="#tourDetailsAccordion">
    <div class="accordion-body px-0">
      <div class="row g-4">

        {{-- 📝 Nota general --}}
        <div class="mt-3">
          <strong>{{ __('adminlte::adminlte.pickup_details') }}</strong>
          <p>{{ __('adminlte::adminlte.pickup_note') }}</p>
        </div>

        {{-- =======================
             📍 PICKUP (HOTELES)
           ======================= --}}
        <div class="col-md-6">
          <h6 class="mb-1">
            <i class="fas fa-person-walking-luggage me-1"></i>
            {{ __('adminlte::adminlte.pickup_points') }}
          </h6>
          <p class="text-muted small mb-2">{{ __('adminlte::adminlte.select_pickup') }}</p>

          <div class="position-relative">
            <input
              type="text"
              class="form-control border rounded px-3 py-2 ps-4"
              id="pickupInput"
              name="pickup_name"
              placeholder="{{ __('adminlte::adminlte.type_to_search') }}"
              autocomplete="off"
              style="padding-left: 2rem;"
            >
            <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-2 text-muted"></i>
          </div>

          <ul class="list-group mt-2 d-none" id="pickupList" style="max-height: 220px; overflow-y: auto;">
            @foreach($hotels as $hotel)
              <li class="list-group-item list-group-item-action pickup-option"
                  data-id="{{ $hotel->hotel_id }}"
                  data-name="{{ $hotel->name }}">
                <i class="fas fa-hotel me-2 text-success"></i>
                {{ $hotel->name }}
              </li>
            @endforeach
          </ul>

          <div id="pickupValidMsg" class="text-success small mt-2 d-none">
            ✔️ {{ __('adminlte::adminlte.pickup_valid') }}
          </div>
          <div id="pickupInvalidMsg" class="text-danger small mt-2 d-none">
            ❌ {{ __('adminlte::adminlte.outside_area') }}
          </div>

          <input type="hidden" name="selected_pickup_point" id="selectedPickupPoint" value="">
        </div>

        {{-- =======================
             📍 MEETING POINT (igual al hotel)
           ======================= --}}
        <div class="col-md-6">
          <h6 class="mb-1">
            <i class="fas fa-map-marker-alt me-1"></i>
            {{ __('adminlte::adminlte.meeting_points') }}
          </h6>
          <p class="text-muted small mb-2">{{ __('adminlte::adminlte.select_meeting') }}</p>

          <div class="position-relative">
            <input
              type="text"
              class="form-control border rounded px-3 py-2 ps-4"
              id="meetingInput"
              placeholder="{{ __('adminlte::adminlte.type_to_search') }}"
              autocomplete="off"
              style="padding-left: 2rem;"
            >
            <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-2 text-muted"></i>
          </div>

          <ul class="list-group mt-2 d-none" id="meetingList" style="max-height: 220px; overflow-y: auto;">
            @foreach($meetingPoints as $mp)
              <li class="list-group-item list-group-item-action meeting-option"
                  data-id="{{ $mp->id }}"
                  data-name="{{ $mp->name }}">
                <i class="fas fa-location-dot me-2 text-success"></i>
                <div class="d-inline">
                  <strong>{{ $mp->name }}</strong>
                  @if(!empty($mp->pickup_time))
                    <small class="text-muted ms-2">{{ $mp->pickup_time }}</small>
                  @endif
                </div>
              </li>
            @endforeach
          </ul>

          {{-- Mensajes (ocultos hasta que el usuario elija) --}}
          <div id="meetingValidMsg" class="text-success small mt-2 d-none">
            ✔️ {{ __('adminlte::adminlte.meeting_valid') }}
          </div>
          <div id="meetingInvalidMsg" class="text-danger small mt-2 d-none">
            ❌ {{ __('adminlte::adminlte.meetingpoint_other_notice')
                  ?: 'Has seleccionado un punto de encuentro personalizado. Contáctanos para confirmar disponibilidad y hora exacta.' }}
          </div>

          <input type="hidden" name="selected_meeting_point" id="selectedMeetingPoint" value="">
        </div>

      </div>
    </div>
  </div>
</div>

{{-- ======= JS: mismo comportamiento/estética que hoteles ======= --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
  /* ---------- Helpers ---------- */
  const clickOutside = (elem, cb) => {
    document.addEventListener('click', (e) => {
      if (!elem.contains(e.target)) cb();
    });
  };

  /* ---------- PICKUP (hoteles) ---------- */
  const pickupInput = document.getElementById('pickupInput');
  const pickupList  = document.getElementById('pickupList');
  const pickupItems = pickupList ? pickupList.querySelectorAll('.pickup-option') : [];
  const pickupOK    = document.getElementById('pickupValidMsg');
  const pickupErr   = document.getElementById('pickupInvalidMsg');
  const pickupHidden= document.getElementById('selectedPickupPoint');

  function showPickupList(){ pickupList && pickupList.classList.remove('d-none'); }
  function hidePickupList(){ pickupList && pickupList.classList.add('d-none'); }

  function filterPickup(q){
    if (!pickupList) return;
    const term = (q || '').toLowerCase();
    let any = false;
    pickupItems.forEach(li => {
      const name = (li.dataset.name || '').toLowerCase();
      const show = !term || name.includes(term);
      li.classList.toggle('d-none', !show);
      if (show) any = true;
    });
  }

  function selectPickup(id, label){
    if (pickupHidden) pickupHidden.value = id || '';
    if (pickupInput && label) pickupInput.value = label;
    // Visual feedback (solo cuando hay selección real)
    if (id) {
      pickupOK && pickupOK.classList.remove('d-none');
      pickupErr && pickupErr.classList.add('d-none');
    } else {
      pickupOK && pickupOK.classList.add('d-none');
      pickupErr && pickupErr.classList.add('d-none');
    }
  }

  pickupInput?.addEventListener('focus', () => { showPickupList(); filterPickup(pickupInput.value); });
  pickupInput?.addEventListener('input', () => { showPickupList(); filterPickup(pickupInput.value); });

  pickupItems.forEach(li => {
    li.addEventListener('click', () => {
      selectPickup(li.dataset.id, li.dataset.name);
      hidePickupList();
    });
  });

  pickupInput && clickOutside(pickupList.parentElement, hidePickupList);

  // Estado inicial: nada seleccionado (ocultar mensajes)
  selectPickup('', '');

  /* ---------- MEETING (igual al hotel) ---------- */
  const meetingInput = document.getElementById('meetingInput');
  const meetingList  = document.getElementById('meetingList');
  const meetingItems = meetingList ? meetingList.querySelectorAll('.meeting-option') : [];
  const meetingOK    = document.getElementById('meetingValidMsg');
  const meetingErr   = document.getElementById('meetingInvalidMsg');
  const meetingHidden= document.getElementById('selectedMeetingPoint');

  function showMeetingList(){ meetingList && meetingList.classList.remove('d-none'); }
  function hideMeetingList(){ meetingList && meetingList.classList.add('d-none'); }

  function filterMeeting(q){
    if (!meetingList) return;
    const term = (q || '').toLowerCase();
    let any = false;
    meetingItems.forEach(li => {
      const name = (li.dataset.name || '').toLowerCase();
      const show = !term || name.includes(term) || li.dataset.id === 'other';
      li.classList.toggle('d-none', !show);
      if (show) any = true;
    });
  }

  function selectMeeting(id, label){
    if (meetingHidden) meetingHidden.value = id || '';
    if (meetingInput && label) meetingInput.value = label;

    // Mensajes: igual que en “otro hotel”
    if (!id) {
      meetingOK && meetingOK.classList.add('d-none');
      meetingErr && meetingErr.classList.add('d-none');
      return;
    }
    if (id === 'other') {
      meetingOK && meetingOK.classList.add('d-none');
      meetingErr && meetingErr.classList.remove('d-none');
    } else {
      meetingErr && meetingErr.classList.add('d-none');
      meetingOK && meetingOK.classList.remove('d-none');
    }
  }

  meetingInput?.addEventListener('focus', () => { showMeetingList(); filterMeeting(meetingInput.value); });
  meetingInput?.addEventListener('input', () => { showMeetingList(); filterMeeting(meetingInput.value); });

  meetingItems.forEach(li => {
    li.addEventListener('click', () => {
      const id    = li.dataset.id || '';
      const label = (id === 'other')
        ? ( '{{ __('adminlte::adminlte.meeting_point_other') ?? 'Other meeting point' }}' )
        : (li.dataset.name || '');
      selectMeeting(id, label);
      hideMeetingList();
    });
  });

  meetingInput && clickOutside(meetingList.parentElement, hideMeetingList);

  // Estado inicial: nada seleccionado ni mensajes
  selectMeeting('', '');
});
</script>
