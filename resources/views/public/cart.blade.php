@extends('layouts.app')

@section('title', __('adminlte::adminlte.myCart'))

@section('content')
@php
  // --- Fallback Meeting Points if controller didn't send them ---
  $meetingPoints = $meetingPoints
      ?? \App\Models\MeetingPoint::where('is_active', true)
          ->orderByRaw('sort_order IS NULL, sort_order ASC')
          ->orderBy('name', 'asc')
          ->get();

  // JSON for data-attributes
  $mpListJson = ($meetingPoints ?? collect())
      ->map(fn($mp) => [
          'id'          => $mp->id,
          'name'        => $mp->name,
          'pickup_time' => $mp->pickup_time,
          'description' => $mp->description,
          'map_url'     => $mp->map_url,
      ])->values()->toJson();

  $pickupLabel = __('adminlte::adminlte.pickupTime');

  // Conditional columns
  $showHotelColumn = ($cart && $cart->items)
      ? $cart->items->contains(fn($it) => $it->hotel || $it->is_other_hotel || $it->other_hotel_name)
      : false;

  $showMeetingPointColumn = ($cart && $cart->items)
      ? $cart->items->contains(fn($it) => !$it->hotel && !$it->is_other_hotel && ($it->meeting_point_id))
      : false;

  // Timer config
  $expiryMinutes  = (int) config('cart.expiry_minutes', 15);
  $extendMinutes  = (int) config('cart.extend_minutes', 15);

  // Promo in session
  $promoSession = session('public_cart_promo');
@endphp

{{-- ========== TIMER ========== --}}
@if(!empty($expiresAtIso))
  <div id="cart-timer"
       class="gv-timer shadow-sm"
       role="alert"
       data-expires-at="{{ $expiresAtIso }}"
       data-total-minutes="{{ $expiryMinutes }}"
       data-expire-endpoint="{{ route('public.carts.expire') }}"
       data-refresh-endpoint="{{ route('public.carts.refreshExpiry') }}">
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
      <button id="cart-timer-refresh" class="btn btn-dark btn-sm gv-timer-btn">
        {{ trans_choice('carts.timer.extend', $extendMinutes, ['count' => $extendMinutes]) }}
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

  {{-- Validation errors --}}
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
            icon: 'error',
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

    {{-- Desktop table --}}
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
              <td>{{ \Carbon\Carbon::parse($item->tour_date)->format('d/m/Y') }}</td>
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
                    <div class="fw-semibold">{{ $mp->name }}</div>
                    @if($mp->pickup_time)
                      <div class="small text-muted">
                        {{ __('adminlte::adminlte.pickupTime') }}: {{ $mp->pickup_time }}
                      </div>
                    @endif
                    @if($mp->description)
                      <div class="small text-muted">
                        <i class="fas fa-map-marker-alt me-1"></i>{{ $mp->description }}
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

    {{-- Mobile cards --}}
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
            <div class="mb-2"><strong>{{ __('adminlte::adminlte.date') }}:</strong> {{ \Carbon\Carbon::parse($item->tour_date)->format('d/m/Y') }}</div>
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
                  <div>{{ $mp->name }}</div>
                  @if($mp->pickup_time)
                    <div class="small text-muted">
                      {{ __('adminlte::adminlte.pickupTime') }}: {{ $mp->pickup_time }}
                    </div>
                  @endif
                  @if($mp->description)
                    <div class="small text-muted"><i class="fas fa-map-marker-alt me-1"></i>{{ $mp->description }}</div>
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
          $<span id="cart-total">{{ number_format($total, 2) }}</span>
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

    {{-- Confirm booking --}}
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
{{-- MODALS: per-item edit        --}}
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

          {{-- keep active when saving --}}
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
              {{-- Date --}}
              <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">{{ __('adminlte::adminlte.date') }}</label>
                <input type="date"
                       name="tour_date"
                       class="form-control"
                       value="{{ \Carbon\Carbon::parse($item->tour_date)->format('Y-m-d') }}"
                       min="{{ now()->format('Y-m-d') }}"
                       required>
              </div>

              {{-- Schedule --}}
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

              {{-- Language --}}
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

              {{-- Quantities --}}
              <div class="col-6 col-md-3">
                <label class="form-label fw-semibold">{{ __('adminlte::adminlte.adults') }}</label>
                <input type="number" name="adults_quantity" class="form-control" min="1" max="12" value="{{ (int) $item->adults_quantity }}" required>
              </div>
              <div class="col-6 col-md-3">
                <label class="form-label fw-semibold">{{ __('adminlte::adminlte.kids') }}</label>
                <input type="number" name="kids_quantity" class="form-control" min="0" max="12" value="{{ (int) $item->kids_quantity }}">
              </div>

              {{-- ====== PICKUP (segmented) ====== --}}
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
                        <option value="{{ $mp->id }}" @selected($currentMeetingPoint == $mp->id)>{{ $mp->name }}</option>
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

