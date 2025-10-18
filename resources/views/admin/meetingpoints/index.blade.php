{{-- resources/views/admin/meetingpoints/index.blade.php --}}
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
  {{-- ===== Formulario (crear) ===== --}}
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
      <form action="{{ route('admin.meetingpoints.store') }}" method="POST" autocomplete="off" novalidate class="create-form">
        @csrf
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Nombre <span class="text-danger">*</span></label>
            <input type="text" name="name"
                   class="form-control @error('name') is-invalid @enderror"
                   placeholder="Parque Central de La Fortuna"
                   value="{{ old('name') }}" required>
            @error('name')
              <div class="invalid-feedback">{{ $message }}</div>
            @else
              <div class="form-text">Ej: "Parque Central de La Fortuna".</div>
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
            @php $suggestedOrder = (optional($points)->max('sort_order') ?? 0) + 1; @endphp
            <label class="form-label">Orden</label>
            <input type="number" name="sort_order"
                   class="form-control @error('sort_order') is-invalid @enderror"
                   min="0" step="1" value="{{ old('sort_order', $suggestedOrder) }}">
            @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="col-md-6">
            <label class="form-label">Descripción</label>
            <input type="text" name="description"
                   class="form-control @error('description') is-invalid @enderror"
                   placeholder="Centro de La Fortuna" value="{{ old('description') }}">
            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
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

          <div class="col-12 d-flex gap-2">
            <button class="btn btn-success">
              <i class="fas fa-save me-1"></i> Guardar
            </button>
            <button type="reset" class="btn btn-outline-secondary">
              <i class="fas fa-eraser me-1"></i> Limpiar
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  {{-- ===== Listado Desktop (Tabla) ===== --}}
  <div class="card shadow-sm d-none d-lg-block">
    <div class="card-header d-flex flex-wrap gap-2 align-items-center justify-content-between">
      <span class="fw-semibold"><i class="fas fa-list me-2"></i>Listado</span>
      <div class="d-flex gap-2 align-items-center">
        <form action="{{ route('admin.meetingpoints.index') }}" method="GET">
          <button type="submit" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-sync-alt me-1"></i> Recargar
          </button>
        </form>
        <div class="input-group input-group-sm">
          <span class="input-group-text"><i class="fas fa-search"></i></span>
          <input type="text" id="tableFilter" class="form-control"
                 placeholder="Buscar…">
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
              <th style="width:120px">Hora</th>
              <th>Descripción</th>
              <th style="width:80px">Mapa</th>
              <th style="width:90px">Orden</th>
              <th style="width:90px">Estado</th>
              <th style="width:140px">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($points as $i => $p)
              <tr data-row-text="{{ strtolower(trim(($p->name ?? '').' '.($p->description ?? ''))) }}">
                <td class="text-center text-muted">{{ $i+1 }}</td>
                <td><strong>{{ $p->name }}</strong></td>
                <td class="text-center">{{ $p->pickup_time ?: '—' }}</td>
                <td>{{ $p->description ?: '—' }}</td>
                <td class="text-center">
                  @if ($p->map_url)
                    <a href="{{ $p->map_url }}" target="_blank" class="btn btn-sm btn-outline-info" title="Ver mapa">
                      <i class="fas fa-map-marked-alt"></i>
                    </a>
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>
                <td class="text-center">{{ $p->sort_order ?? 0 }}</td>
                <td class="text-center">
                  <span class="badge {{ $p->is_active ? 'bg-success' : 'bg-secondary' }}">
                    {{ $p->is_active ? 'Activo' : 'Inactivo' }}
                  </span>
                </td>
                <td class="text-center">
                  <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary edit-btn" 
                            data-id="{{ $p->id }}"
                            data-name="{{ $p->name }}"
                            data-pickup-time="{{ $p->pickup_time }}"
                            data-description="{{ $p->description }}"
                            data-map-url="{{ $p->map_url }}"
                            data-sort-order="{{ $p->sort_order }}"
                            data-is-active="{{ $p->is_active }}"
                            title="Editar">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-outline-{{ $p->is_active ? 'warning' : 'success' }} toggle-btn"
                            data-id="{{ $p->id }}"
                            data-name="{{ $p->name }}"
                            data-active="{{ $p->is_active ? 1 : 0 }}"
                            title="{{ $p->is_active ? 'Desactivar' : 'Activar' }}">
                      <i class="fas fa-power-off"></i>
                    </button>
                    <button type="button" class="btn btn-outline-danger delete-btn"
                            data-id="{{ $p->id }}"
                            data-name="{{ $p->name }}"
                            title="Eliminar">
                      <i class="fas fa-trash"></i>
                    </button>
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
  </div>

  {{-- ===== Listado Mobile (Cards) ===== --}}
  <div class="d-lg-none">
    <div class="card shadow-sm mb-3">
      <div class="card-header">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <span class="fw-semibold"><i class="fas fa-list me-2"></i>Listado</span>
          <form action="{{ route('admin.meetingpoints.index') }}" method="GET">
            <button type="submit" class="btn btn-sm btn-outline-secondary">
              <i class="fas fa-sync-alt"></i>
            </button>
          </form>
        </div>
        <div class="input-group input-group-sm">
          <span class="input-group-text"><i class="fas fa-search"></i></span>
          <input type="text" id="mobileFilter" class="form-control" placeholder="Buscar…">
        </div>
      </div>
    </div>

    <div id="mobilePointsList">
      @forelse ($points as $p)
        <div class="card shadow-sm mb-3 mobile-point-card" 
             data-row-text="{{ strtolower(trim(($p->name ?? '').' '.($p->description ?? ''))) }}">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <div class="flex-grow-1">
                <h5 class="mb-1">
                  <i class="fas fa-map-marker-alt text-primary me-1"></i>
                  {{ $p->name }}
                </h5>
                <div class="text-muted small mb-2">
                  @if($p->description)
                    <i class="fas fa-info-circle me-1"></i>{{ $p->description }}
                  @endif
                </div>
              </div>
              <span class="badge {{ $p->is_active ? 'bg-success' : 'bg-secondary' }}">
                {{ $p->is_active ? 'Activo' : 'Inactivo' }}
              </span>
            </div>

            <div class="row g-2 mb-3">
              @if($p->pickup_time)
                <div class="col-6">
                  <small class="text-muted d-block">Hora</small>
                  <strong><i class="fas fa-clock me-1"></i>{{ $p->pickup_time }}</strong>
                </div>
              @endif
              <div class="col-6">
                <small class="text-muted d-block">Orden</small>
                <strong><i class="fas fa-sort me-1"></i>{{ $p->sort_order ?? 0 }}</strong>
              </div>
              @if($p->map_url)
                <div class="col-12">
                  <a href="{{ $p->map_url }}" target="_blank" class="btn btn-sm btn-outline-info w-100">
                    <i class="fas fa-map-marked-alt me-1"></i> Ver en mapa
                  </a>
                </div>
              @endif
            </div>

            <div class="d-flex gap-2">
              <button class="btn btn-sm btn-primary flex-grow-1 edit-btn-mobile" 
                      data-id="{{ $p->id }}"
                      data-name="{{ $p->name }}"
                      data-pickup-time="{{ $p->pickup_time }}"
                      data-description="{{ $p->description }}"
                      data-map-url="{{ $p->map_url }}"
                      data-sort-order="{{ $p->sort_order }}"
                      data-is-active="{{ $p->is_active }}">
                <i class="fas fa-edit me-1"></i> Editar
              </button>
              <button type="button" class="btn btn-sm btn-{{ $p->is_active ? 'warning' : 'success' }} toggle-btn-mobile"
                      data-id="{{ $p->id }}"
                      data-name="{{ $p->name }}"
                      data-active="{{ $p->is_active ? 1 : 0 }}">
                <i class="fas fa-power-off"></i>
              </button>
              <button type="button" class="btn btn-sm btn-danger delete-btn-mobile"
                      data-id="{{ $p->id }}"
                      data-name="{{ $p->name }}">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </div>
        </div>
      @empty
        <div class="card shadow-sm">
          <div class="card-body text-center text-muted py-5">
            <i class="fas fa-info-circle me-1"></i> No hay registros. Crea el primero arriba.
          </div>
        </div>
      @endforelse
    </div>
  </div>

  {{-- ===== Modal de Edición ===== --}}
  <div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Editar Meeting Point</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form id="editForm" method="POST">
          @csrf @method('PUT')
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Nombre <span class="text-danger">*</span></label>
              <input type="text" name="name" id="edit_name" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Hora de recogida</label>
              <input type="text" name="pickup_time" id="edit_pickup_time" class="form-control" placeholder="7:10 AM">
            </div>
            <div class="mb-3">
              <label class="form-label">Descripción</label>
              <input type="text" name="description" id="edit_description" class="form-control">
            </div>
            <div class="mb-3">
              <label class="form-label">URL de mapa</label>
              <input type="url" name="map_url" id="edit_map_url" class="form-control" placeholder="https://maps.google.com/...">
            </div>
            <div class="mb-3">
              <label class="form-label">Orden</label>
              <input type="number" name="sort_order" id="edit_sort_order" class="form-control" min="0" step="1">
            </div>
            <div class="form-check">
              <input type="hidden" name="is_active" value="0">
              <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active" value="1">
              <label class="form-check-label" for="edit_is_active">Activo</label>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              <i class="fas fa-times me-1"></i> Cancelar
            </button>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save me-1"></i> Guardar cambios
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  {{-- Formularios ocultos para toggle y delete --}}
  <form id="toggleForm" method="POST" style="display:none;">
    @csrf @method('PATCH')
  </form>

  <form id="deleteForm" method="POST" style="display:none;">
    @csrf @method('DELETE')
  </form>
