@extends('adminlte::page')

@section('title', 'Agregar Disponibilidad')

@section('content_header')
    <h1>Agregar Nueva Disponibilidad</h1>
    <a href="{{ route('admin.tours.availabilities.index') }}" class="btn btn-secondary">Volver</a>
@stop

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.tours.availabilities.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="tour_id">Tour</label>
            <select name="tour_id" id="tour_id" class="form-control" required>
                <option value="">Seleccione un tour</option>
                @foreach($tours as $tour)
                    <option value="{{ $tour->tour_id }}" {{ old('tour_id') == $tour->tour_id ? 'selected' : '' }}>{{ $tour->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="date">Fecha</label>
            <input type="date" name="date" id="date" class="form-control" value="{{ old('date') }}" required>
        </div>

        <div class="form-group">
            <label for="start_time">Hora Inicio (opcional)</label>
            <input type="time" name="start_time" id="start_time" class="form-control" value="{{ old('start_time') }}">
        </div>

        <div class="form-group">
            <label for="end_time">Hora Fin (opcional)</label>
            <input type="time" name="end_time" id="end_time" class="form-control" value="{{ old('end_time') }}">
        </div>

        <div class="form-group">
            <label for="available">Disponible</label>
            <select name="available" id="available" class="form-control" required>
                <option value="1" {{ old('available') == 1 ? 'selected' : '' }}>Sí</option>
                <option value="0" {{ old('available') == 0 ? 'selected' : '' }}>No</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Guardar</button>
    </form>
@stop
