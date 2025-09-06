@extends('adminlte::page')

@section('title', 'Meeting Points')

@section('content_header')
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h1 class="m-0">Meeting Points</h1>
        <span class="badge bg-primary fs-6 px-3 py-2">
            <i class="fas fa-map-marker-alt me-1"></i> {{ $points->count() }} registros
        </span>
    </div>
@stop

@section('content')

    {{-- ===== Alerts / errores ===== --}}
    <div id="alerts" class="mb-3">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-times-circle me-1"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="fw-bold mb-1">
                    <i class="fas fa-ban me-1"></i> Hay errores en el formulario:
                </div>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li class="small">{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif
    </div>

    {{-- ===== Formulario (arriba) ===== --}}
    <div class="card shadow-sm mb-3">
        <div class="card-header d-flex align-items-center justify-content-between">
            <span class="fw-semibold"><i class="fas fa-plus me-2"></i>Añadir punto</span>
            <form action="{{ route('admin.meetingpoints.index') }}" method="GET">
                <button type="submit" class="btn btn-sm btn-outline-secondary" title="Recargar">
                    <i class="fas fa-undo"></i>
                </button>
            </form>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.meetingpoints.store') }}" method="POST" autocomplete="off" novalidate>
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="Oficina de Green Vacations"
                               value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @else
                            <div class="form-text">Ej: “Parque Central de La Fortuna”.</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Hora de recogida</label>
                        <input type="text" name="pickup_time"
                               class="form-control @error('pickup_time') is-invalid @enderror"
                               placeholder="7:10 AM" value="{{ old('pickup_time') }}">
                        @error('pickup_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-3">
                        @php
                            $suggestedOrder = (optional($points)->max('sort_order') ?? 0) + 1;
                        @endphp
                        <label class="form-label">Orden</label>
                        <input type="number" name="sort_order"
                               class="form-control @error('sort_order') is-invalid @enderror"
                               min="0" step="1" value="{{ old('sort_order', $suggestedOrder) }}">
                        @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Dirección</label>
                        <input type="text" name="address"
                               class="form-control @error('address') is-invalid @enderror"
                               placeholder="Centro de La Fortuna" value="{{ old('address') }}">
                        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">URL de mapa</label>
                        <input type="url" name="map_url"
                               class="form-control @error('map_url') is-invalid @enderror"
                               placeholder="https://maps.google.com/..." value="{{ old('map_url') }}">
                        @error('map_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <div class="form-check">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" id="mp_active_new"
                                   name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                            <label class="form-check-label" for="mp_active_new">Activo</label>
                        </div>
                    </div>

                    <div class="col-12">
                        <button class="btn btn-success">
                            <i class="fas fa-save me-1"></i> Guardar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== Listado (abajo) ===== --}}
    <div class="card shadow-sm">
        <div class="card-header d-flex flex-wrap gap-2 align-items-center justify-content-between">
            <span class="fw-semibold"><i class="fas fa-list me-2"></i>Listado</span>
            <div class="d-flex gap-2 align-items-center">
                <form action="{{ route('admin.meetingpoints.index') }}" method="GET" class="me-2 d-none d-md-block">
                    <button type="submit" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-sync-alt me-1"></i> Recargar
                    </button>
                </form>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" id="tableFilter" class="form-control"
                           placeholder="Buscar por nombre o dirección…">
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped table-bordered align-middle mb-0" id="meetingPointsTable">
                    <thead class="bg-light position-sticky top-0">
                        <tr class="text-center small text-muted">
                            <th style="width:60px">#</th>
                            <th>Nombre</th>
                            <th style="width:140px">Hora</th>
                            <th>Dirección</th>
                            <th style="width:90px">Mapa</th>
                            <th style="width:110px">Orden</th>
                            <th style="width:110px">Activo</th>
                            <th style="width:240px">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($points as $i => $p)
                            <tr data-row-text="{{ strtolower(trim(($p->name ?? '').' '.($p->address ?? ''))) }}">
                                <td class="text-center text-muted">{{ $i+1 }}</td>

                                {{-- Nombre --}}
                                <td>
                                    <form action="{{ route('admin.meetingpoints.update', $p->id) }}" method="POST" class="d-flex gap-2">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="pickup_time" value="{{ $p->pickup_time }}">
                                        <input type="hidden" name="address" value="{{ $p->address }}">
                                        <input type="hidden" name="map_url" value="{{ $p->map_url }}">
                                        <input type="hidden" name="sort_order" value="{{ $p->sort_order }}">
                                        <input type="text" name="name" class="form-control form-control-sm"
                                               value="{{ $p->name }}" required placeholder="Nombre…">
                                        <button class="btn btn-sm btn-outline-primary" title="Guardar">
                                            <i class="fas fa-save"></i>
                                        </button>
                                    </form>
                                </td>

                                {{-- Hora --}}
                                <td>
                                    <form action="{{ route('admin.meetingpoints.update', $p->id) }}" method="POST" class="d-flex gap-2">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="name" value="{{ $p->name }}">
                                        <input type="hidden" name="address" value="{{ $p->address }}">
                                        <input type="hidden" name="map_url" value="{{ $p->map_url }}">
                                        <input type="hidden" name="sort_order" value="{{ $p->sort_order }}">
                                        <input type="text" name="pickup_time" class="form-control form-control-sm text-center"
                                               value="{{ $p->pickup_time }}" placeholder="7:30 AM">
                                        <button class="btn btn-sm btn-outline-primary" title="Guardar">
                                            <i class="fas fa-save"></i>
                                        </button>
                                    </form>
                                </td>

                                {{-- Dirección --}}
                                <td>
                                    <form action="{{ route('admin.meetingpoints.update', $p->id) }}" method="POST" class="d-flex gap-2">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="name" value="{{ $p->name }}">
                                        <input type="hidden" name="pickup_time" value="{{ $p->pickup_time }}">
                                        <input type="hidden" name="map_url" value="{{ $p->map_url }}">
                                        <input type="hidden" name="sort_order" value="{{ $p->sort_order }}">
                                        <input type="text" name="address" class="form-control form-control-sm"
                                               value="{{ $p->address }}" placeholder="Dirección…">
                                        <button class="btn btn-sm btn-outline-primary" title="Guardar">
                                            <i class="fas fa-save"></i>
                                        </button>
                                    </form>
                                </td>

                                {{-- Mapa --}}
                                <td class="text-center">
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        @if ($p->map_url)
                                            <a href="{{ $p->map_url }}" target="_blank" class="btn btn-sm btn-outline-info" title="Abrir mapa">
                                                <i class="fas fa-map-marked-alt"></i>
                                            </a>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse"
                                                data-bs-target="#mapEdit{{ $p->id }}" title="Editar URL">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>

                                    <div id="mapEdit{{ $p->id }}" class="collapse mt-2">
                                        <form action="{{ route('admin.meetingpoints.update', $p->id) }}" method="POST" class="d-flex gap-2">
                                            @csrf @method('PUT')
                                            <input type="hidden" name="name" value="{{ $p->name }}">
                                            <input type="hidden" name="pickup_time" value="{{ $p->pickup_time }}">
                                            <input type="hidden" name="address" value="{{ $p->address }}">
                                            <input type="hidden" name="sort_order" value="{{ $p->sort_order }}">
                                            <input type="url" name="map_url" class="form-control form-control-sm"
                                                   value="{{ $p->map_url }}" placeholder="https://maps.google.com/…">
                                            <button class="btn btn-sm btn-outline-primary"><i class="fas fa-save"></i></button>
                                        </form>
                                    </div>
                                </td>

                                {{-- Orden --}}
                                <td>
                                    <form action="{{ route('admin.meetingpoints.update', $p->id) }}" method="POST" class="d-flex gap-2">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="name" value="{{ $p->name }}">
                                        <input type="hidden" name="pickup_time" value="{{ $p->pickup_time }}">
                                        <input type="hidden" name="address" value="{{ $p->address }}">
                                        <input type="hidden" name="map_url" value="{{ $p->map_url }}">
                                        <input type="number" name="sort_order" class="form-control form-control-sm text-center"
                                               value="{{ $p->sort_order ?? 0 }}" min="0" step="1">
                                        <button class="btn btn-sm btn-outline-primary" title="Guardar">
                                            <i class="fas fa-save"></i>
                                        </button>
                                    </form>
                                </td>

                                {{-- Activo --}}
                                <td class="text-center">
                                    <form action="{{ route('admin.meetingpoints.toggle', $p->id) }}" method="POST"
                                          class="d-inline toggle-form" data-name="{{ $p->name }}" data-active="{{ $p->is_active ? 1 : 0 }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-sm {{ $p->is_active ? 'btn-success' : 'btn-secondary' }}"
                                                title="{{ $p->is_active ? 'Desactivar' : 'Activar' }}">
                                            <i class="fas fa-toggle-{{ $p->is_active ? 'on' : 'off' }}"></i>
                                        </button>
                                    </form>
                                </td>

                                {{-- Acciones --}}
                                <td class="text-center">
                                    <div class="d-inline-flex gap-2">
                                        <form action="{{ route('admin.meetingpoints.update', $p->id) }}" method="POST" class="d-none d-md-inline">
                                            @csrf @method('PUT')
                                            <input type="hidden" name="name" value="{{ $p->name }}">
                                            <input type="hidden" name="pickup_time" value="{{ $p->pickup_time }}">
                                            <input type="hidden" name="address" value="{{ $p->address }}">
                                            <input type="hidden" name="map_url" value="{{ $p->map_url }}">
                                            <input type="hidden" name="sort_order" value="{{ $p->sort_order }}">
                                            <button class="btn btn-sm btn-outline-primary" title="Guardar todo">
                                                <i class="fas fa-save"></i>
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.meetingpoints.destroy', $p->id) }}" method="POST"
                                              class="delete-form" data-name="{{ $p->name }}">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    <i class="fas fa-info-circle me-1"></i> No hay registros. Crea el primero arriba.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer small text-muted">
            Consejo: Puedes editar en línea y presionar <kbd>Enter</kbd> o el botón <strong>Guardar</strong> <i class="fas fa-save"></i>.
        </div>
    </div>
@endsection

@push('css')
<style>
.table thead th { font-weight: 600; }
.table td, .table th { vertical-align: middle; }
.table thead.position-sticky { z-index: 2; }
tr.filtered-out { display: none !important; }
</style>
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Filtro por texto
const filterInput = document.getElementById('tableFilter');
if (filterInput) {
  filterInput.addEventListener('input', function(){
    const q = (this.value || '').trim().toLowerCase();
    document.querySelectorAll('#meetingPointsTable tbody tr').forEach(tr => {
      const haystack = tr.getAttribute('data-row-text') || '';
      tr.classList.toggle('filtered-out', q && !haystack.includes(q));
    });
  });
}

// Confirmar activar/desactivar
document.querySelectorAll('.toggle-form').forEach(form => {
  form.addEventListener('submit', function(e){
    e.preventDefault();
    const name = form.getAttribute('data-name') || '';
    const isActive = form.getAttribute('data-active') === '1';
    Swal.fire({
      title: isActive ? `Desactivar “${name}”` : `Activar “${name}”`,
      text:  isActive ? '¿Seguro que deseas desactivarlo?' : '¿Seguro que deseas activarlo?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sí',
      cancelButtonText: 'Cancelar'
    }).then(r=>{ if(r.isConfirmed) form.submit(); });
  });
});

// Confirmar eliminar
document.querySelectorAll('.delete-form').forEach(form => {
  form.addEventListener('submit', function(e){
    e.preventDefault();
    const name = form.getAttribute('data-name') || '';
    Swal.fire({
      title: `Eliminar “${name}”`,
      text: 'Esta acción no se puede deshacer.',
      icon: 'error',
      showCancelButton: true,
      confirmButtonText: 'Eliminar',
      cancelButtonText: 'Cancelar'
    }).then(r=>{ if(r.isConfirmed) form.submit(); });
  });
});

// Flash
@if (session('success'))
  Swal.fire({icon:'success', title:'OK', text:@json(session('success')), timer:2200, showConfirmButton:false});
@endif
@if (session('error'))
  Swal.fire({icon:'error', title:'Error', text:@json(session('error')), timer:2500, showConfirmButton:false});
@endif

// Enfocar primer campo con error
@if ($errors->any())
  document.getElementById('alerts')?.scrollIntoView({behavior:'smooth', block:'start'});
  const firstInvalid = document.querySelector('.is-invalid');
  if (firstInvalid) firstInvalid.focus();
@endif
</script>
@endpush
