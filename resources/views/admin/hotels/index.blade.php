@extends('adminlte::page')

@section('title', 'Lista de Hoteles')

@section('content_header')
    <h1>Hoteles Registrados</h1>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Formulario para agregar nuevo hotel --}}
    <form action="{{ route('admin.hotels.store') }}" method="POST" class="mb-4">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <input type="text" name="name" class="form-control" placeholder="Nombre del hotel" required>
            </div>
            <div class="col-md-2">
                <button class="btn btn-success" type="submit">Agregar</button>
            </div>
        </div>
    </form>

    {{-- Tabla de hoteles --}}
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($hotels as $hotel)
                <tr>
                    <td>{{ $hotel->name }}</td>
                    <td>
                        <span class="badge bg-{{ $hotel->is_active ? 'success' : 'secondary' }}">
                            {{ $hotel->is_active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td>
                        {{-- Botón para desactivar/activar --}}
                        <form action="{{ route('admin.hotels.update', $hotel->hotel_id) }}" method="POST" style="display: inline-block;">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="name" value="{{ $hotel->name }}">
                            <input type="hidden" name="is_active" value="{{ $hotel->is_active ? 0 : 1 }}">
                            <button class="btn btn-sm btn-warning" type="submit">
                                {{ $hotel->is_active ? 'Desactivar' : 'Activar' }}
                            </button>
                        </form>

                        {{-- Botón eliminar --}}
                        <form action="{{ route('admin.hotels.destroy', $hotel->hotel_id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('¿Seguro que deseas eliminar este hotel?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">No hay hoteles registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@stop
