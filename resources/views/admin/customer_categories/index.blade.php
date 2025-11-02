@extends('adminlte::page')

@section('title', 'Categorías de Clientes')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Categorías de Clientes</h1>
        <a href="{{ route('admin.customer_categories.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nueva Categoría
        </a>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('error') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Listado de Categorías</h3>
            <div class="card-tools">
                <span class="badge badge-primary">{{ $categories->total() }} categorías</span>
            </div>
        </div>

        <div class="card-body p-0">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th style="width: 60px">#</th>
                        <th>Nombre</th>
                        <th>Slug</th>
                        <th>Rango de Edad</th>
                        <th style="width: 80px">Orden</th>
                        <th style="width: 100px">Estado</th>
                        <th style="width: 150px" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td>{{ $category->category_id }}</td>
                            <td>
                                <strong>{{ $category->name }}</strong>
                            </td>
                            <td>
                                <code>{{ $category->slug }}</code>
                            </td>
                            <td>
                                <span class="badge badge-info">
                                    {{ $category->age_range }}
                                    @if($category->age_to === null)
                                        años o más
                                    @else
                                        años
                                    @endif
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-secondary">{{ $category->order }}</span>
                            </td>
                            <td>
                                <form action="{{ route('admin.customer_categories.toggle', $category) }}"
                                      method="POST"
                                      style="display: inline">
                                    @csrf
                                    <button type="submit"
                                            class="btn btn-sm {{ $category->is_active ? 'btn-success' : 'btn-secondary' }}"
                                            title="{{ $category->is_active ? 'Activo' : 'Inactivo' }}">
                                        <i class="fas fa-{{ $category->is_active ? 'check' : 'times' }}"></i>
                                        {{ $category->is_active ? 'Activo' : 'Inactivo' }}
                                    </button>
                                </form>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('admin.customer_categories.edit', $category) }}"
                                       class="btn btn-sm btn-info"
                                       title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <button type="button"
                                            class="btn btn-sm btn-danger"
                                            data-toggle="modal"
                                            data-target="#deleteModal{{ $category->category_id }}"
                                            title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>

                                {{-- Modal de Confirmación --}}
                                <div class="modal fade" id="deleteModal{{ $category->category_id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger">
                                                <h5 class="modal-title">Confirmar Eliminación</h5>
                                                <button type="button" class="close" data-dismiss="modal">
                                                    <span>&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <p>¿Estás seguro de eliminar la categoría <strong>{{ $category->name }}</strong>?</p>
                                                <p class="text-muted small">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                    Esta acción no se puede deshacer.
                                                </p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                    Cancelar
                                                </button>
                                                <form action="{{ route('admin.customer_categories.destroy', $category) }}"
                                                      method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="fas fa-trash"></i> Eliminar
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                No hay categorías registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
            <div class="card-footer">
                {{ $categories->links() }}
            </div>
        @endif
    </div>

    {{-- Información sobre rangos --}}
    <div class="card card-info">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-info-circle"></i> Información
            </h3>
        </div>
        <div class="card-body">
            <ul class="mb-0">
                <li>Los rangos de edad <strong>no pueden solaparse</strong> entre categorías activas.</li>
                <li>El <strong>orden</strong> determina cómo se muestran en el sistema.</li>
                <li>Usa <code>age_to = NULL</code> para indicar "sin límite superior" (ej: 18+ años).</li>
                <li>Las categorías inactivas no se muestran en formularios de reserva.</li>
            </ul>
        </div>
    </div>
@stop

@section('css')
    <style>
        .table td {
            vertical-align: middle;
        }
    </style>
@stop
