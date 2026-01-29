{{-- resources/views/admin/tours/capacity/index.blade.php --}}
@extends('adminlte::page')

@section('title', __('m_bookings.capacity.ui.page_title'))

@section('content_header')
<h1>
  <i class="fas fa-users me-2"></i>
  {{ __('m_bookings.capacity.ui.page_heading') }}
</h1>
@stop

@section('content')
<div class="card">
  <div class="card-header">
    <ul class="nav nav-tabs card-header-tabs" role="tablist">
      <li class="nav-item">
        <a class="nav-link {{ $tab === 'global' ? 'active' : '' }}"
          href="{{ route('admin.products.capacity.index', ['tab' => 'global']) }}">
          <i class="fas fa-globe me-1"></i>
          {{ __('m_bookings.capacity.tabs.global') }}
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ $tab === 'by-product' ? 'active' : '' }}"
          href="{{ route('admin.products.capacity.index', ['tab' => 'by-product']) }}">
          <i class="fas fa-clock me-1"></i>
          {{ __('m_bookings.capacity.tabs.by_tour') }}
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ $tab === 'day-schedules' ? 'active' : '' }}"
          href="{{ route('admin.products.capacity.index', ['tab' => 'day-schedules']) }}">
          <i class="fas fa-calendar-check me-1"></i>
          {{ __('m_bookings.capacity.tabs.day_schedules') }}
        </a>
      </li>
    </ul>
  </div>

  <div class="card-body">
    {{-- TAB 1: Capacidades Globales --}}
    @if($tab === 'global')
    <div class="alert alert-info">
      <i class="fas fa-info-circle me-1"></i>
      {!! __('m_bookings.capacity.alerts.global_info') !!}
    </div>

    <table class="table table-sm table-striped">
      <thead>
        <tr>
          <th>{{ __('m_bookings.capacity.tables.global.product') }}</th>
          <th>{{ __('m_bookings.capacity.tables.global.type') }}</th>
          <th style="width: 150px;">{{ __('m_bookings.capacity.tables.global.capacity') }}</th>
          <th style="width: 100px;">{{ __('m_bookings.capacity.tables.global.level') }}</th>
        </tr>
      </thead>
      <tbody>
        @foreach($products as $product)
        <tr>
          <td>{{ $product->getTranslatedName() ?? $product->name }}</td>
          <td>
            <span class="badge bg-secondary">
              {{ $product->productType->getNameTranslatedAttribute() ?? $product->productType->name ?? '—' }}
            </span>
          </td>
          <td>
            <form action="{{ route('admin.products.capacity.update-product', $product) }}"
              method="POST"
              class="d-flex gap-2">
              @csrf
              @method('PATCH')
              <input type="number"
                name="max_capacity"
                value="{{ $product->max_capacity ?? 15 }}"
                class="form-control form-control-sm"
                min="1"
                max="999"
                required>
              @can('edit-product-availability')
              <button type="submit" class="btn btn-primary btn-sm" title="{{ __('m_bookings.capacity.buttons.save') }}">
                <i class="fas fa-save"></i>
              </button>
              @endcan
            </form>
          </td>
          <td>
            <span class="badge bg-info">{{ __('m_bookings.capacity.badges.base') }}</span>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
    @endif

    {{-- TAB 2: Por Product + Horario --}}
    @if($tab === 'by-product')
    <div class="alert alert-info">
      <i class="fas fa-info-circle me-1"></i>
      {!! __('m_bookings.capacity.alerts.by_tour_info') !!}
    </div>

    @foreach($products as $product)
    <div class="card mb-3">
      <div class="card-header bg-dark text-white">
        <h6 class="mb-0">
          {{ $product->getTranslatedName() ?? $product->name }}
          <span class="badge bg-info ms-2">
            {{ __('m_bookings.capacity.badges.base') }}: {{ $product->max_capacity ?? '—' }}
          </span>
        </h6>
      </div>
      <div class="card-body p-0">
        <table class="table table-sm table-striped mb-0">
          <thead>
            <tr>
              <th>{{ __('m_bookings.capacity.tables.by_product.schedule') }}</th>
              <th style="width: 200px;">{{ __('m_bookings.capacity.tables.by_product.capacity') }}</th>
              <th style="width: 120px;">{{ __('m_bookings.capacity.tables.by_product.level') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse($product->schedules as $schedule)
            <tr>
              <td>
                <strong>{{ date('g:i A', strtotime($schedule->start_time)) }} - {{ date('g:i A', strtotime($schedule->end_time)) }}</strong>
                @if($schedule->label)
                <br><small class="text-muted">{{ $schedule->label }}</small>
                @endif
              </td>
              <td>
                <form action="{{ route('admin.products.schedule.update-pivot-capacity', [$product, $schedule]) }}"
                  method="POST"
                  class="d-flex gap-2 align-items-center">
                  @csrf
                  @method('PATCH')
                  <input type="number"
                    name="base_capacity"
                    value="{{ $schedule->pivot->base_capacity }}"
                    class="form-control form-control-sm"
                    min="1"
                    max="999"
                    placeholder="{{ __('m_bookings.capacity.messages.empty_placeholder', ['capacity' => $product->max_capacity ?? 15]) }}"
                    style="max-width: 120px;">
                  @can('edit-product-availability')
                  <button type="submit" class="btn btn-primary btn-sm" title="{{ __('m_bookings.capacity.buttons.save') }}">
                    <i class="fas fa-save"></i>
                  </button>
                  @endcan
                </form>
                <small class="text-muted d-block mt-1">
                  {{ __('m_bookings.capacity.messages.empty_placeholder', ['capacity' => $product->max_capacity ?? 15]) }}
                </small>
              </td>
              <td>
                @if($schedule->pivot->base_capacity)
                <span class="badge bg-success">{{ __('m_bookings.capacity.badges.override') }}</span>
                @else
                <span class="badge bg-secondary">{{ __('m_bookings.capacity.badges.global') }}</span>
                @endif
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="3" class="text-center text-muted py-3">
                {{ __('m_bookings.capacity.tables.by_product.no_schedules') }}
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    @endforeach
    @endif

    {{-- TAB 3: Overrides por Día + Horario --}}
    @if($tab === 'day-schedules')
    <div class="alert alert-info mb-3">
      <i class="fas fa-info-circle me-1"></i>
      {!! __('m_bookings.capacity.alerts.day_schedules_info') !!}
    </div>

    <table class="table table-sm table-striped">
      <thead>
        <tr>
          <th>{{ __('m_bookings.capacity.tables.day_schedules.date') }}</th>
          <th>{{ __('m_bookings.capacity.tables.day_schedules.product') }}</th>
          <th>{{ __('m_bookings.capacity.tables.day_schedules.schedule') }}</th>
          <th>{{ __('m_bookings.capacity.tables.day_schedules.capacity') }}</th>
          <th style="width: 100px;">{{ __('m_bookings.capacity.tables.day_schedules.actions') }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse($dayScheduleOverrides as $override)
        <tr>
          <td>{{ Carbon\Carbon::parse($override->date)->format('d/m/Y') }}</td>
          <td>{{ $override->product->getTranslatedName() ?? $override->product->name }}</td>
          <td><strong>{{ date('g:i A', strtotime($override->schedule->start_time)) }}</strong></td>
          <td>
            @if($override->is_blocked)
            <span class="badge bg-danger">{{ __('m_bookings.capacity.badges.blocked') }}</span>
            @else
            <span class="badge bg-success">{{ $override->max_capacity ?? __('m_bookings.capacity.badges.unlimited') }}</span>
            @endif
          </td>
          <td>
            <form action="{{ route('admin.products.capacity.destroy', $override) }}"
              method="POST"
              class="d-inline"
              onsubmit="return confirm('{{ __('m_bookings.capacity.messages.deleted_confirm') }}')">
              @csrf
              @method('DELETE')
              @can('edit-product-availability')
              <button type="submit" class="btn btn-danger btn-sm" title="{{ __('m_bookings.capacity.buttons.delete') }}">
                <i class="fas fa-trash"></i>
              </button>
              @endcan
            </form>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="5" class="text-center text-muted py-3">
            {{ __('m_bookings.capacity.tables.day_schedules.no_overrides') }}
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>

    <div class="d-flex justify-content-center">
      {{ $dayScheduleOverrides->links() }}
    </div>
    @endif
  </div>
</div>
@stop

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  @if(session('success'))
  Swal.fire({
    icon: 'success',
    title: @json(__('m_bookings.capacity.toasts.success_title')),
    text: @json(session('success')),
    timer: 2000,
    showConfirmButton: false
  });
  @endif
  @if(session('error'))
  Swal.fire({
    icon: 'error',
    title: @json(__('m_bookings.capacity.toasts.error_title')),
    text: @json(session('error')),
    timer: 2600,
    showConfirmButton: false
  });
  @endif
</script>
@endpush