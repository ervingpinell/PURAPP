@extends('adminlte::page')

@section('title', 'Horarios de Tours')

@section('content_header')
  <h1>Gestión de Horarios</h1>
@stop

@section('content')
<div class="p-3">

  @php
    // Si prefieres, pásalo desde el controller (ya lo hace index()).
    // $generalSchedules = \App\Models\Schedule::orderBy('start_time')->get();
    // $tours = \App\Models\Tour::with(['schedules' => fn($q) => $q->orderBy('start_time')])->orderBy('name')->get();
  @endphp

  {{-- ===================== HORARIOS GENERALES ===================== --}}
  <div class="card mb-4 shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Horarios generales</h5>
      <button class="btn btn-edit btn-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoHorarioGeneral">
        <i class="fas fa-plus"></i> Nuevo horario
      </button>
    </div>

    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped table-hover align-middle mb-0">
          <thead class="bg-primary text-white">
            <tr>
              <th class="text-nowrap">Horario</th>
              <th>Etiqueta</th>
              <th class="text-center">Capacidad</th>
              <th class="text-center">Estado</th>
              <th class="text-center" style="width:260px">Acciones</th>
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
                <td class="text-center">{{ $s->max_capacity ?? '—' }}</td>
                <td class="text-center">
                  <span class="badge {{ $s->is_active ? 'bg-success' : 'bg-secondary' }}">
                    {{ $s->is_active ? 'Activo' : 'Inactivo' }}
                  </span>
                </td>
                <td class="text-center">
                  {{-- Editar (modal único) --}}
                  <button class="btn btn-edit btn-sm"
                          data-bs-toggle="modal"
                          data-bs-target="#modalEditarHorarioGeneral"
                          data-id="{{ $s->schedule_id }}"
                          data-start="{{ $s->start_time }}"
                          data-end="{{ $s->end_time }}"
                          data-label="{{ $s->label }}"
                          data-capacity="{{ $s->max_capacity }}"
                          data-active="{{ $s->is_active ? 1 : 0 }}"
                          title="Editar (global)">
                    <i class="fas fa-edit"></i>
                  </button>

                  {{-- Toggle GLOBAL --}}
                  <form action="{{ route('admin.tours.schedule.toggle', $s->schedule_id) }}"
                        method="POST" class="d-inline form-toggle-global"
                        data-label="{{ $s->label ?: ( \Carbon\Carbon::createFromTimeString($s->start_time)->format('g:i A') . ' - ' . \Carbon\Carbon::createFromTimeString($s->end_time)->format('g:i A') ) }}"
                        data-active="{{ $s->is_active ? 1 : 0 }}">
                    @csrf @method('PUT')
                    <button type="submit" class="btn btn-toggle btn-sm" title="Activar/Desactivar (global)">
                      <i class="fas fa-toggle-{{ $s->is_active ? 'on' : 'off' }}"></i>
                    </button>
                  </form>

                  {{-- Eliminar GLOBAL --}}
                  <form action="{{ route('admin.tours.schedule.destroy', $s->schedule_id) }}"
                        method="POST" class="d-inline form-delete"
                        data-label="{{ $s->label ?: ( \Carbon\Carbon::createFromTimeString($s->start_time)->format('g:i A') . ' - ' . \Carbon\Carbon::createFromTimeString($s->end_time)->format('g:i A') ) }}">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-delete btn-sm" title="Eliminar (global)">
                      <i class="fas fa-trash"></i>
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-muted py-4">No hay horarios generales.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- ===================== TOURS Y SUS HORARIOS ===================== --}}
  <div class="row">
    @foreach($tours as $tour)
      <div class="col-md-6 mb-4">
        <div class="card shadow-sm h-100">
          <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ $tour->name }}</h5>
            <div class="d-flex gap-2">
              {{-- Asignar existente --}}
              <button class="btn btn-view btn-sm"
                      data-bs-toggle="modal"
                      data-bs-target="#modalAsignarExistente{{ $tour->tour_id }}">
                <i class="fas fa-link"></i> Asignar existente
              </button>
              {{-- Crear nuevo para este tour --}}
              <button class="btn btn-edit btn-sm"
                      data-bs-toggle="modal"
                      data-bs-target="#modalCrearParaTour{{ $tour->tour_id }}">
                <i class="fas fa-plus"></i> Nuevo
              </button>
            </div>
          </div>

          <div class="card-body">
            @forelse($tour->schedules as $bloque)
              @php
                $assignActive = (bool) ($bloque->pivot->is_active ?? true);
              @endphp

              <div class="d-flex justify-content-between align-items-center border rounded p-2 mb-2">
                <div>
                  <div class="fw-semibold">
                    🕒 {{ \Carbon\Carbon::createFromTimeString($bloque->start_time)->format('g:i A') }} –
                    {{ \Carbon\Carbon::createFromTimeString($bloque->end_time)->format('g:i A') }}
                  </div>
                  <div class="text-muted small">
                    {{ $bloque->label ?: 'Sin etiqueta' }} · Cap: {{ $bloque->max_capacity ?? '—' }}
                  </div>

                  {{-- Estados diferenciados (spans dinámicos) --}}
