{{-- resources/views/admin/bookings/index.blade.php --}}
@extends('adminlte::page')

@section('title', __('m_bookings.bookings.ui.page_title'))

@section('content_header')
<h1>{{ __('m_bookings.bookings.ui.page_heading') }}</h1>
@stop

@push('css')
<style>
  /* Zoom container */
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
    box-shadow: 0 2px 8px rgba(0, 0, 0, .2);
    opacity: .9;
  }

  .badge-interactive:active {
    transform: scale(0.98);
  }

  /* Badge color legibility */
  .badge.bg-warning.text-dark {
    color: #000 !important;
  }

  .badge.bg-success.text-white,
  .badge.bg-danger.text-white {
    color: #fff !important;
  }

  /* Details button hover */
  .btn-details:hover {
    transform: scale(1.1);
    transition: transform 0.2s;
  }

  /* ===== Paginação numérica ===== */
  .bookings-pagination {
    margin-top: .75rem;
    display: flex;
    justify-content: center;
  }

  .bookings-pagination .pagination {
    margin-bottom: 0;
  }

  .bookings-pagination .page-link {
    padding: .25rem .55rem;
    font-size: 0.875rem;
    line-height: 1.4;
  }

  /* ===== MOBILE RESPONSIVE ===== */
  @media (max-width: 768px) {

    /* Smaller table font on mobile */
    .table-compact {
      font-size: 0.75rem;
    }

    .table-compact td,
    .table-compact th {
      padding: 0.35rem;
    }

    /* Tabs responsive */
    .nav-tabs {
      flex-wrap: nowrap;
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
    }

    .nav-tabs .nav-link {
      white-space: nowrap;
      font-size: 0.875rem;
      padding: 0.5rem 0.75rem;
    }

    /* Larger touch targets for buttons */
    .btn-sm {
      min-width: 36px;
      min-height: 36px;
      padding: 0.4rem 0.6rem;
    }

    /* Stack action buttons vertically on very small screens */
    @media (max-width: 576px) {
      .gap-2 {
        flex-direction: column !important;
        align-items: stretch !important;
      }

      .gap-2>* {
        width: 100% !important;
      }
    }
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

  {{-- 🟩 Top buttons - Reorganized: Filters LEFT, Actions RIGHT --}}
  <div class="mb-3 d-flex flex-wrap align-items-center justify-content-between gap-2">

    {{-- LEFT GROUP: Filters & Controls --}}
    <div class="d-flex flex-wrap align-items-center gap-2">
      {{-- Quick reference filter --}}
      <form method="GET" action="{{ route('admin.bookings.index') }}" class="d-flex">
        {{-- Preserve parameters --}}
        <input type="hidden" name="view" value="{{ request('view', 'active') }}">
        <input type="hidden" name="status" value="{{ request('status', 'general') }}">

        <div class="input-group" style="width: 280px;">
          <input
            type="text"
            name="reference"
            class="form-control"
            placeholder="{{ __('m_bookings.filters.search_reference') }}"
            value="{{ request('reference') }}"
            aria-label="{{ __('m_bookings.filters.search_reference') }}">
          <button class="btn btn-outline-secondary" type="submit" title="{{ __('m_bookings.ui.search') }}">
            <i class="fas fa-search"></i>
          </button>
        </div>
      </form>

      {{-- Advanced Filters Button --}}
      <button class="btn btn-secondary" type="button"
        data-bs-toggle="collapse"
        data-bs-target="#advancedFilters"
        aria-expanded="false"
        aria-controls="advancedFilters">
        <i class="fas fa-filter"></i> {{ __('m_bookings.filters.advanced_filters') }}
      </button>

      {{-- Zoom Controls --}}
      <div class="btn-group" role="group" aria-label="Zoom">
        <button type="button" class="btn btn-outline-secondary" id="zoomOut" title="{{ __('m_bookings.ui.zoom_out') }}">
          <i class="fas fa-search-minus"></i>
        </button>
        <button type="button" class="btn btn-outline-secondary" id="zoomReset" title="{{ __('m_bookings.ui.zoom_reset') }}">
          <i class="fas fa-search"></i>
        </button>
        <button type="button" class="btn btn-outline-secondary" id="zoomIn" title="{{ __('m_bookings.ui.zoom_in') }}">
          <i class="fas fa-search-plus"></i>
        </button>
      </div>
    </div>

    {{-- RIGHT GROUP: Action Buttons --}}
    <div class="d-flex flex-wrap align-items-center gap-2">
      @can('create-bookings')
      <a href="{{ route('admin.bookings.create') }}" class="btn btn-success">
        <i class="fas fa-plus"></i> {{ __('m_bookings.bookings.ui.add_booking') }}
      </a>
      @endcan

      <a href="{{ route('admin.bookings.export.pdf') }}" class="btn btn-danger">
        <i class="fas fa-file-pdf"></i> {{ __('m_bookings.reports.download_pdf') }}
      </a>

      <a href="{{ route('admin.bookings.export.excel', request()->query()) }}" class="btn btn-success">
        <i class="fas fa-file-excel"></i> {{ __('m_bookings.reports.export_excel') }}
      </a>
    </div>
  </div>

  {{-- 🧪 Advanced filters --}}
  @include('admin.bookings.partials.advanced-filters')

  {{-- 📑 TABS: Status Tabs + Trash --}}
  <ul class="nav nav-tabs mb-3" role="tablist">
    @php
    $currentView = request('view', 'active');
    $currentStatus = request('status', 'general');
    $isTrash = $currentView === 'trash';
    @endphp

    {{-- STATUS TABS (only shown when not in trash) --}}
    @if(!$isTrash)
    <li class="nav-item" role="presentation">
      <a class="nav-link {{ $currentStatus === 'general' ? 'active' : '' }}"
        href="{{ route('admin.bookings.index', array_merge(request()->except(['view', 'status']), ['view' => 'active', 'status' => 'general'])) }}">
        <i class="fas fa-list"></i> {{ __('m_bookings.tabs.general') }}
        @if($currentStatus === 'general')
        <span class="badge bg-primary ms-1">{{ $bookings->total() }}</span>
        @endif
      </a>
    </li>
    <li class="nav-item" role="presentation">
      <a class="nav-link {{ $currentStatus === 'active' ? 'active' : '' }}"
        href="{{ route('admin.bookings.index', array_merge(request()->except(['view', 'status']), ['view' => 'active', 'status' => 'active'])) }}">
        <i class="fas fa-check-circle"></i> {{ __('m_bookings.tabs.active') }}
        @if($currentStatus === 'active')
        <span class="badge bg-success ms-1">{{ $bookings->total() }}</span>
        @endif
      </a>
    </li>
    <li class="nav-item" role="presentation">
      <a class="nav-link {{ $currentStatus === 'cancelled' ? 'active' : '' }}"
        href="{{ route('admin.bookings.index', array_merge(request()->except(['view', 'status']), ['view' => 'active', 'status' => 'cancelled'])) }}">
        <i class="fas fa-times-circle"></i> {{ __('m_bookings.tabs.cancelled') }}
        @if($currentStatus === 'cancelled')
        <span class="badge bg-danger ms-1">{{ $bookings->total() }}</span>
        @endif
      </a>
    </li>
    <li class="nav-item" role="presentation">
      <a class="nav-link {{ $currentStatus === 'pending' ? 'active' : '' }}"
        href="{{ route('admin.bookings.index', array_merge(request()->except(['view', 'status']), ['view' => 'active', 'status' => 'pending'])) }}">
        <i class="fas fa-clock"></i> {{ __('m_bookings.tabs.pending') }}
        @if($currentStatus === 'pending')
        <span class="badge bg-warning ms-1">{{ $bookings->total() }}</span>
        @endif
      </a>
    </li>
    <li class="nav-item ms-auto" role="presentation">
      <a class="nav-link"
        href="{{ route('admin.bookings.index', array_merge(request()->except(['view', 'status']), ['view' => 'trash'])) }}">
        <i class="fas fa-trash"></i> {{ __('m_bookings.tabs.trash') }}
      </a>
    </li>
    @else
    {{-- TRASH TAB (show only when in trash) --}}
    <li class="nav-item" role="presentation">
      <a class="nav-link"
        href="{{ route('admin.bookings.index', array_merge(request()->except(['view', 'status']), ['view' => 'active', 'status' => 'general'])) }}">
        <i class="fas fa-arrow-left"></i> {{ __('m_bookings.bookings.trash.back_to_bookings') }}
      </a>
    </li>
    <li class="nav-item" role="presentation">
      <a class="nav-link active">
        <i class="fas fa-trash"></i> {{ __('m_bookings.tabs.trash') }}
        <span class="badge bg-danger ms-1">{{ $bookings->total() }}</span>
      </a>
    </li>
    @endif
  </ul>

  {{-- 📋 Compact Table --}}
  <div class="table-responsive mt-4" id="bookingsTableContainer">
    @include('admin.bookings.partials.table-compact')

    {{-- 🔢 Paginación SOLO con números --}}
    @if($bookings instanceof \Illuminate\Pagination\LengthAwarePaginator && $bookings->lastPage() > 1)
    <div class="bookings-pagination">
      <ul class="pagination pagination-sm">
        @for ($page = 1; $page <= $bookings->lastPage(); $page++)
          @if ($page == $bookings->currentPage())
          <li class="page-item active" aria-current="page">
            <span class="page-link">{{ $page }}</span>
          </li>
          @else
          <li class="page-item">
            <a class="page-link" href="{{ $bookings->url($page) }}">{{ $page }}</a>
          </li>
          @endif
          @endfor
      </ul>
    </div>
    @endif
  </div>
</div>


@endsection

@push('js')
{{-- Scripts propios (incluye handlers de zoom y filtros) --}}
@include('admin.bookings.partials.scripts')
@endpush