{{-- resources/views/admin/bookings/create.blade.php --}}
@extends('adminlte::page')

@section('title', __('m_bookings.bookings.ui.create_booking'))

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h1 class="mb-2 mb-sm-0">
            <i class="fas fa-plus-circle me-2"></i>
            {{ __('m_bookings.bookings.ui.create_booking') }}
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

    <form action="{{ route('admin.bookings.store') }}" method="POST" id="createBookingForm">
        @csrf

        <div class="row g-3">
            {{-- Left Column: Customer & Tour Info --}}
            <div class="col-12 col-xl-8">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            {{ __('m_bookings.bookings.ui.booking_info') }}
                        </h3>
                    </div>
                    <div class="card-body">

                        {{-- Customer (sin fondo blanco) --}}
                        <div class="form-group">
                            <label for="user_id">{{ __('m_bookings.bookings.fields.customer') }} *</label>
                            <select name="user_id" id="user_id" class="form-control select2 no-bg" required>
                                <option value="">-- {{ __('m_bookings.bookings.ui.select_customer') }} --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->user_id }}" {{ old('user_id') == $user->user_id ? 'selected' : '' }}>
                                        {{ $user->full_name ?? trim(($user->first_name ?? '').' '.($user->last_name ?? '')) }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Tour (sin fondo blanco) --}}
                        <div class="form-group">
                            <label for="tour_id">{{ __('m_bookings.bookings.fields.tour') }} *</label>
                            <select name="tour_id" id="tour_id" class="form-control select2 no-bg" required>
                                <option value="">-- {{ __('m_bookings.bookings.ui.select_tour') }} --</option>
                                @foreach($tours as $tour)
                                    <option value="{{ $tour->tour_id }}" {{ old('tour_id') == $tour->tour_id ? 'selected' : '' }}>
                                        {{ $tour->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            {{-- Date (ajustada por límites dinámicos) --}}
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="tour_date">{{ __('m_bookings.bookings.fields.tour_date') }} *</label>
                                    <input
                                        type="date"
                                        name="tour_date"
                                        id="tour_date"
                                        class="form-control"
                                        value="{{ old('tour_date') }}"
                                        required
                                    >
                                    <small class="form-text text-muted" id="tour_date_help"></small>
                                </div>
                            </div>

                            {{-- Schedule --}}
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="schedule_id">{{ __('m_bookings.bookings.fields.schedule') }} *</label>
                                    <select name="schedule_id" id="schedule_id" class="form-control" required disabled>
                                        <option value="">{{ __('m_bookings.bookings.ui.select_tour_first') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Language --}}
                        <div class="form-group">
                            <label for="tour_language_id">{{ __('m_bookings.bookings.fields.language') }} *</label>
                            <select name="tour_language_id" id="tour_language_id" class="form-control" required disabled>
                                <option value="">{{ __('m_bookings.bookings.ui.select_tour_first') }}</option>
                            </select>
                        </div>

                        {{-- Hotel / Meeting Point (mutuamente excluyentes con mensaje) --}}
                        <div class="row">
                            {{-- HOTEL --}}
                            <div class="col-12 col-md-6">
                                <div id="hotel_group">
                                    <div class="form-group">
                                        <label for="hotel_id">{{ __('m_bookings.bookings.fields.hotel') }}</label>
                                        <select name="hotel_id" id="hotel_id" class="form-control">
                                            <option value="">-- {{ __('m_bookings.bookings.ui.select_option') }} --</option>
                                            @foreach($hotels as $hotel)
                                                <option value="{{ $hotel->hotel_id }}" {{ old('hotel_id') == $hotel->hotel_id ? 'selected' : '' }}>
                                                    {{ $hotel->name }}
                                                </option>
                                            @endforeach
                                            <option value="other" {{ old('is_other_hotel') ? 'selected' : '' }}>
                                                {{ __('m_bookings.bookings.ui.other_hotel') }}
                                            </option>
                                        </select>
                                    </div>

                                    <div class="form-group" id="other_hotel_wrapper" style="display:none;">
                                        <label for="other_hotel_name">{{ __('m_bookings.bookings.fields.hotel_name') }}</label>
                                        <input type="text" name="other_hotel_name" id="other_hotel_name"
                                               class="form-control" value="{{ old('other_hotel_name') }}">
                                        <input type="hidden" name="is_other_hotel" id="is_other_hotel" value="{{ old('is_other_hotel', 0) }}">
                                    </div>
                                </div>

                                {{-- Texto cuando HOTEL está bloqueado por Meeting Point --}}
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
                                                <option value="{{ $mp->id }}" {{ old('meeting_point_id') == $mp->id ? 'selected' : '' }}>
                                                    {{ $mp->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- Texto cuando MEETING POINT está bloqueado por Hotel --}}
                                <div id="mp_locked" class="form-control-plaintext border rounded px-2 py-2 d-none">
                                    {{ __('m_bookings.bookings.messages.meeting_point_locked_by_hotel') ?? 'Se seleccionó un hotel; no se puede seleccionar punto de encuentro.' }}
                                </div>
                            </div>
                        </div>

                        {{-- Status oculto (siempre pending) --}}
                        <input type="hidden" name="status" value="pending">

                        {{-- Notes --}}
                        <div class="form-group mb-0">
                            <label for="notes">{{ __('m_bookings.bookings.fields.notes') }}</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Categories + Promo + Totals (con límites y warnings) --}}
            <div class="col-12 col-xl-4">
                <div class="card sticky-lg-top">
                    <div class="card-header bg-success text-white">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-users me-2"></i>
                            {{ __('m_bookings.bookings.fields.travelers') }}
                        </h3>
                    </div>
                    <div class="card-body">
                        {{-- Resumen de límites globales (opcional visible) --}}
                        @if(!empty($bookingLimits ?? null))
                          <div class="alert alert-info py-2">
                            <div class="small">
                              <div><strong>{{ __('m_bookings.validation.max_persons_label') ?? 'Máx. personas por reserva' }}:</strong> {{ $bookingLimits['max_persons_total'] ?? ($bookingLimits['max_persons_per_booking'] ?? '—') }}</div>
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

                            {{-- En XS el botón se apila debajo, en ≥SM queda a la par --}}
                            <div class="input-group flex-wrap">
                                <input type="text"
                                       name="promo_code"
                                       id="promo_code"
                                       class="form-control flex-fill"
                                       value="{{ old('promo_code') }}"
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
                            <input type="hidden" id="promo-operation" value="">
                            <input type="hidden" id="promo-amount" value="0">
                            <input type="hidden" id="promo-percent" value="">
                        </div>

                        {{-- Totales + Warnings de límites --}}
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
                            {{ __('m_bookings.bookings.buttons.save') }}
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
  {{-- Exponer límites de booking y por tour al front (igual que edit) --}}
  <script>
    window.BOOKING_LIMITS  = @json($bookingLimits ?? []);
    window.LIMITS_PER_TOUR = @json($limitsPerTour ?? []);
  </script>

  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script>
  $(function() {
    $('.select2').select2({ theme: 'bootstrap4', width: '100%' });

    // ===== Endpoints =====
    const apiBase = '/api/v1';
    const epSchedules  = tourId => `${apiBase}/tours/${tourId}/schedules`;
    const epLanguages  = tourId => `${apiBase}/tours/${tourId}/languages`;
    const epCategories = tourId => `${apiBase}/tours/${tourId}/categories`;
    const epVerify     =        `${apiBase}/bookings/verify-promo-code`;

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

    // ---------- Date limits from config (igual a edit) ----------
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
// Texto base traducido con placeholders (:from — :to)
const hintTpl = @json(__('m_bookings.validation.date_range_hint') ?: 'Rango permitido: :from — :to');

// Construye las fechas min/max
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

    // ---------- Loaders ----------
    function loadSchedules(tourId, preselect = '{{ old('schedule_id') }}') {
        const $sel = $('#schedule_id');
        resetSelect($sel, @json(__('m_bookings.bookings.ui.loading')), true);
        $.get(epSchedules(tourId))
            .done(list => {
                if (!Array.isArray(list) || !list.length) {
                    resetSelect($sel, @json(__('m_bookings.bookings.ui.no_results')), true);
                    return;
                }
                let html = `<option value="">${@json(__('m_bookings.bookings.ui.select_option'))}</option>`;
                list.forEach(s => {
                    html += `<option value="${s.schedule_id}">${s.start_time} - ${s.end_time}</option>`;
                });
                $sel.html(html).prop('disabled', false);
                applyOldValue($sel, preselect);
            })
            .fail(() => resetSelect($sel, @json(__('m_bookings.bookings.ui.error_loading')), true));
    }

    function loadLanguages(tourId, preselect = '{{ old('tour_language_id') }}') {
        const $sel = $('#tour_language_id');
        resetSelect($sel, @json(__('m_bookings.bookings.ui.loading')), true);
        $.get(epLanguages(tourId))
            .done(list => {
                if (!Array.isArray(list) || !list.length) {
                    resetSelect($sel, @json(__('m_bookings.bookings.ui.no_results')), true);
                    return;
                }
                let html = `<option value="">${@json(__('m_bookings.bookings.ui.select_option'))}</option>`;
                list.forEach(l => {
                    html += `<option value="${l.tour_language_id}">${l.name}</option>`;
                });
                $sel.html(html).prop('disabled', false);
                applyOldValue($sel, preselect);
            })
            .fail(() => resetSelect($sel, @json(__('m_bookings.bookings.ui.error_loading')), true));
    }

    function loadCategories(tourId, oldCategories = @json(old('categories', []))) {
        const $c = $('#categories-container');
        $c.html(
          `<div class="alert alert-info mb-0">
              <i class="fas fa-spinner fa-spin me-2"></i>{{ __('m_bookings.bookings.ui.loading') }}
           </div>`
        );

        $.get(epCategories(tourId))
            .done(list => {
                currentCategories = (list || []).filter(c => c.is_active);
                if (!currentCategories.length) {
                    $c.html(
                      `<div class="alert alert-warning mb-0">
                          {{ __('m_bookings.bookings.ui.tour_without_categories') }}
                       </div>`
                    );
                    updateTotals();
                    return;
                }

                let html = '';
                currentCategories.forEach(cat => {
                    const min = parseInt(cat.min);
                    const max = parseInt(cat.max);
                    const oldVal = parseInt(oldCategories[cat.id] ?? min) || min;
                    const clamped = Math.max(min, Math.min(max, oldVal));

                    html += `
                      <div class="category-row mb-3 p-2 border rounded">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                          <strong>${cat.name}</strong>
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
                        <small class="text-muted d-block mt-1">Min: ${min}, Max: ${max}</small>
                      </div>`;
                });

                $c.html(html);
                attachCategoryHandlers();
                updateTotals();
            })
            .fail(() => {
                $c.html(
                  `<div class="alert alert-danger mb-0">
                      {{ __('m_bookings.bookings.ui.error_loading') }}
                   </div>`
                );
                currentCategories = [];
                updateTotals();
            });
    }

    // ---------- Helpers para límites globales (igual que edit) ----------
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

    function showLimitsWarning(msg) {
      $('#limits-warning').text(msg).show();
    }
    function hideLimitsWarning() {
      $('#limits-warning').text('').hide();
    }

    function updateTotals() {
        hideLimitsWarning();

        const { persons, subtotal } = computeSubtotalAndPersons();
        const maxTotal = Number(LIMITS.max_persons_per_booking ?? LIMITS.max_persons_total ?? 12);
        let finalPersons = persons;

        // ====== Máx. personas por reserva ======
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

        // ====== Min adultos / Max niños ======
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

    // ---------- Categories interactions ----------
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

    // ===== PROMO (idéntico a edit) =====
    const APPLY_LABEL  = @json(__('m_bookings.bookings.buttons.apply'));
    const REMOVE_LABEL = @json(__('m_bookings.bookings.buttons.delete') ?? 'Quitar');

    function setPromoButton(mode) {
        const $btn   = $('#btn-verify-promo');
        const $label = $btn.find('.promo-label');
        if (mode === 'remove') {
            $btn.data('mode', 'remove')
                .removeClass('btn-success').addClass('btn-danger');
            $label.length ? $label.text(REMOVE_LABEL) : $btn.text(REMOVE_LABEL);
        } else {
            $btn.data('mode', 'apply')
                .removeClass('btn-danger').addClass('btn-success');
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

    $('#promo_code').on('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            $('#btn-verify-promo').trigger('click');
        }
    });

    $('#promo_code').on('input', function() {
        const val = ($(this).val() || '').trim();
        if (val === '' && $('#btn-verify-promo').data('mode') === 'remove') {
            clearPromoUI(true);
        }
    });

    $('#btn-verify-promo').on('click', function() {
        const $btn = $(this);
        const mode = $btn.data('mode');

        if (mode === 'remove') {
            clearPromoUI(true);
            $('#promo_code').val('');
            return;
        }

        const code = ($('#promo_code').val() || '').trim();
        const { subtotal } = computeSubtotalAndPersons();
        if (!code) { $('#promo-feedback').removeClass('text-success').addClass('text-danger').text('{{ __('m_bookings.bookings.validation.promo_empty') }}'); return; }
        if (subtotal <= 0) { $('#promo-feedback').removeClass('text-success').addClass('text-danger').text('{{ __('m_bookings.bookings.validation.promo_needs_subtotal') }}'); return; }

        const originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span><span class="promo-label">{{ __('m_bookings.bookings.ui.verifying') ?? 'Verificando…' }}</span>');

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
    // ===== /PROMO =====

    // ---------- Locks Hotel vs Meeting Point ----------
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
        if (hasMP) {
            lockHotel('{{ __("m_bookings.bookings.messages.hotel_locked_by_meeting_point") ?? "Se seleccionó un punto de encuentro; no se puede seleccionar hotel." }}');
        } else {
            unlockHotel();
        }
    });

    $('#hotel_id').on('change', function () {
        const val = $(this).val() || '';
        const pickedHotel = val !== '';
        const pickedOther = val === 'other';

        if (pickedOther) {
            $('#other_hotel_wrapper').slideDown();
            $('#is_other_hotel').val('1');
        } else {
            $('#other_hotel_wrapper').slideUp();
            $('#is_other_hotel').val('0');
            $('#other_hotel_name').val('');
        }

        if (pickedHotel || pickedOther) {
            lockMeetingPoint('{{ __("m_bookings.bookings.messages.meeting_point_locked_by_hotel") ?? "Se seleccionó un hotel; no se puede seleccionar punto de encuentro." }}');
        } else {
            unlockMeetingPoint();
        }
    });

    // ---------- Tour change ----------
    $('#tour_id').on('change', function() {
        const tourId = $(this).val();
        resetSelect($('#schedule_id'), @json(__('m_bookings.bookings.ui.select_tour_first')), true);
        resetSelect($('#tour_language_id'), @json(__('m_bookings.bookings.ui.select_tour_first')), true);
        $('#categories-container').html(
          `<div class="alert alert-info mb-0">
             {{ __('m_bookings.bookings.ui.select_tour_to_see_categories') }}
           </div>`
        );
        currentCategories = [];
        // reset promo
        clearPromoUI(false);
        $('#promo_code').val('');

        if (!tourId) return;
        loadSchedules(tourId);
        loadLanguages(tourId);
        loadCategories(tourId);
    });

    // ---------- Inicialización ----------
    if ($('#hotel_id').val() === 'other') { $('#other_hotel_wrapper').show(); }

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

    // Si hay old('tour_id'), recarga desde API y re-aplica old de schedule/language/categorías
    const oldTourId = '{{ old('tour_id') }}';
    if (oldTourId) {
        $('#tour_id').val(oldTourId).trigger('change.select2');
        loadSchedules(oldTourId, '{{ old('schedule_id') }}');
        loadLanguages(oldTourId, '{{ old('tour_language_id') }}');
        loadCategories(oldTourId, @json(old('categories', [])));
    }
  });
  </script>
@stop

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css" rel="stylesheet" />
<style>
/* Corrige fondo blanco solo en Cliente y Tour (selects con clase .no-bg) */
select.no-bg + .select2-container--bootstrap4 .select2-selection--single { background: transparent !important; }
select.no-bg + .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered { background: transparent !important; }
select.no-bg + .select2-container--bootstrap4 .select2-selection--single .select2-selection__placeholder { color: inherit; }
select.no-bg + .select2-container--bootstrap4 .select2-selection--single:focus { box-shadow: none; }

/* Tema oscuro: texto del render del select2 y texto de "plaintext" */
.select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered { color: white !important; }
.form-control-plaintext { color: white; }

/* Sticky solo en ≥LG para evitar solaparse en móviles */
.sticky-lg-top { position: static; }
@media (min-width: 992px) {
  .sticky-lg-top { position: sticky; top: 20px; z-index: 1020; }
}

/* Input-group responsive para el promo:
   En XS el botón ocupa 100%, en ≥SM vuelve a auto */
.w-sm-auto { width: auto; }
@media (max-width: 575.98px) {
  .w-sm-auto { width: 100% !important; }
}

/* Mejoras touch en XS para los controles de categorías */
@media (max-width: 575.98px) {
  .category-row .btn { padding: .45rem .6rem; }
  .category-row input { width: 68px !important; }
}

/* Alineación interna del botón de promo (sin hacks de ancho) */
.btn-promo {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: .4rem;
}
</style>
@stop
