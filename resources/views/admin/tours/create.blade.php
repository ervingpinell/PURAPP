@php
    $itineraryJson = $itineraries->keyBy('itinerary_id')->map(function ($it) {
        return [
            'description' => $it->description,
            'items' => $it->items->map(function ($item) {
                return [
                    'title' => $item->title,
                    'description' => $item->description,
                ];
            })->toArray()
        ];
    });
@endphp

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
          <x-adminlte-input name="name" label="Nombre del Tour" value="{{ old('name') }}" required />

          {{-- Overview --}}
          <x-adminlte-textarea name="overview" label="Resumen (Overview)"  style="height:200px">{{ old('overview') }}</x-adminlte-textarea>

          {{-- Precios y duración --}}
          <div class="row mb-3">
            <div class="col-md-4">
              <x-adminlte-input name="adult_price" label="Precio Adulto" type="number" step="0.01" value="{{ old('adult_price') }}" />
            </div>
            <div class="col-md-4">
              <x-adminlte-input name="kid_price" label="Precio Niño" type="number" step="0.01" value="{{ old('kid_price') }}" />
            </div>
            <div class="col-md-4">
              <x-adminlte-input name="length" label="Duración (horas)" type="number" value="{{ old('length') }}" />
            </div>
          </div>

          {{-- Tipo de Tour --}}
          <x-adminlte-select name="tour_type_id" label="Tipo de Tour">
            <option value="">Seleccione tipo</option>
            @foreach($tourtypes as $type)
              <option value="{{ $type->tour_type_id }}" {{ old('tour_type_id') == $type->tour_type_id ? 'selected' : '' }}>{{ $type->name }}</option>
            @endforeach
          </x-adminlte-select>

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

          {{-- Amenidades NO incluidas --}}
<div class="mb-3">
    <label class="form-label text-danger">Amenidades NO incluidas</label><br>
    @foreach($amenities as $am)
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" name="excluded_amenities[]" value="{{ $am->amenity_id }}"
                {{ in_array($am->amenity_id, old('excluded_amenities', [])) ? 'checked' : '' }}>
            <label class="form-check-label">{{ $am->name }}</label>
        </div>
    @endforeach
</div>
          {{-- Horarios --}}
<x-adminlte-input name="schedule_am_start" label="Horario AM (Inicio)" type="text" placeholder="Ej: 08:00 o 8:00 AM" value="{{ old('schedule_am_start') }}" />
<x-adminlte-input name="schedule_am_end" label="Horario AM (Fin)" type="text" placeholder="Ej: 11:30 o 11:30 AM" value="{{ old('schedule_am_end') }}" />
<x-adminlte-input name="schedule_pm_start" label="Horario PM (Inicio)" type="text" placeholder="Ej: 13:30 o 1:30 PM" value="{{ old('schedule_pm_start') }}" />
<x-adminlte-input name="schedule_pm_end" label="Horario PM (Fin)" type="text" placeholder="Ej: 17:30 o 5:30 PM" value="{{ old('schedule_pm_end') }}" />

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

          {{-- Descripción e ítems dinámicos --}}
          <div id="selected-itinerary-description" class="mb-2 text-muted" style="white-space: pre-line; display: none;"></div>

          <div id="view-itinerary-items-create" class="mb-3" style="display: none;">
            <label class="form-label">Ítems del itinerario seleccionado:</label>
            <ul class="list-group"></ul>
          </div>

          {{-- Sección para nuevo itinerario --}}
          <div id="new-itinerary-section" style="display: none;">
            <x-adminlte-input name="new_itinerary_name" label="Nombre del nuevo itinerario" value="{{ old('new_itinerary_name') }}" />
            <x-adminlte-textarea name="new_itinerary_description" label="Descripción del nuevo itinerario" style="height:200px;">{{ old('new_itinerary_description')}}</x-adminlte-textarea>

            <label>Asignar Ítems Existentes</label>
            @foreach($availableItems as $item)
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="existing_item_ids[]" value="{{ $item->item_id }}" {{ in_array($item->item_id, old('existing_item_ids', [])) ? 'checked' : '' }}>
                <label class="form-check-label"><strong>{{ $item->title }}</strong>: {{ $item->description }}</label>
              </div>
            @endforeach

            <label class="form-label mt-3">Agregar Ítems Nuevos</label>
            <div id="new-itinerary-items" class="mb-2 itinerary-container">
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
