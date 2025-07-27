@extends('adminlte::page')

@section('title', 'Calendario de Reservas')

@section('css')
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet"/>
  <link href="{{ asset('css/calendar.css') }}" rel="stylesheet"/>
  <link href="https://unpkg.com/tippy.js@6/dist/tippy.css" rel="stylesheet"/>
@stop

@section('content_header')
  <h1>Calendario de Reservas</h1>
@stop

@section('content')
  {{-- ✅ Filtros --}}
  <div class="row mb-3 align-items-end justify-content-center">
    <div class="col-sm-2">
      <label for="filter-from" class="form-label">From</label>
      <input type="date" id="filter-from" class="form-control">
    </div>
    <div class="col-sm-2">
      <label for="filter-to" class="form-label">To</label>
      <input type="date" id="filter-to" class="form-control">
    </div>
    <div class="col-sm-3">
      <label for="filter-tour" class="form-label">Tour</label>
      <select id="filter-tour" class="form-select">
        <option value="">Todos</option>
        @foreach ($tours as $tour)
          <option value="{{ $tour->id }}">{{ Str::limit($tour->name, 30) }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-sm-2">
      <button id="btn-apply" class="btn btn-primary w-100">Aplicar filtro</button>
    </div>
    <div class="col-sm-2">
      <button id="btn-clear" class="btn btn-secondary w-100">Limpiar filtros</button>
    </div>
  </div>

  {{-- ✅ Calendario --}}
  <div id="calendar"></div>

  {{-- ✅ Modal reutilizable --}}
  <div class="modal fade" id="bookingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <form id="bookingModalForm" method="POST">
        @csrf @method('PUT')
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Editar Reserva</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body" id="bookingModalContent">
            <div class="text-center p-3">
              <div class="spinner-border"></div>
              <p class="mt-2">Cargando...</p>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
@stop

@section('js')
<script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="https://unpkg.com/tippy.js@6"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    const filterFrom = document.getElementById('filter-from');
    const filterTo = document.getElementById('filter-to');
    const filterTour = document.getElementById('filter-tour');
    const btnApply = document.getElementById('btn-apply');
    const btnClear = document.getElementById('btn-clear');

    const calendar = new FullCalendar.Calendar(calendarEl, {
      themeSystem: 'bootstrap',
      initialView: 'dayGridMonth',
      height: 'auto',
      nowIndicator: true,
      slotEventOverlap: false,
      eventMaxStack: 4,
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
          to: filterTo.value || '',
          tour_id: filterTour.value || ''
        })
      },

      dateClick(info) {
        if (calendar.view.type === 'dayGridMonth') {
          calendar.changeView('timeGridDay', info.dateStr);
        }
      },

      eventClick: function (info) {
        const bookingId = info.event.id;
        if (bookingId) {
          const modal = new bootstrap.Modal(document.getElementById('bookingModal'));
          modal.show();

          document.getElementById('bookingModalContent').innerHTML = `
            <div class="text-center p-3">
              <div class="spinner-border"></div>
              <p class="mt-2">Cargando...</p>
            </div>
          `;

          fetch(`/admin/reservas/${bookingId}/edit`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
          })
            .then(response => response.text())
            .then(html => {
              document.getElementById('bookingModalContent').innerHTML = html;
              document.getElementById('bookingModalForm').action = `/admin/reservas/${bookingId}`;
            })
            .catch(err => {
              console.error(err);
              document.getElementById('bookingModalContent').innerHTML = `<p class="text-danger">Error al cargar el formulario.</p>`;
            });
        }
      },

      eventContent(arg) {
        const paxText = arg.event.extendedProps.pax || '—';
        const tourName = arg.event.extendedProps.short_tour_name || '';
        const bookingRef = arg.event.extendedProps.booking_ref || '';
        const hotel = arg.event.extendedProps.hotel_name || '';
        const status = arg.event.extendedProps.status || 'pending';

        const estadoEmoji = {
          confirmed: '✅',
          pending: '⚠️',
          cancelled: '❌'
        };
        const estadoTexto = `${estadoEmoji[status] ?? ''} ${status}`;

        const container = document.createElement('div');
        container.classList.add('fc-event-custom-content');
        container.innerHTML = `
          <div style="display: flex; justify-content: space-between; font-size: 0.75em; margin-bottom: 2px;">
            <div><strong>${paxText}</strong></div>
            <div><strong>${estadoTexto}</strong></div>
          </div>
          <div>${tourName}</div>
          <div>${hotel}</div>
          <div style="font-size: 0.85em">${bookingRef}</div>
        `;
        return { domNodes: [container] };
      },

      eventDidMount(info) {
        info.el.style.color = '#000';
        info.el.style.border = '1px solid #ddd';
        tippy(info.el, {
          allowHTML: true,
          theme: 'light-border',
          content: `
            <div><strong>${info.event.extendedProps.pax}</strong></div>
            <div>${info.event.extendedProps.short_tour_name}</div>
            <div>${info.event.extendedProps.hotel_name}</div>
            <div>${info.event.extendedProps.booking_ref}</div>
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

    btnApply.onclick = () => {
      calendar.refetchEvents();
      updateTitle();
    };
    btnClear.onclick = () => {
      filterFrom.value = '';
      filterTo.value = '';
      filterTour.value = '';
      calendar.refetchEvents();
      updateTitle();
    };
  });
</script>
@stop
