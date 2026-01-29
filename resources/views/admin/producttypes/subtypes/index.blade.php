@extends('adminlte::page')

@section('title', 'Subtipos de ' . $productType->getTranslatedName())

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1>
        <i class="fas fa-tags"></i> 
        Subtipos de {{ $productType->getTranslatedName() }}
    </h1>
    <a href="{{ route('admin.product-types.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
</div>
@stop

@section('content')
<div class="p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="text-muted mb-0">
            Gestiona los subtipos para clasificar productos de tipo "{{ $productType->getTranslatedName() }}"
        </p>
        
        @can('create-tour-types')
        <button class="btn btn-success" data-toggle="modal" data-target="#modalCreate">
            <i class="fas fa-plus"></i> Nuevo Subtipo
        </button>
        @endcan
    </div>

    @if($subtypes->isEmpty())
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        No hay subtipos configurados. Crea el primero para comenzar.
    </div>
    @else
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="bg-primary text-white">
                <tr>
                    <th width="50">#</th>
                    <th>Nombre</th>
                    <th>Slug</th>
                    {{-- <th width="80">Icono</th> --}}
                    <th width="100">Estado</th>
                    <th width="200">Acciones</th>
                </tr>
            </thead>
            <tbody id="subtypes-tbody">
                @foreach($subtypes as $subtype)
                <tr data-id="{{ $subtype->subtype_id }}">
                    <td class="text-center">
                        <i class="fas fa-grip-vertical handle" style="cursor: move;"></i>
                    </td>
                    <td>
                        <strong>{{ $subtype->getTranslatedName($currentLocale) }}</strong>
                        @if($subtype->description)
                        <br><small class="text-muted">{{ $subtype->description }}</small>
                        @endif
                        
                        {{-- Badges de idiomas --}}
                        @php
                        $availableLocales = array_keys($subtype->getTranslations('name'));
                        @endphp
                        @if(count($availableLocales) > 0)
                        <div class="mt-1">
                            @foreach(['es' => 'ES', 'en' => 'EN', 'fr' => 'FR', 'pt' => 'PT', 'de' => 'DE'] as $locale => $label)
                            @if(in_array($locale, $availableLocales))
                            <span class="badge badge-success badge-sm">{{ $label }}</span>
                            @endif
                            @endforeach
                        </div>
                        @endif
                    </td>
                    <td><code>{{ $subtype->slug }}</code></td>
                    {{-- <td class="text-center">
                        @if($subtype->icon)
                        <i class="{{ $subtype->icon }} fa-2x" 
                           @if($subtype->color) style="color: {{ $subtype->color }}" @endif></i>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td> --}}
                    <td>
                        @if($subtype->is_active)
                        <span class="badge bg-success">Activo</span>
                        @else
                        <span class="badge bg-secondary">Inactivo</span>
                        @endif
                    </td>
                    <td class="text-nowrap">
                        {{-- Editar --}}
                        @can('edit-tour-types')
                        <button class="btn btn-edit btn-sm me-1"
                                data-toggle="modal"
                                data-target="#modalEdit{{ $subtype->subtype_id }}"
                                title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>

                        {{-- Toggle --}}
                        <form action="{{ route('admin.product-types.subtypes.toggle', $subtype->subtype_id) }}"
                              method="POST"
                              class="d-inline me-1 js-confirm-toggle"
                              data-name="{{ $subtype->getTranslatedName() }}"
                              data-active="{{ $subtype->is_active ? 1 : 0 }}">
                            @csrf
                            @method('PUT')
                            <button type="submit"
                                    class="btn btn-sm {{ $subtype->is_active ? 'btn-toggle' : 'btn-secondary' }}"
                                    title="{{ $subtype->is_active ? 'Desactivar' : 'Activar' }}">
                                <i class="fas fa-toggle-{{ $subtype->is_active ? 'on' : 'off' }}"></i>
                            </button>
                        </form>
                        @endcan

                        {{-- Eliminar --}}
                        @can('delete-tour-types')
                        <form action="{{ route('admin.product-types.subtypes.destroy', $subtype->subtype_id) }}"
                              method="POST"
                              class="d-inline js-confirm-delete"
                              data-name="{{ $subtype->getTranslatedName() }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-delete btn-sm" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endcan
                    </td>
                </tr>

                {{-- Modal Editar --}}
                <div class="modal fade" id="modalEdit{{ $subtype->subtype_id }}" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <form action="{{ route('admin.product-types.subtypes.update', $subtype->subtype_id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Editar Subtipo</h5>
                                    <button type="button" class="close" data-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    {{-- Tabs de idiomas --}}
                                    @php $locales = ['es', 'en', 'fr', 'pt', 'de']; @endphp
                                    <ul class="nav nav-tabs mb-3" role="tablist">
                                        @foreach($locales as $i => $loc)
                                        <li class="nav-item">
                                            <button class="nav-link {{ $i===0 ? 'active' : '' }}"
                                                    id="edit-tab-{{ $subtype->subtype_id }}-{{ $loc }}"
                                                    data-toggle="tab"
                                                    data-target="#edit-pane-{{ $subtype->subtype_id }}-{{ $loc }}"
                                                    type="button">
                                                {{ strtoupper($loc) }}
                                            </button>
                                        </li>
                                        @endforeach
                                    </ul>

                                    <div class="tab-content">
                                        @foreach($locales as $i => $loc)
                                        <div class="tab-pane fade {{ $i===0 ? 'show active' : '' }}"
                                             id="edit-pane-{{ $subtype->subtype_id }}-{{ $loc }}">
                                            
                                            <div class="mb-3">
                                                <label>Nombre ({{ strtoupper($loc) }}) *</label>
                                                <input type="text"
                                                       name="translations[{{ $loc }}][name]"
                                                       class="form-control"
                                                       value="{{ $subtype->getTranslation('name', $loc, false) }}"
                                                       {{ $i===0 ? 'required' : '' }}>
                                            </div>

                                            <div class="mb-3">
                                                <label>Descripción ({{ strtoupper($loc) }})</label>
                                                <textarea name="translations[{{ $loc }}][description]"
                                                          class="form-control"
                                                          rows="3">{{ $subtype->getTranslation('description', $loc, false) }}</textarea>
                                            </div>

                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-success">Guardar</button>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

