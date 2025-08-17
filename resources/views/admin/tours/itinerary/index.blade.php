@extends('adminlte::page')

@section('title', 'Itinerarios y sus Ítems')

@section('content_header')
    <h1>Itinerarios y Gestión de Ítems</h1>
@stop

@push('css')
<style>
    .sortable-items .handle { cursor: move; }

    .itinerary-collapse {
        overflow: hidden;
        transition: max-height 0.4s ease, opacity 0.4s ease;
        opacity: 1;
        max-height: 1000px;
    }
    .itinerary-collapse.collapsed { opacity: 0; max-height: 0; }

    .card-header { padding: 1rem; }
    .card.mb-3 { margin-bottom: 1.5rem !important; }

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
    .btn-action-group > .btn { margin-left: 0.5rem; }
    .btn-action-group > *:not(:first-child) { margin-left: 0.5rem; }

    .icon-toggle { color: #00bc8c; margin-right: 0.75rem; }
</style>
@endpush

@section('content')
@php
    $itineraryToEdit = request('itinerary_id');
@endphp

{{-- Mensajes (igual tenemos SweetAlert abajo) --}}
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
                    <a href="#" class="btn btn-sm btn-view"
                       data-bs-toggle="modal"
                       data-bs-target="#modalAsignar{{ $itinerary->itinerary_id }}">
                        Asignar
                    </a>

                    {{-- Editar --}}
                    <a href="#" class="btn btn-sm btn-edit"
                       data-bs-toggle="modal"
                       data-bs-target="#modalEditar{{ $itinerary->itinerary_id }}">
                        Editar
                    </a>

                    {{-- Alternar activo/inactivo --}}
                    @php
                        $active = $itinerary->is_active;
                        $btnClass = $active ? 'btn-delete' : 'btn-secondary';
                        $label   = $active ? 'Desactivar' : 'Activar';
                    @endphp
                    <form action="{{ route('admin.tours.itinerary.destroy', $itinerary->itinerary_id) }}"
                          method="POST"
                          class="d-inline form-toggle-itinerary"
                          data-name="{{ $itinerary->name }}"
                          data-active="{{ $active ? 1 : 0 }}">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm {{ $btnClass }}" type="submit">
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

        {{-- Modales --}}
        @include('admin.tours.itinerary.partials.create')
        @include('admin.tours.itinerary.partials.assign', ['items' => $items])
        @include('admin.tours.itinerary.partials.edit', ['itinerary' => $itinerary])
    @endforeach

    @include('admin.tours.itinerary.items.crud', ['items' => $items])
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // ===== Sortable =====
    document.querySelectorAll('.sortable-items').forEach(list => {
        new Sortable(list, { animation: 150, handle: '.handle' });
    });

    // ===== Toggle acordeón =====
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

    // ===== Ver más / Ver menos en descripciones =====
    document.querySelectorAll(".btn-toggle-desc").forEach(button => {
        button.addEventListener("click", function () {
            const targetId = this.getAttribute("data-target");
            const desc = document.getElementById(targetId);
            if (!desc) return;
            desc.classList.toggle("desc-expanded");
            this.textContent = desc.classList.contains("desc-expanded") ? "Ver menos" : "Ver más";
        });
    });

    // ===== Util: lock + spinner (no deshabilita inputs) =====
    function lockAndSubmit(form, loadingText = 'Procesando...') {
        if (!form.checkValidity()) {
            if (typeof form.reportValidity === 'function') form.reportValidity();
            return;
        }
        const buttons = form.querySelectorAll('button');
        const submitBtn = form.querySelector('button[type="submit"]') || buttons[0];

        buttons.forEach(btn => { if (!submitBtn || btn !== submitBtn) btn.disabled = true; });

        if (submitBtn) {
            if (!submitBtn.dataset.originalHtml) submitBtn.dataset.originalHtml = submitBtn.innerHTML;
            submitBtn.innerHTML =
                '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>' +
                loadingText;
            submitBtn.classList.add('disabled');
            submitBtn.disabled = true;
        }

        // Asegura que inputs no queden deshabilitados al enviar
        form.querySelectorAll('input,select,textarea').forEach(el => { if (el.disabled) el.disabled = false; });
        form.submit();
    }

    // ===== Alternar Itinerario (activar / desactivar) =====
    document.querySelectorAll('.form-toggle-itinerary').forEach(form => {
        form.addEventListener('submit', e => {
            e.preventDefault();
            const name = form.dataset.name || 'este itinerario';
            const isActive = form.dataset.active === '1';
            Swal.fire({
                title: isActive ? '¿Desactivar itinerario?' : '¿Activar itinerario?',
                html: isActive
                    ? 'El itinerario <b>'+name+'</b> quedará <b>inactivo</b>.'
                    : 'El itinerario <b>'+name+'</b> quedará <b>activo</b>.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, continuar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: isActive ? '#ffc107' : '#28a745',
                cancelButtonColor: '#6c757d'
            }).then(res => { if (res.isConfirmed) lockAndSubmit(form, isActive ? 'Desactivando...' : 'Activando...'); });
        });
    });

    // ===== Asignar Ítems (genera item_ids[ID]=orden, valida y confirma) =====
    document.querySelectorAll('form[data-itinerary-id].form-assign-items').forEach(form => {
        form.addEventListener('submit', e => {
            e.preventDefault();

            const itineraryId     = form.dataset.itineraryId;
            const ul              = document.getElementById(`sortable-${itineraryId}`);
            const hiddenContainer = document.getElementById(`ordered-inputs-${itineraryId}`);

            if (!ul || !hiddenContainer) {
                // fallback: enviar normal
                lockAndSubmit(form, 'Guardando...');
                return;
            }

            // Limpia y reconstruye los inputs hidden en orden visual
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

            // Elimina el dummy si hay seleccionados
            const dummy = form.querySelector('input[name="item_ids[dummy]"]');
            if (dummy && anySelected) dummy.remove();

            if (!anySelected) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Debes seleccionar al menos un ítem',
                    text: 'Por favor, selecciona al menos un ítem para continuar.'
                });
                return;
            }

            Swal.fire({
                title: '¿Asignar ítems seleccionados?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, asignar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#6c757d'
            }).then(res => { if (res.isConfirmed) lockAndSubmit(form, 'Asignando...'); });
        });
    });

    // ===== Reabrir modal de asignación si hubo error en validación =====
    @if (session('showAssignModal'))
        const mid = 'modalAsignar{{ session('showAssignModal') }}';
        const modalEl = document.getElementById(mid);
        if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const m = new bootstrap.Modal(modalEl);
            m.show();
        }
    @endif

    // ===== SweetAlert flash =====
    @if (session('success'))
      Swal.fire({ icon: 'success', title: 'Éxito', text: @json(session('success')), timer: 2200, showConfirmButton: false });
    @endif
    @if (session('error'))
      Swal.fire({ icon: 'error', title: 'Error', text: @json(session('error')), confirmButtonColor: '#d33' });
    @endif
    @if ($errors->any())
      Swal.fire({
        icon: 'error',
        title: 'No se pudo procesar',
        html: `<ul style="text-align:left;margin:0;padding-left:18px;">{!! collect($errors->all())->map(fn($e)=>"<li>".e($e)."</li>")->implode('') !!}</ul>`,
        confirmButtonColor: '#d33'
      });
    @endif
});
</script>
@endpush

@push('css')
<style>
    .desc-wrapper { max-width: 240px; min-width: 100px; word-wrap: break-word; }
    .desc-truncate {
        overflow: hidden; text-overflow: ellipsis; display: -webkit-box;
        -webkit-line-clamp: 1; -webkit-box-orient: vertical;
        max-height: 2em; transition: all 0.3s ease;
        word-break: break-word; white-space: normal;
    }
    .desc-expanded { -webkit-line-clamp: unset !important; max-height: none !important; }
    .btn-toggle-desc { font-size: 0.75rem; color: #007bff; background: none; border: none; padding: 0; cursor: pointer; }
    @media (max-width: 768px) {
        .desc-wrapper { max-width: 140px; }
        .desc-truncate { font-size: 0.8rem; }
        .btn-toggle-desc { font-size: 0.7rem; }
    }
</style>
@endpush

