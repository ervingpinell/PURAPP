@extends('adminlte::page')

@section('title', __('m_tours.schedule.ui.page_title'))

@section('content_header')
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <h1 class="mb-0">{{ __('m_tours.schedule.ui.page_heading') }}</h1>
  </div>
  {{-- Patrón para construir la URL de update (se reemplaza ___ID___ en JS) --}}
  <meta name="schedule-update-url" content="{{ route('admin.tours.schedule.update', ['schedule' => '___ID___']) }}">
@stop

@push('css')
<style>
  /* ==== Layout general ==== */
  .card { border-radius: .5rem; }
  .card-header { font-weight: 600; }
  table { width: 100%; }

  /* ==== Tabla responsive sin romper estructura ==== */
  .table-responsive { overflow-x: auto; }
  table thead th { white-space: nowrap; }

  /* ==== Botones y badges ==== */
  .btn { border-radius: .35rem; }
  .btn-edit { background-color: #00a65a; color: #fff; border: none; }
  .btn-view { background-color: #3c8dbc; color: #fff; border: none; }
  .btn-delete { background-color: #dd4b39; color: #fff; border: none; }
  .btn-toggle { background-color: #f39c12; color: #fff; border: none; }
  .btn:hover, .btn:focus { opacity: .88; }

  .badge.bg-success { background-color: #00a65a !important; }
  .badge.bg-danger { background-color: #dd4b39 !important; }
  .badge.bg-secondary { background-color: #6c757d !important; }

  /* ==== Tours Cards ==== */
  .tour-header{
    display:flex; align-items:center; justify-content:space-between;
    flex-wrap:wrap; gap:.5rem;
  }
  .tour-title{
    flex:1 1 auto; min-width:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
  }
  .tour-actions{ display:flex; flex-wrap:wrap; gap:.4rem; justify-content:flex-end; }
  @media (max-width: 768px){ .tour-actions{ justify-content:center; } }

  /* ==== Schedule Row ==== */
  .schedule-row{
    display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap;
    border:1px solid rgba(255,255,255,0.1); background:#222d32; color:#fff;
    border-radius:.4rem; padding:.75rem; margin-bottom:.75rem;
  }
  .schedule-row .schedule-info{ flex:1 1 auto; min-width:0; }
  .schedule-row .tour-actions{ display:flex; gap:.4rem; flex-wrap:nowrap; align-items:center; }

  @media (max-width: 768px){
    .schedule-row{ flex-direction:column; align-items:stretch; }
    .schedule-row .tour-actions{ justify-content:center; margin-top:.5rem; }
  }
</style>
@endpush

@section('content')
<div class="p-3">

  {{-- ===================== HORARIOS GENERALES ===================== --}}
  <div class="card mb-4 shadow-sm">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center flex-wrap gap-2">
      <h5 class="mb-0">{{ __('m_tours.schedule.ui.general_title') }}</h5>
      <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoHorarioGeneral">
        <i class="fas fa-plus me-1"></i> {{ __('m_tours.schedule.ui.new_schedule') }}
      </button>
    </div>

    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped table-hover align-middle mb-0">
          <thead class="bg-primary text-white">
            <tr>
              <th>{{ __('m_tours.schedule.ui.time_range') }}</th>
              <th>{{ __('m_tours.schedule.fields.label') }}</th>
              <th class="text-center">{{ __('m_tours.schedule.fields.max_capacity') }}</th>
              <th class="text-center">{{ __('m_tours.schedule.ui.state') }}</th>
              <th class="text-center">{{ __('m_tours.schedule.ui.actions') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse($generalSchedules as $s)
              <tr>
                <td>
                  {{ \Carbon\Carbon::createFromTimeString($s->start_time)->format('g:i A') }}
                  –
                  {{ \Carbon\Carbon::createFromTimeString($s->end_time)->format('g:i A') }}
                </td>
                <td>{{ $s->label ?: '—' }}</td>
                <td class="text-center">{{ $s->max_capacity ?? '—' }}</td>
                <td class="text-center">
                  <span class="badge {{ $s->is_active ? 'bg-success' : 'bg-secondary' }}">
                    {{ $s->is_active ? __('m_tours.schedule.status.active') : __('m_tours.schedule.status.inactive') }}
                  </span>
                </td>
                <td class="text-center">
                  <div class="d-flex justify-content-center flex-wrap gap-1">
                    {{-- Editar (modal único) --}}
                    <button class="btn btn-edit btn-sm"
                      data-bs-toggle="modal"
                      data-bs-target="#modalEditarHorarioGeneral"
                      data-id="{{ $s->schedule_id }}"
                      data-start="{{ $s->start_time }}"
                      data-end="{{ $s->end_time }}"
                      data-label="{{ $s->label }}"
                      data-capacity="{{ $s->max_capacity }}"
                      data-active="{{ $s->is_active ? 1 : 0 }}">
                      <i class="fas fa-edit"></i>
                    </button>

                    {{-- Toggle GLOBAL --}}
                    <form action="{{ route('admin.tours.schedule.toggle', $s->schedule_id) }}"
                          method="POST" class="d-inline form-toggle-global"
                          data-label="{{ $s->label ?: 'Horario' }}"
                          data-active="{{ $s->is_active ? 1 : 0 }}">
                      @csrf @method('PUT')
                      <button type="submit" class="btn btn-toggle btn-sm" title="{{ __('m_tours.schedule.ui.toggle_global_title') }}">
                        <i class="fas fa-toggle-{{ $s->is_active ? 'on' : 'off' }}"></i>
                      </button>
                    </form>

                    {{-- Eliminar GLOBAL --}}
                    <form action="{{ route('admin.tours.schedule.destroy', $s->schedule_id) }}"
                          method="POST" class="d-inline form-delete"
                          data-label="{{ $s->label ?: 'Horario' }}">
                      @csrf @method('DELETE')
                      <button type="submit" class="btn btn-delete btn-sm" title="{{ __('m_tours.schedule.ui.delete_forever') }}">
                        <i class="fas fa-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-muted py-3">{{ __('m_tours.schedule.ui.no_general') }}</td>
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
      <div class="col-lg-6 col-md-12 mb-4">
        <div class="card shadow-sm h-100">
          <div class="card-header bg-dark text-white tour-header">
            <h5 class="mb-0 tour-title" title="{{ $tour->name }}">{{ $tour->name }}</h5>
            <div class="tour-actions">
              {{-- Asignar existente --}}
              <button class="btn btn-view btn-sm"
                      data-bs-toggle="modal"
                      data-bs-target="#modalAsignarExistente{{ $tour->tour_id }}">
                <i class="fas fa-link me-1"></i>{{ __('m_tours.schedule.ui.assign_existing') }}
              </button>
              {{-- Crear nuevo para este tour --}}
              <button class="btn btn-edit btn-sm"
                      data-bs-toggle="modal"
                      data-bs-target="#modalCrearParaTour{{ $tour->tour_id }}">
                <i class="fas fa-plus me-1"></i>{{ __('m_tours.schedule.ui.new') }}
              </button>
            </div>
          </div>

          <div class="card-body">
            @forelse($tour->schedules as $bloque)
              @php $assignActive = (bool) ($bloque->pivot->is_active ?? true); @endphp

              <div class="schedule-row">
                <div class="schedule-info">
                  <div class="fw-semibold">
                    🕒
                    {{ \Carbon\Carbon::createFromTimeString($bloque->start_time)->format('g:i A') }}
                    – {{ \Carbon\Carbon::createFromTimeString($bloque->end_time)->format('g:i A') }}
                  </div>
                  <div class="small text-muted">
                    {{ $bloque->label ?: __('m_tours.schedule.ui.no_label') }}
                    · {{ __('m_tours.schedule.fields.max_capacity') }}: {{ $bloque->max_capacity ?? '—' }}
                  </div>

                  <div class="small mt-1">
                    <strong>{{ __('m_tours.schedule.ui.schedule_state') }}:</strong>
                    <span class="badge {{ $bloque->is_active ? 'bg-success' : 'bg-danger' }}">
                      {{ $bloque->is_active ? __('m_tours.schedule.status.active') : __('m_tours.schedule.status.inactive') }}
                    </span>
                    <strong class="ms-2">{{ __('m_tours.schedule.ui.assignment_state') }}:</strong>
                    <span class="badge {{ $assignActive ? 'bg-success' : 'bg-danger' }}">
                      {{ $assignActive ? __('m_tours.schedule.status.active') : __('m_tours.schedule.status.inactive') }}
                    </span>
                  </div>
                </div>

                <div class="tour-actions">
                  {{-- Editar GLOBAL --}}
                  <button class="btn btn-edit btn-sm"
                          data-bs-toggle="modal"
                          data-bs-target="#modalEditarHorarioGeneral"
                          data-id="{{ $bloque->schedule_id }}"
                          data-start="{{ $bloque->start_time }}"
                          data-end="{{ $bloque->end_time }}"
                          data-label="{{ $bloque->label }}"
                          data-capacity="{{ $bloque->max_capacity }}"
                          data-active="{{ $bloque->is_active ? 1 : 0 }}">
                    <i class="fas fa-edit"></i>
                  </button>

                  {{-- Toggle ASIGNACIÓN (pivote) --}}
                  <form action="{{ route('admin.tours.schedule.assignment.toggle', [$tour->tour_id, $bloque->schedule_id]) }}"
                        method="POST" class="d-inline form-assignment-toggle"
                        data-tour="{{ $tour->name }}" data-active="{{ $assignActive ? 1 : 0 }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-toggle btn-sm"
                            title="{{ $assignActive ? __('m_tours.schedule.ui.toggle_off_tour') : __('m_tours.schedule.ui.toggle_on_tour') }}">
                      <i class="fas fa-toggle-{{ $assignActive ? 'on' : 'off' }}"></i>
                    </button>
                  </form>

                  {{-- Quitar del tour --}}
                  <form action="{{ route('admin.tours.schedule.detach', [$tour->tour_id, $bloque->schedule_id]) }}"
                        method="POST" class="d-inline form-detach" data-tour="{{ $tour->name }}">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-delete btn-sm" title="{{ __('m_tours.schedule.ui.detach_from_tour') }}">
                      <i class="fas fa-unlink"></i>
                    </button>
                  </form>
                </div>
              </div>
            @empty
              <span class="text-muted">{{ __('m_tours.schedule.ui.no_tour_schedules') }}</span>
            @endforelse
          </div>

          <div class="card-footer text-muted small">
            {{ $tour->schedules->count() }} {{ __('m_tours.schedule.ui.assigned_count') }}
          </div>
        </div>
      </div>

      {{-- ===== Modal: ASIGNAR EXISTENTE (por tour) ===== --}}
      <div class="modal fade" id="modalAsignarExistente{{ $tour->tour_id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
          <form action="{{ route('admin.tours.schedule.attach', $tour->tour_id) }}"
                method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
              <h5 class="modal-title">{{ __('m_tours.schedule.ui.assign_to_tour', ['tour' => $tour->name]) }}</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">{{ __('m_tours.schedule.ui.select_schedule') }}</label>
                <select name="schedule_id" class="form-select" required>
                  <option value="" disabled selected>— {{ __('m_tours.schedule.ui.choose') }} —</option>
                  @foreach($generalSchedules as $opt)
                    @php
                      $oStart = \Carbon\Carbon::createFromTimeString($opt->start_time)->format('g:i A');
                      $oEnd   = \Carbon\Carbon::createFromTimeString($opt->end_time)->format('g:i A');
                    @endphp
                    <option value="{{ $opt->schedule_id }}">
                      {{ $oStart }} – {{ $oEnd }}{{ $opt->label ? ' · '.$opt->label : '' }}
                    </option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="modal-footer flex-wrap gap-2">
              <button class="btn btn-view"><i class="fas fa-link me-1"></i>{{ __('m_tours.schedule.ui.assign') }}</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('m_tours.schedule.ui.cancel') }}</button>
            </div>
          </form>
        </div>
      </div>

      {{-- ===== Modal: CREAR PARA ESTE TOUR (por tour) ===== --}}
      <div class="modal fade" id="modalCrearParaTour{{ $tour->tour_id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
          <form action="{{ route('admin.tours.schedule.store') }}" method="POST" class="modal-content" autocomplete="off">
            @csrf
            <input type="hidden" name="tour_id" value="{{ $tour->tour_id }}">
            <div class="modal-header">
              <h5 class="modal-title">{{ __('m_tours.schedule.ui.new_for_tour_title', ['tour' => $tour->name]) }}</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
              <div class="row g-2 mt-2">
                <div class="col-6">
                  <label class="form-label">{{ __('m_tours.schedule.fields.max_capacity') }}</label>
                  <input type="number" name="max_capacity" class="form-control" min="1" value="20" required>
                </div>
                <div class="col-6">
                  <label class="form-label">{{ __('m_tours.schedule.fields.label_optional') }}</label>
                  <input type="text" name="label" class="form-control" maxlength="255">
                </div>
              </div>
              <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" id="active-{{ $tour->tour_id }}" name="is_active" value="1" checked>
                <label class="form-check-label" for="active-{{ $tour->tour_id }}">{{ __('m_tours.schedule.fields.active') }}</label>
              </div>
            </div>
            <div class="modal-footer flex-wrap gap-2">
              <button class="btn btn-edit"><i class="fas fa-save me-1"></i> {{ __('m_tours.schedule.ui.save') }}</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('m_tours.schedule.ui.cancel') }}</button>
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
        <h5 class="modal-title">{{ __('m_tours.schedule.ui.new_general_title') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
        <div class="row g-2 mt-2">
          <div class="col-6">
            <label class="form-label">{{ __('m_tours.schedule.fields.max_capacity') }}</label>
            <input type="number" name="max_capacity" class="form-control" min="1" value="20" required>
          </div>
          <div class="col-6">
            <label class="form-label">{{ __('m_tours.schedule.fields.label') }}</label>
            <input type="text" name="label" class="form-control" maxlength="255">
          </div>
        </div>
        <div class="form-check mt-2">
          <input class="form-check-input" type="checkbox" id="active-general" name="is_active" value="1" checked>
          <label class="form-check-label" for="active-general">{{ __('m_tours.schedule.fields.active') }}</label>
        </div>
      </div>
      <div class="modal-footer flex-wrap gap-2">
        <button class="btn btn-edit"><i class="fas fa-save me-1"></i> {{ __('m_tours.schedule.ui.save') }}</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('m_tours.schedule.ui.cancel') }}</button>
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
        <h5 class="modal-title">{{ __('m_tours.schedule.ui.edit_schedule') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
        <div class="row g-2 mt-2">
          <div class="col-6">
            <label class="form-label">{{ __('m_tours.schedule.fields.max_capacity') }}</label>
            <input type="number" id="edit-capacity" name="max_capacity" class="form-control" min="1" required>
          </div>
          <div class="col-6">
            <label class="form-label">{{ __('m_tours.schedule.fields.label') }}</label>
            <input type="text" id="edit-label" name="label" class="form-control" maxlength="255">
          </div>
        </div>

        <input type="hidden" name="is_active" value="0">
        <div class="form-check mt-2">
          <input class="form-check-input" type="checkbox" id="edit-active" name="is_active" value="1">
          <label class="form-check-label" for="edit-active">{{ __('m_tours.schedule.fields.active') }}</label>
        </div>
      </div>
      <div class="modal-footer flex-wrap gap-2">
        <button class="btn btn-edit" type="submit"><i class="fas fa-save me-1"></i> {{ __('m_tours.schedule.ui.save_changes') }}</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('m_tours.schedule.ui.cancel') }}</button>
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

// ===== Spinner + lock (solo botones; NO deshabilita inputs) =====
function lockAndSubmit(form, opts = {}) {
  const loadingText = opts.loadingText || @json(__('m_tours.schedule.ui.processing'));

  if (!form.checkValidity()) {
    if (typeof form.reportValidity === 'function') form.reportValidity();
    return;
  }

  const buttons = form.querySelectorAll('button');
  let submitBtn =
    form.querySelector('button[type="submit"]') ||
    form.querySelector('.btn-edit, .btn-primary, .btn-success');

  buttons.forEach(btn => {
    if (submitBtn && btn === submitBtn) return;
    btn.disabled = true;
  });

  if (submitBtn) {
    if (!submitBtn.dataset.originalHtml) submitBtn.dataset.originalHtml = submitBtn.innerHTML;
    submitBtn.innerHTML =
      '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>' +
      loadingText;
    submitBtn.classList.add('disabled');
    submitBtn.disabled = true;
  }

  form.querySelectorAll('input, select, textarea').forEach(el => { if (el.disabled) el.disabled = false; });
  form.submit();
}

// ===== Rellenar modal de edición =====
document.getElementById('modalEditarHorarioGeneral')?.addEventListener('show.bs.modal', function (ev) {
  const btn = ev.relatedTarget; if (!btn) return;

  const id     = btn.getAttribute('data-id');
  const start  = timeToHHMM(btn.getAttribute('data-start') || '');
  const end    = timeToHHMM(btn.getAttribute('data-end')   || '');
  const label  = btn.getAttribute('data-label') || '';
  const cap    = btn.getAttribute('data-capacity') || '';
  const active = btn.getAttribute('data-active') === '1';

  const form = document.getElementById('formEditarHorarioGeneral');
  const pattern = document.querySelector('meta[name="schedule-update-url"]')?.content;
  if (pattern) form.action = pattern.replace('___ID___', id);

  document.getElementById('edit-start').value    = start;
  document.getElementById('edit-end').value      = end;
  document.getElementById('edit-label').value    = label;
  document.getElementById('edit-capacity').value = cap;
  document.getElementById('edit-active').checked = active;
});

// ===== Confirmaciones con SweetAlert + lock/spinner =====

// Toggle GLOBAL
document.querySelectorAll('.form-toggle-global').forEach(form => {
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    const label = form.getAttribute('data-label') || @json(__('m_tours.schedule.ui.this_schedule'));
    const isActive = form.getAttribute('data-active') === '1';
    Swal.fire({
      title: isActive ? @json(__('m_tours.schedule.ui.toggle_global_off_title'))
                      : @json(__('m_tours.schedule.ui.toggle_global_on_title')),
      html: isActive
        ? @json(__('m_tours.schedule.ui.toggle_global_off_html', ['label' => ':label'])).replace(':label', label)
        : @json(__('m_tours.schedule.ui.toggle_global_on_html',  ['label' => ':label'])).replace(':label', label),
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#f39c12',
      cancelButtonColor: '#6c757d',
      confirmButtonText: @json(__('m_tours.schedule.ui.yes_continue'))
    }).then(r => { if (r.isConfirmed) lockAndSubmit(form, {loadingText: @json(__('m_tours.schedule.ui.applying'))}); });
  });
});

// Toggle ASIGNACIÓN (pivote por tour)
document.querySelectorAll('.form-assignment-toggle').forEach(form => {
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    const tour = form.getAttribute('data-tour') || @json(__('m_tours.schedule.ui.this_tour'));
    const isActive = form.getAttribute('data-active') === '1';
    Swal.fire({
      title: isActive ? @json(__('m_tours.schedule.ui.toggle_assign_off_title'))
                      : @json(__('m_tours.schedule.ui.toggle_assign_on_title')),
      html: isActive
        ? @json(__('m_tours.schedule.ui.toggle_assign_off_html', ['tour' => ':tour'])).replace(':tour', tour)
        : @json(__('m_tours.schedule.ui.toggle_assign_on_html',  ['tour' => ':tour'])).replace(':tour', tour),
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#f39c12',
      cancelButtonColor: '#6c757d',
      confirmButtonText: @json(__('m_tours.schedule.ui.yes_continue'))
    }).then(r => { if (r.isConfirmed) lockAndSubmit(form, {loadingText: @json(__('m_tours.schedule.ui.applying'))}); });
  });
});

// Eliminar GLOBAL
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
    }).then(r => { if (r.isConfirmed) lockAndSubmit(form, {loadingText: @json(__('m_tours.schedule.ui.deleting'))}); });
  });
});

