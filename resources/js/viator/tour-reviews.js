// resources/js/viator/tour-reviews.js
import { fetchReviewsOnce } from '../reviews-fetcher';

document.addEventListener('DOMContentLoaded', () => {
  const tourId = window.tourId;
  const code = window.productCode;
  const container = document.getElementById(`review-carousel-tour-${tourId}`);
  const prev = document.querySelector(`.carousel-prev[data-tour="${tourId}"]`);
  const next = document.querySelector(`.carousel-next[data-tour="${tourId}"]`);
  const wrapper = container?.closest('.tour-review-carousel');

  const T = {
    none: window.I18N?.no_reviews ?? 'No hay reseñas disponibles.',
    seeMore: window.I18N?.see_more ?? 'Ver más',
    seeLess: window.I18N?.see_less ?? 'Ver menos',
    anonymous: window.I18N?.anonymous ?? 'Anónimo',
    loading: window.I18N?.loading_reviews ?? 'Cargando reseñas...',
  };

  if (!container || !code) {
    if (container) container.innerHTML = `<p class="text-muted text-center">${T.none}</p>`;
    return;
  }

  const REFRESH_MS = Number(window.REVIEWS_REFRESH_MS) || (10 * 60 * 1000);

  const renderStars = (rating) => {
    const r = Math.max(0, Math.min(5, Number(rating) || 0));
    const full = Math.floor(r);
    return '★'.repeat(full) + '☆'.repeat(5 - full);
  };

  const escapeHtml = (s) =>
    String(s ?? '')
      .replaceAll('&', '&amp;').replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;').replaceAll('"', '&quot;')
      .replaceAll("'", '&#39;');

  let reviews = [];
  let index = 0;
  let expanded = false;

  const renderReview = (i) => {
    const r = reviews[i] || {};
    const stars = renderStars(r.rating);
    const date = r.publishedDate ? new Date(r.publishedDate).toLocaleDateString() : '';
    const label = r.title ? `<div class="review-label">${escapeHtml(r.title)}</div>` : '';

    const fullText = (r.text || '').trim();
    const truncated = fullText.length > 250;
    const shortText = truncated ? fullText.slice(0, 250).trim() + '...' : fullText;

    container.innerHTML = `
      <div class="review-body-wrapper">
        <div class="review-header">
          <strong>${escapeHtml(r.userName || T.anonymous)}</strong>
          <small>${escapeHtml(date)}</small>
          <div class="review-stars">${stars} <span class="rating-number">(${Number(r.rating || 0).toFixed(1)}/5)</span></div>
          ${label}
        </div>
        <div class="review-content ${expanded ? 'expanded' : ''}" id="review-text-${i}">${escapeHtml(expanded ? fullText : shortText)}</div>
        <div class="review-footer">
          ${truncated ? `<button class="toggle-review">${expanded ? T.seeLess : T.seeMore}</button>` : ''}
        </div>
      </div>
    `;

    if (wrapper) wrapper.classList.toggle('expanded', expanded);

    const toggleBtn = container.querySelector('.toggle-review');
    const reviewText = container.querySelector(`#review-text-${i}`);

    if (toggleBtn && reviewText) {
      toggleBtn.addEventListener('click', () => {
        expanded = !expanded;
        reviewText.classList.toggle('expanded', expanded);
        reviewText.textContent = expanded ? fullText : shortText;
        toggleBtn.textContent = expanded ? T.seeLess : T.seeMore;
        if (wrapper) wrapper.classList.toggle('expanded', expanded);
      });
    }
  };

  const render = (data) => {
    reviews = Array.isArray(data?.reviews) ? data.reviews : [];
    if (!reviews.length) {
      container.innerHTML = `<p class="text-muted text-center">${T.none}</p>`;
      return;
    }
    index = Math.min(index, reviews.length - 1);
    expanded = false;
    renderReview(index);
  };

  // loading
  container.innerHTML = `<p class="text-muted text-center">${T.loading}</p>`;

  const load = (opts = {}) =>
    fetchReviewsOnce(code, { count: 20, start: 1, provider: 'ALL', sortBy: 'MOST_RECENT', ttlMs: 5 * 60 * 1000, ...opts })
      .then(render)
      .catch(() => (container.innerHTML = '<p class="text-danger text-center">Error al cargar las reseñas.</p>'));

  load();

  prev?.addEventListener('click', () => {
    if (!reviews.length) return;
    index = (index - 1 + reviews.length) % reviews.length;
    expanded = false;
    renderReview(index);
  });

  next?.addEventListener('click', () => {
    if (!reviews.length) return;
    index = (index + 1) % reviews.length;
    expanded = false;
    renderReview(index);
  });

  if (REFRESH_MS > 0) {
    setInterval(() => { load({ force: true }); }, REFRESH_MS);
  }
});
