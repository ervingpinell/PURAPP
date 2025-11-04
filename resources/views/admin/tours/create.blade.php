@extends('adminlte::page')

@section('title', 'Crear Tour')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Crear Nuevo Tour</h1>
        <a href="{{ route('admin.tours.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver al Listado
        </a>
    </div>
@stop

@section('content')
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Errores en el formulario:</strong>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.tours.store') }}" method="POST" id="tourForm">
        @csrf

        <div class="card card-primary card-outline card-outline-tabs">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" id="tourTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="details-tab" data-toggle="pill" href="#details" role="tab">
                            <i class="fas fa-info-circle"></i> Detalles
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="prices-tab" data-toggle="pill" href="#prices" role="tab">
                            <i class="fas fa-dollar-sign"></i> Precios
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="itinerary-tab" data-toggle="pill" href="#itinerary" role="tab">
                            <i class="fas fa-route"></i> Itinerario
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="schedules-tab" data-toggle="pill" href="#schedules" role="tab">
                            <i class="fas fa-clock"></i> Horarios
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="languages-tab" data-toggle="pill" href="#languages" role="tab">
                            <i class="fas fa-language"></i> Idiomas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="amenities-tab" data-toggle="pill" href="#amenities" role="tab">
                            <i class="fas fa-check-circle"></i> Amenidades
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="summary-tab" data-toggle="pill" href="#summary" role="tab">
                            <i class="fas fa-eye"></i> Resumen
                        </a>
                    </li>
                </ul>
            </div>

            <div class="card-body">
                <div class="tab-content" id="tourTabsContent">
                    {{-- Pestaña: Detalles --}}
                    <div class="tab-pane fade show active" id="details" role="tabpanel">
                        @include('admin.tours.partials.tab-details', ['tour' => null])
                    </div>

                    {{-- Pestaña: Precios --}}
                    <div class="tab-pane fade" id="prices" role="tabpanel">
                        @include('admin.tours.partials.tab-prices', ['tour' => null])
                    </div>

                    {{-- Pestaña: Itinerario --}}
                    <div class="tab-pane fade" id="itinerary" role="tabpanel">
                        @include('admin.tours.partials.tab-itinerary', ['tour' => null])
                    </div>

                    {{-- Pestaña: Horarios --}}
                    <div class="tab-pane fade" id="schedules" role="tabpanel">
                        @include('admin.tours.partials.tab-schedules', ['tour' => null])
                    </div>

                    {{-- Pestaña: Idiomas --}}
                    <div class="tab-pane fade" id="languages" role="tabpanel">
                        @include('admin.tours.partials.tab-languages', ['tour' => null])
                    </div>

                    {{-- Pestaña: Amenidades --}}@extends('adminlte::page')

