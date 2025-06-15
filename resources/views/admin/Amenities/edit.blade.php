@extends('adminlte::page')

@section('title', 'Editar Amenidad')

@section('content_header')
    <h1>Editar Amenidad</h1>
@stop

@section('content')
    <form action="{{ route('amenities.update', $amenity) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">Nombre</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $amenity->name) }}" required>
            @error('name')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <button class="btn btn-primary mt-2" type="submit">Actualizar</button>
        <a href="{{ route('amenities.index') }}" class="btn btn-secondary mt-2">Cancelar</a>
    </form>
@stop