<div class="small mt-1">
  <span class="me-3">
    <strong>Horario:</strong>
    @if ($bloque->is_active)
      <span class="badge bg-success">Activo</span>
    @else
      <span class="badge bg-danger">Inactivo</span>
    @endif
  </span>
  <span>
    <strong>Asignación:</strong>
    @if ($assignActive)
      <span class="badge bg-success">Activa</span>
    @else
      <span class="badge bg-danger">Inactiva</span>
    @endif
  </span>
</div>

                </div>

                <div class="d-inline-flex gap-2">
                  {{-- Editar GLOBAL (misma entidad Schedule) --}}
                  <button class="btn btn-edit btn-sm"
                          data-bs-toggle="modal"
                          data-bs-target="#modalEditarHorarioGeneral"
                          data-id="{{ $bloque->schedule_id }}"
                          data-start="{{ $bloque->start_time }}"
                          data-end="{{ $bloque->end_time }}"
                          data-label="{{ $bloque->label }}"
                          data-capacity="{{ $bloque->max_capacity }}"
                          data-active="{{ $bloque->is_active ? 1 : 0 }}"
                          title="Editar (global)">
                    <i class="fas fa-edit"></i>
                  </button>

                  {{-- Toggle ASIGNACIÓN (pivote) --}}
                  <form action="{{ route('admin.tours.schedule.assignment.toggle', [$tour->tour_id, $bloque->schedule_id]) }}"
                        method="POST" class="d-inline form-assignment-toggle"
                        data-tour="{{ $tour->name }}" data-active="{{ $assignActive ? 1 : 0 }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-toggle btn-sm" title="Activar/Desactivar en este tour">
                      <i class="fas fa-toggle-{{ $assignActive ? 'on' : 'off' }}"></i>
                    </button>
                  </form>

                  {{-- Quitar del tour (DETACH) --}}
                  <form action="{{ route('admin.tours.schedule.detach', [$tour->tour_id, $bloque->schedule_id]) }}"
                        method="POST" class="d-inline form-detach" data-tour="{{ $tour->name }}">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-delete btn-sm" title="Quitar del tour">
                      <i class="fas fa-unlink"></i>
                    </button>
                  </form>
                </div>
              </div>
            @empty
              <span class="text-muted">Este tour aún no tiene horarios.</span>
            @endforelse
          </div>

          <div class="card-footer text-muted">
            @php $asignados = $tour->schedules->count(); @endphp
            {{ $asignados }} horario{{ $asignados === 1 ? '' : 's' }} asignado{{ $asignados === 1 ? '' : 's' }}
          </div>
        </div>
      </div>

      {{-- Modal: ASIGNAR EXISTENTE --}}
      <div class="modal fade" id="modalAsignarExistente{{ $tour->tour_id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
          <form action="{{ route('admin.tours.schedule.attach', $tour->tour_id) }}"
                method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
              <h5 class="modal-title">Asignar horario a "{{ $tour->name }}"</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">Selecciona un horario</label>
                <select name="schedule_id" class="form-select" required>
                  <option value="" disabled selected>— Elige —</option>
                  @foreach($generalSchedules as $opt)
                    <option value="{{ $opt->schedule_id }}">
                      {{ \Carbon\Carbon::createFromTimeString($opt->start_time)->format('g:i A') }}
                      –
                      {{ \Carbon\Carbon::createFromTimeString($opt->end_time)->format('g:i A') }}
                      {{ $opt->label ? ' · '.$opt->label : '' }}
                    </option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="modal-footer">
              <button class="btn btn-view">Asignar</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            </div>
          </form>
        </div>
      </div>

      {{-- Modal: CREAR PARA ESTE TOUR --}}
      <div class="modal fade" id="modalCrearParaTour{{ $tour->tour_id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
          <form action="{{ route('admin.tours.schedule.store') }}" method="POST" class="modal-content" autocomplete="off">
            @csrf
            <input type="hidden" name="tour_id" value="{{ $tour->tour_id }}">
            <div class="modal-header">
              <h5 class="modal-title">Nuevo horario para "{{ $tour->name }}"</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <div class="row g-2">
                <div class="col-6">
                  <label class="form-label">Inicio</label>
                  <input type="time" name="start_time" class="form-control" required>
                </div>
                <div class="col-6">
                  <label class="form-label">Fin</label>
                  <input type="time" name="end_time" class="form-control" required>
                </div>
              </div>
              <div class="row g-2 mt-2">
                <div class="col-6">
                  <label class="form-label">Capacidad máx.</label>
                  <input type="number" name="max_capacity" class="form-control" min="1" value="20" required>
                </div>
                <div class="col-6">
                  <label class="form-label">Etiqueta (opcional)</label>
                  <input type="text" name="label" class="form-control" maxlength="255">
                </div>
              </div>
              <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" id="active-{{ $tour->tour_id }}" name="is_active" value="1" checked>
                <label class="form-check-label" for="active-{{ $tour->tour_id }}">Activo</label>
              </div>
            </div>
            <div class="modal-footer">
              <button class="btn btn-edit"><i class="fas fa-save"></i> Guardar</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            </div>
          </form>
        </div>
      </div>
    @endforeach
  </div>
