<script>
// === Mostrar/Ocultar campo 'Otro hotel' ===
document.addEventListener('DOMContentLoaded', () => {
  const modal = document.getElementById('modalRegistrar');

  // === Mostrar u ocultar campo otro hotel al seleccionar 'Otro...'
  const hotelSelect = modal.querySelector('#selectHotel');
  const otherHotelDiv = modal.querySelector('#otherHotelRegistrarWrapper');
  const isOtherInput = modal.querySelector('#isOtherHotelRegistrar');

  if (hotelSelect) {
    hotelSelect.addEventListener('change', () => {
      if (hotelSelect.value === 'other') {
        otherHotelDiv.classList.remove('d-none');
        isOtherInput.value = 1;
      } else {
        otherHotelDiv.classList.add('d-none');
        otherHotelDiv.querySelector('input').value = '';
        isOtherInput.value = 0;
      }
    });
  }

  // === Cálculo del total según cantidad y precio
  function calcularTotal(modal) {
    const adultos = parseInt(modal.querySelector('.cantidad-adultos')?.value || 0);
    const ninos   = parseInt(modal.querySelector('.cantidad-ninos')?.value || 0);
    const precioA = parseFloat(modal.querySelector('.precio-adulto')?.value || 0);
    const precioN = parseFloat(modal.querySelector('.precio-nino')?.value || 0);
    const total   = (adultos * precioA) + (ninos * precioN);
    const totalInput = modal.querySelector('.total-pago');
    if (totalInput) totalInput.value = total.toFixed(2);
  }

  modal.addEventListener('input', () => calcularTotal(modal));

  // === Llenado dinámico de horarios y precios al cambiar tour
  const selectTour = modal.querySelector('#selectTour');
  const selectSchedule = modal.querySelector('#selectSchedule');

  if (selectTour && selectSchedule) {
    selectTour.addEventListener('change', function () {
      const selectedOption = this.options[this.selectedIndex];
      const precioAdulto = parseFloat(selectedOption.dataset.precioAdulto) || 0;
      const precioNino   = parseFloat(selectedOption.dataset.precioNino) || 0;

      modal.querySelector('.precio-adulto').value = precioAdulto.toFixed(2);
      modal.querySelector('.precio-nino').value   = precioNino.toFixed(2);
      calcularTotal(modal);

      const schedules = JSON.parse(selectedOption.dataset.schedules || '[]');
      selectSchedule.innerHTML = '<option value="">Seleccione un horario</option>';
      schedules.forEach(s => {
        const option = document.createElement('option');
        option.value = s.schedule_id;
        option.text  = `${s.start_time} – ${s.end_time}`;
        selectSchedule.appendChild(option);
      });
    });
  }

  // === SweetAlert Éxito (si aplica)
  @if(session('success'))
    Swal.fire({
      icon: 'success',
      title: 'Éxito',
      text: '{{ session('success') }}',
      confirmButtonColor: '#3085d6',
      confirmButtonText: 'OK'
    });
  @endif

  // === SweetAlert Error de capacidad (si aplica)
  @if($errors->has('capacity'))
    Swal.fire({
      icon: 'error',
      title: 'Cupo Excedido',
      text: @json($errors->first('capacity')),
      confirmButtonColor: '#d33'
    });
  @endif

  // === Mostrar modal de edición si viene de error de validación
  @if(session('showEditModal'))
    const id = '{{ session('showEditModal') }}';
    const modal = new bootstrap.Modal(document.getElementById('modalEditar' + id));
    modal.show();
  @endif
});
</script>

{{-- Scripts adicionales por cada reserva para llenar horarios en edición --}}
@foreach($bookings as $reserva)
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const tourSel = document.getElementById('edit_tour_{{ $reserva->booking_id }}');
      const schSel  = document.getElementById('edit_schedule_{{ $reserva->booking_id }}');
      tourSel?.addEventListener('change', () => {
        const opt = tourSel.options[tourSel.selectedIndex];
        const schedules = JSON.parse(opt.dataset.schedules || '[]');
        schSel.innerHTML = '<option value="">Seleccione horario</option>';
        schedules.forEach(s => {
          const o = document.createElement('option');
          o.value = s.schedule_id;
          o.text  = `${s.start_time} – ${s.end_time}`;
          schSel.appendChild(o);
        });
      });
    });
  </script>
@endforeach
