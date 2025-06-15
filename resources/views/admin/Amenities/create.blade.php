@extends('adminlte::page')

@section('title', 'Crear Amenidad')

@section('content_header')
    <h1>Crear Amenidad</h1>
@stop

@section('content')
    <form action="{{ route('amenities.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Nombre</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
            @error('name')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <button class="btn btn-primary mt-2" type="submit">Guardar</button>
        <a href="{{ route('amenities.index') }}" class="btn btn-secondary mt-2">Cancelar</a>
    </form>
@stop