@endsection

@push('css')
<style>
.table thead th { font-weight: 600; }
.table td, .table th { vertical-align: middle; }
.table thead.position-sticky { z-index: 2; }
tr.filtered-out, .mobile-point-card.filtered-out { 
  display: none !important; 
}
.mobile-point-card {
  transition: transform 0.2s, box-shadow 0.2s;
}
.mobile-point-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
</style>
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
/** Helpers SweetAlert */
const showSuccess = (title, text = '') => {
  Swal.fire({
    icon: 'success',
    title: title,
    text: text,
    showConfirmButton: false,
    timer: 2500,
    timerProgressBar: true,
    position: 'center',
    backdrop: true
  });
};

const showError = (title, text = '') => {
  Swal.fire({
    icon: 'error',
    title: title,
    text: text,
    confirmButtonText: 'Entendido',
    confirmButtonColor: '#3085d6'
  });
};

const confirmAction = (title, text, icon = 'warning') => {
  return Swal.fire({
    title: title,
    text: text,
    icon: icon,
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Sí, continuar',
    cancelButtonText: 'Cancelar'
  });
};

/** Buscar/filtrar tabla (Desktop) */
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

/** Buscar/filtrar cards (Mobile) */
const mobileFilterInput = document.getElementById('mobileFilter');
if (mobileFilterInput) {
  mobileFilterInput.addEventListener('input', function(){
    const q = (this.value || '').trim().toLowerCase();
    document.querySelectorAll('.mobile-point-card').forEach(card => {
      const haystack = card.getAttribute('data-row-text') || '';
      card.classList.toggle('filtered-out', q && !haystack.includes(q));
    });
  });
}

