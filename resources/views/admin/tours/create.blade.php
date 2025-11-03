@extends('adminlte::page')

@section('title', 'Crear Tour')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Crear Nuevo Tour</h1>
        <a href="{{ route('admin.tours.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver al Listado
        </a>
    </div>
@stop

@section('content')
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Errores en el formulario:</strong>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.tours.store') }}" method="POST" id="tourForm">
        @csrf

        <div class="card card-primary card-outline card-outline-tabs">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" id="tourTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="details-tab" data-toggle="pill" href="#details" role="tab">
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
                    <li class="nav-item">
                        <a class="nav-link" id="summary-tab" data-toggle="pill" href="#summary" role="tab">
                            <i class="fas fa-eye"></i> Resumen
                        </a>
                    </li>
                </ul>
            </div>

            <div class="card-body">
                <div class="tab-content" id="tourTabsContent">
                    {{-- Pestaña: Detalles --}}
                    <div class="tab-pane fade show active" id="details" role="tabpanel">
                        @include('admin.tours.partials.tab-details', ['tour' => null])
                    </div>

                    {{-- Pestaña: Precios --}}
                    <div class="tab-pane fade" id="prices" role="tabpanel">
                        @include('admin.tours.partials.tab-prices', ['tour' => null])
                    </div>

                    {{-- Pestaña: Itinerario --}}
                    <div class="tab-pane fade" id="itinerary" role="tabpanel">
                        @include('admin.tours.partials.tab-itinerary', ['tour' => null])
                    </div>

                    {{-- Pestaña: Horarios --}}
                    <div class="tab-pane fade" id="schedules" role="tabpanel">
                        @include('admin.tours.partials.tab-schedules', ['tour' => null])
                    </div>

                    {{-- Pestaña: Idiomas --}}
                    <div class="tab-pane fade" id="languages" role="tabpanel">
                        @include('admin.tours.partials.tab-languages', ['tour' => null])
                    </div>

                    {{-- Pestaña: Amenidades --}}
                    <div class="tab-pane fade" id="amenities" role="tabpanel">
                        @include('admin.tours.partials.tab-amenities', ['tour' => null])
                    </div>

                    {{-- Pestaña: Resumen --}}
                    <div class="tab-pane fade" id="summary" role="tabpanel">
                        @include('admin.tours.partials.tab-summary', ['tour' => null])
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fas fa-save"></i> Crear Tour
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
