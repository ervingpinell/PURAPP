@extends('adminlte::page')

@section('title', 'Horarios de Tours')

@section('content_header')
  <h1>Gestión de Horarios</h1>
@stop

@section('content')
<div class="p-3">
  <div class="mb-3">
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalRegistrarGeneral">
      <i class="fas fa-plus"></i> Añadir Horario
    </button>
  </div>

  <div class="row">
    @php
      $allTours = \App\Models\Tour::with(['schedules' => function ($q) {
        $q->orderBy('start_time');
      }])->orderBy('tour_id')->get();
    @endphp

    @foreach($allTours as $tour)
      <div class="col-md-6 mb-4">
        <div class="card shadow-sm">
          <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ $tour->name }}</h5>
            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalAgregarHorario{{ $tour->tour_id }}">
              <i class="fas fa-plus"></i> Añadir
            </button>
          </div>
          <div class="card-body">
            @forelse($tour->schedules as $bloque)
              <div class="d-flex justify-content-between align-items-center border rounded p-2 mb-2">
                <div>
                  🕒 {{ \Carbon\Carbon::parse($bloque->start_time)->format('g:i A') }} –
                  {{ \Carbon\Carbon::parse($bloque->end_time)->format('g:i A') }}<br>
                  👥 <strong>{{ $bloque->max_capacity }}</strong> personas<br>
                  <span class="badge {{ $bloque->is_active ? 'bg-success' : 'bg-secondary' }}">
                    {{ $bloque->is_active ? 'Activo' : 'Inactivo' }}
                  </span>
                </div>
                <div>
                  <button class="btn btn-sm btn-warning"
                          data-bs-toggle="modal"
                          data-bs-target="#modalEditar{{ $bloque->schedule_id }}">
                    <i class="fas fa-edit"></i>
                  </button>
                  <form action="{{ route('admin.tours.schedule.toggle', $bloque->schedule_id) }}"
                        method="POST" class="d-inline">
                    @csrf @method('PUT')
                    <button type="submit"
                            class="btn btn-sm {{ $bloque->is_active ? 'btn-danger' : 'btn-success' }}"
                            onclick="return confirm('¿Estás seguro?')">
                      <i class="fas {{ $bloque->is_active ? 'fa-times' : 'fa-check' }}"></i>
                    </button>
                  </form>
                </div>
              </div>

              {{-- Modal Editar --}}
              <div class="modal fade" id="modalEditar{{ $bloque->schedule_id }}" tabindex="-1">
                <div class="modal-dialog">
                  <form action="{{ route('admin.tours.schedule.update', $bloque->schedule_id) }}" method="POST">
                    @csrf @method('PUT')
                    <input type="hidden" name="tour_id" value="{{ $tour->tour_id }}">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title">Editar Horario</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                        <div class="mb-3">
                          <label>Inicio</label>
                          <input type="time" name="start_time" class="form-control"
                                 value="{{ $bloque->start_time }}" required>
                        </div>
                        <div class="mb-3">
                          <label>Fin</label>
                          <input type="time" name="end_time" class="form-control"
                                 value="{{ $bloque->end_time }}" required>
                        </div>
                        <div class="mb-3">
                          <label>Cupo Máximo</label>
                          <input type="number" name="max_capacity" class="form-control"
                                value="{{ $bloque->max_capacity }}" min="1" required>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button class="btn btn-warning">Actualizar</button>
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            @empty
              <span class="text-muted">No hay horarios configurados.</span>
            @endforelse
          </div>
          <div class="card-footer text-muted">
            {{ $tour->schedules->where('is_active', true)->count() }} bloque{{ $tour->schedules->where('is_active', true)->count() !== 1 ? 's' : '' }} activo{{ $tour->schedules->where('is_active', true)->count() !== 1 ? 's' : '' }}
          </div>
        </div>
      </div>

      {{-- Modal Agregar Horario --}}
      <div class="modal fade" id="modalAgregarHorario{{ $tour->tour_id }}" tabindex="-1">
        <div class="modal-dialog">
          <form action="{{ route('admin.tours.schedule.store') }}" method="POST">
            @csrf
            <input type="hidden" name="tour_id" value="{{ $tour->tour_id }}">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Nuevo horario para {{ $tour->name }}</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <div class="mb-3">
                  <label>Inicio</label>
                  <input type="time" name="start_time" class="form-control" required>
                </div>
                <div class="mb-3">
                  <label>Fin</label>
                  <input type="time" name="end_time" class="form-control" required>
                </div>
                <div class="mb-3">
                  <label>Cupo Máximo</label>
                  <input type="number" name="max_capacity" class="form-control" min="1" required>
                </div>
              </div>
              <div class="modal-footer">
                <button class="btn btn-success">Guardar</button>
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    @endforeach
  </div>
</div>

{{-- Modal General --}}
<div class="modal fade" id="modalRegistrarGeneral" tabindex="-1">
  <div class="modal-dialog">
    <form action="{{ route('admin.tours.schedule.store') }}" method="POST">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Registrar Horario</h5>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>Tour</label>
            <select name="tour_id" class="form-control" required>
              <option value="">Seleccione un tour</option>
              @foreach(\App\Models\Tour::orderBy('tour_id')->get() as $t)
                <option value="{{ $t->tour_id }}">{{ $t->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label>Inicio</label>
            <input type="time" name="start_time" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Fin</label>
            <input type="time" name="end_time" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Cupo Máximo</label>
            <input type="number" name="max_capacity" class="form-control" min="1" required>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-primary">Guardar</button>
          <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </form>
  </div>
</div>
@stop

@section('js')
@if(session('success'))
<script>
  Swal.fire({
    icon: 'success',
    title: '¡Éxito!',
    text: '{{ session('success') }}',
    confirmButtonColor: '#28a745'
  });
</script>
@endif
@stop
