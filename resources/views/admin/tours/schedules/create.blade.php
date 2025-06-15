@extends('adminlte::page')

@section('title', 'Agregar Horario')

@section('content_header')
    <h1>Agregar Nuevo Horario</h1>
@stop

@section('content')

@include('admin.partials.alerts')

<form action="{{ route('admin.tours.schedules.store') }}" method="POST">
    @csrf

    <div class="form-group">
        <label for="tour_id">Tour</label>
        <select name="tour_id" id="tour_id" class="form-control @error('tour_id') is-invalid @enderror" required>
            <option value="">Seleccione un tour</option>
            @foreach($tours as $tour)
                <option value="{{ $tour->tour_id }}" {{ old('tour_id') == $tour->tour_id ? 'selected' : '' }}>
                    {{ $tour->name }}
                </option>
            @endforeach
        </select>
        @error('tour_id')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="start_time">Hora de Inicio</label>
        <input type="time" name="start_time" id="start_time" class="form-control @error('start_time') is-invalid @enderror" value="{{ old('start_time') }}" required>
        @error('start_time')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="label">Etiqueta (opcional)</label>
        <input type="text" name="label" id="label" class="form-control @error('label') is-invalid @enderror" value="{{ old('label') }}">
        @error('label')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="{{ route('admin.tours.schedules.index') }}" class="btn btn-secondary">Cancelar</a>
</form>
@stop
