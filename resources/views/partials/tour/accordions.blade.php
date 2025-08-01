<div class="accordion" id="tourDetailsAccordion">
  @include('partials.tour.accordion-itinerary', ['tour' => $tour])
  @include('partials.tour.accordion-included', ['tour' => $tour])
  @include('partials.tour.accordion-hotels', ['tour' => $tour, 'hotels' => $hotels])
</div>
