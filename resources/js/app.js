/* =========================================================
   APP.JS — HEADER OFFSET + MENÚ MOBILE + UTILIDADES PÚBLICAS
   Localized URLs aware (respeta /{locale})
   ========================================================= */
(function () {
  const $doc = document;

  /* ---------- helpers de localización ---------- */
  const SUPPORTED = ['es','en','fr','de','pt'];

  function detectCurrentLocale() {
    // 1) por primer segmento de la URL
    const seg0 = (location.pathname.replace(/^\/+/, '').split('/', 1)[0] || '').toLowerCase();
    if (SUPPORTED.includes(seg0)) return seg0;
    // 2) por <html lang="xx-XX">
    const htmlLang = (document.documentElement.lang || '').slice(0,2).toLowerCase();
    if (SUPPORTED.includes(htmlLang)) return htmlLang;
    // 3) fallback a config por defecto (ajusta si quieres)
    return 'es';
  }
  const CUR_LOCALE = detectCurrentLocale();

  // Asegura prefijo /{locale} para rutas absolutas del sitio (no aplica a http(s):// ni #)
  function withLocale(path) {
    if (!path) return '/' + CUR_LOCALE + '/';
    if (path.startsWith('http://') || path.startsWith('https://') || path.startsWith('#')) return path;

    // Normaliza slashes
    let p = path.startsWith('/') ? path : '/' + path;
    // Evita duplicar si ya trae el locale
    const first = p.replace(/^\/+/, '').split('/', 1)[0].toLowerCase();
    if (SUPPORTED.includes(first)) return p;
    return '/' + CUR_LOCALE + p;
  }

  function isHomeWithLocale() {
    const p = location.pathname.replace(/\/+$/, ''); // quita slash final
    return p === '/' + CUR_LOCALE || p === '/' + CUR_LOCALE.replace(/\/+$/,'');
  }

  /* -----------------------------
   * 1) HEADER FIJO: medir altura
   * ----------------------------- */
  const header =
    $doc.querySelector('.navbar-custom') ||
    $doc.querySelector('header.site-header') ||
    $doc.getElementById('site-header');

  function setNavH() {
    if (!header) return;
    const h = Math.ceil(header.getBoundingClientRect().height || 0);
    if (h > 0) {
      document.documentElement.style.setProperty('--nav-h', h + 'px');
      document.body.classList.add('has-fixed-navbar');
    }
  }

  let t;
  const debounce = (fn, ms) => { clearTimeout(t); t = setTimeout(fn, ms); };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', setNavH);
  } else {
    setNavH();
  }
  window.addEventListener('resize', () => debounce(setNavH, 120), { passive: true });
  window.addEventListener('orientationchange', () => setTimeout(setNavH, 200), { passive: true });
  window.addEventListener('load', () => setTimeout(setNavH, 100));
  window.addEventListener('pageshow', () => setTimeout(setNavH, 60)); // bfcache

  if (document.fonts?.ready) document.fonts.ready.then(setNavH);
  if (header) {
    header.querySelectorAll('img').forEach((img) => {
      if (!img.complete) img.addEventListener('load', setNavH, { once: true });
    });
  }

  /* -------------------------------------------------
   * 2) NAVBAR TOGGLE (mobile) + bloqueo del scroll
   * ------------------------------------------------- */
  const toggleBtn   = $doc.getElementById('navbar-toggle');
  const mobileLinks = $doc.getElementById('navbar-links');

  function closeMenu() {
    if (!mobileLinks) return;
    mobileLinks.classList.remove('show');
    document.body.classList.remove('menu-open');
    if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'false');
  }
  function openMenu() {
    if (!mobileLinks) return;
    mobileLinks.classList.add('show');
    document.body.classList.add('menu-open');
    if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'true');
  }

  if (toggleBtn && mobileLinks) {
    toggleBtn.addEventListener('click', () => {
      mobileLinks.classList.contains('show') ? closeMenu() : openMenu();
    });
    mobileLinks.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', () => closeMenu());
    });
  }

  /* --------------------------------------------
   * 3) SCROLL SUAVE respetando altura del header
   * -------------------------------------------- */
  function getNavOffset() {
    const v = getComputedStyle(document.documentElement).getPropertyValue('--nav-h');
    const n = parseInt(v, 10);
    return Number.isFinite(n) ? n : 0;
  }
  function smoothScrollTo(target) {
    if (!target) return;
    const rect = target.getBoundingClientRect();
    const absoluteY = window.pageYOffset + rect.top;
    const offset = getNavOffset();
    window.scrollTo({ top: absoluteY - offset - 8, behavior: 'smooth' });
  }

  // Enlaces a anclas (#id)
  $doc.querySelectorAll('a[href^="#"]').forEach((a) => {
    a.addEventListener('click', (ev) => {
      const hash = a.getAttribute('href');
      if (!hash || hash === '#') return;
      const el = $doc.querySelector(hash);
      if (el) {
        ev.preventDefault();
        closeMenu();
        smoothScrollTo(el);
      }
    });
  });

  // Botones “Tours” (respetando locale actual)
  $doc.querySelectorAll('.scroll-to-tours').forEach(link => {
    link.addEventListener('click', function (e) {
      e.preventDefault();
      if (isHomeWithLocale()) {
        const target = document.getElementById('tours') || document.querySelector('[data-anchor="tours"]');
        if (target) smoothScrollTo(target);
        closeMenu();
      } else {
        // Lleva al home del locale actual con hash
        window.location.href = withLocale('/') + '#tours';
      }
    });
  });

  // Si llegamos con #tours, hacer scroll
  if (window.location.hash === '#tours') {
    setTimeout(() => {
      const target = document.getElementById('tours') || document.querySelector('[data-anchor="tours"]');
      if (target) smoothScrollTo(target);
    }, 200);
  }

  /* ---------------------------------
   * 4) LEER MÁS / LEER MENOS (home)
   * --------------------------------- */
  $doc.querySelectorAll('.toggle-overview-link').forEach(link => {
    link.addEventListener('click', function () {
      const overview = document.getElementById(this.dataset.target);
      if (!overview) return;
      const textMore = this.dataset.textMore || 'Leer más';
      const textLess = this.dataset.textLess || 'Leer menos';
      overview.classList.toggle('expanded');
      this.textContent = overview.classList.contains('expanded') ? textLess : textMore;
    });
  });

  /* ---------------------------------
   * 5) Accordion toggle icons (FAQ)
   * --------------------------------- */
  $doc.querySelectorAll('.accordion-button').forEach(btn => {
    btn.addEventListener('click', () => {
      const icon = btn.querySelector('.toggle-icon');
      if (icon) {
        icon.classList.toggle('fa-minus');
        icon.classList.toggle('fa-plus');
      }
    });
  });

  /* ---------------------------------
   * 6) Cart item counter (fetch)
   * --------------------------------- */
  function updateCartCount() {
    // ⬇️ usa la ruta localizada
    fetch(withLocale('/cart/count'))
      .then(res => res.ok ? res.json() : Promise.reject(`HTTP ${res.status}`))
      .then(data => {
        if (typeof window.setCartCount === 'function') {
          window.setCartCount(data.count);
          return;
        }
        // Fallback si no existe setCartCount
        const badgeEls = document.querySelectorAll('.cart-count-badge');
        badgeEls.forEach(el => {
          el.textContent = data.count;
          el.style.display = data.count > 0 ? 'inline-block' : 'none';
          el.classList.remove('flash'); void el.offsetWidth; el.classList.add('flash');
        });
      })
      .catch(err => console.error('❌ Error al obtener la cantidad del carrito:', err));
  }
  updateCartCount();

  /* ----------------------------------------------------
   * 7) Precios: modal + resumen (si existen en la vista)
   * ---------------------------------------------------- */
  const modalTotalPrice = document.getElementById('modal-total-price');
  const reservationTotalPrice = document.getElementById('reservation-total-price');
  const summarySpan = document.getElementById('traveler-summary');

  const adultPrice = parseFloat(document.querySelector('.reservation-box')?.dataset.adultPrice || 0);
  const kidPrice   = parseFloat(document.querySelector('.reservation-box')?.dataset.kidPrice   || 0);
  const maxTotal = 12;
  const minTotal = 2;
  const maxKids  = 2;

  function updateModalTotal() {
    const adultCount = parseInt(document.getElementById('adult-count')?.textContent || 0);
    const kidCount   = parseInt(document.getElementById('kid-count')?.textContent   || 0);
    const total = (adultCount * adultPrice) + (kidCount * kidPrice);
    if (modalTotalPrice) modalTotalPrice.textContent = `Total: $${total.toFixed(2)}`;
  }

  function updateReservationTotal() {
    const adultCount = parseInt(document.getElementById('adult-count')?.textContent || 0);
    const kidCount   = parseInt(document.getElementById('kid-count')?.textContent   || 0);

    const adultsQtyInput = document.getElementById('adults_quantity');
    const kidsQtyInput   = document.getElementById('kids_quantity');

    if (adultsQtyInput) adultsQtyInput.value = adultCount;
    if (kidsQtyInput)   kidsQtyInput.value   = kidCount;

    const total = (adultCount * adultPrice) + (kidCount * kidPrice);
    if (reservationTotalPrice) reservationTotalPrice.textContent = `$${total.toFixed(2)}`;
  }

  // Contadores (+ / -)
  const plusBtns  = document.querySelectorAll('.traveler-btn[data-action="increase"]');
  const minusBtns = document.querySelectorAll('.traveler-btn[data-action="decrease"]');
  const adultCountEl = document.getElementById('adult-count');
  const kidCountEl   = document.getElementById('kid-count');

  if (adultCountEl && kidCountEl) {
    if (!adultCountEl.textContent) adultCountEl.textContent = '2';
    if (!kidCountEl.textContent)   kidCountEl.textContent   = '0';
    updateModalTotal();
    updateReservationTotal();
  }

  plusBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const type = btn.dataset.type;
      let adults = parseInt(adultCountEl?.textContent || 0);
      let kids   = parseInt(kidCountEl?.textContent   || 0);
      const total = adults + kids;

      if (type === 'adult' && total < maxTotal) adults++;
      if (type === 'kid'   && kids  < maxKids && total < maxTotal) kids++;
      if (adults + kids < minTotal) adults = minTotal - kids;

      if (adultCountEl) adultCountEl.textContent = adults;
      if (kidCountEl)   kidCountEl.textContent   = kids;

      updateModalTotal();
    });
  });

  minusBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const type = btn.dataset.type;
      let adults = parseInt(adultCountEl?.textContent || 0);
      let kids   = parseInt(kidCountEl?.textContent   || 0);

      if (type === 'adult' && adults > 0) adults--;
      if (type === 'kid'   && kids > 0)   kids--;

      if (adults + kids < minTotal) {
        kids = 0;
        adults = minTotal;
      }

      if (adultCountEl) adultCountEl.textContent = adults;
      if (kidCountEl)   kidCountEl.textContent   = kids;

      updateModalTotal();
    });
  });

  document.querySelector('#travelerModal .btn-success')?.addEventListener('click', () => {
    const adultCount = parseInt(adultCountEl?.textContent || 0);
    const kidCount   = parseInt(kidCountEl?.textContent   || 0);
    if (summarySpan) summarySpan.textContent = adultCount + kidCount;
    updateReservationTotal();
  });

  /* ----------------------------------
   * 8) Validación de capacidad (API)
   * ---------------------------------- */
  const addToCartForm = document.querySelector('.reservation-box');
  if (addToCartForm) {
    const tourId        = window.tourId;
    const maxCapacity   = window.maxCapacity;
    const tourDateInput = addToCartForm.querySelector('[name="tour_date"]');
    const scheduleSelect= addToCartForm.querySelector('[name="schedule_id"]');

    addToCartForm.addEventListener('submit', async function (e) {
      e.preventDefault();

      // Saneo hotel_id: entero o vacío
      const hotelIdInput = document.getElementById('selectedPickupPoint');
      if (hotelIdInput) {
        const v = (hotelIdInput.value || '').trim();
        hotelIdInput.value = /^\d+$/.test(v) ? v : '';
      }

      const adults    = parseInt(document.getElementById('adults_quantity')?.value || 0);
      const kids      = parseInt(document.getElementById('kids_quantity')?.value   || 0);
      const requested = adults + kids;

      const tourDate  = tourDateInput?.value;
      const scheduleId= scheduleSelect?.value;

      if (!tourDate || !scheduleId) {
        Swal.fire('Error', 'Selecciona una fecha y un horario válido.', 'error');
        return;
      }

      try {
        // Si tu endpoint es localizado, usa withLocale('/api/get-reserved')
        const res = await fetch(`/api/get-reserved?tour_id=${tourId}&schedule_id=${scheduleId}&tour_date=${encodeURIComponent(tourDate)}`);
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

  /* ----------------------------------
   * 9) Pickup (autocompletar simple)
   * ---------------------------------- */
  const pickupInput         = document.getElementById('pickupInput');        // visible
  const pickupList          = document.getElementById('pickupList');         // UL/LI
  const pickupValidMsg      = document.getElementById('pickupValidMsg');
  const pickupInvalidMsg    = document.getElementById('pickupInvalidMsg');
  const selectedPickupPoint = document.getElementById('selectedPickupPoint'); // hidden (hotel_id)

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

      if (pickupValidMsg)   pickupValidMsg.classList.add('d-none');
      if (pickupInvalidMsg) pickupInvalidMsg.classList.toggle('d-none', found || filter === '');

      selectedPickupPoint.value = ''; // si el usuario escribe, resetea id
    });

    pickupInput.addEventListener('focus', () => {
      pickupList.classList.remove('d-none');
    });

    pickupList.addEventListener('click', (e) => {
      const li = e.target.closest('.pickup-option');
      if (li) {
        const hotelName = li.textContent.trim();
        const hotelId   = li.dataset.id;

        pickupInput.value = hotelName;
        selectedPickupPoint.value = /^\d+$/.test(String(hotelId)) ? hotelId : '';

        if (pickupValidMsg)   pickupValidMsg.classList.remove('d-none');
        if (pickupInvalidMsg) pickupInvalidMsg.classList.add('d-none');
        pickupList.classList.add('d-none');
      }
    });

    document.addEventListener('click', (e) => {
      if (!pickupList.contains(e.target) && e.target !== pickupInput) {
        pickupList.classList.add('d-none');
      }
    });
  }

  /* -------------------------------------------
   * 10) Hardening para carousels/iframes móviles
   * ------------------------------------------- */
  document.querySelectorAll('.carousel, .carousel-inner, .carousel-item')
    .forEach(el => el.style.transform = 'translateZ(0)');
})();
