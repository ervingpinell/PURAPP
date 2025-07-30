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
        icon.classList.toggle('fa-minus');
        icon.classList.toggle('fa-plus');
      }
    });
  });

  // ✅ CONTADOR DEL CARRITO
  function updateCartCount() {
    fetch('/cart/count')
      .then(res => res.ok ? res.json() : Promise.reject(`HTTP ${res.status}`))
      .then(data => {
        const badgeEls = document.querySelectorAll('.cart-count-badge');
        badgeEls.forEach(el => {
          el.textContent = data.count;
          el.style.display = data.count > 0 ? 'inline-block' : 'none';
          el.classList.remove('flash');
          void el.offsetWidth;
          el.classList.add('flash');
        });
      })
      .catch(err => console.error('❌ Error al obtener la cantidad del carrito:', err));
  }

  updateCartCount();

  // ✅ PRECIOS DEL MODAL Y RESERVA
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

    const adultsQtyInput = document.getElementById('adults_quantity');
    const kidsQtyInput = document.getElementById('kids_quantity');

    if (adultsQtyInput) adultsQtyInput.value = adultCount;
    if (kidsQtyInput) kidsQtyInput.value = kidCount;

    const total = (adultCount * adultPrice) + (kidCount * kidPrice);
    if (reservationTotalPrice) reservationTotalPrice.textContent = `$${total.toFixed(2)}`;
  }

  const plusBtns = document.querySelectorAll('.traveler-btn[data-action="increase"]');
  const minusBtns = document.querySelectorAll('.traveler-btn[data-action="decrease"]');

  plusBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const type = btn.dataset.type;
      let adults = parseInt(document.getElementById('adult-count')?.textContent || 0);
      let kids = parseInt(document.getElementById('kid-count')?.textContent || 0);
      const total = adults + kids;

      if (type === 'adult' && total < maxTotal) adults++;
      if (type === 'kid' && kids < maxKids && total < maxTotal) kids++;
      if (adults + kids < minTotal) adults = minTotal - kids;

      document.getElementById('adult-count').textContent = adults;
      document.getElementById('kid-count').textContent = kids;

      updateModalTotal();
    });
  });

  minusBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const type = btn.dataset.type;
      let adults = parseInt(document.getElementById('adult-count')?.textContent || 0);
      let kids = parseInt(document.getElementById('kid-count')?.textContent || 0);

      if (type === 'adult' && adults > 0) adults--;
      if (type === 'kid' && kids > 0) kids--;

      if (adults + kids < minTotal) {
        kids = 0;
        adults = minTotal;
      }

      document.getElementById('adult-count').textContent = adults;
      document.getElementById('kid-count').textContent = kids;

      updateModalTotal();
    });
  });

  document.querySelector('#travelerModal .btn-success')?.addEventListener('click', () => {
    const adultCount = parseInt(document.getElementById('adult-count')?.textContent || 0);
    const kidCount = parseInt(document.getElementById('kid-count')?.textContent || 0);
    if (summarySpan) summarySpan.textContent = adultCount + kidCount;
    updateReservationTotal();
  });

  // Inicialización
  document.getElementById('adult-count').textContent = 2;
  document.getElementById('kid-count').textContent = 0;
  updateModalTotal();
  updateReservationTotal();

  // ✅ VALIDACIÓN DE CUPO
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

        addToCartForm.submit();
        updateCartCount();
      } catch (err) {
        console.error(err);
        Swal.fire('Error', 'No se pudo validar el cupo disponible.', 'error');
      }
    });
  }

 const pickupInput = document.getElementById('pickupInput');
const pickupList = document.getElementById('pickupList');
const pickupValidMsg = document.getElementById('pickupValidMsg');
const pickupInvalidMsg = document.getElementById('pickupInvalidMsg');
const selectedPickupPoint = document.getElementById('selectedPickupPoint');

if (pickupInput && pickupList && selectedPickupPoint) {
  pickupInput.addEventListener('input', () => {
    const filter = pickupInput.value.toLowerCase().trim();
    let found = false;

    pickupList.querySelectorAll('li').forEach(li => {
      const name = li.textContent.toLowerCase();
      const match = name.includes(filter);
      li.classList.toggle('d-none', !match);
      if (match) found = true;
    });

    pickupList.classList.remove('d-none');

    // Mensajes
    pickupValidMsg.classList.add('d-none');
    pickupInvalidMsg.classList.toggle('d-none', found || filter === '');

    // Guardar como "otro"
    selectedPickupPoint.value = found ? '' : 'other:' + pickupInput.value;
  });

  // Mostrar lista al hacer focus
  pickupInput.addEventListener('focus', () => {
    pickupList.classList.remove('d-none');
  });

  // Seleccionar hotel desde lista
  pickupList.addEventListener('click', (e) => {
    const li = e.target.closest('.pickup-option');
    if (li) {
      const hotelName = li.textContent.trim();
      const hotelId = li.dataset.id;

      pickupInput.value = hotelName;
      selectedPickupPoint.value = hotelId;

      pickupValidMsg.classList.remove('d-none');
      pickupInvalidMsg.classList.add('d-none');
      pickupList.classList.add('d-none');
    }
  });

  // Ocultar lista si se hace clic fuera
  document.addEventListener('click', (e) => {
    if (!pickupList.contains(e.target) && e.target !== pickupInput) {
      pickupList.classList.add('d-none');
    }
  });
}


});
