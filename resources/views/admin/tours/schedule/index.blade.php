@extends('adminlte::page')

@section('title', 'Horarios de Tours')

@section('content_header')
    <h1>Gestión de Horarios</h1>
@stop

@section('content')
<div class="p-3">
    <a href="#" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalRegistrarGeneral">
        <i class="fas fa-plus"></i> Añadir Horario General
    </a>

    <div class="row">
        @php
            $allTours = \App\Models\Tour::with('schedules')->orderBy('tour_id')->get();
        @endphp

        @foreach ($allTours as $tour)
            @php
                $horarios = $tour->schedules->sortBy(function ($s) {
                    $h = (int) date('H', strtotime($s->start_time));
                    return ($h < 12 ? 0 : 1) . date('H:i', strtotime($s->start_time));
                });
                $activos = $horarios->where('is_active', true);
            @endphp

            <div class="col-md-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ $tour->name }}</h5>
                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalAgregarHorario{{ $tour->tour_id }}">
                            <i class="fas fa-plus"></i> Añadir horario
                        </button>
                    </div>
                    <div class="card-body">
                        @forelse($horarios as $bloque)
                            <div class="d-flex justify-content-between align-items-center border rounded p-2 mb-2">
                                <div>
                                    🕒 {{ date('g:i A', strtotime($bloque->start_time)) }}
                                    –
                                    {{ date('g:i A', strtotime($bloque->end_time)) }}<br>
                                    <span class="badge {{ $bloque->is_active ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $bloque->is_active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </div>
                                <div>
                                    <a href="#" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEditar{{ $bloque->schedule_id }}">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="{{ route('admin.tours.schedule.toggle', $bloque->schedule_id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit"
                                            class="btn btn-sm {{ $bloque->is_active ? 'btn-danger' : 'btn-success' }}"
                                            title="{{ $bloque->is_active ? 'Desactivar' : 'Activar' }}"
                                            onclick="return confirm('¿Deseas {{ $bloque->is_active ? 'desactivar' : 'activar' }} este horario?')">
                                            <i class="fas {{ $bloque->is_active ? 'fa-times' : 'fa-check' }}"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            {{-- Modal editar --}}
                            <div class="modal fade" id="modalEditar{{ $bloque->schedule_id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form action="{{ route('admin.tours.schedule.update', $bloque->schedule_id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Editar Horario</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="tour_id" value="{{ $bloque->tour_id }}">
                                                <div class="mb-3">
                                                    <label>Hora de Inicio</label>
                                                    <input type="time" name="start_time" class="form-control" value="{{ $bloque->start_time }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Hora de Fin</label>
                                                    <input type="time" name="end_time" class="form-control" value="{{ $bloque->end_time }}" required>
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
                        @empty
                            <span class="text-muted">No hay horarios configurados.</span>
                        @endforelse
                    </div>
                    <div class="card-footer text-muted">
                        {{ $activos->count() }} bloque{{ $activos->count() != 1 ? 's' : '' }} activo{{ $activos->count() != 1 ? 's' : '' }}
                    </div>
                </div>
            </div>

            {{-- Modal agregar horario --}}
            <div class="modal fade" id="modalAgregarHorario{{ $tour->tour_id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <form action="{{ route('admin.tours.schedule.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="tour_id" value="{{ $tour->tour_id }}">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Añadir Horario a {{ $tour->name }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label>Hora de Inicio</label>
                                    <input type="time" name="start_time" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label>Hora de Fin</label>
                                    <input type="time" name="end_time" class="form-control" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Guardar</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- Modal para agregar general --}}
<div class="modal fade" id="modalRegistrarGeneral" tabindex="-1" aria-hidden="true">
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
                            @foreach(\App\Models\Tour::orderBy('tour_id')->get() as $tour)
                                <option value="{{ $tour->tour_id }}">{{ $tour->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Hora de Inicio</label>
                        <input type="time" name="start_time" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Hora de Fin</label>
                        <input type="time" name="end_time" class="form-control" required>
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
@stop
