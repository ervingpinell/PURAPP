document.addEventListener('DOMContentLoaded', async () => {
  const container = document.getElementById('viator-carousel');
  const inner = container?.querySelector('.carousel-inner');
  const products = window.VIATOR_CAROUSEL_PRODUCTS || [];

  if (!container || !inner || products.length === 0) return;

  const T = {
    loading: window.I18N?.loading_reviews ?? 'Cargando reseñas...',
    none: window.I18N?.no_reviews ?? 'No hay reseñas para mostrar.',
    seeMore: window.I18N?.see_more ?? 'Ver más',
    seeLess: window.I18N?.see_less ?? 'Ver menos',
    anonymous: window.I18N?.anonymous ?? 'Anónimo',
    noDate: window.I18N?.no_date ?? 'Fecha no disponible',
    poweredBy: window.I18N?.powered_by ?? 'Powered by',
  };

  // Peticiones en paralelo
  const requests = products.map(({ code }) =>
    fetch('/api/reviews', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      },
      body: JSON.stringify({
        productCode: code,
        count: 5,
        start: 1,
        provider: 'VIATOR',
        sortBy: 'MOST_RECENT',
      })
    }).then(res => res.ok ? res.json() : Promise.reject(new Error('HTTP ' + res.status)))
      .catch(err => ({ error: err }))
  );

  const results = await Promise.all(requests);
  const frag = document.createDocumentFragment();
  let slideIndex = 0;

  products.forEach((prod, i) => {
    const { id, name, code } = prod;
    const data = results[i];

    if (data?.error || !Array.isArray(data?.reviews) || data.reviews.length === 0) {
      // Si quieres mostrar una tarjeta vacía por producto, descomenta:
      // const empty = document.createElement('div');
      // empty.className = 'carousel-item' + (slideIndex === 0 ? ' active' : '');
      // empty.innerHTML = `<div class="w-100 text-center text-muted py-5">${T.none}</div>`;
      // frag.appendChild(empty);
      return;
    }

    // 2 reviews aleatorias por producto
    const shuffled = [...data.reviews].sort(() => 0.5 - Math.random()).slice(0, 2);

    shuffled.forEach((r) => {
      const avatar = r.avatarUrl || '/images/avatar-default.png';
      const date = r.publishedDate ? new Date(r.publishedDate).toLocaleDateString() : T.noDate;
      const stars = renderStars(r.rating);
      const userName = r.userName || T.anonymous;
      const reviewTitle = r.title || '';
      const text = (r.text || '').trim();
      const words = text.split(/\s+/);
      const needsCollapse = words.length > 45;
      const collapsedText = needsCollapse ? words.slice(0, 45).join(' ') : text;

      const slide = document.createElement('div');
      slide.className = 'carousel-item' + (slideIndex === 0 ? ' active' : '');
      slide.dataset.slideIndex = String(slideIndex);

      // Usamos data attrs para toggle
      slide.innerHTML = `
        <div class="review-item card shadow-sm border-0 mx-auto w-100" style="max-width: 600px;">
          <div class="card-body d-flex flex-column justify-content-between position-relative" style="min-height: 400px;">
            <span class="tour-name fw-semibold">
              <a href="#" class="tour-link text-success fw-semibold d-inline-block"
                 data-id="${id}" data-name="${escapeHtml(name)}"
                 style="text-decoration: underline;">
                 ${escapeHtml(name)}
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
              <a href="https://www.viator.com/searchResults/all?search=${encodeURIComponent(code)}"
                 target="_blank"
                 class="text-muted small text-decoration-none viator-credit"
                 title="Ver ${escapeHtml(name)} en Viator">
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
    return;
  }

  // Inserta todo de una
  inner.innerHTML = '';
  inner.appendChild(frag);

  // Delegación de eventos: toggle "Ver más / Ver menos"
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

  // Delegación de eventos: abrir modal de confirmación
  inner.addEventListener('click', (e) => {
    const a = e.target.closest('.tour-link');
    if (!a) return;
    e.preventDefault();

    const tourId = a.dataset.id;
    const tourName = a.dataset.name;

    const modalText = document.getElementById('confirmTourModalText');
    const modalGoBtn = document.getElementById('confirmTourModalGo');
    const modalConfirmBtn = document.getElementById('tourModalConfirm');

    if (modalText) modalText.innerHTML = `¿Deseas ir al tour "<strong>${escapeHtml(tourName)}</strong>"?`;
    if (modalGoBtn) modalGoBtn.href = `/tour/${encodeURIComponent(tourId)}`;
    if (modalConfirmBtn) modalConfirmBtn.href = `/tour/${encodeURIComponent(tourId)}`;

    const modalEl = document.getElementById('confirmTourModal');
    if (modalEl) new bootstrap.Modal(modalEl).show();
  });
});

// Utilidades
function renderStars(rating) {
  const full = Math.floor(rating);
  const half = rating % 1 >= 0.5 ? 1 : 0;
  const empty = 5 - full - half;

  let stars = '';
  for (let i = 0; i < full; i++) stars += '★';
  if (half) stars += '⯨';
  for (let i = 0; i < empty; i++) stars += '☆';

  const formatted = Number.isInteger(rating) ? rating : Number(rating).toFixed(1);
  return `<div class="mb-2 review-stars text-warning">${stars}<span class="rating-number"> (${formatted}/5)</span></div>`;
}

function escapeHtml(str = '') {
  return String(str)
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');
}
