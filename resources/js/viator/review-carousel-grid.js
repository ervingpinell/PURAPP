// resources/js/viator/review-carousel-grid.js
import { fetchReviewsBatch } from '../reviews-fetcher';

document.addEventListener('DOMContentLoaded', () => {
  const tours = Array.isArray(window.VIATOR_TOURS) ? window.VIATOR_TOURS : [];
  if (!tours.length) return;

  const REFRESH_MS = Number(window.REVIEWS_REFRESH_MS) || (10 * 60 * 1000); // 10 min por defecto
  const codes = tours.map(t => t.code).filter(Boolean);

  // Mensaje de carga manteniendo tu UI
  for (const { id } of tours) {
    const container = document.getElementById(`carousel-${id}`);
    if (container) {
      container.innerHTML = `
        <p class="text-center text-muted">
          ${(window.I18N && window.I18N.loading_reviews) || 'Loading reviews...'}
        </p>`;
    }
  }

  const renderMap = new Map(); // para comparar y evitar re-render innecesario

  const renderTour = (tour, reviews) => {
    const container = document.getElementById(`carousel-${tour.id}`);
    const card = document.getElementById(`card-${tour.id}`);
    if (!container || !card) return;

    // snapshot simple para evitar rerender cuando no cambia
    const snap = JSON.stringify((reviews[0] ? [reviews.length, reviews[0].publishedDate, Math.round(reviews[0].rating || 0)] : [0]));
    if (renderMap.get(tour.id) === snap) return;
    renderMap.set(tour.id, snap);

    let index = 0;
    let expanded = false;

    const escapeHtml = (s) =>
      String(s ?? '')
        .replaceAll('&', '&amp;').replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;').replaceAll('"', '&quot;')
        .replaceAll("'", '&#39;');

    const render = () => {
      const r = reviews[index] || {};
      const rating = Math.round(Number(r.rating) || 0);
      const stars = '★'.repeat(rating) + '☆'.repeat(5 - rating);
      const date = r.publishedDate ? new Date(r.publishedDate).toLocaleDateString() : '';
      const text = (r.text || '').toString();
      const isShort = text.length < 120;
      const truncated = text.length > 250;
      const shortText = truncated ? text.slice(0, 250) + '...' : text;

      container.innerHTML = `
        <div class="review-body-wrapper ${isShort ? 'centered' : ''}">
          <div><strong>${escapeHtml(r.userName || 'Anonymous')}</strong><br>${date}</div>
          <div class="review-stars">${stars} (${rating}/5)</div>
          ${r.title ? `<div class="review-label">${escapeHtml(r.title)}</div>` : ''}
          <div class="review-content ${expanded ? 'expanded' : ''}">
            <p>${escapeHtml(expanded || !truncated ? text : shortText)}</p>
          </div>
          ${truncated ? `<button class="review-toggle">${expanded ? 'Ver menos' : 'Ver más'}</button>` : ''}
        </div>
      `;

      card.classList.toggle('expanded', expanded);
    };

    if (!Array.isArray(reviews) || !reviews.length) {
      container.innerHTML = '<p class="text-muted text-center">No reviews available.</p>';
    } else {
      render();

      const prevBtn = document.querySelector(`.carousel-prev[data-tour="${tour.id}"]`);
      const nextBtn = document.querySelector(`.carousel-next[data-tour="${tour.id}"]`);

      if (prevBtn) prevBtn.onclick = () => { expanded = false; index = (index - 1 + reviews.length) % reviews.length; render(); };
      if (nextBtn) nextBtn.onclick = () => { expanded = false; index = (index + 1) % reviews.length; render(); };

      container.addEventListener('click', e => {
        if (e.target.classList.contains('review-toggle')) {
          expanded = !expanded; render();
        }
      });
    }
  };

  const loadAndRender = (opts = {}) => {
    return fetchReviewsBatch(codes, { count: 20, start: 1, provider: 'ALL', sortBy: 'MOST_RECENT', ttlMs: 5 * 60 * 1000, ...opts })
      .then(({ results }) => {
        for (const tour of tours) {
          const payload = results[tour.code] || window.__REVIEWS_CACHE__[tour.code] || { reviews: [] };
          renderTour(tour, payload.reviews || []);
        }
      })
      .catch(() => {
        for (const { id } of tours) {
          const container = document.getElementById(`carousel-${id}`);
          if (container) container.innerHTML = `<p class="text-danger text-center">Error loading reviews.</p>`;
        }
      });
  };

  // primera carga (usa caché si fresco, si no va a red)
  loadAndRender();

  // auto-refresh cada X minutos (fuerza revalidar memoria; backend sigue cacheado)
  if (REFRESH_MS > 0) {
    setInterval(() => { loadAndRender({ force: true }); }, REFRESH_MS);
  }
});
