@extends('adminlte::page')

@section('title', 'Horarios de Tours')

@section('content_header')
    <h1>Gestión de Horarios</h1>
@stop

@section('content')
<div class="p-3 table-responsive">
    <a href="#" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalRegistrar">
        <i class="fas fa-plus"></i> Añadir Horario
    </a>

    <table class="table table-bordered table-striped table-hover">
        <thead class="bg-primary text-white">
            <tr>
                <th>ID</th>
                <th>Tour</th>
                <th>Hora</th>
                <th>Etiqueta</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($schedules as $schedule)
            <tr>
                <td>{{ $schedule->tour_schedule_id }}</td>
                <td>{{ $schedule->tour->name ?? 'N/A' }}</td>
                <td>{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}</td>
                <td>{{ $schedule->label }}</td>
                <td>
                    @if ($schedule->is_active)
                        <span class="badge bg-success">Activo</span>
                    @else
                        <span class="badge bg-secondary">Inactivo</span>
                    @endif
                </td>
                <td>
                    <!-- Editar -->
                    <a href="#" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditar{{ $schedule->tour_schedule_id }}">
                        <i class="fas fa-edit"></i>
                    </a>

                    <!-- Desactivar -->
                    <form action="{{ route('admin.tours.schedule.destroy', $schedule->tour_schedule_id) }}" method="POST" style="display: inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger"
                            onclick="return confirm('¿Deseas desactivarlo?')">
                            <i class="fas fa-times-circle"></i>
                        </button>
                    </form>
                </td>
            </tr>

            <!-- Modal editar -->
            <div class="modal fade" id="modalEditar{{ $schedule->tour_schedule_id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <form action="{{ route('admin.tours.schedule.update', $schedule->tour_schedule_id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Editar Horario</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label>Tour</label>
                                    <select name="tour_id" class="form-control" required>
                                        @foreach(App\Models\Tour::all() as $tour)
                                            <option value="{{ $tour->tour_id }}" {{ $schedule->tour_id == $tour->tour_id ? 'selected' : '' }}>
                                                {{ $tour->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label>Hora (HH:mm)</label>
                                    <input type="time" name="start_time" class="form-control" value="{{ $schedule->start_time }}" required>
                                </div>
                                <div class="mb-3">
                                    <label>Etiqueta</label>
                                    <input type="text" name="label" class="form-control" value="{{ $schedule->label }}">
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

    {{ $schedules->links() }}
</div>

<!-- Modal registrar -->
<div class="modal fade" id="modalRegistrar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.tours.schedule.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Horario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Tour</label>
                        <select name="tour_id" class="form-control" required>
                            <option value="">Seleccione un tour</option>
                            @foreach(App\Models\Tour::all() as $tour)
                                <option value="{{ $tour->tour_id }}">{{ $tour->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Hora (HH:mm)</label>
                        <input type="time" name="start_time" class="form-control" value="{{ old('start_time') }}" required>
                    </div>
                    <div class="mb-3">
                        <label>Etiqueta</label>
                        <input type="text" name="label" class="form-control" value="{{ old('label') }}">
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
@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Éxito',
        text: '{{ session('success') }}',
        confirmButtonColor: '#28a745'
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