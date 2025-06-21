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
                <td>
                    @if ($tourtype->is_active)
                        <span class="badge bg-success">Activo</span>
                    @else
                        <span class="badge bg-secondary">Inactivo</span>
                    @endif
                </td>
                <td>
                    <!-- Editar -->
                    <a href="#" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditar{{ $tourtype->tour_type_id }}">
                        <i class="fas fa-edit"></i>
                    </a>

                    <!-- Activar/Desactivar -->
                    <form action="{{ route('admin.tourtypes.toggle', $tourtype->tour_type_id) }}" method="POST" style="display: inline-block;">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-sm {{ $tourtype->is_active ? 'btn-danger' : 'btn-success' }}"
                            onclick="return confirm('{{ $tourtype->is_active ? '¿Deseas desactivarlo?' : '¿Deseas activarlo?' }}')">
                            <i class="fas {{ $tourtype->is_active ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                        </button>
                    </form>
                </td>
            </tr>

            <!-- Modal editar -->
            <div class="modal fade" id="modalEditar{{ $tourtype->tour_type_id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                  <form action="{{ route('admin.tourtypes.update', $tourtype->tour_type_id) }}" method="POST">

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
                                    <input type="text" name="name" class="form-control"
                                        value="{{ session('edit_modal') == $tourtype->tour_type_id ? old('name', $tourtype->name) : $tourtype->name }}" required>
                                </div>
                                <div class="mb-3">
                                    <label>Descripción</label>
                                    <textarea name="description" class="form-control">{{ session('edit_modal') == $tourtype->tour_type_id ? old('description', $tourtype->description) : $tourtype->description }}</textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-warning">Actualizar</button>
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
        <form action="{{ route('admin.tourtypes.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Tipo de Tour</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nombre</label>
                        <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                    </div>
                    <div class="mb-3">
                        <label>Descripción</label>
                        <textarea name="description" class="form-control">{{ old('description') }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@if(session('success') && session('alert_type'))
<script>
    let icon = 'success';
    let title = 'Éxito';
    let color = '#3085d6';

    switch ("{{ session('alert_type') }}") {
        case 'activado':
            icon = 'success';
            title = 'Tipo de Tour Activado';
            color = '#28a745';
            break;
        case 'desactivado':
            icon = 'warning';
            title = 'Tipo de Tour Desactivado';
            color = '#ffc107';
            break;
        case 'actualizado':
            icon = 'info';
            title = 'Tipo de Tour Actualizado';
            color = '#17a2b8';
            break;
        case 'creado':
            icon = 'success';
            title = 'Tipo de Tour Creado';
            color = '#007bff';
            break;
    }

    Swal.fire({
        icon: icon,
        title: title,
        text: '{{ session('success') }}',
        confirmButtonColor: color,
        confirmButtonText: 'OK'
    });
</script>
@endif

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
