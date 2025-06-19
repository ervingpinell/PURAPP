@foreach($tours as $tour)
  <div class="modal fade" id="modalEditar{{ $tour->tour_id }}" tabindex="-1" aria-labelledby="modalEditarLabel{{ $tour->tour_id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-warning text-white">
          <h5 class="modal-title" id="modalEditarLabel{{ $tour->tour_id }}">Editar Tour #{{ $tour->tour_id }}</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <form action="{{ route('admin.tours.update', $tour->tour_id) }}" method="POST">
          @csrf
          @method('PUT')

          <div class="modal-body">
            {{-- Validaciones --}}
            @if($errors->any() && session('showEditModal') == $tour->tour_id)
              <div class="alert alert-danger">
                <ul class="mb-0">
                  @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif

            {{-- Nombre, resumen --}}
            <x-adminlte-input name="name" label="Nombre del Tour" value="{{ old('name', $tour->name) }}" required />
            <x-adminlte-textarea name="overview" label="Resumen (Overview)">{{ old('overview', $tour->overview) }}</x-adminlte-textarea>

            {{-- Precios y duración --}}
            <div class="row mb-3">
              <div class="col-md-4">
                <x-adminlte-input name="adult_price" label="Precio Adulto" type="number" step="0.01" value="{{ old('adult_price', $tour->adult_price) }}" required />
              </div>
              <div class="col-md-4">
                <x-adminlte-input name="kid_price" label="Precio Niño" type="number" step="0.01" value="{{ old('kid_price', $tour->kid_price) }}" />
              </div>
              <div class="col-md-4">
                <x-adminlte-input name="length" label="Duración (horas)" type="number" value="{{ old('length', $tour->length) }}" required />
              </div>
            </div>

            {{-- Tipo --}}
            <x-adminlte-select name="tour_type_id" label="Tipo de Tour" required>
              <option value="">-- Seleccione un tipo --</option>
              @foreach($tourtypes as $type)
                <option value="{{ $type->tour_type_id }}" {{ old('tour_type_id', $tour->tour_type_id) == $type->tour_type_id ? 'selected' : '' }}>
                  {{ $type->name }}
                </option>
              @endforeach
            </x-adminlte-select>

            {{-- Idiomas --}}
            <div class="mb-3">
              <label class="form-label">Idiomas Disponibles</label>
              <div>
                @foreach($languages as $lang)
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="languages[]" value="{{ $lang->tour_language_id }}"
                      id="edit_lang_{{ $tour->tour_id }}_{{ $lang->tour_language_id }}"
                      {{ in_array($lang->tour_language_id, old('languages', $tour->languages->pluck('tour_language_id')->toArray())) ? 'checked' : '' }}>
                    <label class="form-check-label" for="edit_lang_{{ $tour->tour_id }}_{{ $lang->tour_language_id }}">{{ $lang->name }}</label>
                  </div>
                @endforeach
              </div>
            </div>

            {{-- Amenidades --}}
            <div class="mb-3">
              <label class="form-label">Amenidades</label>
              <div>
                @foreach($amenities as $am)
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="amenities[]" value="{{ $am->amenity_id }}"
                      id="edit_am_{{ $tour->tour_id }}_{{ $am->amenity_id }}"
                      {{ in_array($am->amenity_id, old('amenities', $tour->amenities->pluck('amenity_id')->toArray())) ? 'checked' : '' }}>
                    <label class="form-check-label" for="edit_am_{{ $tour->tour_id }}_{{ $am->amenity_id }}">{{ $am->name }}</label>
                  </div>
                @endforeach
              </div>
            </div>

            {{-- Horarios --}}
            <div class="row mb-3">
              <div class="col-md-6">
                <x-adminlte-input name="schedule_am_start" label="Horario AM (Inicio)" type="time" value="{{ old('schedule_am_start', optional($tour->schedules->first())->start_time) }}" />
              </div>
              <div class="col-md-6">
                <x-adminlte-input name="schedule_am_end" label="Horario AM (Fin)" type="time" value="{{ old('schedule_am_end', optional($tour->schedules->first())->end_time) }}" />
              </div>
              <div class="col-md-6">
                <x-adminlte-input name="schedule_pm_start" label="Horario PM (Inicio)" type="time" value="{{ old('schedule_pm_start', optional($tour->schedules->skip(1)->first())->start_time) }}" />
              </div>
              <div class="col-md-6">
                <x-adminlte-input name="schedule_pm_end" label="Horario PM (Fin)" type="time" value="{{ old('schedule_pm_end', optional($tour->schedules->skip(1)->first())->end_time) }}" />
              </div>
            </div>

            {{-- Itinerario --}}
            <div class="mb-3">
              <label class="form-label">Itinerario</label>
              <select name="itinerary_id" id="edit-itinerary-{{ $tour->tour_id }}" class="form-select">
                <option value="">-- Seleccione un itinerario --</option>
                @foreach($itineraries as $itin)
                  <option value="{{ $itin->itinerary_id }}" {{ old('itinerary_id', $tour->itinerary_id) == $itin->itinerary_id ? 'selected' : '' }}>
                    {{ $itin->name }}
                  </option>
                @endforeach
                <option value="new" {{ old('itinerary_id') == 'new' ? 'selected' : '' }}>+ Crear nuevo itinerario</option>
              </select>
            </div>

            {{-- Ítems del itinerario existente (solo lectura, dinámico) --}}
<div id="view-itinerary-items-{{ $tour->tour_id }}" class="mb-3" style="display: {{ old('itinerary_id', $tour->itinerary_id) !== 'new' ? 'block' : 'none' }}">
  <label class="form-label">Ítems del itinerario seleccionado:</label>
  <ul class="list-group">
    @php
      $itinerarioActual = $itineraries->firstWhere('itinerary_id', old('itinerary_id', $tour->itinerary_id));
    @endphp
    @if($itinerarioActual && $itinerarioActual->items->count())
      @foreach($itinerarioActual->items->sortBy('pivot.item_order') as $item)
        <li class="list-group-item">
          <strong>{{ $item->title }}</strong><br>
          <small class="text-muted">{{ $item->description }}</small>
        </li>
      @endforeach
    @else
      <li class="list-group-item text-muted">Este itinerario no contiene ítems.</li>
    @endif
  </ul>
</div>
            {{-- Bloque nuevo itinerario (editable) --}}
            <div id="new-itinerary-section-{{ $tour->tour_id }}" style="display: {{ old('itinerary_id') === 'new' ? 'block' : 'none' }}">
              <div class="mb-3">
                <label>Nombre del nuevo itinerario</label>
                <input type="text" name="new_itinerary_name" class="form-control" value="{{ old('new_itinerary_name') }}">
              </div>

              <label class="form-label">Asignar Ítems Existentes</label>
              @foreach ($availableItems as $item)
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="existing_item_ids[]" value="{{ $item->item_id }}"
                    id="edit_item_{{ $tour->tour_id }}_{{ $item->item_id }}"
                    {{ in_array($item->item_id, old('existing_item_ids', [])) ? 'checked' : '' }}>
                  <label class="form-check-label" for="edit_item_{{ $tour->tour_id }}_{{ $item->item_id }}">
                    <strong>{{ $item->title }}</strong>: {{ $item->description }}
                  </label>
                </div>
              @endforeach

              {{-- Aquí puedes incluir el template para nuevos ítems si lo usas --}}
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-warning">Actualizar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  
@endforeach

