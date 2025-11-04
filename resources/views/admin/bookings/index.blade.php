{{-- resources/views/admin/bookings/index.blade.php --}}

@extends('adminlte::page')

@section('title', __('m_bookings.bookings.ui.page_title'))

@section('content_header')
  <h1>{{ __('m_bookings.bookings.ui.page_heading') }}</h1>
@stop

@push('css')
<style>
  /* Zoom functionality */
  #bookingsTableContainer {
    transform-origin: top left;
    transition: transform 0.3s ease;
  }

  /* Compact table styling */
  .table-compact {
    font-size: 0.875rem;
  }

  .table-compact td,
  .table-compact th {
    padding: 0.5rem;
    white-space: nowrap;
  }

  .badge-compact {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
  }

  /* Interactive badge styling */
  .badge-interactive {
    transition: all 0.2s ease;
    cursor: pointer;
  }

  .badge-interactive:hover {
    transform: scale(1.05);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    opacity: 0.9;
  }

  .badge-interactive:active {
    transform: scale(0.98);
  }

  /* Ensure text color for different badge states */
  .badge.bg-warning.text-dark {
    color: #000 !important;
  }

  .badge.bg-success.text-white,
  .badge.bg-danger.text-white {
    color: #fff !important;
  }

  /* Responsive adjustments */
  @media (max-width: 768px) {
    .table-compact {
      font-size: 0.75rem;
    }
  }

  /* Details button hover effect */
  .btn-details:hover {
    transform: scale(1.1);
    transition: transform 0.2s;
  }
</style>
@endpush

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

  {{-- 🟩 Top buttons - MEJORADO Y ALINEADO --}}
  <div class="mb-3 d-flex flex-wrap align-items-center gap-2">
    {{-- Action Buttons --}}
<a href="{{ route('admin.bookings.create') }}" class="btn btn-success">
  <i class="fas fa-plus"></i> {{ __('m_bookings.bookings.ui.add_booking') }}
</a>
    <a href="{{ route('admin.bookings.export.pdf') }}" class="btn btn-danger">
      <i class="fas fa-file-pdf"></i> {{ __('m_bookings.reports.download_pdf') }}
    </a>

    <a href="{{ route('admin.bookings.export.excel', request()->query()) }}" class="btn btn-success">
      <i class="fas fa-file-excel"></i> {{ __('m_bookings.reports.export_excel') }}
    </a>

    {{-- Spacer --}}
    <div class="flex-grow-1"></div>

    {{-- 🔍 Quick reference filter - ALINEADO --}}
    <form method="GET" action="{{ route('admin.bookings.index') }}" class="d-flex">
      <div class="input-group" style="width: 280px;">
        <input type="text" name="reference" class="form-control"
               placeholder="{{ __('m_bookings.filters.search_reference') }}"
               value="{{ request('reference') }}">
        <button class="btn btn-outline-secondary" type="submit">
          <i class="fas fa-search"></i>
        </button>
      </div>
    </form>

    {{-- Advanced Filters Button --}}
    <button class="btn btn-secondary" type="button" data-bs-toggle="collapse"
            data-bs-target="#advancedFilters"
            aria-expanded="{{ $activeFilters ? 'true' : 'false' }}">
      <i class="fas fa-filter"></i> {{ __('m_bookings.filters.advanced_filters') }}
    </button>

    {{-- Zoom Controls --}}
    <div class="btn-group" role="group">
      <button type="button" class="btn btn-outline-secondary" id="zoomOut" title="{{ __('m_bookings.ui.zoom_out') }}">
        <i class="fas fa-search-minus"></i>
      </button>
      <button type="button" class="btn btn-outline-secondary" id="zoomReset" title="{{ __('m_bookings.ui.zoom_reset') }}">
        <i class="fas fa-compress"></i>
      </button>
      <button type="button" class="btn btn-outline-secondary" id="zoomIn" title="{{ __('m_bookings.ui.zoom_in') }}">
        <i class="fas fa-search-plus"></i>
      </button>
    </div>
  </div>

  {{-- 🧪 Advanced filters --}}
  @include('admin.bookings.partials.advanced-filters')

  {{-- 📋 Compact Table --}}
  <div class="table-responsive mt-4" id="bookingsTableContainer">
    @include('admin.bookings.partials.table-compact')
  </div>
</div>

{{-- ✨ Modals --}}
@foreach ($bookings as $booking)
  @include('admin.bookings.partials.modal-details', ['booking' => $booking])
@endforeach
@endsection

@push('js')
@include('admin.bookings.partials.scripts')
@endpush
