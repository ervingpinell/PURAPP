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

      overview.classList.toggle('expanded');
      this.textContent = overview.classList.contains('expanded') ? textLess : textMore;
    });
  });

  // ============================================
  // ✅ ACCORDION ICON TOGGLE +/-
  // ============================================
  document.querySelectorAll('.accordion-button').forEach(btn => {
    btn.addEventListener('click', () => {
      const icon = btn.querySelector('.toggle-icon');
      if (icon) icon.classList.toggle('fa-plus', !icon.classList.contains('fa-minus'));
      if (icon) icon.classList.toggle('fa-minus');
    });
  });

  // ============================================
  // ✅ FUNCIONALIDAD DEL CONTADOR DEL CARRITO
  // ============================================
function updateCartCount() {
  fetch('/cart/count')
    .then(res => res.json())
    .then(data => {
      const badgeEls = document.querySelectorAll('.cart-count-badge');
      badgeEls.forEach(el => {
        el.textContent = data.count;
        el.style.display = data.count > 0 ? 'inline-block' : 'none';

        // 🔁 Animación flash cuando cambia el número
        el.classList.remove('flash'); // Reiniciar si ya tiene clase
        void el.offsetWidth; // Forzar reflow
        el.classList.add('flash');
      });
    })
    .catch(err => console.error('Error al obtener la cantidad del carrito:', err));
}
  updateCartCount(); // Llamada inicial

  // ============================================
  // ✅ TRAVELERS MODAL: Quantity & Price Logic
  // ============================================
  const plusBtns = document.querySelectorAll('.traveler-btn[data-action="increase"]');
  const minusBtns = document.querySelectorAll('.traveler-btn[data-action="decrease"]');

  const modalTotalPrice = document.getElementById('modal-total-price');
  const reservationTotalPrice = document.getElementById('reservation-total-price');
  const summarySpan = document.getElementById('traveler-summary');

  const adultPrice = parseFloat(document.querySelector('.reservation-box')?.dataset.adultPrice || 0);
  const kidPrice = parseFloat(document.querySelector('.reservation-box')?.dataset.kidPrice || 0);

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

      if (type === 'adult' && totalPeople < maxTotal) adultCount++;
      if (type === 'kid' && kidCount < maxKids && totalPeople < maxTotal) kidCount++;

      if (adultCount + kidCount < minTotal) adultCount = minTotal - kidCount;

      document.getElementById('adult-count').textContent = adultCount;
      document.getElementById('kid-count').textContent = kidCount;

      updateModalTotal();
    });
  });

  minusBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const type = btn.dataset.type;
      let adultCount = parseInt(document.getElementById('adult-count').textContent) || 0;
      let kidCount = parseInt(document.getElementById('kid-count').textContent) || 0;

      if (type === 'adult' && adultCount > 0) adultCount--;
      if (type === 'kid' && kidCount > 0) kidCount--;

      if (adultCount + kidCount < minTotal) {
        kidCount = 0;
        adultCount = minTotal;
      }

      document.getElementById('adult-count').textContent = adultCount;
      document.getElementById('kid-count').textContent = kidCount;

      updateModalTotal();
    });
  });

  document.querySelector('#travelerModal .btn-success')?.addEventListener('click', () => {
    const adultCount = parseInt(document.getElementById('adult-count').textContent) || 0;
    const kidCount = parseInt(document.getElementById('kid-count').textContent) || 0;
    summarySpan.textContent = adultCount + kidCount;
    updateReservationTotal();
  });

  // Defaults
  document.getElementById('adult-count').textContent = 2;
  document.getElementById('kid-count').textContent = 0;
  updateModalTotal();
  updateReservationTotal();

  // ============================================
  // ✅ VALIDACIÓN CUPOS DISPONIBLES + CARRITO
  // ============================================
  const addToCartForm = document.querySelector('.reservation-box');

  if (addToCartForm) {
    const tourId = window.tourId;
    const maxCapacity = window.maxCapacity;
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
          Swal.fire('Cupo no disponible', `Solo quedan ${spotsLeft} espacios para este horario.`, 'error');
          return;
        }

        // ✅ Si todo está bien, enviar el formulario y actualizar contador
        addToCartForm.submit();
        updateCartCount();

      } catch (err) {
        console.error(err);
        Swal.fire('Error', 'No se pudo validar el cupo disponible.', 'error');
      }
    });
  }

  // ============================================
  // ✅ HOTEL SELECT + OTRO HOTEL PERSONALIZADO
  // ============================================
  const hotelSelect = document.getElementById('hotelSelect');
  const otherWrapper = document.getElementById('otherHotelWrapper');
  const otherInput = document.getElementById('otherHotelInput');
  const isOtherHotelInput = document.getElementById('isOtherHotel');
  const warningMessage = document.getElementById('outsideAreaMessage');

  if (hotelSelect) {
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
});
