{{-- resources/views/admin/bookings/edit.blade.php --}}
@extends('adminlte::page')

@section('title', __('m_bookings.bookings.ui.edit_booking'))

@php
// Precarga PROMO desde redención real (si existe)
$redemption = $booking->redemption;
$promoModel = optional($redemption)->promoCode;
$initPromoCode = old('promo_code', $promoModel->code ?? '');
$initOp = old('promo_operation', $redemption->operation_snapshot ?? ($promoModel->operation ?? ''));
$initAmount = (float) old('promo_amount', $redemption->applied_amount ?? ($promoModel->discount_amount ?? 0));
$initPercent = old('promo_percent', $redemption->percent_snapshot ?? ($promoModel->discount_percent ?? ''));

// Parse category quantities from booking details
$categoryQuantitiesById = [];
$rawCategories = $booking->detail?->categories;
$categoriesSnapshot = null; // Initialize to prevent undefined variable

if (is_string($rawCategories)) {
try {
$categoriesSnapshot = json_decode($rawCategories, true);
} catch (\Throwable $e) {
$categoriesSnapshot = null;
}
} elseif (is_array($rawCategories)) {
$categoriesSnapshot = $rawCategories;
}

if (is_array($categoriesSnapshot)) {
if (isset($categoriesSnapshot[0]) && is_array($categoriesSnapshot[0])) {
foreach ($categoriesSnapshot as $item) {
$cid = (string)($item['category_id'] ?? $item['id'] ?? '');
if ($cid !== '') {
$categoryQuantitiesById[$cid] = (int)($item['quantity'] ?? 0);
}
}
} else {
foreach ($categoriesSnapshot as $cid => $info) {
$categoryQuantitiesById[(string)$cid] = (int)($info['quantity'] ?? 0);
}
}
}

// Bootstrap promo para JS
$promoBootstrapValue = $initPercent !== '' ? (float) $initPercent : (float) $initAmount;
$promoBootstrapType = $initPercent !== '' ? 'percentage' : ($initAmount ? 'fixed' : null);
$promoBootstrapOperation = $initOp ?: 'subtract';
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

@section('css')
<style>
    .category-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px;
        border-bottom: 1px solid #eee;
    }

    .category-item:last-child {
        border-bottom: none;
    }

    .category-name {
        font-weight: 600;
        flex: 1;
    }

    .category-price {
        font-weight: 500;
        color: #28a745;
        margin-right: 15px;
    }

    .category-controls {
        display: flex;
        align-items: center;
    }

    .category-qty {
        width: 60px;
        text-align: center;
        margin: 0 5px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        padding: 4px;
    }

    .btn-minus,
    .btn-plus {
        width: 30px;
        height: 30px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }

    /* Promo button responsive */
    .promo-input-group .btn {
        white-space: nowrap;
    }

    @media (max-width: 576px) {
        .promo-input-group {
            flex-wrap: wrap;
        }

        .promo-input-group .form-control {
            flex: 1 0 100%;
            margin-bottom: 0.5rem;
        }

        .promo-input-group .input-group-append {
            width: 100%;
        }

        .promo-input-group .btn {
            width: 100%;
        }
    }
