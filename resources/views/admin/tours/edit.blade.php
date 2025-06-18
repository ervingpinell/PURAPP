{{-- resources/views/admin/tours/edit.blade.php --}}
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
            {{-- Errores de validación --}}
            @if($errors->any())
              <div class="alert alert-danger">
                <ul class="mb-0">
                  @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif

            {{-- Nombre --}}
            <div class="mb-3">
              <label class="form-label">Nombre del Tour</label>
              <input type="text"
                     name="name"
                     class="form-control"
                     value="{{ old('name', $tour->name) }}"
                     required>
            </div>

            {{-- Overview --}}
            <div class="mb-3">
              <label class="form-label">Resumen (Overview)</label>
              <textarea name="overview" class="form-control" rows="2">{{ old('overview', $tour->overview) }}</textarea>
            </div>

            {{-- Descripción --}}
            <div class="mb-3">
              <label class="form-label">Descripción</label>
              <textarea name="description" class="form-control" rows="3">{{ old('description', $tour->description) }}</textarea>
            </div>

            {{-- Precios y duración --}}
            <div class="row mb-3">
              <div class="col-md-4">
                <label class="form-label">Precio Adulto</label>
                <input type="number" step="0.01"
                       name="adult_price"
                       class="form-control"
                       value="{{ old('adult_price', $tour->adult_price) }}"
                       required>
              </div>
              <div class="col-md-4">
                <label class="form-label">Precio Niño</label>
                <input type="number" step="0.01"
                       name="kid_price"
                       class="form-control"
                       value="{{ old('kid_price', $tour->kid_price) }}">
              </div>
              <div class="col-md-4">
                <label class="form-label">Duración (horas)</label>
                <input type="number" step="1"
                       name="length"
                       class="form-control"
                       value="{{ old('length', $tour->length) }}"
                       required>
              </div>
            </div>

            {{-- Tipo de Tour --}}
            <div class="mb-3">
              <label class="form-label">Tipo de Tour</label>
              <select name="tour_type_id" class="form-select" required>
                <option value="">-- Seleccione un tipo --</option>
                @foreach($tourtypes as $type)
                  <option value="{{ $type->tour_type_id }}"
                    {{ old('tour_type_id', $tour->tour_type_id) == $type->tour_type_id ? 'selected' : '' }}>
                    {{ $type->name }}
                  </option>
                @endforeach
              </select>
            </div>

            {{-- Idiomas --}}
            <div class="mb-3">
              <label class="form-label">Idiomas Disponibles</label>
              <div>
                @foreach($languages as $lang)
                  <div class="form-check form-check-inline">
                    <input class="form-check-input"
                           type="checkbox"
                           name="languages[]"
                           id="edit_lang_{{ $tour->tour_id }}_{{ $lang->tour_language_id }}"
                           value="{{ $lang->tour_language_id }}"
                           {{ in_array($lang->tour_language_id, old('languages', $tour->languages->pluck('tour_language_id')->toArray())) ? 'checked' : '' }}>
                    <label class="form-check-label"
                           for="edit_lang_{{ $tour->tour_id }}_{{ $lang->tour_language_id }}">
                      {{ $lang->name }}
                    </label>
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
                    <input class="form-check-input"
                           type="checkbox"
                           name="amenities[]"
                           id="edit_am_{{ $tour->tour_id }}_{{ $am->amenity_id }}"
                           value="{{ $am->amenity_id }}"
                           {{ in_array($am->amenity_id, old('amenities', $tour->amenities->pluck('amenity_id')->toArray())) ? 'checked' : '' }}>
                    <label class="form-check-label"
                           for="edit_am_{{ $tour->tour_id }}_{{ $am->amenity_id }}">
                      {{ $am->name }}
                    </label>
                  </div>
                @endforeach
              </div>
            </div>

            {{-- Horarios AM --}}
            <div class="mb-3">
              <label class="form-label">Horario AM</label>
              <div class="row g-2">
                <div class="col-md-6">
                  <input type="time"
                         name="schedule_am_start"
                         class="form-control"
                         value="{{ old('schedule_am_start',
                                    optional($tour->schedules->first())->start_time
                                      ? \Carbon\Carbon::parse($tour->schedules->first()->start_time)->format('H:i')
                                      : ''
                                  ) }}">
                </div>
                <div class="col-md-6">
                  <input type="time"
                         name="schedule_am_end"
                         class="form-control"
                         value="{{ old('schedule_am_end',
                                    optional($tour->schedules->first())->end_time
                                      ? \Carbon\Carbon::parse($tour->schedules->first()->end_time)->format('H:i')
                                      : ''
                                  ) }}">
                </div>
              </div>
            </div>

            {{-- Horarios PM --}}
            <div class="mb-3">
              <label class="form-label">Horario PM</label>
              <div class="row g-2">
                <div class="col-md-6">
                  <input type="time"
                         name="schedule_pm_start"
                         class="form-control"
                         value="{{ old('schedule_pm_start',
                                    optional($tour->schedules->skip(1)->first())->start_time
                                      ? \Carbon\Carbon::parse($tour->schedules->skip(1)->first()->start_time)->format('H:i')
                                      : ''
                                  ) }}">
                </div>
                <div class="col-md-6">
                  <input type="time"
                         name="schedule_pm_end"
                         class="form-control"
                         value="{{ old('schedule_pm_end',
                                    optional($tour->schedules->skip(1)->first())->end_time
                                      ? \Carbon\Carbon::parse($tour->schedules->skip(1)->first()->end_time)->format('H:i')
                                      : ''
                                  ) }}">
                </div>
              </div>
            </div>

            {{-- Itinerario existente o nuevo --}}
            <div class="mb-3">
              <label class="form-label">Itinerario</label>
              <select name="itinerary_id"
                      id="edit-itinerary-{{ $tour->tour_id }}"
                      class="form-select">
                <option value="">-- Seleccione un itinerario --</option>
                @foreach($itineraries as $itin)
                  <option value="{{ $itin->itinerary_id }}"
                    {{ old('itinerary_id', $tour->itinerary_id) == $itin->itinerary_id ? 'selected' : '' }}>
                    {{ $itin->name }}
                  </option>
                @endforeach
                <option value="new" {{ old('itinerary_id') == 'new' ? 'selected' : '' }}>
                  + Crear nuevo itinerario
                </option>
              </select>
            </div>

            {{-- Campos para crear nuevo itinerario --}}
            <div id="new-itinerary-fields-{{ $tour->tour_id }}"
                 class="mb-3"
                 style="display: {{ old('itinerary_id', $tour->itinerary_id) === 'new' ? 'block' : 'none' }};">
              <div class="mb-3">
                <label class="form-label">Nombre del nuevo itinerario</label>
                <input type="text"
                       name="new_itinerary_name"
                       class="form-control"
                       value="{{ old('new_itinerary_name') }}">
              </div>

              <label class="form-label">Ítems del itinerario</label>
              <div id="itinerary-{{ $tour->tour_id }}">
                @php
                  $items = old('itinerary',
                            $tour->itinerary?->items
                              ->sortBy('order')
                              ->values()
                              ->map(fn($i)=>[
                                'title'=>$i->title,
                                'description'=>$i->description
                              ])
                              ->toArray()
                            ?? []);
                @endphp
                @foreach($items as $i => $item)
                  <div class="row g-2 mb-2 itinerary-item">
                    <div class="col-md-4">
                      <input type="text"
                             name="itinerary[{{ $i }}][title]"
                             class="form-control"
                             placeholder="Título"
                             value="{{ $item['title'] }}"
                             required>
                    </div>
                    <div class="col-md-6">
                      <input type="text"
                             name="itinerary[{{ $i }}][description]"
                             class="form-control"
                             placeholder="Descripción"
                             value="{{ $item['description'] }}"
                             required>
                    </div>
                    <div class="col-md-2 text-end">
                      <button type="button" class="btn btn-danger btn-sm btn-remove-itinerary">×</button>
                    </div>
                  </div>
                @endforeach
              </div>
              <button type="button"
                      class="btn btn-outline-secondary btn-sm btn-add-itinerary"
                      data-target="#itinerary-{{ $tour->tour_id }}">
                + Añadir Ítem
              </button>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">Cancelar</button>
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
  @foreach($tours as $tour)
    (function(){
      const sel    = document.getElementById('edit-itinerary-{{ $tour->tour_id }}');
      const box    = document.getElementById('new-itinerary-fields-{{ $tour->tour_id }}');
      const cont   = document.getElementById('itinerary-{{ $tour->tour_id }}');
      let   idx    = cont.children.length;

      sel?.addEventListener('change', ()=> {
        box.style.display = sel.value === 'new' ? 'block' : 'none';
      });

      cont.addEventListener('click', e => {
        if(e.target.matches('.btn-remove-itinerary')) {
          e.target.closest('.itinerary-item').remove();
        }
      });

      sel.closest('.modal-dialog')
         .querySelector('.btn-add-itinerary')
         .addEventListener('click', ()=> {
           const row = document.createElement('div');
           row.className = 'row g-2 mb-2 itinerary-item';
           row.innerHTML = `
             <div class="col-md-4">
               <input type="text" name="itinerary[${idx}][title]" class="form-control" placeholder="Título" required>
             </div>
             <div class="col-md-6">
               <input type="text" name="itinerary[${idx}][description]" class="form-control" placeholder="Descripción" required>
             </div>
             <div class="col-md-2 text-end">
               <button type="button" class="btn btn-danger btn-sm btn-remove-itinerary">×</button>
             </div>`;
           cont.append(row);
           idx++;
         });
    })();
  @endforeach
});
</script>
@endpush
