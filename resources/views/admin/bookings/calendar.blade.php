{{-- resources/views/admin/bookings/calendar.blade.php --}}
@extends('adminlte::page')

@section('title', 'Calendario de Reservas')

@section('css')
  {{-- FullCalendar CSS --}}
  <link
    href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css"
    rel="stylesheet"
  />
  {{-- Tu CSS personalizado --}}
  <link href="{{ asset('css/calendar.css') }}" rel="stylesheet" />
@stop

@section('content_header')
  <h1>Calendario de Reservas</h1>
@stop

@section('content')
  {{-- Leyenda de estados --}}
  <div class="legend-container mb-4">
    <div class="legend-item">
      <span class="legend-badge pending"></span>
      <span class="legend-text">Pending</span>
    </div>
    <div class="legend-item">
      <span class="legend-badge confirmed"></span>
      <span class="legend-text">Confirmed</span>
    </div>
    <div class="legend-item">
      <span class="legend-badge cancelled"></span>
      <span class="legend-text">Cancelled</span>
    </div>
  </div>

  {{-- Controles de filtro --}}
  <div class="row mb-3 align-items-end">
    <div class="col-sm-3">
      <label for="filter-from" class="form-label">Desde</label>
      <input type="date" id="filter-from" class="form-control">
    </div>
    <div class="col-sm-3">
      <label for="filter-to" class="form-label">Hasta</label>
      <input type="date" id="filter-to" class="form-control">
    </div>
    <div class="col-sm-3">
      <button id="btn-apply" class="btn btn-primary w-100">Aplicar filtro</button>
    </div>
    <div class="col-sm-3">
      <button id="btn-clear" class="btn btn-secondary w-100">Limpiar filtros</button>
    </div>
  </div>

  {{-- Contenedor para FullCalendar --}}
  <div id="calendar"></div>

  {{-- Modal para editar reserva vía AJAX --}}
  <div class="modal fade" id="editBookingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <form id="editBookingForm" method="POST">
        @csrf @method('PUT')
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Editar Reserva</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div id="editFormContainer"></div>
          </div>
        </div>
      </form>
    </div>
  </div>
@stop

@section('js')
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const calendarEl = document.getElementById('calendar');
      let filterFrom = document.getElementById('filter-from');
      let filterTo   = document.getElementById('filter-to');

      // Creamos el calendario, con extraParams para enviar fecha desde/hasta
      const calendar = new FullCalendar.Calendar(calendarEl, {
        themeSystem: 'bootstrap',
        initialView: 'dayGridMonth',
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek'
        },
        contentHeight: 750,
        aspectRatio: 2,
        dayMaxEventRows: 3,

        eventDidMount(info) {
          const colors = {
            pending:   '#f0ad4e',
            confirmed: '#5cb85c',
            cancelled: '#d9534f'
          };
          let c = colors[info.event.extendedProps.status] || '#3788d8';
          info.el.style.backgroundColor = c;
          info.el.style.borderColor     = c;
          info.el.style.cursor          = 'pointer';
        },

        eventContent(arg) {
          let { title, extendedProps } = arg.event;
          let { adults, kids } = extendedProps;
          let container = document.createElement('div');
          container.classList.add('fc-event-custom');

          let titleEl = document.createElement('div');
          titleEl.classList.add('fc-event-title');
          titleEl.innerText = title;
          container.appendChild(titleEl);

          let infoEl = document.createElement('div');
          infoEl.classList.add('fc-event-info');
          infoEl.innerHTML = `
            <span class="badge bg-light text-dark me-1">👤${adults}</span>
            <span class="badge bg-light text-dark">🧒${kids}</span>
          `;
          container.appendChild(infoEl);
          return { domNodes: [container] };
        },

        events: {
          url: '{{ route("admin.reservas.calendarData") }}',
          extraParams: function() {
            return {
              from: filterFrom.value || '',
              to:   filterTo.value   || ''
            };
          }
        },

        eventClick(info) {
          const id        = info.event.id;
          const form      = document.getElementById('editBookingForm');
          const container = document.getElementById('editFormContainer');

          fetch(`/admin/reservas/${id}/edit`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
          })
          .then(r => r.text())
          .then(html => {
            container.innerHTML = html;
            form.action = `/admin/reservas/${id}`;
            new bootstrap.Modal(
              document.getElementById('editBookingModal')
            ).show();
          })
          .catch(console.error);
        }
      });

      calendar.render();

      // Al hacer click en “Aplicar filtro” → recarga eventos
      document.getElementById('btn-apply').addEventListener('click', () => {
        calendar.refetchEvents();
      });

      // “Limpiar filtros”
      document.getElementById('btn-clear').addEventListener('click', () => {
        filterFrom.value = '';
        filterTo.value   = '';
        calendar.refetchEvents();
      });
    });
  </script>
@stop
