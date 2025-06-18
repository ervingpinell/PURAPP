{{-- Modal para registrar un nuevo tour --}}
<x-adminlte-modal
    id="modalRegistrar"
    title="Registrar Tour"
    size="lg"
    theme="primary"
    icon="fas fa-plus"
>
    <form id="formCrearTour" action="{{ route('admin.tours.store') }}" method="POST">
        @csrf

+       {{-- Mostrar errores de validación --}}
+       @if($errors->any())
+         <div class="alert alert-danger">
+           <ul class="mb-0">
+             @foreach($errors->all() as $err)
+               <li>{{ $err }}</li>
+             @endforeach
+           </ul>
+         </div>
+       @endif

        {{-- Nombre, resumen y descripción --}}
        <x-adminlte-input name="name" label="Nombre del Tour" value="{{ old('name') }}" required />
        <x-adminlte-textarea name="overview" label="Resumen (Overview)">{{ old('overview') }}</x-adminlte-textarea>
        <x-adminlte-textarea name="description" label="Descripción">{{ old('description') }}</x-adminlte-textarea>

        {{-- Precios y duración --}}
        <div class="row">
            <div class="col-md-4">
                <x-adminlte-input name="adult_price" label="Precio Adulto"
-                                 type="number" step="0.01" value="{{ old('adult_price') }}" required />
+                                 type="number" step="0.01" value="{{ old('adult_price') }}" required />
            </div>
            <div class="col-md-4">
                <x-adminlte-input name="kid_price" label="Precio Niño"
-                                 type="number" step="0.01" value="{{ old('kid_price') }}" />
+                                 type="number" step="0.01" value="{{ old('kid_price') }}" />
            </div>
            <div class="col-md-4">
                <x-adminlte-input name="length" label="Duración (horas)"
-                                 type="number" step="1" value="{{ old('length') }}" required />
+                                 type="number" step="1" value="{{ old('length') }}" required />
            </div>
        </div>

        {{-- Tipo de tour --}}
        <div class="mb-3">
            <label>Tipo de Tour</label>
            <select name="tour_type_id" class="form-control" required>
                <option value="">Seleccione un tipo de tour</option>
                @foreach($tourtypes as $tourType)
                    <option value="{{ $tourType->tour_type_id }}"
-                           {{ old('tour_type_id') == $tourType->tour_type_id ? 'selected' : '' }}>
+                           {{ old('tour_type_id') == $tourType->tour_type_id ? 'selected' : '' }}>
                        {{ $tourType->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Idiomas disponibles --}}
        <div class="mb-3">
            <label>Idiomas Disponibles</label>
            <div>
                @foreach($languages as $lang)
                    <div class="form-check form-check-inline">
                        <input class="form-check-input"
-                              type="checkbox" name="languages[]" value="{{ $lang->tour_language_id }}"
-                              id="lang_{{ $lang->tour_language_id }}"
-                              {{ in_array($lang->tour_language_id, old('languages', [])) ? 'checked' : '' }}>
+                              type="checkbox" name="languages[]" value="{{ $lang->tour_language_id }}"
+                              id="lang_{{ $lang->tour_language_id }}"
+                              {{ in_array($lang->tour_language_id, old('languages', [])) ? 'checked' : '' }}>
                        <label class="form-check-label" for="lang_{{ $lang->tour_language_id }}">{{ $lang->name }}</label>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Amenidades --}}
        <div class="mb-3">
            <label>Amenidades</label>
            <div>
                @foreach($amenities as $amenity)
                    <div class="form-check form-check-inline">
                        <input class="form-check-input"
-                              type="checkbox" name="amenities[]" value="{{ $amenity->amenity_id }}"
-                              id="amenity_{{ $amenity->amenity_id }}"
-                              {{ in_array($amenity->amenity_id, old('amenities', [])) ? 'checked' : '' }}>
+                              type="checkbox" name="amenities[]" value="{{ $amenity->amenity_id }}"
+                              id="amenity_{{ $amenity->amenity_id }}"
+                              {{ in_array($amenity->amenity_id, old('amenities', [])) ? 'checked' : '' }}>
                        <label class="form-check-label" for="amenity_{{ $amenity->amenity_id }}">{{ $amenity->name }}</label>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Horarios AM y PM --}}
        <div class="mb-3">
            <label>Horario AM</label>
            <div class="row">
                <div class="col-md-6">
                    <input type="time" name="schedule_am_start" class="form-control"
