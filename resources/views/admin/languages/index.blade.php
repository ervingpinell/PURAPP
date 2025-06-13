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

    <table class="table table-bordered table-striped table-hover">
        <thead class="bg-primary text-white">
            <tr>
                <th>ID</th>
                <th>Idioma</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($languages as $language)
            <tr>
                <td>{{ $language->tour_language_id }}</td>
                <td>{{ $language->name }}</td>
                <td>
                    @if ($language->is_active)
                        <span class="badge bg-success">Activo</span>
                    @else
                        <span class="badge bg-secondary">Inactivo</span>
                    @endif
                </td>
                <td>
                    <a href="#" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditar{{ $language->tour_language_id }}">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('admin.languages.destroy', $language->tour_language_id) }}" method="POST" style="display: inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm {{ $language->is_active ? 'btn-danger' : 'btn-success' }}"
                            onclick="return confirm('{{ $language->is_active ? '¿Deseas desactivarlo?' : '¿Deseas activarlo?' }}')">
                            <i class="fas {{ $language->is_active ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                        </button>
                    </form>
                </td>
            </tr>

            <div class="modal fade" id="modalEditar{{ $language->tour_language_id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <form action="{{ route('admin.languages.update', $language->tour_language_id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Editar Idioma</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label>Idioma</label>
                                    <input type="text" name="name" class="form-control" value="{{ $language->name }}" required>
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

<div class="modal fade" id="modalRegistrar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.languages.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Idioma</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Idioma</label>
                        <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
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
            title = 'Idioma Activado';
            color = '#28a745';
            break;
        case 'desactivado':
            icon = 'warning';
            title = 'Idioma Desactivado';
            color = '#ffc107';
            break;
        case 'actualizado':
            icon = 'info';
            title = 'Idioma Actualizado';
            color = '#17a2b8';
            break;
        case 'creado':
            icon = 'success';
            title = 'Idioma Registrado';
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
            title: 'Idioma inválido',
            text: '{{ $errors->first('name') }}',
            confirmButtonColor: '#d33'
        });

        const modal = new bootstrap.Modal(document.getElementById('modalRegistrar'));
        modal.show();
    });
</script>
@endif
@stop
