<!-- Modal Registrar Tour -->
<div class="modal fade" id="modalRegistrar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg"> <!-- más ancho -->
        <form action="{{ route('admin.tours.store') }}" method="POST" id="formCrearTour">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Tour</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Campos existentes omitidos para no repetirlos -->

                    {{-- ... campos nombre, descripcion, precios, duración, categoría, idioma ... --}}

                    <!-- Amenidades -->
                    <div class="mb-3">
                        <label class="form-label">Amenidades</label>
                        <div>
                            @foreach ($amenities as $amenity)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="amenities[]" id="amenity_{{ $amenity->amenity_id }}" value="{{ $amenity->amenity_id }}">
                                    <label class="form-check-label" for="amenity_{{ $amenity->amenity_id }}">{{ $amenity->name }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Horarios -->
                    <div class="mb-3">
                        <label class="form-label">Horarios Disponibles</label>
                        <div id="schedule-container-crear">
                            <div class="row align-items-end mb-2 schedule-item">
                                <div class="col-4">
                                    <label>Día</label>
                                    <input type="text" name="schedules[0][day]" class="form-control" placeholder="Lunes, Martes..." required>
                                </div>
                                <div class="col-3">
                                    <label>Hora Inicio</label>
                                    <input type="time" name="schedules[0][start_time]" class="form-control" required>
                                </div>
                                <div class="col-3">
                                    <label>Hora Fin</label>
                                    <input type="time" name="schedules[0][end_time]" class="form-control" required>
                                </div>
                                <div class="col-2">
                                    <button type="button" class="btn btn-danger btn-sm btn-remove-schedule" title="Quitar horario">&times;</button>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-secondary btn-sm" id="btnAddScheduleCrear">+ Añadir Horario</button>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Editar Tour -->
@foreach ($tours as $tour)
<div class="modal fade" id="modalEditar{{ $tour->tour_id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('admin.tours.update', $tour->tour_id) }}" method="POST" class="formEditarTour">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Tour</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Campos existentes omitidos -->

                    <!-- Amenidades -->
                    <div class="mb-3">
                        <label class="form-label">Amenidades</label>
                        <div>
                            @foreach ($amenities as $amenity)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="amenities[]" id="amenity_edit_{{ $amenity->amenity_id }}_{{ $tour->tour_id }}" value="{{ $amenity->amenity_id }}" 
                                    {{ $tour->amenities->contains($amenity->amenity_id) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="amenity_edit_{{ $amenity->amenity_id }}_{{ $tour->tour_id }}">{{ $amenity->name }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Horarios -->
                    <div class="mb-3">
                        <label class="form-label">Horarios Disponibles</label>
                        <div id="schedule-container-editar-{{ $tour->tour_id }}">
                            @foreach ($tour->schedules as $index => $schedule)
                            <div class="row align-items-end mb-2 schedule-item">
                                <div class="col-4">
                                    <label>Día</label>
                                    <input type="text" name="schedules[{{ $index }}][day]" class="form-control" value="{{ $schedule->day }}" required>
                                </div>
                                <div class="col-3">
                                    <label>Hora Inicio</label>
                                    <input type="time" name="schedules[{{ $index }}][start_time]" class="form-control" value="{{ $schedule->start_time }}" required>
                                </div>
                                <div class="col-3">
                                    <label>Hora Fin</label>
                                    <input type="time" name="schedules[{{ $index }}][end_time]" class="form-control" value="{{ $schedule->end_time }}" required>
                                </div>
                                <div class="col-2">
                                    <button type="button" class="btn btn-danger btn-sm btn-remove-schedule" title="Quitar horario">&times;</button>
                                </div>
                            </div>
                            @endforeach
                            @if ($tour->schedules->isEmpty())
                            <div class="row align-items-end mb-2 schedule-item">
                                <div class="col-4">
                                    <label>Día</label>
                                    <input type="text" name="schedules[0][day]" class="form-control" placeholder="Lunes, Martes..." required>
                                </div>
                                <div class="col-3">
                                    <label>Hora Inicio</label>
                                    <input type="time" name="schedules[0][start_time]" class="form-control" required>
                                </div>
                                <div class="col-3">
                                    <label>Hora Fin</label>
                                    <input type="time" name="schedules[0][end_time]" class="form-control" required>
                                </div>
                                <div class="col-2">
                                    <button type="button" class="btn btn-danger btn-sm btn-remove-schedule" title="Quitar horario">&times;</button>
                                </div>
                            </div>
                            @endif
                        </div>
                        <button type="button" class="btn btn-secondary btn-sm btnAddScheduleEditar" data-tour-id="{{ $tour->tour_id }}">+ Añadir Horario</button>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning">Actualizar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endforeach

@section('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Función para crear un nuevo bloque horario
    function createScheduleItem(index, prefix = '') {
        return `
        <div class="row align-items-end mb-2 schedule-item">
            <div class="col-4">
                <label>Día</label>
                <input type="text" name="${prefix}schedules[${index}][day]" class="form-control" placeholder="Lunes, Martes..." required>
            </div>
            <div class="col-3">
                <label>Hora Inicio</label>
                <input type="time" name="${prefix}schedules[${index}][start_time]" class="form-control" required>
            </div>
            <div class="col-3">
                <label>Hora Fin</label>
                <input type="time" name="${prefix}schedules[${index}][end_time]" class="form-control" required>
            </div>
            <div class="col-2">
                <button type="button" class="btn btn-danger btn-sm btn-remove-schedule" title="Quitar horario">&times;</button>
            </div>
        </div>`;
    }

    // Agregar horarios en formulario crear
    let scheduleIndexCrear = 1;
    document.getElementById('btnAddScheduleCrear').addEventListener('click', function() {
        const container = document.getElementById('schedule-container-crear');
        container.insertAdjacentHTML('beforeend', createScheduleItem(scheduleIndexCrear));
        scheduleIndexCrear++;
    });

    // Agregar horarios en formularios editar
    document.querySelectorAll('.btnAddScheduleEditar').forEach(button => {
        button.addEventListener('click', function() {
            const tourId = this.dataset.tourId;
            const container = document.getElementById('schedule-container-editar-' + tourId);

            // Contar elementos actuales para índice
            const currentCount = container.querySelectorAll('.schedule-item').length;
            container.insertAdjacentHTML('beforeend', createScheduleItem(currentCount));
        });
    });

    // Eliminar horario
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-remove-schedule')) {
            const item = e.target.closest('.schedule-item');
            if (item) item.remove();
        }
    });
</script>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Éxito',
        text: "{{ session('success') }}",
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'OK'
    });
</script>
@endif
</script>
@stop
