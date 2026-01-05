<form action="/carts/add" method="POST"
  class="reservation-box gv-ui is-compact is-compact-2 p-3 shadow-sm rounded bg-white mb-4 border"
  data-max-total="{{ $maxPersonsGlobal ?? 12 }}">
  @csrf
  <input type="hidden" name="tour_id" value="{{ $tour->tour_id }}">

  @include('partials.tour.reservation.header')

  <div class="form-body position-relative">
    <fieldset>
      @include('partials.tour.reservation.travelers')
      @include('partials.tour.reservation.fields')

      {{-- Hidden fields para otros datos --}}
      <input type="hidden" name="is_other_hotel" id="isOtherHotel" value="0">
      <input type="hidden" name="selected_pickup_point" id="selectedPickupPoint">
      <input type="hidden" name="selected_meeting_point" id="selectedMeetingPoint">
    </fieldset>

    {{-- Pay Later Option (Phase 10) - Fuera del fieldset para que funcione cuando esté autenticado --}}
    @auth
    @if(setting('booking.pay_later.enabled', false))
    <div class="form-check mt-3 mb-2 px-3">
      <input type="checkbox" class="form-check-input" id="is_pay_later" name="is_pay_later" value="1">
      <label class="form-check-label" for="is_pay_later">
        <strong>{{ __('Reserve ahora, paga después') }}</strong>
        <small class="text-muted d-block">
          Paga manualmente antes de {{ setting('booking.pay_later.cancel_hours_before_tour', 24) }} horas del tour o se cancelará automáticamente.
        </small>
      </label>
    </div>
    @endif
    @endauth
  </div>

  @include('partials.tour.reservation.cta')
</form>