@section('title', __('m_bookings.bookings.ui.create_booking'))

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>
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

        <div class="row">
            {{-- Left Column: Customer & Tour Info --}}
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle me-2"></i>
                            {{ __('m_bookings.bookings.ui.booking_info') }}
                        </h3>
                    </div>
                    <div class="card-body">
                        {{-- Customer Selection --}}
                        <div class="form-group">
                            <label for="user_id">{{ __('m_bookings.bookings.fields.customer') }} *</label>
                            <select name="user_id" id="user_id" class="form-control select2" required>
                                <option value="">-- {{ __('m_bookings.bookings.ui.select_customer') }} --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->user_id }}" {{ old('user_id') == $user->user_id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Tour Selection --}}
                        <div class="form-group">
                            <label for="tour_id">{{ __('m_bookings.bookings.fields.tour') }} *</label>
                            <select name="tour_id" id="tour_id" class="form-control select2" required>
                                <option value="">-- {{ __('m_bookings.bookings.ui.select_tour') }} --</option>
                                @foreach($tours as $tour)
                                    <option value="{{ $tour->tour_id }}" {{ old('tour_id') == $tour->tour_id ? 'selected' : '' }}>
                                        {{ $tour->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            {{-- Tour Date --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tour_date">{{ __('m_bookings.bookings.fields.tour_date') }} *</label>
                                    <input type="date" name="tour_date" id="tour_date"
                                           class="form-control"
                                           value="{{ old('tour_date') }}"
                                           min="{{ now()->toDateString() }}"
                                           required>
                                </div>
                            </div>

                            {{-- Schedule --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="schedule_id">{{ __('m_bookings.bookings.fields.schedule') }} *</label>
                                    <select name="schedule_id" id="schedule_id" class="form-control" required disabled>
                                        <option value="">-- {{ __('m_bookings.bookings.ui.select_tour_first') }} --</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Language --}}
                        <div class="form-group">
                            <label for="tour_language_id">{{ __('m_bookings.bookings.fields.language') }} *</label>
                            <select name="tour_language_id" id="tour_language_id" class="form-control" required disabled>
                                <option value="">-- {{ __('m_bookings.bookings.ui.select_tour_first') }} --</option>
                            </select>
                        </div>

                        {{-- Hotel / Meeting Point --}}
                        <div class="row">
                            <div class="col-md-6">
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

                                <div class="form-group" id="other_hotel_wrapper" style="display: none;">
                                    <label for="other_hotel_name">{{ __('m_bookings.bookings.fields.hotel_name') }}</label>
                                    <input type="text" name="other_hotel_name" id="other_hotel_name"
                                           class="form-control" value="{{ old('other_hotel_name') }}">
                                    <input type="hidden" name="is_other_hotel" id="is_other_hotel" value="0">
                                </div>
                            </div>

                            <div class="col-md-6">
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
                        </div>

                        {{-- Status --}}
                        <div class="form-group">
                            <label for="status">{{ __('m_bookings.bookings.fields.status') }} *</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="pending" {{ old('status', 'pending') === 'pending' ? 'selected' : '' }}>
                                    {{ __('m_bookings.bookings.statuses.pending') }}
                                </option>
                                <option value="confirmed" {{ old('status') === 'confirmed' ? 'selected' : '' }}>
                                    {{ __('m_bookings.bookings.statuses.confirmed') }}
                                </option>
                            </select>
                        </div>

                        {{-- Notes --}}
                        <div class="form-group">
                            <label for="notes">{{ __('m_bookings.bookings.fields.notes') }}</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Categories (Dynamic) --}}
            <div class="col-lg-4">
                <div class="card sticky-top" style="top: 20px;">
                    <div class="card-header bg-success text-white">
                        <h3 class="card-title">
                            <i class="fas fa-users me-2"></i>
                            {{ __('m_bookings.bookings.fields.travelers') }}
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="categories-container">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                {{ __('m_bookings.bookings.ui.select_tour_to_see_categories') }}
                            </div>
                        </div>

                        {{-- Total Section --}}
                        <div class="mt-3 p-3 bg-light border rounded">
                            <div class="d-flex justify-content-between mb-2">
                                <strong>{{ __('m_bookings.bookings.fields.total_persons') }}:</strong>
                                <span id="total-persons" class="badge bg-info">0</span>
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%'
    });

    let currentCategories = [];

    // Tour selection handler - Carga categorías via AJAX
    $('#tour_id').on('change', function() {
        const tourId = $(this).val();

        if (!tourId) {
            $('#schedule_id, #tour_language_id').prop('disabled', true).html('<option value="">--</option>');
            $('#categories-container').html('<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>Selecciona un tour</div>');
            currentCategories = [];
            updateTotals();
            return;
        }

        // Load schedules
        $.get(`/admin/api/tours/${tourId}/schedules`, function(schedules) {
            let html = '<option value="">-- Seleccionar --</option>';
            schedules.forEach(s => {
                html += `<option value="${s.schedule_id}">${s.start_time} - ${s.end_time}</option>`;
            });
            $('#schedule_id').html(html).prop('disabled', false);
        });

        // Load languages
        $.get(`/admin/api/tours/${tourId}/languages`, function(languages) {
            let html = '<option value="">-- Seleccionar --</option>';
            languages.forEach(l => {
                html += `<option value="${l.tour_language_id}">${l.name}</option>`;
            });
            $('#tour_language_id').html(html).prop('disabled', false);
        });

        // Load categories
        $.get(`/admin/api/tours/${tourId}/categories`, function(categories) {
            currentCategories = categories;
            renderCategories();
        });
    });

    function renderCategories() {
        if (!currentCategories.length) {
            $('#categories-container').html('<div class="alert alert-warning">Este tour no tiene categorías configuradas</div>');
            return;
        }

        let html = '';
        currentCategories.forEach(cat => {
            if (!cat.is_active) return;

            html += `
                <div class="category-row mb-3 p-2 border rounded">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong>${cat.name}</strong>
                        <span class="text-muted small">$${parseFloat(cat.price).toFixed(2)}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <button type="button" class="btn btn-sm btn-outline-secondary category-minus" data-category-id="${cat.id}">
                            <i class="fas fa-minus"></i>
                        </button>
                        <input type="number"
                               name="categories[${cat.id}]"
                               class="form-control form-control-sm text-center category-input mx-2"
                               data-category-id="${cat.id}"
                               data-price="${cat.price}"
                               min="${cat.min}"
                               max="${cat.max}"
                               value="${cat.min}"
                               style="width: 70px;">
                        <button type="button" class="btn btn-sm btn-outline-secondary category-plus" data-category-id="${cat.id}">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <small class="text-muted d-block mt-1">Min: ${cat.min}, Max: ${cat.max}</small>
                </div>
            `;
        });

        $('#categories-container').html(html);
        attachCategoryHandlers();
        updateTotals();
    }

    function attachCategoryHandlers() {
        $('.category-minus').on('click', function() {
            const catId = $(this).data('category-id');
            const input = $(`.category-input[data-category-id="${catId}"]`);
            const min = parseInt(input.attr('min'));
            const current = parseInt(input.val());

            if (current > min) {
                input.val(current - 1);
                updateTotals();
            }
        });

        $('.category-plus').on('click', function() {
            const catId = $(this).data('category-id');
            const input = $(`.category-input[data-category-id="${catId}"]`);
            const max = parseInt(input.attr('max'));
            const current = parseInt(input.val());

            if (current < max) {
                input.val(current + 1);
                updateTotals();
            }
        });

        $('.category-input').on('change', function() {
            const min = parseInt($(this).attr('min'));
            const max = parseInt($(this).attr('max'));
            let val = parseInt($(this).val());

            if (val < min) val = min;
            if (val > max) val = max;

            $(this).val(val);
            updateTotals();
        });
    }

    function updateTotals() {
        let totalPersons = 0;
        let totalPrice = 0;

        $('.category-input').each(function() {
            const qty = parseInt($(this).val()) || 0;
            const price = parseFloat($(this).data('price')) || 0;

            totalPersons += qty;
            totalPrice += qty * price;
        });

        $('#total-persons').text(totalPersons);
        $('#total-price').text('$' + totalPrice.toFixed(2));
    }

    // Hotel "other" handler
    $('#hotel_id').on('change', function() {
        if ($(this).val() === 'other') {
            $('#other_hotel_wrapper').slideDown();
            $('#is_other_hotel').val('1');
        } else {
            $('#other_hotel_wrapper').slideUp();
            $('#is_other_hotel').val('0');
            $('#other_hotel_name').val('');
        }
    });

    // Trigger on page load if there's old input
    if ($('#hotel_id').val() === 'other') {
        $('#other_hotel_wrapper').show();
    }

    if ($('#tour_id').val()) {
        $('#tour_id').trigger('change');
    }
});
</script>
@stop

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css" rel="stylesheet" />
<style>
.sticky-top {
    position: sticky;
    z-index: 1020;
}
</style>
@stop
                    <div class="tab-pane fade" id="amenities" role="tabpanel">
                        @include('admin.tours.partials.tab-amenities', ['tour' => null])
                    </div>

                    {{-- Pestaña: Resumen --}}
                    <div class="tab-pane fade" id="summary" role="tabpanel">
                        @include('admin.tours.partials.tab-summary', ['tour' => null])
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fas fa-save"></i> Crear Tour
                </button>
                <a href="{{ route('admin.tours.index') }}" class="btn btn-secondary btn-lg">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </div>
    </form>
@stop

@section('js')
    @include('admin.tours.partials.scripts')
@stop
