@extends('adminlte::page')

@section('title', 'Editar Tour - ' . $tour->name)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Editar: {{ $tour->name }}</h1>
        <div>
            <a href="{{ route('admin.tours.prices.index', $tour) }}" class="btn btn-warning mr-2">
                <i class="fas fa-dollar-sign"></i> Gestionar Precios Detallados
            </a>
            <a href="{{ route('admin.tours.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
@stop

@section('content')
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Errores:</strong>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.tours.update', $tour) }}" method="POST" id="tourForm">
        @csrf
        @method('PUT')

        <div class="card card-primary card-outline card-outline-tabs">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" id="tourTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="summary-tab" data-toggle="pill" href="#summary" role="tab">
                            <i class="fas fa-eye"></i> Resumen
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="details-tab" data-toggle="pill" href="#details" role="tab">
                            <i class="fas fa-info-circle"></i> Detalles
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="prices-tab" data-toggle="pill" href="#prices" role="tab">
                            <i class="fas fa-dollar-sign"></i> Precios
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="itinerary-tab" data-toggle="pill" href="#itinerary" role="tab">
                            <i class="fas fa-route"></i> Itinerario
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="schedules-tab" data-toggle="pill" href="#schedules" role="tab">
                            <i class="fas fa-clock"></i> Horarios
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="languages-tab" data-toggle="pill" href="#languages" role="tab">
                            <i class="fas fa-language"></i> Idiomas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="amenities-tab" data-toggle="pill" href="#amenities" role="tab">
                            <i class="fas fa-check-circle"></i> Amenidades
                        </a>
                    </li>
                </ul>
            </div>

            <div class="card-body">
                <div class="tab-content" id="tourTabsContent">
                    {{-- Pestaña: Resumen (Primera en edit) --}}
                    <div class="tab-pane fade show active" id="summary" role="tabpanel">
                        @include('admin.tours.partials.tab-summary', ['tour' => $tour])
                    </div>

                    {{-- Pestaña: Detalles --}}
                    <div class="tab-pane fade" id="details" role="tabpanel">
                        @include('admin.tours.partials.tab-details', ['tour' => $tour])
                    </div>

                    {{-- Pestaña: Precios --}}
                    <div class="tab-pane fade" id="prices" role="tabpanel">
                        @include('admin.tours.partials.tab-prices', ['tour' => $tour])
                    </div>

                    {{-- Pestaña: Itinerario --}}
                    <div class="tab-pane fade" id="itinerary" role="tabpanel">
                        @include('admin.tours.partials.tab-itinerary', ['tour' => $tour])
                    </div>

                    {{-- Pestaña: Horarios --}}
                    <div class="tab-pane fade" id="schedules" role="tabpanel">
                        @include('admin.tours.partials.tab-schedules', ['tour' => $tour])
                    </div>

                    {{-- Pestaña: Idiomas --}}
                    <div class="tab-pane fade" id="languages" role="tabpanel">
                        @include('admin.tours.partials.tab-languages', ['tour' => $tour])
                    </div>

                    {{-- Pestaña: Amenidades --}}
                    <div class="tab-pane fade" id="amenities" role="tabpanel">
                        @include('admin.tours.partials.tab-amenities', ['tour' => $tour])
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save"></i> Actualizar Tour
                </button>
                <a href="{{ route('admin.tours.index') }}" class="btn btn-secondary btn-lg">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </div>
    </form>
@stop

@section('js')
    @include('admin.tours.partials.scripts')
@stop
