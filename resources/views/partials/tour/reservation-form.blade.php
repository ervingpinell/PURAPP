@php
  // Hoy según zona horaria de la app
  $tz = config('app.timezone', 'America/Costa_Rica');
  $today = \Carbon\Carbon::today($tz)->toDateString();
@endphp

<form action="{{ route('carrito.agregar', $tour->tour_id) }}" method="POST"
  class="reservation-box p-3 shadow-sm rounded bg-white mb-4 border"
  data-adult-price="{{ $tour->adult_price }}"
  data-kid-price="{{ $tour->kid_price }}">

  @csrf
  <input type="hidden" name="tour_id" value="{{ $tour->tour_id }}">

  {{-- Precio --}}
  <h3 class="fw-bold fs-5 mb-2">{{ __('adminlte::adminlte.price') }}</h3>
  <div class="price-breakdown mb-3">
    <span class="fw-bold">{{ __('adminlte::adminlte.adult') }}:</span>
    <span class="price-adult fw-bold">${{ number_format($tour->adult_price, 2) }}</span> |
    <span class="fw-bold">{{ __('adminlte::adminlte.kid') }}:</span>
    <span class="price-kid fw-bold">${{ number_format($tour->kid_price, 2) }}</span>
  </div>

  {{-- Botón viajeros --}}
  <div class="mb-2">
    <button type="button"
      class="btn traveler-button d-flex align-items-center justify-content-between"
      data-bs-toggle="modal" data-bs-target="#travelerModal">
      <span>
        <i class="fas fa-user me-1"></i>
        <span id="traveler-summary">2</span>
      </span>
      <i class="fas fa-chevron-down"></i>
    </button>
  </div>

  {{-- Total dinámico --}}
  <p class="fw-bold mb-3" style="font-size: 1rem;">
    {{ __('adminlte::adminlte.total') }}:
    <span id="reservation-total-price" style="color:#F92526; font-weight: bold;">$0.00</span>
  </p>

  {{-- Fecha (bloquea días pasados) --}}
  <label class="form-label">{{ __('adminlte::adminlte.select_date') }}</label>
  <input
    type="date"
    name="tour_date"
    class="form-control mb-3"
    min="{{ $today }}"
    value="{{ old('tour_date') }}"
    required
  >

  {{-- Horario --}}
  <label class="form-label">{{ __('adminlte::adminlte.select_time') }}</label>
  <select name="schedule_id" class="form-select mb-3" id="scheduleSelect" required>
    <option value="">-- {{ __('adminlte::adminlte.select_option') }} --</option>
    @foreach($tour->schedules->sortBy('start_time') as $schedule)
      <option value="{{ $schedule->schedule_id }}">
        {{ date('g:i A', strtotime($schedule->start_time)) }} - {{ date('g:i A', strtotime($schedule->end_time)) }}
      </option>
    @endforeach
  </select>

  {{-- Idioma --}}
  <label class="form-label">{{ __('adminlte::adminlte.select_language') }}</label>
  <select name="tour_language_id" class="form-select mb-3" required>
    <option value="">-- {{ __('adminlte::adminlte.select_option') }} --</option>
    @foreach($tour->languages as $lang)
      <option value="{{ $lang->tour_language_id }}">{{ $lang->name }}</option>
    @endforeach
  </select>

  {{-- Hotel --}}
  <label class="form-label">{{ __('adminlte::adminlte.select_hotel') }}</label>
  <select class="form-select mb-3" id="hotelSelect" name="hotel_id">
    <option value="">-- {{ __('adminlte::adminlte.select_option') }} --</option>
    @foreach($hotels as $hotel)
      <option value="{{ $hotel->hotel_id }}">{{ $hotel->name }}</option>
    @endforeach
    <option value="other">{{ __('adminlte::adminlte.hotel_other') }}</option>
  </select>

  <div class="mb-3 d-none" id="otherHotelWrapper">
    <label for="otherHotelInput" class="form-label">{{ __('adminlte::adminlte.hotel_name') }}</label>
    <input type="text" class="form-control" name="other_hotel_name" id="otherHotelInput"
           placeholder="{{ __('adminlte::adminlte.hotel_name') }}">
    <div class="form-text text-danger mt-1" id="outsideAreaMessage" style="display: none;">
      {{ __('adminlte::adminlte.outside_area') }}
    </div>
  </div>

  <input type="hidden" name="is_other_hotel" id="isOtherHotel" value="0">

  {{-- Cantidades --}}
  <input type="hidden" name="adults_quantity" id="adults_quantity" value="2" required>
  <input type="hidden" name="kids_quantity" id="kids_quantity" value="0">

  {{-- Campos auxiliares (si los usas) --}}
  <input type="hidden" name="selected_pickup_point" id="selectedPickupPoint">
  <input type="hidden" name="selected_meeting_point" id="selectedMeetingPoint">

  {{-- Submit / Login --}}
  @auth
    <button type="submit" class="btn btn-success w-100">
      <i class="fas fa-cart-plus me-1"></i> {{ __('adminlte::adminlte.add_to_cart') }}
    </button>
  @else
    <a href="{{ route('login') }}" class="btn btn-success w-100"
      onclick="return askLoginWithSwal(event, this.href);">
      <i class="fas fa-cart-plus me-1"></i> {{ __('adminlte::adminlte.add_to_cart') }}
    </a>

    @once
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
      <script>
        function askLoginWithSwal(e, loginUrl) {
          e.preventDefault();
          if (window.Swal) {
            Swal.fire({
              title: @json(__('adminlte::adminlte.login_required_title')),
              text:  @json(__('adminlte::adminlte.login_required_text')),
              icon: 'info',
              showCancelButton: true,
              confirmButtonText: @json(__('adminlte::adminlte.login')),
              cancelButtonText:  @json(__('adminlte::adminlte.cancel')),
              confirmButtonColor: '#198754',
              allowOutsideClick: false
            }).then(res => {
              if (res.isConfirmed) window.location.href = loginUrl;
            });
          } else {
            if (confirm(@json(__('adminlte::adminlte.login_required_text_confirm')))) {
              window.location.href = loginUrl;
            }
          }
          return false;
        }
      </script>
    @endonce
  @endauth
