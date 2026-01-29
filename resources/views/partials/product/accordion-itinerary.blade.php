<div class="accordion-item border-0 border-bottom">
  <h2 class="accordion-header" id="headingItinerary">
    <button
      class="accordion-button bg-white px-0 shadow-none collapsed"
      type="button"
      data-bs-toggle="collapse"
      data-bs-target="#collapseItinerary"
      aria-expanded="false"
      aria-controls="collapseItinerary"
    >
      <span class="me-2 d-inline-flex align-items-center" aria-hidden="true">
        <i class="fas fa-plus icon-plus"></i>
        <i class="fas fa-minus icon-minus"></i>
      </span>
      {{ __('adminlte::adminlte.itinerary') }}
    </button>
  </h2>
  <div id="collapseItinerary" class="accordion-collapse collapse"
       data-bs-parent="#tourDetailsAccordion">
    <div class="accordion-body px-0">
      @if($product->itinerary)
        <h5>{{ $product->itinerary->translated_name ?? '' }}</h5>
        <p>{{ $product->itinerary->translated_description ?? '' }}</p>

        <div class="itinerary-timeline">
          @foreach($product->itinerary->items as $index => $item)
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
