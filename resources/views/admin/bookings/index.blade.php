@extends('adminlte::page')

@section('title', 'Reservas')

@section('content_header')
  <h1>Gestión de Reservas</h1>
@stop

@section('content')
@php
  $filtrosActivos = request()->hasAny([
    'reference', 'status',
    'booking_date_from', 'booking_date_to',
    'tour_date_from', 'tour_date_to',
    'tour_id', 'schedule_id'
  ]);
@endphp

<div class="container-fluid">

  {{-- 🟩 Botones superiores --}}
  <div class="mb-3 row align-items-end">
    <div class="col-md-auto mb-2">
      <a href="#" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalRegistrar">
        <i class="fas fa-plus"></i> Añadir Reserva
      </a>
    </div>

    <div class="col-md-auto mb-2">
      <a href="{{ route('admin.reservas.pdf') }}" class="btn btn-danger">
        <i class="fas fa-file-pdf"></i> Descargar PDF
      </a>
    </div>

    <div class="col-md-auto mb-2">
      <a href="{{ route('admin.reservas.excel', request()->query()) }}" class="btn btn-success">
        <i class="fas fa-file-excel"></i> Exportar Excel
      </a>
    </div>

    {{-- 🔍 Filtro rápido de referencia --}}
    <div class="col-md-3 mb-2">
      <form method="GET" action="{{ route('admin.reservas.index') }}">
        <div class="input-group">
          <input type="text" name="reference" class="form-control" placeholder="Buscar referencia..." value="{{ request('reference') }}">
          <button class="btn btn-outline-secondary" type="submit">
            <i class="fas fa-search"></i>
          </button>
        </div>
      </form>
    </div>

    <div class="col-md-auto text-end mb-2">
      <button class="btn btn-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#filtrosAvanzados" aria-expanded="{{ $filtrosActivos ? 'true' : 'false' }}">
        <i class="fas fa-filter"></i> Filtros avanzados
      </button>
    </div>
  </div>

  {{-- 🧪 Filtros avanzados --}}
  @include('admin.bookings.partials.filtros-avanzados')

  {{-- 📋 Tabla --}}
  <div class="table-responsive mt-4">
    @include('admin.bookings.partials.table')
  </div>
</div>

{{-- ✨ Modales --}}
@include('admin.bookings.partials.modal-register')
@foreach ($bookings as $reserva)
  @include('admin.bookings.partials.modal-edit', ['reserva' => $reserva])
@endforeach
@stop

@push('js')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const cerrarBtn = document.getElementById('cerrarFiltrosBtn');
    const filtros = document.getElementById('filtrosAvanzados');

    if (cerrarBtn && filtros) {
      cerrarBtn.addEventListener('click', () => {
        new bootstrap.Collapse(filtros, { toggle: true });
      });
    }
  });
</script>
@include('admin.bookings.partials.scripts')
@endpush
<script>
document.addEventListener('shown.bs.modal', (ev) => {
  const modalEl = ev.target;
  if (!modalEl.id || !modalEl.id.startsWith('modalEditar')) return;

  // --- Tour / Schedule (scoped al modal) ---
  const tourSel = modalEl.querySelector('select[name="tour_id"]');
  const schSel  = modalEl.querySelector('select[name="schedule_id"]');

  if (tourSel && schSel && tourSel.dataset.bound !== '1') {
    tourSel.dataset.bound = '1';

    tourSel.addEventListener('change', () => {
      const opt = tourSel.options[tourSel.selectedIndex];
      const schedules = JSON.parse(opt?.dataset?.schedules || '[]');

      schSel.innerHTML = '<option value="">Seleccione horario</option>';
      schedules.forEach(s => {
        const o = document.createElement('option');
        o.value = s.schedule_id;
        o.textContent = `${s.start_time} – ${s.end_time}`;
        schSel.appendChild(o);
      });

      // obligar a elegir un horario válido del tour nuevo
      schSel.value = '';
    });
  }

  // --- Hotel "Otro…" (scoped al modal) ---
  const hotelSel       = modalEl.querySelector('select[name="hotel_id"]');
  const otherWrap      = modalEl.querySelector('[data-role="other-hotel-wrapper"]');
  const otherInput     = modalEl.querySelector('input[name="other_hotel_name"]');
  const isOtherHidden  = modalEl.querySelector('input[name="is_other_hotel"]');

  const toggleOther = () => {
    if (!hotelSel) return;
    if (hotelSel.value === 'other') {
      otherWrap?.classList.remove('d-none');
      if (isOtherHidden) isOtherHidden.value = 1;
    } else {
      otherWrap?.classList.add('d-none');
      if (otherInput) otherInput.value = '';
      if (isOtherHidden) isOtherHidden.value = 0;
    }
  };

  if (hotelSel && hotelSel.dataset.bound !== '1') {
    hotelSel.dataset.bound = '1';
    hotelSel.addEventListener('change', toggleOther);
    // Por si el modal abre con "other" seleccionado:
    toggleOther();
  }
});
</script>