</div>

{{-- ===================== MODAL: NUEVO HORARIO GENERAL ===================== --}}
<div class="modal fade" id="modalNuevoHorarioGeneral" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form action="{{ route('admin.tours.schedule.store') }}" method="POST" class="modal-content" autocomplete="off">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">Nuevo horario general</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-2">
          <div class="col-6">
            <label class="form-label">Inicio</label>
            <input type="time" name="start_time" class="form-control" required>
          </div>
          <div class="col-6">
            <label class="form-label">Fin</label>
            <input type="time" name="end_time" class="form-control" required>
          </div>
        </div>
        <div class="row g-2 mt-2">
          <div class="col-6">
            <label class="form-label">Capacidad máx.</label>
            <input type="number" name="max_capacity" class="form-control" min="1" value="20" required>
          </div>
          <div class="col-6">
            <label class="form-label">Etiqueta (opcional)</label>
            <input type="text" name="label" class="form-control" maxlength="255">
          </div>
        </div>
        <div class="form-check mt-2">
          <input class="form-check-input" type="checkbox" id="active-general" name="is_active" value="1" checked>
          <label class="form-check-label" for="active-general">Activo</label>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-edit"><i class="fas fa-save"></i> Guardar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </form>
  </div>
</div>

{{-- ===================== MODAL ÚNICO: EDITAR HORARIO GENERAL ===================== --}}
<div class="modal fade" id="modalEditarHorarioGeneral" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formEditarHorarioGeneral" action="#" method="POST" class="modal-content" autocomplete="off">
      @csrf @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title">Editar horario</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-2">
          <div class="col-6">
            <label class="form-label">Inicio</label>
            <input type="time" id="edit-start" name="start_time" class="form-control" required>
          </div>
          <div class="col-6">
            <label class="form-label">Fin</label>
            <input type="time" id="edit-end" name="end_time" class="form-control" required>
          </div>
        </div>
        <div class="row g-2 mt-2">
          <div class="col-6">
            <label class="form-label">Capacidad máx.</label>
            <input type="number" id="edit-capacity" name="max_capacity" class="form-control" min="1" required>
          </div>
          <div class="col-6">
            <label class="form-label">Etiqueta</label>
            <input type="text" id="edit-label" name="label" class="form-control" maxlength="255">
          </div>
        </div>
        <div class="form-check mt-2">
          <input class="form-check-input" type="checkbox" id="edit-active" name="is_active" value="1">
          <label class="form-check-label" for="edit-active">Activo</label>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-edit"><i class="fas fa-save"></i> Guardar cambios</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </form>
  </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Rellenar modal de edición (global)
