<script>
// === Show/Hide 'Other hotel' field ===
document.addEventListener('DOMContentLoaded', () => {
  const modal = document.getElementById('modalRegister');

  // === Show or hide other hotel field when selecting 'Other...'
  const hotelSelect = modal.querySelector('#selectHotel');
  const otherHotelDiv = modal.querySelector('#otherHotelRegisterWrapper');
  const isOtherInput = modal.querySelector('#isOtherHotelRegister');

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

  // === Total calculation based on quantity and price
  function calculateTotal(modal) {
    const adults = parseInt(modal.querySelector('.adults-quantity')?.value || 0);
    const kids   = parseInt(modal.querySelector('.kids-quantity')?.value || 0);
    const priceA = parseFloat(modal.querySelector('.adult-price')?.value || 0);
    const priceK = parseFloat(modal.querySelector('.kid-price')?.value || 0);
    const total   = (adults * priceA) + (kids * priceK);
    const totalInput = modal.querySelector('.total-payment');
    if (totalInput) totalInput.value = total.toFixed(2);
  }

  modal.addEventListener('input', () => calculateTotal(modal));

  // === Dynamic filling of schedules and prices when changing tour
  const selectTour = modal.querySelector('#selectTour');
  const selectSchedule = modal.querySelector('#selectSchedule');

  if (selectTour && selectSchedule) {
    selectTour.addEventListener('change', function () {
      const selectedOption = this.options[this.selectedIndex];
      const adultPrice = parseFloat(selectedOption.dataset.adultPrice) || 0;
      const kidPrice   = parseFloat(selectedOption.dataset.kidPrice) || 0;

      modal.querySelector('.adult-price').value = adultPrice.toFixed(2);
      modal.querySelector('.kid-price').value   = kidPrice.toFixed(2);
      calculateTotal(modal);

      const schedules = JSON.parse(selectedOption.dataset.schedules || '[]');
      selectSchedule.innerHTML = '<option value="">Select a schedule</option>';
      schedules.forEach(s => {
        const option = document.createElement('option');
        option.value = s.schedule_id;
        option.text  = `${s.start_time} — ${s.end_time}`;
        selectSchedule.appendChild(option);
      });
    });
  }

  // === SweetAlert Success (if applicable)
  @if(session('success'))
    Swal.fire({
      icon: 'success',
      title: 'Success',
      text: '{{ session('success') }}',
      confirmButtonColor: '#3085d6',
      confirmButtonText: 'OK'
    });
  @endif

  // === SweetAlert Capacity Error (if applicable)
  @if($errors->has('capacity'))
    Swal.fire({
      icon: 'error',
      title: 'Capacity Exceeded',
      text: @json($errors->first('capacity')),
      confirmButtonColor: '#d33'
    });
  @endif

  // === Show edit modal if coming from validation error
  @if(session('showEditModal'))
    const id = '{{ session('showEditModal') }}';
    const modal = new bootstrap.Modal(document.getElementById('modalEdit' + id));
    modal.show();
  @endif
});
</script>

{{-- Additional scripts for each booking to fill schedules on edit --}}
@foreach($bookings as $booking)
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const tourSel = document.getElementById('edit_tour_{{ $booking->booking_id }}');
      const schSel  = document.getElementById('edit_schedule_{{ $booking->booking_id }}');
      tourSel?.addEventListener('change', () => {
        const opt = tourSel.options[tourSel.selectedIndex];
        const schedules = JSON.parse(opt.dataset.schedules || '[]');
        schSel.innerHTML = '<option value="">Select schedule</option>';
        schedules.forEach(s => {
          const o = document.createElement('option');
          o.value = s.schedule_id;
          o.text  = `${s.start_time} — ${s.end_time}`;
          schSel.appendChild(o);
        });
      });
    });
  </script>
@endforeach
