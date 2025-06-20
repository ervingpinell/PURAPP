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
    @foreach($itineraries as $index => $itinerary)
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <button class="btn btn-link text-start text-decoration-none d-flex align-items-center gap-2"
                        data-bs-toggle="collapse"
                        data-bs-target="#collapseItinerary{{ $itinerary->itinerary_id }}"
                        aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                        aria-controls="collapseItinerary{{ $itinerary->itinerary_id }}">
                        <span class="d-flex align-items-center">
                        <i class="fas {{ $index === 0 ? 'fa-minus' : 'fa-plus' }} icon-toggle"
                        style="color:#00bc8c; margin-right: 0.5rem;"
                        id="iconToggle{{ $itinerary->itinerary_id }}"></i>
                        <h5 class="mb-0" style="color:#dee2e6">{{ $itinerary->name }}</h5>
                </span>
                </button>

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

            <div id="collapseItinerary{{ $itinerary->itinerary_id }}"
                 class="collapse {{ $index === 0 ? 'show' : '' }}">
                <div class="card-body">
                    @if ($itinerary->items->isEmpty())
                        <p class="text-muted">No hay ítems asignados a este itinerario.</p>
                    @else
                        <div class="mb-2">
                            <h6 class="text-muted">{!! nl2br(e($itinerary->description)) !!}</h6>
                        </div>
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

    {{-- Importamos la vista de ítems --}}
@include('admin.tours.itinerary.items.crud', ['items' => $items])
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
@section('js')
<script>
    // Toggle icon "+" ↔ "−"
    document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(btn => {
        const targetId = btn.getAttribute('data-bs-target');
        const icon = btn.querySelector('.icon-toggle');
        const collapse = document.querySelector(targetId);

        if (collapse) {
            collapse.addEventListener('show.bs.collapse', () => {
                if (icon) icon.classList.replace('fa-plus', 'fa-minus');
            });
            collapse.addEventListener('hide.bs.collapse', () => {
                if (icon) icon.classList.replace('fa-minus', 'fa-plus');
            });
        }
    });
</script>
@endsection