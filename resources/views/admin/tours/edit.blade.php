@foreach($tours as $tour)
    <x-adminlte-modal
        id="modalEditar{{ $tour->tour_id }}"
        title="Editar Tour"
        size="lg"
        theme="warning"
        icon="fas fa-edit"
    >
        <form id="formEditar{{ $tour->tour_id }}"
              action="{{ route('admin.tours.update', $tour->tour_id) }}"
              method="POST">
            @csrf @method('PUT')

            <x-adminlte-input
                name="name" label="Nombre del Tour"
                value="{{ old('name', $tour->name) }}" required
            />

            <x-adminlte-textarea
                name="overview" label="Resumen (Overview)"
            >{{ old('overview', $tour->overview) }}</x-adminlte-textarea>

            <x-adminlte-textarea
                name="description" label="Descripción"
            >{{ old('description', $tour->description) }}</x-adminlte-textarea>

            <div class="row">
                <div class="col-md-4">
                    <x-adminlte-input name="adult_price" label="Precio Adulto"
                        type="number" step="0.01"
                        value="{{ old('adult_price', $tour->adult_price) }}" required />
                </div>
                <div class="col-md-4">
                    <x-adminlte-input name="kid_price" label="Precio Niño"
                        type="number" step="0.01"
                        value="{{ old('kid_price', $tour->kid_price) }}" />
                </div>
                <div class="col-md-4">
                    <x-adminlte-input name="length" label="Duración (horas)"
                        type="number" step="1"
                        value="{{ old('length', $tour->length) }}" required />
                </div>
            </div>

            {{-- Tipo de Tour --}}
            <div class="mb-3">
                <label>Tipo de Tour</label>
                <select name="tour_type_id" class="form-select" required>
                    <option value="">Seleccione un tipo</option>
                    @foreach ($tourtypes as $type)
                        <option value="{{ $type->tour_type_id }}"
                            {{ old('tour_type_id', $tour->tour_type_id) == $type->tour_type_id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Idiomas --}}
            <div class="mb-3">
                <label>Idiomas Disponibles</label>
                <div>
                    @foreach($languages as $lang)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox"
                                   name="languages[]" value="{{ $lang->tour_language_id }}"
                                   id="edit_lang_{{ $tour->tour_id }}_{{ $lang->tour_language_id }}"
                                   {{ in_array($lang->tour_language_id,
                                       old('languages', $tour->languages->pluck('tour_language_id')->toArray())) ? 'checked' : '' }}>
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
                <label>Amenidades</label>
                <div>
                    @foreach($amenities as $am)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox"
                                   name="amenities[]" value="{{ $am->amenity_id }}"
                                   id="edit_am_{{ $tour->tour_id }}_{{ $am->amenity_id }}"
                                   {{ in_array($am->amenity_id,
                                       old('amenities', $tour->amenities->pluck('amenity_id')->toArray())) ? 'checked' : '' }}>
                            <label class="form-check-label"
                                   for="edit_am_{{ $tour->tour_id }}_{{ $am->amenity_id }}">
                                {{ $am->name }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Horarios --}}
            <div class="mb-3">
                <label>Horario AM</label>
                <div class="row">
                    <div class="col-md-6">
                        <input type="time" name="schedule_am_start" class="form-control"
                            value="{{ old('schedule_am_start', optional($tour->schedules->first())->start_time) }}">
                    </div>
                    <div class="col-md-6">
                        <input type="time" name="schedule_am_end" class="form-control"
                            value="{{ old('schedule_am_end', optional($tour->schedules->first())->end_time) }}">
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label>Horario PM</label>
                <div class="row">
                    <div class="col-md-6">
                        <input type="time" name="schedule_pm_start" class="form-control"
                            value="{{ old('schedule_pm_start', optional($tour->schedules->skip(1)->first())->start_time) }}">
                    </div>
                    <div class="col-md-6">
                        <input type="time" name="schedule_pm_end" class="form-control"
                            value="{{ old('schedule_pm_end', optional($tour->schedules->skip(1)->first())->end_time) }}">
                    </div>
                </div>
            </div>

            {{-- Itinerario dinámico --}}
            <div class="mb-3">
                <label>Itinerario</label>
                <div id="itinerary-{{ $tour->tour_id }}" class="itinerary-container">
                    @foreach(($tour->itinerary?->items ?? collect())->sortBy('order')->values() as $i => $item)
                        <div class="row mb-2 itinerary-item">
                            <div class="col-md-4">
                                <input type="text" name="itinerary[{{ $i }}][title]" class="form-control"
                                       placeholder="Título" required value="{{ $item->title }}">
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="itinerary[{{ $i }}][description]" class="form-control"
                                       placeholder="Descripción" required value="{{ $item->description }}">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger btn-sm btn-remove-itinerary">&times;</button>
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="button" class="btn btn-outline-secondary btn-sm btn-add-itinerary"
                        data-target="#itinerary-{{ $tour->tour_id }}">
                    + Añadir Itinerario
                </button>
            </div>
        </form>

        <x-slot name="footerSlot">
            <button form="formEditar{{ $tour->tour_id }}" type="submit" class="btn btn-warning">
                Actualizar
            </button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                Cancelar
            </button>
        </x-slot>
    </x-adminlte-modal>
@endforeach