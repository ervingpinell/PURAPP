@extends('adminlte::page')

@section('title', 'Itinerarios y sus Ítems')

@section('content_header')
    <h1>Itinerarios y Gestión de Ítems</h1>
@stop

@section('content')
@php
    $itineraryToEdit = request('itinerary_id');
@endphp

<div class="p-3">
    <a href="#" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalCrearItinerario">
        <i class="fas fa-plus"></i> Nuevo Itinerario
    </a>

    @foreach($itineraries as $index => $itinerary)
        @php
    $openEditModal = $itineraryToEdit && $itineraryToEdit == $itinerary->itinerary_id;
    $expandItinerary = $openEditModal;


        @endphp

        <div class="card mb-3" id="card-itinerary-{{ $itinerary->itinerary_id }}">
            <div class="card-header d-flex justify-content-between align-items-center">
                <button class="btn btn-link text-start text-decoration-none d-flex align-items-center gap-2"
                        data-bs-toggle="collapse"
                        data-bs-target="#collapseItinerary{{ $itinerary->itinerary_id }}"
                        aria-expanded="{{ $expandItinerary ? 'true' : 'false' }}"
                        aria-controls="collapseItinerary{{ $itinerary->itinerary_id }}">
                        <span class="d-flex align-items-center">
                            <i class="fas {{ $expandItinerary ? 'fa-minus' : 'fa-plus' }} icon-toggle"
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
                        @php
                            $active = $itinerary->is_active;
                            $btnClass = $active ? 'btn-danger' : 'btn-success';
                            $icon    = $active ? 'fa-times-circle' : 'fa-check-circle';
                            $text    = $active ? 'Desactivar este itinerario?' : 'Activar este itinerario?';
                        @endphp
                        <button class="btn btn-sm {{ $btnClass }}"
                                onclick="return confirm('{{ $text }}')"
                                title="{{ $active ? 'Desactivar' : 'Activar' }}">
                            <i class="fas {{ $icon }}"></i>
                        </button>
                    </form>

                </div>
            </div>

            <div id="collapseItinerary{{ $itinerary->itinerary_id }}"
                 class="collapse {{ $expandItinerary ? 'show' : '' }}">
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
                            <div class="mb-3">
                                <label>Descripción</label>
                                <textarea name="description" class="form-control" rows="3">{{ $itinerary->description }}</textarea>
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

        @if ($openEditModal)
            <script>
                window.addEventListener('DOMContentLoaded', function () {
                    // Abrir modal
                    const modal = new bootstrap.Modal(document.getElementById('modalEditar{{ $itinerary->itinerary_id }}'));
                    modal.show();

                    // Expandir el collapse (por si no lo hizo Laravel con 'show')
                    const collapseTarget = document.getElementById('collapseItinerary{{ $itinerary->itinerary_id }}');
                    if (collapseTarget && !collapseTarget.classList.contains('show')) {
                        new bootstrap.Collapse(collapseTarget, { toggle: true });
                    }

                    // Scroll automático
                    const card = document.getElementById('card-itinerary-{{ $itinerary->itinerary_id }}');
                    if (card) {
                        card.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                });
            </script>
        @endif
    @endforeach

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
                    <div class="mb-3">
                        <label>Descripción</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Crear</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@stop

@push('js')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // ===== REAPERTURA DE MODALES Y ALERTAS =====

    // 1) Si hubo error al asignar ítems, reabrir modal y mostrar SweetAlert
    @if(session('showAssignModal'))
        const assignId = {{ session('showAssignModal') }};
        new bootstrap.Modal(
            document.getElementById(`modalAsignar${assignId}`)
        ).show();
        Swal.fire({
            icon: 'error',
            title: 'Error al asignar ítems',
            text: '{{ $errors->first() }}'
        });
    @endif

    // 2) Si hubo éxito general, mostrar SweetAlert
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: '{{ session("success") }}',
            timer: 2000,
            showConfirmButton: false
        });
    @endif

    // 3) Si hubo error general, mostrar SweetAlert
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: '{{ session("error") }}',
            timer: 2500,
            showConfirmButton: false
        });
    @endif

    // 4) Reabrir modal “Crear Itinerario” si corresponde
    @if(session('showCreateModal'))
        new bootstrap.Modal(
            document.getElementById('modalCrearItinerario')
        ).show();
    @endif

    // 5) Reabrir modal “Editar Itinerario” si corresponde
    @if(session('showEditModal'))
        new bootstrap.Modal(
            document.getElementById(`modalEditar{{ session('showEditModal') }}`)
        ).show();
    @endif

   
});
</script>
@endpush
