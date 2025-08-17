{{-- resources/views/admin/tours/create.blade.php --}}

@php
    // útil para mostrar preview de itinerario
    $itineraryJson = $itineraries->keyBy('itinerary_id')->map(function ($it) {
        return [
            'description' => $it->description,
            'items' => $it->items->map(fn($item) => [
                'title' => $item->title,
                'description' => $item->description,
            ])->toArray()
        ];
    });
@endphp

@if ($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach ($errors->all() as $err)
        <li>{{ $err }}</li>
      @endforeach
    </ul>
  </div>
@endif

<div class="modal fade" id="modalRegistrar" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Registrar Tour</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <form id="formCrearTour" action="{{ route('admin.tours.store') }}" method="POST">
        @csrf
        <div class="modal-body">

          <div class="row g-4">
            <div class="col-lg-7">
              {{-- Datos principales --}}
              <x-adminlte-input name="name" label="Nombre del Tour" value="{{ old('name') }}" required />

              <div class="row">
                <div class="col-md-4">
                  <label class="form-label">Color del Tour</label>
                  <input type="color" name="color" class="form-control form-control-color"
                         value="{{ old('color', '#5cb85c') }}">
                </div>
                <div class="col-md-4">
                  <x-adminlte-input name="viator_code" label="Código Viator (opcional)" value="{{ old('viator_code') }}" />
                </div>
                <div class="col-md-4">
                  <x-adminlte-input name="length" label="Duración (horas)" type="number" step="0.1"
                                    value="{{ old('length') }}" required />
                </div>
              </div>

              <x-adminlte-textarea name="overview" label="Resumen (Overview)" style="height:180px">{{ old('overview') }}</x-adminlte-textarea>

              <div class="row">
                <div class="col-md-4">
                  <x-adminlte-input name="adult_price" label="Precio Adulto" type="number" step="0.01" value="{{ old('adult_price') }}" required />
                </div>
                <div class="col-md-4">
                  <x-adminlte-input name="kid_price" label="Precio Niño" type="number" step="0.01" value="{{ old('kid_price') }}" />
                </div>
                <div class="col-md-4">
                  <label class="form-label">Cupo por defecto</label>
                  <input type="number" name="max_capacity" class="form-control" value="{{ old('max_capacity', 12) }}" min="1" required>
                </div>
              </div>

              <x-adminlte-select name="tour_type_id" label="Tipo de Tour" required>
                <option value="">Seleccione tipo</option>
                @foreach($tourtypes as $type)
                  <option value="{{ $type->tour_type_id }}" @selected(old('tour_type_id') == $type->tour_type_id)>
                    {{ $type->name }}
                  </option>
                @endforeach
              </x-adminlte-select>

              <div class="mb-3">
                <label>Idiomas Disponibles</label><br>
                @foreach($languages as $lang)
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="languages[]" value="{{ $lang->tour_language_id }}"
                           @checked(in_array($lang->tour_language_id, old('languages', [])))>
                    <label class="form-check-label">{{ $lang->name }}</label>
                  </div>
                @endforeach
              </div>

              <div class="mb-3">
                <label>Amenidades</label><br>
                @foreach($amenities as $am)
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="amenities[]" value="{{ $am->amenity_id }}"
                           @checked(in_array($am->amenity_id, old('amenities', [])))>
                    <label class="form-check-label">{{ $am->name }}</label>
                  </div>
                @endforeach
              </div>

              <div class="mb-3">
                <label class="form-label text-danger">Amenidades NO incluidas</label><br>
                @foreach($amenities as $am)
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="excluded_amenities[]" value="{{ $am->amenity_id }}"
                           @checked(in_array($am->amenity_id, old('excluded_amenities', [])))>
                    <label class="form-check-label">{{ $am->name }}</label>
                  </div>
                @endforeach
              </div>
            </div>

            <div class="col-lg-5">
              {{-- Itinerario (solo asignar) --}}
              <div class="card mb-3 shadow-sm">
                <div class="card-header bg-light fw-bold">Itinerario</div>
                <div class="card-body">
                  <select name="itinerary_id" id="select-itinerary" class="form-select" required>
                    <option value="">-- Seleccione --</option>
                    @foreach($itineraries as $it)
                      <option value="{{ $it->itinerary_id }}" @selected(old('itinerary_id') == $it->itinerary_id)>
                        {{ $it->name }}
                      </option>
                    @endforeach
                  </select>

                  <div id="itinerary-preview" class="mt-3" style="display:none;">
                    <div id="selected-itinerary-description" class="small text-muted mb-2" style="white-space: pre-line;"></div>
                    <ul class="list-group small" id="itinerary-items-list"></ul>
                  </div>
                </div>
              </div>

              {{-- Horarios --}}
              <div class="card shadow-sm">
                <div class="card-header bg-light fw-bold d-flex align-items-center justify-content-between">
                  <span>Horarios del Tour</span>
                </div>
                <div class="card-body">
                  <div class="mb-3">
                    <label class="form-label">Usar horarios existentes</label>
                    <select name="schedules_existing[]" class="form-select" multiple size="6">
                      @foreach($allSchedules as $sc)
                        @php
                          $start = \Carbon\Carbon::parse($sc->start_time)->format('H:i');
                          $end   = \Carbon\Carbon::parse($sc->end_time)->format('H:i');
                          $lbl   = $sc->label ? " - {$sc->label}" : '';
                        @endphp
                        <option value="{{ $sc->schedule_id }}">
                          {{ $start }} - {{ $end }}{{ $lbl }} (cap: {{ $sc->max_capacity ?? '—' }})
                        </option>
                      @endforeach
                    </select>
                    <div class="form-text">Mantén CTRL/CMD para seleccionar varios.</div>
                  </div>

                  <hr>

                  <label class="form-label">Crear horarios nuevos</label>
                  <div id="schedules-new-container">
                    <div class="row g-2 schedule-new mb-2">
                      <div class="col-4">
                        <input type="text" name="schedules_new[0][start_time]" class="form-control" placeholder="Inicio (8:00 AM)">
                      </div>
                      <div class="col-4">
                        <input type="text" name="schedules_new[0][end_time]" class="form-control" placeholder="Fin (12:00 PM)">
                      </div>
                      <div class="col-4">
                        <input type="text" name="schedules_new[0][label]" class="form-control" placeholder="Etiqueta (opcional)">
                      </div>
                      <div class="col-6 mt-2">
                        <input type="number" name="schedules_new[0][max_capacity]" class="form-control"
                               placeholder="Cupo (vacío = default del tour)">
                      </div>
                      <div class="col-6 mt-2 text-end">
                        <button type="button" class="btn btn-outline-danger btn-sm btn-remove-sched">Eliminar</button>
                      </div>
                    </div>
                  </div>

                  <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-add-sched">+ Añadir horario</button>
                </div>
              </div>

              {{-- Eliminar (dejado preparado) --}}
              {{--
              <form action="{{ route('admin.tours.destroy', $tour->tour_id) }}" method="POST" onsubmit="return confirm('¿Eliminar tour?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger mt-3">Eliminar</button>
              </form>
              --}}
            </div>
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
document.addEventListener('DOMContentLoaded', function () {
  // ==== Preview de itinerario seleccionado ====
  const itineraryData = @json($itineraryJson);
  const selIt = document.getElementById('select-itinerary');
  const prevBox = document.getElementById('itinerary-preview');
  const descBox = document.getElementById('selected-itinerary-description');
  const itemsUl = document.getElementById('itinerary-items-list');

  function refreshItineraryPreview(){
    const id = selIt.value;
    if (!id || !itineraryData[id]) {
      prevBox.style.display = 'none';
      return;
    }
    const data = itineraryData[id];
    descBox.textContent = data.description || '';
    itemsUl.innerHTML = data.items.length
      ? data.items.map(i => `<li class="list-group-item d-flex justify-content-between">
          <strong>${i.title}</strong><span class="text-muted ms-2">${i.description ?? ''}</span>
        </li>`).join('')
      : '<li class="list-group-item text-muted">Este itinerario no contiene ítems.</li>';
    prevBox.style.display = 'block';
  }
  if (selIt) {
    selIt.addEventListener('change', refreshItineraryPreview);
    refreshItineraryPreview();
  }

  // ==== Horarios nuevos: añadir/eliminar filas ====
  const container = document.getElementById('schedules-new-container');
  const addBtn = document.getElementById('btn-add-sched');

  addBtn?.addEventListener('click', () => {
    const idx = container.querySelectorAll('.schedule-new').length;
    const row = document.createElement('div');
    row.className = 'row g-2 schedule-new mb-2';
    row.innerHTML = `
      <div class="col-4">
        <input type="text" name="schedules_new[${idx}][start_time]" class="form-control" placeholder="Inicio (8:00 AM)">
      </div>
      <div class="col-4">
        <input type="text" name="schedules_new[${idx}][end_time]" class="form-control" placeholder="Fin (12:00 PM)">
      </div>
      <div class="col-4">
        <input type="text" name="schedules_new[${idx}][label]" class="form-control" placeholder="Etiqueta (opcional)">
      </div>
      <div class="col-6 mt-2">
        <input type="number" name="schedules_new[${idx}][max_capacity]" class="form-control"
               placeholder="Cupo (vacío = default del tour)">
      </div>
      <div class="col-6 mt-2 text-end">
        <button type="button" class="btn btn-outline-danger btn-sm btn-remove-sched">Eliminar</button>
      </div>
    `;
    container.appendChild(row);
  });

  container?.addEventListener('click', (e) => {
    if (e.target.closest('.btn-remove-sched')) {
      const rows = container.querySelectorAll('.schedule-new');
      if (rows.length > 1) e.target.closest('.schedule-new').remove();
    }
  });
});
</script>
@endpush
