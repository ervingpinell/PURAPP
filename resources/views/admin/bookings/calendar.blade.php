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
  {{-- Tippy.js styling (opcional) --}}
  <link href="https://unpkg.com/tippy.js@6/dist/tippy.css" rel="stylesheet" />
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
  <div class="row mb-3 align-items-end justify-content-center">
    <div class="col-sm-2">
      <label for="filter-from" class="form-label">From</label>
      <input type="date" id="filter-from" class="form-control">
    </div>
    <div class="col-sm-2">
      <label for="filter-to" class="form-label">To</label>
      <input type="date" id="filter-to" class="form-control">
    </div>
    <div class="col-sm-2">
      <button id="btn-apply" class="btn btn-primary w-100">Aplicar filtro</button>
    </div>
    <div class="col-sm-2">
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
  {{-- Popper + Tippy --}}
  <script src="https://unpkg.com/@popperjs/core@2"></script>
  <script src="https://unpkg.com/tippy.js@6"></script>
  {{-- FullCalendar --}}
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const calendarEl = document.getElementById('calendar');
      const filterFrom = document.getElementById('filter-from');
      const filterTo   = document.getElementById('filter-to');
      const btnApply   = document.getElementById('btn-apply');
      const btnClear   = document.getElementById('btn-clear');
      const btnExport  = document.getElementById('btn-export');
      const titleEl    = () => document.querySelector('.fc-toolbar-title');

      // Inicializamos FullCalendar
      const calendar = new FullCalendar.Calendar(calendarEl, {
        themeSystem: 'bootstrap',
        initialView: 'dayGridMonth',
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek'
        },
        contentHeight: 700,
        aspectRatio: 1.5,
        dayMaxEventRows: 3,

        // Colorea y tooltip con Tippy.js
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

          // Tooltip
          let { title, extendedProps } = info.event;
          let adults = extendedProps.adults;
          let kids   = extendedProps.kids;
          let total  = extendedProps.total;
          tippy(info.el, {
            allowHTML: true,
            theme: 'light-border',
            delay: [100,50],
            content: `
              <strong>${title}</strong><br>
              👤 Adultos: ${adults}<br>
              🧒 Niños: ${kids}<br>
              💰 Total: $${(parseFloat(total)||0).toFixed(2)}
            `
          });
        },

        // Renderizado custom (título + badges)
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

        // Carga eventos pasando filtro
        events: {
          url: '{{ route("admin.reservas.calendarData") }}',
          extraParams: () => ({
            from: filterFrom.value || '',
            to:   filterTo.value   || ''
          })
        },

        // Click abre modal
        eventClick(info) {
          const id        = info.event.id;
          const form      = document.getElementById('editBookingForm');
          const container = document.getElementById('editFormContainer');
          fetch(`/admin/reservas/${id}/edit`, {
            headers: {'X-Requested-With':'XMLHttpRequest'}
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

      // Actualiza el título según filtros
      function updateTitle() {
        if (filterFrom.value && filterTo.value) {
          titleEl().innerText = `${filterFrom.value} → ${filterTo.value}`;
        } else {
          // Restablece al mes actual que maneja FullCalendar
          titleEl().innerText = calendar.view.title;
        }
      }

      // Aplicar + limpiar filtros
      btnApply.onclick = () => {
        calendar.refetchEvents();
        updateTitle();
      };
      btnClear.onclick = () => {
        filterFrom.value = '';
        filterTo.value   = '';
        calendar.refetchEvents();
        updateTitle();
      };
    });
  </script>
@stop
