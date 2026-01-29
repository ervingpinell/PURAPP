<form action="/carts/add" method="POST"
  class="reservation-box brand-ui is-compact is-compact-2 p-3 shadow-sm rounded bg-white mb-4 border"
  data-max-total="{{ $maxPersonsGlobal ?? 12 }}">
  @csrf
  <input type="hidden" name="product_id" value="{{ $product->product_id }}">



  <div class="form-body position-relative">
    <fieldset>
      @include('partials.product.reservation.travelers')
      @include('partials.product.reservation.fields')

      {{-- Hidden fields para otros datos (Moved/Handled in fields.blade.php) --}}
    </fieldset>

    {{-- Pay Later Option (Phase 10) - Fuera del fieldset para que funcione cuando esté autenticado --}}
    @auth
    @if(setting('booking.pay_later.enabled', false))
    <div class="form-check mt-3 mb-2 px-3">
      <input type="checkbox" class="form-check-input" id="is_pay_later" name="is_pay_later" value="1">
      <label class="form-check-label" for="is_pay_later">
        <strong>{{ __('Reserve ahora, paga después') }}</strong>
        <small class="text-muted d-block">
          Paga manualmente antes de {{ setting('booking.pay_later.cancel_hours_before_tour', 24) }} horas del product o se cancelará automáticamente.
        </small>
      </label>
    </div>
    @endif
    @endauth
  </div>

  @include('partials.product.reservation.cta')
</form>