/** Modal de Edición */
let editModal;
const editModalElement = document.getElementById('editModal');
const editForm = document.getElementById('editForm');

// Inicializar modal cuando el DOM esté listo
if (editModalElement) {
  editModal = new bootstrap.Modal(editModalElement, {
    backdrop: true,
    keyboard: true,
    focus: true
  });
  
  // Asegurar que los botones de cerrar funcionen
  editModalElement.querySelectorAll('[data-bs-dismiss="modal"]').forEach(btn => {
    btn.addEventListener('click', function() {
      editModal.hide();
    });
  });
}

// Desktop edit buttons
document.querySelectorAll('.edit-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    const id = this.dataset.id;
    const route = "{{ route('admin.meetingpoints.update', ':id') }}".replace(':id', id);
    
    editForm.action = route;
    document.getElementById('edit_name').value = this.dataset.name || '';
    document.getElementById('edit_pickup_time').value = this.dataset.pickupTime || '';
    document.getElementById('edit_description').value = this.dataset.description || '';
    document.getElementById('edit_map_url').value = this.dataset.mapUrl || '';
    document.getElementById('edit_sort_order').value = this.dataset.sortOrder || 0;
    document.getElementById('edit_is_active').checked = this.dataset.isActive === '1';
    
    editModal.show();
  });
});

// Mobile edit buttons
document.querySelectorAll('.edit-btn-mobile').forEach(btn => {
  btn.addEventListener('click', function() {
    const id = this.dataset.id;
    const route = "{{ route('admin.meetingpoints.update', ':id') }}".replace(':id', id);
    
    editForm.action = route;
    document.getElementById('edit_name').value = this.dataset.name || '';
    document.getElementById('edit_pickup_time').value = this.dataset.pickupTime || '';
    document.getElementById('edit_description').value = this.dataset.description || '';
    document.getElementById('edit_map_url').value = this.dataset.mapUrl || '';
    document.getElementById('edit_sort_order').value = this.dataset.sortOrder || 0;
    document.getElementById('edit_is_active').checked = this.dataset.isActive === '1';
    
    editModal.show();
  });
});

