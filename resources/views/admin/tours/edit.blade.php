{{-- resources/views/admin/tours/edit.blade.php --}}
@php use Carbon\Carbon; @endphp

@foreach($tours as $tour)
<div class="modal fade" id="modalEditar{{ $tour->tour_id }}" tabindex="-1" aria-labelledby="modalEditarLabel{{ $tour->tour_id }}" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header bg-warning text-white">
        <h5 class="modal-title" id="modalEditarLabel{{ $tour->tour_id }}">Editar Tour #{{ $tour->tour_id }}</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <form action="{{ route('admin.tours.update', $tour->tour_id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="modal-body">

          {{-- Validaciones sólo si este modal fue el que falló --}}
          @if($errors->any() && session('showEditModal') == $tour->tour_id)
            <div class="alert alert-danger">
              <ul class="mb-0">
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <div class="row g-4">
            {{-- ===== Columna izquierda (igual que create) ===== --}}
            <div class="col-lg-7">

              {{-- Nombre --}}
              <x-adminlte-input name="name" label="Nombre del Tour" value="{{ old('name', $tour->name) }}" required />

              {{-- Color / Viator / Duración --}}
              <div class="row">
                <div class="col-md-4">
                  <label class="form-label">Color del Tour</label>
                  <input type="color" name="color" class="form-control form-control-color"
                         value="{{ old('color', $tour->color ?? '#5cb85c') }}">
                </div>
                <div class="col-md-4">
                  <x-adminlte-input name="viator_code" label="Código Viator (opcional)"
                                    value="{{ old('viator_code', $tour->viator_code) }}" />
                </div>
                <div class="col-md-4">
                  <x-adminlte-input name="length" label="Duración (horas)" type="number" step="0.1"
                                    value="{{ old('length', $tour->length) }}" required />
                </div>
              </div>

              {{-- Overview --}}
              <x-adminlte-textarea name="overview" label="Resumen (Overview)" style="height:180px">
                {{ old('overview', $tour->overview) }}
              </x-adminlte-textarea>

              {{-- Precios + Cupo por defecto (alineado igual que create) --}}
              <div class="row">
                <div class="col-md-4">
                  <x-adminlte-input name="adult_price" label="Precio Adulto" type="number" step="0.01"
                                    value="{{ old('adult_price', $tour->adult_price) }}" required />
                </div>
                <div class="col-md-4">
                  <x-adminlte-input name="kid_price" label="Precio Niño" type="number" step="0.01"
                                    value="{{ old('kid_price', $tour->kid_price) }}" />
                </div>
                <div class="col-md-4">
                  <label class="form-label">Cupo por defecto</label>
                  <input type="number" name="max_capacity" class="form-control"
                         value="{{ old('max_capacity', $tour->max_capacity) }}" min="1" required>
                </div>
              </div>

              {{-- Tipo --}}
              <x-adminlte-select name="tour_type_id" label="Tipo de Tour" required>
                <option value="">Seleccione tipo</option>
                @foreach($tourtypes as $type)
                  <option value="{{ $type->tour_type_id }}"
                          @selected(old('tour_type_id', $tour->tour_type_id) == $type->tour_type_id)>
                    {{ $type->name }}
                  </option>
                @endforeach
              </x-adminlte-select>

              {{-- Idiomas --}}
              <div class="mb-3">
                <label>Idiomas Disponibles</label><br>
                @php $langsChecked = old('languages', $tour->languages->pluck('tour_language_id')->toArray()); @endphp
                @foreach($languages as $lang)
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="languages[]" value="{{ $lang->tour_language_id }}"
                           @checked(in_array($lang->tour_language_id, $langsChecked))>
                    <label class="form-check-label">{{ $lang->name }}</label>
                  </div>
                @endforeach
              </div>

              {{-- Amenidades incluidas --}}
              <div class="mb-3">
                <label>Amenidades</label><br>
                @php $amsChecked = old('amenities', $tour->amenities->pluck('amenity_id')->toArray()); @endphp
                @foreach($amenities as $am)
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="amenities[]" value="{{ $am->amenity_id }}"
                           @checked(in_array($am->amenity_id, $amsChecked))>
                    <label class="form-check-label">{{ $am->name }}</label>
                  </div>
                @endforeach
              </div>

              {{-- Amenidades NO incluidas --}}
              <div class="mb-3">
                <label class="form-label text-danger">Amenidades NO incluidas</label><br>
                @php $exChecked = old('excluded_amenities', $tour->excludedAmenities->pluck('amenity_id')->toArray()); @endphp
                @foreach($amenities as $am)
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="excluded_amenities[]" value="{{ $am->amenity_id }}"
                           @checked(in_array($am->amenity_id, $exChecked))>
                    <label class="form-check-label">{{ $am->name }}</label>
                  </div>
                @endforeach
              </div>
            </div>

            {{-- ===== Columna derecha (igual que create) ===== --}}
            <div class="col-lg-5">

              {{-- Itinerario (sólo asignar) --}}
              <div class="card mb-3 shadow-sm">
                <div class="card-header bg-light fw-bold">Itinerario</div>
                <div class="card-body">
                  <select name="itinerary_id" id="edit-itinerary-{{ $tour->tour_id }}" class="form-select" required>
                    <option value="">-- Seleccione --</option>
                    @foreach($itineraries as $it)
                      <option value="{{ $it->itinerary_id }}"
                              @selected(old('itinerary_id', $tour->itinerary_id) == $it->itinerary_id)>
                        {{ $it->name }}
                      </option>
                    @endforeach
                  </select>

                  {{-- Vista previa (la rellena el mismo script global que usa el create;
                       para eso necesita estos IDs por tour) --}}
                  <div id="itinerary-preview-{{ $tour->tour_id }}" class="mt-3" style="display:none;">
                    <div id="selected-itinerary-description-{{ $tour->tour_id }}"
                         class="small text-muted mb-2" style="white-space: pre-line;"></div>
                    <ul class="list-group small" id="itinerary-items-list-{{ $tour->tour_id }}"></ul>
                  </div>
                </div>
              </div>

              {{-- Horarios --}}
              <div class="card shadow-sm">
                <div class="card-header bg-light fw-bold d-flex align-items-center justify-content-between">
                  <span>Horarios del Tour</span>
                </div>
                <div class="card-body">
                  {{-- existentes --}}
                  <div class="mb-3">
                    <label class="form-label">Usar horarios existentes</label>
                    @php
                      $preSelected = collect(old('schedules_existing', $tour->schedules->pluck('schedule_id')))
                                      ->map(fn($v)=>(int)$v)->all();
                    @endphp
                    <select class="form-select" name="schedules_existing[]" multiple size="6">
                      @foreach($allSchedules as $sc)
                        @php
                          $start = Carbon::parse($sc->start_time)->format('H:i');
                          $end   = Carbon::parse($sc->end_time)->format('H:i');
                          $lbl   = $sc->label ? " - {$sc->label}" : '';
                          $cap   = $sc->max_capacity ?? '—';
                        @endphp
                        <option value="{{ $sc->schedule_id }}" @selected(in_array($sc->schedule_id, $preSelected, true))>
                          {{ $start }} - {{ $end }}{{ $lbl }} (cap: {{ $cap }})
                        </option>
                      @endforeach
                    </select>
                    <div class="form-text">Mantén CTRL/CMD para seleccionar varios.</div>
                  </div>

                  <hr>

                  {{-- nuevos (misma UX del create) --}}
                  <label class="form-label">Crear horarios nuevos</label>
                  <div id="edit-new-schedules-{{ $tour->tour_id }}">
                    {{-- Fila plantilla (clonada por scripts.blade) --}}
                    <div class="schedule-row border-bottom pb-3 mb-3 d-none" id="edit-row-template-{{ $tour->tour_id }}">
                      <div class="row g-2 align-items-end">
                        <div class="col-4">
                          <label class="form-label">Inicio</label>
                          <input type="text" class="form-control sch-start" placeholder="Inicio (8:00 AM)">
                        </div>
                        <div class="col-4">
                          <label class="form-label">Fin</label>
                          <input type="text" class="form-control sch-end" placeholder="Fin (12:00 PM)">
                        </div>
                        <div class="col-4">
                          <label class="form-label">Etiqueta (opcional)</label>
                          <input type="text" class="form-control sch-label" placeholder="Etiqueta">
                        </div>
                        <div class="col-6 mt-2">
                          <input type="number" min="1" class="form-control sch-cap"
                                 placeholder="Cupo (vacío = default del tour)">
                        </div>
                        <div class="col-6 mt-2 text-end">
                          <button type="button" class="btn btn-outline-danger btn-sm btn-remove-row">Eliminar</button>
                        </div>
                      </div>
                    </div>

                    {{-- Si viene con old(), las pintamos (igual que create) --}}
                    @php $oldRows = old('schedules_new', []); @endphp
                    @foreach($oldRows as $i => $r)
                      <div class="schedule-row border-bottom pb-3 mb-3">
                        <div class="row g-2 align-items-end">
                          <div class="col-4">
                            <label class="form-label">Inicio</label>
                            <input type="text" name="schedules_new[{{ $i }}][start_time]" class="form-control"
                                   value="{{ $r['start_time'] ?? '' }}" placeholder="Inicio (8:00 AM)">
                          </div>
                          <div class="col-4">
                            <label class="form-label">Fin</label>
                            <input type="text" name="schedules_new[{{ $i }}][end_time]" class="form-control"
                                   value="{{ $r['end_time'] ?? '' }}" placeholder="Fin (12:00 PM)">
                          </div>
                          <div class="col-4">
                            <label class="form-label">Etiqueta (opcional)</label>
                            <input type="text" name="schedules_new[{{ $i }}][label]" class="form-control"
                                   value="{{ $r['label'] ?? '' }}">
                          </div>
                          <div class="col-6 mt-2">
                            <input type="number" name="schedules_new[{{ $i }}][max_capacity]" class="form-control"
                                   min="1" value="{{ $r['max_capacity'] ?? '' }}" placeholder="Cupo (vacío = default del tour)">
                          </div>
                          <div class="col-6 mt-2 text-end">
                            <button type="button" class="btn btn-outline-danger btn-sm btn-remove-row">Eliminar</button>
                          </div>
                        </div>
                      </div>
                    @endforeach
                  </div>

                  <button
                    type="button"
                    class="btn btn-outline-secondary btn-sm btn-add-schedule-row"
                    data-target="#edit-new-schedules-{{ $tour->tour_id }}"
                    data-template="#edit-row-template-{{ $tour->tour_id }}">
                    + Añadir horario
                  </button>

                </div>{{-- card-body --}}
              </div>{{-- card --}}
            </div>{{-- col-lg-5 --}}
          </div>{{-- row --}}

        </div>{{-- modal-body --}}

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-warning">Actualizar</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endforeach
