@extends('adminlte::page')

@section('title', 'Fechas Bloqueadas')

@section('content_header')
    <h1><i class="fas fa-calendar-times"></i> Fechas Bloqueadas de Tours</h1>
@stop

@section('content')

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Fechas globales -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <label>Fecha inicio</label>
        <input type="date" id="global_start_date" class="form-control">
    </div>
    <div class="col-md-3">
        <label>Fecha fin</label>
        <input type="date" id="global_end_date" class="form-control">
    </div>
    <div class="col-md-4">
        <label>Motivo</label>
        <input type="text" id="global_reason" class="form-control" placeholder="Opcional">
    </div>
    <div class="col-md-1 d-flex align-items-end">
        <button id="blockAllBtn" class="btn btn-danger w-100">
            <i class="fas fa-ban"> Block All</i>
        </button>
    </div>
    <div class="col-md-1 d-flex align-items-end">
        <button id="blockSelectedBtn" class="btn btn-warning w-100">
            <i class="fas fa-check-double"> Block Selected</i>
        </button>
    </div>
</div>

<!-- Tours agrupados por horario -->
@if(isset($groupedTours) && $groupedTours->count())
    <h3 class="mt-4">Bloquear disponibilidad por horario</h3>

    @foreach($groupedTours as $hora => $toursPorHora)
        <div class="card mb-3">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                {{ \Carbon\Carbon::parse($hora)->format('g:i A') }}
                <button class="btn btn-danger btn-sm" onclick="bloquearTodos('{{ \Illuminate\Support\Str::slug($hora) }}')">
                    Bloquear todos
                </button>
            </div>

            <div class="list-group list-group-flush" id="hora-{{ \Illuminate\Support\Str::slug($hora) }}">
                @foreach($toursPorHora as $tour)
                    <div class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                        <div class="form-check">
                            <input class="form-check-input tour-checkbox" type="checkbox"
                                   data-tour-id="{{ $tour['tour_id'] }}"
                                   data-schedule-id="{{ $tour['schedule_id'] ?? '' }}">
                            <label class="form-check-label">{{ $tour['name'] }}</label>
                        </div>
                        <div>
                            <button class="btn btn-outline-warning btn-sm me-2"
                                onclick="bloquearTour('{{ $tour['tour_id'] }}', 'No está en funcionamiento', '{{ $tour['schedule_id'] ?? '' }}')">
                                No está en funcionamiento
                            </button>
                            <button class="btn btn-outline-danger btn-sm"
                                onclick="bloquearTour('{{ $tour['tour_id'] }}', 'Sin disponibilidad', '{{ $tour['schedule_id'] ?? '' }}')">
                                Sin disponibilidad
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
@endif

<!-- Tabla de fechas bloqueadas -->
<table class="table table-bordered table-hover mt-4">
    <thead class="table-dark">
        <tr>
            <th>Tour</th>
            <th>Horario</th>
            <th>Desde</th>
            <th>Hasta</th>
            <th>Motivo</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @forelse($excludedDates as $date)
            <tr>
                <td>{{ optional($date->tour)->name ?? '-' }}</td>
                <td>{{ optional($date->schedule)->start_time ?? '-' }}</td>
                <td>{{ $date->start_date }}</td>
                <td>{{ $date->end_date ?? '-' }}</td>
                <td>{{ $date->reason ?? '-' }}</td>
                <td>
                    <form action="{{ route('admin.tours.excluded_dates.destroy', $date->tour_excluded_date_id) }}"
                          method="POST" class="d-inline form-delete">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center">No hay fechas bloqueadas registradas.</td>
            </tr>
        @endforelse
    </tbody>
</table>

@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: '{{ session('success') }}',
        timer: 2000,
        showConfirmButton: false
    });
</script>
@endif

<script>
    function getGlobalDates() {
        return {
            start: document.getElementById('global_start_date').value,
            end: document.getElementById('global_end_date').value,
            reason: document.getElementById('global_reason').value
        };
    }

    function bloquearTour(tourId, motivo, scheduleId = null) {
        const { start, end, reason } = getGlobalDates();

        if (!start) {
            return Swal.fire({
                icon: 'warning',
                title: 'Falta la fecha de inicio',
                text: 'Debes seleccionar una fecha de inicio'
            });
        }

        fetch("{{ route('admin.tours.excluded_dates.store') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                tour_id: tourId,
                schedule_id: scheduleId,
                start_date: start,
                end_date: end || null,
                reason: motivo || reason || null
            })
        }).then(res => {
            if (res.ok) {
                Swal.fire({
                    icon: 'success',
                    title: 'Tour bloqueado correctamente',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire('Error', 'No se pudo registrar el bloqueo.', 'error');
            }
        });
    }

    function bloquearTodos(horaSlug) {
        const container = document.getElementById(`hora-${horaSlug}`);
        const buttons = container.querySelectorAll('button.btn-outline-danger');
        buttons.forEach(btn => btn.click());
    }

    // Bloqueo total
    document.getElementById('blockAllBtn').addEventListener('click', function () {
        const { start, end, reason } = getGlobalDates();

        if (!start) {
            return Swal.fire({
                icon: 'warning',
                title: 'Falta la fecha de inicio',
                text: 'Debes seleccionar una fecha de inicio'
            });
        }

        fetch("{{ route('admin.tours.excluded_dates.blockAll') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                start_date: start,
                end_date: end || null,
                reason: reason || 'Bloqueo total'
            })
        }).then(res => {
            if (res.ok) {
                Swal.fire('Éxito', 'Todos los tours han sido bloqueados.', 'success')
                    .then(() => location.reload());
            } else {
                Swal.fire('Error', 'No se pudo completar el bloqueo.', 'error');
            }
        });
    });

    // Bloquear seleccionados
    document.getElementById('blockSelectedBtn').addEventListener('click', function () {
        const { start, end, reason } = getGlobalDates();

        if (!start) {
            return Swal.fire({
                icon: 'warning',
                title: 'Falta la fecha de inicio',
                text: 'Debes seleccionar una fecha de inicio'
            });
        }

        const checkboxes = document.querySelectorAll('.tour-checkbox:checked');
        if (checkboxes.length === 0) {
            return Swal.fire({
                icon: 'info',
                title: 'No hay tours seleccionados',
                text: 'Debes seleccionar al menos un tour para bloquear.'
            });
        }

        const promesas = [];

        checkboxes.forEach(checkbox => {
            const tourId = checkbox.getAttribute('data-tour-id');
            const scheduleId = checkbox.getAttribute('data-schedule-id');

            promesas.push(
                fetch("{{ route('admin.tours.excluded_dates.store') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        tour_id: tourId,
                        schedule_id: scheduleId,
                        start_date: start,
                        end_date: end || null,
                        reason: reason || 'Bloqueo múltiple'
                    })
                })
            );
        });

        Promise.all(promesas)
            .then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Selected tours blocked successfully',
                    timer: 2000,
                    showConfirmButton: false
                });
            })
            .catch(() => {
                Swal.fire('Error', 'Some tours could not be blocked.', 'error');
            });
    });

    // Confirmación al eliminar
    document.querySelectorAll('.form-delete').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            Swal.fire({
                title: '¿Eliminar esta fecha bloqueada?',
                text: 'Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then(result => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    });
</script>
@stop
