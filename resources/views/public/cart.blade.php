@extends('layouts.app')

@section('title', __('adminlte::adminlte.myCart'))

@section('content')
@php
  // --- Fallback Meeting Points if controller didn't send them ---
  $meetingPoints = $meetingPoints
      ?? \App\Models\MeetingPoint::where('is_active', true)
          ->with('translations') // evitar N+1 y tener traducciones
          ->orderByRaw('sort_order IS NULL, sort_order ASC')
          ->orderBy('name', 'asc')
          ->get();

  // JSON para data-attributes (ya traducido)
  $mpListJson = ($meetingPoints ?? collect())
      ->map(fn($mp) => [
          'id'          => $mp->id,
          'name'        => $mp->getTranslated('name'),
          'pickup_time' => $mp->pickup_time,
          'description' => $mp->getTranslated('description'),
          'map_url'     => $mp->map_url,
      ])->values()->toJson();

  $pickupLabel = __('adminlte::adminlte.pickupTime');

  // Columnas condicionales
  $showHotelColumn = ($cart && $cart->items)
      ? $cart->items->contains(fn($it) => $it->hotel || $it->is_other_hotel || $it->other_hotel_name)
      : false;

  $showMeetingPointColumn = ($cart && $cart->items)
      ? $cart->items->contains(fn($it) => !$it->hotel && !$it->is_other_hotel && ($it->meeting_point_id))
      : false;

  // Timer config
  $expiryMinutes  = (int) config('cart.expiry_minutes', 15);
  $extendMinutes  = (int) config('cart.extend_minutes', 15);
  $extendMax      = (int) config('cart.max_extensions', 1);
$extendUsed = (int) ($cart->extended_count ?? 0);
  $isExtendDisabled = $extendUsed >= $extendMax;

  // Promo en sesión
  $promoSession = session('public_cart_promo');
@endphp

{{-- ========== TIMER ========== --}}
@if($cart && $cart->is_active && $cart->items->count() && !empty($expiresAtIso) && !$cart->isExpired())
  <div id="cart-timer"
       class="gv-timer shadow-sm"
       role="alert"
       data-expires-at="{{ $expiresAtIso }}"
       data-total-minutes="{{ $expiryMinutes }}"
       data-expire-endpoint="{{ route('public.carts.expire') }}"
       data-refresh-endpoint="{{ route('public.carts.refreshExpiry') }}"
       data-extend-max="{{ $extendMax }}"
       data-extend-used="{{ $extendUsed }}">
    <div class="gv-timer-head">
      <div class="gv-timer-icon">
        <i class="fas fa-hourglass-half"></i>
      </div>
      <div class="gv-timer-text">
        <div class="gv-timer-title">{{ __('carts.timer.will_expire') }}</div>
        <div class="gv-timer-sub">
          {{ __('carts.timer.time_left') }}
          <span id="cart-timer-remaining" class="gv-timer-remaining">--:--</span>
        </div>
      </div>

      <button id="cart-timer-refresh"
              class="btn btn-sm gv-timer-btn {{ $isExtendDisabled ? 'btn-secondary disabled' : 'btn-dark' }}"
              @disabled($isExtendDisabled)
              data-label-default="{{ trans_choice('carts.timer.extend', $extendMinutes, ['count' => $extendMinutes]) }}"
              data-label-disabled="{{ __('carts.timer.already_extended') }}">
        {{ $isExtendDisabled
            ? __('carts.timer.already_extended')
            : trans_choice('carts.timer.extend', $extendMinutes, ['count' => $extendMinutes])
        }}
      </button>
    </div>
    <div class="gv-timer-bar">
      <div class="gv-timer-bar-fill" id="cart-timer-bar" style="width:100%"></div>
    </div>
  </div>
