@extends('adminlte::page')

@section('title', 'Bookings Calendar')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet" />
<link href="{{ asset('css/calendar.css') }}" rel="stylesheet" />
<link href="https://unpkg.com/tippy.js@6/dist/tippy.css" rel="stylesheet" />
@stop

@section('content_header')
<h1>Bookings Calendar</h1>
@stop

@section('content')
{{-- Filters --}}
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
      <option value="">All</option>
      @foreach ($tours as $tour)
      <option value="{{ $tour->product_id }}">{{ Str::limit($tour->name, 30) }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-sm-2">
    <button id="btn-apply" class="btn btn-primary w-100">Apply filter</button>
  </div>
  <div class="col-sm-2">
    <button id="btn-clear" class="btn btn-secondary w-100">Clear filters</button>
  </div>
</div>

{{-- Calendar --}}
<div id="calendar"></div>

{{-- Modal to edit booking --}}
<div class="modal fade" id="bookingModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form id="bookingModalForm" method="POST">
      @csrf @method('PUT')
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Booking</h5>
          <button type="button" class="close" data-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="bookingModalContent">
          <div class="text-center p-3">
            <div class="spinner-border"></div>
            <p class="mt-2">Loading...</p>
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
  document.addEventListener('DOMContentLoaded', function() {
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
        dayGridMonth: {
          dayMaxEvents: 2
        },
        timeGridDay: {
          slotLabelFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
          }
        }
      },
      events: {
        url: '{{ route("admin.bookings.calendarData") }}',
        extraParams: () => ({
          from: filterFrom.value || '',
          to: filterTo.value || '',
          product_id: filterTour.value || ''
        })
      },

      dateClick(info) {
        if (calendar.view.type === 'dayGridMonth') {
          calendar.changeView('timeGridDay', info.dateStr);
        }
      },

      eventClick(info) {
        const bookingId = info.event.id;
        if (bookingId) {
          const modal = new bootstrap.Modal(document.getElementById('bookingModal'));
          modal.show();

          document.getElementById('bookingModalContent').innerHTML = `
              <div class="text-center p-3">
                <div class="spinner-border"></div>
                <p class="mt-2">Loading...</p>
              </div>
            `;

          fetch(`/admin/bookings/${bookingId}/edit`, {
              headers: {
                'X-Requested-With': 'XMLHttpRequest'
              }
            })
            .then(response => response.text())
            .then(html => {
              document.getElementById('bookingModalContent').innerHTML = html;
              document.getElementById('bookingModalForm').action = `/admin/bookings/${bookingId}`;
            })
            .catch(err => {
              console.error(err);
              document.getElementById('bookingModalContent').innerHTML = `<p class="text-danger">Error loading form.</p>`;
            });
        }
      },

      eventContent(arg) {
        const paxText = arg.event.extendedProps.pax || '—';
        const tourName = arg.event.extendedProps.short_tour_name || '';
        const bookingRef = arg.event.extendedProps.booking_ref || '';
        const hotel = arg.event.extendedProps.hotel_name || '';
        const status = arg.event.extendedProps.status || 'pending';

        const statusEmoji = {
          confirmed: 'OK',
          pending: 'WARNING',
          cancelled: 'ERROR'
        };
        const statusText = `${statusEmoji[status] ?? ''} ${status}`;

        const container = document.createElement('div');
        container.classList.add('fc-event-custom-content');
        container.innerHTML = `
            <div style="display: flex; justify-content: space-between; font-size: 0.75em; margin-bottom: 2px;">
              <div><strong>${paxText}</strong></div>
              <div><strong>${statusText}</strong></div>
            </div>
            <div>${tourName}</div>
            <div>${hotel}</div>
            <div style="font-size: 0.85em">${bookingRef}</div>
          `;
        return {
          domNodes: [container]
        };
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
      calendar.removeAllEvents(); // clear current events
      calendar.refetchEvents(); // call backend again
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