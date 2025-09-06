@extends('layouts.app')

@section('title', __('adminlte::adminlte.myCart'))

@section('content')
@php
  // --- Fallback Meeting Points si el controller no los envía ---
  $meetingPoints = $meetingPoints
      ?? \App\Models\MeetingPoint::where('is_active', true)
          ->orderByRaw('sort_order IS NULL, sort_order ASC')
          ->orderBy('name', 'asc')
          ->get();

  // JSON preconstruido para evitar @json de arrow functions en atributos
  $mpListJson = ($meetingPoints ?? collect())
      ->map(function($mp){
          return [
              'id'          => $mp->id,
              'name'        => $mp->name,
              'pickup_time' => $mp->pickup_time,
              'address'     => $mp->address,
              'map_url'     => $mp->map_url,
          ];
      })
      ->values()
      ->toJson(); // string JSON

  // Etiqueta para "Pick-up" (se usará desde JS vía data-attribute)
  $pickupLabel = __('adminlte::adminlte.pickupTime') ?? 'Pick-up';

  // Mostrar columnas condicionales
  $showHotelColumn = ($cart && $cart->items)
      ? $cart->items->contains(function($it){
          return $it->hotel || $it->is_other_hotel || $it->other_hotel_name;
        })
      : false;

  $showMeetingPointColumn = ($cart && $cart->items)
      ? $cart->items->contains(function($it){
          return !$it->hotel && !$it->is_other_hotel && ($it->meeting_point_id || $it->meeting_point_name);
        })
      : false;
@endphp

