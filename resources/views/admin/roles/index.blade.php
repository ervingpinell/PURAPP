@extends('adminlte::page')

@section('title', 'Reservas')

@section('content_header')
    <h1>Gestión de Roles</h1>
@stop

@section('content')

    <!-- Botón para crear nuevo rol -->
    <div class="mb-3">
        <a href="#" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCrearRol">
            <i class="fas fa-plus"></i> Añadir Rol
        </a>
    </div>

    <div class="card">
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre del Rol</th>
                        <th>Descripción</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($roles as $role)
                        <tr>
                            <td>{{ $role->role_id }}</td>
                            <td>{{ $role->role_name }}</td>
                            <td>{{ $role->description ?? '—' }}</td>
                            <td>
                                <a href="{{ route('admin.roles.edit', $role->role_id) }}" class="btn btn-sm btn-warning">Editar</a>

                                <form action="{{ route('admin.roles.destroy', $role->role_id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    @php
                                        $text = $role->is_active ? '¿Deseas desactivar este rol?' : '¿Deseas activar este rol?';
                                        $label = $role->is_active ? 'Desactivar' : 'Activar';
                                        $btnClass = $role->is_active ? 'btn-danger' : 'btn-success';
                                    @endphp
                                    <button class="btn btn-sm {{ $btnClass }}" onclick="return confirm('{{ $text }}')">
                                        {{ $label }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No hay roles registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Crear Rol -->
    <div class="modal fade" id="modalCrearRol" tabindex="-1" aria-labelledby="modalCrearRolLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('admin.roles.store') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCrearRolLabel">Crear Nuevo Rol</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="role_name" class="form-label">Nombre del Rol</label>
                        <input type="text" name="role_name" id="role_name" class="form-control" required maxlength="50">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Descripción (opcional)</label>
                        <textarea name="description" id="description" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
    </div>

@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: "{{ session('success') }}",
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
        </script>
    @endif
@stop
