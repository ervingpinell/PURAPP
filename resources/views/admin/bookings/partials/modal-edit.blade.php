{{-- resources/views/admin/bookings/partials/modal-edit.blade.php --}}

@once
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endonce

@php
  /** @var \App\Models\Booking $booking */
  $detail  = $booking->detail;
  $reopen  = (session('showEditModal') == $booking->booking_id)
          || (old('_modal') === 'edit:'.$booking->booking_id && $errors->any());

  $promo   = $booking->promoCode; // actual
  $hasPromo= (bool)$promo;
  $isAdd   = $promo && $promo->operation === 'add';
  $promoP  = $promo?->discount_percent;
  $promoA  = $promo?->discount_amount;

  // Label seguro (sin claves raras)
  $promoLabel = \Illuminate\Support\Facades\Lang::has('m_bookings.bookings.ui.promo_code')
    ? __('m_bookings.bookings.ui.promo_code')
    : 'Código promocional';

  // Para depuración “ver detalles”
  $errArray = $errors->any() ? $errors->getMessages() : [];
@endphp

<div class="modal fade" id="modalEdit{{ $booking->booking_id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <form id="editBookingForm-{{ $booking->booking_id }}"
          action="{{ route('admin.bookings.update', $booking->booking_id) }}"
          method="POST"
          novalidate
          class="needs-validation js-edit-booking-form"
          data-booking-id="{{ $booking->booking_id }}">
      @csrf
      @method('PUT')
      <input type="hidden" name="_modal" value="edit:{{ $booking->booking_id }}">

      {{-- booking_date (oculto) --}}
      <input type="hidden" name="booking_date"
             value="{{ old('booking_date', optional($booking->booking_date)->format('Y-m-d') ?? now()->toDateString()) }}">

      {{-- user bloqueado --}}
      <input type="hidden" name="user_id" value="{{ $booking->user_id }}">

      {{-- señales de cupón --}}
      <input type="hidden" name="promo_action" id="promoAction-{{ $booking->booking_id }}" value="keep">
      <input type="hidden" name="promo_code"   id="promoHidden-{{ $booking->booking_id }}" value="">

      <div class="modal-content">
        <div class="modal-header bg-dark text-white">
          <h5 class="modal-title">
            <i class="fas fa-edit me-2"></i>
            {{ __('m_bookings.bookings.ui.edit_booking') }}
            <span class="text-muted">#{{ $booking->booking_id }}</span>
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">

          {{-- Errores/top alert --}}
          @if ($reopen && (session('error') || $errors->any()))
            <div class="alert alert-danger mb-3">
              <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>{{ session('error') ?? __('m_bookings.bookings.errors.update') }}</strong>
              </div>

              @if ($errors->any())
                <ul class="mt-2 mb-0">
                  @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                  @endforeach
                </ul>
              @endif

              {{-- detalles técnicos colapsables --}}
              <details class="mt-2">
                <summary class="small text-light">Ver detalles técnicos</summary>
                <pre class="bg-dark text-white rounded p-2 mt-2" style="white-space:pre-wrap">{{ json_encode($errArray, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
              </details>
            </div>
          @endif

          {{-- === Form principal (todo menos el cupón) === --}}
          @include('admin.bookings.partials.form-edit', [
            'booking' => $booking,
            'detail'  => $detail,
          ])

          {{-- === Cupón (MOVIDO AL FINAL) === --}}
          <div class="border rounded p-3 mt-4">
            <div class="d-flex flex-column flex-md-row align-items-md-end gap-2">
              <div class="flex-grow-1">
                <label for="promoInput-{{ $booking->booking_id }}" class="form-label mb-1">
                  {{ $promoLabel }}
                </label>
                <input type="text"
                       class="form-control"
                       id="promoInput-{{ $booking->booking_id }}"
                       placeholder="Escribe o pega el cupón"
                       value="{{ $promo?->code ?? '' }}">
              </div>
              <div class="d-flex gap-2">
                {{-- Solo Verificar y Quitar; el “Aplicar” ya no es necesario --}}
                <button type="button" class="btn btn-outline-secondary" id="promoVerifyBtn-{{ $booking->booking_id }}">
                  Verificar
                </button>
                <button type="button" class="btn btn-danger" id="promoRemoveBtn-{{ $booking->booking_id }}" {{ $hasPromo ? '' : 'disabled' }}>
                  Quitar
                </button>
              </div>
            </div>

            {{-- resumen + feedback --}}
            <div class="d-flex align-items-center mt-2">
              <div id="promoSummary-{{ $booking->booking_id }}" class="small me-auto">
                @if ($hasPromo)
                  <span class="badge bg-info text-dark">
                    <i class="fas fa-ticket-alt me-1"></i>{{ $promo->code }}
                  </span>
                  <span class="badge {{ $isAdd ? 'bg-success' : 'bg-danger' }} ms-1">
                    {{ $isAdd ? __('m_config.promocode.operation.add') : __('m_config.promocode.operation.subtract') }}
                  </span>
                  @if (!is_null($promoP))
                    <span class="badge bg-secondary ms-1">{{ number_format($promoP,0) }}%</span>
                  @elseif (!is_null($promoA))
                    <span class="badge bg-secondary ms-1">${{ number_format($promoA,2) }}</span>
                  @endif
                @else
                  <span class="text-muted">{{ \Illuminate\Support\Facades\Lang::has('m_bookings.bookings.ui.no_promo_code') ? __('m_bookings.bookings.ui.no_promo_code') : 'Sin cupón aplicado' }}</span>
                @endif
              </div>
              <div id="promoFeedback-{{ $booking->booking_id }}" class="small"></div>
            </div>

            <p class="text-muted small mb-0 mt-2">
              * Tras verificar, el cupón se aplicará al guardar la reserva.
            </p>
          </div>
          {{-- === /Cupón === --}}

        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">
            <i class="fas fa-save me-1"></i>{{ __('m_bookings.bookings.buttons.update') }}
          </button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i>{{ __('m_bookings.bookings.buttons.cancel') }}
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

@if ($reopen)
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const el = document.getElementById('modalEdit{{ $booking->booking_id }}');
      if (el) bootstrap.Modal.getOrCreateInstance(el).show();
    });
  </script>
@endif

{{-- Lógica del cupón (verificar / quitar). El aplicar ocurre al guardar. --}}
<script>
(function(){
  const id   = {{ (int)$booking->booking_id }};
  const $inp = document.getElementById('promoInput-'+id);
  const $v   = document.getElementById('promoVerifyBtn-'+id);
  const $r   = document.getElementById('promoRemoveBtn-'+id);
  const $fb  = document.getElementById('promoFeedback-'+id);
  const $sum = document.getElementById('promoSummary-'+id);
  const $act = document.getElementById('promoAction-'+id);
  const $hid = document.getElementById('promoHidden-'+id);

  const VERIFY_URL = @json(route('admin.bookings.verify-promo'));

  const ok  = (t)=>{ $fb.className='small text-success'; $fb.textContent=t||''; };
  const err = (t)=>{ $fb.className='small text-danger';  $fb.textContent=t||''; };
  const clr = ()=>{ $fb.className='small'; $fb.textContent=''; };

  @if ($errors->any())
    console.group('Booking edit — validation errors (#{{ $booking->booking_id }})');
    console.log(@json($errArray));
    console.groupEnd();
  @endif
  @if (session('error'))
    console.warn('Booking edit — session error:', @json(session('error')));
  @endif

  // habilita “Quitar” si ya hay código
  if (($inp.value||'').trim().length) { $r.disabled=false; }

  $v?.addEventListener('click', async () => {
    clr();
    const code = ($inp.value||'').trim();
    if (!code) { err('Ingresa un cupón.'); return; }

    try {
      const url = new URL(VERIFY_URL, window.location.origin);
      url.searchParams.set('code', code);

      const res  = await fetch(url.toString(), { headers: { 'Accept':'application/json' }});
      const data = await res.json();

      if (!data?.valid) {
        err(data?.message || 'Cupón inválido o no disponible.');
        $act.value='keep'; $hid.value='';
        return;
      }

      // Visual: chips
      let chips = [];
      chips.push(`<span class="badge bg-info text-dark"><i class="fas fa-ticket-alt me-1"></i>${data.code||code}</span>`);
      chips.push(`<span class="badge ${data.operation==='add'?'bg-success':'bg-danger'} ms-1">${data.operation==='add' ? 'Suma' : 'Resta'}</span>`);
      if (data.discount_percent) chips.push(`<span class="badge bg-secondary ms-1">${Number(data.discount_percent).toFixed(0)}%</span>`);
      if (data.discount_amount)  chips.push(`<span class="badge bg-secondary ms-1">$${Number(data.discount_amount).toFixed(2)}</span>`);
      $sum.innerHTML = chips.join(' ');

      ok('Cupón verificado. Se aplicará al guardar.');
      $act.value  = 'apply';
      $hid.value  = data.code || code;
      $r.disabled = false;

      if (window.Swal) Swal.fire({icon:'success',title:'Cupón verificado',timer:1200,showConfirmButton:false});

    } catch(e){
      console.error('verifyPromo error:', e);
      err('No se pudo verificar el cupón.');
      $act.value='keep'; $hid.value='';
      if (window.Swal) Swal.fire({icon:'error',title:'Error al verificar',text:String(e).slice(0,200),timer:1600,showConfirmButton:false});
    }
  });

  $r?.addEventListener('click', () => {
    $act.value='remove'; $hid.value='';
    ok('Se quitará al guardar.');
    $sum.innerHTML = `<span class="text-muted">{{ \Illuminate\Support\Facades\Lang::has('m_bookings.bookings.ui.no_promo_code') ? __('m_bookings.bookings.ui.no_promo_code') : 'Sin cupón aplicado' }}</span>`;
    if (window.Swal) Swal.fire({icon:'info',title:'Se quitará el cupón al guardar',timer:1300,showConfirmButton:false});
  });
})();
</script>

@push('css')
<style>
  .modal-xl .modal-body { max-height: 70vh; overflow-y: auto; }
</style>
@endpush
