@extends('adminlte::page')

@section('title', 'Itinerarios y sus Ítems')

@section('content_header')
    <h1>Itinerarios y Gestión de Ítems</h1>
@stop

@section('content')
<div class="p-3">
    <a href="#" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalCrearItinerario">
        <i class="fas fa-plus"></i> Nuevo Itinerario
    </a>

    <!-- Lista de Itinerarios -->
    @foreach($itineraries as $itinerary)
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ $itinerary->name }}</h5>
                <div>
                    <a href="#" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalAsignar{{ $itinerary->itinerary_id }}">
                        <i class="fas fa-link"></i> Asignar Ítems
                    </a>
                    <a href="#" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEditar{{ $itinerary->itinerary_id }}">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('admin.tours.itinerary.destroy', $itinerary->itinerary_id) }}" method="POST" style="display:inline-block">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este itinerario?')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-body">

                @if ($itinerary->items->isEmpty())
                    <p class="text-muted">No hay ítems asignados a este itinerario.</p>
                @else
                 <div>
                  <h5 class="mb-0">{!! nl2br(e($itinerary->description)) !!}</h5>
               </div>
               <br>
                    <ul class="list-group">
                        @foreach ($itinerary->items->sortBy('order') as $item)
                            <li class="list-group-item">
                                <strong>{{ $item->title }}</strong><br>
                                <span class="text-muted">{{ $item->description }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <!-- Modal asignar ítems -->
        <div class="modal fade" id="modalAsignar{{ $itinerary->itinerary_id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form action="{{ route('admin.tours.itinerary.assignItems', $itinerary->itinerary_id) }}" method="POST">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Asignar Ítems a {{ $itinerary->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            @foreach ($items as $item)
                                <div class="form-check">
                              <input class="form-check-input" type="checkbox" name="item_ids[]" value="{{ $item->item_id }}"
    {{ $itinerary->items->contains('item_id', $item->item_id) ? 'checked' : '' }}>
                                    <label class="form-check-label">
                                        <strong>{{ $item->title }}</strong> - {{ $item->description }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Guardar</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal editar itinerario -->
        <div class="modal fade" id="modalEditar{{ $itinerary->itinerary_id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form action="{{ route('admin.tours.itinerary.update', $itinerary->itinerary_id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Editar Itinerario</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label>Nombre</label>
                                <input type="text" name="name" class="form-control" value="{{ $itinerary->name }}" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-warning">Actualizar</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

    <!-- CRUD de Itinerary Items -->
    <hr>
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h4>Ítems de Itinerario</h4>
        <a href="#" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalRegistrarItem">
            <i class="fas fa-plus"></i> Añadir Ítem
        </a>
    </div>
    <table class="table table-bordered table-striped">
        <thead class="bg-secondary text-white">
            <tr>
                <th>#</th>
                <th>Título</th>
                <th>Descripción</th>
                <th>Orden</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
            <tr>
                <td>{{ $item->item_id }}</td>
                <td>{{ $item->title }}</td>
                <td>{{ $item->description }}</td>
                <td>{{ $item->order }}</td>
                <td>
                    @if ($item->is_active)
                        <span class="badge bg-success">Activo</span>
                    @else
                        <span class="badge bg-secondary">Inactivo</span>
                    @endif
                </td>
                <td>
                    <a href="#" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditarItem{{ $item->item_id }}">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('admin.tours.itinerary_items.destroy', $item->item_id) }}" method="POST" style="display:inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm {{ $item->is_active ? 'btn-danger' : 'btn-success' }}"
                            onclick="return confirm('¿Cambiar estado del ítem?')">
                            <i class="fas {{ $item->is_active ? 'fa-times-circle' : 'fa-check-circle' }}"></i>
                        </button>
                    </form>
                </td>
            </tr>

            <!-- Modal editar ítem -->
            <div class="modal fade" id="modalEditarItem{{ $item->item_id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <form action="{{ route('admin.tours.itinerary_items.update', $item->item_id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Editar Ítem</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label>Título</label>
                                    <input type="text" name="title" class="form-control" value="{{ $item->title }}" required>
                                </div>
                                <div class="mb-3">
                                    <label>Descripción</label>
                                    <textarea name="description" class="form-control" required>{{ $item->description }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label>Orden</label>
                                    <input type="number" name="order" class="form-control" value="{{ $item->order }}" min="0">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-warning">Actualizar</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal registrar ítem -->
<div class="modal fade" id="modalRegistrarItem" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.tours.itinerary_items.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Ítem</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Título</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Descripción</label>
                        <textarea name="description" class="form-control" required></textarea>
                    </div>
        
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal crear itinerario -->
<div class="modal fade" id="modalCrearItinerario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.tours.itinerary.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nuevo Itinerario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nombre</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Crear</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@stop