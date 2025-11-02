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
                                    data-description="{{ $itinerary->description }}"
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
                    <div class="form-group">
                        <label for="new_itinerary_description">Descripción del Itinerario</label>
                        <textarea name="new_itinerary[description]"
                                  id="new_itinerary_description"
                                  class="form-control"
                                  rows="3"
                                  placeholder="Descripción general del itinerario...">{{ old('new_itinerary.description') }}</textarea>
                    </div>

                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Ítems del Itinerario</h4>
                            <button type="button" class="btn btn-sm btn-success" id="add-itinerary-item">
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
                <h5>Estructura</h5>
                <p class="small">
                    Cada ítem tiene un <strong>título</strong> y una <strong>descripción</strong>
                    que explica qué sucede en esa parte del tour.
                </p>
                <p class="small mb-0">
                    Ejemplo: "Recogida en Hotel" → "Nuestro transporte lo recogerá en su hotel entre 7:00 - 7:30 AM"
                </p>
            </div>
        </div>
    </div>
</div>

{{-- Template para nuevos ítems --}}
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

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const itinerarySelect = document.getElementById('itinerary_id');
    const existingView = document.getElementById('existing-itinerary-view');
    const newForm = document.getElementById('new-itinerary-form');
    const descriptionEl = document.getElementById('itinerary-description');
    const itemsList = document.getElementById('itinerary-items-list');

    // Datos de itinerarios (pasados desde el controlador)
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

    // Event listener para cambio de select
    if (itinerarySelect) {
        itinerarySelect.addEventListener('change', updateItineraryView);
        updateItineraryView(); // Inicializar
    }

    // Agregar ítem de itinerario
    let itemIndex = {{ old('new_itinerary.items') ? count(old('new_itinerary.items')) : 0 }};

    document.getElementById('add-itinerary-item')?.addEventListener('click', function() {
        const template = document.getElementById('itinerary-item-template');
        const container = document.getElementById('itinerary-items-container');

        const clone = template.content.cloneNode(true);
        const html = clone.querySelector('.itinerary-item').outerHTML.replace(/__INDEX__/g, itemIndex);

        container.insertAdjacentHTML('beforeend', html);
        itemIndex++;
    });

    // Remover ítem
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-itinerary-item')) {
            e.target.closest('.itinerary-item').remove();
        }
    });
});
</script>
@endpush
