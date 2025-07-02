@extends('layouts.app')
@vite(['resources/css/tour.css', 'resources/js/public.js'])

@section('content')

<section class="tour-section py-5">
  <div class="container">
    <div class="row">

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

        <h3>Overview</h3>
        <p>{{ $tour->overview }}</p>
      </div>

      {{-- 📅 RESERVATION BOX --}}
      <div class="col-md-5">
        <div class="reservation-box p-4 shadow rounded bg-white mb-4 border"
             data-adult-price="{{ $tour->adult_price }}"
             data-kid-price="{{ $tour->kid_price }}">

          <h6 class="mb-3">
            <strong>Price:</strong>
            <span class="fw-bold">Adult:</span> <span style="color:#F92526">${{ number_format($tour->adult_price, 2) }}</span> |
            <span class="fw-bold">Kid:</span> <span style="color:#F92526">${{ number_format($tour->kid_price, 2) }}</span>
          </h6>

          {{-- ✅ Traveler button minimal --}}
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

          {{-- ✅ Total dinámico fuera del modal --}}
          <p class="fw-bold mb-3">
            Total: <span id="reservation-total-price" style="color:#F92526;">$0.00</span>
          </p>

          {{-- Fecha --}}
          <label class="form-label">Select date</label>
          <input type="date" class="form-control mb-3">

          {{-- Horario --}}
          <label class="form-label">Select time</label>
          <select class="form-select mb-3">
            @foreach($tour->schedules->sortBy('start_time') as $schedule)
              <option>
                {{ date('g:i A', strtotime($schedule->start_time)) }} - {{ date('g:i A', strtotime($schedule->end_time)) }}
              </option>
            @endforeach
          </select>

          {{-- Botón reservar --}}
          <button class="btn btn-success w-100">Check Availability</button>
        </div>

        {{-- 🌐 INFORMATION BOX --}}
        <div class="languages-schedules-box p-3 shadow rounded bg-white border">
          <h4>Information</h6>
          <H6>Hours:</h6> {{ $tour->length }}</p>
          <h6>Group Size:</h6> {{ $tour->max_capacity }}</p>

          <h6>Languages Available</h6>
          <p>
            @foreach($tour->languages as $lang)
              <span class="badge bg-secondary mb-1">{{ $lang->name }}</span>
            @endforeach
          </p>

          <h6>Schedules</h6>
          <p>
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
                  <ul>
                    @foreach($tour->itinerary->items as $item)
                      <li>{{ $item->title }}</li>
                      <p>{{ $item->description }}</p>
                    @endforeach
                  </ul>
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

        </div>
      </div>
    </div>
  </div>
</section>

{{-- ✅ Travelers Modal --}}
@include('partials.bookmodal')

@endsection
