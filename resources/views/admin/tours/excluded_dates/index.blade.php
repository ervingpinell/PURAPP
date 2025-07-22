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
                        <strong>{{ $tour['name'] }}</strong>
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
                <td colspan="5" class="text-center">No hay fechas bloqueadas registradas.</td>
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
                    Swal.fire('Bloqueado', 'Se ha bloqueado el tour correctamente.', 'success')
                        .then(() => location.reload());
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
