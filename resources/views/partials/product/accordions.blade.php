<style>
  .accordion-button::after { content: none !important; }

  /* Íconos +/− como en los otros acordeones */
  .accordion-button .icon-plus,
  .accordion-button .icon-minus {
    width: 1em;
    text-align: center;
  }
  .accordion-button[aria-expanded="false"] .icon-minus { display: none !important; }
  .accordion-button[aria-expanded="true"]  .icon-plus  { display: none !important; }

  .accordion-item { background: transparent; }
  .accordion-button { background: transparent; }           /* sin padding forzado */
  .accordion-button.px-0 { padding-left: 0 !important; }   /* asegura alineación */

</style>

<div class="accordion" id="tourDetailsAccordion">
  @include('partials.product.accordion-itinerary', ['product' => $product])
  @include('partials.product.accordion-included', ['product' => $product])
  @include('partials.product.accordion-hotels', ['product' => $product, 'hotels' => $hotels])
  @include('partials.product.accordion-policies', [
    'product'      => $product,
    'cancelPolicy' => $cancelPolicy ?? null,
    'refundPolicy' => $refundPolicy ?? null,
  ])
</div>
