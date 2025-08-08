@extends('adminlte::page')

@section('title', 'Itinerarios y sus Ítems')

@section('content_header')
    <h1>Itinerarios y Gestión de Ítems</h1>
@stop

@push('css')
<style>
    .sortable-items .handle {
        cursor: move;
    }

    .itinerary-collapse {
        overflow: hidden;
        transition: max-height 0.4s ease, opacity 0.4s ease;
        opacity: 1;
        max-height: 1000px;
    }

    .itinerary-collapse.collapsed {
        opacity: 0;
        max-height: 0;
    }

    .card-header {
        padding: 1rem;
    }

    .card.mb-3 {
        margin-bottom: 1.5rem !important;
    }

    .card-header .btn {
        min-width: 100px;
        text-align: center;
        font-weight: 500;
    }

    .itinerary-title {
        max-width: 60vw;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
/* Espaciado entre botones */
.btn-action-group > .btn {
    margin-left: 0.5rem;
}
.btn-action-group > *:not(:first-child) {
    margin-left: 0.5rem;
}


    .icon-toggle {
        color: #00bc8c;
        margin-right: 0.75rem;
    }
</style>
@endpush

@section('content')
@php
    $itineraryToEdit = request('itinerary_id');
@endphp
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div class="p-3">
    <a href="#" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalCrearItinerario">
        <i class="fas fa-plus"></i> Nuevo Itinerario
    </a>

    @foreach($itineraries as $itinerary)
        @php
            $openEditModal = $itineraryToEdit && $itineraryToEdit == $itinerary->itinerary_id;
            $expandItinerary = $openEditModal;
        @endphp

        <div class="card mb-3" id="card-itinerary-{{ $itinerary->itinerary_id }}">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                <div class="d-flex align-items-center flex-grow-1">
                    <button type="button"
                            class="btn btn-link toggle-itinerary-btn text-start text-decoration-none d-flex align-items-center"
                            data-target="collapseItinerary{{ $itinerary->itinerary_id }}">
                        <i class="fas icon-toggle {{ $expandItinerary ? 'fa-minus' : 'fa-plus' }}"></i>
                        <h5 class="mb-0 text-light itinerary-title">{{ $itinerary->name }}</h5>
                    </button>
                </div>

<div class="d-flex align-items-center btn-action-group ms-auto mt-2 mt-md-0">

    {{-- Asignar Ítems --}}
    <a href="#" class="btn btn-sm btn-success"
       data-bs-toggle="modal"
       data-bs-target="#modalAsignar{{ $itinerary->itinerary_id }}">
        Asignar
    </a>

    {{-- Editar --}}
    <a href="#" class="btn btn-sm btn-warning"
       data-bs-toggle="modal"
       data-bs-target="#modalEditar{{ $itinerary->itinerary_id }}">
        Editar
    </a>

    {{-- Activar / Desactivar --}}
    <form action="{{ route('admin.tours.itinerary.destroy', $itinerary->itinerary_id) }}"
          method="POST" style="display:inline;">
        @csrf
        @method('DELETE')
        @php
            $active = $itinerary->is_active;
            $btnClass = $active ? 'btn-danger' : 'btn-success';
            $text    = $active ? '¿Desactivar este itinerario?' : '¿Activar este itinerario?';
            $label   = $active ? 'Desactivar' : 'Activar';
        @endphp
        <button class="btn btn-sm {{ $btnClass }}" onclick="return confirm('{{ $text }}')">
            {{ $label }}
        </button>
    </form>

</div>

            </div>

            <div id="collapseItinerary{{ $itinerary->itinerary_id }}"
                 class="itinerary-collapse {{ $expandItinerary ? '' : 'collapsed' }}">
                <div class="card-body">
                    @if (!empty($itinerary->description))
                        <div class="mb-2">
                            <h6 class="text-muted">{!! nl2br(e($itinerary->description)) !!}</h6>
                        </div>
                    @endif

                    @if ($itinerary->items->isEmpty())
                        <p class="text-warning">No hay ítems asignados a este itinerario.</p>
                    @else
                        <ul class="list-group">
                            @foreach ($itinerary->items->sortBy('order') as $item)
                                <li class="list-group-item">
                                    <strong>{{ $item->title }}</strong><br>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
@include('admin.tours.itinerary.partials.create-modal')

        @include('admin.tours.itinerary.partials.assign-modal', ['items' => $items, 'itineraries' => $itineraries])
        @include('admin.tours.itinerary.partials.edit-modal', ['itineraries' => $itineraries])
    @endforeach

    @include('admin.tours.itinerary.items.crud', ['items' => $items])
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Sortable
    document.querySelectorAll('.sortable-items').forEach(list => {
        new Sortable(list, {
            animation: 150,
            handle: '.handle',
        });
    });

    // Validar selección de ítems
    document.querySelectorAll('form[data-itinerary-id]').forEach(form => {
        form.addEventListener('submit', e => {
            const itineraryId = form.dataset.itineraryId;
            const ul = document.getElementById(`sortable-${itineraryId}`);
            const hiddenContainer = document.getElementById(`ordered-inputs-${itineraryId}`);
            hiddenContainer.innerHTML = '';

            let index = 0;
            let anySelected = false;

            ul.querySelectorAll('li').forEach(li => {
                const id = li.dataset.id;
                const checkbox = li.querySelector('.checkbox-assign');
                if (checkbox && checkbox.checked) {
                    anySelected = true;
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = `item_ids[${id}]`;
                    input.value = index++;
                    hiddenContainer.appendChild(input);
                }
            });

            const dummy = form.querySelector('input[name="item_ids[dummy]"]');
            if (dummy && anySelected) dummy.remove();

            if (!anySelected) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Debes seleccionar al menos un ítem',
                    text: 'Por favor, selecciona al menos un ítem para continuar.'
                });
            }
        });
    });

    // Toggle itinerarios
    document.querySelectorAll('.toggle-itinerary-btn').forEach(button => {
        button.addEventListener('click', e => {
            e.preventDefault();

            const targetId = button.dataset.target;
            const content = document.getElementById(targetId);
            const icon = button.querySelector('.icon-toggle');

            if (!content || !icon) return;

            content.classList.toggle('collapsed');
            icon.classList.toggle('fa-plus');
            icon.classList.toggle('fa-minus');
        });
    });
});
</script>
@endpush