document.getElementById('modalEditarHorarioGeneral')?.addEventListener('show.bs.modal', function (ev) {
  const btn = ev.relatedTarget; if (!btn) return;
  const id = btn.getAttribute('data-id');
  const start = btn.getAttribute('data-start') || '';
  const end = btn.getAttribute('data-end') || '';
  const label = btn.getAttribute('data-label') || '';
  const cap = btn.getAttribute('data-capacity') || '';
  const active = btn.getAttribute('data-active') === '1';

  const form = document.getElementById('formEditarHorarioGeneral');
  form.action = "{{ route('admin.tours.schedule.update', '__ID__') }}".replace('__ID__', id);
  document.getElementById('edit-start').value = start;
  document.getElementById('edit-end').value = end;
  document.getElementById('edit-label').value = label;
  document.getElementById('edit-capacity').value = cap;
  document.getElementById('edit-active').checked = active;
});

// Confirmación SweetAlert: Toggle GLOBAL
document.querySelectorAll('.form-toggle-global').forEach(form => {
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    const label = form.getAttribute('data-label') || 'este horario';
    const isActive = form.getAttribute('data-active') === '1';
    Swal.fire({
      title: isActive ? '¿Desactivar horario (global)?' : '¿Activar horario (global)?',
      html: isActive
        ? 'Se desactivará <b>'+label+'</b> para todos los tours.'
        : 'Se activará <b>'+label+'</b> para todos los tours.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#f39c12',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Sí, continuar'
    }).then(r => { if (r.isConfirmed) form.submit(); });
  });
});

// Confirmación SweetAlert: Toggle ASIGNACIÓN (pivote)
document.querySelectorAll('.form-assignment-toggle').forEach(form => {
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    const tour = form.getAttribute('data-tour') || 'este tour';
    const isActive = form.getAttribute('data-active') === '1';
    Swal.fire({
      title: isActive ? '¿Desactivar en este tour?' : '¿Activar en este tour?',
      html: isActive
        ? 'La asignación de este horario quedará <b>inactiva</b> para <b>'+tour+'</b>.'
        : 'La asignación de este horario quedará <b>activa</b> para <b>'+tour+'</b>.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#f39c12',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Sí, continuar'
    }).then(r => { if (r.isConfirmed) form.submit(); });
  });
});

// Confirmación SweetAlert: Eliminar GLOBAL
document.querySelectorAll('.form-delete').forEach(form => {
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    const label = form.getAttribute('data-label') || 'este horario';
    Swal.fire({
      title: '¿Eliminar definitivamente?',
      html: 'Se eliminará <b>'+label+'</b> (global) y no podrás deshacerlo.',
      icon: 'error',
      showCancelButton: true,
      confirmButtonColor: '#dd4b39',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Sí, eliminar'
    }).then(r => { if (r.isConfirmed) form.submit(); });
  });
});

// Confirmación SweetAlert: Quitar del tour (DETACH)
document.querySelectorAll('.form-detach').forEach(form => {
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    const tour = form.getAttribute('data-tour') || 'este tour';
    Swal.fire({
      title: '¿Quitar del tour?',
      html: 'El horario se <b>desasignará</b> de <b>'+tour+'</b>.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#dc3545',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Sí, quitar'
    }).then(r => { if (r.isConfirmed) form.submit(); });
  });
});

// Flash messages
@if (session('success'))
  Swal.fire({ icon: 'success', title: 'Éxito', text: @json(session('success')), timer: 2000, showConfirmButton: false });
@endif
@if (session('error'))
  Swal.fire({ icon: 'error', title: 'Error', text: @json(session('error')), timer: 2600, showConfirmButton: false });
@endif
</script>
@stop