-                          value="{{ old('schedule_am_start') }}">
+                          value="{{ old('schedule_am_start') }}">
                </div>
                <div class="col-md-6">
                    <input type="time" name="schedule_am_end" class="form-control"
-                          value="{{ old('schedule_am_end') }}">
+                          value="{{ old('schedule_am_end') }}">
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label>Horario PM</label>
            <div class="row">
                <div class="col-md-6">
                    <input type="time" name="schedule_pm_start" class="form-control"
-                          value="{{ old('schedule_pm_start') }}">
+                          value="{{ old('schedule_pm_start') }}">
                </div>
                <div class="col-md-6">
                    <input type="time" name="schedule_pm_end" class="form-control"
-                          value="{{ old('schedule_pm_end') }}">
+                          value="{{ old('schedule_pm_end') }}">
                </div>
            </div>
        </div>

        {{-- Selección de itinerario existente o nuevo --}}
        <div class="mb-3">
            <label>Itinerario</label>
            <select name="itinerary_id" id="select-itinerary" class="form-control" required>
                <option value="">Seleccione un itinerario</option>
                @foreach($itineraries as $it)
                    <option value="{{ $it->itinerary_id }}" {{ old('itinerary_id') == $it->itinerary_id ? 'selected' : '' }}>
                        {{ $it->name }}
                    </option>
                @endforeach
-               <option value="new" {{ old('itinerary_id') == 'new' ? 'selected' : '' }}>+ Crear nuevo itinerario</option>
+               <option value="new" {{ old('itinerary_id') == 'new' ? 'selected' : '' }}>+ Crear nuevo itinerario</option>
            </select>
        </div>

        {{-- Campos condicionales para nuevo itinerario --}}
        <div id="new-itinerary-fields" style="display: none;">
            <div class="mb-3">
                <label>Nombre del nuevo itinerario</label>
-               <input type="text" name="new_itinerary_name" class="form-control" value="{{ old('new_itinerary_name') }}">
+               <input type="text" name="new_itinerary_name" class="form-control" value="{{ old('new_itinerary_name') }}">
            </div>

            <div class="mb-3">
                <label>Ítems del itinerario</label>
                <div id="new-itinerary-items" class="itinerary-container">
-                   <div class="row mb-2 itinerary-item">
+                   <div class="row mb-2 itinerary-item">
                        <div class="col-md-4">
-                          <input type="text" name="itinerary[0][title]" disabled class="form-control" placeholder="Título" required>
+                          <input type="text" name="itinerary[0][title]" class="form-control" placeholder="Título" required>
                        </div>
                        <div class="col-md-6">
-                           <input type="text" name="itinerary[0][description]" disabled  class="form-control" placeholder="Descripción" required>
+                           <input type="text" name="itinerary[0][description]" class="form-control" placeholder="Descripción" required>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger btn-sm btn-remove-itinerary">&times;</button>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-secondary btn-sm btn-add-itinerary" data-target="#new-itinerary-items">
                    + Añadir Ítem
                </button>
            </div>
        </div>
    </form>

    {{-- Footer del modal --}}
    <x-slot name="footerSlot">
        <button form="formCrearTour" type="submit" class="btn btn-primary">Guardar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
    </x-slot>
</x-adminlte-modal>

{{-- Añade este pequeño script justo al final de scripts.blade.php --}}
@push('js')
<script>
    // Mostrar/ocultar bloque de nuevo itinerario
    document.getElementById('select-itinerary').addEventListener('change', function(){
        document.getElementById('new-itinerary-fields')
            .style.display = this.value === 'new' ? 'block' : 'none';
    });
</script>
@endpush
