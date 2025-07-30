document.addEventListener('DOMContentLoaded', () => {
  // ✅ NAVBAR TOGGLE
  const toggle = document.getElementById('navbar-toggle');
  const links = document.getElementById('navbar-links');

  if (toggle && links) {
    toggle.addEventListener('click', () => links.classList.toggle('show'));
    document.querySelectorAll('.navbar-links a').forEach(link =>
      link.addEventListener('click', () => links.classList.remove('show'))
    );
  }

  // ✅ LEER MÁS / LEER MENOS
  document.querySelectorAll('.toggle-overview-link').forEach(link => {
    link.addEventListener('click', function () {
      const overview = document.getElementById(this.dataset.target);
      if (!overview) return;
      const textMore = this.dataset.textMore || 'Leer más';
      const textLess = this.dataset.textLess || 'Leer menos';
      overview.classList.toggle('expanded');
      this.textContent = overview.classList.contains('expanded') ? textLess : textMore;
    });
  });

  // ✅ TOGGLE ICONOS DEL ACORDEÓN
  document.querySelectorAll('.accordion-button').forEach(btn => {
    btn.addEventListener('click', () => {
      const icon = btn.querySelector('.toggle-icon');
      if (icon) {
        icon.classList.toggle('fa-plus', !icon.classList.contains('fa-minus'));
        icon.classList.toggle('fa-minus');
      }
    });
  });

  // ✅ CONTADOR DEL CARRITO
  function updateCartCount() {
    fetch('/cart/count')
      .then(res => {
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        return res.json();
      })
      .then(data => {
        const badgeEls = document.querySelectorAll('.cart-count-badge');
        if (!badgeEls || badgeEls.length === 0) return;

        badgeEls.forEach(el => {
          el.textContent = data.count;
          el.style.display = data.count > 0 ? 'inline-block' : 'none';
          el.classList.remove('flash');
          void el.offsetWidth;
          el.classList.add('flash');
        });
      })
      .catch(err => {
        console.error('❌ Error al obtener la cantidad del carrito:', err);
      });
  }

  updateCartCount();

  // ✅ LÓGICA DE PRECIOS EN EL MODAL DE TRAVELERS
  const modalTotalPrice = document.getElementById('modal-total-price');
  const reservationTotalPrice = document.getElementById('reservation-total-price');
  const summarySpan = document.getElementById('traveler-summary');

  const adultPrice = parseFloat(document.querySelector('.reservation-box')?.dataset.adultPrice || 0);
  const kidPrice = parseFloat(document.querySelector('.reservation-box')?.dataset.kidPrice || 0);

  const maxTotal = 12;
  const minTotal = 2;
  const maxKids = 2;

  function updateModalTotal() {
    const adultCount = parseInt(document.getElementById('adult-count')?.textContent || 0);
    const kidCount = parseInt(document.getElementById('kid-count')?.textContent || 0);
    const total = (adultCount * adultPrice) + (kidCount * kidPrice);
    if (modalTotalPrice) modalTotalPrice.textContent = `Total: $${total.toFixed(2)}`;
  }

  function updateReservationTotal() {
    const adultCount = parseInt(document.getElementById('adult-count')?.textContent || 0);
    const kidCount = parseInt(document.getElementById('kid-count')?.textContent || 0);

    const adultsInput = document.getElementById('adults_quantity');
    const kidsInput = document.getElementById('kids_quantity');

    if (adultsInput) adultsInput.value = adultCount;
    if (kidsInput) kidsInput.value = kidCount;

    const total = (adultCount * adultPrice) + (kidCount * kidPrice);
    if (reservationTotalPrice) reservationTotalPrice.textContent = `$${total.toFixed(2)}`;
  }

  const plusBtns = document.querySelectorAll('.traveler-btn[data-action="increase"]');
  const minusBtns = document.querySelectorAll('.traveler-btn[data-action="decrease"]');

  plusBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const type = btn.dataset.type;
      let adultCount = parseInt(document.getElementById('adult-count')?.textContent || 0);
      let kidCount = parseInt(document.getElementById('kid-count')?.textContent || 0);
      const totalPeople = adultCount + kidCount;

      if (type === 'adult' && totalPeople < maxTotal) adultCount++;
      if (type === 'kid' && kidCount < maxKids && totalPeople < maxTotal) kidCount++;

      if (adultCount + kidCount < minTotal) adultCount = minTotal - kidCount;

      const adultEl = document.getElementById('adult-count');
      const kidEl = document.getElementById('kid-count');
      if (adultEl) adultEl.textContent = adultCount;
      if (kidEl) kidEl.textContent = kidCount;

      updateModalTotal();
    });
  });

  minusBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const type = btn.dataset.type;
      let adultCount = parseInt(document.getElementById('adult-count')?.textContent || 0);
      let kidCount = parseInt(document.getElementById('kid-count')?.textContent || 0);

      if (type === 'adult' && adultCount > 0) adultCount--;
      if (type === 'kid' && kidCount > 0) kidCount--;

      if (adultCount + kidCount < minTotal) {
        kidCount = 0;
        adultCount = minTotal;
      }

      const adultEl = document.getElementById('adult-count');
      const kidEl = document.getElementById('kid-count');
      if (adultEl) adultEl.textContent = adultCount;
      if (kidEl) kidEl.textContent = kidCount;

      updateModalTotal();
    });
  });

  document.querySelector('#travelerModal .btn-success')?.addEventListener('click', () => {
    const adultCount = parseInt(document.getElementById('adult-count')?.textContent || 0);
    const kidCount = parseInt(document.getElementById('kid-count')?.textContent || 0);
    if (summarySpan) summarySpan.textContent = adultCount + kidCount;
    updateReservationTotal();
  });

  if (document.getElementById('adult-count')) document.getElementById('adult-count').textContent = 2;
  if (document.getElementById('kid-count')) document.getElementById('kid-count').textContent = 0;
  updateModalTotal();
  updateReservationTotal();

  // ✅ VALIDACIÓN DE CUPO DISPONIBLE
  const addToCartForm = document.querySelector('.reservation-box');

  if (addToCartForm) {
    const tourId = window.tourId;
    const maxCapacity = window.maxCapacity;
    const tourDateInput = addToCartForm.querySelector('[name="tour_date"]');
    const scheduleSelect = addToCartForm.querySelector('[name="schedule_id"]');

    addToCartForm.addEventListener('submit', async function (e) {
      e.preventDefault();

      const adults = parseInt(document.getElementById('adults_quantity')?.value || 0);
      const kids = parseInt(document.getElementById('kids_quantity')?.value || 0);
      const requested = adults + kids;

      const tourDate = tourDateInput?.value;
      const scheduleId = scheduleSelect?.value;

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

        // 🟢 Si hay cupo, enviar formulario y actualizar contador
        addToCartForm.submit();
        updateCartCount();
      } catch (err) {
        console.error(err);
        Swal.fire('Error', 'No se pudo validar el cupo disponible.', 'error');
      }
    });
  }

  // ✅ SELECCIÓN DE HOTEL PERSONALIZADO
  const hotelSelect = document.getElementById('hotelSelect');
  const otherWrapper = document.getElementById('otherHotelWrapper');
  const otherInput = document.getElementById('otherHotelInput');
  const isOtherHotelInput = document.getElementById('isOtherHotel');
  const warningMessage = document.getElementById('outsideAreaMessage');

  if (hotelSelect) {
    hotelSelect.addEventListener('change', function () {
      if (this.value === 'other') {
        otherWrapper?.classList.remove('d-none');
        if (isOtherHotelInput) isOtherHotelInput.value = '1';
        if (otherInput) otherInput.required = true;
        if (warningMessage) warningMessage.style.display = 'block';
      } else {
        otherWrapper?.classList.add('d-none');
        if (isOtherHotelInput) isOtherHotelInput.value = '0';
        if (otherInput) {
          otherInput.required = false;
          otherInput.value = '';
        }
        if (warningMessage) warningMessage.style.display = 'none';
      }
    });
  }
});
