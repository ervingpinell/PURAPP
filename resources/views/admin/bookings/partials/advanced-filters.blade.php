{{-- resources/views/admin/bookings/partials/advanced-filters.blade.php --}}
<div class="collapse {{ request()->hasAny(['reference', 'status', 'booking_date_from', 'booking_date_to', 'tour_date_from', 'tour_date_to', 'tour_id', 'schedule_id']) ? 'show' : '' }}" id="advancedFilters">
  <form method="GET" class="card card-body shadow-sm bg-dark text-white border-0">
    <h5 class="text-center mb-3">{{ __('m_bookings.filters.advanced_filters') }}</h5>

    {{-- 📅 Dates --}}
    <h6 class="mb-2">{{ __('m_bookings.filters.dates') }}</h6>
    <div class="row mb-4 border-top pt-3">
      <div class="col-md-6">
        <label class="form-label">{{ __('m_bookings.filters.booked_from') }}:</label>
        <input type="date" name="booking_date_from" class="form-control mb-2" value="{{ request('booking_date_from') }}">
        <label class="form-label">{{ __('m_bookings.filters.booked_until') }}:</label>
        <input type="date" name="booking_date_to" class="form-control" value="{{ request('booking_date_to') }}">
      </div>
      <div class="col-md-6">
        <label class="form-label">{{ __('m_bookings.filters.tour_from') }}:</label>
        <input type="date" name="tour_date_from" class="form-control mb-2" value="{{ request('tour_date_from') }}">
        <label class="form-label">{{ __('m_bookings.filters.tour_until') }}:</label>
        <input type="date" name="tour_date_to" class="form-control" value="{{ request('tour_date_to') }}">
      </div>
    </div>

    {{-- 🎯 Tour and Schedule --}}
    <div class="row mb-4">
      <div class="col-md-6">
        <label class="form-label">{{ __('m_bookings.bookings.fields.tour') }}:</label>
        <select name="tour_id" class="form-select">
          <option value="">{{ __('m_bookings.filters.all') }}</option>
          @foreach($tours as $t)
            <option value="{{ $t->tour_id }}" {{ request('tour_id') == $t->tour_id ? 'selected' : '' }}>
              {{ $t->name }}
            </option>
          @endforeach
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">{{ __('m_bookings.bookings.fields.schedule') }}:</label>
        <select name="schedule_id" class="form-select">
          <option value="">{{ __('m_bookings.filters.all') }}</option>
          @foreach($schedules as $s)
            <option value="{{ $s->schedule_id }}" {{ request('schedule_id') == $s->schedule_id ? 'selected' : '' }}>
              {{ $s->start_time }} - {{ $s->end_time }}
            </option>
          @endforeach
        </select>
      </div>
    </div>

    {{-- Status and Reference --}}
    <div class="row mb-4">
      <div class="col-md-6">
        <label class="form-label">{{ __('m_bookings.bookings.fields.status') }}:</label>
        <select name="status" class="form-select">
          <option value="">{{ __('m_bookings.filters.all') }}</option>
          <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('m_bookings.bookings.statuses.pending') }}</option>
          <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>{{ __('m_bookings.bookings.statuses.confirmed') }}</option>
          <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>{{ __('m_bookings.bookings.statuses.cancelled') }}</option>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">{{ __('m_bookings.bookings.fields.reference') }}:</label>
        <input type="text" name="reference" class="form-control" value="{{ request('reference') }}" placeholder="{{ __('m_bookings.filters.enter_reference') }}">
      </div>
    </div>

    {{-- Filter Buttons --}}
    <div class="d-flex justify-content-end gap-2">
      <button type="submit" class="btn btn-primary">
        <i class="fas fa-check"></i> {{ __('m_bookings.filters.apply') }}
      </button>

      <a href="{{ route('admin.bookings.index') }}" class="btn btn-light">
        <i class="fas fa-times"></i> {{ __('m_bookings.filters.clear') }}
      </a>

      <button type="button" class="btn btn-outline-light" id="closeFiltersBtn">
        <i class="fas fa-chevron-up"></i> {{ __('m_bookings.filters.close_filters') }}
      </button>
    </div>
  </form>
</div>
