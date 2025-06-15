@extends('adminlte::page')

@section('title', 'Horarios de Tours')

@section('content_header')
    <h1>Horarios de Tours</h1>
@stop

@section('content')
    @include('admin.partials.alerts') {{-- Para mostrar mensajes success/error, crea este partial --}}

    <a href="{{ route('admin.tours.schedules.create') }}" class="btn btn-primary mb-3">Nuevo Horario</a>

    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tour</th>
                <th>Hora de Inicio</th>
                <th>Etiqueta</th>
                <th>Activo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($schedules as $schedule)
                <tr>
                    <td>{{ $schedule->tour_schedule_id }}</td>
                    <td>{{ $schedule->tour->name ?? 'N/A' }}</td>
                    <td>{{ $schedule->start_time }}</td>
                    <td>{{ $schedule->label }}</td>
                    <td>{!! $schedule->is_active ? '<span class="badge badge-success">Sí</span>' : '<span class="badge badge-danger">No</span>' !!}</td>
                    <td>
                        <a href="{{ route('admin.tours.schedules.edit', $schedule->tour_schedule_id) }}" class="btn btn-sm btn-warning">Editar</a>

                        <form action="{{ route('admin.tours.schedules.destroy', $schedule->tour_schedule_id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('¿Eliminar este horario?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@stop
