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

          {{-- Nombre y resumen --}}
          <x-adminlte-input name="name" label="Nombre del Tour" value="{{ old('name', $tour->name) }}" required />

          <div class="mb-3">
  <label for="color" class="form-label">Color del Tour</label>
  <input type="color" id="color" name="color" class="form-control form-control-color"
         value="{{ old('color', $tour->color ?? '#5cb85c') }}">
</div>

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

                    {{-- Capacidad --}}
          <div class="mb-3">
            <label class="form-label">Cupo máximo</label>
            <input type="number" name="max_capacity" class="form-control" value="{{ old('max_capacity', $tour->max_capacity) }}" min="1" required>
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
          @endphp

          <div id="schedules-container-{{ $tour->tour_id }}">
            @foreach(old('schedules', $tour->schedules) as $i => $schedule)
              <div class="row g-2 schedule-item mb-2">
                <div class="col-md-4">
                  <input type="text" name="schedules[{{ $i }}][start_time]" class="form-control"
                    placeholder="Inicio (Ej: 8:00 AM)"
                    value="{{ is_array($schedule) ? $fmt($schedule['start_time']) : $fmt($schedule->start_time) }}">
                </div>
                <div class="col-md-4">
                  <input type="text" name="schedules[{{ $i }}][end_time]" class="form-control"
                    placeholder="Fin (Ej: 12:00 PM)"
                    value="{{ is_array($schedule) ? $fmt($schedule['end_time']) : $fmt($schedule->end_time) }}">
                </div>
                <div class="col-md-1 text-end">
                  <button type="button" class="btn btn-danger btn-sm btn-remove-schedule">×</button>
                </div>
              </div>
            @endforeach
          </div>

          <div id="schedule-template-{{ $tour->tour_id }}" class="d-none">
            <div class="row g-2 schedule-item mb-2">
              <div class="col-md-4">
                <input type="text" name="__START__" class="form-control" placeholder="Inicio (Ej: 8:00 AM)">
              </div>
              <div class="col-md-4">
                <input type="text" name="__END__" class="form-control" placeholder="Fin (Ej: 12:00 PM)">
              </div>
              <div class="col-md-1 text-end">
                <button type="button" class="btn btn-danger btn-sm btn-remove-schedule">×</button>
              </div>
            </div>
          </div>

          <button type="button" class="btn btn-outline-secondary btn-sm mt-2 add-schedule-btn" data-tour="{{ $tour->tour_id }}">
            + Añadir Horario
          </button>

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

          <label class="form-label">Descripción:</label>
          @php
            $itinerarioActual = $itineraries->firstWhere('itinerary_id', old('itinerary_id', $tour->itinerary_id));
          @endphp
          <div id="edit-itinerary-description-{{ $tour->tour_id }}" class="mb-2 border rounded p-2" style="white-space: pre-line; min-height: 80px;">
            {{ $itinerarioActual?->description ?: 'Sin descripción.' }}
          </div>

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

          <a href="{{ route('admin.tours.itinerary.index', ['itinerary_id' => $itinerarioActual?->itinerary_id]) }}" class="btn btn-outline-info btn-sm mb-2" target="_blank">
            ✏️ Editar Itinerario
          </a>

          {{-- Nuevo itinerario --}}
          <div id="new-itinerary-section-{{ $tour->tour_id }}" style="display: {{ old('itinerary_id') === 'new' && session('showEditModal') == $tour->tour_id ? 'block' : 'none' }};">
            <x-adminlte-input name="new_itinerary_name" label="Nombre del nuevo itinerario" value="{{ old('new_itinerary_name') }}" />
            <x-adminlte-textarea name="new_itinerary_description" label="Descripción del nuevo itinerario" style="height:200px;">{{ old('new_itinerary_description') }}</x-adminlte-textarea>

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

    document.querySelectorAll('.add-schedule-btn').forEach(btn => {
      btn.addEventListener('click', function () {
        const tourId = this.dataset.tour;
        const container = document.getElementById(`schedules-container-${tourId}`);
        const template = document.getElementById(`schedule-template-${tourId}`);
        const index = container.querySelectorAll('.schedule-item').length;

        if (!template) return;

        let html = template.innerHTML;
        html = html.replace(/__START__/g, `schedules[${index}][start_time]`)
                    .replace(/__END__/g, `schedules[${index}][end_time]`);

        container.insertAdjacentHTML('beforeend', html);
      });
    });

    document.addEventListener('click', function (e) {
      if (e.target.classList.contains('btn-remove-schedule')) {
        const container = e.target.closest('.modal').querySelector('[id^="schedules-container-"]');
        if (container.querySelectorAll('.schedule-item').length > 1) {
          e.target.closest('.schedule-item').remove();
        }
      }
      if (e.target.classList.contains('btn-remove-itinerary')) {
        e.target.closest('.itinerary-item').remove();
      }
    });

  });
</script>
@endpush
