<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Seleccionar o Crear Itinerario</h3>
            </div>
            <div class="card-body">
                {{-- Selector de Itinerario Existente --}}
                <div class="form-group">
                    <label for="itinerary_id">Itinerario</label>
                    <select name="itinerary_id"
                            id="itinerary_id"
                            class="form-control @error('itinerary_id') is-invalid @enderror">
                        <option value="">-- Crear Nuevo --</option>
                        @foreach($itineraries ?? [] as $itinerary)
                            <option value="{{ $itinerary->itinerary_id }}"
                                    {{ old('itinerary_id', $tour->itinerary_id ?? '') == $itinerary->itinerary_id ? 'selected' : '' }}>
                                {{ $itinerary->name ?? "Itinerario #{$itinerary->itinerary_id}" }}
                            </option>
                        @endforeach
                    </select>
                    @error('itinerary_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Vista de Itinerario Existente --}}
                <div id="existing-itinerary-view" style="display: none;">
                    <div class="alert alert-info">
                        <strong>Descripción:</strong>
                        <p id="itinerary-description" class="mb-0 mt-2"></p>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Ítems del Itinerario</h4>
                        </div>
                        <div class="card-body p-0">
                            <ul id="itinerary-items-list" class="list-group list-group-flush">
                                <!-- Se llena dinámicamente -->
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Formulario para Crear Nuevo Itinerario --}}
                <div id="new-itinerary-form" style="display: none;">
                    {{-- NOMBRE DEL ITINERARIO --}}
                    <div class="form-group">
                        <label for="new_itinerary_name">Nombre del Itinerario <span class="text-danger">*</span></label>
                        <input type="text"
                               name="new_itinerary[name]"
                               id="new_itinerary_name"
                               class="form-control"
                               placeholder="Ej: Itinerario Estándar Volcán Arenal"
                               value="{{ old('new_itinerary.name') }}">
                        <small class="form-text text-muted">Nombre identificador para este itinerario</small>
                    </div>

                    <div class="form-group">
                        <label for="new_itinerary_description">Descripción del Itinerario</label>
                        <textarea name="new_itinerary[description]"
                                  id="new_itinerary_description"
                                  class="form-control"
                                  rows="3"
                                  placeholder="Descripción general del itinerario...">{{ old('new_itinerary.description') }}</textarea>
                    </div>

                    {{-- SELECCIONAR ITEMS EXISTENTES --}}
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-list"></i> Seleccionar Ítems Existentes
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning">
                                <i class="fas fa-hand-pointer"></i>
                                <strong>Instrucciones:</strong> Marca los ítems que quieres incluir y arrástralos para ordenarlos.
                            </div>

                            @php
                                $availableItems = \App\Models\ItineraryItem::where('is_active', true)
                                    ->orderBy('title')
                                    ->get();
                            @endphp

                            @if($availableItems->isNotEmpty())
                                <ul class="list-group sortable-items-new" id="sortable-new-itinerary">
                                    @foreach($availableItems as $item)
                                        <li class="list-group-item d-flex justify-content-between align-items-center"
                                            data-id="{{ $item->item_id }}">
                                            <div class="form-check">
                                                <input type="checkbox"
                                                       class="form-check-input checkbox-item-new"
                                                       value="{{ $item->item_id }}"
                                                       id="new-item-{{ $item->item_id }}"
                                                       {{ in_array($item->item_id, old('new_itinerary.existing_items', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="new-item-{{ $item->item_id }}">
                                                    <strong>{{ $item->title }}</strong>
                                                    @if($item->description)
                                                        <br><small class="text-muted">{{ Str::limit($item->description, 50) }}</small>
                                                    @endif
                                                </label>
                                            </div>
                                            <i class="fas fa-arrows-alt handle text-muted" style="cursor: move;" title="Arrastrar para ordenar"></i>
                                        </li>
                                    @endforeach
                                </ul>
                                <div id="ordered-new-itinerary-inputs"></div>
                            @else
                                <p class="text-muted">No hay ítems disponibles. <a href="{{ route('admin.tours.itinerary.index') }}" target="_blank">Crear ítems primero</a></p>
                            @endif
                        </div>
                    </div>

                    {{-- O CREAR ITEMS NUEVOS --}}
                    <div class="card mt-3">
                        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-plus-circle"></i> O Crear Nuevos Ítems
                            </h5>
                            <button type="button" class="btn btn-sm btn-light" id="add-itinerary-item">
                                <i class="fas fa-plus"></i> Agregar Ítem
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="itinerary-items-container">
                                <!-- Los ítems se agregan aquí dinámicamente -->
                                @if(old('new_itinerary.items'))
                                    @foreach(old('new_itinerary.items') as $index => $item)
                                        <div class="itinerary-item mb-3 p-3 border rounded">
                                            <div class="row">
                                                <div class="col-md-11">
                                                    <div class="form-group">
                                                        <label>Título</label>
                                                        <input type="text"
                                                               name="new_itinerary[items][{{ $index }}][title]"
                                                               class="form-control"
                                                               value="{{ $item['title'] ?? '' }}"
                                                               placeholder="Ej: Recogida en Hotel">
                                                    </div>
                                                    <div class="form-group mb-0">
                                                        <label>Descripción</label>
                                                        <textarea name="new_itinerary[items][{{ $index }}][description]"
                                                                  class="form-control"
                                                                  rows="2"
                                                                  placeholder="Descripción del ítem...">{{ $item['description'] ?? '' }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="col-md-1 d-flex align-items-center">
                                                    <button type="button" class="btn btn-danger btn-sm remove-itinerary-item">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>

                            @if(!old('new_itinerary.items'))
                                <div class="text-muted text-center py-3" id="empty-items-message">
                                    <i class="fas fa-info-circle"></i> Haz clic en "Agregar Ítem" para crear ítems nuevos
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle"></i> Ayuda
                </h3>
            </div>
            <div class="card-body">
                <h5>Opciones</h5>
                <ul>
                    <li><strong>Seleccionar existente:</strong> Usa un itinerario ya creado</li>
                    <li><strong>Crear nuevo:</strong> Define un itinerario único para este tour</li>
                </ul>
                <hr>
                <h5>Al crear nuevo puedes:</h5>
                <ul class="small">
                    <li><strong>Seleccionar ítems existentes:</strong> Marca los que quieras y ordénalos arrastrando</li>
                    <li><strong>Crear ítems nuevos:</strong> Define ítems únicos para este itinerario</li>
                    <li><strong>Combinar ambos:</strong> Puedes usar ítems existentes Y crear nuevos</li>
                </ul>
            </div>
        </div>

        @if($tour ?? false)
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">Itinerario Actual</h3>
                </div>
                <div class="card-body">
                    @if($tour->itinerary)
                        <h5>{{ $tour->itinerary->name ?? 'Sin nombre' }}</h5>
                        <p class="text-muted small">{{ $tour->itinerary->description }}</p>
                        @if($tour->itinerary->items->isNotEmpty())
                            <ol class="pl-3">
                                @foreach($tour->itinerary->items as $item)
                                    <li><strong>{{ $item->title }}</strong></li>
                                @endforeach
                            </ol>
                        @endif
                    @else
                        <p class="text-muted mb-0">Sin itinerario asignado</p>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Template para nuevos ítems (crear desde cero) --}}
<template id="itinerary-item-template">
    <div class="itinerary-item mb-3 p-3 border rounded">
        <div class="row">
            <div class="col-md-11">
                <div class="form-group">
                    <label>Título</label>
                    <input type="text"
                           name="new_itinerary[items][__INDEX__][title]"
                           class="form-control"
                           placeholder="Ej: Recogida en Hotel">
                </div>
                <div class="form-group mb-0">
                    <label>Descripción</label>
                    <textarea name="new_itinerary[items][__INDEX__][description]"
                              class="form-control"
                              rows="2"
                              placeholder="Descripción del ítem..."></textarea>
                </div>
            </div>
            <div class="col-md-1 d-flex align-items-center">
                <button type="button" class="btn btn-danger btn-sm remove-itinerary-item">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    </div>
</template>

@push('css')
<style>
.sortable-items-new { list-style: none; padding: 0; }
.sortable-items-new .handle { cursor: move; }
</style>
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const itinerarySelect = document.getElementById('itinerary_id');
    const existingView = document.getElementById('existing-itinerary-view');
    const newForm = document.getElementById('new-itinerary-form');
    const descriptionEl = document.getElementById('itinerary-description');
    const itemsList = document.getElementById('itinerary-items-list');

    // Datos de itinerarios
    const itineraryData = @json($itineraryJson ?? []);

    function updateItineraryView() {
        const selectedValue = itinerarySelect.value;

        if (!selectedValue) {
            // Crear nuevo
            existingView.style.display = 'none';
            newForm.style.display = 'block';
        } else {
            // Mostrar existente
            existingView.style.display = 'block';
            newForm.style.display = 'none';

            const data = itineraryData[selectedValue];
            if (data) {
                descriptionEl.textContent = data.description || 'Sin descripción';

                if (data.items && data.items.length > 0) {
                    itemsList.innerHTML = data.items.map(item => `
                        <li class="list-group-item">
                            <strong>${item.title || 'Sin título'}</strong>
                            <p class="mb-0 text-muted small">${item.description || ''}</p>
                        </li>
                    `).join('');
                } else {
                    itemsList.innerHTML = '<li class="list-group-item text-muted">No hay ítems en este itinerario</li>';
                }
            }
        }
    }

    if (itinerarySelect) {
        itinerarySelect.addEventListener('change', updateItineraryView);
        updateItineraryView();
    }

    // ===== Sortable para items existentes =====
    const sortableList = document.getElementById('sortable-new-itinerary');
    if (sortableList) {
        new Sortable(sortableList, {
            animation: 150,
            handle: '.handle'
        });
    }

    // ===== Al enviar formulario: construir orden de items seleccionados =====
    const tourForm = document.getElementById('tourForm');
    if (tourForm) {
        tourForm.addEventListener('submit', function(e) {
            // Solo si estamos creando nuevo itinerario
            if (itinerarySelect && itinerarySelect.value === '') {
                buildOrderedInputs();
            }
        });
    }

    function buildOrderedInputs() {
        const container = document.getElementById('ordered-new-itinerary-inputs');
        if (!container) return;

        container.innerHTML = '';
        let index = 0;

        const listItems = sortableList.querySelectorAll('li');
        listItems.forEach(li => {
            const checkbox = li.querySelector('.checkbox-item-new');
            if (checkbox && checkbox.checked) {
                const itemId = checkbox.value;
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `new_itinerary[existing_items][${index}]`;
                input.value = itemId;
                container.appendChild(input);
                index++;
            }
        });
    }

    // ===== Agregar nuevo ítem (crear desde cero) =====
    let itemIndex = {{ old('new_itinerary.items') ? count(old('new_itinerary.items')) : 0 }};

    document.getElementById('add-itinerary-item')?.addEventListener('click', function() {
        const template = document.getElementById('itinerary-item-template');
        const container = document.getElementById('itinerary-items-container');
        const emptyMessage = document.getElementById('empty-items-message');

        if (emptyMessage) emptyMessage.style.display = 'none';

        const clone = template.content.cloneNode(true);
        const html = clone.querySelector('.itinerary-item').outerHTML.replace(/__INDEX__/g, itemIndex);

        container.insertAdjacentHTML('beforeend', html);
        itemIndex++;
    });

    // Remover ítem
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-itinerary-item')) {
            e.target.closest('.itinerary-item').remove();

            // Mostrar mensaje si no hay items
            const container = document.getElementById('itinerary-items-container');
            const emptyMessage = document.getElementById('empty-items-message');
            if (container && container.children.length === 0 && emptyMessage) {
                emptyMessage.style.display = 'block';
            }
        }
    });
});
</script>
@endpush
