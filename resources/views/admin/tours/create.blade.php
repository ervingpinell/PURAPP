{{-- Modal Registrar Tour --}}
<div class="modal fade" id="modalRegistrar" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Registrar Tour</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <form id="formCrearTour" action="{{ route('admin.tours.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          {{-- Nombre --}}
          <div class="mb-3">
            <label class="form-label">Nombre del Tour</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
          </div>

          {{-- Overview --}}
          <div class="mb-3">
            <label class="form-label">Resumen (Overview)</label>
            <textarea name="overview" class="form-control">{{ old('overview') }}</textarea>
          </div>

          {{-- Descripción --}}
          <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea name="description" class="form-control">{{ old('description') }}</textarea>
          </div>

          {{-- Precios y duración --}}
          <div class="row mb-3">
            <div class="col-md-4">
              <label>Precio Adulto</label>
              <input type="number" step="0.01" name="adult_price" class="form-control" value="{{ old('adult_price') }}">
            </div>
            <div class="col-md-4">
              <label>Precio Niño</label>
              <input type="number" step="0.01" name="kid_price" class="form-control" value="{{ old('kid_price') }}">
            </div>
            <div class="col-md-4">
              <label>Duración (horas)</label>
              <input type="number" step="1" name="length" class="form-control" value="{{ old('length') }}">
            </div>
          </div>

          {{-- Tipo de Tour --}}
          <div class="mb-3">
            <label>Tipo de Tour</label>
            <select name="tour_type_id" class="form-select">
              <option value="">Seleccione tipo</option>
              @foreach($tourtypes as $type)
                <option value="{{ $type->tour_type_id }}" {{ old('tour_type_id') == $type->tour_type_id ? 'selected' : '' }}>{{ $type->name }}</option>
              @endforeach
            </select>
          </div>

          {{-- Idiomas --}}
          <div class="mb-3">
            <label>Idiomas Disponibles</label><br>
            @foreach($languages as $lang)
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="languages[]" value="{{ $lang->tour_language_id }}" {{ in_array($lang->tour_language_id, old('languages', [])) ? 'checked' : '' }}>
                <label class="form-check-label">{{ $lang->name }}</label>
              </div>
            @endforeach
          </div>

          {{-- Amenidades --}}
          <div class="mb-3">
            <label>Amenidades</label><br>
            @foreach($amenities as $am)
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="amenities[]" value="{{ $am->amenity_id }}" {{ in_array($am->amenity_id, old('amenities', [])) ? 'checked' : '' }}>
                <label class="form-check-label">{{ $am->name }}</label>
              </div>
            @endforeach
          </div>

          {{-- Horarios --}}
          <div class="mb-3">
            <label>Horario AM</label>
            <input type="time" name="schedule_am_start" class="form-control mb-2" value="{{ old('schedule_am_start') }}">
            <input type="time" name="schedule_am_end" class="form-control" value="{{ old('schedule_am_end') }}">
          </div>
          <div class="mb-3">
            <label>Horario PM</label>
            <input type="time" name="schedule_pm_start" class="form-control mb-2" value="{{ old('schedule_pm_start') }}">
            <input type="time" name="schedule_pm_end" class="form-control" value="{{ old('schedule_pm_end') }}">
          </div>

          {{-- Itinerario --}}
          <div class="mb-3">
            <label>Itinerario</label>
            <select name="itinerary_id" id="select-itinerary" class="form-select">
              <option value="">-- Seleccione --</option>
              @foreach($itineraries as $it)
                <option value="{{ $it->itinerary_id }}" {{ old('itinerary_id') == $it->itinerary_id ? 'selected' : '' }}>{{ $it->name }}</option>
              @endforeach
              <option value="new" {{ old('itinerary_id') === 'new' ? 'selected' : '' }}>+ Crear nuevo itinerario</option>
            </select>
          </div>

          <div id="new-itinerary-section" style="display: none;">
            <div class="mb-3">
              <label>Nombre del nuevo itinerario</label>
              <input type="text" name="new_itinerary_name" class="form-control" value="{{ old('new_itinerary_name') }}">
            </div>

            <div class="mb-3">
              <label>Asignar Ítems Existentes</label>
              @foreach($availableItems as $item)
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="existing_item_ids[]" value="{{ $item->item_id }}" {{ in_array($item->item_id, old('existing_item_ids', [])) ? 'checked' : '' }}>
                  <label class="form-check-label"><strong>{{ $item->title }}</strong>: {{ $item->description }}</label>
                </div>
              @endforeach
            </div>

            <label class="form-label">Agregar Ítems Nuevos</label>
            <div id="new-itinerary-items" class="mb-2 itinerary-container">
              {{-- Plantilla por defecto visible si hay error --}}
              @php $oldItems = old('itinerary', []) @endphp
              @forelse($oldItems as $i => $item)
                <div class="row g-2 itinerary-item mb-2">
                  <div class="col-md-5">
                    <input type="text" name="itinerary[{{ $i }}][title]" class="form-control" placeholder="Título" value="{{ $item['title'] }}">
                  </div>
                  <div class="col-md-5">
                    <input type="text" name="itinerary[{{ $i }}][description]" class="form-control" placeholder="Descripción" value="{{ $item['description'] }}">
                  </div>
                  <div class="col-md-2 text-end">
                    <button type="button" class="btn btn-danger btn-sm btn-remove-itinerary">×</button>
                  </div>
                </div>
              @empty
                <div class="row g-2 itinerary-item mb-2">
                  <div class="col-md-5">
                    <input type="text" name="itinerary[0][title]" class="form-control" placeholder="Título">
                  </div>
                  <div class="col-md-5">
                    <input type="text" name="itinerary[0][description]" class="form-control" placeholder="Descripción">
                  </div>
                  <div class="col-md-2 text-end">
                    <button type="button" class="btn btn-danger btn-sm btn-remove-itinerary">×</button>
                  </div>
                </div>
              @endforelse
            </div>

            <button type="button" class="btn btn-outline-secondary btn-sm btn-add-itinerary" data-target="#new-itinerary-items">+ Añadir Ítem</button>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar Tour</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Template oculto para nuevos ítems --}}
<template id="itinerary-template">
  <div class="row g-2 itinerary-item mb-2">
    <div class="col-md-5">
      <input type="text" name="__NAME__" class="form-control" placeholder="Título">
    </div>
    <div class="col-md-5">
      <input type="text" name="__DESC__" class="form-control" placeholder="Descripción">
    </div>
    <div class="col-md-2 text-end">
      <button type="button" class="btn btn-danger btn-sm btn-remove-itinerary">×</button>
    </div>
  </div>
</template>
