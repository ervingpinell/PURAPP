@php
  // Hoy según la zona horaria de la app
  $tz = config('app.timezone', 'America/Costa_Rica');
  $today = \Carbon\Carbon::today($tz)->toDateString();
@endphp

@once
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endonce

<div class="modal fade" id="modalRegistrar" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="createBookingForm" action="{{ route('admin.reservas.store') }}" method="POST" novalidate>
      @csrf

      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Registrar Reserva</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <!-- Requeridos por el controller -->
          <input type="hidden" name="booking_date" value="{{ now()->toDateString() }}">
          <input type="hidden" name="is_other_hotel" id="is_other_hotel" value="0">

          {{-- Form principal (user_id, tour_id, tour_date, schedule_id, status, pax, hotel_id, other_hotel_name, tour_language_id, etc.) --}}
          @include('admin.bookings.partials.form', ['modo' => 'crear'])

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

{{-- Reabrir el modal si hubo errores de validación --}}
@if ($errors->any())
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      new bootstrap.Modal(document.getElementById('modalRegistrar')).show();
    });
  </script>
@endif

{{-- Toggle "Other hotel" si el partial define #hotel_id, #other_hotel_wrapper, #other_hotel_name --}}
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const sel   = document.getElementById('hotel_id');
    const wrap  = document.getElementById('other_hotel_wrapper');
    const hid   = document.getElementById('is_other_hotel');
    const input = document.getElementById('other_hotel_name');
    if (!sel || !wrap || !hid) return;

    const toggle = () => {
      const isOther = sel.value === 'other';
      wrap.classList.toggle('d-none', !isOther);
      hid.value = isOther ? 1 : 0;
      if (!isOther && input) input.value = '';
    };

    toggle();
    sel.addEventListener('change', toggle);
  });
</script>

{{-- Validación de fecha (no días pasados) + Promo code + Spinner en un único submit --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
  const form      = document.getElementById('createBookingForm');
  const btnSubmit = form?.querySelector('button[type="submit"]');
  const promoInp  = document.getElementById('promo_code');
  const promoHelp = document.getElementById('promo_help');
  const btnApply  = document.getElementById('btn-apply-promo');

  // localizar el input de fecha del partial
  const dateInput = form?.querySelector('input[name="tour_date"]');

  // ====== FECHA: bloquear días pasados (sin tocar el partial) ======
  if (dateInput) {
    // setear min="YYYY-MM-DD"
    dateInput.setAttribute('min', {{ json_encode($today) }});

    // si escriben manualmente una fecha pasada, corrige a hoy
    dateInput.addEventListener('change', () => {
      if (!dateInput.value) return;
      const picked = new Date(dateInput.value); picked.setHours(0,0,0,0);
      const today  = new Date({{ json_encode($today) }}); today.setHours(0,0,0,0);
      if (picked < today) dateInput.value = {{ json_encode($today) }};
    });
  }

  // ====== Helpers de botón ======
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

  // ====== Promo code ======
  let promoValid = false;

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

  // Botón "Apply"
  btnApply?.addEventListener('click', async () => {
    const code = (promoInp?.value || '').trim();
    if (!code) {
      setPromoState({ valid: false, message: 'Enter a promo code first.' });
      return;
    }
    setPromoState({ valid: false, message: 'Checking code…' });
    await validatePromo(code);
  });

  // Si editan el campo, se invalida el estado
  promoInp?.addEventListener('input', () => {
    setPromoState({ valid: false, message: '' });
    btnApply?.removeAttribute('disabled');
  });

  // ====== ÚNICO manejador de submit (primero fecha, luego promo) ======
  form?.addEventListener('submit', async (e) => {
    // 1) Validación de fecha pasada ANTES de cualquier cosa
    if (dateInput && dateInput.value) {
      const picked = new Date(dateInput.value); picked.setHours(0,0,0,0);
      const today  = new Date({{ json_encode($today) }}); today.setHours(0,0,0,0);
      if (picked < today) {
        e.preventDefault();
        restoreBtn();
        if (window.Swal) {
          Swal.fire({
            icon: 'error',
            title: @json(__('adminlte::adminlte.error') ?? 'Error'),
            text:  @json(__('adminlte::adminlte.cannot_book_past_dates') ?? 'No puedes reservar para fechas anteriores a hoy.'),
            confirmButtonColor: '#dc3545'
          });
        } else {
          alert(@json(__('adminlte::adminlte.cannot_book_past_dates') ?? 'No puedes reservar para fechas anteriores a hoy.'));
        }
        dateInput.focus();
        return false;
      }
    }

    // 2) Promo + spinner como de costumbre
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
