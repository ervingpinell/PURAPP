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


          <div class="mb-3">
  <label for="color" class="form-label">Color del Tour</label>
  <input type="color" id="color" name="color" class="form-control form-control-color"
         value="{{ old('color', $tour->color ?? '#5cb85c') }}">
</div>

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

          {{-- Capacidad de Tour --}}
          <div class="mb-3">
            <label class="form-label">Cupo máximo</label>
            <input type="number"
                  name="max_capacity"
                  class="form-control"
                  value="{{ old('max_capacity', 12) }}"
                  min="1"
                  required>
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
          @php
              use Carbon\Carbon;
              $fmt = fn($t) => $t ? Carbon::parse($t)->format('g:i A') : '';
              $amStart = old('schedule_am_start') ? $fmt(old('schedule_am_start')) : '';
              $amEnd   = old('schedule_am_end')   ? $fmt(old('schedule_am_end'))   : '';
              $pmStart = old('schedule_pm_start') ? $fmt(old('schedule_pm_start')) : '';
              $pmEnd   = old('schedule_pm_end')   ? $fmt(old('schedule_pm_end'))   : '';
          @endphp

          {{-- Agregar horarios --}}
          <div id="schedules-container">
          <div class="row g-2 schedule-item mb-2">
            <div class="col-md-4">
              <input type="text" name="schedules[0][start_time]" class="form-control" placeholder="Inicio (Ej: 8:00 AM)">
            </div>
            <div class="col-md-4">
              <input type="text" name="schedules[0][end_time]" class="form-control" placeholder="Fin (Ej: 12:00 PM)">
            </div>
            <div class="col-md-1 text-end">
              <button type="button" class="btn btn-danger btn-sm btn-remove-schedule">×</button>
            </div>
          </div>
        </div>

        <button type="button" class="btn btn-outline-secondary btn-sm mt-2" id="add-schedule-btn">+ Añadir Horario</button>
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
@section('js')
<script>
  document.getElementById('add-schedule-btn').addEventListener('click', function() {
  const container = document.getElementById('schedules-container');
  const items = container.querySelectorAll('.schedule-item');
  const newIndex = items.length;

  const clone = items[0].cloneNode(true);
  clone.querySelectorAll('input').forEach(input => {
    input.value = '';
    if (input.name.includes('schedules')) {
      input.name = input.name.replace(/\[\d+\]/, `[${newIndex}]`);
    }
  });
  container.appendChild(clone);
});

document.addEventListener('click', function(e) {
  if (e.target.classList.contains('btn-remove-schedule')) {
    const container = document.getElementById('schedules-container');
    if (container.querySelectorAll('.schedule-item').length > 1) {
      e.target.closest('.schedule-item').remove();
    }
  }
});
</script>
@endsection