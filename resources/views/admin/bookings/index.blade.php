@extends('adminlte::page')

@section('title', 'Bookings')

@section('content_header')
  <h1>Bookings Management</h1>
@stop

@section('content')
@php
  $activeFilters = request()->hasAny([
    'reference', 'status',
    'booking_date_from', 'booking_date_to',
    'tour_date_from', 'tour_date_to',
    'tour_id', 'schedule_id'
  ]);
@endphp

<div class="container-fluid">

  {{-- 🟩 Top buttons --}}
  <div class="mb-3 row align-items-end">
    <div class="col-md-auto mb-2">
      <a href="#" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalRegister">
        <i class="fas fa-plus"></i> Add Booking
      </a>
    </div>

    <div class="col-md-auto mb-2">
      <a href="{{ route('admin.bookings.export.pdf') }}" class="btn btn-danger">
        <i class="fas fa-file-pdf"></i> Download PDF
      </a>
    </div>

    <div class="col-md-auto mb-2">
      <a href="{{ route('admin.bookings.export.excel', request()->query()) }}" class="btn btn-success">
        <i class="fas fa-file-excel"></i> Export Excel
      </a>
    </div>

    {{-- 🔍 Quick reference filter --}}
    <div class="col-md-3 mb-2">
      <form method="GET" action="{{ route('admin.bookings.index') }}">
        <div class="input-group">
          <input type="text" name="reference" class="form-control" placeholder="Search reference..." value="{{ request('reference') }}">
          <button class="btn btn-outline-secondary" type="submit">
            <i class="fas fa-search"></i>
          </button>
        </div>
      </form>
    </div>

    <div class="col-md-auto text-end mb-2">
      <button class="btn btn-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#advancedFilters" aria-expanded="{{ $activeFilters ? 'true' : 'false' }}">
        <i class="fas fa-filter"></i> Advanced filters
      </button>
    </div>
  </div>

  {{-- 🧪 Advanced filters --}}
  @include('admin.bookings.partials.advanced-filters')

  {{-- 📋 Table --}}
  <div class="table-responsive mt-4">
    @include('admin.bookings.partials.table')
  </div>
</div>

{{-- ✨ Modals --}}
@include('admin.bookings.partials.modal-register')
@foreach ($bookings as $booking)
  @include('admin.bookings.partials.modal-edit', ['booking' => $booking])
@endforeach
@stop

@push('js')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const closeBtn = document.getElementById('closeFiltersBtn');
    const filters = document.getElementById('advancedFilters');

    if (closeBtn && filters) {
      closeBtn.addEventListener('click', () => {
        new bootstrap.Collapse(filters, { toggle: true });
      });
    }
  });
</script>
@include('admin.bookings.partials.scripts')
@endpush
<script>
document.addEventListener('shown.bs.modal', (ev) => {
  const modalEl = ev.target;
  if (!modalEl.id || !modalEl.id.startsWith('modalEdit')) return;

  // --- Tour / Schedule (scoped to modal) ---
  const tourSel = modalEl.querySelector('select[name="tour_id"]');
  const schSel  = modalEl.querySelector('select[name="schedule_id"]');

  if (tourSel && schSel && tourSel.dataset.bound !== '1') {
    tourSel.dataset.bound = '1';

    tourSel.addEventListener('change', () => {
      const opt = tourSel.options[tourSel.selectedIndex];
      const schedules = JSON.parse(opt?.dataset?.schedules || '[]');

      schSel.innerHTML = '<option value="">Select schedule</option>';
      schedules.forEach(s => {
        const o = document.createElement('option');
        o.value = s.schedule_id;
        o.textContent = `${s.start_time} — ${s.end_time}`;
        schSel.appendChild(o);
      });

      // force selection of valid schedule for new tour
      schSel.value = '';
    });
  }

  // --- Hotel "Other…" (scoped to modal) ---
  const hotelSel       = modalEl.querySelector('select[name="hotel_id"]');
  const otherWrap      = modalEl.querySelector('[data-role="other-hotel-wrapper"]');
  const otherInput     = modalEl.querySelector('input[name="other_hotel_name"]');
  const isOtherHidden  = modalEl.querySelector('input[name="is_other_hotel"]');

  const toggleOther = () => {
    if (!hotelSel) return;
    if (hotelSel.value === 'other') {
      otherWrap?.classList.remove('d-none');
      if (isOtherHidden) isOtherHidden.value = 1;
    } else {
      otherWrap?.classList.add('d-none');
      if (otherInput) otherInput.value = '';
      if (isOtherHidden) isOtherHidden.value = 0;
    }
  };

  if (hotelSel && hotelSel.dataset.bound !== '1') {
    hotelSel.dataset.bound = '1';
    hotelSel.addEventListener('change', toggleOther);
    // In case modal opens with "other" selected:
    toggleOther();
  }
});
</script>
