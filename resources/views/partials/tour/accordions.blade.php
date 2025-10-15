<style>
  .accordion-button::after { content: none !important; }
  .accordion-button .icon-plus,
  .accordion-button .icon-minus { display:inline-block; width:1em; text-align:center; }
  .accordion-button[aria-expanded="false"] .icon-minus { display:none !important; }
  .accordion-button[aria-expanded="true"]  .icon-plus  { display:none !important; }
  .accordion-item { background: transparent; }
  .accordion-button { background: transparent; }
</style>

<div class="accordion" id="tourDetailsAccordion">
  @include('partials.tour.accordion-itinerary', ['tour' => $tour])
  @include('partials.tour.accordion-included', ['tour' => $tour])
  @include('partials.tour.accordion-hotels', ['tour' => $tour, 'hotels' => $hotels])
  @include('partials.tour.accordion-policies', [
    'tour' => $tour,
    'cancelPolicy'=> $cancelPolicy,
    'refundPolicy'=> $refundPolicy,
  ])
</div>
