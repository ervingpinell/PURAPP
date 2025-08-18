// resources/js/viator/carousel-reviews.js
import { fetchReviewsBatch } from '../reviews-fetcher';

document.addEventListener('DOMContentLoaded', async () => {
  const container = document.getElementById('viator-carousel');
  const inner = container?.querySelector('.carousel-inner');
  const products = Array.isArray(window.VIATOR_CAROUSEL_PRODUCTS) ? window.VIATOR_CAROUSEL_PRODUCTS : [];
  if (!container || !inner || products.length === 0) return;

  const REFRESH_MS = Number(window.REVIEWS_REFRESH_MS) || (10 * 60 * 1000);

  // ====== I18N ======
  const T = {
    loading:   window.I18N?.loading_reviews ?? 'Cargando reseñas...',
    none:      window.I18N?.no_reviews ?? 'No hay reseñas para mostrar.',
    seeMore:   window.I18N?.see_more ?? 'Leer más',
    seeLess:   window.I18N?.see_less ?? 'Leer menos',
    anonymous: window.I18N?.anonymous ?? 'Anónimo',
    noDate:    window.I18N?.no_date ?? 'Fecha no disponible',
    poweredBy: window.I18N?.powered_by ?? 'Powered by',
  };

  // ====== Utils ======
  const escapeHtml = (str = '') =>
    String(str)
      .replaceAll('&', '&amp;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;')
      .replaceAll('"', '&quot;')
      .replaceAll("'", '&#039;');

  const renderStars = (rating) => {
    const n = Math.max(0, Math.min(5, Number(rating) || 0));
    const full = Math.floor(n);
    const half = n % 1 >= 0.5 ? 1 : 0;
    const empty = 5 - full - half;
    let stars = '';
    for (let i = 0; i < full; i++) stars += '★';
    if (half) stars += '⯨';
    for (let i = 0; i < empty; i++) stars += '☆';
    const formatted = Number.isInteger(n) ? n : n.toFixed(1);
    return `<div class="mb-2 review-stars text-warning">${stars}<span class="rating-number"> (${formatted}/5)</span></div>`;
  };

  const resolveProductName = (product, firstReview) =>
    (product?.name) || (firstReview?.productTitle) || (product?.code) || '';

  // ====== Viator URL helpers ======
  const VIATOR_DEFAULTS = { destId: 821, citySlug: 'La-Fortuna' };

  /** Crea slug de producto legible tipo "Arenal-Volcano-La-Fortuna-and-Lunch" */
  const toProductSlug = (name = '') => {
    let s = String(name)
      .replace(/&|\+/g, ' and ')                             // & y + -> "and"
      .normalize('NFD').replace(/[\u0300-\u036f]/g, '')      // quitar tildes
      .replace(/[^A-Za-z0-9\s-]/g, ' ')                      // quitar símbolos
      .replace(/\s+/g, ' ')                                  // colapsar espacios
      .trim();
    s = s.split(' ').map(w => (w ? (w[0].toUpperCase() + w.slice(1)) : '')).join('-');
    return s || 'Tour';
  };

  /** Agrega parámetros de query (afiliación/tracking) si existen */
  const appendQuery = (url, params) => {
    if (!params) return url;
    try {
      const u = new URL(url);
      if (typeof params === 'string') {
        // admite "?pid=...&campaign=..." como string
        const s = params.startsWith('?') ? params.slice(1) : params;
        new URLSearchParams(s).forEach((v, k) => u.searchParams.set(k, v));
      } else if (typeof params === 'object') {
        Object.entries(params).forEach(([k, v]) => {
          if (v != null && v !== '') u.searchParams.set(k, String(v));
        });
      }
      return u.toString();
    } catch {
      return url;
    }
  };

  /**
   * Construye URL pública de la ficha en Viator:
   * https://www.viator.com/tours/{CitySlug}/{ProductSlug}/d{DEST_ID}-{PRODUCT_CODE}
   */
  const viatorProductUrl = (p = {}, fallbackName = '') => {
    const code        = (p.code || '').trim();
    const destId      = Number(p.destId ?? p.viator_destination_id) || VIATOR_DEFAULTS.destId;
    const citySlugRaw = (p.citySlug ?? p.viator_city_slug ?? VIATOR_DEFAULTS.citySlug) || VIATOR_DEFAULTS.citySlug;
    const citySlug    = citySlugRaw.replace(/\s+/g, '-'); // por si llega con espacios
    const productSlug = p.viatorSlug ?? p.viator_slug ?? toProductSlug(fallbackName || p.name || code);

    let url = `https://www.viator.com/tours/${encodeURIComponent(citySlug)}/${productSlug}/d${destId}-${encodeURIComponent(code)}`;

    // Soporte opcional de afiliación: window.VIATOR_AFFILIATE (objeto o string "?pid=...")
    if (window.VIATOR_AFFILIATE) {
      url = appendQuery(url, window.VIATOR_AFFILIATE);
    }
    return url;
  };

  // ====== Data ======
  const codes = products.map(p => p.code).filter(Boolean);

  // ====== Render ======
  const buildSlides = () => {
    const frag = document.createDocumentFragment();
    let slideIndex = 0;

    products.forEach((product) => {
      const payload = window.__REVIEWS_CACHE__?.[product.code];
      const dataReviews = Array.isArray(payload?.reviews) ? payload.reviews : [];
      if (!dataReviews.length) return;

      // Toma 2 reseñas aleatorias por producto
      const shuffled = [...dataReviews].sort(() => 0.5 - Math.random()).slice(0, 2);
      const nameForHeader = resolveProductName(product, shuffled[0]);
      const viatorHref    = viatorProductUrl(product, nameForHeader);

      shuffled.forEach((r) => {
        const avatar = r.avatarUrl || '/images/avatar-default.png';
        const date = r.publishedDate ? new Date(r.publishedDate).toLocaleDateString() : T.noDate;
        const stars = renderStars(r.rating || 0);
        const userName = r.userName || T.anonymous;
        const reviewTitle = r.title || '';
        const text = (r.text || '').trim();
        const words = text.split(/\s+/);
        const needsCollapse = words.length > 45;
        const collapsedText = needsCollapse ? words.slice(0, 45).join(' ') : text;

        const slide = document.createElement('div');
        slide.className = 'carousel-item' + (slideIndex === 0 ? ' active' : '');
        slide.dataset.slideIndex = String(slideIndex);

        slide.innerHTML = `
          <div class="review-item card shadow-sm border-0 mx-auto w-100" style="max-width: 600px;">
            <div class="card-body d-flex flex-column justify-content-between position-relative" style="min-height: 400px;">
              <span class="tour-name fw-semibold">
                <a href="#" class="tour-link text-success fw-semibold d-inline-block"
                   data-id="${product.id}"
                   data-name="${escapeHtml(nameForHeader)}"
                   style="text-decoration: underline;">
                   ${escapeHtml(nameForHeader)}
                </a>
              </span>

              <div>
                <div class="d-flex align-items-center mb-3">
                  <img src="${avatar}" alt="Foto de ${escapeHtml(userName)}" class="rounded-circle me-3" width="50" height="50">
                  <div>
                    <h6 class="mb-0">${escapeHtml(userName)}</h6>
                    <small class="text-muted">${escapeHtml(date)}</small>
                  </div>
                </div>
                ${stars}
                ${reviewTitle ? `<h5 class="card-title">${escapeHtml(reviewTitle)}</h5>` : ''}

                <p class="card-text review-text"
                   data-full="${encodeURIComponent(text)}"
                   data-short="${encodeURIComponent(collapsedText)}"
                   data-collapsed="${needsCollapse ? '1' : '0'}">
                   ${needsCollapse ? escapeHtml(collapsedText) + '…' : escapeHtml(text)}
                </p>

                ${needsCollapse
                  ? `<a href="#" class="text-decoration-none small toggle-review">${T.seeMore}</a>`
                  : ''
                }
              </div>

              <div class="text-end mt-3">
                <a href="${viatorHref}"
                   target="_blank"
                   rel="noopener sponsored"
                   class="text-muted small text-decoration-none viator-credit"
                   title="Ver ${escapeHtml(nameForHeader)} en Viator">
                   ${T.poweredBy} Viator
                </a>
              </div>
            </div>
          </div>
        `;

        frag.appendChild(slide);
        slideIndex++;
      });
    });

    if (slideIndex === 0) {
      container.outerHTML = `<p class="text-muted">${T.none}</p>`;
      return false;
    }

    inner.innerHTML = '';
    inner.appendChild(frag);
    return true;
  };

  // ====== Delegated events ======
  const bindDelegates = () => {
    // Ver más / Ver menos
    inner.addEventListener('click', (e) => {
      const link = e.target.closest('.toggle-review');
      if (!link) return;
      e.preventDefault();

      const textEl = link.closest('.card-body')?.querySelector('.review-text');
      if (!textEl) return;

      const collapsed = textEl.dataset.collapsed === '1';
      const shortText = decodeURIComponent(textEl.dataset.short || '');
      const fullText = decodeURIComponent(textEl.dataset.full || '');

      if (collapsed) {
        textEl.textContent = fullText;
        textEl.dataset.collapsed = '0';
        link.textContent = T.seeLess;
        textEl.closest('.card-body').style.maxHeight = 'none';
      } else {
        textEl.textContent = shortText + '…';
        textEl.dataset.collapsed = '1';
        link.textContent = T.seeMore;
        textEl.closest('.card-body').style.maxHeight = '400px';
      }
    });

    // Click en el nombre del tour -> modal (si existe)
    inner.addEventListener('click', (e) => {
      const a = e.target.closest('.tour-link');
      if (!a) return;
      e.preventDefault();

      const tourId = a.dataset.id;
      const tourName = a.dataset.name || '';

      const modalText = document.getElementById('confirmTourModalText');
      const modalGoBtn = document.getElementById('confirmTourModalGo');
      const modalConfirmBtn = document.getElementById('tourModalConfirm');

      if (modalText) modalText.innerHTML = `¿Deseas ir al tour "<strong>${escapeHtml(tourName)}</strong>"?`;
      if (modalGoBtn) modalGoBtn.href = `/tour/${encodeURIComponent(tourId)}`;
      if (modalConfirmBtn) modalConfirmBtn.href = `/tour/${encodeURIComponent(tourId)}`;

      const modalEl = document.getElementById('confirmTourModal');
      if (modalEl && window.bootstrap?.Modal) new bootstrap.Modal(modalEl).show();
    });
  };

  // ====== Carga de reseñas ======
  const load = (opts = {}) =>
    fetchReviewsBatch(codes, {
      count: 5,
      start: 1,
      provider: 'ALL',
      sortBy: 'MOST_RECENT',
      ttlMs: 5 * 60 * 1000,
      ...opts
    })
    .then(() => { buildSlides() && bindDelegates(); })
    .catch(() => { container.outerHTML = `<p class="text-muted">${T.none}</p>`; });

  // primera carga
  await load();

  // auto-refresh
  if (REFRESH_MS > 0) {
    setInterval(() => { load({ force: true }); }, REFRESH_MS);
  }
});
