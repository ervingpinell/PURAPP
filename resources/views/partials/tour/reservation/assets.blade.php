@push('scripts')
<script>
(function() {
  // Prevenir inicialización doble
  if (window.__gvReservationInit) return;
  window.__gvReservationInit = true;

  const form = document.querySelector('.reservation-box');
  if (!form) return;

  const categoriesJson = form.getAttribute('data-categories');
  const maxTotal = parseInt(form.getAttribute('data-max-total') || '12');

  let categories = [];
  try {
    categories = JSON.parse(categoriesJson || '[]');
  } catch(e) {
    console.error('Error parsing form categories:', e);
    return;
  }

  // Configuración de fechas/horarios (si existe rulesPayload)
  @if(isset($rulesPayload))
  const rulesPayload = @json($rulesPayload);

  const dateInput = document.getElementById('tourDateInput');
  const scheduleSelect = document.getElementById('scheduleSelect');

  if (dateInput && scheduleSelect && window.flatpickr) {
    let fp = flatpickr(dateInput, {
      dateFormat: 'd/m/Y',
      minDate: rulesPayload.initialMin || 'today',
      disable: [],
    });

    scheduleSelect.addEventListener('change', function() {
      const sid = this.value;
      if (!sid) return;

      const rule = rulesPayload.schedules[sid] || rulesPayload.tour;
      if (fp && rule.min) {
        fp.set('minDate', rule.min);
        fp.clear();
      }
    });
  }
  @endif

  // Manejo de Hotel/Meeting Point
  const hotelSelect = document.getElementById('hotelSelect');
  const otherHotelWrapper = document.getElementById('otherHotelWrapper');
  const otherHotelInput = document.getElementById('otherHotelInput');
  const isOtherHotelInput = document.getElementById('isOtherHotel');
  const outsideMessage = document.getElementById('outsideAreaMessage');

  const meetingPointSelect = document.getElementById('meetingPointSelect');
  const meetingPointInfo = document.getElementById('meetingPointInfo');

  if (hotelSelect && otherHotelWrapper) {
    hotelSelect.addEventListener('change', function() {
      const isOther = this.value === 'other';
      otherHotelWrapper.classList.toggle('d-none', !isOther);
      if (isOtherHotelInput) isOtherHotelInput.value = isOther ? '1' : '0';
      if (outsideMessage) outsideMessage.style.display = isOther ? 'block' : 'none';
      if (isOther && otherHotelInput) otherHotelInput.focus();
    });
  }

  if (meetingPointSelect && meetingPointInfo) {
    meetingPointSelect.addEventListener('change', function() {
      const option = this.options[this.selectedIndex];
      if (!this.value) {
        meetingPointInfo.classList.add('d-none');
        return;
      }

      const desc = option.getAttribute('data-desc') || '';
      const time = option.getAttribute('data-time') || '';
      const url  = option.getAttribute('data-url') || '';

      meetingPointInfo.classList.remove('d-none');
      document.getElementById('mpDesc').textContent = desc;
      document.getElementById('mpTime').textContent = time ? `⏰ ${time}` : '';

      const link = document.getElementById('mpLink');
      if (url) {
        link.href = url;
        link.classList.remove('d-none');
      } else {
        link.classList.add('d-none');
      }
    });
  }

  // Validación al submit
  form.addEventListener('submit', function(e) {
    let hasError = false;
    let errorMsg = '';

    // Validar categorías
    let totalPax = 0;
    const quantities = {};

    categories.forEach(cat => {
      const input = document.querySelector(`.category-input[data-category-id="${cat.id}"]`);
      if (!input) return;

      const qty = parseInt(input.value || '0');
      quantities[cat.id] = qty;
      totalPax += qty;

      if (qty < cat.min) {
        hasError = true;
        const tmpl = @json(__('adminlte::adminlte.min_category_required', ['category' => ':category', 'min' => ':min']));
        errorMsg = (tmpl || ':category requires at least :min')
          .replace(':category', cat.label || cat.slug) // 👉 usar nombre traducido
          .replace(':min', cat.min);
      }

      if (qty > cat.max) {
        hasError = true;
        const tmpl = @json(__('adminlte::adminlte.max_category_exceeded', ['category' => ':category', 'max' => ':max']));
        errorMsg = (tmpl || ':category accepts up to :max')
          .replace(':category', cat.label || cat.slug) // 👉 usar nombre traducido
          .replace(':max', cat.max);
      }
    });

    if (totalPax > maxTotal) {
      hasError = true;
      errorMsg = (@json(__('adminlte::adminlte.max_persons_exceeded', ['max' => ':max'])) || 'Max persons: :max')
        .replace(':max', maxTotal);
    }

    if (totalPax < 1) {
      hasError = true;
      errorMsg = @json(__('adminlte::adminlte.min_one_person')) ?? 'Debe haber al menos una persona';
    }

    if (hasError) {
      e.preventDefault();
      alert(errorMsg);
      return false;
    }

    return true;
  });
})();
</script>
@endpush