// Submit edit form
editForm.addEventListener('submit', function(e) {
  e.preventDefault();
  const name = document.getElementById('edit_name').value.trim();
  
  confirmAction(
    '¿Guardar cambios?',
    `Se actualizará el meeting point "${name}"`,
    'question'
  ).then(result => {
    if (result.isConfirmed) {
      this.submit();
    }
  });
});

/** Confirmar crear */
document.querySelector('.create-form')?.addEventListener('submit', function(e){
  e.preventDefault();
  const name = (this.querySelector('input[name="name"]')?.value || '').trim();
  
  confirmAction(
    '¿Crear nuevo meeting point?',
    name ? `Se creará "${name}"` : 'Se creará un nuevo punto de encuentro',
    'question'
  ).then(result => {
    if (result.isConfirmed) {
      this.submit();
    }
  });
});

/** Toggle activar/desactivar (Desktop) */
document.querySelectorAll('.toggle-btn').forEach(btn => {
  btn.addEventListener('click', function(e){
    e.preventDefault();
    const id = this.dataset.id;
    const name = this.dataset.name || '';
    const isActive = this.dataset.active === '1';
    const route = "{{ route('admin.meetingpoints.toggle', ':id') }}".replace(':id', id);
    
    confirmAction(
      isActive ? '¿Desactivar meeting point?' : '¿Activar meeting point?',
      isActive ? `"${name}" quedará inactivo` : `"${name}" quedará activo`,
      'warning'
    ).then(result => {
      if (result.isConfirmed) {
        const form = document.getElementById('toggleForm');
        form.action = route;
        form.submit();
      }
    });
  });
});

/** Toggle activar/desactivar (Mobile) */
document.querySelectorAll('.toggle-btn-mobile').forEach(btn => {
  btn.addEventListener('click', function(e){
    e.preventDefault();
    const id = this.dataset.id;
    const name = this.dataset.name || '';
    const isActive = this.dataset.active === '1';
    const route = "{{ route('admin.meetingpoints.toggle', ':id') }}".replace(':id', id);
    
    confirmAction(
      isActive ? '¿Desactivar?' : '¿Activar?',
      isActive ? `"${name}" quedará inactivo` : `"${name}" quedará activo`,
      'warning'
    ).then(result => {
      if (result.isConfirmed) {
        const form = document.getElementById('toggleForm');
        form.action = route;
        form.submit();
      }
    });
  });
});

/** Eliminar (Desktop) */
document.querySelectorAll('.delete-btn').forEach(btn => {
  btn.addEventListener('click', function(e){
    e.preventDefault();
    const id = this.dataset.id;
    const name = this.dataset.name || '';
    const route = "{{ route('admin.meetingpoints.destroy', ':id') }}".replace(':id', id);
    
    confirmAction(
      '¿Eliminar meeting point?',
      `"${name}" será eliminado permanentemente. Esta acción no se puede deshacer.`,
      'error'
    ).then(result => {
      if (result.isConfirmed) {
        const form = document.getElementById('deleteForm');
        form.action = route;
        form.submit();
      }
    });
  });
});

/** Eliminar (Mobile) */
document.querySelectorAll('.delete-btn-mobile').forEach(btn => {
  btn.addEventListener('click', function(e){
    e.preventDefault();
    const id = this.dataset.id;
    const name = this.dataset.name || '';
    const route = "{{ route('admin.meetingpoints.destroy', ':id') }}".replace(':id', id);
    
    confirmAction(
      '¿Eliminar?',
      `"${name}" será eliminado permanentemente. Esta acción no se puede deshacer.`,
      'error'
    ).then(result => {
      if (result.isConfirmed) {
        const form = document.getElementById('deleteForm');
        form.action = route;
        form.submit();
      }
    });
  });
});

/** Flash messages -> SweetAlert */
@if (session('success'))
  showSuccess('¡Éxito!', @json(session('success')));
@endif

@if (session('error'))
  showError('Error', @json(session('error')));
@endif

/** Errores de validación */
@if ($errors->any())
  Swal.fire({
    icon: 'error',
    title: 'Errores de validación',
    html: `<ul style="text-align:left;margin:0;padding-left:18px;">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>`,
    confirmButtonText: 'Entendido'
  }).then(() => {
    const firstInvalid = document.querySelector('.is-invalid');
    if (firstInvalid) {
      firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
      firstInvalid.focus();
    }
  });
@endif
</script>
@endpush