@foreach($tours as $tour)
<div class="modal fade"
     id="modalCart{{ $tour->tour_id }}"
     tabindex="-1"
     aria-hidden="true"
     data-max="{{ $tour->max_capacity }}">
  <div class="modal-dialog">
    <form method="POST"
          action="{{ route('admin.carts.store') }}"
          class="modal-content">
      @csrf
      <input type="hidden" name="tour_id" value="{{ $tour->tour_id }}">
      <input type="hidden" name="adult_price" value="{{ $tour->adult_price }}">
      <input type="hidden" name="kid_price"   value="{{ $tour->kid_price }}">

      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">
          {{ __('carts.add_modal.title', ['name' => $tour->name]) }}
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="{{ __('adminlte::adminlte.close') }}"></button>
      </div>

      <div class="modal-body">
        <div class="mb-3">
          <label>{{ __('carts.fields.tour_date') }}</label>
          <input type="date"
                 name="tour_date"
                 class="form-control"
                 required>
        </div>

        <div class="mb-3">
          <label>{{ __('carts.fields.language') }}</label>
          <select name="tour_language_id"
                  class="form-control"
                  required>
            <option value="">{{ __('carts.placeholders.select') }}</option>
            @foreach($tour->languages as $lang)
              <option value="{{ $lang->tour_language_id }}">{{ $lang->name }}</option>
            @endforeach
          </select>
        </div>

        <div class="mb-3">
          <label>{{ __('carts.fields.schedule') }}</label>
          <select name="schedule_id" class="form-control" required>
            <option value="">{{ __('carts.placeholders.select_schedule') }}</option>
            @foreach ($tour->schedules as $schedule)
              <option value="{{ $schedule->schedule_id }}">
                {{ \Carbon\Carbon::parse($schedule->start_time)->format('g:i A') }}
                —
                {{ \Carbon\Carbon::parse($schedule->end_time)->format('g:i A') }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="mb-3">
          <label>{{ __('carts.fields.hotel') }}</label>
          <select name="hotel_id"
                  id="hotel_select_{{ $tour->tour_id }}"
                  class="form-control"
                  required>
            <option value="">{{ __('carts.placeholders.select_hotel') }}</option>
            @foreach($hotels as $hotel)
              <option value="{{ $hotel->hotel_id }}">{{ $hotel->name }}</option>
            @endforeach
            <option value="other">{{ __('carts.placeholders.other_hotel_option') }}</option>
          </select>
        </div>

        <div class="mb-3 d-none"
             id="other_hotel_container_{{ $tour->tour_id }}">
          <label>{{ __('carts.fields.hotel_name') }}</label>
          <input type="text"
                 name="other_hotel_name"
                 class="form-control"
                 placeholder="{{ __('carts.placeholders.enter_hotel_name') }}">
        </div>

        <div class="mb-3">
          <label>{{ __('carts.fields.adults') }}</label>
          <input type="number"
                 name="adults_quantity"
                 class="form-control"
                 min="1"
                 value="1"
                 required>
        </div>
        <div class="mb-3">
          <label>{{ __('carts.fields.kids') }}</label>
          <input type="number"
                 name="kids_quantity"
                 class="form-control"
                 min="0" max="2"
                 value="0">
        </div>

        <input type="hidden"
               name="is_other_hotel"
               id="is_other_hotel_{{ $tour->tour_id }}"
               value="0">
      </div>

      <div class="modal-footer">
        <button type="submit"
                class="btn btn-success w-100">
          <i class="fas fa-cart-plus"></i> {{ __('carts.buttons.add_to_cart') }}
        </button>
      </div>
    </form>
  </div>
</div>
@endforeach

@push('js')
<script>
document.addEventListener('DOMContentLoaded', () => {
  @foreach($tours as $tour)
    (function() {
      const sel  = document.getElementById('hotel_select_{{ $tour->tour_id }}');
      const box  = document.getElementById('other_hotel_container_{{ $tour->tour_id }}');
      const hid  = document.getElementById('is_other_hotel_{{ $tour->tour_id }}');
      if (!sel || !box || !hid) return;

      sel.addEventListener('change', () => {
        if (sel.value === 'other') {
          box.classList.remove('d-none');
          hid.value = '1';
        } else {
          box.classList.add('d-none');
          const input = box.querySelector('input');
          if (input) input.value = '';
          hid.value = '0';
        }
      });
    })();
  @endforeach

  document.querySelectorAll('.modal[data-max]').forEach(modal => {
    const form   = modal.querySelector('form');
    const maxCap = parseInt(modal.dataset.max, 10);

    form.addEventListener('submit', async function(e) {
      e.preventDefault();

      const tourIdEl    = form.querySelector('[name="tour_id"]');
      const dateEl      = form.querySelector('[name="tour_date"]');
      const scheduleEl  = form.querySelector('[name="schedule_id"]');
      const adultsEl    = form.querySelector('[name="adults_quantity"]');
      const kidsEl      = form.querySelector('[name="kids_quantity"]');

      const tourDate   = dateEl.value;
      const scheduleId = (scheduleEl && scheduleEl.value) || '';
      const adults     = parseInt(adultsEl.value, 10) || 0;
      const kids       = parseInt(kidsEl.value, 10)   || 0;
      const requested  = adults + kids;

      if (!tourDate) {
        return Swal.fire({
          icon: 'warning',
          title: @json(__('carts.alerts.select_tour_date'))
        });
      }

      let reserved = 0;
      try {
        const params = new URLSearchParams({
          tour_id: tourIdEl.value,
          tour_date: tourDate,
          ...(scheduleId ? { schedule_id: scheduleId } : {})
        });

        const resp = await fetch(`/admin/bookings/reserved?${params.toString()}`, {
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await resp.json();
        reserved = parseInt(data.reserved, 10) || 0;
      } catch (_) {
        return Swal.fire({
          icon: 'error',
          title: 'Error',
          text: @json(__('carts.promo.apply_error'))
        });
      }

      if (reserved + requested > maxCap) {
        const available = Math.max(0, maxCap - reserved);
        const textTmpl  = @json(__('carts.alerts.capacity_exceeded.text', ['date' => '::date', 'available' => '::available']));
        return Swal.fire({
          icon: 'error',
          title: @json(__('carts.alerts.capacity_exceeded.title')),
          text: textTmpl.replace('::date', tourDate).replace('::available', String(available))
        });
      }

      this.submit();
    });
  });
});
</script>
@endpush
