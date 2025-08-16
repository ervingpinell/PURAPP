<style>
  /* Oculta el chevron por defecto */
  .accordion-button::after { content: none !important; }

  /* Muestra + o − según estado (sin JS) */
  .accordion-button .icon-plus,
  .accordion-button .icon-minus { display:inline-block; width:1em; text-align:center; }

  .accordion-button[aria-expanded="false"] .icon-minus { display:none !important; }
  .accordion-button[aria-expanded="true"]  .icon-plus  { display:none !important; }

  /* Estética */
  .accordion-item { background: transparent; }
  .accordion-button { background: transparent; }
</style>

<div class="accordion" id="tourDetailsAccordion">
  @include('partials.tour.accordion-itinerary', ['tour' => $tour])
  @include('partials.tour.accordion-included', ['tour' => $tour])
  @include('partials.tour.accordion-hotels', ['tour' => $tour, 'hotels' => $hotels])
  @include('partials.tour.accordion-policies', [
      'tour'   => $tour,
      'cancel' => $cancel,
      'refund' => $refund,
  ])
</div>
