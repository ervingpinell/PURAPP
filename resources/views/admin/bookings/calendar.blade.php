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
    <div class="legend-container">
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
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Editar Reserva</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              {{-- Aquí se inyectará el partial con el formulario --}}
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
        let calendar = new FullCalendar.Calendar(
          document.getElementById('calendar'),
          {
            initialView: 'dayGridMonth',
            headerToolbar: {
              left: 'prev,next today',
              center: 'title',
              right: 'dayGridMonth,timeGridWeek'
            },
            height: 650,
            aspectRatio: 1.5,
            eventDidMount(info) {
              // asigna color según status
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
            events: '{{ route("admin.reservas.calendarData") }}',
            eventClick(info) {
              let id        = info.event.id;
              let form      = document.getElementById('editBookingForm');
              let container = document.getElementById('editFormContainer');
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
          }
        );
        calendar.render();
      });
    </script>
@stop
