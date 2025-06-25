@php
    use Carbon\Carbon;
@endphp

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
            <x-adminlte-textarea name="overview" label="Resumen (Overview)" style="height:200px">{{ old('overview', $tour->overview) }}</x-adminlte-textarea>

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
            <x-adminlte-select name="tour_type_id" label="Tipo de Tour">
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

            {{-- Amenidades NO incluidas --}}
            <div class="mb-3">
              <label class="form-label text-danger">Amenidades NO incluidas</label>
              <div>
                @foreach($amenities as $am)
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="excluded_amenities[]" value="{{ $am->amenity_id }}"
                      id="edit_excl_am_{{ $tour->tour_id }}_{{ $am->amenity_id }}"
                      {{ in_array($am->amenity_id, old('excluded_amenities', $tour->excludedAmenities->pluck('amenity_id')->toArray())) ? 'checked' : '' }}>
                    <label class="form-check-label" for="edit_excl_am_{{ $tour->tour_id }}_{{ $am->amenity_id }}">{{ $am->name }}</label>
                  </div>
                @endforeach
              </div>
            </div>

            {{-- Horarios --}}
            @php
                $fmt = fn($t) => $t ? Carbon::parse($t)->format('g:i A') : '';
                $amStart = old('schedule_am_start') ?: $fmt(optional($tour->schedules->first())->start_time);
                $amEnd   = old('schedule_am_end')   ?: $fmt(optional($tour->schedules->first())->end_time);
                $pmStart = old('schedule_pm_start') ?: $fmt(optional($tour->schedules->skip(1)->first())->start_time);
                $pmEnd   = old('schedule_pm_end')   ?: $fmt(optional($tour->schedules->skip(1)->first())->end_time);
            @endphp

            <div class="row mb-3">
              <div class="col-md-6">
                <x-adminlte-input name="schedule_am_start" label="Horario AM (Inicio)" type="text" value="{{ $amStart }}" placeholder="Ej: 8:00 AM" />
              </div>
              <div class="col-md-6">
                <x-adminlte-input name="schedule_am_end" label="Horario AM (Fin)" type="text" value="{{ $amEnd }}" placeholder="Ej: 11:30 AM" />
              </div>
              <div class="col-md-6">
                <x-adminlte-input name="schedule_pm_start" label="Horario PM (Inicio)" type="text" value="{{ $pmStart }}" placeholder="Ej: 1:30 PM" />
              </div>
              <div class="col-md-6">
                <x-adminlte-input name="schedule_pm_end" label="Horario PM (Fin)" type="text" value="{{ $pmEnd }}" placeholder="Ej: 5:30 PM" />
              </div>
            </div>


            {{-- Itinerario --}}
            <div class="mb-3">
              <label class="form-label">Itinerario</label>
              <select name="itinerary_id" id="edit-itinerary-{{ $tour->tour_id }}" class="form-select">
                <option value="">-- Seleccione un itinerario --</option>
                @foreach($itineraries as $itin)
                  <option value="{{ $itin->itinerary_id }}"
                    {{ old('itinerary_id', $tour->itinerary_id) == $itin->itinerary_id ? 'selected' : '' }}>
                    {{ $itin->name }}
                  </option>
                @endforeach
                <option value="new" {{ old('itinerary_id') === 'new' ? 'selected' : '' }}>+ Crear nuevo itinerario</option>
              </select>
            </div>
{{-- Descripción dinámica --}}
<label class="form-label">Descripción:</label>

@php
  $itinerarioActual = $itineraries->firstWhere('itinerary_id', old('itinerary_id', $tour->itinerary_id));
@endphp

<div id="edit-itinerary-description-{{ $tour->tour_id }}"
     class="mb-2 border rounded p-2"
     style="white-space: pre-line; min-height: 80px; text-align: left; display: flex; align-items: flex-start;">
  {{ $itinerarioActual?->description ?: 'Sin descripción.' }}
</div>


            {{-- Lista de ítems --}}
            <div id="view-itinerary-items-{{ $tour->tour_id }}">
              <label class="form-label">Ítems del itinerario seleccionado:</label>
              <ul class="list-group">
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
  <a href="{{ route('admin.tours.itinerary.index', ['itinerary_id' => $itinerarioActual?->itinerary_id]) }}"
   class="btn btn-outline-info btn-sm mb-2" target="_blank">
   ✏️ Editar Itinerario
</a>

{{-- Sección para crear nuevo itinerario --}}
<div id="new-itinerary-section-{{ $tour->tour_id }}" style="display: {{ old('itinerary_id') === 'new' && session('showEditModal') == $tour->tour_id ? 'block' : 'none' }};">
  <x-adminlte-input name="new_itinerary_name" label="Nombre del nuevo itinerario"
    value="{{ old('new_itinerary_name') }}" />

  <x-adminlte-textarea name="new_itinerary_description" label="Descripción del nuevo itinerario"
    style="height:200px;">{{ old('new_itinerary_description') }}</x-adminlte-textarea>

  <label>Asignar Ítems Existentes</label>
  @foreach($availableItems as $item)
    <div class="form-check">
      <input class="form-check-input" type="checkbox" name="existing_item_ids[]" value="{{ $item->item_id }}"
        {{ in_array($item->item_id, old('existing_item_ids', [])) ? 'checked' : '' }}>
      <label class="form-check-label"><strong>{{ $item->title }}</strong>: {{ $item->description }}</label>
    </div>
  @endforeach

  <label class="form-label mt-3">Agregar Ítems Nuevos</label>
  <div id="new-itinerary-items-{{ $tour->tour_id }}" class="mb-2 itinerary-container">
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

  <button type="button" class="btn btn-outline-secondary btn-sm btn-add-itinerary" data-target="#new-itinerary-items-{{ $tour->tour_id }}">+ Añadir Ítem</button>
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
@push('js')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('select[id^="edit-itinerary-"]').forEach(select => {
      select.addEventListener('change', function () {
        const tourId = this.id.replace('edit-itinerary-', '');
        const newSection = document.getElementById(`new-itinerary-section-${tourId}`);
        newSection.style.display = this.value === 'new' ? 'block' : 'none';
      });
    });

    document.querySelectorAll('.btn-add-itinerary').forEach(btn => {
      btn.addEventListener('click', function () {
        const containerId = this.dataset.target;
        const container = document.querySelector(containerId);
        const index = container.querySelectorAll('.itinerary-item').length;
        const html = `
        <div class="row g-2 itinerary-item mb-2">
          <div class="col-md-5">
            <input type="text" name="itinerary[${index}][title]" class="form-control" placeholder="Título">
          </div>
          <div class="col-md-5">
            <input type="text" name="itinerary[${index}][description]" class="form-control" placeholder="Descripción">
          </div>
          <div class="col-md-2 text-end">
            <button type="button" class="btn btn-danger btn-sm btn-remove-itinerary">×</button>
          </div>
        </div>`;
        container.insertAdjacentHTML('beforeend', html);
      });
    });

    document.addEventListener('click', function (e) {
      if (e.target && e.target.classList.contains('btn-remove-itinerary')) {
        e.target.closest('.itinerary-item').remove();
      }
    });
  });
</script>
@endpush