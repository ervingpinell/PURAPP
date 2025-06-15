@extends('adminlte::page')

@section('title', 'Itinerario de Tours')

@section('content_header')
    <h1>Ítems del Itinerario</h1>
@stop

@section('content')
<div class="p-3 table-responsive">
    <a href="#" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalRegistrar">
        <i class="fas fa-plus"></i> Añadir Ítem
    </a>

    <table class="table table-bordered table-striped table-hover">
        <thead class="bg-primary text-white">
            <tr>
                <th>#</th>
                <th>Título</th>
                <th>Descripción</th>
                <th>Orden</th>
                <th>Tour</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
            <tr>
                <td>{{ $item->item_id }}</td>
                <td>{{ $item->title }}</td>
                <td>{{ $item->description }}</td>
                <td>{{ $item->order }}</td>
                <td>{{ $item->tour->name ?? 'N/A' }}</td>
                <td>
                    @if ($item->is_active)
                        <span class="badge bg-success">Activo</span>
                    @else
                        <span class="badge bg-secondary">Inactivo</span>
                    @endif
                </td>
                <td>
                    <!-- Editar -->
                    <a href="#" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditar{{ $item->item_id }}">
                        <i class="fas fa-edit"></i>
                    </a>

                    <!-- Activar/Desactivar -->
                    <form action="{{ route('admin.tours.itinerary.destroy', $item->item_id) }}" method="POST" style="display: inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm {{ $item->is_active ? 'btn-danger' : 'btn-success' }}"
                            onclick="return confirm('{{ $item->is_active ? '¿Deseas desactivarlo?' : '¿Deseas activarlo?' }}')">
                            <i class="fas {{ $item->is_active ? 'fa-times-circle' : 'fa-check-circle' }}"></i>
                        </button>
                    </form>
                </td>
            </tr>

            <!-- Modal editar -->
            <div class="modal fade" id="modalEditar{{ $item->item_id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <form action="{{ route('admin.tours.itinerary.update', $item->item_id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Editar Ítem</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label>Tour</label>
                                    <select name="tour_id" class="form-control" required>
                                        @foreach ($tours as $tour)
                                            <option value="{{ $tour->tour_id }}" {{ $item->tour_id == $tour->tour_id ? 'selected' : '' }}>
                                                {{ $tour->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label>Título</label>
                                    <input type="text" name="title" class="form-control" value="{{ $item->title }}" required>
                                </div>
                                <div class="mb-3">
                                    <label>Descripción</label>
                                    <textarea name="description" class="form-control" required>{{ $item->description }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label>Orden</label>
                                    <input type="number" name="order" class="form-control" value="{{ $item->order }}" min="0">
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
        <form action="{{ route('admin.tours.itinerary.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Ítem</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Tour</label>
                        <select name="tour_id" class="form-control" required>
                            @foreach ($tours as $tour)
                                <option value="{{ $tour->tour_id }}">{{ $tour->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Título</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Descripción</label>
                        <textarea name="description" class="form-control" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Orden</label>
                        <input type="number" name="order" class="form-control" value="0" min="0">
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
<!-- SweetAlert feedback -->
@if(session('success') && session('alert_type'))
<script>
    let icon = 'success';
    let title = 'Éxito';
    let color = '#3085d6';

    switch ("{{ session('alert_type') }}") {
        case 'activado':
            icon = 'success';
            title = 'Ítem Activado';
            color = '#28a745';
            break;
        case 'desactivado':
            icon = 'warning';
            title = 'Ítem Desactivado';
            color = '#ffc107';
            break;
        case 'actualizado':
            icon = 'info';
            title = 'Ítem Actualizado';
            color = '#17a2b8';
            break;
        case 'creado':
            icon = 'success';
            title = 'Ítem Creado';
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

@if ($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ $errors->first() }}',
            confirmButtonColor: '#d33'
        });

        const modal = new bootstrap.Modal(document.getElementById('modalRegistrar'));
        modal.show();
    });
</script>
@endif
@stop