@endif

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

  {{-- Toasts --}}
  @if (session('success') || session('error'))
    @once
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endonce
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        @if (session('success'))
          Swal.fire({
            icon: 'success',
            title: @json(__('adminlte::adminlte.success')),
            text:  @json(session('success')),
            confirmButtonColor: '#198754',
            allowOutsideClick: false
          });
        @endif
        @if (session('error'))
          Swal.fire({
            icon: 'error'),
            title: @json(__('adminlte::adminlte.error')),
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
              <th>{{ __('adminlte::adminlte.meeting_point') }}</th>
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
            <tr class="text-center cart-item-row"
                data-item-id="{{ $item->item_id }}"
                data-subtotal="{{ number_format($itemSubtotal, 2, '.', '') }}">
              <td>{{ $item->tour->getTranslatedName() ?? $item->tour->name }}</td>
              <td>{{ \Carbon\Carbon::parse($item->tour_date)->format('d/M/Y') }}</td>
              <td>
                @if($item->schedule)
                  {{ \Carbon\Carbon::parse($item->schedule->start_time)->format('g:i A') }} -
                  {{ \Carbon\Carbon::parse($item->schedule->end_time)->format('g:i A') }}
                @else
                  {{ __('adminlte::adminlte.noSchedule') }}
                @endif
              </td>
              <td>{{ $item->language?->name ?? __('adminlte::adminlte.notSpecified') }}</td>
              <td>{{ $item->adults_quantity }}</td>
              <td>{{ $item->kids_quantity }}</td>

              @if($showHotelColumn)
                <td>
                  @if($item->is_other_hotel && $item->other_hotel_name)
                    {{ $item->other_hotel_name }} <small class="text-muted">({{ __('adminlte::adminlte.custom') }})</small>
                  @elseif($item->hotel)
                    {{ $item->hotel->name }}
                  @endif
                </td>
              @endif

              @if($showMeetingPointColumn)
                <td class="text-start">
                  @php $mp = $item->meetingPoint; @endphp
                  @if(!$item->hotel && !$item->is_other_hotel && $mp)
                    <div class="fw-semibold">{{ $mp->getTranslated('name') }}</div>
                    @if($mp->pickup_time)
                      <div class="small text-muted">
                        {{ __('adminlte::adminlte.pickupTime') }}: {{ $mp->pickup_time }}
                      </div>
                    @endif
                    @php $mpDesc = $mp->getTranslated('description'); @endphp
                    @if($mpDesc)
                      <div class="small text-muted">
                        <i class="fas fa-map-marker-alt me-1"></i>{{ $mpDesc }}
                      </div>
                    @endif
                    @if($mp->map_url)
                      <a href="{{ $mp->map_url }}" target="_blank" class="small">
                        <i class="fas fa-external-link-alt me-1"></i>{{ __('adminlte::adminlte.openMap') }}
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
                <button type="button"
                        class="btn btn-sm btn-primary me-1"
                        data-bs-toggle="modal"
                        data-bs-target="#editItemModal-{{ $item->item_id }}">
                  <i class="fas fa-edit"></i> {{ __('adminlte::adminlte.edit') }}
                </button>

                <form action="{{ route('public.carts.destroy', $item->item_id) }}"
                      method="POST"
                      class="d-inline delete-item-form">
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
          $showMpInCard    = !$item->hotel && !$item->is_other_hotel && $item->meeting_point_id;
        @endphp
        <div class="card mb-3 shadow-sm cart-item-card"
             data-item-id="{{ $item->item_id }}"
             data-subtotal="{{ number_format($itemSubtotal, 2, '.', '') }}">
          <div class="card-header text-center fw-semibold">
            {{ $item->tour->getTranslatedName() ?? $item->tour->name }}
          </div>
          <div class="card-body">
            <div class="mb-2"><strong>{{ __('adminlte::adminlte.date') }}:</strong> {{ \Carbon\Carbon::parse($item->tour_date)->format('d/M/Y') }} </div>
            <div class="mb-2"><strong>{{ __('adminlte::adminlte.schedule') }}:</strong>
              @if($item->schedule)
                {{ \Carbon\Carbon::parse($item->schedule->start_time)->format('g:i A') }} -
                {{ \Carbon\Carbon::parse($item->schedule->end_time)->format('g:i A') }}
              @else
                {{ __('adminlte::adminlte.noSchedule') }}
              @endif
            </div>
            <div class="mb-2"><strong>{{ __('adminlte::adminlte.language') }}:</strong> {{ $item->language?->name ?? __('adminlte::adminlte.notSpecified') }}</div>
            <div class="mb-2"><strong>{{ __('adminlte::adminlte.adults') }}:</strong> {{ $item->adults_quantity }}</div>
            <div class="mb-2"><strong>{{ __('adminlte::adminlte.kids') }}:</strong> {{ $item->kids_quantity }}</div>

            @if($showHotelInCard)
              <div class="mb-3"><strong>{{ __('adminlte::adminlte.hotel') }}:</strong>
                @if($item->is_other_hotel && $item->other_hotel_name)
                  {{ $item->other_hotel_name }} <small class="text-muted">({{ __('adminlte::adminlte.custom') }})</small>
                @elseif($item->hotel)
                  {{ $item->hotel->name }}
                @endif
              </div>
            @endif

            @if($showMpInCard)
              @php $mp = $item->meetingPoint; @endphp
              @if($mp)
                <div class="mb-3"><strong>{{ __('adminlte::adminlte.meeting_point') }}:</strong>
                  <div>{{ $mp->getTranslated('name') }}</div>
                  @if($mp->pickup_time)
                    <div class="small text-muted">
                      {{ __('adminlte::adminlte.pickupTime') }}: {{ $mp->pickup_time }}
                    </div>
                  @endif
                  @php $mpDesc = $mp->getTranslated('description'); @endphp
                  @if($mpDesc)
                    <div class="small text-muted"><i class="fas fa-map-marker-alt me-1"></i>{{ $mpDesc }}</div>
                  @endif
                  @if($mp->map_url)
                    <a href="{{ $mp->map_url }}" class="small" target="_blank">
                      <i class="fas fa-external-link-alt me-1"></i>{{ __('adminlte::adminlte.openMap') }}
                    </a>
                  @endif
                </div>
              @endif
            @endif

            <div class="d-grid gap-2">
              <button type="button"
                      class="btn btn-success"
                      data-bs-toggle="modal"
                      data-bs-target="#editItemModal-{{ $item->item_id }}">
                <i class="fas fa-edit"></i> {{ __('adminlte::adminlte.edit') }}
              </button>

              <form action="{{ route('public.carts.destroy', $item->item_id) }}"
                    method="POST"
                    class="delete-item-form">
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

    {{-- Total + Promo code --}}
    @php
      $total = $cart->items->sum(fn($it) =>
        ($it->tour->adult_price * $it->adults_quantity)
        + ($it->tour->kid_price * $it->kids_quantity)
      );
      if ($promoSession) {
        $op = ($promoSession['operation'] ?? 'subtract') === 'add' ? 1 : -1;
        $total = max(0, round($total + $op * (float)($promoSession['adjustment'] ?? 0), 2));
      }
    @endphp

    <div class="card shadow-sm mb-4">
      <div class="card-body">
 <h4 class="mb-3">
  <strong>{{ __('adminlte::adminlte.totalEstimated') }}:</strong>
  <span class="currency-symbol">$</span>
  <span id="cart-total" class="gv-total">{{ number_format($total, 2) }}</span>
</h4>


        <label for="promo-code" class="form-label fw-semibold">{{ __('adminlte::adminlte.promoCode') }}</label>
        <div class="d-flex flex-column flex-sm-row gap-2">
          <input
            type="text"
            id="promo-code"
            name="promo_code"
            class="form-control"
            placeholder="{{ __('adminlte::adminlte.promoCodePlaceholder') }}"
            value="{{ $promoSession['code'] ?? '' }}"
          >
          <button
            type="button"
            id="toggle-promo"
            class="btn {{ $promoSession ? 'btn-outline-danger' : 'btn-outline-primary' }}"
            data-state="{{ $promoSession ? 'applied' : 'idle' }}"
          >
            {{ $promoSession ? __('adminlte::adminlte.remove') : __('adminlte::adminlte.apply') }}
          </button>
        </div>
        <div id="promo-message" class="mt-2 small {{ $promoSession ? 'text-success' : '' }}">
          @if($promoSession)
            <i class="fas fa-check-circle me-1"></i>{{ __('carts.messages.code_applied') }}.
          @endif
        </div>
      </div>
    </div>

    {{-- Confirmar reserva --}}
    <form action="{{ route('public.bookings.storeFromCart') }}" method="POST" id="confirm-reserva-form">
      @csrf
      <input type="hidden" name="promo_code" id="promo_code_hidden" value="{{ $promoSession['code'] ?? '' }}">
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
{{-- MODALES: edición por item     --}}
{{-- ============================= --}}
@foreach(($cart->items ?? collect()) as $item)
  @php
    $currentScheduleId   = $item->schedule?->schedule_id ?? null;
    $currentTourLangId   = $item->tour_language_id ?? $item->language?->tour_language_id;
    $currentHotelId      = $item->hotel?->hotel_id ?? null;
    $currentMeetingPoint = $item->meeting_point_id ?? null;

    $schedules           = $item->tour->schedules ?? collect();
    $tourLangs           = $item->tour->languages ?? collect();
    $initPickup          = $item->meeting_point_id ? 'mp' : ($item->is_other_hotel ? 'custom' : ($item->hotel ? 'hotel' : 'hotel'));
  @endphp
  <div class="modal fade" id="editItemModal-{{ $item->item_id }}" tabindex="-1" aria-labelledby="editItemLabel-{{ $item->item_id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-fullscreen-sm-down">
      <div class="modal-content">
        <form action="{{ route('public.carts.update', $item->item_id) }}" method="POST" class="edit-item-form">
          @csrf @method('PUT')

          <input type="hidden" name="is_active" value="1" />
          <input type="hidden" name="is_other_hotel" id="is-other-hidden-{{ $item->item_id }}" value="{{ $item->is_other_hotel ? 1 : 0 }}">

          <div class="modal-header">
            <h5 class="modal-title" id="editItemLabel-{{ $item->item_id }}">
              <i class="fas fa-pencil-alt me-2"></i>
              {{ __('adminlte::adminlte.editItem') }} — {{ $item->tour->getTranslatedName() ?? $item->tour->name }}
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('adminlte::adminlte.close') }}"></button>
          </div>

          <div class="modal-body">
            <div class="row g-3">
              {{-- Fecha --}}
              <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">{{ __('adminlte::adminlte.date') }}</label>
                <input type="date"
                       name="tour_date"
                       class="form-control"
                       value="{{ \Carbon\Carbon::parse($item->tour_date)->format('Y-m-d') }}"
                       min="{{ now()->format('Y-m-d') }}"
                       required>
              </div>

              {{-- Horario --}}
              <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">{{ __('adminlte::adminlte.schedule') }}</label>
                <select name="schedule_id" class="form-select">
                  <option value="">{{ __('adminlte::adminlte.selectOption') }}</option>
                  @foreach($schedules as $sch)
                    @php
                      $label = \Carbon\Carbon::parse($sch->start_time)->format('g:i A') . ' - ' . \Carbon\Carbon::parse($sch->end_time)->format('g:i A');
                    @endphp
                    <option value="{{ $sch->schedule_id }}" @selected($currentScheduleId == $sch->schedule_id)>{{ $label }}</option>
                  @endforeach
                </select>
                <div class="form-text">{{ __('adminlte::adminlte.scheduleHelp') }}</div>
              </div>

              {{-- Idioma --}}
              <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">{{ __('adminlte::adminlte.language') }}</label>
                <select name="tour_language_id" class="form-select" required>
                  @forelse($tourLangs as $tl)
                    <option value="{{ $tl->tour_language_id }}" @selected($currentTourLangId == $tl->tour_language_id)>
                      {{ $tl->name ?? $tl->language->name ?? __('adminlte::adminlte.language') }}
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

              {{-- ====== PICKUP (segmentado) ====== --}}
              <div class="col-12">
                <label class="form-label fw-semibold d-flex align-items-center gap-2">
                  <i class="fas fa-bus"></i> {{ __('adminlte::adminlte.pickup') }}
                </label>

                <div class="btn-group w-100 mb-2 pickup-tabs" role="group" aria-label="Pickup options"
                     data-item="{{ $item->item_id }}" data-init="{{ $initPickup }}">
                  <button type="button" class="btn btn-outline-secondary flex-fill" data-pickup-tab="hotel">
                    <i class="fas fa-hotel me-1"></i>{{ __('adminlte::adminlte.hotel') }}
                  </button>
                  <button type="button" class="btn btn-outline-secondary flex-fill" data-pickup-tab="custom">
                    <i class="fas fa-pen me-1"></i>{{ __('adminlte::adminlte.otherHotel') }}
                  </button>
                  <button type="button" class="btn btn-outline-secondary flex-fill" data-pickup-tab="mp">
                    <i class="fas fa-map-marker-alt me-1"></i>{{ __('adminlte::adminlte.meeting_point') }}
                  </button>
                </div>

                <div id="pickup-panes-{{ $item->item_id }}">
                  <div class="pickup-pane" id="pane-hotel-{{ $item->item_id }}" style="display:none">
                    <select name="hotel_id" id="hotel-select-{{ $item->item_id }}" class="form-select">
                      <option value="">{{ __('adminlte::adminlte.selectOption') }}</option>
                      @foreach(($hotels ?? []) as $hotel)
                        <option value="{{ $hotel->hotel_id }}" @selected($currentHotelId == $hotel->hotel_id)>{{ $hotel->name }}</option>
                      @endforeach
                    </select>
                    <div class="form-text">{{ __('adminlte::adminlte.selectHotelHelp') }}</div>
                  </div>

                  <div class="pickup-pane" id="pane-custom-{{ $item->item_id }}" style="display:none">
                    <input type="text" name="other_hotel_name" id="custom-hotel-input-{{ $item->item_id }}" class="form-control" value="{{ $item->other_hotel_name }}" placeholder="{{ __('adminlte::adminlte.customHotelName') }}">
                    <div class="form-text">{{ __('adminlte::adminlte.customHotelHelp') }}</div>
                  </div>

                  <div class="pickup-pane" id="pane-mp-{{ $item->item_id }}" style="display:none">
                    <select name="meeting_point_id"
                            class="form-select meetingpoint-select"
                            id="meetingpoint-select-{{ $item->item_id }}"
                            data-target="#mp-info-{{ $item->item_id }}"
                            data-mplist='{!! $mpListJson !!}'>
                      <option value="">{{ __('adminlte::adminlte.selectOption') }}</option>
                      @foreach($meetingPoints as $mp)
                        <option value="{{ $mp->id }}" @selected($currentMeetingPoint == $mp->id)>{{ $mp->getTranslated('name') }}</option>
                      @endforeach
                    </select>

                    <div class="border rounded p-2 mt-2 bg-light small" id="mp-info-{{ $item->item_id }}" style="display:none">
                      <div class="mp-name fw-semibold"></div>
                      <div class="mp-time text-muted"></div>
                      <div class="mp-addr mt-1"></div>
                      <a class="mp-link mt-1 d-inline-block" href="#" target="_blank" style="display:none">
                        <i class="fas fa-external-link-alt me-1"></i>{{ __('adminlte::adminlte.openMap') }}
                      </a>
                    </div>
                  </div>
                </div>
              </div>
              {{-- ====== /PICKUP ====== --}}
            </div>
          </div>

          <div class="modal-footer d-block d-sm-flex">
            <button type="button" class="btn btn-secondary w-100 w-sm-auto me-sm-2 mb-2 mb-sm-0" data-bs-dismiss="modal">
              <i class="fas fa-times"></i> {{ __('adminlte::adminlte.cancel') }}
            </button>
            <button type="submit" class="btn btn-primary w-100 w-sm-auto">
              <i class="fas fa-save"></i> {{ __('adminlte::adminlte.update') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endforeach
@endsection

@push('scripts')
  @include('partials.cart.cart-scripts')
@endpush

