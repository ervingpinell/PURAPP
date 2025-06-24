{{-- resources/views/admin/bookings/calendar.blade.php --}}
@extends('adminlte::page')

@section('title', 'Calendario de Reservas')

@section('css')
  {{-- FullCalendar CSS --}}
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet"/>
  {{-- Tu CSS personalizado --}}
  <link href="{{ asset('css/calendar.css') }}" rel="stylesheet" />
@stop

@section('content_header')
  <h1>Calendario de Reservas</h1>
@stop

@section('content')
  {{-- Filtros manuales de fechas --}}
  <div class="d-flex justify-content-center mb-3">
    <input type="date" id="filterStart" class="form-control w-auto me-2" />
    <input type="date" id="filterEnd"   class="form-control w-auto me-2" />
    <button id="filterBtn" class="btn btn-primary me-2">Filtrar</button>
    <button id="clearBtn"  class="btn btn-secondary">Limpiar</button>
  </div>

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
      const inputStart = document.getElementById('filterStart');
      const inputEnd   = document.getElementById('filterEnd');
      const filterBtn  = document.getElementById('filterBtn');
      const clearBtn   = document.getElementById('clearBtn');

      const calendarEl = document.getElementById('calendar');
      const calendar = new FullCalendar.Calendar(calendarEl, {
        themeSystem: 'bootstrap',
        initialView: 'dayGridMonth',
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek'
        },
        height: 650,
        aspectRatio: 1.5,
        events: function(fetchInfo, successCallback, failureCallback) {
          const start = inputStart.value || fetchInfo.startStr;
          const end   = inputEnd.value   || fetchInfo.endStr;
          const params = new URLSearchParams({ start, end });
          fetch('{{ route("admin.reservas.calendarData") }}?' + params)
            .then(res => res.json())
            .then(data => successCallback(data))
            .catch(err  => failureCallback(err));
        },
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

      // Botón Filtrar
      filterBtn.addEventListener('click', () => {
        calendar.refetchEvents();
        if (inputStart.value) calendar.gotoDate(inputStart.value);
      });

      // Botón Limpiar
      clearBtn.addEventListener('click', () => {
        inputStart.value = '';
        inputEnd.value   = '';
        calendar.refetchEvents();
      });
    });
  </script>
@stop
