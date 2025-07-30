<div class="collapse {{ request()->hasAny(['reference', 'status', 'booking_date_from', 'booking_date_to', 'tour_date_from', 'tour_date_to', 'tour_id', 'schedule_id']) ? 'show' : '' }}" id="filtrosAvanzados">
  <form method="GET" class="card card-body shadow-sm bg-dark text-white border-0">
    <h5 class="text-center mb-3">Filtros avanzados</h5>

    {{-- 📅 Fechas --}}
    <h6 class="mb-2">Fechas</h6>
    <div class="row mb-4 border-top pt-3">
      <div class="col-md-6">
        <label class="form-label">Reservada desde:</label>
        <input type="date" name="booking_date_from" class="form-control mb-2" value="{{ request('booking_date_from') }}">
        <label class="form-label">Reservada hasta:</label>
        <input type="date" name="booking_date_to" class="form-control" value="{{ request('booking_date_to') }}">
      </div>
      <div class="col-md-6">
        <label class="form-label">Viaje desde:</label>
        <input type="date" name="tour_date_from" class="form-control mb-2" value="{{ request('tour_date_from') }}">
        <label class="form-label">Viaje hasta:</label>
        <input type="date" name="tour_date_to" class="form-control" value="{{ request('tour_date_to') }}">
      </div>
    </div>

    {{-- 🎯 Tour y Horario --}}
    <div class="row mb-4">
      <div class="col-md-6">
        <label class="form-label">Tour:</label>
        <select name="tour_id" class="form-select">
          <option value="">Todos</option>
          @foreach($tours as $t)
            <option value="{{ $t->tour_id }}" {{ request('tour_id') == $t->tour_id ? 'selected' : '' }}>
              {{ $t->name }}
            </option>
          @endforeach
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Horario:</label>
        <select name="schedule_id" class="form-select">
          <option value="">Todos</option>
          @foreach($schedules as $s)
            <option value="{{ $s->schedule_id }}" {{ request('schedule_id') == $s->schedule_id ? 'selected' : '' }}>
              {{ $s->start_time }} - {{ $s->end_time }}
            </option>
          @endforeach
        </select>
      </div>
    </div>

    {{-- Estado y Referencia --}}
    <div class="row mb-4">
      <div class="col-md-6">
        <label class="form-label">Estado:</label>
        <select name="status" class="form-select">
          <option value="">Todos</option>
          <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
          <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
          <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Referencia:</label>
        <input type="text" name="reference" class="form-control" value="{{ request('reference') }}">
      </div>
    </div>

    {{-- Botones filtros --}}
    <div class="d-flex justify-content-end gap-2">
      <button type="submit" class="btn btn-primary">
        <i class="fas fa-check"></i> Aplicar
      </button>

      <a href="{{ route('admin.reservas.index') }}" class="btn btn-light">
        <i class="fas fa-times"></i> Limpiar
      </a>

      <button type="button" class="btn btn-outline-light" id="cerrarFiltrosBtn">
        <i class="fas fa-chevron-up"></i> Cerrar filtros
      </button>
    </div>
  </form>
</div>
