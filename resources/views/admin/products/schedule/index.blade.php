@extends('adminlte::page')

@section('title', __('m_tours.schedule.ui.page_title'))

@section('content_header')
<h1>{{ __('m_tours.schedule.ui.page_heading') }}</h1>
{{-- Patrón para construir la URL de update (se reemplaza ___ID___ en JS) --}}
<meta name="schedule-update-url" content="{{ route('admin.products.schedule.update', ['schedule' => '___ID___']) }}">
@stop

@push('css')
<style>
  /* ===== Tabla ORIGINAL (intacta) ===== */
  .table-responsive {
    overflow-x: auto;
  }

  .table thead th {
    white-space: nowrap;
  }

  /* ===== Botones & badges ===== */
  .btn {
    border-radius: .35rem;
  }

  .btn-edit {
    background: #00a65a;
    color: #fff;
    border: none;
  }

  .btn-view {
    background: #3c8dbc;
    color: #fff;
    border: none;
  }

  .btn-delete {
    background: #dd4b39;
    color: #fff;
    border: none;
  }

  .btn-toggle {
    background: #f39c12;
    color: #fff;
    border: none;
  }

  .btn:hover,
  .btn:focus {
    opacity: .9;
  }

  .btn i {
    pointer-events: none;
  }

  .badge.bg-success {
    background: #00a65a !important;
  }

  .badge.bg-danger {
    background: #dd4b39 !important;
  }

  .badge.bg-secondary {
    background: #6c757d !important;
  }

  .badge.bg-info {
    background: #17a2b8 !important;
  }

  /* ===== Cards ===== */
  .card {
    border-radius: .5rem;
  }

  .card-header {
    font-weight: 600;
  }

  /* Compactar separaciones entre cards en mobile */
  .mb-4 {
    margin-bottom: 1.25rem !important;
  }

  /* ===== Encabezado de cada tour ===== */
  .tour-header {
    display: flex;
    align-items: center;
    gap: .5rem;
    justify-content: space-between;
    flex-wrap: wrap;
  }

  .tour-title {
    flex: 1 1 auto;
    min-width: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .tour-actions {
    flex: 0 0 auto;
    display: flex;
    gap: .5rem;
    flex-wrap: wrap;
    justify-content: flex-end;
  }

  /* ===== Fila de horario por tour ===== */
  .schedule-row {
    display: flex;
    align-items: flex-start;
    gap: .5rem;
    justify-content: space-between;
    flex-wrap: wrap;
    border: 1px solid rgba(255, 255, 255, .1);
    background: #222d32;
    color: #fff;
    border-radius: .5rem;
    padding: .75rem;
    margin-bottom: .75rem;
    overflow-wrap: break-word;
  }

  .schedule-row .schedule-info {
    flex: 1 1 auto;
    min-width: 0;
  }

  /* evita texto vertical */
  .schedule-row .text-truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .schedule-row .tour-actions {
    flex: 0 0 auto;
    display: flex;
    gap: .5rem;
    flex-wrap: nowrap;
  }

  /* ===== Responsive ===== */
  @media (max-width: 768px) {
    .tour-actions {
      width: 100%;
      justify-content: center;
    }

    .schedule-row {
      flex-direction: column;
      align-items: stretch;
    }

    .schedule-row .tour-actions {
      justify-content: center;
      flex-wrap: wrap;
      margin-top: .5rem;
    }
  }

  @media (max-width:576px) {

    .btn,
    .form-select,
    input.form-control {
      min-height: 40px;
    }
  }
</style>
@endpush

@section('content')
<div class="card card-primary card-outline card-outline-tabs">
  <div class="card-header p-0 border-bottom-0">
    <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
      <li class="nav-item">
        <a class="nav-link active" href="#" role="tab" aria-selected="true">{{ __('m_tours.schedule.status.active') }}</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.products.schedule.trash') }}" role="tab" aria-selected="false">
          {{ __('m_tours.schedule.ui.trash_title') }}
          @if(isset($trashedCount) && $trashedCount > 0)
          <span class="badge badge-danger right ml-2">{{ $trashedCount }}</span>
          @endif
        </a>
      </li>
    </ul>
  </div>
  <div class="card-body">


    {{-- ===================== HORARIOS GENERALES (sin max_capacity) ===================== --}}
    <div class="card mb-4 shadow-sm">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">{{ __('m_tours.schedule.ui.general_title') }}</h5>
        @can('create-tour-schedules')
        <button class="btn btn-edit btn-sm" data-toggle="modal" data-target="#modalNuevoHorarioGeneral">
          <i class="fas fa-plus"></i> {{ __('m_tours.schedule.ui.new_schedule') }}
        </button>
        @endcan
      </div>

      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-striped table-hover align-middle mb-0">
            <thead class="bg-primary text-white">
              <tr>
                <th class="text-nowrap">{{ __('m_tours.schedule.ui.time_range') }}</th>
                <th>{{ __('m_tours.schedule.fields.label') }}</th>
                <th class="text-center">{{ __('m_tours.schedule.ui.state') }}</th>
                <th class="text-center" style="width:200px">{{ __('m_tours.schedule.ui.actions') }}</th>
              </tr>
            </thead>
            <tbody>
              @forelse($generalSchedules as $s)
              <tr>
                <td class="text-nowrap fw-semibold">
                  {{ \Carbon\Carbon::createFromTimeString($s->start_time)->format('g:i A') }}
                  –
                  {{ \Carbon\Carbon::createFromTimeString($s->end_time)->format('g:i A') }}
                </td>
                <td>{{ $s->label ?: '—' }}</td>
                <td class="text-center">
                  <span class="badge {{ $s->is_active ? 'bg-success' : 'bg-secondary' }}">
                    {{ $s->is_active ? __('m_tours.schedule.status.active') : __('m_tours.schedule.status.inactive') }}
                  </span>
                </td>
                <td class="text-center">
                  {{-- Editar (modal único) --}}
                  @can('edit-tour-schedules')
                  <button class="btn btn-edit btn-sm"
                    data-toggle="modal"
                    data-target="#modalEditarHorarioGeneral"
                    data-id="{{ $s->schedule_id }}"
                    data-start="{{ $s->start_time }}"
                    data-end="{{ $s->end_time }}"
                    data-label="{{ $s->label }}"
                    data-active="{{ $s->is_active ? 1 : 0 }}"
                    title="{{ __('m_tours.schedule.ui.edit_global') }}">
                    <i class="fas fa-edit"></i>
                  </button>
                  @endcan

                  {{-- Toggle GLOBAL --}}
                  @can('publish-tour-schedules')
                  <form action="{{ route('admin.products.schedule.toggle', $s->schedule_id) }}"
                    method="POST" class="d-inline form-toggle-global"
                    data-label="{{ $s->label ?: ( \Carbon\Carbon::createFromTimeString($s->start_time)->format('g:i A') . ' - ' . \Carbon\Carbon::createFromTimeString($s->end_time)->format('g:i A') ) }}"
                    data-active="{{ $s->is_active ? 1 : 0 }}">
                    @csrf @method('PUT')
                    <button type="submit" class="btn btn-sm {{ $s->is_active ? 'btn-toggle' : 'btn-secondary' }}" title="{{ __('m_tours.schedule.ui.toggle_global_title') }}">
                      <i class="fas fa-toggle-{{ $s->is_active ? 'on' : 'off' }}"></i>
                    </button>
                  </form>
                  @endcan

                  {{-- Eliminar GLOBAL --}}
                  @can('soft-delete-tour-schedules')
                  <form action="{{ route('admin.products.schedule.destroy', $s->schedule_id) }}"
                    method="POST" class="d-inline form-delete"
                    data-label="{{ $s->label ?: ( \Carbon\Carbon::createFromTimeString($s->start_time)->format('g:i A') . ' - ' . \Carbon\Carbon::createFromTimeString($s->end_time)->format('g:i A') ) }}">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-delete btn-sm" title="{{ __('m_tours.schedule.ui.delete_forever') }}">
                      <i class="fas fa-trash"></i>
                    </button>
                  </form>
                  @endcan
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="4" class="text-center text-muted py-4">{{ __('m_tours.schedule.ui.no_general') }}</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- ===================== TOURS Y SUS HORARIOS ===================== --}}
    <div class="row">
      @foreach($products as $product)
      <div class="col-md-6 mb-4">
        <div class="card shadow-sm">
          <div class="card-header bg-dark text-white tour-header">
            <h5 class="mb-0 tour-title text-truncate" title="{{ $product->name }}">
              {{ $product->name }}
            </h5>
            <div class="tour-actions">
              {{-- Asignar existente --}}
              @can('edit-tour-schedules')
              <button class="btn btn-view btn-sm"
                data-toggle="modal"
                data-target="#modalAsignarExistente{{ $product->product_id }}">
                <i class="fas fa-link"></i> {{ __('m_tours.schedule.ui.assign_existing') }}
              </button>
              @endcan
              {{-- Crear nuevo para este tour --}}
              @can('create-tour-schedules')
              <button class="btn btn-edit btn-sm"
                data-toggle="modal"
                data-target="#modalCrearParaTour{{ $product->product_id }}">
                <i class="fas fa-plus"></i> {{ __('m_tours.schedule.ui.new') }}
              </button>
              @endcan
            </div>
          </div>

          <div class="card-body pt-3 pb-2">
            @forelse($product->schedules as $bloque)
            @php
            $assignActive = (bool) ($bloque->pivot->is_active ?? true);
            $baseCapacity = $bloque->pivot->base_capacity ?? null;
            @endphp

            <div class="schedule-row">
              <div class="schedule-info">
                <div class="fw-semibold text-truncate">
                  🕒 {{ \Carbon\Carbon::createFromTimeString($bloque->start_time)->format('g:i A') }} –
                  {{ \Carbon\Carbon::createFromTimeString($bloque->end_time)->format('g:i A') }}
                </div>
                <div class="text-muted small text-truncate">
                  {{ $bloque->label ?: __('m_tours.schedule.ui.no_label') }}
                  @if($baseCapacity)
                  · <span class="badge bg-info"><i class="fas fa-users me-1"></i>{{ $baseCapacity }} pax</span>
                  @else
                  · <span class="badge bg-secondary"><i class="fas fa-users me-1"></i>{{ $product->max_capacity ?? 15 }} pax</span>
                  @endif
                </div>

                <div class="small mt-1">
                  <span class="me-3">
                    <strong>{{ __('m_tours.schedule.ui.schedule_state') }}:</strong>
                    @if ($bloque->is_active)
                    <span class="badge bg-success">{{ __('m_tours.schedule.status.active') }}</span>
                    @else
                    <span class="badge bg-danger">{{ __('m_tours.schedule.status.inactive') }}</span>
                    @endif
                  </span>
                  <span>
                    <strong>{{ __('m_tours.schedule.ui.assignment_state') }}:</strong>
                    @if ($assignActive)
                    <span class="badge bg-success">{{ __('m_tours.schedule.status.active') }}</span>
                    @else
                    <span class="badge bg-danger">{{ __('m_tours.schedule.status.inactive') }}</span>
                    @endif
                  </span>
                </div>
              </div>

              <div class="tour-actions">
                {{-- Editar GLOBAL --}}
                @can('edit-tour-schedules')
                <button class="btn btn-edit btn-sm"
                  data-toggle="modal"
                  data-target="#modalEditarHorarioGeneral"
                  data-id="{{ $bloque->schedule_id }}"
                  data-start="{{ $bloque->start_time }}"
                  data-end="{{ $bloque->end_time }}"
                  data-label="{{ $bloque->label }}"
                  data-active="{{ $bloque->is_active ? 1 : 0 }}"
                  title="{{ __('m_tours.schedule.ui.edit_global') }}">
                  <i class="fas fa-edit"></i>
                </button>
                @endcan

                {{-- Editar CAPACIDAD del pivote --}}
                @can('edit-tour-schedules')
                <button class="btn btn-sm btn-view"
                  data-toggle="modal"
                  data-target="#modalEditarCapacidadPivote{{ $product->product_id }}_{{ $bloque->schedule_id }}"
                  title="Editar capacidad para este tour">
                  <i class="fas fa-users"></i>
                </button>
                @endcan

                {{-- Toggle ASIGNACIÓN (pivote) --}}
                @can('publish-tour-schedule-assignments')
                <form action="{{ route('admin.products.schedule.assignment.toggle', [$product->product_id, $bloque->schedule_id]) }}"
                  method="POST" class="d-inline form-assignment-toggle"
                  data-tour="{{ $product->name }}" data-active="{{ $assignActive ? 1 : 0 }}">
                  @csrf @method('PATCH')
                  <button type="submit"
                    class="btn btn-toggle btn-sm"
                    title="{{ $assignActive ? __('m_tours.schedule.ui.toggle_off_tour') : __('m_tours.schedule.ui.toggle_on_tour') }}">
                    <i class="fas fa-toggle-{{ $assignActive ? 'on' : 'off' }}"></i>
                  </button>
                </form>
                @endcan

                {{-- Quitar del tour --}}
                @can('edit-tour-schedules')
                <form action="{{ route('admin.products.schedule.detach', [$product->product_id, $bloque->schedule_id]) }}"
                  method="POST" class="d-inline form-detach" data-tour="{{ $product->name }}">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-delete btn-sm" title="{{ __('m_tours.schedule.ui.detach_from_tour') }}">
                    <i class="fas fa-unlink"></i>
                  </button>
                </form>
                @endcan
              </div>
            </div>

            {{-- Modal: EDITAR CAPACIDAD DEL PIVOTE --}}
            <div class="modal fade" id="modalEditarCapacidadPivote{{ $product->product_id }}_{{ $bloque->schedule_id }}" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-sm">
                <form action="{{ route('admin.products.schedule.update-pivot-capacity', [$product->product_id, $bloque->schedule_id]) }}"
                  method="POST" class="modal-content">
                  @csrf @method('PATCH')
                  <div class="modal-header">
                    <h5 class="modal-title">{{ __('m_tours.schedule.ui.capacity_override') }}</h5>
                    <button type="button" class="close" data-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    <p class="small text-muted mb-3">
                      <strong>{{ $product->name }}</strong><br>
                      {{ \Carbon\Carbon::createFromTimeString($bloque->start_time)->format('g:i A') }} -
                      {{ \Carbon\Carbon::createFromTimeString($bloque->end_time)->format('g:i A') }}
                    </p>

                    <div class="alert alert-info small mb-3">
                      <i class="fas fa-info-circle me-1"></i>
                      {!! __('m_tours.schedule.ui.leave_empty_for_base', ['capacity' => '<strong>'.($product->max_capacity ?? 'No definida').'</strong>']) !!}
                    </div>

                    <div class="mb-3">
                      <label class="form-label">{{ __('m_tours.schedule.ui.capacity_override') }}</label>
                      <input type="number"
                        name="base_capacity"
                        class="form-control"
                        min="1"
                        max="999"
                        value="{{ $baseCapacity }}"
                        placeholder="{{ __('m_tours.schedule.ui.use_tour_capacity') }}">
                      <small class="text-muted">{{ __('m_tours.schedule.ui.only_this_schedule') }}</small>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="submit" class="btn btn-view btn-sm">
                      <i class="fas fa-save"></i> Guardar
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancelar</button>
                  </div>
                </form>
              </div>
            </div>
            @empty
            <span class="text-muted">{{ __('m_tours.schedule.ui.no_tour_schedules') }}</span>
            @endforelse
          </div>

          <div class="card-footer text-muted pt-2 pb-2">
            @php $asignados = $product->schedules->count(); @endphp
            {{ $asignados }} {{ __('m_tours.schedule.ui.assigned_count') }}
          </div>
        </div>
      </div>

      {{-- Modal: ASIGNAR EXISTENTE (con campo opcional de capacidad) --}}
      <div class="modal fade" id="modalAsignarExistente{{ $product->product_id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
          <form action="{{ route('admin.products.schedule.attach', $product->product_id) }}"
            method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
              <h5 class="modal-title">{{ __('m_tours.schedule.ui.assign_to_tour', ['tour' => $product->name]) }}</h5>
              <button type="button" class="close" data-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">{{ __('m_tours.schedule.ui.select_schedule') }}</label>
                <select name="schedule_id" class="form-select" required>
                  <option value="" disabled selected>— {{ __('m_tours.schedule.ui.choose') }} —</option>
                  @foreach($generalSchedules as $opt)
                  @if($product->schedules->contains('schedule_id', $opt->schedule_id))
                    @continue
                  @endif
                  <option value="{{ $opt->schedule_id }}">
                    {{ \Carbon\Carbon::createFromTimeString($opt->start_time)->format('g:i A') }}
                    –
                    {{ \Carbon\Carbon::createFromTimeString($opt->end_time)->format('g:i A') }}
                    {{ $opt->label ? ' · '.$opt->label : '' }}
                  </option>
                  @endforeach
                </select>
              </div>

              <div class="alert alert-info small">
                <i class="fas fa-info-circle me-1"></i>
                {{ __('m_tours.schedule.ui.tour_base_capacity') }} <strong>{{ $product->max_capacity ?? 'No definida' }}</strong>
              </div>

              <div class="mb-3">
                <label class="form-label">{{ __('m_tours.schedule.ui.capacity_override') }} ({{ __('m_tours.common.optional') }})</label>
                <input type="number"
                  name="base_capacity"
                  class="form-control"
                  min="1"
                  max="999"
                  placeholder="{{ __('m_tours.schedule.ui.capacity_override_placeholder') }}">
                <small class="text-muted">{{ __('m_tours.schedule.ui.capacity_override_help') }}</small>
              </div>
            </div>
            <div class="modal-footer">
              <button class="btn btn-view">{{ __('m_tours.schedule.ui.assign') }}</button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('m_tours.schedule.ui.cancel') }}</button>
            </div>
          </form>
        </div>
      </div>

      {{-- Modal: CREAR PARA ESTE TOUR (con campo opcional de capacidad) --}}
      <div class="modal fade" id="modalCrearParaTour{{ $product->product_id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
          <form action="{{ route('admin.products.schedule.store') }}" method="POST" class="modal-content" autocomplete="off">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->product_id }}">
            <div class="modal-header">
              <h5 class="modal-title">{{ __('m_tours.schedule.ui.new_for_tour_title', ['tour' => $product->name]) }}</h5>
              <button type="button" class="close" data-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <div class="row g-2">
                <div class="col-6">
                  <label class="form-label">{{ __('m_tours.schedule.fields.start_time') }}</label>
                  <input type="time" name="start_time" class="form-control" required>
                </div>
                <div class="col-6">
                  <label class="form-label">{{ __('m_tours.schedule.fields.end_time') }}</label>
                  <input type="time" name="end_time" class="form-control" required>
                </div>
              </div>

              <div class="mt-2">
                <label class="form-label">{{ __('m_tours.schedule.fields.label_optional') }}</label>
                <input type="text" name="label" class="form-control" maxlength="255">
              </div>

              <div class="alert alert-info small mt-2">
                <i class="fas fa-info-circle me-1"></i>
                {{ __('m_tours.schedule.ui.tour_base_capacity') }} <strong>{{ $product->max_capacity ?? 'No definida' }}</strong>
              </div>

              <div class="mt-2">
                <label class="form-label">{{ __('m_tours.schedule.ui.capacity_override') }} ({{ __('m_tours.common.optional') }})</label>
                <input type="number"
                  name="base_capacity"
                  class="form-control"
                  min="1"
                  max="999"
                  placeholder="{{ __('m_tours.schedule.ui.capacity_override_placeholder') }}">
                <small class="text-muted">{{ __('m_tours.schedule.ui.capacity_override_help') }}</small>
              </div>

              <div class="form-check mt-3">
                <input class="form-check-input" type="checkbox" id="active-{{ $product->product_id }}" name="is_active" value="1" checked>
                <label class="form-check-label" for="active-{{ $product->product_id }}">{{ __('m_tours.schedule.fields.active') }}</label>
              </div>
            </div>
            <div class="modal-footer">
              <button class="btn btn-edit"><i class="fas fa-save"></i> {{ __('m_tours.schedule.ui.save') }}</button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('m_tours.schedule.ui.cancel') }}</button>
            </div>
          </form>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</div>
