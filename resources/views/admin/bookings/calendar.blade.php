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

    const calendar = new FullCalendar.Calendar(calendarEl, {
      themeSystem: 'bootstrap',
      initialView: 'timeGridWeek',
      slotMinTime: '06:00:00',
      slotMaxTime: '18:00:00',
      nowIndicator: true,
      slotEventOverlap: false,
      expandRows: true,
      height: 'auto',
      contentHeight: 'auto',
      aspectRatio: 1.5,

      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
      },

      views: {
        dayGridMonth: {
          dayMaxEvents: 2         // Muestra solo 2 eventos y el resto en +n more
        },
        timeGridWeek: {
          dayHeaderFormat: { weekday: 'short', day: 'numeric' },
          slotLabelFormat: { hour: '2-digit', minute: '2-digit', hour12: false }
        }
      },


      events: {
        url: '{{ route("admin.reservas.calendarData") }}',
        extraParams: () => ({
          from: filterFrom.value || '',
          to:   filterTo.value   || ''
        })
      },

        // 👉 Correcto: CLICK EN EL DÍA
        dateClick: function(info) {
          if (calendar.view.type === 'dayGridMonth') {
            const clickedDate = info.dateStr;
            const allEvents = calendar.getEvents();
            const eventsThatDay = allEvents.filter(ev =>
              ev.startStr.slice(0, 10) === clickedDate
            );

            const modal = document.getElementById('editBookingModal');
            const modalTitle = modal.querySelector('.modal-title');
            const container = document.getElementById('editFormContainer');

            // LIMPIA
            modalTitle.innerText = `Reservas del ${clickedDate}`;
            container.innerHTML = '';

            if (eventsThatDay.length === 0) {
              container.innerHTML = '<p class="text-center">No hay reservas para este día.</p>';
            } else {
              eventsThatDay.forEach(ev => {
                container.innerHTML += `
                  <div class="p-2 border-bottom">
                    <strong>${ev.title}</strong><br>
                    Hotel: ${ev.extendedProps.hotel || ''}<br>
                    Adultos: ${ev.extendedProps.adults} – Niños: ${ev.extendedProps.kids}<br>
                    Total: $${parseFloat(ev.extendedProps.total).toFixed(2)}<br>
                    Estado: ${ev.extendedProps.status}
                  </div>
                `;
              });
            }

            bootstrap.Modal.getOrCreateInstance(modal).show();
          }
        },

        // 👉 Correcto: CLICK EN EVENTO INDIVIDUAL
        eventClick: function(info) {
          const id = info.event.id;
          const modal = document.getElementById('editBookingModal');
          const modalTitle = modal.querySelector('.modal-title');
          const container = document.getElementById('editFormContainer');
          const form = document.getElementById('editBookingForm');

          // LIMPIA
          modalTitle.innerText = '';
          container.innerHTML = '';

          fetch(`/admin/reservas/${id}/edit`, {
            headers: {'X-Requested-With':'XMLHttpRequest'}
          })
          .then(r => r.text())
          .then(html => {
            container.innerHTML = html;
            form.action = `/admin/reservas/${id}`;
            modalTitle.innerText = info.event.title;

            bootstrap.Modal.getOrCreateInstance(modal).show();
          })
          .catch(console.error);
        },

        eventDidMount(info) {
          const colors = {
            pending: '#f0ad4e',
            confirmed: '#5cb85c',
            cancelled: '#d9534f'
          };
          const c = colors[info.event.extendedProps.status] || '#3788d8';
          info.el.style.backgroundColor = c;
          info.el.style.borderColor     = c;
          info.el.style.padding         = '5px';
          info.el.style.fontSize        = '0.9rem';
          info.el.style.borderRadius    = '6px';

          tippy(info.el, {
            allowHTML: true,
            theme: 'light-border',
            delay: [100, 50],
            content: `
              <strong>${info.event.title}</strong><br>
              🏨 Hotel: ${info.event.extendedProps.hotel || ''}<br>
              👤 Adultos: ${info.event.extendedProps.adults}<br>
              🧒 Niños: ${info.event.extendedProps.kids}<br>
              💰 Total: $${parseFloat(info.event.extendedProps.total).toFixed(2)}
            `
          });
        },

        eventContent(arg) {
          const { title, extendedProps } = arg.event;
          const container = document.createElement('div');
          container.classList.add('fc-event-custom');
          container.style.whiteSpace = 'normal';

          const line1 = document.createElement('div');
          line1.innerHTML = `<strong>${title}</strong>`;

          const line2 = document.createElement('div');
          line2.innerText = `🏨 ${extendedProps.hotel || ''}`;

          const info = document.createElement('div');
          info.innerHTML = `
            <span class="badge bg-light text-dark me-1">👤${extendedProps.adults}</span>
            <span class="badge bg-light text-dark me-1">🧒${extendedProps.kids}</span>
            <span class="badge bg-light text-dark">💵$${parseFloat(extendedProps.total).toFixed(2)}</span>
          `;

          container.append(line1, line2, info);
          return { domNodes: [container] };
        }
      });

      calendar.render();

      function updateTitle() {
        const titleEl = document.querySelector('.fc-toolbar-title');
        if (filterFrom.value && filterTo.value) {
          titleEl.innerText = `${filterFrom.value} → ${filterTo.value}`;
        } else {
          titleEl.innerText = calendar.view.title;
        }
      }

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