<div class="container py-5 mb-5" id="mp-config" data-pickup-label="{{ $pickupLabel }}">

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
            @if($showHotelColumn)
              <th>{{ __('adminlte::adminlte.hotel') }}</th>
            @endif
            @if($showMeetingPointColumn)
              <th>{{ __('adminlte::adminlte.meeting_point') ?? 'Meeting point' }}</th>
            @endif
            <th>{{ __('adminlte::adminlte.status') }}</th>
            <th>{{ __('adminlte::adminlte.actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($cart->items as $item)
            @php
              $itemSubtotal = ($item->tour->adult_price * $item->adults_quantity)
                            + ($item->tour->kid_price   * $item->kids_quantity);
            @endphp
            <tr
              class="text-center cart-item-row"
              data-item-id="{{ $item->item_id }}"
              data-subtotal="{{ number_format($itemSubtotal, 2, '.', '') }}"
            >
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

              @if($showHotelColumn)
                <td>
                  @if($item->is_other_hotel && $item->other_hotel_name)
                    {{ $item->other_hotel_name }} <small class="text-muted">(personalizado)</small>
                  @elseif($item->hotel)
                    {{ $item->hotel->name }}
                  @endif
                </td>
              @endif

              @if($showMeetingPointColumn)
                <td class="text-start">
                  @if(!$item->hotel && !$item->is_other_hotel && $item->meeting_point_name)
                    <div class="fw-semibold">{{ $item->meeting_point_name }}</div>
                    @if($item->meeting_point_pickup_time)
                      <div class="small text-muted">
                        {{ __('adminlte::adminlte.pickupTime') ?? 'Pick-up' }}:
                        {{ $item->meeting_point_pickup_time }}
                      </div>
                    @endif
                    @if($item->meeting_point_address)
                      <div class="small text-muted">
                        <i class="fas fa-map-marker-alt me-1"></i>{{ $item->meeting_point_address }}
                      </div>
                    @endif
                    @if($item->meeting_point_map_url)
                      <a href="{{ $item->meeting_point_map_url }}" target="_blank" class="small">
                        <i class="fas fa-external-link-alt me-1"></i>{{ __('adminlte::adminlte.openMap') ?? 'Abrir mapa' }}
                      </a>
                    @endif
                  @endif
                </td>
              @endif

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
        @php
          $itemSubtotal   = ($item->tour->adult_price * $item->adults_quantity)
                          + ($item->tour->kid_price   * $item->kids_quantity);
          $showHotelInCard = ($item->is_other_hotel && $item->other_hotel_name) || $item->hotel;
          $showMpInCard    = !$item->hotel && !$item->is_other_hotel && $item->meeting_point_name;
        @endphp
        <div class="card mb-3 shadow-sm cart-item-card"
             data-item-id="{{ $item->item_id }}"
             data-subtotal="{{ number_format($itemSubtotal, 2, '.', '') }}">
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

            @if($showHotelInCard)
              <div class="mb-3"><strong>{{ __('adminlte::adminlte.hotel') }}:</strong>
                @if($item->is_other_hotel && $item->other_hotel_name)
                  {{ $item->other_hotel_name }} <small class="text-muted">(personalizado)</small>
                @elseif($item->hotel)
                  {{ $item->hotel->name }}
                @endif
              </div>
            @endif

            @if($showMpInCard)
              <div class="mb-3"><strong>{{ __('adminlte::adminlte.meetingPoint') ?? 'Meeting point' }}:</strong>
                <div>{{ $item->meeting_point_name }}</div>
                @if($item->meeting_point_pickup_time)
                  <div class="small text-muted">
                    {{ __('adminlte::adminlte.pickupTime') ?? 'Pick-up' }}: {{ $item->meeting_point_pickup_time }}
                  </div>
                @endif
                @if($item->meeting_point_address)
                  <div class="small text-muted"><i class="fas fa-map-marker-alt me-1"></i>{{ $item->meeting_point_address }}</div>
                @endif
                @if($item->meeting_point_map_url)
                  <a href="{{ $item->meeting_point_map_url }}" class="small" target="_blank">
                    <i class="fas fa-external-link-alt me-1"></i>{{ __('adminlte::adminlte.openMap') ?? 'Abrir mapa' }}
                  </a>
                @endif
              </div>
            @endif

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
{{-- MODALES: EDICIÓN POR ITEM   --}}
{{-- ============================= --}}
@foreach(($cart->items ?? collect()) as $item)
  @php
    $currentScheduleId   = $item->schedule?->schedule_id ?? null;
    $currentTourLangId   = $item->tour_language_id ?? $item->language?->tour_language_id;
    $currentHotelId      = $item->hotel?->hotel_id ?? null;
    $currentMeetingPoint = $item->meeting_point_id ?? null;
    $schedules           = $item->tour->schedules ?? collect();
    $tourLangs           = $item->tour->languages ?? collect();

    // Pickup mode inicial
    $initPickup = $item->is_other_hotel ? 'custom' : ($item->hotel ? 'hotel' : ($item->meeting_point_id ? 'mp' : 'hotel'));
  @endphp
  <div class="modal fade" id="editItemModal-{{ $item->item_id }}" tabindex="-1" aria-labelledby="editItemLabel-{{ $item->item_id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-fullscreen-sm-down">
      <div class="modal-content">
        <form action="{{ route('public.cart.update', $item->item_id) }}" method="POST" class="edit-item-form">
          @csrf @method('PUT')

          {{-- Mantener activo al guardar --}}
          <input type="hidden" name="is_active" value="1" />
          <input type="hidden" name="is_other_hotel" id="is-other-hidden-{{ $item->item_id }}" value="{{ $item->is_other_hotel ? 1 : 0 }}">

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
                <input type="number" name="kids_quantity" class="form-control" min="0" max="12" value="{{ (int) $item->kids_quantity }}">
              </div>

              {{-- ====== PICKUP (Segmented) ====== --}}
              <div class="col-12">
                <label class="form-label fw-semibold d-flex align-items-center gap-2">
                  <i class="fas fa-bus"></i> {{ __('adminlte::adminlte.pickup') ?? 'Pickup' }}
                </label>

                {{-- Tabs / Segmented --}}
                <div class="btn-group w-100 mb-2 pickup-tabs" role="group" aria-label="Pickup options"
                     data-item="{{ $item->item_id }}" data-init="{{ $initPickup }}">
                  <button type="button" class="btn btn-outline-secondary flex-fill" data-pickup-tab="hotel">
                    <i class="fas fa-hotel me-1"></i>{{ __('adminlte::adminlte.hotel') ?? 'Hotel' }}
                  </button>
                  <button type="button" class="btn btn-outline-secondary flex-fill" data-pickup-tab="custom">
                    <i class="fas fa-pen me-1"></i>{{ __('adminlte::adminlte.otherHotel') ?? 'Otro hotel' }}
                  </button>
                  <button type="button" class="btn btn-outline-secondary flex-fill" data-pickup-tab="mp">
                    <i class="fas fa-map-marker-alt me-1"></i>{{ __('adminlte::adminlte.meetingPoint') ?? 'Meeting point' }}
                  </button>
                </div>

                {{-- Panes --}}
                <div id="pickup-panes-{{ $item->item_id }}">
                  {{-- Hotel --}}
                  <div class="pickup-pane" id="pane-hotel-{{ $item->item_id }}" style="display:none">
                    <select name="hotel_id" id="hotel-select-{{ $item->item_id }}" class="form-select">
                      <option value="">{{ __('adminlte::adminlte.selectOption') ?? 'Seleccione…' }}</option>
                      @foreach(($hotels ?? []) as $hotel)
                        <option value="{{ $hotel->hotel_id }}" @selected($currentHotelId == $hotel->hotel_id)>{{ $hotel->name }}</option>
                      @endforeach
                    </select>
                    <div class="form-text">{{ __('adminlte::adminlte.selectHotelHelp') ?? 'Elija su hotel de la lista.' }}</div>
                  </div>

                  {{-- Otro hotel --}}
                  <div class="pickup-pane" id="pane-custom-{{ $item->item_id }}" style="display:none">
                    <input type="text" name="other_hotel_name" id="custom-hotel-input-{{ $item->item_id }}" class="form-control" value="{{ $item->other_hotel_name }}" placeholder="{{ __('adminlte::adminlte.customHotelName') ?? 'Nombre del hotel' }}">
                    <div class="form-text">{{ __('adminlte::adminlte.customHotelHelp') ?? 'Escriba el nombre del hotel si no aparece en la lista.' }}</div>
                  </div>

                  {{-- Meeting point --}}
                  <div class="pickup-pane" id="pane-mp-{{ $item->item_id }}" style="display:none">
                    <select name="meeting_point_id"
                            class="form-select meetingpoint-select"
                            id="meetingpoint-select-{{ $item->item_id }}"
                            data-target="#mp-info-{{ $item->item_id }}"
                            data-mplist='{!! $mpListJson !!}'>
                      <option value="">{{ __('adminlte::adminlte.selectOption') ?? 'Seleccione…' }}</option>
                      @foreach($meetingPoints as $mp)
                        <option value="{{ $mp->id }}" @selected($currentMeetingPoint == $mp->id)>{{ $mp->name }}</option>
                      @endforeach
                    </select>

                    {{-- Info dinámica del MP seleccionado --}}
                    <div class="border rounded p-2 mt-2 bg-light small" id="mp-info-{{ $item->item_id }}" style="display:none">
                      <div class="mp-name fw-semibold"></div>
                      <div class="mp-time text-muted"></div>
                      <div class="mp-addr mt-1"></div>
                      <a class="mp-link mt-1 d-inline-block" href="#" target="_blank" style="display:none">
                        <i class="fas fa-external-link-alt me-1"></i>{{ __('adminlte::adminlte.openMap') ?? 'Abrir mapa' }}
                      </a>
                    </div>
                  </div>
                </div>
                {{-- /Panes --}}
              </div>
              {{-- ====== /PICKUP ====== --}}
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
/* Segmented buttons active state */
.pickup-tabs .btn.active{
  background-color:#0d6efd!important;
  border-color:#0d6efd!important;
  color:#fff!important;
}
</style>
@endpush

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

      // ====== UI dinámica de Meeting Point ======
      const pickupLabel = (document.getElementById('mp-config')?.dataset?.pickupLabel) || 'Pick-up';

      const updateMpInfo = (selectEl) => {
        if (!selectEl) return;
        let mplist = [];
        try {
          const raw = selectEl.getAttribute('data-mplist') || '[]';
          mplist = JSON.parse(raw);
        } catch (e) {
          mplist = [];
        }
        const targetSel = selectEl.getAttribute('data-target');
        const box = targetSel ? document.querySelector(targetSel) : null;
        if (!box) return;

        const id = selectEl.value ? Number(selectEl.value) : null;
        const found = id ? mplist.find(m => Number(m.id) === id) : null;

        const nameEl = box.querySelector('.mp-name');
        const timeEl = box.querySelector('.mp-time');
        const addrEl = box.querySelector('.mp-addr');
        const linkEl = box.querySelector('.mp-link');

        if (found) {
          box.style.display = 'block';
          if (nameEl) nameEl.textContent = found.name || '';
          if (timeEl) timeEl.textContent = found.pickup_time ? (pickupLabel + ': ' + found.pickup_time) : '';
          if (addrEl) addrEl.innerHTML = found.address ? ('<i class="fas fa-map-marker-alt me-1"></i>' + found.address) : '';
          if (linkEl) {
            if (found.map_url) {
              linkEl.href = found.map_url;
              linkEl.style.display = 'inline-block';
            } else {
              linkEl.style.display = 'none';
            }
          }
        } else {
          box.style.display = 'none';
        }
      };

      document.querySelectorAll('.meetingpoint-select').forEach(sel => {
        updateMpInfo(sel); // estado inicial
        sel.addEventListener('change', () => updateMpInfo(sel));
      });

      // Anti doble submit (modales)
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

      // ======= PROMO CODE =======
      const applyBtn    = document.getElementById('apply-promo');
      const codeInput   = document.getElementById('promo-code');
      const msgBox      = document.getElementById('promo-message');
      const totalEl     = document.getElementById('cart-total');
      const codeHidden  = document.getElementById('promo_code_hidden');

      const getUniqueItemElements = () => {
        const els = Array.from(document.querySelectorAll('.cart-item-row, .cart-item-card'));
        const seen = new Set();
        return els.filter(el => {
          const id = el.dataset.itemId || '';
          if (!id || seen.has(id)) return false;
          seen.add(id);
          return true;
        });
      };

      const getBaseCartTotal = () => {
        const els = getUniqueItemElements();
        let sum = 0;
        els.forEach(el => {
          const v = parseFloat(el.dataset.subtotal || '0');
          if (!isNaN(v)) sum += v;
        });
        return Math.round(sum * 100) / 100;
      };

      const renderOk = (data) => {
        if (Array.isArray(data.items_result)) {
          totalEl.textContent = Number(data.cart_new_total).toFixed(2);
          const applied = data.applied_items ?? 0;
          const discTot = data.cart_discount_total ?? 0;

          msgBox.classList.remove('text-danger');
          msgBox.classList.add('text-success');
          msgBox.innerHTML = `
            <i class="fas fa-check-circle me-1"></i>
            Cupón <strong>${data.code}</strong> aplicado a <strong>${applied}</strong> ítem(s).
            Descuento total: <strong>$${Number(discTot).toFixed(2)}</strong>.
            Nuevo total: <strong>$${Number(data.cart_new_total).toFixed(2)}</strong>.
          `;
          if (codeHidden) codeHidden.value = data.code;
        } else {
          const base = getBaseCartTotal();
          const newT = typeof data.new_total === 'number' ? data.new_total : base;
          totalEl.textContent = Number(newT).toFixed(2);

          msgBox.classList.remove('text-danger');
          msgBox.classList.add('text-success');
          msgBox.innerHTML = `
            <i class="fas fa-check-circle me-1"></i>
            Cupón <strong>${data.code}</strong> aplicado.
            Nuevo total: <strong>$${Number(newT).toFixed(2)}</strong>.
          `;
          if (codeHidden) codeHidden.value = data.code;
        }
      };

      const renderError = (message) => {
        msgBox.classList.remove('text-success');
        msgBox.classList.add('text-danger');
        msgBox.innerHTML = `<i class="fas fa-times-circle me-1"></i>${message}`;
        totalEl.textContent = getBaseCartTotal().toFixed(2);
        if (codeHidden) codeHidden.value = '';
      };

      if (applyBtn && codeInput && totalEl) {
        applyBtn.addEventListener('click', async () => {
          const code = (codeInput.value || '').trim();
          if (!code) return renderError('Ingrese un código.');

          const els   = getUniqueItemElements();
          const items = els.map(el => ({ total: parseFloat(el.dataset.subtotal || '0') || 0 }));

          const payload = {
            code,
            preview: true,
            ...(items.length > 0 ? { items } : { total: getBaseCartTotal() }),
          };

          try {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const res = await fetch(@json(route('promo.apply')), {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
              },
              body: JSON.stringify(payload)
            });

            const data = await res.json();
            if (data.success && data.valid) {
              renderOk(data);
            } else {
              renderError(data.message || 'Código inválido o sin vigencia/usos.');
            }
          } catch (e) {
            renderError('No se pudo aplicar el cupón. Intente de nuevo.');
          }
        });
      }

      /* =========================================================
         PICKUP segmented logic (exclusividad y required)
         ========================================================= */
      function setPaneState(itemId, mode){
        const hiddenOther = document.getElementById(`is-other-hidden-${itemId}`);

        // Panes
        const paneHotel = document.getElementById(`pane-hotel-${itemId}`);
        const paneCustom = document.getElementById(`pane-custom-${itemId}`);
        const paneMp = document.getElementById(`pane-mp-${itemId}`);

        // Controles
        const hotelSelect = document.getElementById(`hotel-select-${itemId}`);
        const customInput = document.getElementById(`custom-hotel-input-${itemId}`);
        const mpSelect    = document.getElementById(`meetingpoint-select-${itemId}`);

        // Mostrar/ocultar
        paneHotel.style.display = (mode === 'hotel') ? 'block' : 'none';
        paneCustom.style.display = (mode === 'custom') ? 'block' : 'none';
        paneMp.style.display = (mode === 'mp') ? 'block' : 'none';

        // Required + disabled
        if (hotelSelect){
          hotelSelect.required = (mode === 'hotel');
          hotelSelect.disabled = !(mode === 'hotel');
          if (mode !== 'hotel') hotelSelect.value = '';
        }
        if (customInput){
          customInput.required = (mode === 'custom');
          customInput.disabled = !(mode === 'custom');
          if (mode !== 'custom') customInput.value = '';
        }
        if (mpSelect){
          mpSelect.required = (mode === 'mp');
          mpSelect.disabled = !(mode === 'mp');
          if (mode !== 'mp'){ mpSelect.value = ''; updateMpInfo(mpSelect); }
        }

        if (hiddenOther) hiddenOther.value = (mode === 'custom') ? 1 : 0;

        // Botones activos
        document.querySelectorAll(`.pickup-tabs[data-item="${itemId}"] .btn`).forEach(b => {
          b.classList.toggle('active', b.getAttribute('data-pickup-tab') === mode);
        });
      }

      document.querySelectorAll('.pickup-tabs').forEach(group => {
        const itemId = group.getAttribute('data-item');
        const init   = group.getAttribute('data-init') || 'hotel';

        // Estado inicial
        setPaneState(itemId, init);

        group.querySelectorAll('[data-pickup-tab]').forEach(btn => {
          btn.addEventListener('click', () => {
            const mode = btn.getAttribute('data-pickup-tab');
            setPaneState(itemId, mode);
          });
        });
      });

      /* =========================================================
         LOCALIZAR MENSAJES DE VALIDACIÓN (globos amarillos)
         ========================================================= */
      const V = {
        required:        @json(__('adminlte::adminlte.fillThisField')   ?? 'Please fill out this field.'),
        selectFromList:  @json(__('adminlte::adminlte.selectFromList')  ?? 'Select an item from the list.'),
        emailInvalid:    @json(__('adminlte::adminlte.emailInvalid')     ?? 'Enter a valid email address.'),
        tooShort:        @json(__('adminlte::adminlte.tooShort')         ?? 'Value is too short.'),
        tooLong:         @json(__('adminlte::adminlte.tooLong')          ?? 'Value is too long.'),
        rangeUnderflow:  @json(__('adminlte::adminlte.rangeUnderflow')   ?? 'Value is too small.'),
        rangeOverflow:   @json(__('adminlte::adminlte.rangeOverflow')    ?? 'Value is too large.'),
        stepMismatch:    @json(__('adminlte::adminlte.stepMismatch')     ?? 'Please match the requested step.'),
        patternMismatch: @json(__('adminlte::adminlte.patternMismatch')  ?? 'Please match the requested format.')
      };

      function localizeValidity(el){
        el.setCustomValidity('');
        const v = el.validity;
        if (v.valueMissing) {
          el.setCustomValidity(el.tagName === 'SELECT' ? V.selectFromList : V.required);
        } else if (v.typeMismatch && el.type === 'email') {
          el.setCustomValidity(V.emailInvalid);
        } else if (v.tooShort) {
          el.setCustomValidity(V.tooShort);
        } else if (v.tooLong) {
          el.setCustomValidity(V.tooLong);
        } else if (v.rangeUnderflow) {
          el.setCustomValidity(V.rangeUnderflow);
        } else if (v.rangeOverflow) {
          el.setCustomValidity(V.rangeOverflow);
        } else if (v.stepMismatch) {
          el.setCustomValidity(V.stepMismatch);
        } else if (v.patternMismatch) {
          el.setCustomValidity(V.patternMismatch);
        }
      }

      document.querySelectorAll('.edit-item-form').forEach(form => {
        // form.setAttribute('novalidate','novalidate'); // <- si quieres desactivar totalmente los nativos
        form.addEventListener('submit', (e) => {
          if (!form.checkValidity()) {
            e.preventDefault();
            const firstInvalid = form.querySelector(':invalid');
            if (firstInvalid) {
              localizeValidity(firstInvalid);
              firstInvalid.reportValidity();
              firstInvalid.focus({ preventScroll: true });
            }
          }
        });
        form.querySelectorAll('input, select, textarea').forEach(el => {
          el.addEventListener('invalid', () => localizeValidity(el));
          el.addEventListener('input',  () => el.setCustomValidity(''));
          el.addEventListener('change', () => el.setCustomValidity(''));
        });
      });
    });
  </script>
@endpush
