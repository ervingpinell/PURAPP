{{-- resources/views/admin/bookings/edit.blade.php --}}
@extends('adminlte::page')

@section('title', __('m_bookings.bookings.ui.edit_booking'))

@php
    // ===== Precarga PROMO desde redención real (si existe) =====
    $redemption     = $booking->redemption;
    $promoModel     = optional($redemption)->promoCode;
    $initPromoCode  = old('promo_code', $promoModel->code ?? '');
    $initOp         = old('promo_operation', $redemption->operation_snapshot ?? ($promoModel->operation ?? ''));
    $initAmount     = (float) old('promo_amount',  $redemption->applied_amount ?? ($promoModel->discount_amount ?? 0));
    $initPercent    = old('promo_percent', $redemption->percent_snapshot ?? ($promoModel->discount_percent ?? ''));

    // ===== Precarga DETALLES (categorías ya guardadas en la reserva) =====
    $details = collect($booking->details ?? []);
    $qtyByCat = $details
        ->groupBy('customer_category_id')
        ->map(fn($rows) => (int) $rows->sum('quantity'));

    $priceByCat = $details
        ->groupBy('customer_category_id')
        ->map(fn($rows) => (float) ($rows->first()->unit_price ?? 0));

    $categoryQuantitiesById = $categoryQuantitiesById ?? $qtyByCat;
    $initialCategories = $initialCategories ?? [];
@endphp

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h1 class="mb-2 mb-sm-0">
            <i class="fas fa-edit me-2"></i>
            {{ __('m_bookings.bookings.ui.edit_booking') }} #{{ $booking->booking_id }}
        </h1>
        <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> {{ __('m_bookings.bookings.buttons.back') }}
        </a>
    </div>
@stop

