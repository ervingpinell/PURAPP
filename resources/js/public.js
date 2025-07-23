document.addEventListener('DOMContentLoaded', () => {

  // ============================================
  // ✅ NAVBAR HAMBURGER TOGGLE
  // ============================================
  const toggle = document.getElementById('navbar-toggle');
  const links = document.getElementById('navbar-links');

  if (toggle && links) {
    toggle.addEventListener('click', () => {
      links.classList.toggle('show');
    });

    document.querySelectorAll('.navbar-links a').forEach(link => {
      link.addEventListener('click', () => {
        links.classList.remove('show');
      });
    });
  }

  // ============================================
  // ✅ OVERVIEW TOGGLE "Leer más / Leer menos"
  // ============================================
  const toggleLinks = document.querySelectorAll('.toggle-overview-link');
  toggleLinks.forEach(link => {
    link.addEventListener('click', function () {
      const targetId = this.dataset.target;
      const overview = document.getElementById(targetId);

      const textMore = this.dataset.textMore || 'Leer más';
      const textLess = this.dataset.textLess || 'Leer menos';

      if (overview.classList.contains('expanded')) {
        overview.classList.remove('expanded');
        this.textContent = textMore;
      } else {
        overview.classList.add('expanded');
        this.textContent = textLess;
      }
    });
  });

  // ============================================
  // ✅ ACCORDION ICON TOGGLE +/-
  // ============================================
  document.querySelectorAll('.accordion-button').forEach(btn => {
    btn.addEventListener('click', () => {
      const icon = btn.querySelector('.toggle-icon');
      if (icon) {
        icon.classList.toggle('fa-plus');
        icon.classList.toggle('fa-minus');
      }
    });
  });

  // ============================================
  // ✅ TRAVELERS MODAL: Quantity & Price Logic
  // ============================================
  const plusBtns = document.querySelectorAll('.traveler-btn[data-action="increase"]');
  const minusBtns = document.querySelectorAll('.traveler-btn[data-action="decrease"]');

  const modalTotalPrice = document.getElementById('modal-total-price');
  const reservationTotalPrice = document.getElementById('reservation-total-price');
  const summarySpan = document.getElementById('traveler-summary');

  const adultPrice = parseFloat(document.querySelector('.reservation-box').dataset.adultPrice || 0);
  const kidPrice = parseFloat(document.querySelector('.reservation-box').dataset.kidPrice || 0);

  const maxTotal = 12;
  const minTotal = 2;
  const maxKids = 2;

  function updateModalTotal() {
    const adultCount = parseInt(document.getElementById('adult-count').textContent) || 0;
    const kidCount = parseInt(document.getElementById('kid-count').textContent) || 0;
    const total = (adultCount * adultPrice) + (kidCount * kidPrice);
    modalTotalPrice.textContent = `Total: $${total.toFixed(2)}`;
  }

  function updateReservationTotal() {
    const adultCount = parseInt(document.getElementById('adult-count').textContent) || 0;
    const kidCount = parseInt(document.getElementById('kid-count').textContent) || 0;

    document.getElementById('adults_quantity').value = adultCount;
    document.getElementById('kids_quantity').value = kidCount;

    const total = (adultCount * adultPrice) + (kidCount * kidPrice);
    reservationTotalPrice.textContent = `$${total.toFixed(2)}`;
  }

  plusBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const type = btn.dataset.type;
      let adultCount = parseInt(document.getElementById('adult-count').textContent) || 0;
      let kidCount = parseInt(document.getElementById('kid-count').textContent) || 0;
      const totalPeople = adultCount + kidCount;

      if (type === 'adult' && totalPeople < maxTotal) {
        adultCount++;
        document.getElementById('adult-count').textContent = adultCount;
      }
      if (type === 'kid' && kidCount < maxKids && totalPeople < maxTotal) {
        kidCount++;
        document.getElementById('kid-count').textContent = kidCount;
      }

      if (adultCount + kidCount < minTotal) {
        adultCount = minTotal - kidCount;
        document.getElementById('adult-count').textContent = adultCount;
      }

      updateModalTotal();
    });
  });

  minusBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const type = btn.dataset.type;
      let adultCount = parseInt(document.getElementById('adult-count').textContent) || 0;
      let kidCount = parseInt(document.getElementById('kid-count').textContent) || 0;

      if (type === 'adult' && adultCount > 0) {
        adultCount--;
        if (adultCount + kidCount < minTotal) {
          adultCount = minTotal - kidCount;
        }
        document.getElementById('adult-count').textContent = adultCount;
      }

      if (type === 'kid' && kidCount > 0) {
        kidCount--;
        if (adultCount + kidCount < minTotal) {
          kidCount = 0;
          if (adultCount < minTotal) {
            adultCount = minTotal;
          }
        }
        document.getElementById('kid-count').textContent = kidCount;
      }

      updateModalTotal();
    });
  });

  const applyBtn = document.querySelector('#travelerModal .btn-success');
  applyBtn.addEventListener('click', () => {
    const adultCount = parseInt(document.getElementById('adult-count').textContent) || 0;
    const kidCount = parseInt(document.getElementById('kid-count').textContent) || 0;
    const totalPeople = adultCount + kidCount;

    summarySpan.textContent = totalPeople;

    updateReservationTotal();
  });

  // Defaults
  document.getElementById('adult-count').textContent = 2;
  document.getElementById('kid-count').textContent = 0;
  updateModalTotal();
  updateReservationTotal();

  // ============================================
  // ✅ PICKUP POINTS LOGIC
  // ============================================
  const pickupSearch = document.getElementById('pickupSearch');
  const pickupListWrapper = document.getElementById('pickupListWrapper');
  const pickupListItems = document.querySelectorAll('#pickupList li');
  const pickupNotFound = document.getElementById('pickupNotFound');
  const selectedPickupPoint = document.getElementById('selectedPickupPoint');
  const selectedPickupDisplay = document.getElementById('selectedPickupDisplay');

  const hotelInput = document.getElementById('selectedPickupPoint');
  const isOtherHotelInput = document.getElementById('isOtherHotel');
  const otherHotelNameInput = document.getElementById('otherHotelName');

  if (pickupSearch && pickupListItems) {
    pickupSearch.addEventListener('input', function () {
      const term = pickupSearch.value.trim().toLowerCase();
      let found = false;

      pickupListItems.forEach(item => {
        const match = item.textContent.toLowerCase().includes(term);
        item.style.display = match ? '' : 'none';
        if (match) found = true;
      });

      pickupListWrapper.classList.remove('d-none');
      pickupNotFound.classList.toggle('d-none', found || term === '');
    });

    pickupListWrapper.addEventListener('change', function (e) {
      if (e.target.name === 'pickupOption') {
        hotelInput.value = e.target.value;
        isOtherHotelInput.value = 0;
        otherHotelNameInput.value = '';

        const label = e.target.closest('label');
        const hotelName = label.querySelector('strong').textContent;

        selectedPickupDisplay.querySelector('span').textContent = hotelName;
        pickupListWrapper.classList.add('d-none');
      }
    });

    selectedPickupDisplay.addEventListener('click', function () {
      pickupListWrapper.classList.toggle('d-none');
      pickupSearch.focus();
    });

    document.addEventListener('click', function (e) {
      if (!pickupSearch.contains(e.target) &&
        !pickupListWrapper.contains(e.target) &&
        !selectedPickupDisplay.contains(e.target)) {
        pickupListWrapper.classList.add('d-none');
      }
    });
  }

  // ============================================
  // ✅ MEETING POINTS LOGIC
  // ============================================
  const meetingSearch = document.getElementById('meetingSearch');
  const meetingListWrapper = document.getElementById('meetingListWrapper');
  const meetingListItems = document.querySelectorAll('#meetingList li');
  const meetingNotFound = document.getElementById('meetingNotFound');
  const selectedMeetingPoint = document.getElementById('selectedMeetingPoint');

  if (meetingSearch && meetingListItems) {
    meetingSearch.addEventListener('input', function () {
      const term = meetingSearch.value.trim().toLowerCase();
      let found = false;

      meetingListItems.forEach(item => {
        const match = item.textContent.toLowerCase().includes(term);
        item.style.display = match ? '' : 'none';
        if (match) found = true;
      });

      meetingListWrapper.classList.remove('d-none');
      meetingNotFound.classList.toggle('d-none', found || term === '');
    });

    meetingListWrapper.addEventListener('change', function (e) {
      if (e.target.name === 'meetingOption') {
        selectedMeetingPoint.value = e.target.value;
        meetingListWrapper.classList.add('d-none');
      }
    });

    document.addEventListener('click', function (e) {
      if (!meetingSearch.contains(e.target) && !meetingListWrapper.contains(e.target)) {
        meetingListWrapper.classList.add('d-none');
      }
    });
  }

  // ============================================
