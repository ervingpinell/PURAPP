{{-- Advanced Filters Partial --}}
<div class="collapse {{ request()->hasAny(['reference', 'status', 'booking_date_from', 'booking_date_to', 'tour_date_from', 'tour_date_to', 'tour_id', 'schedule_id']) ? 'show' : '' }}" id="advancedFilters">
  <form method="GET" class="card card-body shadow-sm bg-dark text-white border-0">
    <h5 class="text-center mb-3">Advanced Filters</h5>

    {{-- 📅 Dates --}}
    <h6 class="mb-2">Dates</h6>
    <div class="row mb-4 border-top pt-3">
      <div class="col-md-6">
        <label class="form-label">Booked from:</label>
        <input type="date" name="booking_date_from" class="form-control mb-2" value="{{ request('booking_date_from') }}">
        <label class="form-label">Booked until:</label>
        <input type="date" name="booking_date_to" class="form-control" value="{{ request('booking_date_to') }}">
      </div>
      <div class="col-md-6">
        <label class="form-label">Tour from:</label>
        <input type="date" name="tour_date_from" class="form-control mb-2" value="{{ request('tour_date_from') }}">
        <label class="form-label">Tour until:</label>
        <input type="date" name="tour_date_to" class="form-control" value="{{ request('tour_date_to') }}">
      </div>
    </div>

    {{-- 🎯 Tour and Schedule --}}
    <div class="row mb-4">
      <div class="col-md-6">
        <label class="form-label">Tour:</label>
        <select name="tour_id" class="form-select">
          <option value="">All</option>
          @foreach($tours as $t)
            <option value="{{ $t->tour_id }}" {{ request('tour_id') == $t->tour_id ? 'selected' : '' }}>
              {{ $t->name }}
            </option>
          @endforeach
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Schedule:</label>
        <select name="schedule_id" class="form-select">
          <option value="">All</option>
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
        <label class="form-label">Status:</label>
        <select name="status" class="form-select">
          <option value="">All</option>
          <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
          <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
          <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Reference:</label>
        <input type="text" name="reference" class="form-control" value="{{ request('reference') }}" placeholder="Enter booking reference">
      </div>
    </div>

    {{-- Filter Buttons --}}
    <div class="d-flex justify-content-end gap-2">
      <button type="submit" class="btn btn-primary">
        <i class="fas fa-check"></i> Apply
      </button>

      <a href="{{ route('admin.bookings.index') }}" class="btn btn-light">
        <i class="fas fa-times"></i> Clear
      </a>

      <button type="button" class="btn btn-outline-light" id="closeFiltersBtn">
        <i class="fas fa-chevron-up"></i> Close filters
      </button>
    </div>
  </form>
</div>
