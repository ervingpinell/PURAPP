@extends('adminlte::page')

@section('title', 'Fechas Bloqueadas')

@section('content_header')
    <h1><i class="fas fa-calendar-times"></i> Fechas Bloqueadas de Tours</h1>
@stop

@section('content')

    <!-- Mensaje de error (opcional) -->
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Formulario para agregar -->
    <form action="{{ route('admin.tours.excluded_dates.store') }}" method="POST" class="row g-3 mb-4">
        @csrf

        <div class="col-md-3">
            <label for="tour_id">Tour</label>
            <select name="tour_id" class="form-control" required>
                <option value="">Seleccione un tour</option>
                @foreach($tours as $tour)
                    <option value="{{ $tour->tour_id }}">{{ $tour->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <label>Fecha inicio</label>
            <input type="date" name="start_date" class="form-control" required>
        </div>

        <div class="col-md-2">
            <label>Fecha fin</label>
            <input type="date" name="end_date" class="form-control">
        </div>

        <div class="col-md-3">
            <label>Motivo</label>
            <input type="text" name="reason" class="form-control" placeholder="Opcional">
        </div>

        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-success w-100">
                <i class="fas fa-plus"></i> Agregar
            </button>
        </div>
    </form>

    <!-- Tabla -->
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>Tour</th>
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
                    <td>{{ $date->start_date }}</td>
                    <td>{{ $date->end_date ?? '-' }}</td>
                    <td>{{ $date->reason ?? '-' }}</td>
                    <td>
                        <form action="{{ route('admin.tours.excluded_dates.destroy', $date->tour_excluded_date_id) }}"
                            method="POST"
                            class="d-inline form-delete">
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
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Éxito (flash) -->
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

    <!-- Confirmación al eliminar -->
    <script>
        document.querySelectorAll('.form-delete').forEach(form => {
            form.addEventListener('submit', function(e) {
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
