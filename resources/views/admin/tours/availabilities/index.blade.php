@extends('adminlte::page')

@section('title', 'Disponibilidades de Tours')

@section('content_header')
    <h1>Disponibilidades de Tours</h1>
    <a href="{{ route('admin.tours.availabilities.create') }}" class="btn btn-primary">Agregar Disponibilidad</a>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tour</th>
                <th>Fecha</th>
                <th>Hora Inicio</th>
                <th>Hora Fin</th>
                <th>Disponible</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($availabilities as $availability)
                <tr>
                    <td>{{ $availability->availability_id }}</td>
                    <td>{{ $availability->tour->name }}</td>
                    <td>{{ $availability->date->format('d/m/Y') }}</td>
                    <td>{{ $availability->start_time ?? '-' }}</td>
                    <td>{{ $availability->end_time ?? '-' }}</td>
                    <td>{{ $availability->available ? 'Sí' : 'No' }}</td>
                    <td>
                        <a href="{{ route('admin.tours.availabilities.edit', $availability) }}" class="btn btn-sm btn-warning">Editar</a>

                        <form action="{{ route('admin.tours.availabilities.destroy', $availability) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('¿Desactivar esta disponibilidad?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" type="submit">Desactivar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7">No hay disponibilidades registradas.</td></tr>
            @endforelse
        </tbody>
    </table>
@stop