{{-- Modal Crear --}}
<div class="modal fade" id="modalCreate" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.product-types.subtypes.store', $productType) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nuevo Subtipo</h5>
                    <button type="button" class="close" data-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nombre *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Slug</label>
                        <input type="text" name="slug" class="form-control" placeholder="Se genera automáticamente">
                        <small class="text-muted">Dejar vacío para generar automáticamente</small>
                    </div>

                    <div class="mb-3">
                        <label>Descripción</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>

                    {{-- Campos opcionales ocultos por ahora --}}
                    <div class="row" style="display: none;">
                        <div class="col-md-6 mb-3">
                            <label>Icono (FontAwesome)</label>
                            <input type="text" name="icon" class="form-control" placeholder="fas fa-sun">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Color</label>
                            <input type="color" name="color" class="form-control" value="#000000">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Crear</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

{{-- Success/Error messages --}}
@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: '{{ session('success') }}',
        showConfirmButton: false,
        timer: 2000
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '{{ session('error') }}'
    });
</script>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sortable for drag & drop reordering
    const tbody = document.getElementById('subtypes-tbody');
    if (tbody) {
        new Sortable(tbody, {
            handle: '.handle',
            animation: 150,
            onEnd: function() {
                const order = Array.from(tbody.querySelectorAll('tr')).map(tr => tr.dataset.id);
                
                fetch('{{ route('admin.product-types.subtypes.reorder', $productType) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ order })
                });
            }
        });
    }

    // Confirm delete
    document.querySelectorAll('.js-confirm-delete').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const name = this.dataset.name || '';
            
            Swal.fire({
                title: '¿Eliminar subtipo?',
                text: `¿Estás seguro de eliminar "${name}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Eliminar',
                cancelButtonText: 'Cancelar'
            }).then(res => {
                if (res.isConfirmed) this.submit();
            });
        });
    });

    // Confirm toggle
    document.querySelectorAll('.js-confirm-toggle').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const name = this.dataset.name || '';
            const active = Number(this.dataset.active) === 1;
            
            Swal.fire({
                icon: 'question',
                title: active ? 'Desactivar' : 'Activar',
                text: `¿${active ? 'Desactivar' : 'Activar'} "${name}"?`,
                showCancelButton: true,
                confirmButtonColor: '#fd7e14',
                cancelButtonColor: '#6c757d',
                confirmButtonText: active ? 'Desactivar' : 'Activar',
                cancelButtonText: 'Cancelar'
            }).then(res => {
                if (res.isConfirmed) this.submit();
            });
        });
    });
});
</script>
@stop
