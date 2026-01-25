@extends('adminlte::page')

@section('title', __('m_tours.itinerary.ui.page_title'))

@section('content_header')
<h1>{{ __('m_tours.itinerary.ui.page_heading') }}</h1>
@stop

@section('content')
@php
$itineraryToEdit = request('itinerary_id');
@endphp

<div class="row mb-3">
    <div class="col-12">
        <!-- Tabs de navegación -->
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" href="{{ route('admin.tours.itinerary.index') }}">
                    <i class="fas fa-list"></i> {{ __('m_tours.common.active') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.tours.itinerary.trash') }}">
                    <i class="fas fa-trash"></i> {{ __('m_tours.itinerary.ui.trash_title') }}
                </a>
            </li>
        </ul>
    </div>
</div>


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

<div class="card mb-3">
    <div class="card-body">
         <div class="row align-items-center">
            <div class="col-md-6 mb-2 mb-md-0">
                <form action="{{ route('admin.tours.itinerary.index') }}" method="GET" class="d-flex">
                    <input type="text" name="search" class="form-control me-2" placeholder="{{ __('m_tours.common.search') }}..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-search"></i>
                    </button>
                    @if(request('search'))
                        <a href="{{ route('admin.tours.itinerary.index') }}" class="btn btn-outline-secondary ms-2" title="{{ __('m_tours.common.clear_search') }}">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </form>
            </div>
            <div class="col-md-6 text-md-end">
                @can('create-itineraries')
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrearItinerario">
                    <i class="fas fa-plus"></i> {{ __('m_tours.itinerary.ui.new_itinerary') }}
                </button>
                @endcan
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered dt-responsive nowrap w-100" id="datatable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>{{ __('m_tours.itinerary.table.name') }}</th>
                        <th>{{ __('m_tours.itinerary.ui.description_label') }}</th>
                        <th>{{ __('m_tours.common.status') }}</th>
                        <th>{{ __('m_tours.common.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($itineraries as $itinerary)
                    @php
                    $active = (bool) $itinerary->is_active;
                    $btnClass = $active ? 'btn-warning' : 'btn-secondary';
                    $label = $active ? __('m_tours.itinerary.ui.toggle_off') : __('m_tours.itinerary.ui.toggle_on');
                    @endphp
                    <tr>
                        <td>{{ $itinerary->itinerary_id }}</td>
                        <td>
                            <strong>{{ $itinerary->name }}</strong>
                        </td>
                        <td>
                            <small>{{ Str::limit(strip_tags($itinerary->description), 50) }}</small>
                        </td>
                        <td>
                            @if ($active)
                            <span class="badge bg-success">{{ __('m_tours.common.active') }}</span>
                            @else
                            <span class="badge bg-secondary">{{ __('m_tours.common.inactive') }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center btn-action-group">
                                {{-- Asignar Ítems --}}
                                @can('edit-itineraries')
                                <a href="#" class="btn btn-sm btn-info"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalAsignar{{ $itinerary->itinerary_id }}"
                                    title="{{ __('m_tours.itinerary.ui.assign') }}">
                                    <i class="fas fa-link"></i>
                                </a>
                                @endcan

                                {{-- Editar --}}
                                @can('edit-itineraries')
                                <a href="#" class="btn btn-sm btn-success"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEditar{{ $itinerary->itinerary_id }}"
                                    title="{{ __('m_tours.itinerary.ui.edit') }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan

                                {{-- Alternar activo/inactivo (PATCH a toggle) --}}
                                @can('publish-itineraries')
                                <form action="{{ route('admin.tours.itinerary.toggle', $itinerary) }}"
                                    method="POST"
                                    class="d-inline form-toggle-itinerary"
                                    data-name="{{ $itinerary->name }}"
                                    data-active="{{ $active ? 1 : 0 }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn btn-sm {{ $btnClass }}" type="submit" title="{{ $label }}">
                                        <i class="fas {{ $active ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                    </button>
                                </form>
                                @endcan
                                
                                {{-- Delete (Soft Delete) --}}
                                @can('delete-itineraries')
                                <form action="{{ route('admin.tours.itinerary.destroy', $itinerary->itinerary_id) }}" 
                                      method="POST" 
                                      class="d-inline form-delete-itinerary"
                                      data-name="{{ $itinerary->name }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="{{ __('m_tours.common.delete') }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    
                    {{-- Modales por itinerario (incluidos dentro del loop para mantener IDs unicos) --}}
                    @include('admin.tours.itinerary.partials.assign', ['items' => $items, 'itinerary' => $itinerary])
                    @include('admin.tours.itinerary.partials.edit', ['itinerary' => $itinerary])
                    
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal global: crear itinerario --}}
@include('admin.tours.itinerary.partials.create')

@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // ===== Sortable =====
        document.querySelectorAll('.sortable-items').forEach(list => {
            new Sortable(list, {
                animation: 150,
                handle: '.handle'
            });
        });

        // ===== Util: lock + spinner (no deshabilita inputs) =====
        function lockAndSubmit(form, loadingText = @json(__('m_tours.itinerary.ui.processing'))) {
            if (!form.checkValidity()) {
                if (typeof form.reportValidity === 'function') form.reportValidity();
                return;
            }
            const buttons = form.querySelectorAll('button');
            const submitBtn = form.querySelector('button[type="submit"]') || buttons[0];

            buttons.forEach(btn => {
                if (!submitBtn || btn !== submitBtn) btn.disabled = true;
            });

            if (submitBtn) {
                if (!submitBtn.dataset.originalHtml) submitBtn.dataset.originalHtml = submitBtn.innerHTML;
                submitBtn.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>' +
                    loadingText;
                submitBtn.classList.add('disabled');
                submitBtn.disabled = true;
            }

            form.querySelectorAll('input,select,textarea').forEach(el => {
                if (el.disabled) el.disabled = false;
            });
            form.submit();
        }

        // ===== Alternar Itinerario (activar / desactivar) =====
        document.querySelectorAll('.form-toggle-itinerary').forEach(form => {
            form.addEventListener('submit', e => {
                e.preventDefault();
                const name = form.dataset.name || @json(__('m_tours.itinerary.ui.itinerary_this'));
                const isActive = form.dataset.active === '1';
                Swal.fire({
                    title: isActive ?
                        @json(__('m_tours.itinerary.ui.toggle_confirm_off_title')) : @json(__('m_tours.itinerary.ui.toggle_confirm_on_title')),
                    html: (isActive ?
                        @json(__('m_tours.itinerary.ui.toggle_confirm_off_html', ['label' => ':label'])) :
                        @json(__('m_tours.itinerary.ui.toggle_confirm_on_html', ['label' => ':label']))
                    ).replace(':label', name),
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: @json(__('m_tours.itinerary.ui.yes_continue')),
                    cancelButtonText: @json(__('m_tours.itinerary.ui.cancel')),
                    confirmButtonColor: isActive ? '#ffc107' : '#28a745',
                    cancelButtonColor: '#6c757d'
                }).then(res => {
                    if (res.isConfirmed) {
                        lockAndSubmit(form, isActive ?
                            @json(__('m_tours.itinerary.ui.deactivating')) :
                            @json(__('m_tours.itinerary.ui.activating'))
                        );
                    }
                });
            });
        });

        // ===== Eliminar Itinerario (Soft Delete) =====
        document.querySelectorAll('.form-delete-itinerary').forEach(form => {
            form.addEventListener('submit', e => {
                e.preventDefault();
                const name = form.dataset.name || @json(__('m_tours.itinerary.ui.itinerary_this'));
                Swal.fire({
                    title: @json(__('m_tours.common.confirm_delete')),
                    text: name,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: @json(__('m_tours.common.yes')),
                    cancelButtonText: @json(__('m_tours.common.cancel')),
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d'
                }).then(res => {
                    if (res.isConfirmed) {
                        lockAndSubmit(form, @json(__('m_tours.common.delete')));
                    }
                });
            });
        });

        // ===== Asignar Ítems: generar inputs hidden con orden =====
        document.querySelectorAll('.form-assign-items').forEach(form => {
            const itineraryId = form.dataset.itineraryId;
            const sortableList = document.getElementById(`sortable-${itineraryId}`);
            const outputContainer = document.getElementById(`ordered-inputs-${itineraryId}`);

            function updateOrderedInputs() {
                // Limpiar inputs previos
                outputContainer.innerHTML = '';

                // Obtener todos los items en orden
                const listItems = Array.from(sortableList.querySelectorAll('li'));
                let order = 1;

                listItems.forEach(li => {
                    const checkbox = li.querySelector('.checkbox-assign');
                    if (checkbox && checkbox.checked) {
                        const itemId = checkbox.value;
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = `items[${itemId}]`;
                        input.value = order;
                        outputContainer.appendChild(input);
                        order++;
                    }
                });
            }

            // Actualizar cuando cambian los checkboxes
            sortableList.addEventListener('change', e => {
                if (e.target.classList.contains('checkbox-assign')) {
                    updateOrderedInputs();
                }
            });

            // Actualizar cuando se reordena (después del drag)
            if (window.Sortable) {
                sortableList.addEventListener('sortupdate', updateOrderedInputs);
            }

            // Inicializar al cargar el modal
            const modal = form.closest('.modal');
            if (modal) {
                modal.addEventListener('shown.bs.modal', updateOrderedInputs);
            }

            // Actualizar antes de submit
            form.addEventListener('submit', e => {
                updateOrderedInputs();
                // Remover el dummy input
                const dummyInput = form.querySelector('input[name="item_ids[dummy]"]');
                if (dummyInput) dummyInput.remove();
            });
        });
        
        // Inicializar DataTables si es necesario
        // $('#datatable').DataTable();
    });
</script>
<style>
    .btn-action-group > * {
        margin-right: 5px;
    }
    .btn-action-group > *:last-child {
        margin-right: 0;
    }
</style>
@endpush
