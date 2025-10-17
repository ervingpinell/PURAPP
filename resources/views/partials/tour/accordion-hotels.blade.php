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
             📍 MEETING POINTS
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
                  data-name="{{ $mp->name }}"
                  data-description="{{ $mp->description }}"
                  data-url="{{ $mp->map_url }}">
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

          {{-- 🔍 Detalles del punto seleccionado --}}
          <div id="meetingDetails" class="meeting-details-card d-none mt-3">
            <div class="d-flex align-items-start gap-2">
              <div class="icon bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px;">
                <i class="fas fa-map-marker-alt"></i>
              </div>
              <div>
                <p class="fw-bold mb-1">{{ __('adminlte::adminlte.meeting_point_details') }}</p>
                <p class="small text-muted mb-2" id="meetingDesc"></p>
                <a href="#" id="meetingMapLink" target="_blank" class="btn btn-sm btn-outline-success d-none">
                  <i class="fas fa-map me-1"></i> {{ __('adminlte::adminlte.open_map') }}
                </a>
              </div>
            </div>
          </div>

          {{-- Mensajes de validación --}}
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

{{-- ======= JS: interacción dinámica ======= --}}
<script>
document.addEventListener('DOMContentLoaded', () => {

  /* ---------- PICKUP (Hoteles) ---------- */
  const pickupInput = document.getElementById('pickupInput');
  const pickupList  = document.getElementById('pickupList');
  const pickupItems = pickupList ? pickupList.querySelectorAll('.pickup-option') : [];
  const pickupOK    = document.getElementById('pickupValidMsg');
  const pickupErr   = document.getElementById('pickupInvalidMsg');
  const pickupHidden= document.getElementById('selectedPickupPoint');

  const showPickupList = () => pickupList?.classList.remove('d-none');
  const hidePickupList = () => pickupList?.classList.add('d-none');

  const filterPickup = (q) => {
    const term = (q || '').toLowerCase();
    pickupItems.forEach(li => {
      const name = (li.dataset.name || '').toLowerCase();
      li.classList.toggle('d-none', term && !name.includes(term));
    });
  };

  const selectPickup = (id, name) => {
    pickupHidden.value = id || '';
    pickupInput.value = name || '';
    pickupOK.classList.toggle('d-none', !id);
    pickupErr.classList.add('d-none');
  };

  pickupInput?.addEventListener('focus', () => { showPickupList(); filterPickup(pickupInput.value); });
  pickupInput?.addEventListener('input', () => { showPickupList(); filterPickup(pickupInput.value); });
  pickupItems.forEach(li => li.addEventListener('click', () => { selectPickup(li.dataset.id, li.dataset.name); hidePickupList(); }));
  document.addEventListener('click', e => { if (!pickupList.contains(e.target) && e.target !== pickupInput) hidePickupList(); });

  /* ---------- MEETING POINT ---------- */
  const meetingInput = document.getElementById('meetingInput');
  const meetingList  = document.getElementById('meetingList');
  const meetingItems = meetingList ? meetingList.querySelectorAll('.meeting-option') : [];
  const meetingOK    = document.getElementById('meetingValidMsg');
  const meetingErr   = document.getElementById('meetingInvalidMsg');
  const meetingHidden= document.getElementById('selectedMeetingPoint');
  const detailsBox   = document.getElementById('meetingDetails');
  const descEl       = document.getElementById('meetingDesc');
  const mapLink      = document.getElementById('meetingMapLink');

  const showMeetingList = () => meetingList?.classList.remove('d-none');
  const hideMeetingList = () => meetingList?.classList.add('d-none');

  const filterMeeting = (q) => {
    const term = (q || '').toLowerCase();
    meetingItems.forEach(li => {
      const name = (li.dataset.name || '').toLowerCase();
      li.classList.toggle('d-none', term && !name.includes(term));
    });
  };

  const selectMeeting = (id, name, desc = '', url = '') => {
    meetingHidden.value = id || '';
    meetingInput.value = name || '';

    // Mostrar detalles si existen
    if (desc) {
      descEl.textContent = desc;
      detailsBox.classList.remove('d-none');
      if (url) {
        mapLink.href = url;
        mapLink.classList.remove('d-none');
      } else {
        mapLink.classList.add('d-none');
      }
    } else {
      detailsBox.classList.add('d-none');
    }

    // Mostrar mensajes
    if (!id || id === 'other') {
      meetingOK.classList.add('d-none');
      meetingErr.classList.remove('d-none');
    } else {
      meetingOK.classList.remove('d-none');
      meetingErr.classList.add('d-none');
    }
  };

  meetingInput?.addEventListener('focus', () => { showMeetingList(); filterMeeting(meetingInput.value); });
  meetingInput?.addEventListener('input', () => { showMeetingList(); filterMeeting(meetingInput.value); });

  meetingItems.forEach(li => li.addEventListener('click', () => {
    selectMeeting(li.dataset.id, li.dataset.name, li.dataset.description, li.dataset.url);
    hideMeetingList();
  }));

  document.addEventListener('click', e => { if (!meetingList.contains(e.target) && e.target !== meetingInput) hideMeetingList(); });
});
</script>

@push('css')
<style>
  .meeting-details-card {
    background-color: #f9fdf9;
    border: 1px solid rgba(0,0,0,.08);
    border-left: 4px solid var(--primary-color, #60a862);
    border-radius: 8px;
    padding: 0.75rem 1rem;
    box-shadow: 0 3px 10px rgba(0,0,0,.05);
    transition: all .2s ease-in-out;
  }
  .meeting-details-card:hover {
    box-shadow: 0 5px 14px rgba(0,0,0,.1);
    transform: translateY(-2px);
  }
  .meeting-details-card .icon {
    background: #eaf8eb;
    color: #2e7d32;
    font-size: 1.1rem;
  }
</style>
@endpush
