{{-- resources/views/admin/bookings/partials/advanced-filters.blade.php --}}
<div class="collapse" id="advancedFilters">
  <div class="card shadow-sm mb-3" style="border: 1px solid #495057; border-radius: 8px; overflow: hidden; background: #343a40;">
    <div class="card-header" style="background: #495057; color: #fff; padding: 1rem 1.5rem; border-bottom: 1px solid #6c757d;">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h5 class="mb-0" style="font-weight: 600; color: #fff;">
            <i class="fas fa-filter me-2"></i>{{ __('m_bookings.filters.advanced_filters') }}
          </h5>
          <small style="opacity: 0.8; color: #adb5bd;">Filtra las reservas por múltiples criterios</small>
        </div>
        <button type="button" class="btn btn-sm btn-outline-light" onclick="toggleFilters()" style="border-radius: 6px;">
          <i class="fas fa-times"></i>
        </button>
      </div>
    </div>

    <div class="card-body" style="background: #212529; padding: 1.5rem;">
      <form method="GET" action="{{ route('admin.bookings.index') }}">
        {{-- Preserve view and status --}}
        <input type="hidden" name="view" value="{{ request('view', 'active') }}">
        <input type="hidden" name="status" value="{{ request('status', 'general') }}">

        {{-- 📅 Booking Dates Section --}}
        <div class="mb-4">
          <h6 class="text-uppercase fw-bold mb-3" style="color: #6c757d; font-size: 0.85rem; letter-spacing: 0.5px;">
            <i class="fas fa-calendar-check me-2"></i>{{ __('m_bookings.filters.booking_dates', [], 'Fechas de Reserva') }}
          </h6>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label text-light" style="font-weight: 500; font-size: 0.9rem;">{{ __('m_bookings.filters.booked_from') }}</label>
              <input type="date" name="booking_date_from" class="form-control bg-dark text-light border-secondary" value="{{ request('booking_date_from') }}" style="border-radius: 6px;">
            </div>
            <div class="col-md-6">
              <label class="form-label text-light" style="font-weight: 500; font-size: 0.9rem;">{{ __('m_bookings.filters.booked_until') }}</label>
              <input type="date" name="booking_date_to" class="form-control bg-dark text-light border-secondary" value="{{ request('booking_date_to') }}" style="border-radius: 6px;">
            </div>
          </div>
        </div>

        {{-- 🗓 Tour Dates Section --}}
        <div class="mb-4">
          <h6 class="text-uppercase fw-bold mb-3" style="color: #6c757d; font-size: 0.85rem; letter-spacing: 0.5px;">
            <i class="fas fa-calendar-alt me-2"></i>{{ __('m_bookings.filters.tour_dates', [], 'Fechas del Tour') }}
          </h6>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label text-light" style="font-weight: 500; font-size: 0.9rem;">{{ __('m_bookings.filters.tour_from') }}</label>
              <input type="date" name="tour_date_from" class="form-control bg-dark text-light border-secondary" value="{{ request('tour_date_from') }}" style="border-radius: 6px;">
            </div>
            <div class="col-md-6">
              <label class="form-label text-light" style="font-weight: 500; font-size: 0.9rem;">{{ __('m_bookings.filters.tour_until') }}</label>
              <input type="date" name="tour_date_to" class="form-control bg-dark text-light border-secondary" value="{{ request('tour_date_to') }}" style="border-radius: 6px;">
            </div>
          </div>
        </div>

        {{-- 🎯 Tour & Schedule Section --}}
        <div class="mb-4">
          <h6 class="text-uppercase fw-bold mb-3" style="color: #6c757d; font-size: 0.85rem; letter-spacing: 0.5px;">
            <i class="fas fa-map-marked-alt me-2"></i>{{ __('m_bookings.filters.tour_schedule', [], 'Tour y Horario') }}
          </h6>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label text-light" style="font-weight: 500; font-size: 0.9rem;">{{ __('m_bookings.bookings.fields.tour') }}</label>
              <select name="product_id" class="form-select bg-dark text-light border-secondary" style="border-radius: 6px;">
                <option value="">{{ __('m_bookings.filters.all') }}</option>
                @foreach($tours as $t)
                <option value="{{ $t->product_id }}" {{ request('product_id') == $t->product_id ? 'selected' : '' }}>
                  {{ $t->name }}
                </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label text-light" style="font-weight: 500; font-size: 0.9rem;">{{ __('m_bookings.bookings.fields.schedule') }}</label>
              <select name="schedule_id" class="form-select bg-dark text-light border-secondary" style="border-radius: 6px;">
                <option value="">{{ __('m_bookings.filters.all') }}</option>
                @foreach($schedules as $s)
                <option value="{{ $s->schedule_id }}" {{ request('schedule_id') == $s->schedule_id ? 'selected' : '' }}>
                  {{ $s->start_time }} - {{ $s->end_time }}
                </option>
                @endforeach
              </select>
            </div>
          </div>
        </div>

        {{-- 📋 Status & Reference Section --}}
        <div class="mb-4">
          <h6 class="text-uppercase fw-bold mb-3" style="color: #6c757d; font-size: 0.85rem; letter-spacing: 0.5px;">
            <i class="fas fa-info-circle me-2"></i>{{ __('m_bookings.filters.status_reference', [], 'Estado y Referencia') }}
          </h6>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label text-light" style="font-weight: 500; font-size: 0.9rem;">{{ __('m_bookings.bookings.fields.status') }}</label>
              <select name="status" class="form-select bg-dark text-light border-secondary" style="border-radius: 6px;">
                <option value="">{{ __('m_bookings.filters.all') }}</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('m_bookings.bookings.statuses.pending') }}</option>
                <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>{{ __('m_bookings.bookings.statuses.confirmed') }}</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>{{ __('m_bookings.bookings.statuses.cancelled') }}</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label text-light" style="font-weight: 500; font-size: 0.9rem;">{{ __('m_bookings.bookings.fields.reference') }}</label>
              <input type="text" name="reference" class="form-control bg-dark text-light border-secondary" value="{{ request('reference') }}" placeholder="{{ __('m_bookings.filters.enter_reference') }}" style="border-radius: 6px;">
            </div>
          </div>
        </div>

        {{-- Action Buttons --}}
        <div class="d-flex justify-content-end gap-2 pt-3" style="border-top: 1px solid #495057;">
          <a href="{{ route('admin.bookings.index', ['view' => request('view', 'active'), 'status' => request('status', 'general')]) }}" class="btn btn-secondary" style="border-radius: 6px;">
            <i class="fas fa-redo me-1"></i> {{ __('m_bookings.filters.clear') }}
          </a>
          <button type="submit" class="btn btn-primary" style="border-radius: 6px; padding: 0.5rem 1.5rem;">
            <i class="fas fa-search me-1"></i> {{ __('m_bookings.filters.apply') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  // Simple toggle function
  function toggleFilters() {
    const advancedFilters = document.getElementById('advancedFilters');
    if (advancedFilters) {
      const bsCollapse = bootstrap.Collapse.getInstance(advancedFilters) || new bootstrap.Collapse(advancedFilters, {
        toggle: false
      });
      bsCollapse.hide();
    }
  }

  // Fix main button toggle
  document.addEventListener('DOMContentLoaded', function() {
    const filterButton = document.querySelector('[data-toggle="collapse"][data-target="#advancedFilters"]');

    if (filterButton) {
      // Remove Bootstrap's default handler and add our own
      filterButton.removeAttribute('data-toggle');

      filterButton.addEventListener('click', function(e) {
        e.preventDefault();
        const advancedFilters = document.getElementById('advancedFilters');
        if (advancedFilters) {
          const bsCollapse = bootstrap.Collapse.getInstance(advancedFilters) || new bootstrap.Collapse(advancedFilters, {
            toggle: false
          });
          bsCollapse.toggle();
        }
      });
    }
  });
</script>