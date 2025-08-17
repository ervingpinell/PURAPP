@extends('adminlte::page')

@section('title', 'Idiomas de Tours')

@section('content_header')
    <h1>Gestión de Idiomas</h1>
@stop

@section('content')
<div class="p-3 table-responsive">
    <a href="#" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalRegistrar">
        <i class="fas fa-plus"></i> Añadir Idioma
    </a>

    <table class="table table-bordered table-striped table-hover align-middle">
        <thead class="bg-primary text-white">
            <tr>
                <th>ID</th>
                <th>Idioma</th>
                <th class="text-center">Estado</th>
                <th class="text-center" style="width:220px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($languages as $language)
            <tr>
                <td>{{ $language->tour_language_id }}</td>
                <td>{{ $language->name }}</td>
                <td class="text-center">
                    @if ($language->is_active)
                        <span class="badge bg-success">Activo</span>
                    @else
                        <span class="badge bg-secondary">Inactivo</span>
                    @endif
                </td>
                <td class="text-center">
                    {{-- Editar --}}
                    <a href="#" class="btn btn-edit btn-sm"
                       data-bs-toggle="modal"
                       data-bs-target="#modalEditar{{ $language->tour_language_id }}">
                        <i class="fas fa-edit"></i>
                    </a>

                    {{-- Alternar activar/desactivar (PATCH) --}}
                    <form action="{{ route('admin.languages.toggle', $language->tour_language_id) }}"
                          method="POST"
                          class="d-inline form-toggle-language"
                          data-name="{{ $language->name }}"
                          data-active="{{ $language->is_active ? 1 : 0 }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-sm btn-toggle"
                                title="{{ $language->is_active ? 'Desactivar' : 'Activar' }}">
                            <i class="fas {{ $language->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                        </button>
                    </form>

                    {{-- Eliminar (opcional) --}}
                    {{--
                    <form action="{{ route('admin.languages.destroy', $language->tour_language_id) }}"
                          method="POST"
                          class="d-inline form-delete-language"
                          data-name="{{ $language->name }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                    --}}
                </td>
            </tr>

            {{-- Modal Editar --}}
            <div class="modal fade" id="modalEditar{{ $language->tour_language_id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <form action="{{ route('admin.languages.update', $language->tour_language_id) }}"
                          method="POST"
                          class="form-edit-language">
                        @csrf
                        @method('PUT')
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Editar Idioma</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Idioma</label>
                                    <input type="text" name="name" class="form-control" value="{{ $language->name }}" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save me-1"></i> Actualizar
                                </button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Modal Registrar --}}
<div class="modal fade" id="modalRegistrar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.languages.store') }}" method="POST" class="form-create-language">
            @csrf
            <input type="hidden" name="_from" value="create">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Idioma</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Idioma</label>
                        <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Guardar
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// ===== Utilidad: spinner + lock (solo botones; NO deshabilita inputs) =====
function lockAndSubmit(form, loadingText = 'Procesando...') {
  if (!form.checkValidity()) {
    if (typeof form.reportValidity === 'function') form.reportValidity();
    return;
  }
  const buttons = form.querySelectorAll('button');
  let submitBtn = form.querySelector('button[type="submit"]') || buttons[0];

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

  form.querySelectorAll('input,select,textarea').forEach(el => { if (el.disabled) el.disabled = false; });

  form.submit();
}

// ===== Alternar activar/desactivar =====
document.querySelectorAll('.form-toggle-language').forEach(form => {
  form.addEventListener('submit', function (e) {
    e.preventDefault();
    const name = form.getAttribute('data-name') || 'este idioma';
    const isActive = form.getAttribute('data-active') === '1';

    Swal.fire({
      title: isActive ? '¿Desactivar idioma?' : '¿Activar idioma?',
      html: isActive
        ? 'El idioma <b>'+name+'</b> quedará <b>inactivo</b>.'
        : 'El idioma <b>'+name+'</b> quedará <b>activo</b>.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sí, continuar',
      cancelButtonText: 'Cancelar',
      confirmButtonColor: isActive ? '#ffc107' : '#28a745',
      cancelButtonColor: '#6c757d'
    }).then(res => {
      if (res.isConfirmed) {
        lockAndSubmit(form, isActive ? 'Desactivando...' : 'Activando...');
      }
    });
  });
});

// ===== Editar (confirmación + spinner) =====
document.querySelectorAll('.form-edit-language').forEach(form => {
  form.addEventListener('submit', function (e) {
    e.preventDefault();
    Swal.fire({
      title: '¿Guardar cambios?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Sí, guardar',
      cancelButtonText: 'Cancelar',
      confirmButtonColor: '#0d6efd',
      cancelButtonColor: '#6c757d'
    }).then(res => {
      if (res.isConfirmed) {
        lockAndSubmit(form, 'Guardando...');
      }
    });
  });
});

// ===== (Opcional) Eliminar — listo pero comentado =====
/*
document.querySelectorAll('.form-delete-language').forEach(form => {
  form.addEventListener('submit', function (e) {
    e.preventDefault();
    const name = form.getAttribute('data-name') || 'este idioma';
    Swal.fire({
      title: '¿Eliminar definitivamente?',
      html: 'Se eliminará <b>'+name+'</b> y no podrás deshacerlo.',
      icon: 'error',
      showCancelButton: true,
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar',
      confirmButtonColor: '#dc3545',
      cancelButtonColor: '#6c757d'
    }).then(res => {
      if (res.isConfirmed) {
        lockAndSubmit(form, 'Eliminando...');
      }
    });
  });
});
*/

// ===== Flash messages =====
@if(session('success') && session('alert_type'))
(function(){
  let icon = 'success';
  let title = 'Éxito';
  let color = '#3085d6';
  switch ("{{ session('alert_type') }}") {
    case 'activado':     icon='success'; title='Idioma Activado';     color='#28a745'; break;
    case 'desactivado':  icon='warning'; title='Idioma Desactivado';  color='#ffc107'; break;
    case 'actualizado':  icon='info';    title='Idioma Actualizado';  color='#17a2b8'; break;
    case 'creado':       icon='success'; title='Idioma Registrado';   color='#007bff'; break;
  }
  Swal.fire({ icon, title, text: @json(session('success')), confirmButtonColor: color });
})();
@endif

// ===== Errores de validación (crear o editar) =====
@if ($errors->any())
Swal.fire({
  icon: 'error',
  title: 'No se pudo guardar',
  html: `<ul style="text-align:left;margin:0;padding-left:18px;">{!! collect($errors->all())->map(fn($e)=>"<li>".e($e)."</li>")->implode('') !!}</ul>`,
  confirmButtonColor: '#d33'
}).then(() => {
  // Si el error fue al crear, abrimos el modal Registrar otra vez
  @if (old('_from') === 'create')
    const el = document.getElementById('modalRegistrar');
    if (el && typeof bootstrap !== 'undefined') new bootstrap.Modal(el).show();
  @endif
});
@endif
</script>
@stop