@push('styles')
<style>
/* ===== Timer minimal ===== */
.gv-timer{
  background: linear-gradient(90deg, #fff7e6, #fff);
  border: 1px solid #ffe2b9;
  border-left: 6px solid #f0ad4e;
  border-radius: 14px;
  padding: 14px 16px 10px;
  margin: 10px auto 0;
  max-width: 1100px;
}
.gv-timer-head{ display:flex; align-items:center; gap:14px; }
.gv-timer-icon{
  width:48px;height:48px; border-radius:50%;
  display:grid; place-items:center;
  background:#fff; border:2px dashed #f0ad4e; color:#b36b00; font-size:22px;
}
.gv-timer-text{ flex:1; line-height:1.2; }
.gv-timer-title{ font-weight:700; font-size:1.05rem; color:#8a5a00; }
.gv-timer-sub{ font-size:.95rem; color:#6c4a00; }
.gv-timer-remaining{
  display:inline-block; font-variant-numeric: tabular-nums; font-weight:800;
  font-size:1.15rem; color:#000; letter-spacing:.5px; padding:2px 8px;
  border-radius:8px; background:#fff; border:1px solid #ffe2b9; margin-left:6px;
}
.gv-timer-btn{ white-space:nowrap; }
.gv-timer-bar{ position:relative; height:8px; background:#ffe7c4; border-radius:8px; overflow:hidden; margin-top:10px; }
.gv-timer-bar-fill{ position:absolute; left:0; top:0; bottom:0; width:100%; background: linear-gradient(90deg, #ffc107, #fd7e14); transition: width .35s ease; }

/* Mobile */
@media (max-width: 575.98px){
  .gv-timer{ border-left-width:5px; padding:12px 12px 9px; }
  .gv-timer-icon{ width:42px; height:42px; font-size:20px; }
  .gv-timer-title{ font-size:1rem; }
  .gv-timer-remaining{ font-size:1.05rem; }
}

/* Tweaks */
@media (max-width: 767.98px) {
  .modal-body { padding: 1rem; }
  .modal-header, .modal-footer { padding: .75rem 1rem; }
  .btn { min-height: 42px; }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  /* ===== Confirm booking ===== */
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
      }).then((r) => { if(r.isConfirmed){ reservaForm.submit(); } });
    });
  }

  /* ===== Delete item ===== */
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
      }).then((r) => { if(r.isConfirmed){ form.submit(); } });
    });
  });

  /* ===== Meeting Point preview ===== */
  const pickupLabel = (document.getElementById('mp-config')?.dataset?.pickupLabel) || 'Pick-up';
  const updateMpInfo = (selectEl) => {
    if (!selectEl) return;
    let mplist = [];
    try { mplist = JSON.parse(selectEl.getAttribute('data-mplist') || '[]'); } catch (_) { mplist = []; }
    const box = document.querySelector(selectEl.getAttribute('data-target'));
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
      if (addrEl) addrEl.innerHTML = found.description ? ('<i class="fas fa-map-marker-alt me-1"></i>' + found.description) : '';
      if (linkEl) {
        if (found.map_url) { linkEl.href = found.map_url; linkEl.style.display = 'inline-block'; }
        else { linkEl.style.display = 'none'; }
      }
    } else {
      box.style.display = 'none';
    }
  };
  document.querySelectorAll('.meetingpoint-select').forEach(sel => {
    updateMpInfo(sel);
    sel.addEventListener('change', () => updateMpInfo(sel));
  });

  /* ===== Pickup tabs ===== */
  const activatePickupTab = (group, tab) => {
    const itemId = group.getAttribute('data-item');
    const panes = document.getElementById('pickup-panes-' + itemId);
    if (!panes) return;

    group.querySelectorAll('[data-pickup-tab]').forEach(btn => {
      btn.classList.toggle('btn-secondary', btn.getAttribute('data-pickup-tab') === tab);
      btn.classList.toggle('btn-outline-secondary', btn.getAttribute('data-pickup-tab') !== tab);
    });

    panes.querySelectorAll('.pickup-pane').forEach(p => p.style.display = 'none');
    const showPane = document.getElementById('pane-' + tab + '-' + itemId);
    if (showPane) showPane.style.display = 'block';

    const isOtherHidden = document.getElementById('is-other-hidden-' + itemId);
    const hotelSelect   = document.getElementById('hotel-select-' + itemId);
    const customInput   = document.getElementById('custom-hotel-input-' + itemId);
    const mpSelect      = document.getElementById('meetingpoint-select-' + itemId);

    if (hotelSelect) hotelSelect.value = hotelSelect.value;
    if (customInput) { /* keep text */ }
    if (mpSelect) mpSelect.value = mpSelect.value;

    if (tab === 'hotel') {
      if (isOtherHidden) isOtherHidden.value = 0;
      if (mpSelect) mpSelect.value = '';
      updateMpInfo(mpSelect);
    } else if (tab === 'custom') {
      if (isOtherHidden) isOtherHidden.value = 1;
      if (hotelSelect) hotelSelect.value = '';
      if (mpSelect) mpSelect.value = '';
      updateMpInfo(mpSelect);
    } else if (tab === 'mp') {
      if (isOtherHidden) isOtherHidden.value = 0;
      if (hotelSelect) hotelSelect.value = '';
    }
  };

  document.querySelectorAll('.pickup-tabs').forEach(group => {
    const init = group.getAttribute('data-init') || 'hotel';
    activatePickupTab(group, init);
    group.querySelectorAll('[data-pickup-tab]').forEach(btn => {
      btn.addEventListener('click', () => activatePickupTab(group, btn.getAttribute('data-pickup-tab')));
    });
  });

  /* ===== Prevent double submit in modals ===== */
  document.querySelectorAll('.edit-item-form').forEach(f => {
    f.addEventListener('submit', () => {
      const btn = f.querySelector('button[type="submit"]');
      if (btn) {
        btn.disabled = true;
        btn.innerHTML =
          '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>' +
          (@json(__('adminlte::adminlte.saving')));
      }
    });
  });

  /* ===== Promo code toggle ===== */
  {
    const toggleBtn   = document.getElementById('toggle-promo');
    const codeInput   = document.getElementById('promo-code');
    const msgBox      = document.getElementById('promo-message');
    const totalEl     = document.getElementById('cart-total');
    const hiddenCode  = document.getElementById('promo_code_hidden');

    const setMsg = (ok, text) => {
      msgBox.classList.remove('text-success','text-danger');
      msgBox.classList.add(ok ? 'text-success' : 'text-danger');
      msgBox.innerHTML = text;
    };

    const baseTotal = () => {
      const rows = Array.from(document.querySelectorAll('.cart-item-row, .cart-item-card'));
      const seen = new Set(); let sum = 0;
      rows.forEach(el => {
        const id = el.dataset.itemId || '';
        if (!id || seen.has(id)) return; seen.add(id);
        const v = parseFloat(el.dataset.subtotal || '0'); if (!isNaN(v)) sum += v;
      });
      return Math.round(sum * 100) / 100;
    };

    const setState = (applied, code, newTotal) => {
      toggleBtn.dataset.state = applied ? 'applied' : 'idle';
      toggleBtn.textContent = applied ? (@json(__('adminlte::adminlte.remove'))) : (@json(__('adminlte::adminlte.apply')));
      toggleBtn.classList.toggle('btn-outline-danger', applied);
      toggleBtn.classList.toggle('btn-outline-primary', !applied);
      hiddenCode.value = applied ? (code || '') : '';
      if (typeof newTotal === 'number') totalEl.textContent = newTotal.toFixed(2);
    };

    const applyCode = async (code) => {
      const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      const res = await fetch(@json(route('public.carts.applyPromo')), {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
        body: JSON.stringify({ code })
      });
      return res.json();
    };

    const removeCode = async () => {
      const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      const res = await fetch(@json(route('public.carts.removePromo')), {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
      });
      return res.json();
    };

    toggleBtn?.addEventListener('click', async () => {
      const state = toggleBtn.dataset.state || 'idle';

      if (state === 'applied') {
        try {
          const data = await removeCode();
          setMsg(true, `<i class="fas fa-check-circle me-1"></i>${data?.message || @json(__('carts.messages.code_removed'))}`);
          setState(false, '', baseTotal());
        } catch {
          setMsg(false, '<i class="fas fa-times-circle me-1"></i>' + @json(__('carts.messages.code_remove_failed')));
        }
        return;
      }

      const code = (codeInput?.value || '').trim();
      if (!code) {
        setMsg(false, '<i class="fas fa-times-circle me-1"></i>' + @json(__('carts.messages.enter_code')));
        return;
      }

      try {
        const data = await applyCode(code);
        if (!data?.ok) {
          setMsg(false, `<i class="fas fa-times-circle me-1"></i>${data?.message || @json(__('carts.messages.invalid_code'))}`);
          setState(false, '', baseTotal());
        } else {
          setMsg(true, `<i class="fas fa-check-circle me-1"></i>${data?.message || @json(__('carts.messages.code_applied'))}`);
          const newTotal = Number(data?.new_total ?? baseTotal());
          setState(true, data?.code || code, newTotal);
        }
      } catch {
        setMsg(false, '<i class="fas fa-times-circle me-1"></i>' + @json(__('carts.messages.code_apply_failed')));
      }
    });
  }

  /* ===== Timer countdown ===== */
  (function(){
    const box = document.getElementById('cart-timer');
    if (!box) return;

    const csrf            = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const remainingEl     = document.getElementById('cart-timer-remaining');
    const barEl           = document.getElementById('cart-timer-bar');
    const btnRefresh      = document.getElementById('cart-timer-refresh');
    const expireEndpoint  = box.getAttribute('data-expire-endpoint');
    const refreshEndpoint = box.getAttribute('data-refresh-endpoint');

    const totalSecondsCfg = Number(box.getAttribute('data-total-minutes') || '15') * 60;
    let serverExpires = new Date(box.getAttribute('data-expires-at')).getTime();
    let rafId = null;

    const fmt = (sec) => {
      const s = Math.max(0, sec|0);
      const m = Math.floor(s / 60);
      const r = s % 60;
      return String(m).padStart(2,'0') + ':' + String(r).padStart(2,'0');
    };
    const setBar = (remainingSec) => {
      const frac = Math.max(0, Math.min(1, remainingSec / totalSecondsCfg));
      if (barEl) barEl.style.width = (frac * 100).toFixed(2) + '%';
    };

    const tick = () => {
      const now = Date.now();
      const remainingSec = Math.ceil((serverExpires - now) / 1000);
      if (remainingEl) remainingEl.textContent = fmt(remainingSec);
      setBar(remainingSec);
      if (remainingSec <= 0) { cancelAnimationFrame(rafId); return handleExpire(); }
      rafId = requestAnimationFrame(tick);
    };

    const handleExpire = async () => {
      try {
        await fetch(expireEndpoint, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' } });
      } catch {}
      location.reload();
    };

    const handleRefresh = async (e) => {
      e?.preventDefault?.();
      try {
        const res = await fetch(refreshEndpoint, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' } });
        const data = await res.json();
        if (data?.ok && data?.expires_at) {
          serverExpires = new Date(data.expires_at).getTime();
          if (rafId) cancelAnimationFrame(rafId);
          tick();
        } else {
          location.reload();
        }
      } catch { location.reload(); }
    };

    if (btnRefresh) btnRefresh.addEventListener('click', handleRefresh);
    tick();
  })();
});
</script>
@endpush
