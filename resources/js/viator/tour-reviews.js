document.addEventListener('DOMContentLoaded', () => {
  const tourId = window.tourId;
  const container = document.getElementById(`review-carousel-tour-${tourId}`);
  const prev = document.querySelector(`.carousel-prev[data-tour="${tourId}"]`);
  const next = document.querySelector(`.carousel-next[data-tour="${tourId}"]`);
  const wrapper = container?.closest('.tour-review-carousel');

  const T = {
    none: window.I18N?.no_reviews ?? 'No hay reseñas disponibles.',
    seeMore: window.I18N?.see_more ?? 'Ver más',
    seeLess: window.I18N?.see_less ?? 'Ver menos',
    anonymous: window.I18N?.anonymous ?? 'Anónimo',
  };

  if (!container || !window.productCode) {
    if (container) container.innerHTML = `<p class="text-muted text-center">${T.none}</p>`;
    return;
  }

  const renderStars = (rating) => {
    const r = Math.max(0, Math.min(5, Number(rating) || 0));
    const full = Math.floor(r);
    const empty = 5 - full;
    return '★'.repeat(full) + '☆'.repeat(empty);
  };

  fetch(`/api/reviews`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
    },
    body: JSON.stringify({
      productCode: window.productCode,
      count: 20,
      start: 1,
      provider: 'VIATOR',
      sortBy: 'MOST_RECENT'
    })
  })
  .then(res => res.ok ? res.json() : Promise.reject(res))
  .then(data => {
    if (!data.reviews || data.reviews.length === 0) {
      container.innerHTML = `<p class="text-muted text-center">${T.none}</p>`;
      return;
    }

    const reviews = data.reviews;
    let index = 0;
    let expanded = false;

    const renderReview = (i) => {
      const r = reviews[i];
      const stars = renderStars(r.rating);
      const date = r.publishedDate ? new Date(r.publishedDate).toLocaleDateString() : '';
      const label = r.title ? `<div class="review-label">${r.title}</div>` : '';

      const fullText = (r.text || '').trim();
      const truncated = fullText.length > 250;
      const shortText = truncated ? fullText.slice(0, 250).trim() + '...' : fullText;

      container.innerHTML = `
        <div class="review-body-wrapper">
          <div class="review-header">
            <strong>${r.userName || T.anonymous}</strong>
            <small>${date}</small>
            <div class="review-stars">${stars} <span class="rating-number">(${Number(r.rating || 0).toFixed(1)}/5)</span></div>
            ${label}
          </div>
          <div class="review-content ${expanded ? 'expanded' : ''}" id="review-text-${i}">${expanded ? fullText : shortText}</div>
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

    renderReview(index);

    prev?.addEventListener('click', () => {
      index = (index - 1 + reviews.length) % reviews.length;
      expanded = false;
      renderReview(index);
    });

    next?.addEventListener('click', () => {
      index = (index + 1) % reviews.length;
      expanded = false;
      renderReview(index);
    });
  })
  .catch(err => {
    console.error('❌ Error al cargar reseñas:', err);
    container.innerHTML = '<p class="text-danger text-center">Error al cargar las reseñas.</p>';
  });
});
