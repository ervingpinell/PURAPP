@extends('adminlte::page')

@section('title', 'Editar Categoría')

@section('content_header')
    <h1>Editar Categoría: {{ $category->name }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <form action="{{ route('admin.customer_categories.update', $category) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        @include('admin.customer_categories.partials.form', ['category' => $category])
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Actualizar Categoría
                        </button>
                        <a href="{{ route('admin.customer_categories.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            @include('admin.customer_categories.partials.help')

            {{-- Información adicional --}}
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">Información</h3>
                </div>
                <div class="card-body">
                    <p><strong>ID:</strong> {{ $category->category_id }}</p>
                    <p><strong>Creado:</strong> {{ $category->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Actualizado:</strong> {{ $category->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
@stop
