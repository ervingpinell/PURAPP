@extends('adminlte::page')

@section('title', 'Amenidades')

@section('content_header')
    <h1>Gestión de Amenidades</h1>
@stop

@section('content')
<div class="p-3 table-responsive">
    <a href="#" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalRegistrar">
        <i class="fas fa-plus"></i> Añadir Amenidad
    </a>

    <table class="table table-bordered table-striped table-hover">
        <thead class="bg-primary text-white">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($amenities as $amenity)
            <tr>
                <td>{{ $amenity->amenity_id }}</td>
                <td>{{ $amenity->name }}</td>
                <td>
                    @if ($amenity->is_active)
                        <span class="badge bg-success">Activo</span>
                    @else
                        <span class="badge bg-secondary">Inactivo</span>
                    @endif
                </td>
<td>
  {{-- Editar --}}
  <a href="#" class="btn btn-edit btn-sm"
     data-bs-toggle="modal" data-bs-target="#modalEditar{{ $amenity->amenity_id }}"
     title="Editar">
    <i class="fas fa-edit"></i>
  </a>

  {{-- Toggle activar/desactivar --}}
  <form action="{{ route('admin.tours.amenities.toggle', $amenity->amenity_id) }}"
        method="POST" class="d-inline">
    @csrf
    @method('PATCH')
    <button type="submit"
            class="btn btn-toggle btn-sm"
            title="{{ $amenity->is_active ? 'Desactivar' : 'Activar' }}"
            onclick="return confirm('{{ $amenity->is_active ? '¿Deseas desactivarla?' : '¿Deseas activarla?' }}')">
      <i class="fas {{ $amenity->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
    </button>
  </form>

  {{-- Eliminar definitivo --}}
  <form action="{{ route('admin.tours.amenities.destroy', $amenity->amenity_id) }}"
        method="POST" class="d-inline">
    @csrf
    @method('DELETE')
    <button type="submit"
            class="btn btn-delete btn-sm"
            title="Eliminar definitivamente"
            onclick="return confirm('¿Eliminar definitivamente esta amenidad? Esta acción no se puede deshacer.')">
      <i class="fas fa-trash"></i>
    </button>
  </form>
</td>

            </tr>

            <!-- Modal editar -->
            <div class="modal fade" id="modalEditar{{ $amenity->amenity_id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <form action="{{ route('admin.tours.amenities.update', $amenity->amenity_id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Editar Amenidad</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label>Nombre</label>
                                    <input type="text" name="name" class="form-control" value="{{ $amenity->name }}" required>
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
        <form action="{{ route('admin.tours.amenities.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Amenidad</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nombre</label>
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
@if(session('success') && session('alert_type'))
<script>
    let icon = 'success';
    let title = 'Éxito';
    let color = '#3085d6';

    switch ("{{ session('alert_type') }}") {
        case 'activado':
            icon = 'success';
            title = 'Amenidad Activada';
            color = '#28a745';
            break;
        case 'desactivado':
            icon = 'warning';
            title = 'Amenidad Desactivada';
            color = '#ffc107';
            break;
        case 'actualizado':
            icon = 'info';
            title = 'Amenidad Actualizada';
            color = '#17a2b8';
            break;
        case 'creado':
            icon = 'success';
            title = 'Amenidad Creada';
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

        const modal = new bootstrap.Modal(document.getElementById('modalRegistrar'));
        modal.show();
    });
</script>
@endif
@stop
