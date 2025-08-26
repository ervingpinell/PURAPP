@extends('layouts.app')

@section('title', __('adminlte::adminlte.myCart'))

@section('content')
<div class="container py-5 mb-5">

  <h1 class="mb-4 d-flex align-items-center">
    <i class="fas fa-shopping-cart me-2"></i>
    {{ __('adminlte::adminlte.myCart') }}
  </h1>

  {{-- Errores de validación --}}
  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- Toasts SweetAlert --}}
  @if (session('success') || session('error'))
    @once
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endonce
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        @if (session('success'))
          Swal.fire({
            icon: 'success',
            title: @json(__('adminlte::adminlte.success') ?? 'Success'),
            text:  @json(session('success')),
            confirmButtonColor: '#198754',
            allowOutsideClick: false
          });
        @endif
        @if (session('error'))
          Swal.fire({
            icon: 'error',
            title: @json(__('adminlte::adminlte.error') ?? 'Error'),
            text:  @json(session('error')),
            confirmButtonColor: '#dc3545',
            allowOutsideClick: false
          });
        @endif
      });
    </script>
  @endif

  @if($cart && $cart->items->count())

    {{-- Tabla (desktop) --}}
    <div class="table-responsive d-none d-md-block mb-4">
      <table class="table table-bordered table-striped table-hover align-middle">
        <thead>
          <tr class="text-center">
            <th>{{ __('adminlte::adminlte.tour') }}</th>
            <th>{{ __('adminlte::adminlte.date') }}</th>
            <th>{{ __('adminlte::adminlte.schedule') }}</th>
            <th>{{ __('adminlte::adminlte.language') }}</th>
            <th>{{ __('adminlte::adminlte.adults') }}</th>
            <th>{{ __('adminlte::adminlte.kids') }}</th>
            <th>{{ __('adminlte::adminlte.hotel') }}</th>
            <th>{{ __('adminlte::adminlte.status') }}</th>
            <th>{{ __('adminlte::adminlte.actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($cart->items as $item)
            <tr class="text-center">
              <td>{{ $item->tour->getTranslatedName() ?? $item->tour->name }}</td>
              <td>{{ \Carbon\Carbon::parse($item->tour_date)->format('d/m/Y') }}</td>
              <td>
                @if($item->schedule)
                  {{ \Carbon\Carbon::parse($item->schedule->start_time)->format('g:i A') }} -
                  {{ \Carbon\Carbon::parse($item->schedule->end_time)->format('g:i A') }}
                @else
                  {{ __('adminlte::adminlte.noSchedule') ?? 'Sin horario' }}
                @endif
              </td>
              <td>{{ $item->language?->name ?? __('adminlte::adminlte.notSpecified') ?? 'No indicado' }}</td>
              <td>{{ $item->adults_quantity }}</td>
              <td>{{ $item->kids_quantity }}</td>
              <td>
                @if($item->is_other_hotel && $item->other_hotel_name)
                  {{ $item->other_hotel_name }} <small class="text-muted">(personalizado)</small>
                @elseif($item->hotel)
                  {{ $item->hotel->name }}
                @else
                  <span class="text-muted">{{ __('adminlte::adminlte.notSpecified') ?? 'No indicado' }}</span>
                @endif
              </td>
              <td>
                <span class="badge {{ $item->is_active ? 'bg-success' : 'bg-secondary' }}">
                  {{ $item->is_active ? __('adminlte::adminlte.active') : __('adminlte::adminlte.inactive') }}
                </span>
              </td>
              <td class="text-nowrap">
                <button
                  type="button"
                  class="btn btn-sm btn-primary me-1"
                  data-bs-toggle="modal"
                  data-bs-target="#editItemModal-{{ $item->item_id }}">
                  <i class="fas fa-edit"></i> {{ __('adminlte::adminlte.edit') ?? 'Editar' }}
                </button>

                <form action="{{ route('public.cart.destroy', $item->item_id) }}" method="POST" class="d-inline delete-item-form">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-sm">
                    <i class="fas fa-trash"></i> {{ __('adminlte::adminlte.delete') }}
                  </button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {{-- Tarjetas (móvil) --}}
    <div class="d-block d-md-none">
      @foreach($cart->items as $item)
        <div class="card mb-3 shadow-sm">
          <div class="card-header text-center fw-semibold">
            {{ $item->tour->getTranslatedName() ?? $item->tour->name }}
          </div>
          <div class="card-body">
            <div class="mb-2"><strong>{{ __('adminlte::adminlte.date') }}:</strong> {{ \Carbon\Carbon::parse($item->tour_date)->format('d/m/Y') }}</div>
            <div class="mb-2"><strong>{{ __('adminlte::adminlte.schedule') }}:</strong>
              @if($item->schedule)
                {{ \Carbon\Carbon::parse($item->schedule->start_time)->format('g:i A') }} -
                {{ \Carbon\Carbon::parse($item->schedule->end_time)->format('g:i A') }}
              @else
                {{ __('adminlte::adminlte.noSchedule') ?? 'Sin horario' }}
              @endif
            </div>
            <div class="mb-2"><strong>{{ __('adminlte::adminlte.language') }}:</strong> {{ $item->language?->name ?? __('adminlte::adminlte.notSpecified') ?? 'No indicado' }}</div>
            <div class="mb-2"><strong>{{ __('adminlte::adminlte.adults') }}:</strong> {{ $item->adults_quantity }}</div>
            <div class="mb-2"><strong>{{ __('adminlte::adminlte.kids') }}:</strong> {{ $item->kids_quantity }}</div>
            <div class="mb-3"><strong>{{ __('adminlte::adminlte.hotel') }}:</strong>
              @if($item->is_other_hotel && $item->other_hotel_name)
                {{ $item->other_hotel_name }} <small class="text-muted">(personalizado)</small>
              @elseif($item->hotel)
                {{ $item->hotel->name }}
              @else
                <span class="text-muted">{{ __('adminlte::adminlte.notSpecified') ?? 'No indicado' }}</span>
              @endif
            </div>

            <div class="d-grid gap-2">
              <button
                type="button"
                class="btn btn-success"
                data-bs-toggle="modal"
                data-bs-target="#editItemModal-{{ $item->item_id }}">
                <i class="fas fa-edit"></i> {{ __('adminlte::adminlte.edit') ?? 'Editar' }}
              </button>

              <form action="{{ route('public.cart.destroy', $item->item_id) }}" method="POST" class="delete-item-form">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger">
                  <i class="fas fa-trash"></i> {{ __('adminlte::adminlte.delete') }}
                </button>
              </form>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    {{-- Total + Código Promocional --}}
    @php
      $total = $cart->items->sum(fn($it) =>
        ($it->tour->adult_price * $it->adults_quantity)
        + ($it->tour->kid_price * $it->kids_quantity)
      );
    @endphp

    <div class="card shadow-sm mb-4">
      <div class="card-body">
        <h4 class="mb-3">
          <strong>{{ __('adminlte::adminlte.totalEstimated') }}:</strong>
          $<span id="cart-total">{{ number_format($total, 2) }}</span>
        </h4>

        <label for="promo-code" class="form-label fw-semibold">{{ __('adminlte::adminlte.promoCode') }}</label>
        <div class="d-flex flex-column flex-sm-row gap-2">
          <input type="text" id="promo-code" name="promo_code" class="form-control"
                 placeholder="{{ __('adminlte::adminlte.promoCodePlaceholder') }}">
          <button type="button" id="apply-promo" class="btn btn-outline-primary">{{ __('adminlte::adminlte.apply') }}</button>
        </div>
        <div id="promo-message" class="mt-2 small text-success"></div>
      </div>
    </div>

    {{-- Confirmar Reserva --}}
    <form action="{{ route('public.reservas.storeFromCart') }}" method="POST" id="confirm-reserva-form">
      @csrf
      <input type="hidden" name="promo_code" id="promo_code_hidden" value="">
      <div class="d-grid">
        <button type="submit" class="btn btn-success btn-lg">
          <i class="fas fa-check"></i> {{ __('adminlte::adminlte.confirmBooking') }}
        </button>
      </div>
    </form>

  @else
    <div class="alert alert-info">
      <i class="fas fa-info-circle"></i> {{ __('adminlte::adminlte.emptyCart') }}
    </div>
  @endif
</div>

{{-- ============================= --}}
{{-- MODALES: FUERA DE BLOQUES d-none --}}
{{-- ============================= --}}
@foreach(($cart->items ?? collect()) as $item)
  <div class="modal fade" id="editItemModal-{{ $item->item_id }}" tabindex="-1" aria-labelledby="editItemLabel-{{ $item->item_id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-fullscreen-sm-down">
      <div class="modal-content">
        <form action="{{ route('public.cart.update', $item->item_id) }}" method="POST" class="edit-item-form">
          @csrf @method('PUT')

          <div class="modal-header">
            <h5 class="modal-title" id="editItemLabel-{{ $item->item_id }}">
              <i class="fas fa-pencil-alt me-2"></i>
              {{ __('adminlte::adminlte.editItem') ?? 'Editar reserva' }} — {{ $item->tour->getTranslatedName() ?? $item->tour->name }}
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('adminlte::adminlte.close') ?? 'Cerrar' }}"></button>
          </div>

          <div class="modal-body">
            <div class="row g-3">
              {{-- Fecha --}}
              <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">{{ __('adminlte::adminlte.date') }}</label>
                <input
                  type="date"
                  name="tour_date"
                  class="form-control"
                  value="{{ \Carbon\Carbon::parse($item->tour_date)->format('Y-m-d') }}"
                  min="{{ now()->format('Y-m-d') }}"
                  required>
              </div>

              {{-- Horario (nullable) --}}
              <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">{{ __('adminlte::adminlte.schedule') }}</label>
                @php
                  $schedules = $item->tour->schedules ?? collect();
                  $currentScheduleId = $item->schedule?->schedule_id ?? null;
                @endphp
                <select name="schedule_id" class="form-select">
                  <option value="">{{ __('adminlte::adminlte.selectOption') ?? 'Seleccione…' }}</option>
                  @foreach($schedules as $sch)
                    @php
                      $label = \Carbon\Carbon::parse($sch->start_time)->format('g:i A') . ' - ' . \Carbon\Carbon::parse($sch->end_time)->format('g:i A');
                    @endphp
                    <option value="{{ $sch->schedule_id }}" @selected($currentScheduleId == $sch->schedule_id)>{{ $label }}</option>
                  @endforeach
                </select>
                <div class="form-text">{{ __('adminlte::adminlte.scheduleHelp') ?? 'Si el tour no requiere horario, déjelo vacío.' }}</div>
              </div>

              {{-- Idioma --}}
              <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">{{ __('adminlte::adminlte.language') }}</label>
                @php
                  $tourLangs = $item->tour->languages ?? collect();
                  $currentTourLangId = $item->tour_language_id ?? $item->language?->tour_language_id;
                @endphp
                <select name="tour_language_id" class="form-select" required>
                  @forelse($tourLangs as $tl)
                    <option value="{{ $tl->tour_language_id }}" @selected($currentTourLangId == $tl->tour_language_id)>
                      {{ $tl->name ?? $tl->language->name ?? 'Idioma' }}
                    </option>
                  @empty
                    @if($item->language)
                      <option value="{{ $item->tour_language_id }}" selected>{{ $item->language->name }}</option>
                    @else
                      <option value="" selected>—</option>
                    @endif
                  @endforelse
                </select>
              </div>

              {{-- Cantidades --}}
              <div class="col-6 col-md-3">
                <label class="form-label fw-semibold">{{ __('adminlte::adminlte.adults') }}</label>
                <input type="number" name="adults_quantity" class="form-control" min="1" max="12" value="{{ (int) $item->adults_quantity }}" required>
              </div>
              <div class="col-6 col-md-3">
                <label class="form-label fw-semibold">{{ __('adminlte::adminlte.kids') }}</label>
                <input type="number" name="kids_quantity" class="form-control" min="0" max="2" value="{{ (int) $item->kids_quantity }}">
              </div>

              {{-- Hotel + Switch --}}
              <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">{{ __('adminlte::adminlte.hotel') }}</label>
                @php $currentHotelId = $item->hotel?->hotel_id ?? null; @endphp
                <select name="hotel_id" id="hotel-select-{{ $item->item_id }}" class="form-select">
                  <option value="">{{ __('adminlte::adminlte.selectOption') ?? 'Seleccione…' }}</option>
                  @foreach(($hotels ?? []) as $hotel)
                    <option value="{{ $hotel->hotel_id }}" @selected($currentHotelId == $hotel->hotel_id)>{{ $hotel->name }}</option>
                  @endforeach
                  <option value="__custom__" @selected($item->is_other_hotel)>{{ __('adminlte::adminlte.customHotel') ?? 'Hotel personalizado…' }}</option>
                </select>

                <div class="form-check form-switch mt-2">
                  <input class="form-check-input" type="checkbox" role="switch"
                        id="is-other-hotel-{{ $item->item_id }}"
                        name="is_other_hotel" value="1"
                        @checked($item->is_other_hotel)>
                  <label class="form-check-label" for="is-other-hotel-{{ $item->item_id }}">
                    {{ __('adminlte::adminlte.otherHotel') ?? 'Usar hotel personalizado' }}
                  </label>
                </div>
              </div>

              {{-- Nombre de "otro hotel" --}}
              <div class="col-12 col-md-6" id="custom-hotel-wrapper-{{ $item->item_id }}" style="display: {{ $item->is_other_hotel ? 'block' : 'none' }};">
                <label class="form-label fw-semibold">{{ __('adminlte::adminlte.customHotelName') ?? 'Nombre de hotel personalizado' }}</label>
                <input type="text" name="other_hotel_name" id="custom-hotel-input-{{ $item->item_id }}" class="form-control" value="{{ $item->other_hotel_name }}">
                <div class="form-text">{{ __('adminlte::adminlte.customHotelHelp') ?? 'Si escribe un hotel personalizado, se ignorará la selección de la lista.' }}</div>
                @error('other_hotel_name')
                  <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>

          <div class="modal-footer d-block d-sm-flex">
            <button type="button" class="btn btn-secondary w-100 w-sm-auto me-sm-2 mb-2 mb-sm-0" data-bs-dismiss="modal">
              <i class="fas fa-times"></i> {{ __('adminlte::adminlte.cancel') ?? 'Cancelar' }}
            </button>
            <button type="submit" class="btn btn-primary w-100 w-sm-auto">
              <i class="fas fa-save"></i> {{ __('adminlte::adminlte.update') ?? 'Actualizar' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endforeach
@endsection

@push('styles')
<style>
@media (max-width: 767.98px) {
  .modal-body { padding: 1rem; }
  .modal-header, .modal-footer { padding: .75rem 1rem; }
  .btn { min-height: 42px; }
  .card .card-title { font-size: 1.05rem; }
}
</style>
@endpush

@push('scripts')
  @vite('resources/js/cart/promo-code.js')
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  {{-- IMPORTANTE: No dupliques Bootstrap JS si AdminLTE ya lo carga.
       Usa BS5 con data-bs-* o BS4 con data-* según tu stack. --}}

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Confirmar reserva
      const reservaForm = document.getElementById('confirm-reserva-form');
      if(reservaForm){
        reservaForm.addEventListener('submit', function(e){
          e.preventDefault();
          Swal.fire({
            title: @json(__('adminlte::adminlte.confirmReservationTitle')),
            text: @json(__('adminlte::adminlte.confirmReservationText')),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#d33',
            confirmButtonText: @json(__('adminlte::adminlte.confirmReservationConfirm')),
            cancelButtonText: @json(__('adminlte::adminlte.confirmReservationCancel'))
          }).then((result) => { if(result.isConfirmed){ reservaForm.submit(); } });
        });
      }

      // Eliminar item
      document.querySelectorAll('.delete-item-form').forEach(form => {
        form.addEventListener('submit', function(e){
          e.preventDefault();
          Swal.fire({
            title: @json(__('adminlte::adminlte.deleteItemTitle')),
            text: @json(__('adminlte::adminlte.deleteItemText')),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: @json(__('adminlte::adminlte.deleteItemConfirm')),
            cancelButtonText: @json(__('adminlte::adminlte.deleteItemCancel'))
          }).then((result) => { if(result.isConfirmed){ form.submit(); } });
        });
      });

      // Toggle "otro hotel" por ítem (sobre los modales ya fuera del display:none)
      document.querySelectorAll('select[id^="hotel-select-"]').forEach(select => {
        const itemId = select.id.replace('hotel-select-', '');
        const wrapper = document.getElementById('custom-hotel-wrapper-' + itemId);
        const input   = document.getElementById('custom-hotel-input-' + itemId);
        const swOther = document.getElementById('is-other-hotel-' + itemId);

        const syncUI = () => {
          const bySelect = select.value === '__custom__';
          const bySwitch = swOther && swOther.checked;
          const show     = bySelect || bySwitch;
          if (wrapper) wrapper.style.display = show ? 'block' : 'none';
          if (!show && input) input.value = '';
        };

        syncUI();

        select.addEventListener('change', () => {
          if (select.value === '__custom__' && swOther && !swOther.checked) swOther.checked = true;
          if (select.value !== '__custom__' && swOther && swOther.checked)  swOther.checked = false;
          syncUI();
        });

        if (swOther) swOther.addEventListener('change', () => {
          if (swOther.checked && select.value !== '__custom__') select.value = '__custom__';
          if (!swOther.checked && select.value === '__custom__') select.value = '';
          syncUI();
        });
      });

      // Anti doble submit
      document.querySelectorAll('.edit-item-form').forEach(f => {
        f.addEventListener('submit', (e) => {
          const btn = f.querySelector('button[type="submit"]');
          if (btn) {
            btn.disabled = true;
            btn.innerHTML =
              '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>' +
              (btn.dataset.loadingText || @json(__('adminlte::adminlte.saving') ?? 'Guardando...'));
          }
        });
      });
    });
  </script>
@endpush
