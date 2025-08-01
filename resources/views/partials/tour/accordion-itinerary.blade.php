<div class="accordion-item border-0 border-bottom">
  <h2 class="accordion-header" id="headingItinerary">
    <button class="accordion-button bg-white px-0 shadow-none collapsed" type="button"
            data-bs-toggle="collapse" data-bs-target="#collapseItinerary">
      <i class="fas fa-plus me-2 toggle-icon"></i> {{ __('adminlte::adminlte.itinerary') }}
    </button>
  </h2>
  <div id="collapseItinerary" class="accordion-collapse collapse"
       data-bs-parent="#tourDetailsAccordion">
    <div class="accordion-body px-0">
      @if($tour->itinerary)
        <h5>{{ $tour->itinerary->translated_name ?? '' }}</h5>
        <p>{{ $tour->itinerary->translated_description ?? '' }}</p>

        <div class="itinerary-timeline">
          @foreach($tour->itinerary->items as $index => $item)
            <div class="timeline-item">
              <div class="timeline-marker">{{ $index + 1 }}</div>
              <div class="timeline-content">
                <h6>{{ $item->translated_title ?? $item->title }}</h6>
                <p>{{ $item->translated_description ?? $item->description }}</p>
                @if($item->duration)
                  <small>{{ $item->duration }}</small>
                @endif
              </div>
            </div>
          @endforeach
        </div>
      @else
        <p>{{ __('adminlte::adminlte.no_itinerary_info') }}</p>
      @endif
    </div>
  </div>
</div>