// ✅ VALIDACIÓN CUPOS DISPONIBLES
// ============================================
const addToCartForm = document.querySelector('.reservation-box');

if (addToCartForm) {
  const tourId = window.tourId;            // ✅ Global desde Blade
  const maxCapacity = window.maxCapacity;  // ✅ Global desde Blade
  const tourDateInput = addToCartForm.querySelector('[name="tour_date"]');
  const scheduleSelect = addToCartForm.querySelector('[name="schedule_id"]');

  addToCartForm.addEventListener('submit', async function (e) {
    e.preventDefault();

    const adults = parseInt(document.getElementById('adults_quantity').value) || 0;
    const kids = parseInt(document.getElementById('kids_quantity').value) || 0;
    const requested = adults + kids;

    const tourDate = tourDateInput.value;
    const scheduleId = scheduleSelect.value;

    if (!tourDate || !scheduleId) {
      Swal.fire('Error', 'Selecciona una fecha y un horario válido.', 'error');
      return;
    }

    try {
      const res = await fetch(`/api/get-reserved?tour_id=${tourId}&schedule_id=${scheduleId}&tour_date=${tourDate}`);
      const data = await res.json();
      const reserved = parseInt(data.reserved) || 0;

      if (reserved + requested > maxCapacity) {
        const spotsLeft = Math.max(maxCapacity - reserved, 0);
        Swal.fire(
          'Cupo no disponible',
          `Lo sentimos, solo quedan ${spotsLeft} espacio(s) disponible(s) para este horario.`,
          'error'
        );
        return;
      }

      addToCartForm.submit();
    } catch (err) {
      console.error(err);
      Swal.fire('Error', 'No se pudo validar el cupo disponible. Intenta de nuevo.', 'error');
    }
  });

  // ============================================
  // ✅ HOTEL SELECT + OTRO HOTEL PERSONALIZADO
  // ============================================
  const hotelSelect = document.getElementById('hotelSelect');
  const otherWrapper = document.getElementById('otherHotelWrapper');
  const otherInput = document.getElementById('otherHotelInput');
  const isOtherHotelInput = document.getElementById('isOtherHotel');
  const warningMessage = document.getElementById('outsideAreaMessage');

  if (hotelSelect && otherWrapper && otherInput && isOtherHotelInput && warningMessage) {
    hotelSelect.addEventListener('change', function () {
      if (this.value === 'other') {
        otherWrapper.classList.remove('d-none');
        isOtherHotelInput.value = '1';
        otherInput.required = true;
        warningMessage.style.display = 'block';
      } else {
        otherWrapper.classList.add('d-none');
        isOtherHotelInput.value = '0';
        otherInput.required = false;
        otherInput.value = '';
        warningMessage.style.display = 'none';
      }
    });
  }
}

});
