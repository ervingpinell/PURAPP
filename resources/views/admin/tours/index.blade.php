@extends('adminlte::page')

@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset('css/gv.css') }}">
@stop

@section('title', 'Gestión de Tours')

@section('content_header')
    <h1>Gestión de Tours</h1>
@stop

@section('content')
<div class="p-3 table-responsive">
    <a href="#" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalRegistrar">
        <i class="fas fa-plus"></i> Añadir Tour
    </a>

    {{-- Template único para nuevos items de itinerario --}}
    <template id="itinerary-template">
        <div class="row mb-2 itinerary-item">
            <div class="col-md-4">
                <input type="text" name="__NAME__" class="form-control" placeholder="Título" required>
            </div>
            <div class="col-md-6">
                <input type="text" name="__DESC__" class="form-control" placeholder="Descripción" required>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger btn-sm btn-remove-itinerary">&times;</button>
            </div>
        </div>
    </template>

    <table class="table table-bordered table-striped table-hover">
        <thead class="bg-primary text-white">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Resumen</th>
                <th>Descripción</th>
                <th>Amenidades</th>
                <th>Itinerario</th>
                <th>Precio Adulto</th>
                <th>Precio Niño</th>
                <th>Duración (h)</th>
                <th>Categoría</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        @foreach($tours as $tour)
            <tr>
                <td>{{ $tour->tour_id }}</td>
                <td>{{ $tour->name }}</td>
                <td>{{ $tour->overview }}</td>
                <td>{{ Str::limit($tour->description, 50) }}</td>
                <td>
                    @forelse($tour->amenities as $am)
                        <span class="badge bg-info">{{ $am->name }}</span>
                    @empty
                        <span class="text-muted">Sin amenidades</span>
                    @endforelse
                </td>
                <td>
                    @forelse($tour->itineraryItems as $item)
                        <span class="badge bg-info">{{ $item->title }}</span>
                    @empty
                        <span class="text-muted">Sin itinerarios</span>
                    @endforelse
                </td>
                <td>{{ number_format($tour->adult_price, 2) }}</td>
                <td>{{ number_format($tour->kid_price, 2) }}</td>
                <td>{{ $tour->length }}</td>
                <td>{{ $tour->category->name }}</td>
                <td>
                    @if($tour->is_active)
                        <span class="badge bg-success">Activo</span>
                    @else
                        <span class="badge bg-secondary">Inactivo</span>
                    @endif
                </td>
                <td>
                    <a href="#" class="btn btn-warning btn-sm"
                       data-bs-toggle="modal"
                       data-bs-target="#modalEditar{{ $tour->tour_id }}">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('admin.tours.destroy', $tour->tour_id) }}"
                          method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="btn btn-sm {{ $tour->is_active ? 'btn-danger' : 'btn-success' }}"
                                onclick="return confirm('{{ $tour->is_active
                                    ? '¿Deseas desactivar este tour?'
                                    : '¿Deseas activar este tour?' }}')">
                            <i class="fas {{ $tour->is_active ? 'fa-toggle-off' : 'fa-toggle-on' }}"></i>
                        </button>
                    </form>
                </td>
            </tr>

            {{-- Modal Editar Tour --}}
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
                        value="{{ old('name', $tour->name) }}"
                        required
                    />
                    <x-adminlte-textarea
                        name="overview" label="Resumen (Overview)"
                    >{{ old('overview', $tour->overview) }}</x-adminlte-textarea>
                    <x-adminlte-textarea
                        name="description" label="Descripción"
                    >{{ old('description', $tour->description) }}</x-adminlte-textarea>

                    <div class="row">
                        <div class="col-md-4">
                            <x-adminlte-input
                                name="adult_price" label="Precio Adulto"
                                type="number" step="0.01"
                                value="{{ old('adult_price', $tour->adult_price) }}"
                                required
                            />
                        </div>
                        <div class="col-md-4">
                            <x-adminlte-input
                                name="kid_price" label="Precio Niño"
                                type="number" step="0.01"
                                value="{{ old('kid_price', $tour->kid_price) }}"
                            />
                        </div>
                        <div class="col-md-4">
                            <x-adminlte-input
                                name="length" label="Duración (horas)"
                                type="number" step="1"
                                value="{{ old('length', $tour->length) }}"
                                required
                            />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label>Categoría</label>
                        <select name="category_id" class="form-control" required>
                            <option value="">Seleccione una categoría</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->category_id }}"
                                    {{ $cat->category_id == old('category_id', $tour->category_id) ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Idiomas y Amenidades --}}
                    <div class="mb-3">
                        <label>Idiomas Disponibles</label>
                        <div>
                            @foreach($languages as $lang)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox"
                                           name="languages[]" value="{{ $lang->tour_language_id }}"
                                           id="edit_lang_{{ $tour->tour_id }}_{{ $lang->tour_language_id }}"
                                           {{ in_array($lang->tour_language_id,
                                               old('languages', $tour->languages->pluck('tour_language_id')->toArray()))
                                               ? 'checked' : '' }}>
                                    <label class="form-check-label"
                                           for="edit_lang_{{ $tour->tour_id }}_{{ $lang->tour_language_id }}">
                                        {{ $lang->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Amenidades</label>
                        <div>
                            @foreach($amenities as $am)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox"
                                           name="amenities[]" value="{{ $am->amenity_id }}"
                                           id="edit_am_{{ $tour->tour_id }}_{{ $am->amenity_id }}"
                                           {{ in_array($am->amenity_id,
                                               old('amenities', $tour->amenities->pluck('amenity_id')->toArray()))
                                               ? 'checked' : '' }}>
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

                    {{-- Itinerario dinámico en edición --}}
                    <div class="mb-3">
                    <label>Itinerario</label>
                    <div id="itinerary-{{ $tour->tour_id }}" class="itinerary-container">
                        @foreach($tour->itineraryItems->sortBy('order')->values() as $i => $item)
                        <div class="row mb-2 itinerary-item">
                            <div class="col-md-4">
                            <input type="text"
                                    name="itinerary[{{ $i }}][title]"
                                    class="form-control"
                                    placeholder="Título"
                                    required
                                    value="{{ $item->title }}">
                            </div>
                            <div class="col-md-6">
                            <input type="text"
                                    name="itinerary[{{ $i }}][description]"
                                    class="form-control"
                                    placeholder="Descripción"
                                    required
                                    value="{{ $item->description }}">
                            </div>
                            <div class="col-md-2">
                            <button type="button" class="btn btn-danger btn-sm btn-remove-itinerary">&times;</button>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <button type="button"
                            class="btn btn-outline-secondary btn-sm btn-add-itinerary"
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
        </tbody>
    </table>
</div>

{{-- Modal Registrar Tour --}}
<x-adminlte-modal
    id="modalRegistrar"
    title="Registrar Tour"
    size="lg"
    theme="primary"
    icon="fas fa-plus"
>
    <form id="formCrearTour" action="{{ route('admin.tours.store') }}" method="POST">
        @csrf

        <x-adminlte-input name="name" label="Nombre del Tour" value="{{ old('name') }}" required/>
        <x-adminlte-textarea name="overview" label="Resumen (Overview)">{{ old('overview') }}</x-adminlte-textarea>
        <x-adminlte-textarea name="description" label="Descripción">{{ old('description') }}</x-adminlte-textarea>

        <div class="row">
            <div class="col-md-4">
                <x-adminlte-input
                    name="adult_price" label="Precio Adulto"
                    type="number" step="0.01"
                    value="{{ old('adult_price') }}" required
                />
            </div>
            <div class="col-md-4">
                <x-adminlte-input
                    name="kid_price" label="Precio Niño"
                    type="number" step="0.01"
                    value="{{ old('kid_price') }}"
                />
            </div>
            <div class="col-md-4">
                <x-adminlte-input
                    name="length" label="Duración (horas)"
                    type="number" step="1"
                    value="{{ old('length') }}" required
                />
            </div>
        </div>

        <div class="mb-3">
            <label>Categoría</label>
            <select name="category_id" class="form-control" required>
                <option value="">Seleccione una categoría</option>
                @foreach($categories as $category)
                    <option value="{{ $category->category_id }}"
                        {{ $category->category_id == old('category_id') ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Idiomas y Amenidades --}}
        <div class="mb-3">
            <label>Idiomas Disponibles</label>
            <div>
                @foreach($languages as $lang)
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox"
                               name="languages[]" value="{{ $lang->tour_language_id }}"
                               id="lang_{{ $lang->tour_language_id }}"
                               {{ in_array($lang->tour_language_id, old('languages', [])) ? 'checked' : '' }}>
                        <label class="form-check-label" for="lang_{{ $lang->tour_language_id }}">
                            {{ $lang->name }}
                        </label>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="mb-3">
            <label>Amenidades</label>
            <div>
                @foreach($amenities as $amenity)
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox"
                               name="amenities[]" value="{{ $amenity->amenity_id }}"
                               id="amenity_{{ $amenity->amenity_id }}"
                               {{ in_array($amenity->amenity_id, old('amenities', [])) ? 'checked' : '' }}>
                        <label class="form-check-label" for="amenity_{{ $amenity->amenity_id }}">
                            {{ $amenity->name }}
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
                        value="{{ old('schedule_am_start') }}">
                </div>
                <div class="col-md-6">
                    <input type="time" name="schedule_am_end" class="form-control"
                        value="{{ old('schedule_am_end') }}">
                </div>
            </div>
        </div>
        <div class="mb-3">
            <label>Horario PM</label>
            <div class="row">
                <div class="col-md-6">
                    <input type="time" name="schedule_pm_start" class="form-control"
                        value="{{ old('schedule_pm_start') }}">
                </div>
                <div class="col-md-6">
                    <input type="time" name="schedule_pm_end" class="form-control"
                        value="{{ old('schedule_pm_end') }}">
                </div>
            </div>
        </div>

        {{-- Itinerario dinámico en creación --}}
        <div class="mb-3">
            <label>Itinerario</label>
            <div id="itinerary-create" class="itinerary-container">
                <div class="row mb-2 itinerary-item">
                    <div class="col-md-4">
                        <input type="text" name="itinerary[0][title]" class="form-control"
                               placeholder="Título" required>
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="itinerary[0][description]" class="form-control"
                               placeholder="Descripción" required>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger btn-sm btn-remove-itinerary">&times;</button>
                    </div>
                </div>
            </div>
            <button type="button"
                    class="btn btn-outline-secondary btn-sm btn-add-itinerary"
                    data-target="#itinerary-create">
                + Añadir Itinerario
            </button>
        </div>

    </form>

    <x-slot name="footerSlot">
        <button form="formCrearTour" type="submit" class="btn btn-primary">Guardar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
    </x-slot>
</x-adminlte-modal>
@endsection

@section('plugins.Sweetalert2', true)
@section('js')
  {{-- Bootstrap y SweetAlert2 --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
  document.addEventListener('DOMContentLoaded', () => {
    document.body.addEventListener('click', e => {
      // 1) Click en “+ Añadir Itinerario”
      const addBtn = e.target.closest('.btn-add-itinerary');
      if (addBtn) {
        const selector  = addBtn.dataset.target;               // ej. "#itinerary-11"
        const container = document.querySelector(selector);
        if (!container) {
          console.error('Contenedor no encontrado:', selector);
          return;
        }
        const idx  = container.querySelectorAll('.itinerary-item').length;
        const tpl  = document.getElementById('itinerary-template').innerHTML;
        const html = tpl
          .replace(/__NAME__/g, `itinerary[${idx}][title]`)
          .replace(/__DESC__/g, `itinerary[${idx}][description]`);
        container.insertAdjacentHTML('beforeend', html);
        return;
      }
        console.log('¡btn-add-itinerary pulsado!');
      // 2) Click en “×” para quitar un item
      const removeBtn = e.target.closest('.btn-remove-itinerary');
      if (removeBtn) {
        const container = removeBtn.closest('.itinerary-container');
        const items     = container.querySelectorAll('.itinerary-item');
        if (items.length <= 1) {
          return Swal.fire('Aviso','Debe haber al menos un ítem en el itinerario','warning');
        }
        removeBtn.closest('.itinerary-item').remove();
        // reindexar los names
        container.querySelectorAll('.itinerary-item').forEach((row, i) => {
          row.querySelector('input[placeholder="Título"]').name       = `itinerary[${i}][title]`;
          row.querySelector('input[placeholder="Descripción"]').name = `itinerary[${i}][description]`;
        });
      }
    });

    // 3) Feedback con SweetAlert...
    @if(session('success'))
      Swal.fire({ icon:'success', title:'{{ session("success") }}',
                  showConfirmButton:false, timer:2000 });
    @endif
    @if(session('error'))
      Swal.fire({ icon:'error', title:'{{ session("error") }}',
                  showConfirmButton:false, timer:2000 });
    @endif

    // 4) Reabrir modal tras validación fallida
    const show = @json(session('show')); 
    if (show === 'create') {
      new bootstrap.Modal(document.getElementById('modalRegistrar')).show();
    } else if (show && show.startsWith('edit-')) {
      const id      = show.split('-')[1];
      const modalEl = document.getElementById(`modalEditar${id}`);
      if (modalEl) new bootstrap.Modal(modalEl).show();
    }
  });
  </script>
@stop
