@php
  // Hoy según la zona horaria de la app
  $tz = config('app.timezone', 'America/Costa_Rica');
  $today = \Carbon\Carbon::today($tz)->toDateString();

  // Asegura la variable $meetingPoints (si no fue enviada desde el controlador)
  // Ideal: pásala desde el controller. Fallback defensivo aquí:
  if (!isset($meetingPoints)) {
      try {
          $meetingPoints = \App\Models\MeetingPoint::where('is_active', true)
              ->orderByRaw('sort_order IS NULL, sort_order ASC')
              ->orderBy('name', 'asc')
              ->get();
      } catch (\Throwable $e) {
          $meetingPoints = collect();
      }
  }
@endphp

@once
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endonce

<div class="modal fade" id="modalRegistrar" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="createBookingForm" action="{{ route('admin.bookings.store') }}" method="POST" novalidate>
      @csrf
      <input type="hidden" name="_modal" value="register"><!-- 👈 Para reabrir este modal tras errores -->

      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Registrar Reserva</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          {{-- ⛑️ Resumen de errores (solo si vienen de este modal) --}}
          @if ($errors->any() && (session('openModal') === 'register' || old('_modal') === 'register'))
            <div class="alert alert-danger">
              <ul class="mb-0">
                @foreach ($errors->all() as $err)
                  <li>{{ $err }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <!-- Requerido por el controller -->
          <input type="hidden" name="booking_date" value="{{ now()->toDateString() }}">

          {{-- Form principal (user/tour/schedule/idioma/fecha/hotel/pax/estado...) --}}
          @include('admin.bookings.partials.form', ['modo' => 'crear'])

          {{-- =======================
               MEETING POINT (opcional)
               ======================= --}}
          <div class="mt-3">
            <label class="form-label">
              <i class="fas fa-map-marker-alt me-1"></i>
              Pickup / Meeting point
            </label>
            <select
              name="meeting_point_id"
              id="meetingPointSelect"
              class="form-select @error('meeting_point_id') is-invalid @enderror"
            >
              <option value="">-- Selecciona un punto --</option>
              @foreach ($meetingPoints as $mp)
                <option
                  value="{{ $mp->id }}"
                  data-name="{{ $mp->name }}"
                  data-time="{{ $mp->pickup_time }}"
                  data-description="{{ $mp->description }}"
                  data-map="{{ $mp->map_url }}"
                  {{ (string)old('meeting_point_id') === (string)$mp->id ? 'selected' : '' }}
                >
                  {{ $mp->name }}{{ $mp->pickup_time ? ' — '.$mp->pickup_time : '' }}
                </option>
              @endforeach
            </select>
            @error('meeting_point_id')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror

            <div id="meetingPointHelp" class="form-text mt-1"></div>
          </div>

          {{-- Código promocional (opcional) --}}
          <div class="mt-3">
            <label for="promo_code" class="form-label">Promo code (optional)</label>
            <div class="input-group">
              <input
                type="text"
                name="promo_code"
                id="promo_code"
                class="form-control @error('promo_code') is-invalid @enderror"
                placeholder="Enter promo code"
                value="{{ old('promo_code') }}"
              >
              <button type="button" class="btn btn-outline-secondary" id="btn-apply-promo">
                Apply
              </button>
            </div>
            <div class="form-text" id="promo_help"></div>
            @error('promo_code')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Guardar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </form>
  </div>
</div>

{{-- Reabrir este modal si el error viene de aquí --}}
@if (session('openModal') === 'register' || (old('_modal') === 'register' && $errors->any()))
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const m = document.getElementById('modalRegistrar');
      if (m) new bootstrap.Modal(m).show();
    });
  </script>
@endif