</form>

{{-- Scripts del partial --}}
@once
  <script>
    (function() {
      const form = document.currentScript.closest('.reservation-box')?.closest('form') 
                || document.querySelector('form[action*="carrito.agregar"]');

      // 1) Toggle "Otro hotel"
      const hotelSel = document.getElementById('hotelSelect');
      const otherWrap = document.getElementById('otherHotelWrapper');
      const isOtherH  = document.getElementById('isOtherHotel');
      const otherInp  = document.getElementById('otherHotelInput');

      const toggleOther = () => {
        const isOther = hotelSel?.value === 'other';
        otherWrap?.classList.toggle('d-none', !isOther);
        if (isOtherH) isOtherH.value = isOther ? 1 : 0;
        if (!isOther && otherInp) otherInp.value = '';
      };
      hotelSel?.addEventListener('change', toggleOther);
      toggleOther();

      // 2) (Opcional) Deshabilitar horarios si no hay fecha
      const dateInput  = form?.querySelector('input[name="tour_date"]');
      const schedule   = document.getElementById('scheduleSelect');

      const toggleSchedule = () => {
        if (schedule) schedule.disabled = !dateInput?.value;
      };
      toggleSchedule();
      dateInput?.addEventListener('change', toggleSchedule);

      // 3) Validación fecha pasada (defensa extra)
      const zeroHours = d => { d.setHours(0,0,0,0); return d; };
      const TODAY = zeroHours(new Date({{ json_encode($today) }}));

      form?.addEventListener('submit', function (e) {
        if (!dateInput || !dateInput.value) return;

        const picked = zeroHours(new Date(dateInput.value));
        if (picked < TODAY) {
          e.preventDefault();
          if (window.Swal) {
            Swal.fire({
              icon: 'error',
              title: @json(__('adminlte::adminlte.error') ?? 'Error'),
              text:  @json(__('adminlte::adminlte.cannot_book_past_dates') ?? 'No puedes reservar para fechas anteriores a hoy.'),
              confirmButtonColor: '#198754'
            });
          } else {
            alert(@json(__('adminlte::adminlte.cannot_book_past_dates') ?? 'No puedes reservar para fechas anteriores a hoy.'));
          }
          dateInput.focus();
        }
      });

      // si escriben manualmente una fecha pasada, corrige a hoy
      dateInput?.addEventListener('change', function () {
        if (!this.value) return;
        const picked = zeroHours(new Date(this.value));
        if (picked < TODAY) {
          this.value = {{ json_encode($today) }};
        }
      });
    })();
  </script>
@endonce