</div>

{{-- ===================== MODAL: NUEVO HORARIO GENERAL (sin capacidad) ===================== --}}
<div class="modal fade" id="modalNuevoHorarioGeneral" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form action="{{ route('admin.products.schedule.store') }}" method="POST" class="modal-content" autocomplete="off">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">{{ __('m_tours.schedule.ui.new_general_title') }}</h5>
        <button type="button" class="close" data-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-2">
          <div class="col-6">
            <label class="form-label">{{ __('m_tours.schedule.fields.start_time') }}</label>
            <input type="time" name="start_time" class="form-control" required>
          </div>
          <div class="col-6">
            <label class="form-label">{{ __('m_tours.schedule.fields.end_time') }}</label>
            <input type="time" name="end_time" class="form-control" required>
          </div>
        </div>

        <div class="mt-2">
          <label class="form-label">{{ __('m_tours.schedule.fields.label') }}</label>
          <input type="text" name="label" class="form-control" maxlength="255">
        </div>

        <div class="alert alert-info small mt-3 mb-0">
          <i class="fas fa-info-circle me-1"></i>
          {{ __('m_tours.schedule.ui.capacity_definition_info') }}
        </div>

        <div class="form-check mt-3">
          <input class="form-check-input" type="checkbox" id="active-general" name="is_active" value="1" checked>
          <label class="form-check-label" for="active-general">{{ __('m_tours.schedule.fields.active') }}</label>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-edit"><i class="fas fa-save"></i> {{ __('m_tours.schedule.ui.save') }}</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('m_tours.schedule.ui.cancel') }}</button>
      </div>
    </form>
  </div>
