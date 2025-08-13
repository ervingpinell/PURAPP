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

        {{-- 📝 Nota --}}
        <div class="mt-3">
          <strong>{{ __('adminlte::adminlte.pickup_details') }}</strong>
          <p>{{ __('adminlte::adminlte.pickup_note') }}</p>
        </div>

        {{-- 📍 Pickup Points --}}
        <div class="col-md-6">
          <h6><i class="fas fa-person-walking-luggage me-1"></i> {{ __('adminlte::adminlte.pickup_points') }}</h6>
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

          <ul class="list-group mt-2 d-none" id="pickupList" style="max-height: 200px; overflow-y: auto;">
            @foreach($hotels as $hotel)
              <li class="list-group-item list-group-item-action pickup-option"
                  data-id="{{ $hotel->hotel_id }}">
                <i class="fas fa-hotel me-2 text-success"></i> {{ $hotel->name }}
              </li>
            @endforeach
          </ul>

          <div id="pickupValidMsg" class="text-success small mt-2 d-none">
            ✔️ {{ __('adminlte::adminlte.pickup_valid') }}
          </div>
          <div id="pickupInvalidMsg" class="text-danger small mt-2 d-none">
            ❌ {{ __('adminlte::adminlte.outside_area') }}
          </div>

          <input type="hidden" name="selected_pickup_point" id="selectedPickupPoint">
        </div>

        {{-- 📍 Meeting Points --}}
        <div class="col-md-6">
          <h6><i class="fas fa-map-marker-alt me-1"></i> {{ __('adminlte::adminlte.meeting_points') }}</h6>
          <p class="text-muted small mb-2">{{ __('adminlte::adminlte.select_meeting') }}</p>
          <input
            type="text"
            id="meetingSearch"
            class="form-control mb-2"
            placeholder="{{ __('adminlte::adminlte.type_to_search') }}"
          >

          <div class="meeting-list border rounded p-2" id="meetingListWrapper" style="max-height: 250px; overflow-y: auto;">
            <ul class="list-unstyled mb-0" id="meetingList">
              <li class="mb-2">
                <label class="d-flex align-items-start gap-2">
                  <input type="radio" name="meetingOption" value="Main Street Entrance">
                  <div>
                    <i class="fas fa-map-marker-alt me-1"></i>
                    <strong>Main Street Entrance</strong><br>
                    <small class="text-muted">{{ __('adminlte::adminlte.example_address') }}</small>
                  </div>
                </label>
              </li>
              {{-- Agrega más puntos si lo necesitas --}}
            </ul>

            <div id="meetingNotFound" class="text-danger small mt-2 d-none">
              {{ __('adminlte::adminlte.meeting_not_found') }}
            </div>
          </div>

          <input type="hidden" name="selected_meeting_point" id="selectedMeetingPoint">
        </div>

      </div>
    </div>
  </div>
</div>
