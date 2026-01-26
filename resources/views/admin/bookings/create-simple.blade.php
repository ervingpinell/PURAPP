@extends('adminlte::page')

@section('title', __('m_bookings.bookings.ui.add_booking'))

@section('content_header')
<h1>{{ __('m_bookings.bookings.ui.add_booking') }}</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="booking-form-wrapper">
        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h5><i class="icon fas fa-ban"></i> {{ __('m_bookings.bookings.validation.error_title') ?? 'Error!' }}</h5>
            <ul>
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Debug: Check if capacity_error session exists --}}
        @if(session('capacity_error'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h5><i class="icon fas fa-exclamation-triangle"></i> DEBUG: Capacity Error Detected</h5>
            <p>{{ session('capacity_error')['message'] ?? 'No message' }}</p>
            <p>Available: {{ session('capacity_error')['available'] ?? 'N/A' }}, Requested: {{ session('capacity_error')['requested'] ?? 'N/A' }}</p>
        </div>
        @endif

        <form id="booking-form" method="POST" action="{{ route('admin.bookings.store') }}">
            @csrf
            <input type="hidden" name="status" value="pending">

            {{-- Tour Cover Image --}}
            <div id="tour-cover-container" style="display:none;" class="mb-3">
                <img id="tour-cover" src="" alt="Tour Cover" class="tour-cover-img">
            </div>

            {{-- Step 1: Tour & Date --}}
            <div class="step-card">
                <div class="step-header">
                    <i class="fas fa-map-marked-alt mr-2"></i>
                    <span>1. {{ __('m_bookings.bookings.steps.select_tour_date') ?? 'Select Tour & Date' }}</span>
                </div>
                <div class="step-body">
                    <div class="row">
                        <div class="col-md-6 col-12 mb-3">
                            <label for="product_id">{{ __('m_bookings.bookings.fields.tour') }} *</label>
                            <select name="product_id" id="product_id" class="form-control select2" required>
                                <option value="">-- {{ __('m_bookings.bookings.ui.select_tour') }} --</option>
                                @foreach($tours as $tour)
                                <option value="{{ $tour->product_id }}"
                                    data-tour='@json($tour)'
                                    data-cover="{{ $tour->cover_image ?? '' }}"
                                    {{ old('product_id') == $tour->product_id ? 'selected' : '' }}>
                                    {{ $tour->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 col-12 mb-3">
                            <label for="tour_date">{{ __('m_bookings.bookings.fields.date') }} *</label>
                            <input type="date"
                                name="tour_date"
                                id="tour_date"
                                class="form-control"
                                value="{{ old('tour_date') }}"
                                required>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Step 2: Schedule & Language --}}
            <div class="step-card" id="step2" style="display:none;">
                <div class="step-header step-header-info">
                    <i class="fas fa-clock mr-2"></i>
                    <span>2. {{ __('m_bookings.bookings.steps.select_schedule_language') ?? 'Select Schedule & Language' }}</span>
                </div>
                <div class="step-body">
                    <div class="row">
                        <div class="col-md-6 col-12 mb-3">
                            <label for="schedule_id">{{ __('m_bookings.bookings.fields.schedule') }} *</label>
                            <select name="schedule_id" id="schedule_id" class="form-control" required>
                                <option value="">-- {{ __('m_bookings.bookings.ui.select_schedule') }} --</option>
                            </select>
                        </div>
                        <div class="col-md-6 col-12 mb-3">
                            <label for="tour_language_id">{{ __('m_bookings.bookings.fields.language') }} *</label>
                            <select name="tour_language_id" id="tour_language_id" class="form-control" required>
                                <option value="">-- {{ __('m_bookings.bookings.ui.select_language') }} --</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Step 3: Categories --}}
            <div class="step-card" id="step3" style="display:none;">
                <div class="step-header step-header-success">
                    <i class="fas fa-users mr-2"></i>
                    <span>3. {{ __('m_bookings.bookings.steps.select_participants') ?? 'Select Participants' }}</span>
                </div>
                <div class="step-body">
                    <div id="categories-container"></div>

                    {{-- Promo Code --}}
                    <div class="mt-3">
                        <label for="promo_code">{{ __('m_bookings.bookings.fields.promo_code') ?? 'Promo Code' }}</label>
                        <div class="input-group">
                            <input type="text" name="promo_code" id="promo_code" class="form-control" value="{{ old('promo_code') }}" placeholder="{{ __('m_bookings.bookings.ui.enter_promo') ?? 'Enter promo code' }}">
                            <div class="input-group-append">
                                <button type="button" id="btn-apply-promo" class="btn btn-success">
                                    <i class="fas fa-check"></i> {{ __('m_bookings.actions.apply') ?? 'Apply' }}
                                </button>
                                <button type="button" id="btn-remove-promo" class="btn btn-danger" style="display:none;">
                                    <i class="fas fa-times"></i> {{ __('m_bookings.actions.remove') ?? 'Remove' }}
                                </button>
                            </div>
                        </div>
                        <small id="promo-message" class="form-text"></small>
                    </div>

                    {{-- Price Breakdown --}}
                    <div class="total-display">
                        <div class="price-breakdown">
                            <div class="price-row">
                                <span>{{ __('m_bookings.bookings.ui.subtotal') ?? 'Subtotal' }}:</span>
                                <span id="subtotal-amount">$0.00</span>
                            </div>
                            <div class="price-row" id="discount-row" style="display:none;">
                                <span>{{ __('m_bookings.bookings.ui.discount') ?? 'Discount' }}:</span>
                                <span id="discount-amount" class="text-success">-$0.00</span>
                            </div>
                            <div class="price-row" id="tax-row" style="display:none;">
                                <span>{{ __('m_bookings.bookings.ui.tax') ?? 'Tax' }} (<span id="tax-rate">0</span>%):</span>
                                <span id="tax-amount">$0.00</span>
                            </div>
                            <div class="price-row total-row">
                                <strong>{{ __('m_bookings.bookings.ui.total') }}:</strong>
                                <strong><span id="total-amount" class="total-amount">$0.00</span></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Step 4: Customer & Details --}}
            <div class="step-card" id="step4" style="display:none;">
                <div class="step-header step-header-warning">
                    <i class="fas fa-user mr-2"></i>
                    <span>4. {{ __('m_bookings.bookings.steps.customer_details') ?? 'Customer & Details' }}</span>
                </div>
                <div class="step-body">
                    <div class="row">
                        <div class="col-md-6 col-12 mb-3">
                            <label for="user_id">{{ __('m_bookings.bookings.fields.customer') }} *</label>
                            <select name="user_id" id="user_id" class="form-control select2" required>
                                <option value="">-- {{ __('m_bookings.bookings.ui.select_customer') }} --</option>
                                @foreach($customers as $customer)
                                <option value="{{ $customer->user_id }}" {{ old('user_id') == $customer->user_id ? 'selected' : '' }}>
                                    {{ $customer->full_name }} ({{ $customer->email }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Hotel or Meeting Point (mutually exclusive) --}}
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label>{{ __('m_bookings.bookings.fields.pickup_location') ?? 'Pickup Location' }}</label>
                            <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                                <label class="btn btn-outline-secondary active">
                                    <input type="radio" name="pickup_type" value="none" checked> {{ __('m_bookings.bookings.ui.no_pickup') ?? 'No Pickup' }}
                                </label>
                                <label class="btn btn-outline-secondary">
                                    <input type="radio" name="pickup_type" value="hotel"> {{ __('m_bookings.bookings.ui.hotel') ?? 'Hotel' }}
                                </label>
                                <label class="btn btn-outline-secondary">
                                    <input type="radio" name="pickup_type" value="meeting_point"> {{ __('m_bookings.bookings.ui.meeting_point') ?? 'Meeting Point' }}
                                </label>
                            </div>
                        </div>
                    </div>

                    <div id="hotel-section" style="display:none;">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="hotel_id">{{ __('m_bookings.bookings.fields.hotel') }}</label>
                                <select name="hotel_id" id="hotel_id" class="form-control">
                                    <option value="">-- {{ __('m_bookings.bookings.ui.select_hotel') }} --</option>
                                    @foreach($hotels as $hotel)
                                    <option value="{{ $hotel->hotel_id }}" {{ old('hotel_id') == $hotel->hotel_id ? 'selected' : '' }}>
                                        {{ $hotel->name }}
                                    </option>
                                    @endforeach
                                    <option value="other" {{ old('hotel_id') == 'other' ? 'selected' : '' }}>
                                        {{ __('m_bookings.bookings.ui.other_hotel') ?? 'Other' }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div id="other-hotel-wrapper" style="display:none;">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label for="other_hotel_name">{{ __('m_bookings.bookings.fields.other_hotel_name') }}</label>
                                    <input type="text" name="other_hotel_name" id="other_hotel_name" class="form-control" value="{{ old('other_hotel_name') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="meeting-point-section" style="display:none;">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="meeting_point_id">{{ __('m_bookings.bookings.fields.meeting_point') }}</label>
                                <select name="meeting_point_id" id="meeting_point_id" class="form-control text-dark">
                                    <option value="">-- {{ __('m_bookings.bookings.ui.select_meeting_point') }} --</option>
                                    @foreach($meetingPoints as $mp)
                                    <option value="{{ $mp->meeting_point_id }}" {{ old('meeting_point_id') == $mp->meeting_point_id ? 'selected' : '' }}>
                                        {{ $mp->getTranslated('name') }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="notes">{{ __('m_bookings.bookings.fields.notes') }}</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Submit Button --}}
            <div class="text-center mt-4 mb-5 pb-4" id="submit-section" style="display:none;">
                <div class="button-group">
                    <button type="button" id="btn-review" class="btn btn-success btn-lg mb-2">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ __('m_bookings.actions.review_booking') ?? 'Review Booking' }}
                    </button>
                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary btn-lg mb-2">
                        <i class="fas fa-times mr-2"></i>
                        {{ __('m_bookings.actions.cancel') ?? 'Cancel' }}
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Confirmation Modal --}}
    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('m_bookings.bookings.ui.confirm_booking') ?? 'Confirm Booking' }}</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="booking-summary"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        {{ __('m_bookings.actions.cancel') ?? 'Cancel' }}
                    </button>
                    <button type="button" id="btn-confirm-submit" class="btn btn-success">
                        <i class="fas fa-save mr-2"></i>
                        {{ __('m_bookings.actions.confirm_create') ?? 'Confirm & Create' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@stop

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css" rel="stylesheet" />
<style>
    /* Centered wrapper for entire form */
    .booking-form-wrapper {
        max-width: 900px;
        margin: 0 auto;
        padding: 0 15px;
    }

    /* Dark theme compatible styles */
    .step-card {
        background: transparent;
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        margin-bottom: 15px;
        overflow: hidden;
    }

    .step-header {
        background: rgba(52, 152, 219, 0.2);
        border-bottom: 1px solid rgba(52, 152, 219, 0.3);
        padding: 10px 15px;
        color: #fff;
        font-weight: 600;
        font-size: 1em;
    }

    .step-header-info {
        background: rgba(23, 162, 184, 0.2);
        border-bottom-color: rgba(23, 162, 184, 0.3);
    }

    .step-header-success {
        background: rgba(40, 167, 69, 0.2);
        border-bottom-color: rgba(40, 167, 69, 0.3);
    }

    .step-header-warning {
        background: rgba(255, 193, 7, 0.2);
        border-bottom-color: rgba(255, 193, 7, 0.3);
    }

    .step-body {
        padding: 15px;
    }

    .tour-cover-img {
        width: 100%;
        max-height: 250px;
        object-fit: cover;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
    }

    #categories-container {
        max-width: 800px;
        margin: 0 auto;
    }

    .category-item {
        display: grid;
        grid-template-columns: 1fr auto auto;
        align-items: center;
        gap: 15px;
        padding: 12px 15px;
        margin-bottom: 10px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 5px;
        background: rgba(255, 255, 255, 0.05);
    }

    .category-item:hover {
        background: rgba(255, 255, 255, 0.08);
    }

    .category-name {
        font-weight: 600;
        color: #fff;
        font-size: 0.95em;
    }

    .category-price {
        color: #28a745;
        font-size: 1.05em;
        font-weight: 600;
        min-width: 80px;
        text-align: right;
    }

    .category-controls {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .category-qty {
        width: 60px;
        text-align: center;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #fff;
        padding: 5px;
        font-size: 0.9em;
    }

    .total-display {
        margin-top: 15px;
        padding: 12px;
        background: rgba(40, 167, 69, 0.2);
        border: 1px solid rgba(40, 167, 69, 0.3);
        border-radius: 5px;
        color: #fff;
    }

    .price-breakdown {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .price-row {
        display: flex;
        justify-content: space-between;
        padding: 3px 0;
        font-size: 0.9em;
    }

    .total-row {
        border-top: 2px solid rgba(255, 255, 255, 0.3);
        padding-top: 8px;
        margin-top: 5px;
        font-size: 1.05em;
    }

    .total-amount {
        color: #28a745;
        font-size: 1.2em;
    }

    label {
        color: rgba(255, 255, 255, 0.9);
        font-weight: 500;
    }

    .form-control {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #fff;
    }

    .form-control:focus {
        background: rgba(255, 255, 255, 0.15);
        border-color: rgba(52, 152, 219, 0.5);
        color: #fff;
    }

    .form-control option {
        background: #343a40;
        color: #fff;
    }

    .btn-outline-secondary {
        color: rgba(255, 255, 255, 0.8);
        border-color: rgba(255, 255, 255, 0.2);
    }

    .btn-outline-secondary.active,
    .btn-outline-secondary:hover {
        background: rgba(52, 152, 219, 0.3);
        border-color: rgba(52, 152, 219, 0.5);
        color: #fff;
    }

    .modal-content {
        background: #2d3748;
        color: #fff;
    }

    .modal-header {
        border-bottom-color: rgba(255, 255, 255, 0.1);
    }

    .modal-footer {
        border-top-color: rgba(255, 255, 255, 0.1);
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .step-header {
            font-size: 1em;
            padding: 12px 15px;
        }

        .step-body {
            padding: 15px;
        }

        .category-item {
            grid-template-columns: 1fr;
            gap: 10px;
            text-align: center;
        }

        .category-name {
            text-align: center;
        }

        .category-price {
            text-align: center;
        }

        .category-controls {
            justify-content: center;
        }

        .tour-cover-img {
            max-height: 200px;
        }
    }

    .button-group {
        display: flex;
        gap: 10px;
        justify-content: center;
        flex-wrap: wrap;
    }

    @media (max-width: 576px) {
        .btn-lg {
            font-size: 0.95rem;
            padding: 0.6rem 1.2rem;
            width: 100%;
            max-width: 300px;
        }

        .button-group {
            flex-direction: column;
            align-items: center;
        }

        .total-amount {
            font-size: 1.2em;
        }

        .input-group-append .btn {
            font-size: 0.85rem;
            padding: 0.375rem 0.75rem;
        }
    }
</style>
{{-- Capacity Confirmation Modal --}}
<div class="modal fade" id="capacityModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content bg-warning">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> {{ __('m_bookings.bookings.validation.capacity_warning') ?? 'Capacity Warning' }}</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="capacity-message"></p>
                <p>{{ __('m_bookings.bookings.validation.force_question') ?? 'Do you want to force this booking anyway?' }}</p>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-outline-dark" data-dismiss="modal">{{ __('m_bookings.actions.cancel') ?? 'Cancel' }}</button>
                <button type="button" id="btn-force-submit" class="btn btn-outline-dark">
                    <i class="fas fa-check"></i> {{ __('m_bookings.actions.yes_force') ?? 'Yes, Force Booking' }}
                </button>
            </div>
        </div>
    </div>
</div>

@if(session('capacity_error'))
<script>
    $(document).ready(function() {
        const error = @json(session('capacity_error'));
        $('#capacity-message').text(error.message);
        $('#capacityModal').modal('show');

        $('#btn-force-submit').on('click', function() {
            // Add hidden input for force
            $('<input>').attr({
                type: 'hidden',
                name: 'force_capacity',
                value: '1'
            }).appendTo('#booking-form');

            $('#capacityModal').modal('hide');
            $('#booking-form').submit();
        });
    });
</script>
@endif
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    console.log('Script block loaded');
    $(document).ready(function() {
        console.log('Document ready fired');
        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%'
        });

        const $tourSelect = $('#product_id');
        const $dateInput = $('#tour_date');
        const $scheduleSelect = $('#schedule_id');
        const $languageSelect = $('#tour_language_id');
        const $categoriesContainer = $('#categories-container');
        const $hotelSelect = $('#hotel_id');
        const $pickupType = $('input[name="pickup_type"]');

        // When tour is selected
        $tourSelect.on('change', function() {
            const selectedOption = $(this).find(':selected');
            const tourData = selectedOption.data('tour');
            const coverImage = selectedOption.data('cover');

            // Show cover image
            if (coverImage) {
                $('#tour-cover').attr('src', coverImage);
                $('#tour-cover-container').fadeIn();
            } else {
                $('#tour-cover-container').hide();
            }

            if (!tourData) {
                $('#step2, #step3, #step4, #submit-section').hide();
                return;
            }

            // Populate schedules
            $scheduleSelect.empty().append('<option value="">-- Select Schedule --</option>');
            if (tourData.schedules && tourData.schedules.length > 0) {
                tourData.schedules.forEach(s => {
                    const startTime = s.start_time ? s.start_time.substring(0, 5) : '';
                    const endTime = s.end_time ? s.end_time.substring(0, 5) : '';
                    $scheduleSelect.append(`<option value="${s.schedule_id}">${startTime} - ${endTime}</option>`);
                });
            }

            // Populate languages
            $languageSelect.empty().append('<option value="">-- Select Language --</option>');
            if (tourData.languages && tourData.languages.length > 0) {
                tourData.languages.forEach(l => {
                    $languageSelect.append(`<option value="${l.tour_language_id}">${l.name}</option>`);
                });
            }

            $('#step2').slideDown();

            if ($dateInput.val()) {
                loadCategories(tourData);
            }
        });

        // When date is selected
        $dateInput.on('change', function() {
            const selectedOption = $tourSelect.find(':selected');
            const tourData = selectedOption.data('tour');

            if (!tourData || !$(this).val()) {
                $('#step3, #step4, #submit-section').hide();
                return;
            }

            loadCategories(tourData);
        });

        function loadCategories(tourData) {
            const selectedDate = new Date($dateInput.val());
            const locale = '{{ app()->getLocale() }}';

            if (!tourData.prices || tourData.prices.length === 0) {
                $categoriesContainer.html('<div class="alert alert-warning">No categories available</div>');
                return;
            }

            // Filter prices by date
            const selectedDateStr = $dateInput.val();

            let validPrices = tourData.prices.filter(p => {
                // No dates = always valid (default price)
                if (!p.valid_from && !p.valid_until) return true;

                // Normalize dates to YYYY-MM-DD strings for comparison
                // p.valid_from is likely "YYYY-MM-DD..." string from JSON
                const validFromStr = p.valid_from ? p.valid_from.substring(0, 10) : null;
                const validUntilStr = p.valid_until ? p.valid_until.substring(0, 10) : null;

                // Check if selected date is within range
                // String comparison works for ISO dates: "2025-12-02" < "2025-12-03" is true
                if (validFromStr && selectedDateStr < validFromStr) return false; // Before start
                if (validUntilStr && selectedDateStr > validUntilStr) return false; // After end

                return true; // Within range or open-ended
            });

            // Group by category
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

            // Render categories
            $categoriesContainer.empty();
            validPrices.forEach(price => {
                let categoryName = 'Category';
                if (price.category && price.category.translations) {
                    const translation = price.category.translations.find(t => t.locale === locale);
                    categoryName = translation ? translation.name : (price.category.name || 'Category');
                } else if (price.category) {
                    categoryName = price.category.name || 'Category';
                }

                const finalPrice = price.final_price || price.price || 0;

                const html = `
                <div class="category-item">
                    <span class="category-name">${categoryName}</span>
                    <span class="category-price">$${parseFloat(finalPrice).toFixed(2)}</span>
                    <div class="category-controls">
                        <button type="button" class="btn btn-sm btn-secondary btn-minus" data-category="${price.category_id}">
                            <i class="fas fa-minus"></i>
                        </button>
                        <input type="number"
                               name="categories[${price.category_id}]"
                               min="0"
                               value="0"
                               data-price="${finalPrice}"
                               data-name="${categoryName}"
                               class="form-control category-qty">
                        <button type="button" class="btn btn-sm btn-secondary btn-plus" data-category="${price.category_id}">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
            `;
                $categoriesContainer.append(html);
            });

            $('.btn-plus').on('click', function() {
                const categoryId = $(this).data('category');
                const $input = $(`input[name="categories[${categoryId}]"]`);
                $input.val(parseInt($input.val() || 0) + 1).trigger('input');
            });

            $('.btn-minus').on('click', function() {
                const categoryId = $(this).data('category');
                const $input = $(`input[name="categories[${categoryId}]"]`);
                const newVal = Math.max(0, parseInt($input.val() || 0) - 1);
                $input.val(newVal).trigger('input');
            });

            $('.category-qty').on('input', updateTotal);

            $('#step3').slideDown();
            $('#step4').slideDown();
            $('#submit-section').slideDown();
        }

        // Pickup type toggle
        $pickupType.on('change', function() {
            const type = $(this).val();
            $('#hotel-section, #meeting-point-section').hide();
            $('#hotel_id, #meeting_point_id').val('').prop('required', false);

            if (type === 'hotel') {
                $('#hotel-section').slideDown();
                $('#hotel_id').prop('required', true);
            } else if (type === 'meeting_point') {
                $('#meeting-point-section').slideDown();
                $('#meeting_point_id').prop('required', true);
            }
        });

        // Hotel "other" handling
        $hotelSelect.on('change', function() {
            if ($(this).val() === 'other') {
                $('#other-hotel-wrapper').slideDown();
                $('#other_hotel_name').prop('required', true);
            } else {
                $('#other-hotel-wrapper').slideUp();
                $('#other_hotel_name').prop('required', false);
            }
        });

        // Promo code functionality
        let promoValue = 0;
        let promoType = 'fixed'; // 'fixed' or 'percentage'
        let promoOperation = 'subtract'; // 'add' or 'subtract'

        $('#btn-apply-promo').on('click', function() {
            const promoCode = $('#promo_code').val().trim();
            const $btn = $(this);

            if (!promoCode) {
                $('#promo-message').removeClass('text-success').addClass('text-danger').text('Please enter a promo code');
                return;
            }

            // Calculate current subtotal for validation
            let subtotal = 0;
            $('.category-qty').each(function() {
                const qty = parseInt($(this).val()) || 0;
                const price = parseFloat($(this).data('price')) || 0;
                subtotal += qty * price;
            });

            if (subtotal <= 0) {
                $('#promo-message').removeClass('text-success').addClass('text-danger').text('Please select participants first');
                return;
            }

            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Checking...');

            $.ajax({
                url: "{{ route('admin.bookings.verifyPromoCode') }}",
                method: 'GET',
                data: {
                    code: promoCode,
                    subtotal: subtotal
                },
                success: function(response) {
                    $btn.prop('disabled', false).html('<i class="fas fa-check"></i> Apply');

                    if (response.valid) {
                        promoValue = response.discount_percent ? response.discount_percent : response.discount_amount;
                        promoType = response.discount_percent ? 'percentage' : 'fixed';
                        promoOperation = response.operation || 'subtract';

                        const valueText = promoType === 'percentage' ? `${promoValue}%` : `$${promoValue}`;
                        const actionText = promoOperation === 'add' ? 'surcharge' : 'discount';

                        $('#promo-message').removeClass('text-danger').addClass('text-success').text(`✓ ${valueText} ${actionText} applied!`);
                        $('#btn-apply-promo').hide();
                        $('#btn-remove-promo').show();
                        $('#promo_code').prop('readonly', true);
                        updateTotal();
                    } else {
                        $('#promo-message').removeClass('text-success').addClass('text-danger').text(response.message || 'Invalid promo code');
                        promoValue = 0;
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).html('<i class="fas fa-check"></i> Apply');
                    $('#promo-message').removeClass('text-success').addClass('text-danger').text('Error validating code');
                    promoValue = 0;
                }
            });
        });

        $('#btn-remove-promo').on('click', function() {
            promoValue = 0;
            $('#promo_code').val('').prop('readonly', false);
            $('#promo-message').text('');
            $('#btn-remove-promo').hide();
            $('#btn-apply-promo').show();
            updateTotal();
        });

        function updateTotal() {
            // 1. Calculate Raw Total (Price * Qty)
            let rawTotal = 0;
            let totalQty = 0;
            $('.category-qty').each(function() {
                const qty = parseInt($(this).val()) || 0;
                const price = parseFloat($(this).data('price')) || 0;
                rawTotal += qty * price;
                totalQty += qty;
            });

            // 2. Get Tax Configuration
            const tourData = $('#product_id option:selected').data('tour');
            const taxes = tourData && tourData.taxes ? tourData.taxes : [];

            // 3. Calculate Base Amount (Remove Inclusive Taxes)
            let baseAmount = rawTotal;
            let inclusiveTaxRateTotal = 0;
            let inclusiveFixedTotal = 0;

            taxes.forEach(tax => {
                if (tax.is_inclusive) {
                    if (tax.type === 'percentage') {
                        inclusiveTaxRateTotal += parseFloat(tax.rate);
                    } else if (tax.type === 'fixed') {
                        inclusiveFixedTotal += parseFloat(tax.rate) * totalQty;
                    }
                }
            });

            // Remove fixed inclusive taxes first
            baseAmount -= inclusiveFixedTotal;
            // Remove percentage inclusive taxes
            if (inclusiveTaxRateTotal > 0) {
                baseAmount = baseAmount / (1 + (inclusiveTaxRateTotal / 100));
            }

            // 4. Apply Promo (Discount or Surcharge) to Base Amount
            let promoAmount = 0;
            if (promoValue > 0) {
                if (promoType === 'percentage') {
                    promoAmount = baseAmount * (promoValue / 100);
                } else {
                    promoAmount = parseFloat(promoValue);
                }
            }

            let adjustedBase = baseAmount;

            if (promoOperation === 'add') {
                adjustedBase += promoAmount;
            } else {
                // Subtract (Discount)
                // Ensure discount doesn't exceed base amount
                promoAmount = Math.min(promoAmount, baseAmount);
                adjustedBase = Math.max(0, baseAmount - promoAmount);
            }

            // 5. Calculate Tax Amounts (on adjusted base)
            let totalTaxAmount = 0;
            let taxDetailsHtml = '';

            taxes.forEach(tax => {
                let taxAmount = 0;
                if (tax.type === 'percentage') {
                    taxAmount = adjustedBase * (parseFloat(tax.rate) / 100);
                } else {
                    taxAmount = parseFloat(tax.rate) * totalQty;
                }

                totalTaxAmount += taxAmount;

                const label = tax.is_inclusive ? `${tax.name} (Included)` : tax.name;
                taxDetailsHtml += `
                    <div class="price-row dynamic-tax-row">
                        <span>${label} (${parseFloat(tax.rate)}%):</span>
                        <span>$${taxAmount.toFixed(2)}</span>
                    </div>
                `;
            });

            // 6. Calculate Final Total
            const finalTotal = adjustedBase + totalTaxAmount;

            // Update Display
            $('#subtotal-amount').text('$' + baseAmount.toFixed(2));

            if (promoAmount > 0) {
                $('#discount-row').show();
                const sign = promoOperation === 'add' ? '+' : '-';
                const label = promoOperation === 'add' ? "{{ __('m_bookings.bookings.ui.surcharge') ?? 'Surcharge' }}" : "{{ __('m_bookings.bookings.ui.discount') ?? 'Discount' }}";
                const colorClass = promoOperation === 'add' ? 'text-danger' : 'text-success';

                // Update label text if possible, or just value
                $('#discount-row span:first').text(label + ':');
                $('#discount-amount').removeClass('text-success text-danger text-warning').addClass(colorClass).text(sign + '$' + promoAmount.toFixed(2));
            } else {
                $('#discount-row').hide();
                // Reset label
                $('#discount-row span:first').text("{{ __('m_bookings.bookings.ui.discount') ?? 'Discount' }}:");
            }

            // Update Tax Section
            if (taxes.length > 0) {
                $('#tax-row').hide(); // Hide the generic tax row
                $('.dynamic-tax-row').remove();
                $(taxDetailsHtml).insertBefore('.total-row');
            } else {
                $('#tax-row').hide();
                $('.dynamic-tax-row').remove();
            }

            $('#total-amount').text('$' + finalTotal.toFixed(2));
        }
        $('#btn-review').on('click', function() {
            console.log('Review button clicked');
            const $btn = $(this);

            // 1. Validate all required fields
            const requiredFields = {
                'user_id': '{{ __("m_bookings.bookings.fields.customer") }}',
                'product_id': '{{ __("m_bookings.bookings.fields.tour") }}',
                'tour_date': '{{ __("m_bookings.bookings.fields.tour_date") }}',
                'schedule_id': '{{ __("m_bookings.bookings.fields.schedule") }}',
                'tour_language_id': '{{ __("m_bookings.bookings.fields.language") }}'
            };

            let missingFields = [];
            for (const [fieldId, fieldLabel] of Object.entries(requiredFields)) {
                const value = $('#' + fieldId).val();
                if (!value || value === '') {
                    missingFields.push(fieldLabel);
                }
            }

            if (missingFields.length > 0) {
                alert('{{ __("m_bookings.bookings.validation.required_fields") ?? "Please fill in all required fields" }}: ' + missingFields.join(', '));
                return;
            }

            // 2. Validate at least one category has quantity > 0
            let totalQty = 0;
            $('.category-qty').each(function() {
                totalQty += parseInt($(this).val()) || 0;
            });

            if (totalQty === 0) {
                alert('{{ __("m_bookings.bookings.validation.select_categories") ?? "Please select at least one participant category" }}');
                return;
            }

            // 3. Validate pickup type selection
            const pickupType = $('input[name="pickup_type"]:checked').val();
            if (!pickupType) {
                alert('{{ __("m_bookings.bookings.validation.select_pickup") ?? "Please select a pickup option" }}');
                return;
            }

            // 4. Validate pickup details based on type
            if (pickupType === 'hotel') {
                const hotelId = $('#hotel_id').val();
                const otherHotelName = $('#other_hotel_name').val();
                if (!hotelId && !otherHotelName) {
                    alert('{{ __("m_bookings.bookings.validation.select_hotel_or_other") ?? "Please select a hotel or enter other hotel name" }}');
                    return;
                }
                if (hotelId === 'other' && !otherHotelName) {
                    alert('{{ __("m_bookings.bookings.validation.enter_other_hotel") ?? "Please enter the hotel name" }}');
                    return;
                }
            } else if (pickupType === 'meeting_point') {
                const meetingPointId = $('#meeting_point_id').val();
                if (!meetingPointId) {
                    alert('{{ __("m_bookings.bookings.validation.select_meeting_point") ?? "Please select a meeting point" }}');
                    return;
                }
            }

            // 5. Check if promo code is entered but not applied
            const promoCodeInput = $('#promo_code').val().trim();
            if (promoCodeInput && promoValue === 0) {
                alert("{{ __('m_bookings.validation.promo_apply_required') ?? 'Please click Apply to validate your promo code first.' }}");
                return;
            }

            // 6. Validate Capacity via AJAX
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> {{ __("m_bookings.bookings.ui.verifying") ?? "Verifying..." }}');

            // Collect form data for validation
            const formData = $('#booking-form').serializeArray();
            // Convert to object for easier handling if needed, but serializeArray works for jQuery ajax data

            $.ajax({
                url: "{{ route('admin.bookings.validate_capacity') }}",
                method: 'POST',
                data: formData,
                success: function(response) {
                    $btn.prop('disabled', false).html('<i class="fas fa-check-circle mr-2"></i> {{ __("m_bookings.actions.review_booking") ?? "Review Booking" }}');

                    // Reset force capacity flag
                    $('input[name="force_capacity"]').remove();

                    if (response.success) {
                        // No capacity issues
                        showReviewModal(null);
                    } else {
                        // Capacity exceeded - show warning in modal
                        // Add hidden input to force capacity if user confirms
                        $('<input>').attr({
                            type: 'hidden',
                            name: 'force_capacity',
                            value: '1'
                        }).appendTo('#booking-form');

                        showReviewModal(response.message);
                    }
                },
                error: function(xhr) {
                    $btn.prop('disabled', false).html('<i class="fas fa-check-circle mr-2"></i> {{ __("m_bookings.actions.review_booking") ?? "Review Booking" }}');
                    console.error('Capacity validation error:', xhr);
                    // If validation fails (e.g. 422), show error
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        let errorMsg = 'Validation Error:\n';
                        for (const field in errors) {
                            errorMsg += `- ${errors[field][0]}\n`;
                        }
                        alert(errorMsg);
                    } else {
                        alert('Error checking capacity. Please try again.');
                    }
                }
            });
        });

        function showReviewModal(warningMessage) {
            // Build summary HTML (same as before)
            const tourName = $('#product_id option:selected').text();
            const tourDate = $('#tour_date').val();
            const schedule = $('#schedule_id option:selected').text();
            const language = $('#tour_language_id option:selected').text();
            const customer = $('#user_id option:selected').text();
            const notes = $('#notes').val();
            const pickupType = $('input[name="pickup_type"]:checked').val();

            let pickupInfo = "{{ __('m_bookings.bookings.ui.no_pickup') ?? 'No Pickup' }}";
            if (pickupType === 'hotel') {
                const hotelName = $('#hotel_id option:selected').text();
                pickupInfo = hotelName === 'Other' ? $('#other_hotel_name').val() : hotelName;
            } else if (pickupType === 'meeting_point') {
                pickupInfo = $('#meeting_point_id option:selected').text();
            }

            let categoriesHtml = '';
            $('.category-qty').each(function() {
                const qty = parseInt($(this).val()) || 0;
                if (qty > 0) {
                    const name = $(this).data('name');
                    const price = parseFloat($(this).data('price'));
                    const subtotal = qty * price;
                    categoriesHtml += `<tr><td>${name}</td><td>${qty}</td><td>$${price.toFixed(2)}</td><td>$${subtotal.toFixed(2)}</td></tr>`;
                }
            });

            const subtotalDisplay = $('#subtotal-amount').text();
            const totalDisplay = $('#total-amount').text();

            let promoHtml = '';
            if (promoValue > 0) {
                const promoLabel = promoOperation === 'add' ? "{{ __('m_bookings.bookings.ui.surcharge') ?? 'Surcharge' }}" : "{{ __('m_bookings.bookings.ui.discount') ?? 'Discount' }}";
                const promoClass = promoOperation === 'add' ? 'text-danger' : 'text-success';
                const promoAmountDisplay = $('#discount-amount').text();
                const promoCode = $('#promo_code').val();
                promoHtml = `<tr><th>${promoLabel} (${promoCode})</th><td class="${promoClass}">${promoAmountDisplay}</td></tr>`;
            }

            let taxesHtml = '';
            $('.dynamic-tax-row').each(function() {
                const label = $(this).find('span:first').text();
                const amount = $(this).find('span:last').text();
                taxesHtml += `<tr><th>${label}</th><td>${amount}</td></tr>`;
            });

            let warningHtml = '';
            if (warningMessage) {
                warningHtml = `
                    <div class="alert alert-warning">
                        <h5><i class="fas fa-exclamation-triangle"></i> {{ __('m_bookings.bookings.validation.capacity_warning') ?? 'Capacity Warning' }}</h5>
                        <p>${warningMessage}</p>
                    </div>
                `;
            }

            const summary = `
            ${warningHtml}
            <table class="table table-bordered">
                <tr><th>{{ __('m_bookings.bookings.fields.tour') }}</th><td>${tourName}</td></tr>
                <tr><th>{{ __('m_bookings.bookings.fields.date') }}</th><td>${tourDate}</td></tr>
                <tr><th>{{ __('m_bookings.bookings.fields.schedule') }}</th><td>${schedule}</td></tr>
                <tr><th>{{ __('m_bookings.bookings.fields.language') }}</th><td>${language}</td></tr>
                <tr><th>{{ __('m_bookings.bookings.fields.customer') }}</th><td>${customer}</td></tr>
                <tr><th>{{ __('m_bookings.bookings.fields.pickup') }}</th><td>${pickupInfo}</td></tr>
                ${notes ? `<tr><th>{{ __('m_bookings.bookings.fields.notes') }}</th><td>${notes}</td></tr>` : ''}
            </table>

            <h5>{{ __('m_bookings.bookings.ui.participants') ?? 'Participants' }}:</h5>
            <table class="table table-bordered table-sm">
                <thead><tr><th>{{ __('m_bookings.bookings.fields.category') }}</th><th>{{ __('m_bookings.bookings.fields.quantity') }}</th><th>{{ __('m_bookings.bookings.fields.price') }}</th><th>{{ __('m_bookings.bookings.ui.subtotal') }}</th></tr></thead>
                <tbody>${categoriesHtml}</tbody>
            </table>

            <h5>{{ __('m_bookings.bookings.ui.price_breakdown') ?? 'Price Breakdown' }}:</h5>
            <table class="table table-bordered table-sm">
                <tr><th style="width: 70%">{{ __('m_bookings.bookings.ui.subtotal') }}</th><td>${subtotalDisplay}</td></tr>
                ${promoHtml}
                ${taxesHtml}
                <tr class="bg-light"><th><strong>{{ __('m_bookings.bookings.ui.total') }}</strong></th><td><strong>${totalDisplay}</strong></td></tr>
            </table>
        `;

            $('#booking-summary').html(summary);

            // Update confirm button text/style based on warning
            const $confirmBtn = $('#btn-confirm-submit');
            if (warningMessage) {
                $confirmBtn.removeClass('btn-success').addClass('btn-warning');
                $confirmBtn.html('<i class="fas fa-exclamation-triangle mr-2"></i> {{ __("m_bookings.actions.yes_force") ?? "Yes, Force Booking" }}');
            } else {
                $confirmBtn.removeClass('btn-warning').addClass('btn-success');
                $confirmBtn.html('<i class="fas fa-save mr-2"></i> {{ __("m_bookings.actions.confirm_create") ?? "Confirm & Create" }}');
            }

            $('#confirmModal').modal('show');
        }

        // Confirm submit - use event delegation for modal
        $(document).on('click', '#btn-confirm-submit', function() {
            console.log('Confirm submit button clicked');
            $('#confirmModal').modal('hide');
            $('#booking-form').submit();
        });

        $('#booking-form').on('submit', function(e) {
            console.log('Booking form submitting...');
            console.log('Form action:', $(this).attr('action'));
            console.log('Form method:', $(this).attr('method'));
        });

        // Initialize on page load
        if ($tourSelect.val()) {
            $tourSelect.trigger('change');
        }
        if ($hotelSelect.val() === 'other') {
            $('#other-hotel-wrapper').show();
        }
    });
</script>
@stop