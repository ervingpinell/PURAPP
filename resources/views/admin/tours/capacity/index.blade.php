@extends('adminlte::page')

@section('title', 'Gestión de Capacidades')

@section('content_header')
  <h1><i class="fas fa-users me-2"></i> Gestión de Capacidades</h1>
@stop

@section('content')

<div class="card">
  <div class="card-header">
    <ul class="nav nav-tabs card-header-tabs" role="tablist">
      <li class="nav-item">
        <a class="nav-link {{ $tab === 'global' ? 'active' : '' }}"
           href="{{ route('admin.tours.capacity.index', ['tab' => 'global']) }}">
          <i class="fas fa-globe me-1"></i> Globales
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ $tab === 'by-tour' ? 'active' : '' }}"
           href="{{ route('admin.tours.capacity.index', ['tab' => 'by-tour']) }}">
          <i class="fas fa-clock me-1"></i> Por Tour + Horario
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ $tab === 'day-schedules' ? 'active' : '' }}"
           href="{{ route('admin.tours.capacity.index', ['tab' => 'day-schedules']) }}">
          <i class="fas fa-calendar-check me-1"></i> Overrides Día+Horario
        </a>
      </li>
    </ul>
  </div>

  <div class="card-body">
    {{-- TAB 1: Capacidades Globales --}}
    @if($tab === 'global')
      <div class="alert alert-info">
        <i class="fas fa-info-circle me-1"></i>
        <strong>Capacidades globales:</strong> Define el límite base para cada tour (todos los días y horarios).
      </div>

      <table class="table table-sm table-striped">
        <thead>
          <tr>
            <th>Tour</th>
            <th>Tipo</th>
            <th style="width: 150px;">Capacidad Global</th>
            <th style="width: 100px;">Nivel</th>
          </tr>
        </thead>
        <tbody>
          @foreach($tours as $tour)
            <tr>
              <td>{{ $tour->name }}</td>
              <td>
                <span class="badge bg-secondary">
                  {{ $tour->tourType->name ?? '—' }}
                </span>
              </td>
              <td>
                <form action="{{ route('admin.tours.capacity.update-tour', $tour) }}"
                      method="POST"
                      class="d-flex gap-2">
                  @csrf
                  @method('PATCH')
                  <input type="number"
                         name="max_capacity"
                         value="{{ $tour->max_capacity ?? 15 }}"
                         class="form-control form-control-sm"
                         min="1"
                         max="999"
                         required>
                  <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-save"></i>
                  </button>
                </form>
              </td>
              <td>
                <span class="badge bg-info">Base</span>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif

    {{-- TAB 2: Por Tour + Horario --}}
    @if($tab === 'by-tour')
      <div class="alert alert-info">
        <i class="fas fa-info-circle me-1"></i>
        <strong>Por Tour + Horario:</strong> Override de capacidad específico para cada horario de cada tour. Estos overrides tienen prioridad sobre la capacidad global del tour.
      </div>

      @foreach($tours as $tour)
        <div class="card mb-3">
          <div class="card-header bg-dark text-white">
            <h6 class="mb-0">
              {{ $tour->name }}
              <span class="badge bg-info ms-2">Base: {{ $tour->max_capacity ?? '—' }}</span>
            </h6>
          </div>
          <div class="card-body p-0">
            <table class="table table-sm table-striped mb-0">
              <thead>
                <tr>
                  <th>Horario</th>
                  <th style="width: 200px;">Capacidad Override</th>
                  <th style="width: 120px;">Nivel</th>
                </tr>
              </thead>
              <tbody>
                @forelse($tour->schedules as $schedule)
                  <tr>
                    <td>
                      <strong>{{ date('g:i A', strtotime($schedule->start_time)) }} - {{ date('g:i A', strtotime($schedule->end_time)) }}</strong>
                      @if($schedule->label)
                        <br><small class="text-muted">{{ $schedule->label }}</small>
                      @endif
                    </td>
                    <td>
                      <form action="{{ route('admin.tours.schedule.update-pivot-capacity', [$tour, $schedule]) }}"
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
                               placeholder="Usar {{ $tour->max_capacity ?? 15 }}"
                               style="max-width: 120px;">
                        <button type="submit" class="btn btn-primary btn-sm" title="Guardar">
                          <i class="fas fa-save"></i>
                        </button>
                      </form>
                      <small class="text-muted d-block mt-1">Vacío = usa capacidad global ({{ $tour->max_capacity ?? 15 }})</small>
                    </td>
                    <td>
                      @if($schedule->pivot->base_capacity)
                        <span class="badge bg-success">Override</span>
                      @else
                        <span class="badge bg-secondary">Global</span>
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="3" class="text-center text-muted py-3">
                      Este tour no tiene horarios asignados
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
        <strong>Día + Horario:</strong> Override de máxima prioridad para un día y horario específico. Estos se gestionan desde la vista de "Disponibilidad y Capacidad".
      </div>

      <table class="table table-sm table-striped">
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Tour</th>
            <th>Horario</th>
            <th>Capacidad</th>
            <th style="width: 100px;">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($dayScheduleOverrides as $override)
            <tr>
              <td>{{ Carbon\Carbon::parse($override->date)->format('d/m/Y') }}</td>
              <td>{{ $override->tour->name }}</td>
              <td>
                <strong>{{ date('g:i A', strtotime($override->schedule->start_time)) }}</strong>
              </td>
              <td>
                @if($override->is_blocked)
                  <span class="badge bg-danger">BLOQUEADO</span>
                @else
                  <span class="badge bg-success">{{ $override->max_capacity ?? '∞' }}</span>
                @endif
              </td>
              <td>
                <form action="{{ route('admin.tours.capacity.destroy', $override) }}"
                      method="POST"
                      class="d-inline"
                      onsubmit="return confirm('¿Eliminar este override?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-sm">
                    <i class="fas fa-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center text-muted py-3">No hay overrides de día + horario</td>
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
@if (session('success'))
  Swal.fire({ icon: 'success', title: 'Éxito', text: @json(session('success')), timer: 2000, showConfirmButton: false });
@endif
@if (session('error'))
  Swal.fire({ icon: 'error', title: 'Error', text: @json(session('error')), timer: 2600, showConfirmButton: false });
@endif
</script>
@endpush
