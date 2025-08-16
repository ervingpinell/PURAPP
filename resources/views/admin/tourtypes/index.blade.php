@extends('adminlte::page')

@section('title', 'Tipos de Tours')

@section('content_header')
    <h1>Tipos de Tours</h1>
@stop

@section('content')
<div class="p-3 table-responsive">
    <a href="#" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalRegistrar">
        <i class="fas fa-plus"></i> Añadir Tipo de Tour
    </a>

    <table class="table table-bordered table-striped table-hover">
        <thead class="bg-primary text-white">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Duración</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tourTypes as $tourtype)
            <tr>
                <td>{{ $tourtype->tour_type_id }}</td>
                <td>{{ $tourtype->name }}</td>
                <td>{{ $tourtype->description }}</td>
                <td>{{ $tourtype->duration }}</td>
                <td>
                    @if ($tourtype->is_active)
                        <span class="badge bg-success">Activo</span>
                    @else
                        <span class="badge bg-secondary">Inactivo</span>
                    @endif
                </td>

                <td class="text-nowrap">
                    <!-- Editar (VERDE) -->
                    <a href="#" class="btn btn-success btn-sm me-1" data-bs-toggle="modal" data-bs-target="#modalEditar{{ $tourtype->tour_type_id }}" title="Editar">
                        <i class="fas fa-edit"></i>
                    </a>

                    <!-- Activar/Desactivar (interruptor naranja) -->
                    <form action="{{ route('admin.tourtypes.toggle', $tourtype->tour_type_id) }}" method="POST" class="d-inline me-1">
                        @csrf
                        @method('PUT')
                        <button type="submit"
                                class="btn btn-warning btn-sm"
                                title="{{ $tourtype->is_active ? 'Desactivar' : 'Activar' }}"
                                onclick="return confirm('{{ $tourtype->is_active ? '¿Deseas desactivarlo?' : '¿Deseas activarlo?' }}')">
                            <i class="fas fa-toggle-{{ $tourtype->is_active ? 'on' : 'off' }}"></i>
                        </button>
                    </form>

                    <!-- Eliminar -->
                    <form action="{{ route('admin.tourtypes.destroy', $tourtype->tour_type_id) }}" method="POST"
                          class="d-inline form-delete" data-name="{{ $tourtype->name }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>

            <!-- Modal editar -->
            <div class="modal fade" id="modalEditar{{ $tourtype->tour_type_id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                  <form action="{{ route('admin.tourtypes.update', $tourtype->tour_type_id) }}" method="POST" autocomplete="off">
                        @csrf
                        @method('PUT')
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Editar Tipo de Tour</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label>Nombre</label>
                                    <input
                                        type="text"
                                        name="name"
                                        class="form-control"
                                        placeholder="Ej.: Aventura, Naturaleza, Relax"
                                        value="{{ session('edit_modal') == $tourtype->tour_type_id ? old('name', $tourtype->name) : $tourtype->name }}"
                                        required
                                    >
                                </div>
                                <div class="mb-3">
                                    <label>Descripción</label>
                                    <textarea
                                        name="description"
                                        class="form-control"
                                        rows="3"
                                        placeholder="Describe brevemente este tipo de tour (opcional)"
                                    >{{ session('edit_modal') == $tourtype->tour_type_id ? old('description', $tourtype->description) : $tourtype->description }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label>Duración</label>
                                    <input
                                        type="text"
                                        name="duration"
                                        class="form-control"
                                        list="durationOptions"
                                        placeholder="Ej.: 4 horas, 8 horas"
                                        title="Usa un formato como: 4 horas, 6 horas, 8 horas"
                                        value="{{ session('edit_modal') == $tourtype->tour_type_id ? old('duration', $tourtype->duration) : ($tourtype->duration ?: '4 horas') }}"
                                    >
                                    <datalist id="durationOptions">
                                        <option value="4 horas"></option>
                                        <option value="6 horas"></option>
                                        <option value="8 horas"></option>
                                        <option value="10 horas"></option>
                                    </datalist>
                                    <small class="text-muted">Formato sugerido: “4 horas”, “8 horas”.</small>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Actualizar</button>
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

<!-- Modal registrar -->
<div class="modal fade" id="modalRegistrar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.tourtypes.store') }}" method="POST" autocomplete="off">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Tipo de Tour</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nombre</label>
                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            placeholder="Ej.: Aventura, Naturaleza, Relax"
                            value="{{ old('name') }}"
                            required
                        >
                    </div>
                    <div class="mb-3">
                        <label>Descripción</label>
                        <textarea
                            name="description"
                            class="form-control"
                            rows="3"
                            placeholder="Describe brevemente este tipo de tour (opcional)"
                        >{{ old('description') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label>Duración</label>
                        <input
                            type="text"
                            name="duration"
                            class="form-control"
                            list="durationOptionsCreate"
                            placeholder="Ej.: 4 horas, 8 horas"
                            title="Usa un formato como: 4 horas, 6 horas, 8 horas"
                            value="{{ old('duration', '4 horas') }}"
                        >
                        <datalist id="durationOptionsCreate">
                            <option value="4 horas"></option>
                            <option value="6 horas"></option>
                            <option value="8 horas"></option>
                            <option value="10 horas"></option>
                        </datalist>
                        <small class="text-muted">Deja “4 horas” si aplica; puedes cambiarlo.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary">Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('success') && session('alert_type'))
<script>
    let icon = 'success';
    let title = 'Éxito';
    let color = '#3085d6';

    switch ("{{ session('alert_type') }}") {
        case 'activado':     icon='success'; title='Tipo de Tour Activado';     color='#fd7e14'; break; // naranja
        case 'desactivado':  icon='warning'; title='Tipo de Tour Desactivado';  color='#fd7e14'; break; // naranja
        case 'actualizado':  icon='info';    title='Tipo de Tour Actualizado';  color='#17a2b8'; break;
        case 'creado':       icon='success'; title='Tipo de Tour Creado';       color='#007bff'; break;
        case 'eliminado':    icon='success'; title='Tipo de Tour Eliminado';    color='#dc3545'; break;
    }

    Swal.fire({ icon, title, text: '{{ session('success') }}', confirmButtonColor: color, confirmButtonText: 'OK' });
</script>
@endif

<script>
  // Confirmación para eliminar
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.form-delete').forEach(form => {
      form.addEventListener('submit', function (e) {
        e.preventDefault();
        const name = this.dataset.name || 'este tipo de tour';
        Swal.fire({
          title: '¿Eliminar?',
          text: `Se eliminará "${name}". Esta acción no se puede deshacer.`,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#dc3545',
          cancelButtonColor: '#6c757d',
          confirmButtonText: 'Sí, eliminar',
          cancelButtonText: 'Cancelar'
        }).then(res => {
          if (res.isConfirmed) this.submit();
        });
      });
    });
  });
</script>

@if ($errors->has('name'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            icon: 'error',
            title: 'Nombre inválido',
            text: '{{ $errors->first('name') }}',
            confirmButtonColor: '#d33'
        });

        @if (session('edit_modal'))
            const modalId = 'modalEditar{{ session('edit_modal') }}';
        @else
            const modalId = 'modalRegistrar';
        @endif

        const modal = new bootstrap.Modal(document.getElementById(modalId));
        modal.show();
    });
</script>
@endif
@stop