@section('content')
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.bookings.update', $booking->booking_id) }}" method="POST" id="editBookingForm">
        @csrf
        @method('PUT')

        <div class="row g-3">
            {{-- Left Column: Info --}}
            <div class="col-12 col-xl-8">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            {{ __('m_bookings.bookings.ui.booking_info') }}
                        </h3>
                    </div>
                    <div class="card-body">

                        {{-- Customer (LOCK: deshabilitado, se envía hidden real) --}}
                        <div class="form-group">
                            <label for="user_id_view">{{ __('m_bookings.bookings.fields.customer') }} *</label>
                            <select id="user_id_view" class="form-control select2 no-bg" disabled>
                                <option value="">-- {{ __('m_bookings.bookings.ui.select_customer') }} --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->user_id }}"
                                        {{ old('user_id', $booking->user_id) == $user->user_id ? 'selected' : '' }}>
                                        {{ $user->full_name ?? trim(($user->first_name ?? '').' '.($user->last_name ?? '')) }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="user_id" value="{{ old('user_id', $booking->user_id) }}">
                        </div>

                        {{-- Tour --}}
                        <div class="form-group">
                            <label for="tour_id">{{ __('m_bookings.bookings.fields.tour') }} *</label>
                            <select name="tour_id" id="tour_id" class="form-control select2 no-bg" required>
                                <option value="">-- {{ __('m_bookings.bookings.ui.select_tour') }} --</option>
                                @foreach($tours as $tour)
                                    <option value="{{ $tour->tour_id }}"
                                        {{ old('tour_id', $booking->tour_id) == $tour->tour_id ? 'selected' : '' }}>
                                        {{ $tour->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            {{-- Date --}}
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="tour_date">{{ __('m_bookings.bookings.fields.tour_date') }} *</label>
                                    <input
                                        type="date"
                                        name="tour_date"
                                        id="tour_date"
                                        class="form-control"
                                        value="{{ old('tour_date', optional($booking->detail?->tour_date ?? $booking->tour_date)->toDateString()) }}"
                                        required
                                    >
                                    <small class="form-text text-muted" id="tour_date_help"></small>
                                </div>
                            </div>

                            {{-- Schedule --}}
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="schedule_id">{{ __('m_bookings.bookings.fields.schedule') }} *</label>
                                    <select name="schedule_id" id="schedule_id" class="form-control"
                                            required {{ old('schedule_id', optional($booking->detail)->schedule_id) ? '' : 'disabled' }}>
                                        @php
                                            $initialScheduleId = old('schedule_id', optional($booking->detail)->schedule_id);
                                            $initialSchedule   = optional($booking->detail)->schedule;
                                        @endphp
                                        @if($initialScheduleId)
                                            <option value="{{ $initialScheduleId }}" selected>
                                                {{ $initialSchedule
                                                    ? ($initialSchedule->start_time.' - '.$initialSchedule->end_time)
                                                    : __('m_bookings.bookings.ui.keep_current_selection') }}
                                            </option>
                                        @else
                                            <option value="">{{ __('m_bookings.bookings.ui.select_tour_first') }}</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Language --}}
                        <div class="form-group">
                            <label for="tour_language_id">{{ __('m_bookings.bookings.fields.language') }} *</label>
                            <select name="tour_language_id" id="tour_language_id" class="form-control"
                                    required {{ old('tour_language_id', $booking->tour_language_id) ? '' : 'disabled' }}>
                                @php
                                    $initialLangId = old('tour_language_id', $booking->tour_language_id);
                                @endphp
                                @if($initialLangId && $booking->tourLanguage)
                                    <option value="{{ $initialLangId }}" selected>{{ $booking->tourLanguage->name }}</option>
                                @else
                                    <option value="">{{ __('m_bookings.bookings.ui.select_tour_first') }}</option>
                                @endif
                            </select>
                        </div>

                        {{-- Hotel / Meeting Point --}}
                        <div class="row">
                            {{-- HOTEL --}}
                            <div class="col-12 col-md-6">
                                <div id="hotel_group">
                                    <div class="form-group">
                                        <label for="hotel_id">{{ __('m_bookings.bookings.fields.hotel') }}</label>
                                        <select name="hotel_id" id="hotel_id" class="form-control">
                                            <option value="">-- {{ __('m_bookings.bookings.ui.select_option') }} --</option>
                                            @foreach($hotels as $hotel)
                                                <option value="{{ $hotel->hotel_id }}"
                                                    {{ old('hotel_id', optional($booking->detail)->hotel_id) == $hotel->hotel_id ? 'selected' : '' }}>
                                                    {{ $hotel->name }}
                                                </option>
                                            @endforeach
                                            <option value="other" {{ old('is_other_hotel', optional($booking->detail)->is_other_hotel) ? 'selected' : '' }}>
                                                {{ __('m_bookings.bookings.ui.other_hotel') }}
                                            </option>
                                        </select>
                                    </div>

                                    <div class="form-group" id="other_hotel_wrapper" style="display:none;">
                                        <label for="other_hotel_name">{{ __('m_bookings.bookings.fields.hotel_name') }}</label>
                                        <input type="text" name="other_hotel_name" id="other_hotel_name"
                                               class="form-control"
                                               value="{{ old('other_hotel_name', optional($booking->detail)->other_hotel_name) }}">
                                        <input type="hidden" name="is_other_hotel" id="is_other_hotel"
                                               value="{{ old('is_other_hotel', optional($booking->detail)->is_other_hotel ? 1 : 0) }}">
                                    </div>
                                </div>

                                <div id="hotel_locked" class="form-control-plaintext border rounded px-2 py-2 d-none">
                                    {{ __('m_bookings.bookings.messages.hotel_locked_by_meeting_point') ?? 'Se seleccionó un punto de encuentro; no se puede seleccionar hotel.' }}
                                </div>
                            </div>

                            {{-- MEETING POINT --}}
                            <div class="col-12 col-md-6">
                                <div id="meeting_point_group">
                                    <div class="form-group">
                                        <label for="meeting_point_id">{{ __('m_bookings.bookings.fields.meeting_point') }}</label>
                                        <select name="meeting_point_id" id="meeting_point_id" class="form-control">
                                            <option value="">-- {{ __('m_bookings.bookings.ui.select_option') }} --</option>
                                            @foreach($meetingPoints as $mp)
                                                <option value="{{ $mp->id }}"
                                                    {{ old('meeting_point_id', optional($booking->detail)->meeting_point_id) == $mp->id ? 'selected' : '' }}>
                                                    {{ $mp->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div id="mp_locked" class="form-control-plaintext border rounded px-2 py-2 d-none">
                                    {{ __('m_bookings.bookings.messages.meeting_point_locked_by_hotel') ?? 'Se seleccionó un hotel; no se puede seleccionar punto de encuentro.' }}
                                </div>
                            </div>
                        </div>

                        {{-- Pickup time (solo hora, opcional) --}}
                        <div class="form-group">
                            <label for="pickup_time">{{ __('m_bookings.bookings.fields.pickup_time') }}</label>
                            <input
                                type="time"
                                name="pickup_time"
                                id="pickup_time"
                                class="form-control"
                                value="{{ old('pickup_time', optional($booking->detail)->pickup_time
                                    ? \Carbon\Carbon::parse($booking->detail->pickup_time)->format('H:i')
                                    : '') }}"
                            >
                        </div>

                        {{-- Status --}}
                        <div class="form-group">
                            <label for="status">{{ __('m_bookings.bookings.fields.status') }} *</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="pending"  {{ old('status', $booking->status) === 'pending'  ? 'selected' : '' }}>
                                    {{ __('m_bookings.bookings.statuses.pending') }}
                                </option>
                                <option value="confirmed" {{ old('status', $booking->status) === 'confirmed' ? 'selected' : '' }}>
                                    {{ __('m_bookings.bookings.statuses.confirmed') }}
                                </option>
                                <option value="cancelled" {{ old('status', $booking->status) === 'cancelled' ? 'selected' : '' }}>
                                    {{ __('m_bookings.bookings.statuses.cancelled') ?? 'Cancelled' }}
                                </option>
                            </select>
                        </div>

                        {{-- Notes --}}
                        <div class="form-group mb-0">
                            <label for="notes">{{ __('m_bookings.bookings.fields.notes') }}</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3">{{ old('notes', $booking->notes) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Categories + Promo + Totals --}}
            <div class="col-12 col-xl-4">
                <div class="card sticky-lg-top">
                    <div class="card-header bg-success text-white">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-users me-2"></i>
                            {{ __('m_bookings.bookings.fields.travelers') }}
                        </h3>
                    </div>
                    <div class="card-body">
                        @if(!empty($bookingLimits ?? null))
                          <div class="alert alert-info py-2">
                            <div class="small">
                              <div><strong>{{ __('m_bookings.validation.max_persons_label') ?? 'Máx. personas por reserva' }}:</strong> {{ $bookingLimits['max_persons_total'] ?? '—' }}</div>
                            </div>
                          </div>
                        @endif

                        <div id="categories-container">
                            <div class="alert alert-primary mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                {{ __('m_bookings.bookings.ui.select_tour_to_see_categories') }}
                            </div>
                        </div>

                        {{-- Promo Code --}}
                        <div class="mt-3 p-3 border rounded">
                            <label for="promo_code" class="mb-1">{{ __('m_bookings.bookings.fields.promo_code') }}</label>
                            <div class="input-group flex-wrap">
                                <input type="text"
                                       name="promo_code"
                                       id="promo_code"
                                       class="form-control flex-fill"
                                       value="{{ $initPromoCode }}"
                                       placeholder="PROMO2025"
                                       autocomplete="off">
                                <div class="input-group-append w-100 w-sm-auto mt-2 mt-sm-0">
                                    <button class="btn btn-success btn-promo w-100 w-sm-auto"
                                            type="button"
                                            id="btn-verify-promo"
                                            data-mode="apply">
                                        <span class="promo-label">{{ __('m_bookings.bookings.buttons.apply') }}</span>
                                    </button>
                                </div>
                            </div>

                            <small id="promo-feedback" class="text-muted d-block mt-1"></small>
                            <input type="hidden" id="promo-operation" value="{{ $initOp }}">
                            <input type="hidden" id="promo-amount"     value="{{ $initAmount }}">
                            <input type="hidden" id="promo-percent"    value="{{ $initPercent }}">
                        </div>

                        {{-- Totales --}}
                        <div class="mt-3 p-3 border rounded">
                            <div class="d-flex justify-content-between mb-1">
                                <span>{{ __('m_bookings.bookings.fields.subtotal') }}:</span>
                                <span id="subtotal-price">$0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1" id="discount-row" style="display:none;">
                                <span>{{ __('m_bookings.bookings.fields.discount') }}:</span>
                                <span id="discount-amount">-$0.00</span>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong class="mb-0">{{ __('m_bookings.bookings.fields.total_persons') }}:</strong>
                                <div id="total-persons" class="fw-bold fs-5">0</div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <strong>{{ __('m_bookings.bookings.fields.total') }}:</strong>
                                <strong id="total-price" class="text-success fs-4">$0.00</strong>
                            </div>
                            <small id="limits-warning" class="text-danger d-block mt-2" style="display:none;"></small>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-success btn-block">
                            <i class="fas fa-save me-1"></i>
                            {{ __('m_bookings.bookings.buttons.update') }}
                        </button>
                        <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary btn-block mt-2">
                            <i class="fas fa-times me-1"></i>
                            {{ __('m_bookings.bookings.buttons.cancel') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@stop

@section('js')
  <script>
    window.BOOKING_LIMITS  = @json($bookingLimits ?? []);
    window.LIMITS_PER_TOUR = @json($limitsPerTour ?? []);
  </script>

  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script>
  $(function() {
    $('.select2').select2({ theme: 'bootstrap4', width: '100%' });

    const I18N = {
      loading:        @json(__('m_bookings.bookings.ui.loading') ?? 'Cargando…'),
      no_results:     @json(__('m_bookings.bookings.ui.no_results') ?? 'Sin resultados'),
      select_option:  @json(__('m_bookings.bookings.ui.select_option') ?? 'Seleccione'),
      select_first:   @json(__('m_bookings.bookings.ui.select_tour_first') ?? 'Seleccione un tour primero'),
      verifying:      @json(__('m_bookings.bookings.ui.verifying') ?? 'Verificando…'),
      tour_no_cats:   @json(__('m_bookings.bookings.ui.tour_without_categories') ?? 'Este tour no tiene categorías activas'),
      min:            @json(__('m_bookings.bookings.ui.min') ?? 'Min'),
      max:            @json(__('m_bookings.bookings.ui.max') ?? 'Max'),
    };

    const apiBase = '/api/v1';
    const epSchedules  = tourId => `${apiBase}/tours/${tourId}/schedules`;
    const epLanguages  = tourId => `${apiBase}/tours/${tourId}/languages`;
    const epCategories = tourId => `${apiBase}/tours/${tourId}/categories`;
    const epVerify     =        `${apiBase}/bookings/verify-promo-code`;

    window.INIT_QTYS       = @json($categoryQuantitiesById ?? []);
    window.INIT_PRICES     = @json($priceByCat ?? []);
    window.INIT_CATEGORIES = @json($initialCategories);
    window.INIT_PROMO = {
        code:     @json($initPromoCode),
        operation:@json($initOp),
        amount:   parseFloat(@json($initAmount)),
        percent:  @json($initPercent)
    };

    let currentCategories = [];
    let lastChangedInputId = null;
    const LIMITS = window.BOOKING_LIMITS || {};
    const PER_TOUR = window.LIMITS_PER_TOUR || {};
    const fmtMoney = n => '$' + (Number(n||0)).toFixed(2);

    function resetSelect($sel, placeholder, disabled=true) {
        $sel.html(`<option value="">${placeholder}</option>`).prop('disabled', !!disabled);
    }
    function applyOldValue($sel, value) {
        if (value === undefined || value === null || value === '') return;
        if ($sel.find(`option[value="${value}"]`).length) {
            $sel.val(String(value)).trigger('change.select2');
        }
    }

    (function setupDateMinMax(){
      const minDays = Number(LIMITS.min_days_advance ?? 1);
      const maxDays = Number(LIMITS.max_days_advance ?? 365);
      const $input = $('#tour_date');
      if (!$input.length) return;

      const today = new Date();
      const minDate = new Date(today); minDate.setDate(today.getDate() + minDays);
      const maxDate = new Date(today); maxDate.setDate(today.getDate() + maxDays);

      const toYMD = (d) => new Date(d.getTime() - d.getTimezoneOffset()*60000).toISOString().slice(0,10);
      $input.attr('min', toYMD(minDate));
      $input.attr('max', toYMD(maxDate));

      const hintTpl = @json(__('m_bookings.validation.date_range_hint') ?: 'Rango permitido: :from — :to');
      const hintText = (hintTpl || 'Rango permitido: :from — :to')
        .replace(':from', toYMD(minDate))
        .replace(':to', toYMD(maxDate));
      $('#tour_date_help').text(hintText);

      const current = $input.val();
      if (current) {
        if (current < toYMD(minDate)) $input.val(toYMD(minDate));
        if (current > toYMD(maxDate)) $input.val(toYMD(maxDate));
      }
    })();

    function loadSchedules(tourId, preselect = '{{ old('schedule_id', optional($booking->detail)->schedule_id) }}') {
        const $sel = $('#schedule_id');
        resetSelect($sel, I18N.loading, true);
        $.get(epSchedules(tourId))
            .done(list => {
                if (!Array.isArray(list) || !list.length) {
                    const cur = '{{ old('schedule_id', optional($booking->detail)->schedule_id) }}';
                    if (cur) $sel.prop('disabled', false);
                    else resetSelect($sel, I18N.no_results, true);
                    return;
                }
                let html = `<option value="">${I18N.select_option}</option>`;
                list.forEach(s => {
                    html += `<option value="${s.schedule_id}">${s.start_time} - ${s.end_time}</option>`;
                });
                $sel.html(html).prop('disabled', false);
                applyOldValue($sel, preselect);
            })
            .fail(() => resetSelect($sel, I18N.no_results, true));
    }

    function loadLanguages(tourId, preselect = '{{ old('tour_language_id', $booking->tour_language_id) }}') {
        const $sel = $('#tour_language_id');
        resetSelect($sel, I18N.loading, true);
        $.get(epLanguages(tourId))
            .done(list => {
                if (!Array.isArray(list) || !list.length) {
                    const cur = '{{ old('tour_language_id', $booking->tour_language_id) }}';
                    if (cur) $sel.prop('disabled', false);
                    else resetSelect($sel, I18N.no_results, true);
                    return;
                }
                let html = `<option value="">${I18N.select_option}</option>`;
                list.forEach(l => {
                    html += `<option value="${l.tour_language_id}">${l.name}</option>`;
                });
                $sel.html(html).prop('disabled', false);
                applyOldValue($sel, preselect);
            })
            .fail(() => resetSelect($sel, I18N.no_results, true));
    }

    function safeName(cat) {
        const init = (window.INIT_CATEGORIES || []).find(x => String(x.id) === String(cat.id));
        return (cat.name || init?.name || cat.slug || `Category #${cat.id}`);
    }

    function bootstrapCategoriesFromInit() {
        const $c = $('#categories-container');
        const qtys   = window.INIT_QTYS || {};
        const prices = window.INIT_PRICES || {};
        const inits  = window.INIT_CATEGORIES || [];

        let catList = Array.isArray(inits) && inits.length
            ? inits.filter(c => (c.is_active ?? true))
            : Object.keys({...qtys, ...prices}).map(id => ({
                id: id,
                slug: null,
                name: `Category #${id}`,
                price: Number(prices[id] || 0),
                min: 0,
                max: 99,
                is_active: true
              }));

        if (!catList.length) {
            $c.html(`<div class="alert alert-warning mb-0">${I18N.tour_no_cats}</div>`);
            currentCategories = [];
            updateTotals();
            initPromoFromBooking();
            return;
        }

        currentCategories = catList;
        let html = '';
        catList.forEach(cat => {
            const min = parseInt(cat.min ?? 0);
            const max = parseInt(cat.max ?? 99);
            const existing = parseInt((qtys || {})[String(cat.id)]);
            const start = Number.isFinite(existing) ? existing : Math.max(min, 0);
            const clamped = Math.max(min, Math.min(max, start));
            const price = Number(cat.price ?? prices[String(cat.id)] ?? 0);
            const label = safeName(cat);

            html += `
              <div class="category-row mb-3 p-2 border rounded">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <strong>${label}</strong>
                  <span class="text-muted small">${fmtMoney(price)}</span>
                </div>
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                  <button type="button" class="btn btn-sm btn-secondary category-minus" data-category-id="${cat.id}">
                    <i class="fas fa-minus"></i>
                  </button>
                  <input type="number"
                         name="categories[${cat.id}]"
                         class="form-control form-control-sm text-center category-input mx-2"
                         data-category-id="${cat.id}"
                         data-price="${price}"
                         min="${min}"
                         max="${max}"
                         value="${clamped}"
                         style="width: 72px;">
                  <button type="button" class="btn btn-sm btn-secondary category-plus" data-category-id="${cat.id}">
                    <i class="fas fa-plus"></i>
                  </button>
                </div>
                <small class="text-muted d-block mt-1">${I18N.min}: ${min}, ${I18N.max}: ${max}</small>
              </div>`;
        });

        $('#categories-container').html(html);
        attachCategoryHandlers();
        updateTotals();
        initPromoFromBooking();
    }

    function loadCategories(tourId) {
        const $c = $('#categories-container');
        $c.html(
          `<div class="alert alert-info mb-0">
              <i class="fas fa-spinner fa-spin me-2"></i>${I18N.loading}
           </div>`
        );

        $.get(epCategories(tourId))
            .done(list => {
                const arr = (list || []).filter(c => c.is_active);
                if (!arr.length) {
                    bootstrapCategoriesFromInit();
                    return;
                }
                currentCategories = arr;
                let html = '';
                const qtys   = window.INIT_QTYS || {};
                arr.forEach(cat => {
                    const min = parseInt(cat.min);
                    const max = parseInt(cat.max);
                    const existing = parseInt((qtys || {})[String(cat.id)]);
                    const start = Number.isFinite(existing) ? existing : min;
                    const clamped = Math.max(min, Math.min(max, start));
                    const label = safeName(cat);

                    html += `
                      <div class="category-row mb-3 p-2 border rounded">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                          <strong>${label}</strong>
                          <span class="text-muted small">${fmtMoney(cat.price)}</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                          <button type="button" class="btn btn-sm btn-secondary category-minus" data-category-id="${cat.id}">
                            <i class="fas fa-minus"></i>
                          </button>
                          <input type="number"
                                 name="categories[${cat.id}]"
                                 class="form-control form-control-sm text-center category-input mx-2"
                                 data-category-id="${cat.id}"
                                 data-price="${cat.price}"
                                 min="${min}"
                                 max="${max}"
                                 value="${clamped}"
                                 style="width: 72px;">
                          <button type="button" class="btn btn-sm btn-secondary category-plus" data-category-id="${cat.id}">
                            <i class="fas fa-plus"></i>
                          </button>
                        </div>
                        <small class="text-muted d-block mt-1">${I18N.min}: ${min}, ${I18N.max}: ${max}</small>
                      </div>`;
                });

                $c.html(html);
                attachCategoryHandlers();
                updateTotals();
                initPromoFromBooking();
            })
            .fail(() => {
                bootstrapCategoriesFromInit();
            });
    }

    function getAdultsKidsFromInputs() {
      const cats = (PER_TOUR.categories || []);
      const adultId = (cats.find(c => c.slug === 'adult') || {}).category_id;
      const kidId   = (cats.find(c => c.slug === 'kid')   || {}).category_id;

      let adults = 0, kids = 0;

      if (adultId) {
        const $a = $(`.category-input[data-category-id="${adultId}"]`);
        adults = parseInt($a.val() || 0);
      }
      if (kidId) {
        const $k = $(`.category-input[data-category-id="${kidId}"]`);
        kids = parseInt($k.val() || 0);
      }
      return { adults, kids };
    }

    function computeSubtotalAndPersons() {
        let persons = 0, subtotal = 0;
        $('.category-input').each(function() {
            const qty = parseInt($(this).val()) || 0;
            const price = parseFloat($(this).data('price')) || 0;
            persons += qty;
            subtotal += qty * price;
        });
        return { persons, subtotal };
    }

    function showLimitsWarning(msg) { $('#limits-warning').text(msg).show(); }
    function hideLimitsWarning() { $('#limits-warning').text('').hide(); }

    function updateTotals() {
        hideLimitsWarning();

        const { persons, subtotal } = computeSubtotalAndPersons();
        const maxTotal = Number(LIMITS.max_persons_per_booking ?? LIMITS.max_persons_total ?? 12);
        let finalPersons = persons;

        if (persons > maxTotal) {
          if (lastChangedInputId !== null) {
            const sumOthers = persons - (parseInt($(`.category-input[data-category-id="${lastChangedInputId}"]`).val()) || 0);
            const allowedForLast = Math.max(0, maxTotal - sumOthers);
            const $last = $(`.category-input[data-category-id="${lastChangedInputId}"]`);
            $last.val(allowedForLast);
            finalPersons = maxTotal;
          }
          showLimitsWarning(`{{ __('m_bookings.validation.max_persons_exceeded') ?? 'Se excede el máximo de personas por reserva' }}: ${maxTotal}`);
        }

        const { adults, kids } = getAdultsKidsFromInputs();
        const minAdults = Number(LIMITS.min_adults_per_booking ?? LIMITS.min_adults ?? 0);
        const maxKids   = Number(LIMITS.max_kids_per_booking ?? LIMITS.max_kids ?? Number.MAX_SAFE_INTEGER);

        if (minAdults > 0 && adults < minAdults) {
          showLimitsWarning(`{{ __('m_bookings.validation.min_adults_required') ?? 'Mínimo de adultos requerido' }}: ${minAdults}`);
        }
        if (kids > maxKids) {
          showLimitsWarning(`{{ __('m_bookings.validation.max_kids_exceeded') ?? 'Máximo de niños excedido' }}: ${maxKids}`);
        }

        $('#total-persons').text(finalPersons);
        $('#subtotal-price').text(fmtMoney(subtotal));

        const op  = $('#promo-operation').val();
        const amt = parseFloat($('#promo-amount').val() || 0);

        if (op === 'subtract' && amt > 0) {
            $('#discount-row').show();
            $('#discount-amount').text('-' + fmtMoney(amt).replace('$',''));
        } else {
            $('#discount-row').hide();
            $('#discount-amount').text('-$0.00');
        }

        const total = subtotal - (op === 'subtract' ? amt : 0) + (op === 'add' ? amt : 0);
        $('#total-price').text(fmtMoney(Math.max(total, 0)));
    }

    function attachCategoryHandlers() {
        $('.category-minus').on('click', function() {
            const id = $(this).data('category-id');
            lastChangedInputId = String(id);
            const $i = $(`.category-input[data-category-id="${id}"]`);
            const min = parseInt($i.attr('min'));
            const cur = parseInt($i.val()) || 0;
            if (cur > min) { $i.val(cur - 1); updateTotals(); }
        });

        $('.category-plus').on('click', function() {
            const id = $(this).data('category-id');
            lastChangedInputId = String(id);
            const $i = $(`.category-input[data-category-id="${id}"]`);
            const max = parseInt($i.attr('max'));
            const cur = parseInt($i.val()) || 0;
            if (cur < max) { $i.val(cur + 1); updateTotals(); }
        });

        $('.category-input').on('change keyup', function() {
            lastChangedInputId = String($(this).data('category-id'));
            const min = parseInt($(this).attr('min'));
            const max = parseInt($(this).attr('max'));
            let v = parseInt($(this).val()) || 0;
            if (v < min) v = min;
            if (v > max) v = max;
            $(this).val(v);
            updateTotals();
        });
    }

    const APPLY_LABEL  = @json(__('m_bookings.bookings.buttons.apply'));
    const REMOVE_LABEL = @json(__('m_bookings.bookings.buttons.delete') ?? 'Quitar');

    function setPromoButton(mode) {
        const $btn   = $('#btn-verify-promo');
        const $label = $btn.find('.promo-label');
        if (mode === 'remove') {
            $btn.data('mode', 'remove').removeClass('btn-success').addClass('btn-danger');
            $label.length ? $label.text(REMOVE_LABEL) : $btn.text(REMOVE_LABEL);
        } else {
            $btn.data('mode', 'apply').removeClass('btn-danger').addClass('btn-success');
            $label.length ? $label.text(APPLY_LABEL) : $btn.text(APPLY_LABEL);
        }
    }

    function clearPromoUI(showMsg = true) {
        $('#promo-operation').val('');
        $('#promo-amount').val('0');
        $('#promo-percent').val('');
        if (showMsg) {
            $('#promo-feedback').removeClass('text-danger text-success').addClass('text-muted')
                .text(@json(__('m_bookings.bookings.messages.promo_removed') ?? 'Código promocional removido.'));
        } else {
            $('#promo-feedback').text('');
        }
        setPromoButton('apply');
        updateTotals();
    }

    function initPromoFromBooking() {
        const init = window.INIT_PROMO || {};
        const hasApplied = (init && init.operation && (parseFloat(init.amount||0) > 0));
        const code = (init.code || '').trim();

        if (code) { $('#promo_code').val(code); }
        if (hasApplied) {
            $('#promo-operation').val(init.operation);
            $('#promo-amount').val(init.amount || 0);
            $('#promo-percent').val(init.percent || '');
            $('#promo-feedback').removeClass('text-danger text-muted').addClass('text-success')
                .text(init.operation === 'subtract'
                    ? @json(__('m_bookings.bookings.messages.promo_applied_subtract')) + ' ' + fmtMoney(init.amount)
                    : @json(__('m_bookings.bookings.messages.promo_applied_add')) + ' ' + fmtMoney(init.amount)
                );
            setPromoButton('remove');
            updateTotals();
        } else {
            setPromoButton('apply');
        }
    }

    $('#promo_code').on('keydown', function(e) {
        if (e.key === 'Enter') { e.preventDefault(); $('#btn-verify-promo').trigger('click'); }
    });

    $('#promo_code').on('input', function() {
        const val = ($(this).val() || '').trim();
        if (val === '' && $('#btn-verify-promo').data('mode') === 'remove') {
            clearPromoUI(true); $('#promo_code').val('');
        }
    });

    $('#btn-verify-promo').on('click', function() {
        const $btn = $(this);
        const mode = $btn.data('mode');

        if (mode === 'remove') { clearPromoUI(true); $('#promo_code').val(''); return; }

        const code = ($('#promo_code').val() || '').trim();
        const { subtotal } = computeSubtotalAndPersons();
        if (!code)  { $('#promo-feedback').removeClass('text-success').addClass('text-danger').text('{{ __('m_bookings.bookings.validation.promo_empty') }}'); return; }
        if (subtotal <= 0) { $('#promo-feedback').removeClass('text-success').addClass('text-danger').text('{{ __('m_bookings.bookings.validation.promo_needs_subtotal') }}'); return; }

        const originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span><span class="promo-label">'+I18N.verifying+'</span>');

        $.get(epVerify, { code, subtotal })
            .done(function(resp) {
                if (!resp || !resp.valid) {
                    clearPromoUI(false);
                    $('#promo-feedback').removeClass('text-success text-muted').addClass('text-danger')
                        .text(resp?.message || 'Código inválido');
                } else {
                    $('#promo-operation').val(resp.operation);
                    $('#promo-amount').val(resp.discount_amount || 0);
                    $('#promo-percent').val(resp.discount_percent ?? '');
                    $('#promo-feedback').removeClass('text-danger text-muted').addClass('text-success')
                        .text(resp.operation === 'subtract'
                            ? '{{ __('m_bookings.bookings.messages.promo_applied_subtract') }} ' + fmtMoney(resp.discount_amount)
                            : '{{ __('m_bookings.bookings.messages.promo_applied_add') }} ' + fmtMoney(resp.discount_amount)
                        );
                    setPromoButton('remove');
                }
                updateTotals();
            })
            .fail(function(xhr) {
                const msg = (xhr?.responseJSON?.message) || 'Error verificando el código.';
                $('#promo-feedback').removeClass('text-success text-muted').addClass('text-danger').text(msg);
            })
            .always(function() {
                $btn.prop('disabled', false);
                if ($btn.data('mode') === 'remove') {
                    $btn.removeClass('btn-success').addClass('btn-danger')
                        .html('<span class="promo-label">' + REMOVE_LABEL + '</span>');
                } else {
                    $btn.removeClass('btn-danger').addClass('btn-success')
                        .html('<span class="promo-label">' + APPLY_LABEL + '</span>');
                }
            });
    });

    function lockHotel(msg){
        $('#hotel_group').addClass('d-none');
        $('#other_hotel_wrapper').hide();
        $('#hotel_locked').removeClass('d-none').text(msg);
        if ($('#hotel_id').val()) $('#hotel_id').val('').trigger('change.select2');
        $('#is_other_hotel').val('0'); $('#other_hotel_name').val('');
    }
    function unlockHotel(){
        $('#hotel_locked').addClass('d-none').text('');
        $('#hotel_group').removeClass('d-none');
    }
    function lockMeetingPoint(msg){
        $('#meeting_point_group').addClass('d-none');
        $('#mp_locked').removeClass('d-none').text(msg);
        if ($('#meeting_point_id').val()) $('#meeting_point_id').val('').trigger('change.select2');
    }
    function unlockMeetingPoint(){
        $('#mp_locked').addClass('d-none').text('');
        $('#meeting_point_group').removeClass('d-none');
    }

    $('#meeting_point_id').on('change', function () {
        const hasMP = ($(this).val() || '') !== '';
        if (hasMP) { lockHotel('{{ __("m_bookings.bookings.messages.hotel_locked_by_meeting_point") ?? "Se seleccionó un punto de encuentro; no se puede seleccionar hotel." }}'); }
        else { unlockHotel(); }
    });

    $('#hotel_id').on('change', function () {
        const val = $(this).val() || '';
        const pickedHotel = val !== '';
        const pickedOther = val === 'other';

        if (pickedOther) { $('#other_hotel_wrapper').slideDown(); $('#is_other_hotel').val('1'); }
        else { $('#other_hotel_wrapper').slideUp(); $('#is_other_hotel').val('0'); $('#other_hotel_name').val(''); }

        if (pickedHotel || pickedOther) {
            lockMeetingPoint('{{ __("m_bookings.bookings.messages.meeting_point_locked_by_hotel") ?? "Se seleccionó un hotel; no se puede seleccionar punto de encuentro." }}');
        } else { unlockMeetingPoint(); }
    });

    if ($('#hotel_id').val() === 'other' || '{{ old('is_other_hotel', optional($booking->detail)->is_other_hotel) ? 1 : 0 }}' === '1') {
        $('#other_hotel_wrapper').show();
        $('#is_other_hotel').val('1');
    }

    (function initState(){
        const hasMP = ($('#meeting_point_id').val() || '') !== '';
        const hVal = $('#hotel_id').val() || '';
        const pickedHotel = hVal !== '';
        const pickedOther = hVal === 'other';

        if (hasMP) {
            lockHotel('{{ __("m_bookings.bookings.messages.hotel_locked_by_meeting_point") ?? "Se seleccionó un punto de encuentro; no se puede seleccionar hotel." }}');
        }
        if (pickedHotel || pickedOther) {
            lockMeetingPoint('{{ __("m_bookings.bookings.messages.meeting_point_locked_by_hotel") ?? "Se seleccionó un hotel; no se puede seleccionar punto de encuentro." }}');
        }
        if (pickedOther) { $('#other_hotel_wrapper').show(); $('#is_other_hotel').val('1'); }
    })();

    const initialTourId = '{{ old('tour_id', $booking->tour_id) }}';
    if (initialTourId) {
        $('#tour_id').val(initialTourId).trigger('change.select2');
        loadSchedules(initialTourId, '{{ old('schedule_id', optional($booking->detail)->schedule_id) }}');
        loadLanguages(initialTourId, '{{ old('tour_language_id', $booking->tour_language_id) }}');
        bootstrapCategoriesFromInit();
        loadCategories(initialTourId);
    }

    $('#tour_id').on('change', function() {
        const tourId = $(this).val();
        resetSelect($('#schedule_id'), I18N.select_first, true);
        resetSelect($('#tour_language_id'), I18N.select_first, true);
        $('#categories-container').html(
          `<div class="alert alert-info mb-0">
             {{ __('m_bookings.bookings.ui.select_tour_to_see_categories') }}
           </div>`
        );
        currentCategories = [];
        clearPromoUI(false);

        if (!tourId) return;
        loadSchedules(tourId);
        loadLanguages(tourId);
        loadCategories(tourId);
    });
  });
  </script>
@stop

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css" rel="stylesheet" />
<style>
select.no-bg + .select2-container--bootstrap4 .select2-selection--single { background: transparent !important; }
select.no-bg + .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered { background: transparent !important; }
select.no-bg + .select2-container--bootstrap4 .select2-selection--single .select2-selection__placeholder { color: inherit; }
select.no-bg + .select2-container--bootstrap4 .select2-selection--single:focus { box-shadow: none; }

.select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered { color: white !important; }
.form-control-plaintext { color: white; }

.sticky-lg-top { position: static; }
@media (min-width: 992px) {
  .sticky-lg-top { position: sticky; top: 20px; z-index: 1020; }
}
.w-sm-auto { width: auto; }
@media (max-width: 575.98px) {
  .w-sm-auto { width: 100% !important; }
}
@media (max-width: 575.98px) {
  .category-row .btn { padding: .45rem .6rem; }
  .category-row input { width: 68px !important; }
}
.btn-promo {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: .4rem;
}
</style>
@stop
