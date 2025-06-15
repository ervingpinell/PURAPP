@extends('adminlte::page')

@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset('css/gv.css') }}"> 
@stop

@section('title', 'Registrar Tour')

@section('content_header')
    <h1>Registrar Tour</h1>
@stop

@section('content')
    <a href="#" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalRegistrar">
        <i class="fas fa-plus"></i> Añadir Tour
    </a>

    <!-- Modal Registrar Tour -->
    <x-adminlte-modal id="modalRegistrar" title="Registrar Tour" size="lg" theme="primary" icon="fas fa-plus">
        <form action="{{ route('admin.tours.store') }}" method="POST" id="formCrearTour">
            @csrf

            <!-- Nombre, Descripcion, Precios, Duracion -->
            <x-adminlte-input name="name" label="Nombre del Tour" required />

            <x-adminlte-textarea name="description" label="Descripción" />

            <div class="row">
                <div class="col-md-4">
                    <x-adminlte-input name="adult_price" label="Precio Adulto" type="number" step="0.01" required />
                </div>
                <div class="col-md-4">
                    <x-adminlte-input name="kid_price" label="Precio Niño" type="number" step="0.01" />
                </div>
                <div class="col-md-4">
                    <x-adminlte-input name="length" label="Duración (horas)" type="number" step="1" required />
                </div>
            </div>

            <!-- Categoría -->
            <div class="mb-3">
                <label>Categoría</label>
                <select name="category_id" id="categorySelect" class="form-control" required>
                    <option value="">Seleccione una categoría</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->category_id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Idiomas (múltiples) -->
            <div class="mb-3">
                <label>Idiomas Disponibles</label>
                <div>
                    @foreach ($languages as $lang)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="languages[]" id="lang_{{ $lang->tour_language_id }}" value="{{ $lang->tour_language_id }}">
                            <label class="form-check-label" for="lang_{{ $lang->tour_language_id }}">{{ $lang->name }}</label>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Amenidades -->
            <div class="mb-3">
                <label>Amenidades</label>
                <div>
                    @foreach ($amenities as $amenity)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="amenities[]" id="amenity_{{ $amenity->amenity_id }}" value="{{ $amenity->amenity_id }}">
                            <label class="form-check-label" for="amenity_{{ $amenity->amenity_id }}">{{ $amenity->name }}</label>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Horarios (dos rangos fijos) -->
            <div class="mb-3">
                <label>Horario AM</label>
                <div class="row">
                    <div class="col-md-6">
                        <input type="time" name="schedule_am_start" class="form-control" placeholder="Hora Inicio">
                    </div>
                    <div class="col-md-6">
                        <input type="time" name="schedule_am_end" class="form-control" placeholder="Hora Fin">
                    </div>
                </div>
            </div>

            <div class="mb-3" id="pmScheduleContainer">
                <label>Horario PM</label>
                <div class="row">
                    <div class="col-md-6">
                        <input type="time" name="schedule_pm_start" class="form-control" placeholder="Hora Inicio">
                    </div>
                    <div class="col-md-6">
                        <input type="time" name="schedule_pm_end" class="form-control" placeholder="Hora Fin">
                    </div>
                </div>
            </div>

            <!-- Itinerario -->
            <div class="mb-3">
                <label>Itinerario</label>
                <div id="itinerary-container">
                    <div class="row mb-2 itinerary-item">
                        <div class="col-md-4">
                            <input type="text" name="itinerary[0][title]" class="form-control" placeholder="Título" required>
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="itinerary[0][description]" class="form-control" placeholder="Descripción" required>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger btn-sm btn-remove-itinerary">&times;</button>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-secondary btn-sm" id="btnAddItinerary">+ Añadir Itinerario</button>
            </div>

            <!-- Botones de acción -->
            <div class="text-right mt-3">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            </div>
        </form>
    </x-adminlte-modal>
@endsection

@section('js')
<script>
let itineraryIndex = 1;

// Añadir nuevos campos de itinerario
const btnAddItinerary = document.getElementById('btnAddItinerary');
btnAddItinerary?.addEventListener('click', function () {
    const container = document.getElementById('itinerary-container');
    const html = `
        <div class="row mb-2 itinerary-item">
            <div class="col-md-4">
                <input type="text" name="itinerary[${itineraryIndex}][title]" class="form-control" placeholder="Título" required>
            </div>
            <div class="col-md-6">
                <input type="text" name="itinerary[${itineraryIndex}][description]" class="form-control" placeholder="Descripción" required>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger btn-sm btn-remove-itinerary">&times;</button>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    itineraryIndex++;
});

// Eliminar campos de itinerario
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('btn-remove-itinerary')) {
        e.target.closest('.itinerary-item').remove();
    }
});

// Mostrar u ocultar horario PM si es Full Day
const categorySelect = document.getElementById('categorySelect');
const pmScheduleContainer = document.getElementById('pmScheduleContainer');
categorySelect?.addEventListener('change', function () {
    const selectedText = categorySelect.options[categorySelect.selectedIndex]?.text?.toLowerCase();
    if (selectedText.includes('full day')) {
        pmScheduleContainer.style.display = 'none';
        pmScheduleContainer.querySelectorAll('input').forEach(input => input.value = '');
    } else {
        pmScheduleContainer.style.display = 'block';
    }
});
</script>
@stop