@extends('adminlte::page')

@section('title', 'Amenidades')

@section('content_header')
    <h1>Amenidades</h1>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('amenities.store') }}" method="POST" class="mb-4">
        @csrf
        <div class="input-group">
            <input type="text" name="name" class="form-control" placeholder="Nueva amenidad" required>
            <div class="input-group-append">
                <button class="btn btn-primary" type="submit">Agregar</button>
            </div>
        </div>
        @error('name')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </form>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($amenities as $amenity)
                <tr>
                    <td>{{ $amenity->name }}</td>
                    <td>
                        <form action="{{ route('amenities.destroy', $amenity) }}" method="POST" style="display:inline-block" onsubmit="return confirm('¿Desactivar esta amenidad?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-warning btn-sm">Desactivar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@stop
