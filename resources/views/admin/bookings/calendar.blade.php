@extends('adminlte::page')

@section('title', 'Calendario de Reservas')

@section('css')
  {{-- FullCalendar & Tippy --}}
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet"/>
  <link href="{{ asset('css/calendar.css') }}" rel="stylesheet"/>
  <link href="https://unpkg.com/tippy.js@6/dist/tippy.css" rel="stylesheet"/>
@stop

@section('content_header')
  <h1>Calendario de Reservas</h1>
@stop

@section('content')
  {{-- Leyenda --}}
  <div class="legend-container mb-4">
    <div class="legend-item"><span class="legend-badge pending"></span>Pending</div>
    <div class="legend-item"><span class="legend-badge confirmed"></span>Confirmed</div>
    <div class="legend-item"><span class="legend-badge cancelled"></span>Cancelled</div>
  </div>

  {{-- Filtros --}}
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

  {{-- Calendario --}}
  <div id="calendar"></div>

{{-- ✅ Dentro de tu Blade --}}
<div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="bookingModalForm" method="POST">
        @csrf @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title" id="bookingModalLabel">Editar Reserva</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="bookingModalContent">
          {{-- Se carga tu partial aquí --}}
          <div class="text-center p-3">
            <div class="spinner-border" role="status"></div>
            <p class="mt-2">Cargando...</p>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
@stop

@section('js')
  <script src="https://unpkg.com/@popperjs/core@2"></script>
  <script src="https://unpkg.com/tippy.js@6"></script>
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
        initialView: 'dayGridMonth',
        slotEventOverlap: false,
        eventMaxStack: 4,
        nowIndicator: true,
        height: 'auto',
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridDay'
        },
        views: {
          dayGridMonth: { dayMaxEvents: 2 },
          timeGridDay: {
            slotLabelFormat: { hour: '2-digit', minute: '2-digit', hour12: false }
          }
        },
        events: {
          url: '{{ route("admin.reservas.calendarData") }}',
          extraParams: () => ({
            from: filterFrom.value || '',
            to: filterTo.value || ''
          })
        },

        dateClick(info) {
          if (calendar.view.type === 'dayGridMonth') {
            calendar.changeView('timeGridDay', info.dateStr);
          }
        },

        eventClick: function(info) {
          const bookingId = info.event.id;
          if (bookingId) {
            // Limpia y muestra spinner
            document.getElementById('bookingModalContent').innerHTML = `
              <div class="text-center p-3">
                <div class="spinner-border" role="status"></div>
                <p class="mt-2">Cargando...</p>
              </div>
            `;
            const modal = new bootstrap.Modal(document.getElementById('bookingModal'));
            modal.show();

            // Cargar HTML parcial de edición
  fetch(`/admin/reservas/${bookingId}/edit`, { headers: {'X-Requested-With':'XMLHttpRequest'} })
              .then(response => response.text())
              .then(html => {
                document.getElementById('bookingModalContent').innerHTML = html;
              })
              .catch(err => {
                console.error(err);
                document.getElementById('bookingModalContent').innerHTML = `<p class="text-danger">Error al cargar el formulario.</p>`;
              });
          }
        },

        eventContent(arg) {
          const adults = arg.event.extendedProps.adults || 0;
          const kids   = arg.event.extendedProps.kids || 0;
          const totalPax = `${adults}+${kids} pax`;

          const tourName = arg.event.title.split('–')[1]?.trim() || arg.event.title;
          const hotel = arg.event.extendedProps.hotel || '';

          const container = document.createElement('div');
          container.style.fontSize = '1rem';
          container.style.color = '#000';
          container.innerHTML = `
            <div><strong>${totalPax}</strong> ${tourName}</div>
            <div style="font-size:0.9rem">${hotel}</div>
          `;
          return { domNodes: [container] };
        },

        eventDidMount(info) {
          if (info.event.backgroundColor) {
            info.el.style.backgroundColor = info.event.backgroundColor;
            info.el.style.borderColor = info.event.backgroundColor;
          }

          info.el.style.color = '#000';
          info.el.style.border = '1px solid #ddd';

          const bgColor = info.event.backgroundColor || '';
          const isLight = (hex) => {
            if (!hex) return false;
            const c = hex.replace('#','');
            const r = parseInt(c.substr(0,2),16);
            const g = parseInt(c.substr(2,2),16);
            const b = parseInt(c.substr(4,2),16);
            return (r*0.299 + g*0.587 + b*0.114) > 186;
          };
          if (isLight(bgColor)) {
            info.el.style.textShadow = '0 0 2px #000';
          }

          tippy(info.el, {
            allowHTML: true,
            theme: 'light-border',
            content: `
              <strong>${info.event.title}</strong><br>
              👤 Adultos: ${info.event.extendedProps.adults}<br>
              🧒 Niños: ${info.event.extendedProps.kids}<br>
              🏨 Hotel: ${info.event.extendedProps.hotel || ''}
            `
          });
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

      btnApply.onclick = () => { calendar.refetchEvents(); updateTitle(); };
      btnClear.onclick = () => {
        filterFrom.value = ''; filterTo.value = '';
        calendar.refetchEvents(); updateTitle();
      };
    });
  </script>
@stop
