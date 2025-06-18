{{-- resources/views/admin/tours/create.blade.php --}}
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
          {{-- Errores de validación --}}
          @if($errors->any())
            <div class="alert alert-danger">
              <ul class="mb-0">
                @foreach($errors->all() as $err)
                  <li>{{ $err }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          {{-- Nombre, Overview y Descripción --}}
          <div class="mb-3">
            <label class="form-label">Nombre del Tour</label>
            <input type="text"
                   name="name"
                   class="form-control"
                   value="{{ old('name') }}"
                   required>
          </div>
          <div class="mb-3">
            <label class="form-label">Resumen (Overview)</label>
            <textarea name="overview" class="form-control" rows="2">{{ old('overview') }}</textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
          </div>

          {{-- Precios y Duración --}}
          <div class="row mb-3">
            <div class="col-md-4">
              <label class="form-label">Precio Adulto</label>
              <input type="number" step="0.01"
                     name="adult_price"
                     class="form-control"
                     value="{{ old('adult_price') }}"
                     required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Precio Niño</label>
              <input type="number" step="0.01"
                     name="kid_price"
                     class="form-control"
                     value="{{ old('kid_price') }}">
            </div>
            <div class="col-md-4">
              <label class="form-label">Duración (horas)</label>
              <input type="number" step="1"
                     name="length"
                     class="form-control"
                     value="{{ old('length') }}"
                     required>
            </div>
          </div>

          {{-- Tipo de Tour --}}
          <div class="mb-3">
            <label class="form-label">Tipo de Tour</label>
            <select name="tour_type_id" class="form-select" required>
              <option value="">-- Seleccione un tipo --</option>
              @foreach($tourtypes as $t)
                <option value="{{ $t->tour_type_id }}"
                  {{ old('tour_type_id') == $t->tour_type_id ? 'selected' : '' }}>
                  {{ $t->name }}
                </option>
              @endforeach
            </select>
          </div>

          {{-- Idiomas Disponibles --}}
          <div class="mb-3">
            <label class="form-label">Idiomas Disponibles</label>
            <div>
              @foreach($languages as $lang)
                <div class="form-check form-check-inline">
                  <input class="form-check-input"
                         type="checkbox"
                         name="languages[]"
                         value="{{ $lang->tour_language_id }}"
                         id="lang_{{ $lang->tour_language_id }}"
                         {{ in_array($lang->tour_language_id, old('languages', [])) ? 'checked' : '' }}>
                  <label class="form-check-label" for="lang_{{ $lang->tour_language_id }}">
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
                         value="{{ $am->amenity_id }}"
                         id="amen_{{ $am->amenity_id }}"
                         {{ in_array($am->amenity_id, old('amenities', [])) ? 'checked' : '' }}>
                  <label class="form-check-label" for="amen_{{ $am->amenity_id }}">
                    {{ $am->name }}
                  </label>
                </div>
              @endforeach
            </div>
          </div>

          {{-- Horarios AM / PM --}}
          <div class="mb-3">
            <label class="form-label">Horario AM</label>
            <div class="row g-2">
              <div class="col-md-6">
                <input type="time"
                       name="schedule_am_start"
                       class="form-control"
                       value="{{ old('schedule_am_start') }}">
              </div>
              <div class="col-md-6">
                <input type="time"
                       name="schedule_am_end"
                       class="form-control"
                       value="{{ old('schedule_am_end') }}">
              </div>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Horario PM</label>
            <div class="row g-2">
              <div class="col-md-6">
                <input type="time"
                       name="schedule_pm_start"
                       class="form-control"
                       value="{{ old('schedule_pm_start') }}">
              </div>
              <div class="col-md-6">
                <input type="time"
                       name="schedule_pm_end"
                       class="form-control"
                       value="{{ old('schedule_pm_end') }}">
              </div>
            </div>
          </div>

          {{-- Itinerario existente o nuevo --}}
          <div class="mb-3">
            <label class="form-label">Itinerario</label>
            <select name="itinerary_id" id="select-itinerary" class="form-select" required>
              <option value="">-- Seleccione un itinerario --</option>
              @foreach($itineraries as $it)
                <option value="{{ $it->itinerary_id }}"
                  {{ old('itinerary_id') == $it->itinerary_id ? 'selected' : '' }}>
                  {{ $it->name }}
                </option>
              @endforeach
              <option value="new" {{ old('itinerary_id') == 'new' ? 'selected' : '' }}>
                + Crear nuevo itinerario
              </option>
            </select>
          </div>

          {{-- Campos para nuevo itinerario --}}
          <div id="new-itinerary-fields" class="mb-3" style="display: none;">
            <div class="mb-3">
              <label class="form-label">Nombre del nuevo itinerario</label>
              <input type="text"
                     name="new_itinerary_name"
                     class="form-control"
                     value="{{ old('new_itinerary_name') }}">
            </div>

            <label class="form-label">Ítems del itinerario</label>
            <div id="new-itinerary-items" class="itinerary-container mb-2">
              <div class="row g-2 itinerary-item">
                <div class="col-md-5">
                  <input type="text"
                         name="itinerary[0][title]"
                         class="form-control"
                         placeholder="Título"
                         required
                         value="{{ old('itinerary.0.title') }}">
                </div>
                <div class="col-md-5">
                  <input type="text"
                         name="itinerary[0][description]"
                         class="form-control"
                         placeholder="Descripción"
                         required
                         value="{{ old('itinerary.0.description') }}">
                </div>
                <div class="col-md-2 text-end">
                  <button type="button" class="btn btn-danger btn-sm btn-remove-itinerary">×</button>
                </div>
              </div>
            </div>
            <button
              type="button"
              class="btn btn-outline-secondary btn-sm btn-add-itinerary"
              data-target="#new-itinerary-items"
            >+ Añadir Ítem</button>
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

@push('js')
<script>
document.addEventListener('DOMContentLoaded', () => {
  // Mostrar/ocultar nuevo itinerario
  document.getElementById('select-itinerary').addEventListener('change', function(){
    document.getElementById('new-itinerary-fields')
      .style.display = (this.value === 'new') ? 'block' : 'none';
  });

  // Añadir/Quitar ítems dinámicamente
  let idx = 1;
  const container = document.getElementById('new-itinerary-items');
  document.querySelector('.btn-add-itinerary').addEventListener('click', () => {
    const row = document.createElement('div');
    row.className = 'row g-2 itinerary-item mb-2';
    row.innerHTML = `
      <div class="col-md-5">
        <input type="text" name="itinerary[${idx}][title]" class="form-control" placeholder="Título" required>
      </div>
      <div class="col-md-5">
        <input type="text" name="itinerary[${idx}][description]" class="form-control" placeholder="Descripción" required>
      </div>
      <div class="col-md-2 text-end">
        <button type="button" class="btn btn-danger btn-sm btn-remove-itinerary">×</button>
      </div>`;
    container.append(row);
    idx++;
  });
  container.addEventListener('click', e => {
    if (e.target.classList.contains('btn-remove-itinerary')) {
      e.target.closest('.itinerary-item').remove();
    }
  });
});
</script>
@endpush
