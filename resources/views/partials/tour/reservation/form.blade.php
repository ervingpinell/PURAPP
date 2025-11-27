<form action="{{ route('public.carts.add') }}" method="POST"
  class="reservation-box gv-ui is-compact is-compact-2 p-3 shadow-sm rounded bg-white mb-4 border"
  data-max-total="{{ $maxPersonsGlobal ?? 12 }}">
  @csrf
  <input type="hidden" name="tour_id" value="{{ $tour->tour_id }}">

  @include('partials.tour.reservation.header')

  <div class="form-body position-relative">
    <fieldset @guest disabled aria-disabled="true" @endguest>
      @include('partials.tour.reservation.travelers')
      @include('partials.tour.reservation.fields')

      {{-- Hidden fields para otros datos --}}
      <input type="hidden" name="is_other_hotel" id="isOtherHotel" value="0">
      <input type="hidden" name="selected_pickup_point" id="selectedPickupPoint">
      <input type="hidden" name="selected_meeting_point" id="selectedMeetingPoint">
    </fieldset>
  </div>

  @include('partials.tour.reservation.cta')
</form>