</style>
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

                    {{-- Customer: bloqueado (solo lectura) --}}
                    <div class="form-group">
                        <label>{{ __('m_bookings.bookings.fields.customer') }} *</label>
                        @if($booking->user)
                        <input type="text"
                            class="form-control"
                            value="{{ $booking->user->full_name ?? trim(($booking->user->first_name ?? '').' '.($booking->user->last_name ?? '')) }} ({{ $booking->user->email }})"
                            disabled>
                        <input type="hidden" name="user_id" value="{{ $booking->user_id }}">
                        @else
                        {{-- Fallback por si algo raro viene sin user --}}
                        <select name="user_id" id="user_id" class="form-control" required>
                            <option value="">-- {{ __('m_bookings.bookings.ui.select_customer') }} --</option>
                            @foreach($users as $user)
                            <option value="{{ $user->user_id }}"
                                {{ old('user_id', $booking->user_id) == $user->user_id ? 'selected' : '' }}>
                                {{ $user->full_name ?? trim(($user->first_name ?? '').' '.($user->last_name ?? '')) }} ({{ $user->email }})
                            </option>
                            @endforeach
                        </select>
                        @endif
                    </div>

                    {{-- Tour (sin Select2) --}}
                    <div class="form-group">
                        <label for="tour_id">{{ __('m_bookings.bookings.fields.tour') }} *</label>
                        <select name="tour_id" id="tour_id" class="form-control" required>
                            <option value="">-- {{ __('m_bookings.bookings.ui.select_tour') }} --</option>
                            @foreach($tours as $tour)
                            <option value="{{ $tour->tour_id }}"
                                data-tour='@json($tour, JSON_HEX_APOS)'
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
                                    required>
                            </div>
                        </div>

                        {{-- Schedule --}}
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="schedule_id">{{ __('m_bookings.bookings.fields.schedule') }} *</label>
                                <select name="schedule_id" id="schedule_id" class="form-control" required>
                                    <option value="">{{ __('m_bookings.bookings.ui.select_tour_first') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Language --}}
                    <div class="form-group">
                        <label for="tour_language_id">{{ __('m_bookings.bookings.fields.language') }} *</label>
                        <select name="tour_language_id" id="tour_language_id" class="form-control" required>
                            <option value="">{{ __('m_bookings.bookings.ui.select_tour_first') }}</option>
                        </select>
                    </div>

                    {{-- Hotel / Meeting Point --}}
                    <div class="row">
                        <div class="col-12 col-md-6">
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
                                        {{ __('m_bookings.bookings.placeholders.other') }}
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

                        <div class="col-12 col-md-6">
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
                    </div>

                    {{-- Pickup time --}}
                    <div class="form-group">
                        <label for="pickup_time">{{ __('m_bookings.bookings.fields.pickup_time') }}</label>
                        <input
                            type="time"
                            name="pickup_time"
                            id="pickup_time"
                            class="form-control"
                            value="{{ old('pickup_time', optional($booking->detail)->pickup_time ? \Carbon\Carbon::parse($booking->detail->pickup_time)->format('H:i') : '') }}">
                    </div>

                    {{-- Status --}}
                    <div class="form-group">
                        <label for="status">{{ __('m_bookings.bookings.fields.status') }} *</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="pending" {{ old('status', $booking->status) === 'pending'  ? 'selected' : '' }}>
                                {{ __('m_bookings.bookings.statuses.pending') }}
                            </option>
                            <option value="confirmed" {{ old('status', $booking->status) === 'confirmed' ? 'selected' : '' }}>
                                {{ __('m_bookings.bookings.statuses.confirmed') }}
                            </option>
                            <option value="cancelled" {{ old('status', $booking->status) === 'cancelled' ? 'selected' : '' }}>
                                {{ __('m_bookings.bookings.statuses.cancelled') }}
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

        {{-- Right Column: Categories + Totals --}}
        <div class="col-12 col-xl-4">
            <div class="card sticky-lg-top">
                <div class="card-header bg-success text-white">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-users me-2"></i>
                        {{ __('m_bookings.bookings.fields.travelers') }}
                    </h3>
                </div>
                <div class="card-body">
                    <div id="categories-container">
                        <div class="alert alert-primary mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            {{ __('m_bookings.bookings.ui.select_tour_to_see_categories') }}
                        </div>
                    </div>

                    {{-- Promo Code --}}
                    <div class="mt-3 p-3 border rounded">
                        <label for="promo_code" class="mb-1">{{ __('m_bookings.bookings.fields.promo_code') }}</label>
                        <div class="input-group promo-input-group">
                            <input type="text"
                                name="promo_code"
                                id="promo_code"
                                class="form-control"
                                value="{{ $initPromoCode }}"
                                placeholder="PROMO2025">
                            <div class="input-group-append">
                                <button class="btn btn-success btn-sm"
                                    type="button"
                                    id="btn-apply-promo">
                                    {{ __('m_bookings.bookings.buttons.apply') }}
                                </button>
                            </div>
                        </div>
                        <small id="promo-message" class="text-muted d-block mt-1"></small>
                    </div>

                    {{-- Totals --}}
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
    $(function() {
        const fmtMoney = n => '$' + (Number(n || 0)).toFixed(2);
        const locale = @json(app() getLocale());
        const initQtys = @json($categoryQuantitiesById ?? []);

        const $tourSelect = $('#tour_id');
        const $dateInput = $('#tour_date');
        const $scheduleSelect = $('#schedule_id');
        const $languageSelect = $('#tour_language_id');
        const $categoriesContainer = $('#categories-container');
        const $promoBtn = $('#btn-apply-promo');
        const $promoMsg = $('#promo-message');
        const $hotelSelect = $('#hotel_id');
        const $meetingSelect = $('#meeting_point_id');

        const LABEL_APPLY = @json(__('m_bookings.bookings.buttons.apply'));
        const LABEL_REMOVE = @json(__('m_bookings.bookings.buttons.remove_promo'));

        // Bootstrap promo desde backend
        let promoValue = {
            {
                $promoBootstrapValue ? : 0
            }
        };
        let promoType = {
            !!json_encode($promoBootstrapType) !!
        }; // "percentage" | "fixed" | null
        let promoOperation = {
            !!json_encode($promoBootstrapOperation) !!
        }; // "add" | "subtract"
        let promoActive = {
            {
                ($promoBootstrapValue > 0 && $promoBootstrapType) ? 'true' : 'false'
            }
        };

        if (promoActive) {
            $promoBtn
                .text(LABEL_REMOVE)
                .removeClass('btn-success')
                .addClass('btn-danger');
        } else {
            $promoBtn
                .text(LABEL_APPLY)
                .removeClass('btn-danger')
                .addClass('btn-success');
        }

        // Cambio de tour
        $tourSelect.on('change', function() {
            const tourData = $(this).find(':selected').data('tour');
            if (!tourData) return;

            // Horarios
            $scheduleSelect.empty().append(
                '<option value="">{{ __('
                m_bookings.bookings.ui.select_tour_first ') }}</option>'
            );
            if (tourData.schedules && tourData.schedules.length > 0) {
                tourData.schedules.forEach(s => {
                    const startTime = s.start_time ? s.start_time.substring(0, 5) : '';
                    const endTime = s.end_time ? s.end_time.substring(0, 5) : '';
                    $scheduleSelect.append(
                        `<option value="${s.schedule_id}">${startTime} - ${endTime}</option>`
                    );
                });
            }

            // Idiomas
            $languageSelect.empty().append(
                '<option value="">{{ __('
                m_bookings.bookings.ui.select_tour_first ') }}</option>'
            );
            if (tourData.languages && tourData.languages.length > 0) {
                tourData.languages.forEach(l => {
                    $languageSelect.append(
                        `<option value="${l.tour_language_id}">${l.name}</option>`
                    );
                });
            }

            if ($dateInput.val()) {
                loadCategories(tourData);
            }
        });

        // Cambio de fecha
        $dateInput.on('change', function() {
            const tourData = $tourSelect.find(':selected').data('tour');
            if (tourData && $(this).val()) {
                loadCategories(tourData);
            }
        });

        function loadCategories(tourData) {
            const selectedDate = $dateInput.val();
            if (!selectedDate || !tourData.prices) {
                $categoriesContainer.html('<div class="alert alert-warning">Select a date</div>');
                return;
            }

            const selectedDateStr = selectedDate;

            // Filtrar precios por fecha
            let validPrices = tourData.prices.filter(p => {
                if (!p.valid_from && !p.valid_until) return true;

                const validFromStr = p.valid_from ? p.valid_from.substring(0, 10) : null;
                const validUntilStr = p.valid_until ? p.valid_until.substring(0, 10) : null;

                if (validFromStr && selectedDateStr < validFromStr) return false;
                if (validUntilStr && selectedDateStr > validUntilStr) return false;

                return true;
            });

            // Agrupar por categoría, privilegiando rangos con fecha
            const categoryMap = {};
            validPrices.forEach(p => {
                const hasDateRange = p.valid_from && p.valid_until;
                if (!categoryMap[p.category_id] || hasDateRange) {
                    categoryMap[p.category_id] = p;
                }
            });
            validPrices = Object.values(categoryMap);

            if (validPrices.length === 0) {
                $categoriesContainer.html('<div class="alert alert-warning">No prices for selected date</div>');
                return;
            }

            // Render de categorías
            $categoriesContainer.empty();
            validPrices.forEach(price => {
                let categoryName = 'Category';
                if (price.category && price.category.translations) {
                    const translation = price.category.translations.find(t => t.locale === locale);
                    categoryName = translation ?
                        translation.name :
                        (price.category.name || 'Category');
                } else if (price.category) {
                    categoryName = price.category.name || 'Category';
                }

                const finalPrice = price.final_price || price.price || 0;
                const categoryId = price.category_id;
                const currentQty = parseInt(initQtys[categoryId] || 0);

                const html = `
                    <div class="category-item">
                        <span class="category-name">${categoryName}</span>
                        <span class="category-price">${fmtMoney(finalPrice)}</span>
                        <div class="category-controls">
                            <button type="button" class="btn btn-sm btn-secondary btn-minus" data-category="${categoryId}">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number"
                                   name="categories[${categoryId}]"
                                   min="0"
                                   value="${currentQty}"
                                   data-price="${finalPrice}"
                                   class="form-control category-qty">
                            <button type="button" class="btn btn-sm btn-secondary btn-plus" data-category="${categoryId}">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                `;
                $categoriesContainer.append(html);
            });

            // Handlers de +/- y cambio manual
            $('.btn-plus').off('click').on('click', function() {
                const categoryId = $(this).data('category');
                const $input = $(`input[name="categories[${categoryId}]"]`);
                $input.val(parseInt($input.val() || 0) + 1).trigger('input');
            });

            $('.btn-minus').off('click').on('click', function() {
                const categoryId = $(this).data('category');
                const $input = $(`input[name="categories[${categoryId}]"]`);
                const newVal = Math.max(0, parseInt($input.val() || 0) - 1);
                $input.val(newVal).trigger('input');
            });

            $('.category-qty').off('input').on('input', updateTotal);
            updateTotal();
        }

        // Toggle Aplicar / Quitar promo
        $promoBtn.on('click', function() {
            const promoCode = $('#promo_code').val().trim();

            // Si ya está activo, quitar
            if (promoActive) {
                promoValue = 0;
                promoType = null;
                promoOperation = 'subtract';
                promoActive = false;

                $promoMsg
                    .removeClass('text-success text-danger')
                    .addClass('text-muted')
                    .text('');

                $promoBtn
                    .text(LABEL_APPLY)
                    .removeClass('btn-danger')
                    .addClass('btn-success');

                // Recalcular inmediatamente SIN promo
                let persons = 0;
                let subtotal = 0;

                $('.category-qty').each(function() {
                    const qty = parseInt($(this).val()) || 0;
                    const price = parseFloat($(this).data('price')) || 0;
                    persons += qty;
                    subtotal += qty * price;
                });

                $('#discount-row').hide();
                $('#discount-amount').text('-' + fmtMoney(0));

                $('#total-persons').text(persons);
                $('#subtotal-price').text(fmtMoney(subtotal));
                $('#total-price').text(fmtMoney(subtotal));

                return;
            }

            // Si no hay código
            if (!promoCode) {
                $promoMsg
                    .removeClass('text-success')
                    .addClass('text-danger')
                    .text(@json(__('m_bookings.bookings.validation.promo_required')));
                return;
            }

            // Subtotal actual
            let subtotal = 0;
            $('.category-qty').each(function() {
                const qty = parseInt($(this).val()) || 0;
                const price = parseFloat($(this).data('price')) || 0;
                subtotal += qty * price;
            });

            if (subtotal <= 0) {
                $promoMsg
                    .removeClass('text-success')
                    .addClass('text-danger')
                    .text(@json(__('m_bookings.bookings.validation.promo_needs_subtotal')));
                return;
            }

            const originalLabel = $promoBtn.text();
            $promoBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

            $.ajax({
                url: "{{ route('admin.bookings.verifyPromoCode') }}",
                method: 'GET',
                data: {
                    code: promoCode,
                    subtotal: subtotal
                },
                success: function(response) {
                    $promoBtn.prop('disabled', false).text(originalLabel);

                    if (response.valid) {
                        promoValue = response.discount_percent ? response.discount_percent : response.discount_amount;
                        promoType = response.discount_percent ? 'percentage' : 'fixed';
                        promoOperation = response.operation || 'subtract';
                        promoActive = true;

                        const valueText = promoType === 'percentage' ? `${promoValue}%` : `$${promoValue}`;
                        const actionText = promoOperation === 'add' ?
                            @json(__('m_bookings.bookings.ui.surcharge')) :
                            @json(__('m_bookings.bookings.ui.discount'));

                        $promoMsg
                            .removeClass('text-danger')
                            .addClass('text-success')
                            .text(`✓ ${valueText} ${actionText} applied!`);

                        $promoBtn
                            .text(LABEL_REMOVE)
                            .removeClass('btn-success')
                            .addClass('btn-danger');

                        updateTotal();
                    } else {
                        $promoMsg
                            .removeClass('text-success')
                            .addClass('text-danger')
                            .text(response.message || @json(__('m_bookings.bookings.validation.promo_invalid')));

                        promoValue = 0;
                        promoType = null;
                        promoActive = false;

                        $promoBtn
                            .text(LABEL_APPLY)
                            .removeClass('btn-danger')
                            .addClass('btn-success');

                        updateTotal();
                    }
                },
                error: function() {
                    $promoBtn.prop('disabled', false).text(originalLabel);
                    $promoMsg
                        .removeClass('text-success')
                        .addClass('text-danger')
                        .text(@json(__('m_bookings.bookings.validation.promo_error')));

                    promoValue = 0;
                    promoType = null;
                    promoActive = false;

                    $promoBtn
                        .text(LABEL_APPLY)
                        .removeClass('btn-danger')
                        .addClass('btn-success');

                    updateTotal();
                }
            });
        });

        function updateTotal() {
            let persons = 0;
            let subtotal = 0;

            $('.category-qty').each(function() {
                const qty = parseInt($(this).val()) || 0;
                const price = parseFloat($(this).data('price')) || 0;
                persons += qty;
                subtotal += qty * price;
            });

            let promoAmount = 0;
            if (promoActive && promoValue > 0 && subtotal > 0 && promoType) {
                if (promoType === 'percentage') {
                    promoAmount = subtotal * (promoValue / 100);
                } else {
                    promoAmount = parseFloat(promoValue);
                }
            }

            let total = subtotal;
            if (promoAmount > 0) {
                if (promoOperation === 'add') {
                    total += promoAmount;
                } else {
                    promoAmount = Math.min(promoAmount, subtotal);
                    total = Math.max(0, subtotal - promoAmount);
                }
            }

            $('#total-persons').text(persons);
            $('#subtotal-price').text(fmtMoney(subtotal));

            if (promoAmount > 0) {
                $('#discount-row').show();
                const isSurcharge = (promoOperation === 'add');
                const sign = isSurcharge ? '+' : '-';
                const label = isSurcharge ?
                    @json(__('m_bookings.bookings.ui.surcharge')) :
                    @json(__('m_bookings.bookings.ui.discount'));

                $('#discount-row span:first').text(label + ':');
                $('#discount-amount').text(sign + fmtMoney(promoAmount));

                if (isSurcharge) {
                    $('#discount-amount').addClass('text-danger').removeClass('text-success');
                } else {
                    $('#discount-amount').addClass('text-success').removeClass('text-danger');
                }
            } else {
                $('#discount-row').hide();
            }

            $('#total-price').text(fmtMoney(total));
        }

        // ==========================
        // Hotel "other" + exclusión con Meeting Point
        // ==========================

        function syncHotelMeetingInitialState() {
            const hasHotel = $hotelSelect.val() && $hotelSelect.val() !== '';
            const hasMeetingPoint = $meetingSelect.val() && $meetingSelect.val() !== '';

            if (hasHotel) {
                // Hay hotel seleccionado -> bloquear meeting
                $meetingSelect.val('');
                $meetingSelect.prop('disabled', true);
            } else if (hasMeetingPoint) {
                // Hay meeting point -> bloquear hotel y limpiar "other"
                $hotelSelect.val('');
                $hotelSelect.prop('disabled', true);
                $('#other_hotel_wrapper').hide();
                $('#is_other_hotel').val('0');
                $('#other_hotel_name').val('');
            } else {
                // Ninguno -> ambos libres
                $hotelSelect.prop('disabled', false);
                $meetingSelect.prop('disabled', false);
            }

            // Mostrar campo "otro hotel" si corresponde
            if ($hotelSelect.val() === 'other') {
                $('#other_hotel_wrapper').show();
                $('#is_other_hotel').val('1');
            }
        }

        // Cambio en hotel
        $hotelSelect.on('change', function() {
            const val = $(this).val();

            if (val === 'other') {
                $('#other_hotel_wrapper').slideDown();
                $('#is_other_hotel').val('1');
            } else {
                $('#other_hotel_wrapper').slideUp();
                $('#is_other_hotel').val('0');
                if (val !== '') {
                    $('#other_hotel_name').val('');
                }
            }

            if (val) {
                // Hay hotel/hotel other, bloquear meeting point
                $meetingSelect.val('');
                $meetingSelect.prop('disabled', true);
            } else {
                // Se limpió hotel -> habilitar meeting
                $meetingSelect.prop('disabled', false);
            }
        });

        // Cambio en meeting point
        $meetingSelect.on('change', function() {
            const val = $(this).val();

            if (val) {
                // Hay meeting point -> limpiar y bloquear hotel
                $hotelSelect.val('');
                $hotelSelect.prop('disabled', true);

                $('#other_hotel_wrapper').slideUp();
                $('#is_other_hotel').val('0');
                $('#other_hotel_name').val('');
            } else {
                // Se limpió meeting -> habilitar hotel
                $hotelSelect.prop('disabled', false);
            }
        });

        // Estado inicial (hotel / meeting)
        syncHotelMeetingInitialState();

        // Inicializar valores al cargar
        const initialTourId = @json(old('tour_id', $booking tour_id));
        const initialScheduleId = @json(old('schedule_id', optional($booking detail) schedule_id));
        const initialLanguageId = @json(old('tour_language_id', $booking tour_language_id));

        if (initialTourId) {
            $tourSelect.val(initialTourId).trigger('change');

            setTimeout(() => {
                if (initialScheduleId) $scheduleSelect.val(initialScheduleId);
                if (initialLanguageId) $languageSelect.val(initialLanguageId);
                updateTotal();
            }, 200);
        }
    });
</script>
@stop
