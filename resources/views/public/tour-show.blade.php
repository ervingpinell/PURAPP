@extends('layouts.app')
@vite(['resources/css/tour.css', 'resources/js/public.js'])

@section('content')

<section class="tour-section py-5">
  <div class="container">
    <div class="row">
      {{-- ✅ Mensajes de feedback --}}
      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif

      @if(session('error'))
        <script>
          Swal.fire({
            icon: 'error',
            title: 'Acceso Denegado',
            html: `{!! session('error') !!}`,
            confirmButtonText: 'OK'
          });
        </script>
      @endif

      @if ($errors->any())
        <div class="alert alert-danger">
          <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      {{-- 📸 CARRUSEL --}}
      <div class="col-md-7">
        <div id="tourCarousel" class="carousel slide shadow-sm rounded mb-3" data-bs-ride="carousel">
          <div class="carousel-inner rounded">
            @for ($i = 0; $i < 3; $i++)
              <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                <img src="{{ asset('images/volcano.png') }}"
                     class="d-block w-100"
                     style="max-height: 350px; object-fit: cover;"
                     alt="{{ $tour->name }}">
              </div>
            @endfor
          </div>
          <button class="carousel-control-prev" type="button" data-bs-target="#tourCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#tourCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
          </button>
        </div>

        {{-- 📝 TÍTULO PRINCIPAL + Overview --}}
        <h1 class="fw-bold">{{ $tour->name }}</h1>
        <p class="text-muted">{{ $tour->tourType->name ?? '' }}</p>

        <h2>Overview</h2>
        <p>{{ $tour->overview }}</p>
      </div>

      {{-- 📅 RESERVATION BOX --}}
      <div class="col-md-5">
        <form action="{{ route('carrito.agregar', $tour->tour_id) }}" method="POST"
              class="reservation-box p-4 shadow rounded bg-white mb-4 border"
              data-adult-price="{{ $tour->adult_price }}"
              data-kid-price="{{ $tour->kid_price }}">
          @csrf
          <input type="hidden" name="tour_id" value="{{ $tour->tour_id }}">

          {{-- ✅ Price Section --}}
          <h3 class="fw-bold mb-2">Price</h3>
          <div class="price-breakdown mb-3">
            <span class="fw-bold">Adult:</span>
            <span class="price-adult fw-bold">${{ number_format($tour->adult_price, 2) }}</span> |
            <span class="fw-bold">Kid:</span>
            <span class="price-kid fw-bold">${{ number_format($tour->kid_price, 2) }}</span>
          </div>

          {{-- ✅ Traveler button --}}
          <div class="mb-2">
            <button type="button"
              class="btn traveler-button d-flex align-items-center justify-content-between"
              data-bs-toggle="modal" data-bs-target="#travelerModal">
              <span>
                <i class="fas fa-user me-1"></i>
                <span id="traveler-summary">2</span>
              </span>
              <i class="fas fa-chevron-down"></i>
            </button>
          </div>

          {{-- ✅ Total dinámico --}}
          <p class="fw-bold mb-3">
            Total: <span id="reservation-total-price" style="color:#F92526;">$0.00</span>
          </p>

          {{-- ✅ Fecha --}}
          <label class="form-label">Select date</label>
          <input type="date" name="tour_date" class="form-control mb-3" required>

          {{-- ✅ Horario --}}
          <label class="form-label">Select time</label>
          <select name="schedule_id" class="form-select mb-3" required>
            <option value="">-- Select --</option>
            @foreach($tour->schedules->sortBy('start_time') as $schedule)
              <option value="{{ $schedule->schedule_id }}">
                {{ date('g:i A', strtotime($schedule->start_time)) }} - {{ date('g:i A', strtotime($schedule->end_time)) }}
              </option>
            @endforeach
          </select>

          {{-- ✅ Language --}}
          <label class="form-label">Select language</label>
          <select name="tour_language_id" class="form-select mb-3" required>
            <option value="">-- Select --</option>
            @foreach($tour->languages as $lang)
              <option value="{{ $lang->tour_language_id }}">{{ $lang->name }}</option>
            @endforeach
          </select>

          {{-- ✅ Adultos y niños ocultos --}}
          <input type="hidden" name="adults_quantity" id="adults_quantity" value="2" required>
          <input type="hidden" name="kids_quantity" id="kids_quantity" value="0">

          {{-- ✅ Hotel o punto de encuentro --}}
          <input type="hidden" name="hotel_id" id="selectedPickupPoint">
          <input type="hidden" name="is_other_hotel" id="isOtherHotel" value="0">
          <input type="hidden" name="other_hotel_name" id="otherHotelName">

          {{-- Botón agregar al carrito --}}
          <button type="submit" class="btn btn-success w-100">
            Add to Cart
          </button>
        </form>

        {{-- 🌐 INFORMATION BOX --}}
        <div class="languages-schedules-box p-3 shadow rounded bg-white border">
          <h3 class="mb-3 fw-bold">Tour Information</h3>

          <h4>Duration</h4>
          <p>{{ $tour->length }} hours</p>

          <h4>Group Size</h4>
          <p>{{ $tour->max_capacity }}</p>

          <h4>Languages Available</h4>
          <p class="badges-group">
            @foreach($tour->languages as $lang)
              <span class="badge bg-secondary mb-1">{{ $lang->name }}</span>
            @endforeach
          </p>

          <h4>Schedules</h4>
          <p class="badges-group">
            @foreach($tour->schedules->sortBy('start_time') as $schedule)
              <span class="badge bg-success mb-1">
                {{ date('g:i A', strtotime($schedule->start_time)) }} - {{ date('g:i A', strtotime($schedule->end_time)) }}
              </span>
            @endforeach
          </p>
        </div>
      </div>
    </div>

    {{-- 🔽 ACCORDIONS --}}
    <div class="row mt-5">
      <div class="col-md-12">
        <div class="accordion" id="tourDetailsAccordion">

          {{-- ✅ Itinerary --}}
          <div class="accordion-item border-0 border-bottom">
            <h2 class="accordion-header" id="headingItinerary">
              <button class="accordion-button bg-white px-0 shadow-none collapsed" type="button"
                      data-bs-toggle="collapse" data-bs-target="#collapseItinerary">
                <i class="fas fa-plus me-2 toggle-icon"></i> Itinerary
              </button>
            </h2>
            <div id="collapseItinerary" class="accordion-collapse collapse"
                 data-bs-parent="#tourDetailsAccordion">
              <div class="accordion-body px-0">
                @if($tour->itinerary)
                  <h5>{{ $tour->itinerary->name }}</h5>
                  <p>{{ $tour->itinerary->description }}</p>

                  <div class="itinerary-timeline">
                    @foreach($tour->itinerary->items as $index => $item)
                      <div class="timeline-item">
                        <div class="timeline-marker">{{ $index + 1 }}</div>
                        <div class="timeline-content">
                          <h6>{{ $item->title }}</h6>
                          <p>{{ $item->description }}</p>
                          @if($item->duration)
                            <small>{{ $item->duration }}</small>
                          @endif
                        </div>
                      </div>
                    @endforeach
                  </div>
                @else
                  <p>No itinerary info.</p>
                @endif
              </div>
            </div>
          </div>

          {{-- ✅ What's Included --}}
          <div class="accordion-item border-0 border-bottom">
            <h2 class="accordion-header" id="headingIncluded">
              <button class="accordion-button bg-white px-0 shadow-none collapsed" type="button"
                      data-bs-toggle="collapse" data-bs-target="#collapseIncluded">
                <i class="fas fa-plus me-2 toggle-icon"></i> What's Included
              </button>
            </h2>
            <div id="collapseIncluded" class="accordion-collapse collapse"
                 data-bs-parent="#tourDetailsAccordion">
              <div class="accordion-body px-0">
                <div class="row">
                  <div class="col-md-6">
                    @foreach($tour->amenities as $am)
                      <li>✔️ {{ $am->name }}</li>
                    @endforeach
                  </div>
                  <div class="col-md-6">
                    @foreach($tour->excludedAmenities as $ex)
                      <li>❌ {{ $ex->name }}</li>
                    @endforeach
                  </div>
                </div>
              </div>
            </div>
          </div>

              {{-- ✅ Hotels & Meeting Points --}}
          <div class="accordion-item border-0 border-bottom">
            <h2 class="accordion-header" id="headingHotels">
              <button class="accordion-button bg-white px-0 shadow-none collapsed" type="button"
                      data-bs-toggle="collapse" data-bs-target="#collapseHotels">
                <i class="fas fa-plus me-2 toggle-icon"></i> Hotels & Meeting Points
              </button>
            </h2>
            <div id="collapseHotels" class="accordion-collapse collapse"
                 data-bs-parent="#tourDetailsAccordion">
              <div class="accordion-body px-0">
                <div class="row g-4">
                  <div class="mt-3">
                    <strong>Pickup details</strong>
                    <p class="mb-0">
                      Free pick-ups are only for hotels in the Fortuna area...
                    </p>
                  </div>

                  {{-- 📍 Pickup Points --}}
                  <div class="col-md-6">
                    <h6><i class="fas fa-person-walking-luggage me-1"></i> Pickup points</h6>
                    <p class="text-muted small mb-2">Select a pickup point</p>

                    <div class="selected-option border rounded px-3 py-2 d-flex justify-content-between align-items-center"
                         id="selectedPickupDisplay" style="cursor: pointer;">
                      <span class="text-muted">Type to search...</span>
                      <i class="fas fa-chevron-down"></i>
                    </div>

                    <div class="pickup-list border rounded p-2 d-none mt-2" id="pickupListWrapper" style="max-height: 250px; overflow-y: auto;">
                      <input type="text" id="pickupSearch" class="form-control mb-2" placeholder="Type to search...">
                      <ul class="list-unstyled mb-0" id="pickupList">
                        @forelse ($hotels as $hotel)
                          <li class="mb-2">
                            <label class="d-flex align-items-start gap-2">
                              <input type="radio" name="pickupOption" value="{{ $hotel->hotel_id }}">
                              <div>
                                <i class="fas fa-hotel me-1"></i>
                                <strong>{{ $hotel->name }}</strong><br>
                                <small class="text-muted">Example address for {{ $hotel->name }}</small>
                              </div>
                            </label>
                          </li>
                        @empty
                          <li>No pickup points available.</li>
                        @endforelse
                      </ul>
                      <div id="pickupNotFound" class="text-danger small mt-2 d-none">
                        Hotel not found.
                      </div>
                    </div>
                    <input type="hidden" name="selected_pickup_point" id="selectedPickupPoint">
                  </div>

                  {{-- 📍 Meeting Points --}}
                  <div class="col-md-6">
                    <h6><i class="fas fa-map-marker-alt me-1"></i> Meeting points</h6>
                    <p class="text-muted small mb-2">Select a meeting point</p>
                    <input type="text" id="meetingSearch" class="form-control mb-2" placeholder="Type to search...">

                    <div class="meeting-list border rounded p-2" id="meetingListWrapper" style="max-height: 250px; overflow-y: auto;">
                      <ul class="list-unstyled mb-0" id="meetingList">
                        <li class="mb-2">
                          <label class="d-flex align-items-start gap-2">
                            <input type="radio" name="meetingOption" value="Main Street Entrance">
                            <div>
                              <i class="fas fa-map-marker-alt me-1"></i>
                              <strong>Main Street Entrance</strong><br>
                              <small class="text-muted">Example Address 123</small>
                            </div>
                          </label>
                        </li>
                        <!-- Otros puntos -->
                      </ul>
                      <div id="meetingNotFound" class="text-danger small mt-2 d-none">
                        Meeting point not found.
                      </div>
                    </div>
                    <input type="hidden" name="selected_meeting_point" id="selectedMeetingPoint">
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>

  </div>
  <script>
    window.tourId = {{ $tour->tour_id }};
    window.maxCapacity = {{ $tour->max_capacity }};
  </script>
</section>

{{-- ✅ Travelers Modal --}}
@include('partials.bookmodal')

@endsection
