@extends('adminlte::page')

@section('title', 'Nueva Categoría de Cliente')

@section('content_header')
    <h1>Nueva Categoría de Cliente</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <form action="{{ route('admin.customer_categories.store') }}" method="POST">
                    @csrf

                    <div class="card-body">
                        @include('admin.customer_categories.partials.form')
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Categoría
                        </button>
                        <a href="{{ route('admin.customer_categories.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            @include('admin.customer_categories.partials.help')
        </div>
    </div>
@stop