// Quitar del tour (DETACH)
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
    }).then(r => { if (r.isConfirmed) lockAndSubmit(form, {loadingText: @json(__('m_tours.schedule.ui.removing'))}); });
  });
});

// ===== Envío modal: validación + spinner =====
document.getElementById('formEditarHorarioGeneral')?.addEventListener('submit', function(e){
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
  lockAndSubmit(this, {loadingText: @json(__('m_tours.schedule.ui.saving_changes'))});
});

// ===== Flash messages =====
@if (session('success'))
  Swal.fire({ icon: 'success', title: @json(__('m_tours.common.success_title')), text: @json(session('success')), timer: 2000, showConfirmButton: false });
@endif
@if (session('error'))
  Swal.fire({ icon: 'error', title: @json(__('m_tours.common.error_title')), text: @json(session('error')), timer: 2600, showConfirmButton: false });
@endif
@if ($errors->any())
  Swal.fire({
    icon: 'error',
    title: @json(__('m_tours.schedule.ui.could_not_save')),
    html: `<ul style="text-align:left;margin:0;padding-left:18px;">{!! collect($errors->all())->map(fn($e)=>"<li>".e($e)."</li>")->implode('') !!}</ul>`,
    confirmButtonColor: '#dc3545'
  });
@endif
</script>
@stop