{{-- Reset al abrir + fecha mínima + toggle otro hotel + horarios dinámicos + promo + spinner + meeting point --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
  const regModal = document.getElementById('modalRegistrar');
  if (!regModal) return;

  const form      = regModal.querySelector('#createBookingForm');
  const btnSubmit = form?.querySelector('button[type="submit"]');

  // Inputs
  const dateInput   = form?.querySelector('input[name="tour_date"]');
  const userSel     = form?.querySelector('select[name="user_id"]');
  const langSel     = form?.querySelector('select[name="tour_language_id"]');
  const statusSel   = form?.querySelector('select[name="status"]');
  const adultsInp   = form?.querySelector('input[name="adults_quantity"]');
  const kidsInp     = form?.querySelector('input[name="kids_quantity"]');

  const hotelSel    = form?.querySelector('#hotel_id');
  const wrapOther   = form?.querySelector('#other_hotel_wrapper');
  const hiddenOther = form?.querySelector('#is_other_hotel');
  const otherInput  = form?.querySelector('#other_hotel_name');

  const tourSel     = form?.querySelector('#selectTour');
  const schedSel    = form?.querySelector('#selectSchedule');

  const promoInp    = form?.querySelector('#promo_code');
  const promoHelp   = form?.querySelector('#promo_help');
  const btnApply    = form?.querySelector('#btn-apply-promo');

  // Meeting point
  const mpSel   = form?.querySelector('#meetingPointSelect');
  const mpHelp  = form?.querySelector('#meetingPointHelp');

  let promoValid = false;

  // No resetea si venimos de errores del servidor para este mismo modal
  const shouldPreserve = {!! (session('openModal') === 'register' || (old('_modal') === 'register' && $errors->any())) ? 'true' : 'false' !!};

  // ---------- RESET AUTOMÁTICO AL ABRIR ----------
  regModal.addEventListener('show.bs.modal', () => {
    if (shouldPreserve) return;

    // Reset nativo del form
    form?.reset();

    // Valores por defecto
    if (userSel)   userSel.value   = '';
    if (langSel)   langSel.value   = '';
    if (statusSel) statusSel.value = 'pending';

    if (tourSel)  tourSel.value  = '';
    if (schedSel) schedSel.innerHTML = '<option value="">Seleccione un horario</option>';

    if (hotelSel) hotelSel.value = '';
    if (wrapOther)   wrapOther.classList.add('d-none');
    if (hiddenOther) hiddenOther.value = '0';
    if (otherInput)  otherInput.value  = '';

    if (adultsInp) adultsInp.value = 1;
    if (kidsInp)   kidsInp.value   = 0;

    if (promoInp)  promoInp.value  = '';
    if (promoHelp) {
      promoHelp.textContent = '';
      promoHelp.classList.remove('text-success', 'text-danger');
    }
    promoValid = false;

    if (mpSel) {
      mpSel.value = '';
      updateMpHelp();
    }
  });

  // ---------- FECHA MÍNIMA ----------
  if (dateInput) {
    dateInput.setAttribute('min', @json($today));
    dateInput.addEventListener('change', () => {
      if (!dateInput.value) return;
      const picked = new Date(dateInput.value + 'T00:00:00');
      const today  = new Date(@json($today) + 'T00:00:00');
      if (picked < today) dateInput.value = @json($today);
    });
  }

  // ---------- OTRO HOTEL ----------
  const toggleOther = () => {
    if (!hotelSel || !wrapOther || !hiddenOther) return;
    const isOther = hotelSel.value === 'other';
    wrapOther.classList.toggle('d-none', !isOther);
    hiddenOther.value = isOther ? 1 : 0;
    if (!isOther && otherInput) otherInput.value = '';
  };
  toggleOther();
  hotelSel?.addEventListener('change', toggleOther);

  // ---------- HORARIOS POR TOUR ----------
  const rebuildSchedules = () => {
    if (!tourSel || !schedSel) return;
    const opt  = tourSel.options[tourSel.selectedIndex];
    const json = opt ? opt.getAttribute('data-schedules') : '[]';
    let list = [];
    try { list = JSON.parse(json || '[]'); } catch(e) {}

    schedSel.innerHTML = '<option value="">Seleccione un horario</option>';
    list.forEach(s => {
      const o = document.createElement('option');
      o.value = s.schedule_id;
      o.textContent = `${s.start_time} – ${s.end_time}`;
      schedSel.appendChild(o);
    });

    // Si el usuario está corrigiendo tras error y había elegido un horario:
    @if(old('schedule_id'))
      schedSel.value = @json(old('schedule_id'));
    @endif
  };
  rebuildSchedules();
  tourSel?.addEventListener('change', rebuildSchedules);

  // ---------- MEETING POINT: ayuda dinámica ----------
  function updateMpHelp() {
    if (!mpSel || !mpHelp) return;
    const opt = mpSel.options[mpSel.selectedIndex];
    if (!opt || !opt.value) {
      mpHelp.innerHTML = '';
      return;
    }
    const time = opt.getAttribute('data-time') || '';
    const addr = opt.getAttribute('data-description') || '';
    const map  = opt.getAttribute('data-map') || '';
    let html = '';
    if (time) html += `<div><i class="far fa-clock me-1"></i><strong>Hora:</strong> ${time}</div>`;
    if (addr) html += `<div><i class="fas fa-map-pin me-1"></i>${addr}</div>`;
    if (map)  html += `<div><a href="${map}" target="_blank" rel="noopener"><i class="fas fa-external-link-alt me-1"></i>Ver mapa</a></div>`;
    mpHelp.innerHTML = html;
  }
  mpSel?.addEventListener('change', updateMpHelp);
  updateMpHelp(); // inicial (respeta old('meeting_point_id'))

  // ---------- SPINNER ----------
  const showSpinner = (text) => {
    if (!btnSubmit) return;
    btnSubmit.disabled = true;
    btnSubmit.dataset.originalText = btnSubmit.dataset.originalText || btnSubmit.innerHTML;
    btnSubmit.innerHTML = `<i class="fas fa-spinner fa-spin me-1"></i> ${text}`;
  };
  const restoreBtn = () => {
    if (!btnSubmit) return;
    btnSubmit.disabled = false;
    if (btnSubmit.dataset.originalText) btnSubmit.innerHTML = btnSubmit.dataset.originalText;
  };

  // ---------- PROMO ----------
  function setPromoState({valid, message}) {
    promoValid = valid;
    if (promoHelp) {
      promoHelp.textContent = message || '';
      promoHelp.classList.toggle('text-success', !!valid);
      promoHelp.classList.toggle('text-danger', !valid);
    }
  }

  async function validatePromo(code) {
    try {
      const res = await fetch('{{ route('api.promo.apply') }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ code })
      });
      if (!res.ok) throw new Error('HTTP ' + res.status);
      const data = await res.json();

      if (data.valid) {
        const msg = data.message || (data.discount_percent
          ? `Code applied: -${data.discount_percent}%`
          : data.discount_amount
            ? `Code applied: -$${Number(data.discount_amount).toFixed(2)}`
            : 'Code applied');
        setPromoState({ valid: true, message: msg });
        if (promoInp) promoInp.value = (code || '').toUpperCase().replace(/\s+/g,'');
        btnApply?.setAttribute('disabled', 'disabled');
      } else {
        setPromoState({ valid: false, message: data.message || 'Invalid promo code.' });
      }
    } catch (err) {
      setPromoState({ valid: false, message: 'Could not validate the code.' });
      console.error(err);
    }
  }

  btnApply?.addEventListener('click', async () => {
    const code = (promoInp?.value || '').trim();
    if (!code) {
      setPromoState({ valid: false, message: 'Enter a promo code first.' });
      return;
    }
    setPromoState({ valid: false, message: 'Checking code…' });
    await validatePromo(code);
  });

  promoInp?.addEventListener('input', () => {
    setPromoState({ valid: false, message: '' });
    btnApply?.removeAttribute('disabled');
  });

  // ---------- SUBMIT ÚNICO ----------
  form?.addEventListener('submit', async (e) => {
    // Fecha no pasada
    if (dateInput && dateInput.value) {
      const picked = new Date(dateInput.value + 'T00:00:00');
      const today  = new Date(@json($today) + 'T00:00:00');
      if (picked < today) {
        e.preventDefault();
        restoreBtn();
        if (window.Swal) {
          Swal.fire({ icon: 'error', title: 'Error', text: 'No puedes reservar para fechas anteriores a hoy.', confirmButtonColor: '#dc3545' });
        } else {
          alert('No puedes reservar para fechas anteriores a hoy.');
        }
        dateInput.focus();
        return false;
      }
    }

    // Promo
    const code = promoInp?.value?.trim();
    if (!code) {
      showSpinner('Guardando...');
      return;
    }

    if (!promoValid) {
      e.preventDefault();
      showSpinner('Validando…');
      await validatePromo(code);
      if (promoValid) {
        showSpinner('Guardando...');
        form.submit(); // no re-dispara el listener
      } else {
        restoreBtn();
      }
    } else {
      showSpinner('Guardando...');
    }
  });
});
</script>
