document.addEventListener('DOMContentLoaded', () => {
  // --------------------------------------------
  // ✅ Hamburguesa y Overview toggle
  // --------------------------------------------
  const toggle = document.getElementById('navbar-toggle');
  const links = document.getElementById('navbar-links');
  if (toggle && links) {
    toggle.addEventListener('click', () => {
      links.classList.toggle('show');
    });
  }
  document.querySelectorAll('.navbar-links a').forEach(link => {
    link.addEventListener('click', () => {
      links.classList.remove('show');
    });
  });

  const toggleLinks = document.querySelectorAll('.toggle-overview-link');
  toggleLinks.forEach(link => {
    link.addEventListener('click', function () {
      const targetId = this.dataset.target;
      const overview = document.getElementById(targetId);
      if (overview.classList.contains('expanded')) {
        overview.style.maxHeight = '4.5em';
        overview.classList.remove('expanded');
        overview.classList.add('collapsed');
        this.textContent = 'Leer más';
      } else {
        overview.style.maxHeight = overview.scrollHeight + 'px';
        overview.classList.remove('collapsed');
        overview.classList.add('expanded');
        this.textContent = 'Leer menos';
      }
    });
  });

  // --------------------------------------------
  // ✅ Accordion +/- icons
  // --------------------------------------------
  document.querySelectorAll('.accordion-button').forEach(btn => {
    btn.addEventListener('click', () => {
      const icon = btn.querySelector('.toggle-icon');
      if (icon) {
        icon.classList.toggle('fa-plus');
        icon.classList.toggle('fa-minus');
      }
    });
  });

  // --------------------------------------------
  // ✅ Travelers modal logic
  // --------------------------------------------
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

  // Solo para mostrar en el modal
  function updateModalTotal() {
    const adultCount = parseInt(document.getElementById('adult-count').textContent) || 0;
    const kidCount = parseInt(document.getElementById('kid-count').textContent) || 0;
    const total = (adultCount * adultPrice) + (kidCount * kidPrice);
    modalTotalPrice.textContent = `Total: $${total.toFixed(2)}`;

    document.getElementById('adult_count').value = adultCount;
    document.getElementById('kid_count').value = kidCount;
  }

  // Se ejecuta solo cuando haces Apply
  function updateReservationTotal() {
    const adultCount = parseInt(document.getElementById('adult-count').textContent) || 0;
    const kidCount = parseInt(document.getElementById('kid-count').textContent) || 0;
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

  // ✅ Apply → sincroniza todo
  const applyBtn = document.querySelector('#travelerModal .btn-success');
  applyBtn.addEventListener('click', () => {
    const adultCount = parseInt(document.getElementById('adult-count').textContent) || 0;
    const kidCount = parseInt(document.getElementById('kid-count').textContent) || 0;
    const totalPeople = adultCount + kidCount;

    summarySpan.textContent = totalPeople;
    updateReservationTotal(); // ✅ SOLO aquí
  });

  // Defaults
  document.getElementById('adult-count').textContent = 2;
  document.getElementById('kid-count').textContent = 0;
  updateModalTotal();
  updateReservationTotal();
});