</div>

{{-- ===================== MODAL ÚNICO: EDITAR HORARIO GENERAL (sin capacidad) ===================== --}}
<div class="modal fade" id="modalEditarHorarioGeneral" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formEditarHorarioGeneral" action="#" method="POST" class="modal-content" autocomplete="off">
      @csrf @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title">{{ __('m_tours.schedule.ui.edit_schedule') }}</h5>
        <button type="button" class="close" data-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-2">
          <div class="col-6">
            <label class="form-label">{{ __('m_tours.schedule.fields.start_time') }}</label>
            <input type="time" id="edit-start" name="start_time" class="form-control" required>
          </div>
          <div class="col-6">
            <label class="form-label">{{ __('m_tours.schedule.fields.end_time') }}</label>
            <input type="time" id="edit-end" name="end_time" class="form-control" required>
          </div>
        </div>

        <div class="mt-2">
          <label class="form-label">{{ __('m_tours.schedule.fields.label') }}</label>
          <input type="text" id="edit-label" name="label" class="form-control" maxlength="255">
        </div>

        <div class="alert alert-info small mt-3 mb-0">
          <i class="fas fa-info-circle me-1"></i>
          Para editar capacidades específicas de tours, usa el botón <i class="fas fa-users"></i> en cada tour
        </div>

        <input type="hidden" name="is_active" value="0">
        <div class="form-check mt-3">
          <input class="form-check-input" type="checkbox" id="edit-active" name="is_active" value="1">
          <label class="form-check-label" for="edit-active">{{ __('m_tours.schedule.fields.active') }}</label>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-edit" type="submit"><i class="fas fa-save"></i> {{ __('m_tours.schedule.ui.save_changes') }}</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('m_tours.schedule.ui.cancel') }}</button>
      </div>
    </form>
  </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  // ===== Utils =====
  function timeToHHMM(t) {
    if (!t) return '';
    const m = String(t).match(/^(\d{2}):(\d{2})(?::\d{2})?$/);
    return m ? `${m[1]}:${m[2]}` : t;
  }

  // ===== Spinner + lock (solo botones) =====
  function lockAndSubmit(form, opts = {}) {
    const loadingText = opts.loadingText || @json(__('m_tours.schedule.ui.processing'));
    if (!form.checkValidity()) {
      if (typeof form.reportValidity === 'function') form.reportValidity();
      return;
    }
    const buttons = form.querySelectorAll('button');
    const submitBtn =
      form.querySelector('button[type="submit"]') ||
      form.querySelector('.btn-edit, .btn-primary, .btn-success');
    buttons.forEach(btn => {
      if (submitBtn && btn === submitBtn) return;
      btn.disabled = true;
    });
    if (submitBtn) {
      if (!submitBtn.dataset.originalHtml) submitBtn.dataset.originalHtml = submitBtn.innerHTML;
      submitBtn.innerHTML =
        '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>' + loadingText;
      submitBtn.classList.add('disabled');
      submitBtn.disabled = true;
    }
    form.querySelectorAll('input, select, textarea').forEach(el => {
      if (el.disabled) el.disabled = false;
    });
    form.submit();
  }

  // ===== Rellenar modal de edición (SIN capacidad) =====
  document.getElementById('modalEditarHorarioGeneral')?.addEventListener('show.bs.modal', function(ev) {
    const btn = ev.relatedTarget;
    if (!btn) return;
    const id = btn.getAttribute('data-id');
    const start = timeToHHMM(btn.getAttribute('data-start') || '');
    const end = timeToHHMM(btn.getAttribute('data-end') || '');
    const label = btn.getAttribute('data-label') || '';
    const active = btn.getAttribute('data-active') === '1';

    const form = document.getElementById('formEditarHorarioGeneral');
    const pattern = document.querySelector('meta[name="schedule-update-url"]')?.content;
    if (pattern) form.action = pattern.replace('___ID___', id);

    document.getElementById('edit-start').value = start;
    document.getElementById('edit-end').value = end;
    document.getElementById('edit-label').value = label;
    document.getElementById('edit-active').checked = active;
  });

  // ===== Confirmaciones con SweetAlert =====
  document.querySelectorAll('.form-toggle-global').forEach(form => {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      const label = form.getAttribute('data-label') || @json(__('m_tours.schedule.ui.this_schedule'));
      const isActive = form.getAttribute('data-active') === '1';
      Swal.fire({
        title: isActive ? @json(__('m_tours.schedule.ui.toggle_global_off_title')) : @json(__('m_tours.schedule.ui.toggle_global_on_title')),
        html: isActive ?
          @json(__('m_tours.schedule.ui.toggle_global_off_html', ['label' => ':label'])).replace(':label', label) : @json(__('m_tours.schedule.ui.toggle_global_on_html', ['label' => ':label'])).replace(':label', label),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f39c12',
        cancelButtonColor: '#6c757d',
        confirmButtonText: @json(__('m_tours.schedule.ui.yes_continue'))
      }).then(r => {
        if (r.isConfirmed) lockAndSubmit(form, {
          loadingText: @json(__('m_tours.schedule.ui.applying'))
        });
      });
    });
  });

  document.querySelectorAll('.form-assignment-toggle').forEach(form => {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      const tour = form.getAttribute('data-tour') || @json(__('m_tours.schedule.ui.this_tour'));
      const isActive = form.getAttribute('data-active') === '1';
      Swal.fire({
        title: isActive ? @json(__('m_tours.schedule.ui.toggle_assign_off_title')) : @json(__('m_tours.schedule.ui.toggle_assign_on_title')),
        html: isActive ?
          @json(__('m_tours.schedule.ui.toggle_assign_off_html', ['tour' => ':tour'])).replace(':tour', tour) : @json(__('m_tours.schedule.ui.toggle_assign_on_html', ['tour' => ':tour'])).replace(':tour', tour),
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#f39c12',
        cancelButtonColor: '#6c757d',
        confirmButtonText: @json(__('m_tours.schedule.ui.yes_continue'))
      }).then(r => {
        if (r.isConfirmed) lockAndSubmit(form, {
          loadingText: @json(__('m_tours.schedule.ui.applying'))
        });
      });
    });
  });

  document.querySelectorAll('.form-delete').forEach(form => {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      const label = form.getAttribute('data-label') || @json(__('m_tours.schedule.ui.this_schedule'));
      Swal.fire({
        title: @json(__('m_tours.schedule.ui.delete_confirm_title')),
        html: @json(__('m_tours.schedule.ui.delete_confirm_html', ['label' => ':label'])).replace(':label', label),
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#dd4b39',
        cancelButtonColor: '#6c757d',
        confirmButtonText: @json(__('m_tours.schedule.ui.yes_delete'))
      }).then(r => {
        if (r.isConfirmed) lockAndSubmit(form, {
          loadingText: @json(__('m_tours.schedule.ui.deleting'))
        });
      });
    });
  });

  document.querySelectorAll('.form-detach').forEach(form => {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      const tour = form.getAttribute('data-tour') || @json(__('m_tours.schedule.ui.this_tour'));
      Swal.fire({
        title: @json(__('m_tours.schedule.ui.detach_confirm_title')),
        html: @json(__('m_tours.schedule.ui.detach_confirm_html', ['tour' => ':tour'])).replace(':tour', tour),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: @json(__('m_tours.schedule.ui.yes_detach'))
      }).then(r => {
        if (r.isConfirmed) lockAndSubmit(form, {
          loadingText: @json(__('m_tours.schedule.ui.removing'))
        });
      });
    });
  });

  // ===== Envío modal: validación + spinner =====
  document.getElementById('formEditarHorarioGeneral')?.addEventListener('submit', function(e) {
    if (!this.checkValidity()) {
      e.preventDefault();
      Swal.fire({
        icon: 'info',
        title: @json(__('m_tours.schedule.ui.missing_fields_title')),
        text: @json(__('m_tours.schedule.ui.missing_fields_text')),
        confirmButtonColor: '#0d6efd'
      });
      return;
    }
    e.preventDefault();
    lockAndSubmit(this, {
      loadingText: @json(__('m_tours.schedule.ui.saving_changes'))
    });
  });

  // ===== Flash messages =====
  @if(session('success'))
  Swal.fire({
    icon: 'success',
    title: @json(__('m_tours.common.success_title')),
    text: @json(session('success')),
    timer: 2000,
    showConfirmButton: false
  });
  @endif
  @if(session('error'))
  Swal.fire({
    icon: 'error',
    title: @json(__('m_tours.common.error_title')),
    text: @json(session('error')),
    timer: 2600,
    showConfirmButton: false
  });
  @endif
  @if($errors -> any())
  Swal.fire({
    icon: 'error',
    title: @json(__('m_tours.schedule.ui.could_not_save')),
    html: `<ul style="text-align:left;margin:0;padding-left:18px;">{!! collect($errors->all())->map(fn($e)=>"<li>".e($e)."</li>")->implode('') !!}</ul>`,
    confirmButtonColor: '#dc3545'
  });
  @endif
</script>
@stop
